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
    <h3 class="box-title">Employee Salary Details List</h3>
    <div class="box-tools pull-right">
        <a href="{{ route('employee.enroll') }}" class="btn btn-sm" style="background-color: #eaf1fb">+ New Enroll</a>
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>

    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">


       <div class="col-lg-2 col-lg-2 col-xs-12 form-group" >
          <select  class="form-control col-lg-12 year onchange" id="year" name="year" style="width: 100%;"  >
            <option value="{{$cyear}}">{{$cyear}}</option>
          </select>
        </div>



        <div class="col-lg-2 col-lg-2 col-xs-12 form-group" >
          <select  class="form-control col-lg-12 onchange" id="month_name" name="month_name" style="width: 100%;"  >
            <option value="{{$cmonth->id}}">{{$cmonth->month_name}}</option>
          </select>
        </div>



        <div class="col-lg-2 col-md-2 col-xs-12 form-group" >
          <select  class="form-control col-lg-12" id="location" name="location" style="width: 100%;"  required>
            @foreach ($default_user_location as $keys)
            <option value={{$keys->id}} selected>{{$keys->location_name}}</option>
            @endforeach
          </select>
        </div>

         <div class="col-lg-2 col-md-2 col-xs-12 form-group" >
               <select class="form-control" id="department" name="department" style="width: 100%;" >
              </select>
          </div>


           <div class="col-lg-2 col-md-2 col-xs-12 form-group" >
              <select class="form-control section" id="section" name="section" style="width: 100%;" >
              </select>
          </div>


          <div class="col-lg-2 col-md-2 col-xs-12 form-group" >
             <select class="form-control category" id="category" name="category" style="width: 100%;" >
              </select>
          </div>


    </div>




    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">

          <div class="col-lg-4 col-md-4 col-xs-12 form-group" >
             <select class="form-control employee_type" id="employee_type" name="employee_type[]" multiple="multiple" style="width: 100%;" >
              </select>
          </div>


    </div>






  <div class="box-body">
    <div class="row">
      <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">
        <table id="employeeSalaryDatatable" class="table table-bordered table-hover" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th style="width: 0%">dtls</th>
              <th style="width: 5%">Dtls</th>
              <th style="width: 5%">Dtls</th>
              <th style="width: 25%">Employee Name</th>
              <!-- <th style="width: 15%">Department</th> -->
              <!-- <th style="width: 20%">Designation</th> -->
              <th style="width: 15%">Location</th>
              <th style="width: 15%">Accounts Code</th>
              <th style="width: 10%">Pay Mode</th>
              <th style="width: 15%">Account No</th>
              <th style="width: 15%">Salary Amount</th>
              <th style="width: 5%">Edit</th>
              <!-- <th style="width: 10%">Edit FB</th> -->

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
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
    function format(d) {
      console.log(d);
      return '<table class="table table-bordered table-hover" cellspacing="0" width="100%">'+
            '<tr>'+
              '<td>'+"Salary Head"+'</td>'+
              '<td>'+"Status"+'</td>'+
              '<td>'+"Amount"+'</td>'+
              '<td>'+"Type"+'</td>'+
              '<td>'+"Head Amount"+'</td>'+
            '</tr>'+
            '<tr>'+
              '<td>'+d.salary_head+'</td>'+
              '<td>'+d.Status+'</td>'+
              '<td>'+d.amount+'</td>'+
              '<td>'+d.amount_type+'</td>'+
              '<td>'+d.salary_head_amount+'</td>'+
            '</tr>'+
          '</table>';
    }

    $(document).ready(function($) {

        // if codition diben jate curent year render na hoi

        for (i = new Date().getFullYear(); i >= 2018; i--){
            $('.year').append($('<option />').val(i).html(i));
        }

        $('#month_name').select2({
            placeholder: 'Enter Month  Name',
            allowClear: true,
            ajax: {
                dataType: 'json',
                url: "{{ url('monthlist') }}",
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

        $(".onchange").change(function() {
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

        function dataLoad() {
            table = $('#employeeSalaryDatatable').DataTable( {
                "destroy":    true,
                "processing": true,
                "serverSide": true,
                "searching":  true,
                "ordering":   true,
                "bInfo":      true,
                "paging":     false,
                "aoColumnDefs": [
                    { "bVisible": false, "aTargets": [1] },
                    { "bVisible": false, "aTargets": [2] }
                ],

                "footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;
                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '')*1 :
                            typeof i === 'number' ?
                                i : 0;
                    };

                    total_amount = api
                        .column(8)
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        },0);

                    $( api.column( 7 ).footer() ).html('Total Salary:');
                    $( api.column( 8 ).footer() ).html(total_amount);
                },

                ajax: {
                    url: "{{ url('/employeesalarylistdata') }}",
                    type: "POST",
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},

                    data: function (query) {
                        query.location = $("#location").val()
                        query.year = $("#year").val()
                        query.month = $("#month_name").val()
                        query.department = $("#department").val()
                        query.section = $("#section").val()
                        query.category = $("#category").val()
                        query.employee_type = $("#employee_type").val()
                    }
                },
                columns: [
                    {
                        "className":      'details-control',
                        "orderable":      true,
                        "data":           null,
                        "defaultContent": ''
                    },
                    { "data": "employee_name" },
                    { "data": "priority" },
                    { "data": "Link",
                        "mRender": function (data, type, full) {
                            return '<a target="_blank"  href="{{URL::to('/')}}/employeeinfo/'+full.employee_id+'">'+full.employee_name+'</a>';
                        }
                    },
                    { "data": "location_name" },
                    { "data": "accounts_code" },
                    { "data": "payment_mode" },
                    { "data": "account_no" },
                    { "data": "salary_amount" },
                    { "data": "Link", name: 'action', orderable: false, searchable: false},
                ],
                "order": [[2, 'asc']],

                // Buttons for Excel Export
                dom: 'Bfrtip', // adds buttons container
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'Excel',
                        title: 'Employee Salary',
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
                        }
                    }
                ]
            });
        }

        dataLoad();

        $('#employeeSalaryDatatable tbody').on('click', 'td.details-control', function () {
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

        $role= $('#location').select2({
            placeholder: 'Choose Location',
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

       $employee_type = $('.employee_type').select2({
            placeholder: 'Employee Type',
            ajax: {
                dataType: 'json',
                url: "{{URL::to('/')}}/employeestatus_list_data",
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

        $employee_type.on('select2:select', function (e) {
            dataLoad();
        });

        $employee_type.on('select2:unselect', function (e) {
            $('.employee_type').val(null).trigger("change");
            dataLoad();
        });

    });
</script>
@endsection
