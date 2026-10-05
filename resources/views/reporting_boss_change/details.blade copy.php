<!-- active_employee_list -->
<!-- employee_list -->
@extends('layouts.main')

<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">


@endsection

<!-- content -->
@section('content')

    <div>
      <div id="alert-danger"></div>
      <div id="alert-success"></div>
    </div>



<div class="box box-default">
	<div class="box-header with-border">
		<h3 class="box-title">Employee List of Reporting Boss Changes</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>

	<div class="box-body">
        <div class="row">
            <div class="col-lg-2 col-md-2 col-xs-12 form-group">
                <div class="form-group">
                    <label for="date_from">From Date</label>
                    <div class="input-group date">
                        <div class="input-group-addon">
                          <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control pull-right onchange datepicker" id="date_from" name="date_from" data-date-format="dd-mm-yyyy" placeholder="dd-mm-yyyy"  value="{{date('d-m-Y')}}" required readonly>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-2 col-xs-12 form-group">
                <div class="form-group">
                    <label for="date_to">To Date</label>
                    <div class="input-group date">
                        <div class="input-group-addon">
                          <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control pull-right onchange datepicker"  id="date_to"  name="date_to" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" placeholder="dd-mm-yyyy" required readonly>
                    </div>
                </div>
            </div>
        </div>

		<div class="row">
	     <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">
				<table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
					<thead>
						<tr>

                            <th style="width: 15%">Employee Name</th>
                            <th style="width: 10%">Department</th>
                            <th style="width: 10%">Joining</th>
                            <th style="width: 8%">Contact</th>
                            <th style="width: 10%">Job Placement</th>
                            <th style="width: 10%">Transfer From</th>
                            <th style="width: 10%">Transfer To</th>
                            <th style="width: 8%">Transfer By</th>
                            <th style="width: 8%">Transfer Date</th>

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



<!-- script -->
@section('script')
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script>


$(document).ready(function($) {

    $('#date_from').datepicker({
       autoclose: true,
    });

    $('#date_to').datepicker({
       autoclose: true,
    });


    $(document).on('change','.onchange', function(){
        dataLoad();
    });


    dataLoad = function(){
          var table = $('#list_table').DataTable( {
            "destroy":    true,
            "processing": true,
            "serverSide": true,
            "searching":  true,
            "ordering":   true,
            "bInfo":      true,
            "paging":     false,
            "scrollX": false,
            "scrollY": 400,
            "ajax": {
                "url": "{{URL::to('/')}}/remporting_boss_changes_emp_list",
                "type": "POST",
                "headers":{'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                "data":   {
                    date_from: $('#date_from').val(),
                    date_to: $('#date_to').val(),
              }
            },
            "columns": [
              { "data": "employee_name" },
              { "data": "depertment_name" },
              { "data": "joining_date" },
              { "data": "contact_number" },
              { "data": "location_name" },
              { "data": "manage_from" },
              { "data": "manage_to" },
              { "data": "change_by" },
              { "data": "change_date" }
            ]
          });
    }
    dataLoad();
});
</script>
@endsection
