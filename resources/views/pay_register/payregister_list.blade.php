
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

    <h3 class="box-title"> Pay Register list</h3>
    <a href="{{ route('employee_wise_salary_process') }}" class="btn btn-sm btn-outline-primary"> + Add Employee </a>


      <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">

          <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
              <select class="form-control year onchange" id="year" name="year" style="width: 100%;" required>
                   <option value={{$running_month_year[0]->year_id}} selected>{{$running_month_year[0]->year_id}}</option>
              </select>
          </div>

          <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">

              <select  class="form-control col-lg-12 onchange" id="month_name" name="month_name" style="width: 100%;" >
                   <option value={{$running_month_year[0]->hrm_month_id}} selected>{{$running_month_year[0]->month_name}}</option>
              </select>
          </div>


          <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
              <select  class="form-control col-lg-12" id="location" name="location" style="width: 100%;"  required>
                  <option value={{$running_month_year[0]->hrm_location_id}} selected>{{$running_month_year[0]->location_name}}</option>
              </select>
          </div>


         <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
              <select  class="form-control col-lg-12" id="salary_master" name="salary_master" style="width: 100%;"  required>
                    {{-- <option value={{$running_month_year[0]->master_id}} selected>{{$running_month_year[0]->declaration_date}}</option> --}}
              </select>
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

          {{-- <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
                  
                    <select class="form-control" id="employee_type_id" style="width: 100%;"  >

                    </select>
                
            </div> --}}
            <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">

        
             <select class="form-control employee_type" id="employee_type" name="employee_type[]" multiple="multiple" style="width: 100%;" >
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
        <table id="payRegisterDatatable" class="table table-bordered table-hover" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th style="width: 0%">EmpName</th>
              <th style="width: 0%">EmpName</th>
              <th style="width: 5%">Dtls</th>
              <th style="width: 20%">Employee Name</th>
              <th style="width: 10%">Pay Mode</th>
              <th style="width: 10%">A/C Number</th>
              <th style="width: 10%">A/C Code</th>
              <th style="width: 10%">Location</th>
              <th style="width: 10%">Payable Days</th>
              <th style="width: 10%">Salary Amt</th>
              <th style="width: 15%">Action</th>
              <!-- <th style="width: 10%">Print</th> -->

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
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>




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
$employee_type = select2Dropdown("#employee_type", "{{ url('/employeestatus_list_data') }}", "Enter Employee Type");
  $(document).ready(function($) {

      for (i = new Date().getFullYear(); i > 2018; i--){
           $('.year').append($('<option />').val(i).html(i));
      }

      $(".onchange").change(function(){
          dataLoad();
      });

     $role= $('#location').select2({
        placeholder: 'Choose Location Mandatory',
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
        $('#salary_master').val(null).trigger("change");
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

      $('#month_name').select2({
        placeholder: 'Enter Month  Name',
        allowClear: true,
        ajax: {
          dataType: 'json',
          url: '{{URL::to('/')}}/monthlist',
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


    // $masterdata = $('#salary_master').select2({
    //     placeholder: 'Declaration Date',
    //     allowClear: true,
    //     ajax: {
    //         dataType: 'json',
    //         url: '{{URL::to('/')}}/salary_master_listdata',
    //         delay: 250,
    //         data: function(params) {
    //             return {
    //                 term: params.term,
    //                 hrm_month_id: $('#month_name').val(),
    //                 year_id: $('#year').val(),
    //                 hrm_location_id:$('#location').val(),
    //                 status:1,
    //             }
    //         },
    //         processResults: function (data, params) {
    //             params.page = params.page || 1;
    //             return {
    //                 results: data
    //             };
    //         },
    //         cache: true
    //     }
    // });

    // $masterdata.on('select2:select', function (e) {
    //     dataLoad();
    // });


    // $masterdata.on('select2:unselect', function (e) {
    //     $('#salary_master').val(null).trigger("change");
    //     dataLoad();
    // });

    $(document).on('change', '#month_name, #year, #location', function() {
        setDeclarationDate();
    })

    $(document).on('change', '#salary_master', function() {
        dataLoad();
    })

    $(document).on('change', '#employee_type', function() {
        dataLoad();
    })

    

    function dataLoad() {
        var table = $('#payRegisterDatatable').DataTable( {
            "destroy":    true,
            "processing": true,
            "serverSide": true,
            "searching":  true,
            "ordering":   true,
            "bInfo":      true,
            "paging":     false,
            "aoColumnDefs": [{ "bVisible": false, "aTargets": [1] },{ "bVisible": false, "aTargets": [2] }],
            "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api(), data;
                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };

                total_amount = api
                    .column(9)
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    },0);

                $( api.column( 8 ).footer() ).html('Total Salary:');
                $( api.column( 9 ).footer() ).html(total_amount);
            },
            ajax: {
                url: "{{URL::to('/')}}/payregisterlistdata",
                type: "POST",
                headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: function (query)  {
                    query.year = $("#year").val()
                    query.month = $("#month_name").val()
                    query.location = $("#location").val()
                    query.department = $("#department").val()
                    query.section = $("#section").val()
                    query.category = $("#category").val()
                    query.hrm_salary_generate_master_id = $("#salary_master").val(),
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
                { "data": "payment_mode" },
                { "data": "account_no" },
                { "data": "accounts_code" },
                { "data": "location_name" },
                { "data": "total_present" },
                { "data": "salary_amount" },
                { "data": "Link", name: 'action', orderable: false, searchable: false},
            ],
            "order": [[2, 'asc']]
        });

        $('#payRegisterDatatable tbody').on('click', 'td.details-control', function () {
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
    }


    setDeclarationDate()

    function setDeclarationDate() {
        let hrm_month_id = $('#month_name option:selected').val();
        let year_id = $("#year option:selected" ).val();
        let hrm_location_id = $('#location option:selected').val();
        let status = 1;

        $.get("{{ URL::to('/') }}/salary_master_listdata?hrm_month_id="+hrm_month_id+"&year_id="+year_id+"&hrm_location_id="+hrm_location_id+"&status="+status)
            .then((response) => {
                if (response.length == 0) {
                    $('#salary_master option').remove();
                } else {
                    $('#salary_master option').remove();

                    let options;
                    response.forEach(res => {
                        options += `<option value="${res.id}">${res.text}</option>`;
                    })

                    $('#salary_master').append(options);

                    dataLoad();
                }
            })
    }
});
</script>
@endsection

