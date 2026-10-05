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
    <h3 class="box-title">Employee KPI List</h3>
     
<!--       <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
          <a href="{{ URL::to('kpi_employee/create')}}"><input type="button" value="Create New" class="btn-success btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold;"></a>
      </div> -->

      <div class="col-xs-12 col-md-12" style="padding-left: 0px; padding-top: 10px;">
         <div class="col-xs-4 col-md-4">
             <select class="form-control" id="hrm_kpi_assesment_date_id" name="hrm_kpi_assesment_date_id" style="width: 100%;" required>
             </select>
          </div>


         <div class="col-xs-4 col-md-4">
              <select class="form-control category" id="category" name="category" style="width: 100%;" >
              </select>
          </div>


         <div class="col-xs-4 col-md-4">
              <select class="form-control employeestatus" id="employeestatus" name="employeestatus" style="width: 100%;" >
              </select>
          </div>



      </div>

 
  
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>

  <div class="box-body">
    <div class="row">
      <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">    
        <table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th style="width: 0%"></th>
              <th style="width: 5%"></th>
              <th style="width: 30%">Employee Name</th>
              <th style="width: 15%">Location</th>
              <th style="width: 15%">Designation</th>
              <th style="width: 15%">Department</th>
              <th style="width: 15%">Emp. Category</th>
              <th style="width: 30%">Evaluation Date</th>
              <th style="width: 5%">Create</th>
              <!-- <th style="width: 5%">Edit</th> -->
              <!-- <th style="width: 5%">Delete</th> -->
     
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
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>



<script>
    function format(d) {
      console.log(d);
      return '<table class="table table-bordered table-hover" cellspacing="0" width="100%">'+
            '<tr>'+
              '<td>'+"Task Name"+'</td>'+
              // '<td>'+"Mark"+'</td>'+
              // '<td>'+"Point"+'</td>'+
      
            '</tr>'+
            '<tr>'+
              '<td>'+d.task_name+'</td>'+
              // '<td>'+d.marks+'</td>'+
              // '<td>'+d.point+'</td>'+
            '</tr>'+  
          '</table>';
    }
$(document).ready(function($) {

      var $assesment = $('#hrm_kpi_assesment_date_id').select2({
              placeholder: 'Select Year/Month',
              allowClear: true,
              ajax: {
                dataType: 'json',
                url: '{{URL::to('/')}}/kpi_assesment_date_list_data',
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

        $assesment.on("select2:select", function (e) {
            dataLoad();
        });

        $assesment.on("select2:unselect", function (e) {
            dataLoad();
        });



     $category=$('#category').select2({
        placeholder: 'Enter Employee Category',
        allowClear: true,
        ajax: {
          dataType: 'json',
          url: '{{URL::to('/')}}/category_list_data',
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

      $category.on('select2:select', function (e) {
        dataLoad();
      });


      $category.on('select2:unselect', function (e) {
          $('#category').val(null).trigger("change");
          dataLoad();
      });


   $employeestatus= $('#employeestatus').select2({
      placeholder: 'Enter employee status',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/employeestatus_list_data',
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


      $employeestatus.on('select2:select', function (e) {
        dataLoad();
      });


      $employeestatus.on('select2:unselect', function (e) {
          $('#employeestatus').val(null).trigger("change");
          dataLoad();
      });





dataLoad = function(){
      $.ajax({
        type:   'POST', 
        url :   "{{URL::to('/')}}/kpi_employee_list_user_wise",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
        data:   {
                  hrm_kpi_assesment_date_id : $("#hrm_kpi_assesment_date_id").val(), 
                  hrm_category_id : $("#category").val(), 
                  hrm_employment_status_id : $("#employeestatus").val(), 
        }, 
        dataType: 'json',
        success: function(data) {
          var dataSet = data.data;
            table = $('#list_table').DataTable( {
              destroy:    true,
              paging:     true,
              searching:  true,
              ordering:   true,
              bInfo:      true,  
              aoColumnDefs: [{ "bVisible": false, "aTargets": 0 }],
              "data":     dataSet,
              "columns": [
              { "data": "priority" },
              {
                "render": function (data, type, JsonResultRow, meta) {
                    return '<img src="{{asset('employee_image')}}/'+JsonResultRow.images+'" style="height:30px; width:30px; border-radius: 30px;"/>';
                }
              },
              { "data": "employee_name" },
              { "data": "location_name" },
              { "data": "designation_name" },
              { "data": "depertment_name" },
              { "data": "category_name" },
              { "data": "kpi_year" },
              { "data": "Link",
                "mRender": function (data, type, full) {
                  return '<a class="btn btn-sm btn-success" href="{{URL::to('/')}}/kpi_employee/'+full.hrm_kpi_assesment_date_id+'/'+full.id+'/create"> <span ></span> Create</a>';
                }
              },
              ],
              order: [ 0, 'asc' ]
            });
        }
      }); 
  }








    $('#list_table tbody').on('click', 'td.details-control', function () {
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