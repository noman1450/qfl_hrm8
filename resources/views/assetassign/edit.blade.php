
@extends('layouts.main')
@section('styles')
  <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

  <style>
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
    @include('assetassign._form')

  </form>
@endsection

@section('script')
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>

<script>
  $(document).ready(function($) {

    $("select").select2();

    $('#assign_date').datepicker({
      autoclose: true
    });

@if($form_type == 'edit')
    let asset_id = '{{$data[0]->hrm_asset_id}}';
    let asset_name = '{{$data[0]->asset}}';

    // ১. Select2 initialize করুন এবং variable-এ রাখুন
    let $asset = $('#asset_id').select2({
        placeholder: 'Enter Asset name',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: '{{URL::to('/')}}/asset_data',
            delay: 250,
            data: function(params) {
                return { term: params.term }
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return { results: data };
            },
            cache: true
        }
    });

    // ২. Manually Option তৈরি করে append করুন
    let newOption = new Option(asset_name, asset_id, true, true);
    $('#asset_id').append(newOption).trigger('change.select2');



@endif

    $('input[type=radio]').click(function(e) {

        let dataId = $(this).data('id')

        if (dataId === 'choose_employee') {
            $('#hrm_depertments_id').val('')
            $('#hrm_depertments_id').text('')

            $employee = $('#employee_name').select2("destroy").select2({
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
                        }
                    },
                    cache: true
                }
            });

            $employee.on("select2:select", function (e) {
                $("#department").val($(this).select2('data')['0']['depertment_name']);
                $("#designation").val($(this).select2('data')['0']['designation_name']);
            });

            $employee.on("select2:unselect", function (e) {
                $("#department").val('');
                $("#designation").val('');
            });

            $('#choose_employee').show();
            $('#choose_department').hide();
        }
        else if (dataId === 'choose_department') {
            $('#employee_name').val('')
            $('#employee_name').text('')
            $("#department").val('');
            $("#designation").val('');

            $('#hrm_depertments_id').select2("destroy").select2({
                placeholder: 'Enter a Department',
                allowClear: true,
                ajax: {
                    dataType: 'json',
                    url: "{{ url('/depertment_list_data') }}",
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
                        }
                    },
                    cache: true
                }
            });

            $('#choose_department').show();
            $('#choose_employee').hide();
        }
    })

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
                }
            },
            cache: true
        }
    });

    $employee.on("select2:select", function (e) {
        $("#department").val($(this).select2('data')['0']['depertment_name']);
        $("#designation").val($(this).select2('data')['0']['designation_name']);
    });

    $employee.on("select2:unselect", function (e) {
        $("#department").val('');
        $("#designation").val('');
    });

    $('#hrm_depertments_id').select2({
        placeholder: 'Enter a Department',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: "{{ url('/depertment_list_data') }}",
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
                }
            },
            cache: true
        }
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

    $asset.on("select2:select", function (e) {
      $("#price").val($(this).select2('data')['0']['price']);
      $("#serial_no").val($(this).select2('data')['0']['serial_no']);
      $("#product_id").val($(this).select2('data')['0']['product_id']);
      $("#asset_store_ledger_id").val($(this).select2('data')['0']['id']);
      $("#current_stock").val($(this).select2('data')['0']['current_stock']);
      $("#qty_stockout").val('');
    });

    $asset.on("select2:unselect", function (e) {
      $("#price").val('');
      $("#current_stock").val('');
      $("#qty_stockout").val('');
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
    $('#qty_stockout').on('input', function() {
        var available = parseFloat($('#current_stock').val()) || 0;
        var assignQty = parseFloat($(this).val()) || 0;

        if(assignQty > available){
            alert('Assign Quantity cannot be greater than Available Quantity');
            $(this).val(available); // auto set to max allowed
        }
    });
  });
</script>


@stop
