<!-- attandance_list -->
@extends('layouts.main')
<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
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
    <h3 class="box-title">Past Salary Increment List</h3>
     

      <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
          

          <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">  
            <select class="form-control" id="location" name="location" style="width: 100%;" required>
               
                @foreach ($default_user_location as $keys)
                      <option value={{$keys->id}} >{{$keys->location_name}}</option>
                @endforeach

            </select>                 
          </div>


        </div>

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>

  <div class="box-body">
    <div class="row">
      <div class="form-group col-lg-8 col-md-8 col-xs-12" style="overflow: auto;">    
        <table id="designation_list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th style="width: 5%"></th>
              <th style="width: 25%">Note</th>
              <th style="width: 15%">Effective Month</th>
              <th style="width: 15%">Location</th>
              <th style="width: 15%">Status</th>
              <!-- <th style="width: 10%">Edit</th> -->
              <!-- <th style="width: 10%">Delete</th> -->
              <th style="width: 0%"></th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>
@endsection

@section('script')

<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script> -->
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>



<script>
    function format(d) {
      console.log(d);
      return '<table class="table table-bordered table-hover table-striped" cellspacing="0" width="100%">'+
            '<tr>'+
              '<td>'+"Employee Name"+'</td>'+
              '<td>'+"Designation Name"+'</td>'+
              // '<td>'+"Department Name"+'</td>'+
              '<td>'+"Salary Amount"+'</td>'+
              '<td>'+"Increase Amount"+'</td>'+
              '<td>'+"New Salary"+'</td>'+
            '</tr>'+
            '<tr>'+
              '<td>'+d.employee_name+'</td>'+
              '<td>'+d.designation_name+'</td>'+
              // '<td>'+d.depertment_name+'</td>'+
              '<td>'+d.salary_amount+'</td>'+
              '<td>'+d.increase_amount+'</td>'+
              '<td>'+d.new_salary_amount+'</td>'+
            '</tr>'+  
          '</table>';
    }
$(document).ready(function($) {
    $('#process_date').datepicker({
      autoclose: true
    });

  $role=  $('#location').select2({
      placeholder: 'Enter a location mandatory',
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

    $role.on('select2:select', function (e) {
        dataLoad();
    });

    $role.on('select2:unselect', function (e) {
        $('#location').val(null).trigger("change");
        dataLoad();
    });

    $(".onchange").change(function(){
        dataLoad();
    });


    dataLoad = function(){

      if ($("#location").val() == null){
        location_id = 0;
      }else{
        location_id = $("#location").val();
      }


      $.ajax({
        type:   'POST', 
        url :   "{{URL::to('/')}}/role_salaryincrement_listdata",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },        
        data:   {
                   punch_date: $("#process_date").val(),
                   location: location_id,
                   status: 2,
                },          
        dataType: 'json',
        success: function(data) {
          var dataSet = data.data;
            table = $('#designation_list_table').DataTable( {
              destroy:    true,
              paging:     true,
              searching:  true,
              ordering:   true,
              bInfo:      false,  
              "data":     dataSet,
              "columnDefs": [
                  {
                      "targets": [ 5 ],
                      "visible": false,
                      "searchable": true
                  }
              ],              
              "columns": [
              {
                "className":      'details-control',
                "orderable":      true,
                "data":           null,
                "defaultContent": ''
              },

              { "data": "note" },
              { "data": "effective_month" },
              { "data": "location_name" },
              { "data": "status" },
              // { "data": "Link",
              //   "mRender": function (data, type, full) {
              //       return '<a href="{{URL::to('/')}}/salaryincrement/'+full.id+'/edit"> <span class="glyphicon glyphicon-edit"></span> Edit</a>';
              //   }
              // },

              // { "data": "Link",
              //   "mRender": function (data, type, full) {
              //       return '<a href="{{URL::to('/')}}/salaryincrement/'+full.id+'/cancel"  onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-sm btn-flat"><span class="glyphicon glyphicon-trash"></span> Delete</a>';
              //   }
              // },
              { "data": "employee_name" },
              ],
              order: [ 1, 'asc' ]
            });
        }
      }); 
    } 

    dataLoad();   

    $('#designation_list_table tbody').on('click', 'td.details-control', function () {
      var tr = $(this).closest('tr');
      var row = table.row( tr );
      if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format(row.data()) ).show();
            tr.addClass('shown');
        }
    });     
});
</script>