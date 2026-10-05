@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}"></script>
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
@endsection

@section('content')
<div class="box box-default">
	<div class="box-header with-border">
		<h3 class="box-title">Employee Leave Application List</h3>
	    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
	        <a href="{{ URL::to('employeeleave/create')}}"><input type="button" value="Create New" class="btn-success btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold;"></a>

            <a href="{{ URL::to('leaveapprovedlist')}}"><input type="button" value="Leave Approved List" class="btn-info btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold; margin-left: 15px; "></a>

            <a href="{{ URL::to('leaverejectedlist')}}"><input type="button" value="Leave Rejected List" class="btn-danger btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold; margin-left: 15px; "></a>
            <div class="col-md-2">
                <input type="text" class="form-control onchange" id="date_from" name="date_from" data-date-format="dd-mm-yyyy" value="{{ date('d-m-Y') }}" required readonly>
            </div>

            <div class="col-md-2">
                <input type="text" class="form-control onchange" id="date_to" name="date_to" data-date-format="dd-mm-yyyy" value="{{ date('d-m-Y') }}" required readonly>
            </div>

            <div class="col-1">
                <input type="checkbox" class="onchange" id="apply_date_range" name="apply_date_range" value="">
                <label for="apply_date_range">Apply date range</label><br>
            </div>
        </div>


		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>

	<div class="box-body">
        <div class="row">
            <div class="form-group col-xs-12" style="overflow: auto;">
                <table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th style="width: 10%">Employee Name</th>
                            <th style="width: 10%">Location</th>
                            <th style="width: 10%">Leave Type</th>
                            <th style="width: 10%">Date From</th>
                            <th style="width: 10%">Date To</th>
                            <th style="width: 10%">Pay Mode</th>
                            <th style="width: 10%">Days</th>
                            <th style="width: 10%">Comment</th>
                            <th style="width: 10%">Apply To</th>
                            <th style="width: 10%">Entry by</th>
                            <th style="width: 5%">Print</th>
                            <th style="width: 5%">Delete</th>
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
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

<script>
$(document).ready(function($) {
    $('#date_from').datepicker({
        autoclose: true
    });

    $('#date_to').datepicker({
        autoclose: true
    });


    dataLoad = function(){
        let checkStatus = $("#apply_date_range").is(':checked');

        $.ajax({
            type: 'POST',
            url: "{{URL::to('/')}}/employeeleave_list",
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            dataType: 'json',
            data: {
                apply_date_range: checkStatus ,
                date_from : $("#date_from").val(),
                date_to : $("#date_to").val()
            },
            success: function(data) {
                var dataSet = data.data;
                table = $('#list_table').DataTable( {
                    destroy:    true,
                    paging:     true,
                    searching:  true,
                    ordering:   true,
                    bInfo:      true,
                    "data":     dataSet,

                    "columns": [
                        { "data": "Link",
                            "mRender": function (data, type, full) {
                                return '<img src="{{asset('employee_image')}}/'+full.Images+'" style="height:30px; width:30px; border-radius: 30px;"/>';
                            }
                        },
                        { "data": "Link",
                            "mRender": function (data, type, full) {
                                return '<a target="_blank" href="{{URL::to('/')}}/employeeinfo/'+full.id+'">'+full.employee_name+'</a>';
                            }
                        },
                        { "data": "location_name" },
                        { "data": "leave_type" },
                        { "data": "date_from" },
                        { "data": "date_to" },
                        { "data": "payment_mode" },
                        { "data": "days" },
                        { "data": "comment" },
                        { "data": "apply_to" },
                        { "data": "users_name" },
                        { "data": "Link",
                            "mRender": function (data, type, full) {
                                return '<a href="{{URL::to('/')}}/print_leave_form/'+full.leave_app_id+'"  target="_blank" class="btn btn-info btn-sm btn-flat"><span class="glyphicon glyphicon-print"></span> Print</a>';
                            }
                        },
                        { "data": "Link",
                            "mRender": function (data, type, full) {
                                return '<a href="{{URL::to('/')}}/employeeleave/'+full.leave_app_id+'/cancel"  onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-sm btn-flat"><span class="glyphicon glyphicon-trash"></span> Delete</a>';
                            }
                        },
                    ],

                    "order": [[1,'asc']]
                });
            }
        });
    }

    $(document).on('click',"#apply_date_range",function(){
        dataLoad();
    })
    $(document).on('change',".onchange",function(){
        dataLoad();
    })

    dataLoad();
});
</script>

@endsection
