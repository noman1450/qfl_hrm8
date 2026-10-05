<!-- employee_list -->
@extends('layouts.main')

<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@endsection

<!-- content -->
@section('content')
<div class="box box-default">
	<div class="box-header with-border">
		<h3 class="box-title">Waiting for approval</h3>
	</div>


	<div class="box-body">
		<div class="row">
	        <div class="form-group col-lg-12 col-md-12 col-xs-12">
				<table id="list_table" class="table table-bordered table-hover" style="overflow: auto;" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th></th>
                            <th></th>
                            <th style="width: 5%"></th>
                            <th style="width: 22%">Employee Name</th>
                            <th style="width: 5%">Unique Code</th>
                            <th style="width: 15%">Designation</th>
                            <th style="width: 13%">Department</th>
                            <th style="width: 12%">Joining</th>
                            <th style="width: 10%">Contact</th>
                            <th style="width: 13%">Job Placement</th>
                            <th style="width: 05%">Action</th>
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


{{-- Start Modal --}}
<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom: 0px;height: 50px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title">Approve Form</h3>
            </div>

            <div class="modal-body">
                <form action="{{ url('/employee_joining_approvals') }}" method="post" style="margin: 0;">
                    @csrf

                    <input type="hidden" id="hrm_employee_job_info_id" name="hrm_employee_job_info_id" >
                    {{-- <input type="hidden" id="hrm_employee_job_approval_id" name="hrm_employee_job_approval_id" > --}}

                    <div class="panel" style="border: 1px solid #00000012; box-shadow: none;">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-4">
                                    Name
                                </div>
                                <div class="col-md-8">
                                    <div style="display: flex; align-items: center; column-gap: 20px;">
                                        <img src="" alt="" style="height: 50px; width: 50px; border-radius: 9999px;" id="employeeImage">

                                        <div style="display: flex; flex-direction: column; row-gap: 5px;">
                                            <strong style="font-size: 14px" id="employee_name"></strong>
                                            <span style="font-size: 13px" id="category_name"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr style="margin-top: 10px; margin-bottom: 10px;">

                            <div class="row">
                                <div class="col-md-4">
                                    Department
                                </div>
                                <div class="col-md-8">
                                    <strong style="font-size: 14px" id="department_name"></strong>
                                </div>
                            </div>

                            <hr style="margin-top: 10px; margin-bottom: 10px;">

                            <div class="row">
                                <div class="col-md-4">
                                    Designation
                                </div>
                                <div class="col-md-8">
                                    <strong style="font-size: 14px" id="designation_name"></strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="forward">Forward</label>
                                <select id="forward" class="form-control" name="forward" required>
                                    <option value="2">Final Decision</option>
                                    <option value="1">Forward</option>
                                </select>
                            </div>

                            <div class="forward_class">
                                <label for="forward_employee_name">Approve By(Forward)</label>
                                <select style="width: 100%;" id="forward_employee_name" name="forward_employee_name">
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="comment">Comment</label>
                                <textarea class="form-control" placeholder="Write Comment" id="comment" name="comment" rows="5"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer" style="padding-right: 0;">
                        <button type="submit" class="btn btn-sm btn-success" id="action" name="action" value="1">Accept</button>
                        <button type="submit" class="btn btn-sm btn-danger" id="reject" name="action" value="2">Reject</button>
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
{{-- End Modal --}}


<!-- script -->
@section('script')
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('bootstrap/js/bootstrap.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

<script>
$(document).ready(function($) {
    function loadTable() {
        $("#list_table").DataTable({
            destroy:        true,
            responsive:     true,
            processing:     true,
            serverSide:     true,
            paging:         true,
            lengthChange:   true,
            searching:      true,
            ordering:       true,
            info:           true,
            autoWidth:      false,
            width:          "100%",
            scrollX:        true,
            scrollY:        "calc(100vh - 320px)",

            aoColumnDefs: [
                { "bVisible": false, "aTargets": [0,1] },
            ],

            ajax: {
                url: "{{ url('employee_joining_approvals') }}",
                type: "GET",
                dataType: "json",
            },

            columns: [
                { "data": "priority" },
                { "data": "employee_name" },
                {
                    "render": function (data, type, JsonResultRow, meta) {
                        return '<img src="{{asset('employee_image')}}/'+JsonResultRow.Images+'" style="height:30px; width:30px; border-radius: 30px;"/>';
                    }
                },
                { "data": "Link",
                    "mRender": function (data, type, full) {
                        return '<a target="_blank"  href="{{URL::to('/')}}/employeeinfo/'+full.id+'">'+full.employee_name+'</a>';
                    }
                },
                { "data": "Unique_Code" },
                { "data": "designation_name" },
                { "data": "depertment_name" },
                { "data": "joining_date" },
                { "data": "contact_number" },
                { "data": "location_name" },
                { "data": "Link", name: 'action', orderable: false, searchable: false },
            ],
            order: [[0, 'desc']]
        })
    }

    loadTable()
});
</script>
@endsection
