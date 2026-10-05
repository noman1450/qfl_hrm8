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
		<h3 class="box-title">My Attandance List</h3>


	    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
	        <div class="col-md-3 form-group" style="padding-left: 0px; padding-top: 10px;">
	            <div class="input-group date">
	                <div class="input-group-addon">
	                  <i class="fa fa-calendar"></i>
	                </div>
	                <input type="text" class="form-control pull-right onchange" id="date_from" name="date_from" data-date-format="dd-mm-yyyy" value="{{ date('01-m-Y') }}" required readonly>
	            </div>
	        </div>

            <div class="col-md-3 form-group" style="padding-left: 0px; padding-top: 10px;">
	            <div class="input-group date">
	                <div class="input-group-addon">
	                  <i class="fa fa-calendar"></i>
	                </div>
	                <input type="text" class="form-control pull-right onchange" id="date_to" name="date_to" data-date-format="dd-mm-yyyy" value="{{ date('t-m-Y') }}" required readonly>
	            </div>
	        </div>
      	</div>

		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>

	<div class="box-body">
		<div class="row">
			<div class="form-group col-lg-12 col-md-12 col-xs-12 " style="overflow: auto;">
				<table id="designation_list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th>Employee Name</th>
                            <th>OnTime</th>
                            <th>Late</th>
                            <th>Weekend</th>
                            <th>Holyday</th>
                            <th>LWP</th>
                            <th>LWOP</th>
                            <th>Absent</th>
                            <th>Pay.Days</th>
                            <th></th>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>



<script>
    // function format(d) {
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
    $('#date_from').datepicker({
        autoclose: true
    });

    $('#date_to').datepicker({
        autoclose: true
    });

    $(".onchange").change(function(){
        dataLoad();
    });

    function dataLoad() {
      $.ajax({
        type: "GET",
        url: "{{ url('/my_attendance') }}",
        data: {
            date_from: $("#date_from").val(),
            date_to: $("#date_to").val(),
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
                    { "data": "EmployeeName" },
                    { "data": "Present" },
                    { "data": "Late" },
                    { "data": "WeeklyHoliday" },
                    { "data": "HolydayCount" },
                    { "data": "withPayLeaves" },
                    { "data": "withoutPayLeaves" },
                    { "data": "Absent" },
                    { "data": "payable_days" },
                    { "data": "Link", orderable: false, searchable: false},
                ],
              order: [ 1, 'asc' ]
            });
        }
      });
    }

    dataLoad();
});
</script>
