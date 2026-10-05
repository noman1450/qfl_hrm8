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
		<h3 class="box-title">Manual Attandance Pending List</h3>


	    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">


          <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
          <a href="{{ URL::to('manual_attendance_entry')}}"><input type="button" value="Create New" class="btn-success btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold;"></a>

          </div>

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
	            <select class="form-control" id="location" name="location" style="width: 100%;" required>
                @foreach ($default_user_location as $keys)
                      <option value={{$keys->id}} selected>{{$keys->location_name}}</option>
                @endforeach
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
			<div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">
				<table id="designation_list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
					<thead>
						<tr>
							<!-- <th style="width: 05%"></th> -->
							{{-- <th style="width: 05%">SL</th> --}}
							<th style="width: 15%">Employee Name</th>
							<th style="width: 10%">Department</th>
							<th style="width: 8%">Designation</th>
							<th style="width: 8%">Type</th>
							<th style="width: 8%">Punch Date</th>
                            <th style="width: 8%">Punch Time</th>
                            <th style="width: 15%">Comment</th>
                            <th style="width: 20%">Entry By</th>
							<th style="width: 05%">Delete</th>
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
        type:   'POST',
        url :   "{{URL::to('/')}}/manual_attendancelistdata",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
        data:   {
                   date_from: $("#date_from").val(),
                   date_to: $("#date_to").val(),
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
            //   { "data": null,
            //     "render": function (data, type, row, meta) {
            //         return meta.row + meta.settings._iDisplayStart + 1;
            //     }
            //   },

              { "data": "Link",
                "mRender": function (data, type, full) {
                    return '<a target="_blank"  href="{{URL::to('/')}}/employeeinfo/'+full.hrm_employee_id+'">'+full.employee_name+'</a>';
                }
              },
              { "data": "depertment_name" },
              { "data": "designation_name" },
              { "data": "typess" },
              { "data": "punch_date" },
              { "data": "punch_time" },
              { "data": "comment" },
              { "data": "users_name" },
              { "data": "Link",
                "mRender": function (data, type, full) {
                    var url = full.source_type == 'Manual'
                        ? '/manual_attendance/'+full.id+'/delete'
                        : '/manual_attendance/'+full.id+'/cancel';
                    return '<a href="{{URL::to('/')}}'+url+'"  onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-sm btn-flat"><span class="glyphicon glyphicon-trash"></a>';
                }
              },
              ],
              // "order": [[1, 'asc']]
              order: [ 6, 'asc' ]
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
