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
    <h3 class="box-title" style="margin-bottom: 20px;">Employee Bonus List Details</h3>


    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <select style="width: 100%;" class="form-control bonus_name" name="bonus_name" id="bonus_name" required>
                </select>
            </div>
        </div>

        <div class="col-md-1">
            <a href="{{ url('/add_employee_to_bonus') }}" class="modalLink btn btn-primary btn-sm" data-title="Add Employee To Bonus" modal footer-none>
                <i class="fa fa-plus"></i>
                Add Employee
            </a>
        </div>
    </div>

      <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">

          <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
               <select class="form-control" id="department" name="department" style="width: 100%;" >
              </select>
          </div>

          <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
              <select class="form-control section" id="section" name="section" style="width: 100%;" >
              </select>
          </div>


          <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
             <select class="form-control category" id="category" name="category" style="width: 100%;" >
              </select>
          </div>
           <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
                        
                      
                <select class="form-control employee_status" multiple="multiple" name="employee_status[]" style="width: 100%;">
                </select>
                
            </div>

        </div>
             





    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>

  <div class="box-body">
    <div class="row">
      <div class="form-group col-lg-12 col-md-12 col-xs-12">
        <table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th style="width: 15%">Employee Name</th>
              <th style="width: 15%">Department</th>
              <th style="width: 15%">Designation</th>
              <th style="width: 10%">Payment Mode</th>
              <th style="width: 10%">Account No</th>
              <th style="width: 10%">Salary Amt</th>
              <th style="width: 5%">Amount Type</th>
              <th style="width: 10%">Bonus Amt</th>
              <th style="width: 5%">Edit</th>
              <th style="width: 5%">Delete</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
          <tfoot>
            <tr>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
            </tr>
          </tfoot>
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

$(document).ready(function($) {

   $bonusData= $('.bonus_name').select2({
      placeholder: 'Select a Bonus Name',
      allowClear: true,
        ajax: {
            dataType: 'json',
            url: "{{URL::to('/')}}/process_bonus_list",
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
   $employee_status = $('.employee_status').select2({
            placeholder: 'Enter an Employee Status'
            , allowClear: true
            , ajax: {
                dataType: 'json'
                , url: "{{URL::to('employeestatus_list_data')}}"
                , delay: 250
                , data: function(params) {
                    return {
                        term: params.term
                        , apply_old_info: $('input[name=apply_old_info]:checked').val()
                        , location: $('#attendanceLocation option:selected').val()
                    }
                }
                , processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data
                        , pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                }
                , cache: true
            }
        });

    $bonusData.on('select2:select', function (e) {
        dataLoad();
    });

    $bonusData.on('select2:unselect', function (e) {
        $('#bonus_name').val(null).trigger("change");
        dataLoad();
    });

     $employee_status.on('select2:select', function (e) {
        dataLoad();
    });
     $employee_status.on('select2:unselect', function (e) {
        dataLoad();
    });



   $department= $('#department').select2({
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

      $department.on('select2:select', function (e) {
        dataLoad();
      });


      $department.on('select2:unselect', function (e) {
          $('#department').val(null).trigger("change");
          dataLoad();
      });


      $section= $('#section').select2({
        placeholder: 'Enter Sub-department',
        allowClear: true,
        ajax: {
          dataType: 'json',
          url: '{{URL::to('/')}}/section_list_data',
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

      $section.on('select2:select', function (e) {
        dataLoad();
      });


      $section.on('select2:unselect', function (e) {
          $('#section').val(null).trigger("change");
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



    dataLoad = function(){

      $.ajax({
        type:   'POST',
        url :   "{{URL::to('/')}}/bonuslistdata",

        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
        data:   {
           bonus_name: $("#bonus_name").val(),
           department: $("#department").val(),
           section: $("#section").val(),
           category: $("#category").val(),
           employee_status: $(".employee_status").val(),
        },

        dataType: 'json',
        success: function(data) {
          var dataSet = data.data;
            table = $('#list_table').DataTable( {
              destroy:    true,
              paging:     false,
              searching:  true,
              ordering:   true,
              bInfo:      true,
              // aoColumnDefs: [{ "bVisible": false, "aTargets": [9] }],
              footerCallback: function ( row, data, start, end, display ) {
                  var api = this.api(), data;
                  var intVal = function ( i ) {
                      return typeof i === 'string' ?
                          i.replace(/[\$,]/g, '')*1 :
                          typeof i === 'number' ?
                              i : 0;
                  };

                    total_amount = api
                      .column(5)
                      .data()
                      .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                      },0);

                    bonus_amount = api
                      .column(7)
                      .data()
                      .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                      },0);



                $( api.column( 4 ).footer() ).html('Total Salary:');
                $( api.column( 5 ).footer() ).html(total_amount);
                $( api.column( 6 ).footer() ).html('Total Bonus:');
                $( api.column( 7 ).footer() ).html(bonus_amount);
              },
              "data":     dataSet,
              "columns": [
              { "data": "Link",
                  "mRender": function (data, type, full) {
                    return '<a target="_blank" href="{{URL::to('/')}}/employeeinfo/'+full.employee_id+'">'+full.employee_name+'</a>';
                }
              },
              { "data": "depertment_name" },
              { "data": "designation_name" },
              { "data": "payment_mode" },
              { "data": "account_no" },
              { "data": "salary_amount" },
              { "data": "amount_type" },
              { "data": "bonus_amount" },
              { "data": "Link",
                "mRender": function (data, type, full) {
                  return '<a href="{{URL::to('/')}}/employeebonus/'+full.id+'/edit"> <span class="glyphicon glyphicon-edit"></span> Edit</a>';
                }
              },
              { "data": "Link",
                "mRender": function (data, type, full) {
                    return '<a href="{{ url('/employeebonus/') }}/'+full.id+'/cancel"  onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-sm btn-flat"><span class="glyphicon glyphicon-trash"></span> Delete</a>';
                }
              },
              ],
              order: [ 9, 'asc' ]
            });
        }
      });

     }

});
</script>
