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
		<h3 class="box-title">Waiting for Approval List</h3>


	    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
          <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
              <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right onchange datepicker" id="date_from" name="date_from" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('date_from') }}" required readonly>
              </div>
          </div>

          <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
	            <div class="input-group date">
	                <div class="input-group-addon">
	                  <i class="fa fa-calendar"></i>
	                </div>
	                <input type="text" class="form-control pull-right onchange datepicker"  id="date_to"  name="date_to" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('joining_date') }}" required readonly>
	            </div>
	        </div>

	        <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
	            <label class="checkbox-inline">
	                <input type="checkbox" id="apply_date_range" name="apply_date_range" value="1" > Apply Date Range
	            </label>
	        </div>

	        <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
	            <select class="form-control" id="location" name="location" style="width: 100%;" required>

              </select>
	        </div>


          <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
               <select style="width: 100%;" class="form-control select2" id="employee_name" name="employee_name" required autofocus >
               </select>
          </div>




      	</div>

		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>

	<div class="box-body">
		<div class="row">
            @can('PendingManualAttendanceAction')
			<div class="form-group col-lg-12 col-md-12 col-xs-12" style="padding-left: 15px;">
                <button type="button" class="btn btn-success btn-sm btn-flat" id="approve_selected"><i class="fa fa-check"></i> Approve</button>
				<button type="button" class="btn btn-danger btn-sm btn-flat" id="reject_selected"><i class="fa fa-times"></i> Reject</button>
			</div>
            @endcan
			<div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">
				<table id="designation_list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th style="width: 05%"><input type="checkbox" id="select_all"></th>
							<th style="width: 15%">Employee Name</th>
							<th style="width: 8%">Location Name</th>
							<th style="width: 10%">Department</th>
							<th style="width: 10%">Designation</th>
							<th style="width: 10%">Type</th>
							<th style="width: 8%">Punch Date</th>
                            <th style="width: 8%">Punch Time</th>
                            <th style="width: 15%">Comment</th>
                            <th style="width: 30%">Entry By</th>
							<!-- <th style="width: 15%">End Time</th> -->
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
    // function format(d) {
    //   console.log(d);
    //   return '<table class="table table-bordered table-hover" cellspacing="0" width="100%">'+
    //         '<tr>'+
    //           '<td>'+"In Time"+'</td>'+
    //           '<td>'+"Out Time"+'</td>'+
    //           '<td>'+"Working Hour"+'</td>'+
    //         '</tr>'+
    //         '<tr>'+
    //           '<td>'+d.indatetime+'</td>'+
    //           '<td>'+d.outdatetime+'</td>'+
    //           '<td>'+d.working_hour+'</td>'+
    //         '</tr>'+
    //       '</table>';
    // }
$(document).ready(function($) {
    $('.datepicker').datepicker({
      autoclose: true
    });



    $employee = $('#employee_name').select2({
        placeholder: 'Enter an Employee Name',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: "{{URL::to('/')}}/join_employee_list",
            delay: 250,
            data: function (params) {
                return {
                    term: params.term,
                 location: $("#location").val()

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

    $employee.on('select2:select', function (e) {
        dataLoad();
    });

    $employee.on('select2:unselect', function (e) {
        $('#employee_name').val(null).trigger("change");
        dataLoad();
    });


   $role= $('#location').select2({
      placeholder: 'Enter a location Mandatory',
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

      $("#apply_date_range").change(function(){
          dataLoad();
      });

    // $("#search").click(function(){
    // 	dataLoad();
    // });

    dataLoad = function(){

      if ($("#location").val() == null){
      	location_id = 0;
      }else{
      	location_id = $("#location").val();
      }


      $.ajax({
        type:   'GET',
        url :   "{{ url()->current() }}",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
        data:   {
                   date_from: $("#date_from").val(),
                   date_to: $("#date_to").val(),
                   apply_date_range: $("#apply_date_range").is(':checked') ? 1 : 0,
                   location: location_id,
                   hrm_employee_id: $("#employee_name").val(),
                },
        dataType: 'json',
        success: function(data) {
          var dataSet = data.data;
            table = $('#designation_list_table').DataTable( {
              destroy:    true,
              paging:     true,
              searching:  true,
              ordering:   true,
              bInfo:      true,
              "data":     dataSet,
              "columns": [
              { "data": "id",
                "mRender": function (data, type, full) {
                    return '<input type="checkbox" class="record_checkbox" value="'+full.id+'">';
                }
              },
            //   { "data": "Link",
            //     "mRender": function (data, type, full) {
            //         return '<a target="_blank"  href="{{URL::to('/')}}/employeeinfo/'+full.hrm_employee_id+'">'+full.employee_name+'</a>';
            //     }
            //   },
              { "data": "employee_name" },
              { "data": "location_name" },
              { "data": "depertment_name" },
              { "data": "designation_name" },
              { "data": "typess" },
              { "data": "punch_date" },
              { "data": "punch_time" },
              { "data": "comment" },
              { "data": "users_name" },
              ],
              // "order": [[1, 'asc']]
              order: [ 5, 'asc' ]
            });
        }
      });
    }

    $('#select_all').on('click', function () {
        var checked = this.checked;
        $('#designation_list_table tbody .record_checkbox').prop('checked', checked);
    });

    $('#designation_list_table tbody').on('change', '.record_checkbox', function () {
        var total   = $('#designation_list_table tbody .record_checkbox').length;
        var checked = $('#designation_list_table tbody .record_checkbox:checked').length;
        $('#select_all').prop('checked', total === checked);
    });

    function attendanceAction(action) {
        var ids = [];
        $('#designation_list_table tbody .record_checkbox:checked').each(function () {
            ids.push(this.value);
        });

        if (ids.length == 0) {
            alert('Please select at least one record!');
            return false;
        }

        if (!confirm('Do you really want to ' + action + ' the selected records?')) {
            return false;
        }

        $.ajax({
            type: 'POST',
            url : '{{URL::to('/')}}/pending_manual_attendance_action',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                ids: ids,
                action: action
            },
            success: function (data) {
                alert(data.message);
                dataLoad();
            }
        });
    }

    $('#approve_selected').on('click', function () {
        attendanceAction('approve');
    });

    $('#reject_selected').on('click', function () {
        attendanceAction('reject');
    });

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
