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

	<div class="box-body" id="daily_tab">

	</div>
</div>
@endsection

@section('script')
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>

<script>
$(document).ready(function($) {
    $('#date_from').datepicker({
        autoclose: true
    });

    $('#date_to').datepicker({
        autoclose: true
    });

    $(document).on('change', '.onchange', function() {
        getDetails($('#date_from').val(), $('#date_to').val())
    })

    getDetails()

    function getDetails(from_date = $('#date_from').val(), to_date = $('#date_to').val()) {
        var query = {
            from_date,
            to_date,
        }

        var url = "{{ url('my_attendance_details/'. request()->segment(2)) }}?" + $.param(query);

        $.get(url).then(data => {
            $('#daily_tab').html(data)
        })
    }
});
</script>
