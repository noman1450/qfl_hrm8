
@extends('layouts.main')
@section('styles')
  <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

  <style type="text/css">
  .table>tbody>tr>td, .table>tbody>tr>th, 
  .table>tfoot>tr>td, .table>tfoot>tr>th, 
  .table>thead>tr>td, .table>thead>tr>th{
    padding: 5px;
  } 
  </style>
@endsection

@section('content')
<div class="box box-default">
  
  <div class="box-header with-border" style="position: inherit;">
    <h3 id="page-header" class="box-title">Asset Assign</h3>
     <div class="box-tools pull-right">
          <a type="button" href="{{ URL::previous() }}" class="btn btn-box-tool"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
        </div>
  </div>

  <form method="POST" action="{{ route('assetassign.update', $id) }}" onkeypress = "return event.keyCode != 13;" id="asset_assign_form" enctype="multipart/form-data" file="true">
    {{ method_field('put') }}
    @php $form_type ='edit' @endphp
    @include('assetassign/_form')

  </form>
@endsection

@section('script')
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>

<script>
  $(document).ready(function($) {

      $('#start_date').datepicker({
        // startDate: new Date() ,	
        autoclose: true
      });

      $employee = $('#employee_name').select2({
        placeholder: 'Enter an Employee Name',
        allowClear: true,
          ajax: {
              dataType: 'json',
              url: "{{URL::to('/')}}/join_employee_list",
              delay: 250,    			
            data: function(params) {
                return {
                  term: params.term
                }
            },
              processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                  results: data,
                  pagination: {
                    more: (params.page * 30) < data.total_count
                  }
                };
              },
              cache: true
          }
      });

      // select old value
    var employee_select = $('#employee_name');
    $.ajax({
      dataType: 'json',
      url: '{{URL::to('/')}}/asset_assign_list_select',
    }).then(function (data) {
      // create the option and append to Select2
      
      var id = {{ $id }};
      
      var i;
      for(i=0; i<data.length; i++){
        if(data[i].id == id){
          break;
        }
      }

      console.log(data, "id = ", id);
      var option = new Option(data[i].employee, data[i].id, true, true);
      employee_select.append(option).trigger('change');
      // manually trigger the `select2:select` event
      employee_select.trigger({
          type: 'select2:select',
          params: {
              data: data
          }
      });
      $("#department").val(data[i].depertment_name);
      $("#designation").val(data[i].designation_name);

    });

      $employee.on("select2:select", function (e) {
        $("#department").val($(this).select2('data')['0']['depertment_name']);
        $("#designation").val($(this).select2('data')['0']['designation_name']);
      });

      $employee.on("select2:unselect", function (e) {
        $("#department").val('');
        $("#designation").val('');
      });

    $asset = $('#asset_id').select2({
        placeholder: 'Enter Asset name',
        allowClear: true,
        ajax: {
          dataType: 'json',
          url: '{{URL::to('/')}}/asset_data',
          delay: 250,
          data: function(params) {
            return {
              term: params.term
            }
          },
          processResults: function (data, params) {
            params.page = params.page || 1;
            return {
              results: data
            };
          },
          cache: true
        }
    });

    // select old value
    var asset_select = $('#asset_id');
    $.ajax({
      dataType: 'json',
      url: '{{URL::to('/')}}/asset_data_old',
    }).then(function (data) {
      // create the option and append to Select2
      var id = {{ $id }};
      
      var i;
      for(i=0; i<data.length; i++){
        if(data[i].id == id){
          break;
        }
      }
      console.log(data, "id = ", id);
      var option = new Option(data[i].text, data[i].id, true, true);
      asset_select.append(option).trigger('change');
      console.log('here');
      // manually trigger the `select2:select` event
      asset_select.trigger({
          type: 'select2:select',
          params: {
              data: data
          }
      });

      $("#price").val(data[i].price);
      $("#serial_no").val(data[i].serial_no);
      $("#product_id").val(data[i].product_id);
      $("#asset_store_ledger_id").val(data[i].asset_store_ledger_id);
    });

    $asset.on("select2:select", function (e) {
      $("#price").val($(this).select2('data')['0']['price']);
      $("#serial_no").val($(this).select2('data')['0']['serial_no']);
      $("#product_id").val($(this).select2('data')['0']['product_id']);
      $("#asset_store_ledger_id").val($(this).select2('data')['0']['id']);
    });

    $asset.on("select2:unselect", function (e) {
      $("#price").val('');
      $("#serial_no").val('');
      $("#asset_store_master_id").val('');
    });


    $( "#asset_assign_form" ).submit(function(event){
      event.preventDefault();

      if(confirm('Do you want to submit?')){
        $("#btnSubmit").attr("disabled", true); 
        $("#btnSubmit").val('Please wait..');
        var $form   = $( this ),
        url         = $form.attr( "action" ); 
        token 		= $("[name='_token']").val();
        $.ajax({
          type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
          url         : url, // the url where we want to POST
          data 		    : $form.serialize(),
          dataType    : 'json', // what type of data do we expect back from the server
          encode      : true,
          _token 		  : token
        })
        .done(function(data) {  
          if(data['success']) {
            $('#btnSubmit').attr("disabled", false);
            $("#btnSubmit").val('Submit');
            alert(data.message);
            window.location.replace("{{ URL::to('assetassign')}}");
          }else{
            $('#btnSubmit').attr("disabled", false);
            $("#btnSubmit").val('Submit');
            alert(data.message);
            window.location.replace("{{ URL::to('assetassign')}}");
          }
        });
      }else{
        return;
      }        
    });
  });
</script>
@stop
