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
@if (session('show-error'))
    <div class="alert alert-danger">
        {{ session('show-error') }}
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    </div>
@endif

<div class="box box-default">
  <div class="box-header with-border">
    <h3 class="box-title">Employee Fringe Benefits Pay Register</h3>

      <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
          <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
              <select class="form-control year onchange" id="year" name="year" style="width: 100%;" required>

                 @foreach ($year as $keys)
                      @if($keys->year_id == $running_month_year[0]->year_id){
                      <option value={{$keys->year_id}} selected>{{$keys->year_id}}</option>
                        }@else{
                      <option value={{$keys->year_id}} >{{$keys->year_id}}</option>
                    }@endif
                  @endforeach

              </select>
          </div>

          <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">

              <select  class="form-control col-lg-12 onchange" id="month_name" name="month_name" style="width: 100%;" >
                  @foreach ($running_month_year as $keys)
                      <option value={{$keys->hrm_month_id}} selected>{{$keys->month_name}}</option>
                  @endforeach
              </select>
          </div>

          <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
              <select  class="form-control col-lg-12" id="location" name="location" style="width: 100%;"  required>
                  @foreach ($default_user_location as $keys)
                        <option value={{$keys->id}} selected>{{$keys->location_name}}</option>
                  @endforeach
              </select>
          </div>

          <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
              <select  class="form-control col-lg-12" id="employeestatus" name="employeestatus" style="width: 100%;"  required>
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
        <table id="other_facility_pay_register_list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th style="width: 5%">Dtls</th>
              <th style="width: 5%">Dtls</th>
              <th style="width: 20%">Employee Name</th>
              <th style="width: 10%">Pay Mode</th>
              <th style="width: 10%">A/C Number</th>
              <th style="width: 10%">A/C Code</th>
              <th style="width: 8%">Location</th>
              <!-- <th style="width: 12%">Present Days</th> -->
              <th style="width: 10%">Salary Amt</th>
              <!-- <th style="width: 8%">Edit</th> -->
              <th style="width: 12%">Action</th>

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

        $(".onchange").change(function(){
            dataLoad();
        });

        $('#deduction_head').select2({
            placeholder: 'Enter Deduction Head',
            allowClear: true,
            ajax: {
                dataType: 'json',
                url: '{{URL::to('/')}}/salary_head_list',
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



        // for (i = new Date().getFullYear(); i > 2015; i--){
        //    $('.year').append($('<option />').val(i).html(i));
        //  }

        // $("#search").click(function(){
        //   dataLoad();
        // });

        // dataLoad();

        function dataLoad() {
            var table = $('#other_facility_pay_register_list_table').DataTable({
                "destroy":    true,
                "processing": true,
                "serverSide": true,
                "searching":  true,
                "ordering":   true,
                "bInfo":      true,
                "paging":     true,
                "aoColumnDefs": [{ "bVisible": false, "aTargets": [1] }],
                "ajax": {
                    "url": "{{URL::to('/')}}/ofpayregisterlistdata",
                    "type": "POST",
                    "headers":{'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    "data":   {
                        year: $("#year").val(),
                        month: $("#month_name").val(),
                        location: $("#location").val(),
                        employeestatus: $("#employeestatus").val(),
                    }
                },
                "columns": [
                    {
                        "className":      'details-control',
                        "orderable":      true,
                        "data":           null,
                        "defaultContent": ''
                    },
                    { "data": "employee_name" },
                    { "data": "Link",
                        "mRender": function (data, type, full) {
                            return '<a target="_blank"  href="{{URL::to('/')}}/employeeinfo/'+full.employee_id+'">'+full.employee_name+'</a>';
                        }
                    },
                    { "data": "payment_mode" },
                    { "data": "account_no" },
                    { "data": "accounts_code" },
                    { "data": "location_name" },
                    // { "data": "total_present" },
                    { "data": "salary_amount" },
                    { "data": "Link", name: 'action', orderable: false, searchable: false},
                ],
                "order": [[1, 'asc']]
            });

            $('#other_facility_pay_register_list_table tbody').on('click', 'td.details-control', function () {
                var tr = $(this).closest('tr');
                var row = table.row( tr );

                if ( row.child.isShown() ) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Open this row
                    row.child( format(row.data()) ).show();
                    tr.addClass('shown');
                }
            });
        }

        dataLoad();
    });
</script>
@endsection
