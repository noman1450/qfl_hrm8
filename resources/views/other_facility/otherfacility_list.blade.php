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
    <h3 class="box-title">Employee Fringe Benefits List</h3>

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>

  <div class="col-lg-3 col-md-3 col-xs-12 form-group" >
    <select  class="form-control col-lg-12" id="location" name="location" style="width: 100%;"  required>
        @foreach ($default_user_location as $keys)
            <option value={{$keys->id}} selected>{{$keys->location_name}}</option>
        @endforeach
    </select>
 </div>


 <div class="col-lg-3 col-md-3 col-xs-12 form-group" >
    <select  class="form-control col-lg-12 year onchange" id="year" name="year" style="width: 100%;"  >
      <option value="{{$cyear}}">{{$cyear}}</option>
    </select>
  </div>



  <div class="col-lg-3 col-md-3 col-xs-12 form-group" >
    <select  class="form-control col-lg-12 onchange" id="month_name" name="month_name" style="width: 100%;"  >
      <option value="{{$cmonth->id}}">{{$cmonth->month_name}}</option>
    </select>
  </div>


  <div class="col-lg-3 col-md-3 col-xs-12 form-group"  >
      <select  class="form-control col-lg-12" id="employeestatus" name="employeestatus" style="width: 100%;"  required>
      </select>
  </div>






  <div class="box-body">
    <div class="row">
      <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">
        <table id="other_facility_list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th style="width: 5%">Dtls</th>
              <th style="width: 5%">Dtls</th>
              <th style="width: 25%">Employee Name</th>
              <th style="width: 10%">Pay Mode</th>
              <th style="width: 10%">A/C Number</th>
              <th style="width: 10%">A/C Code</th>
              <th style="width: 15%">Location</th>
              <th style="width: 12%">Salary Amount</th>
              <th style="width: 10%">Edit</th>

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>


<script>
    function format(d) {
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

        for (i = new Date().getFullYear(); i >= 2019; i--){
            $('.year').append($('<option />').val(i).html(i));
        }

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

        $(".onchange").change(function(){
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

        function dataLoad() {
            var table = $('#other_facility_list_table').DataTable({
                "destroy":    true,
                "processing": true,
                "serverSide": true,
                "searching":  true,
                "ordering":   true,
                "bInfo":      true,
                "paging":     true,
                "aoColumnDefs": [{ "bVisible": false, "aTargets": [1] }],
                "ajax": {
                    "url": "{{URL::to('/')}}/otherfacilitylistdata",
                    "type": "POST",
                    "headers":{'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    "data":  {
                        location: $("#location").val(),
                        year: $("#year").val(),
                        month: $("#month_name").val(),
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
                    { "data": "salary_amount" },
                    { "data": "Link", name: 'action', orderable: false, searchable: false},
                ],
                "order": [[1, 'asc']]
            });


            $('#other_facility_list_table tbody').on('click', 'td.details-control', function () {
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

        dataLoad();


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
    });
</script>
@endsection

