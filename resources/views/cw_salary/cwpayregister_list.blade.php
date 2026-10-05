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
@if (session('show-error'))
    <div class="alert alert-danger">
        {{ session('show-error') }}
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    </div>
@endif

<div class="box box-default">
  <div class="box-header with-border">
    <h3 class="box-title">CW Pay Register</h3>
      <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">

          <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
              <label>Year</label>
              <select class="form-control year onchange" id="year" name="year" style="width: 100%;" required>
                    <option value={{$running_month_year[0]->year_id}} selected>{{$running_month_year[0]->year_id}}</option>
              </select>
          </div>

          <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
            <label>Month Name</label>
            <select  class="form-control col-lg-12 onchange" id="month_name" name="month_name" style="width: 100%;" >
                  <option value={{$running_month_year[0]->hrm_month_id}} selected>{{$running_month_year[0]->month_name}}</option>
            </select>
          </div>

          <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
            <label>Location Name</label>
            <select  class="form-control col-lg-12" id="location" name="location" style="width: 100%;" >
                  <option value={{$running_month_year[0]->hrm_location_id}} selected>{{$running_month_year[0]->location_name}}</option>
            </select>
          </div>

          <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
            <label>Plant Name</label>

            <select  class="form-control col-lg-12" id="plant_name" name="plant_name" style="width: 100%;" >
            </select>
          </div>

          <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
            <label>Salary Type</label>

            <select  class="form-control col-lg-12" id="hrm_plant_with_section_id" name="hrm_plant_with_section_id" style="width: 100%;" >
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
        <table id="cw_payregister_list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
          <thead>
            <tr>
              <!-- <th style="width: 5%">Dtls</th> -->
              <th style="width: 18%">Employee Name</th>
              <th style="width: 15%">Department</th>
              <th style="width: 15%">Designation</th>
              <th style="width: 15%">Plant Name</th>
              <th style="width: 8%">W.Day</th>
              <th style="width: 8%">P.Days</th>
              <th style="width: 8%">Per Day</th>
              <th style="width: 10%">Salary Amt</th>
              <th style="width: 10%">Adv.Adj.</th>
              <th style="width: 10%">Due.Adj.</th>
              <th style="width: 10%">Pay Mode</th>
              <th style="width: 8%">Edit</th>
              <th style="width: 10%">Print</th>
              <th style="width: 10%">Delete</th>

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
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>



<script>
    // function format(d) {
    //   console.log(d);
    //   return '<table class="table table-bordered table-hover" cellspacing="0" width="100%">'+
    //         '<tr>'+
    //           '<td>'+"Salary Head"+'</td>'+
    //           '<td>'+"Status"+'</td>'+
    //           '<td>'+"Amount"+'</td>'+
    //           '<td>'+"Type"+'</td>'+
    //           '<td>'+"Head Amount"+'</td>'+
    //         '</tr>'+
    //         '<tr>'+
    //           '<td>'+d.salary_head+'</td>'+
    //           '<td>'+d.Status+'</td>'+
    //           '<td>'+d.amount+'</td>'+
    //           '<td>'+d.amount_type+'</td>'+
    //           '<td>'+d.salary_head_amount+'</td>'+
    //         '</tr>'+
    //       '</table>';
    // }
    $(document).ready(function($) {

        $role= $('#plant_name').select2({
            placeholder: 'Enter Plant Name',
            allowClear: true,
            ajax: {
                dataType: 'json',
                url: '{{URL::to('/')}}/plantname_list_data',
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
            $('#hrm_plant_with_section_id').val(null).trigger("change");
            dataLoad();
        });


        $role.on('select2:unselect', function (e) {
            $('#plant_name').val(null).trigger("change");
            $('#hrm_plant_with_section_id').val(null).trigger("change");
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

        $location_v= $('#location').select2({
            placeholder: 'Enter Location',
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

        $location_v.on('select2:select', function (e) {
            dataLoad();
        });


        $location_v.on('select2:unselect', function (e) {
            $('#plant_name').val(null).trigger("change");
            dataLoad();
        });


        $section =$('#hrm_plant_with_section_id').select2({
            placeholder: 'Enter Salary Type',
            allowClear: true,
            ajax: {
                dataType: 'json',
                url: '{{URL::to('/')}}/plantwithsection_list_data',
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term,
                        hrm_plant_id:$("#plant_name").val(),
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
            $('#hrm_plant_with_section_id').val(null).trigger("change");
            dataLoad();
        });

        for (i = new Date().getFullYear(); i > 2019; i--){
            $('.year').append($('<option />').val(i).html(i));
        }

        $(".onchange").change(function(){
            dataLoad();
        });

        function dataLoad() {
            table = $('#cw_payregister_list_table').DataTable({
                destroy:    true,
                paging:     true,
                searching:  true,
                ordering:   true,
                bInfo:      true,
                ajax: {
                    url: "{{ url('/cwpayregisterlistdata') }}",
                    type: "POST",
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},

                    data: function (query) {
                        query.year = $("#year").val()
                        query.month = $("#month_name").val()
                        query.location = $("#location").val()
                        query.plant_name = $("#plant_name").val()
                        query.hrm_plant_with_section_id = $("#hrm_plant_with_section_id").val()
                    }
                },
                "columns": [
                    // {
                    //   "className":      'details-control',
                    //   "orderable":      true,
                    //   "data":           null,
                    //   "defaultContent": ''
                    // },
                    { "data": "Link",
                        "mRender": function (data, type, full) {
                            return '<a target="_blank" href="{{URL::to('/')}}/employeeinfo/'+full.employee_id+'">'+full.employee_name+'</a>';
                        }
                    },
                    { "data": "depertment_name" },
                    { "data": "designation_name" },
                    { "data": "plant_name" },
                    { "data": "day_of_month" },
                    { "data": "total_present" },
                    { "data": "salary_amount" },
                    { "data": "total_salary" },
                    { "data": "adv_adjust" },
                    { "data": "due_adjust" },
                    { "data": "payment_mode" },

                    { "data": "Link",
                        "mRender": function (data, type, full) {
                            if (full.salary_genarate_type == 2) {
                                return '<a href="{{URL::to('/')}}/cwpayregister/'+full.id+'/edit"> <span class="glyphicon glyphicon-edit"></span> Edit</a>';
                            }

                            return '<a href="{{URL::to('/')}}/cwpayregister/'+full.id+'/edit" class="modalLink" data-title="Edit CW Employee Pay Register" data-modal-size="xl" footer-none> <span class="glyphicon glyphicon-edit"></span> Edit</a>';
                        }
                    },
                    { "data": "Link",
                        "mRender": function (data, type, full) {
                            return '<a target="_blank"  href="{{URL::to('/')}}/cwpayregister/'+full.id+'/print"> <span class="glyphicon btn btn-success btn-xs btn-flat  glyphicon-print "> Print</span></a>';
                        }
                    },
                    { "data": "Link",
                        "mRender": function (data, type, full) {
                            return '<a   href="{{URL::to('/')}}/cwpayregister/'+full.id+'/delete" onclick="return confirm(\'Are you sure to delete this..!\')"> <span class="glyphicon btn btn-danger btn-xs btn-flat  glyphicon-trush "> Delete</span></a>';
                        }
                    },
                ],

                order: [ 1, 'asc' ]
            });
        }

        $('#cw_payregister_list_table tbody').on('click', 'td.details-control', function () {
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

        dataLoad();
    });
</script>
@endsection
