<!-- attandance_list -->
@extends('layouts.main')
<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables/jquery.dataTables.min.css')}}">





<style type="text/css">
  .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
      padding: 5px;
  } 

  table.dataTable thead > tr > th {
      padding-right: 25px;
  }
  .table>tbody{
    font-size: small;
  }
  .table>thead{
    font-size: smaller;
  }

  td.details-control {
      background: url('{{URL::to('/')}}/dist/img/details_open.png') no-repeat center center;
      cursor: pointer;
  }
  tr.shown td.details-control {
      background: url('{{URL::to('/')}}/dist/img/details_close.png') no-repeat center center;
  } 
</style>
@endsection


@section('content')
<div class="box box-default">
  <div class="box-header with-border">
    <h3 class="box-title">Manual Attandance Out Data Entry</h3>
 


      <div class="row" style="margin-left:10px; ">
          
          <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">  
          
              <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>                
                  <input type="text" class="form-control pull-right" id="process_date" name="process_date" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('process_date') }}" required readonly>
              </div>                  
          </div> 

          <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">  
              <select class="form-control" id="working_shift" name="working_shift" style="width: 100%;" >
              </select>                 
          </div>



          <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">  
              <select class="form-control" id="location" name="location" style="width: 100%;" >
              </select>                 
          </div>


        </div>


      <div class="row" style="margin-left:10px;" >
          <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">  
              <select class="form-control" id="department" name="department" style="width: 100%;" >
              </select>                 
          </div>

          <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">  
              <select class="form-control" id="designation" name="designation" style="width: 100%;" >
              </select>    
          </div>



          <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
            <input type="button" id="search" value="Search" class=" btn-sm block btn-flat btn" style="margin-right: 15px; padding: 7px 10px;background-color: #EEEEEE; color: black; border:1px solid gray;">                  
          </div>

      </div>

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>

  <form  method="POST" action="{{url('submitmanual_inout')}}">
        {{ csrf_field() }}    


  <div class="box-body">
    <div class="row">
      <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">    
        <table id="designation_list_table" class=" cell-border table table-bordered table-hover "  cellspacing="0" width="100%">
          <thead>
            <tr>

             <th  style="width: 10%"    ><input name="select_all" value="1" id="example-select-all" type="checkbox" />_All</th>
              <th style="width: 25%">Employee Name</th>
              <th style="width: 15%">Deparment</th>
              <th style="width: 15%">Designation</th>
              <th style="width: 15%">Shift Name</th>
              <th style="width: 10%">Punch Date</th>
              <th style="width: 10%">End Time</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>


     <input type="submit"  value="Submit" class=" btn-sm btn-success block btn-flat btn" style="margin-left: 15px; padding: 7px 10px; color: black; border:1px solid gray;"> 

    </div>
  </div>

</form>




</div>
@endsection

@section('script')

<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('dist/js/jquery.inputmask.bundle.js')}}"></script>
<script src="https://cdn.datatables.net/buttons/1.5.0/js/dataTables.buttons.min.js"></script>


<script>



$(document).ready(function($) {
    $('#process_date').datepicker({
      autoclose: true
    });

    $('#location').select2({
      placeholder: 'Enter a location',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/location_list_data',
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

 $('#department').select2({
      placeholder: 'Enter department',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/depertment_list_data',
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

    $('#designation').select2({
      placeholder: 'Enter designation',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/designation_list_data',
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

    $('#working_shift').select2({
      placeholder: 'Enter working shift',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/shift_list_data',
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



    $("#search").click(function(){
      // dataLoad();
   



    // dataLoad = function(){

      if ($("#location").val() == null){
        location_id=0;
      }else{
        location_id = $("#location").val();
      }

      if ($("#working_shift").val() == null){
        working_shift=0;
      }else{
        working_shift = $("#working_shift").val();
      }

      if ($("#department").val() == null){
        department_id = 0;
      }else{
        department_id = $("#department").val();
      }

      if ($("#designation").val() == null){
        designation_id = 0;
      }else{
        designation_id = $("#designation").val();
      }


      $.ajax({
        type:   'POST', 
        url :   "{{URL::to('/')}}/manual_in_out_listdata",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },        
        data:   {
                   punch_date: $("#process_date").val(),
                   location      : location_id,
                   department_id : department_id,
                   designation_id: designation_id,
                   working_shift_id: working_shift,
                   status:2,
                   // punch_date: $("#date-from").val(),
                   // dateto:   $("#date-to").val()
                },          
        dataType: 'json',
        success: function(data) {
          var dataSet = data.data;
            table = $('#designation_list_table').DataTable( {
              destroy:    true,
              paging:     false,
              searching:  true,
              ordering:   true,
              bInfo:      false,  
              "data":     dataSet,
              "initComplete": function () {
                // $( ".dateto" ).datepicker({
                //   showOn: "button",
                //   changeMonth: true,
                //   changeYear: true
                // });                 
                // $("#time_apertura").inputmask({"mask":"99:99","regex":"^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$"});
                // $(".dateto").inputmask("hh:mm:ss", {
                $(".dateto").inputmask("hh:mm:ss", {
                  placeholder: "HH:MM:SS", 
                  insertMode: false, 
                  showMaskOnHover: false,
                  hourFormat: "24"
                });                
              },
              "columns": [

              { "data": "checkbox",
                      "mRender": function (data, type, full) {
                      return '<input type="checkbox" name="id[]" value="'+full.id+'">';
              }
              },
              { "data": "employee_name" },
              { "data": "depertment_name" },
              { "data": "designation_name" },
              { "data": "shift_name" },
              { "data": "punch_date" },
              { "data": "text",
                      "mRender": function (data, type, full) {
                      return '<input type="text" class="dateto" name="punch_time['+full.id+']" value="'+full.punch_time+'">'+
                             '<input type="hidden"              name="punch_date['+full.id+']" value="'+full.punch_date+'">';
              }
              },
              ],
              order: [ 1, 'asc' ]
            });
        }
      }); 
    // } 

    });
   // Handle click on "Select all" control
   $('#example-select-all').on('click', function(){
      // Check/uncheck all checkboxes in the table
      var rows = table.rows({ 'search': 'applied' }).nodes();
      $('input[type="checkbox"]', rows).prop('checked', this.checked);
   });


 // Handle click on checkbox to set state of "Select all" control
   $('#designation_list_table tbody').on('change', 'input[type="checkbox"]', function(){
      // If checkbox is not checked
      if(!this.checked){
         var el = $('#example-select-all').get(0);
         // If "Select all" control is checked and has 'indeterminate' property
         if(el && el.checked && ('indeterminate' in el)){
            // Set visual state of "Select all" control 
            // as 'indeterminate'
            el.indeterminate = true;
         }
      }
   });


    

    // $('#designation_list_table tbody').on('click', 'td.details-control', function () {
    //   var tr = $(this).closest('tr');
    //   var row = table.row( tr );
    //   if ( row.child.isShown() ) {
    //         // This row is already open - close it
    //         row.child.hide();
    //         tr.removeClass('shown');
    //     }
    //     else {
    //         // Open this row
    //         row.child( format(row.data()) ).show();
    //         tr.addClass('shown');
    //     }
    // });     
});
</script>