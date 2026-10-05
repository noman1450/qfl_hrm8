@extends('layouts.main')

@section('styles')
    <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
    <link rel="stylesheet" href="{{ asset('plugins/datetimepicker/style.css') }}">
@stop

@section('content')
<div class="box box-default">
    <div class="box-body">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#Asset_List" data-toggle="tab" aria-expanded="true">
                        Task List
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                @if (session('message'))
                    <div class="alert alert-success" alert-dismissible>
                        {{ session('message') }}

                        <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger" alert-dismissible>
                        {{ session('error') }}

                        <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                    </div>
                @endif

                <div class="tab-pane active" id="Asset_List">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label style="cursor:pointer">
                                        <input type="radio" name="choose" value="assign_date" style="cursor:pointer">
                                        Assign Date
                                    </label>

                                    <label style="margin-left:20px;cursor:pointer">
                                        <input type="radio" name="choose" value="due_date" style="cursor:pointer">
                                        Due Date
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-right">
                                    <button type="button" id="reset-filter" class="btn btn-primary">Reset Filter</button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-1">
                                <a href="#" data-toggle="modal" data-target="#taskModal" class="btn-success btn btn-sm button pull-left" style="font-size:12px;font-weight:bold;margin-bottom:10px;margin-top:30px">
                                    Task Create
                                </a>
                            </div>
                            <div class="col-md-3" style="padding-right: 5px;">
                                <div class="form-group">
                                    <label>From Date</label>
                                    <div class="input-group date">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i> </div>
                                        <input type="text" class="form-control datepicker" name="from_date" id="from_date" value="{{ date('d-m-Y') }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3" style="padding-right: 1px;">
                                <div class="form-group">
                                    <label>To Date</label>
                                    <div class="input-group date">
                                        <div class="input-group-addon"> <i class="fa fa-calendar"></i> </div>
                                        <input type="text" class="form-control datepicker" name="to_date" id="to_date" value="{{ date('d-m-Y') }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3" style="padding-right: 1px;">
                                <div class="form-group">
                                    <label>Employee</label>
                                    <select name="employee_id" id="employee_id" class="form-control"></select>
                                </div>
                            </div>

                            <div class="col-md-1" style="padding-right: 1px;">
                                <div class="form-group">
                                    <label>Priority</label>
                                    <select name="priority" id="priority" class="form-control">
                                        <option value="">-- Select --</option>
                                        <option>Low</option>
                                        <option>Medium</option>
                                        <option>High</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-1">
                                <div class="form-group text-right">
                                    <button id="additionalSearchForm" class="btn btn-success" style="margin-top: 25px">Search</button>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow:auto;">
                                    <table id="taskDatatable" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th style="width: 20%">Task Name</th>
                                                <th style="width: 5%">Status</th>
                                                <th style="width: 15%">Assigned To</th>
                                                <th style="width: 10%">Task Type</th>
                                                <th style="width: 15%">Assign Date</th>
                                                <th style="width: 15%">Due Date</th>
                                                <th style="width: 10%">Duration</th>
                                                <th style="width: 5%">Priority</th>
                                                <th style="width: 5%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" data-backdrop="static" id="taskModal" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            {!! Form::open(['method'=>'POST', 'action'=>['EmployeeTaskManagementController@store'], 'onkeypress' => "return event.keyCode != 13;", 'id'=>'jobDescriptionForm']) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="groupAddLabel">Add/Update</h4>
                </div>
                <div class="modal-body" style="padding: 0px;">
                    <div class="col-lg-12 entry_panel_body ">
                        <input type="hidden" class="form-control" id="task_id" name="task_id"/>
                        <input type="hidden" class="form-control" id="assign_id" name="assign_id"/>
                    </div>
                </div>

                <div class="modal-body" style="margin-top: -20px">
                    <label>Task Name <span class="text-danger">*</span></label>
                    <input class="form-control" name="task_name" id="task_name" placeholder="Task Name.." required>
                </div>

                <div class="modal-body" style="margin-top: -20px">
                    <label>Task Type <span class="text-danger">*</span></label>
                    <select name="hrm_employee_task_type_id" id="hrm_employee_task_type_id" class="form-control" style="width:100%;display:block" required>
                    </select>
                </div>

                <div class="modal-body" style="margin-top: -20px">
                    <label class="control-label">Assign Date</label>
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control pull-right" id="assign_date" name="assign_date" autocomplete="off" readonly>
                    </div>
                </div>

                <div class="modal-body" style="margin-top: -20px">
                    <label class="control-label">Due Date</label>
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control pull-right" id="due_date" name="due_date" autocomplete="off" readonly>
                    </div>
                </div>

                <div class="modal-body" style="margin-top: -20px">
                    <label>Employee <span class="text-danger">*</span></label>
                    <select name="hrm_employee_id" id="hrm_employee_id" class="form-control" style="width:100%;display:block">
                    </select>
                </div>

                <div class="modal-body" style="margin-top: -20px">
                    <label>Priority <span class="text-danger">*</span></label>
                    <select name="priority" id="priorityModal" class="form-control">
                        <option value="">-- Select --</option>
                        <option>Low</option>
                        <option>Medium</option>
                        <option>High</option>
                    </select>
                </div>


                <div class="modal-footer">
                    <div>
                        <button type="button" class="btn btn-default closeId" data-dismiss="modal">Close</button>
                        <input type="submit" class="btn btn-success btn-flat pull-right" value="Submit" id="jobdsBtn">
                    </div>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@stop

@section('script')
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.min.js"></script>
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('plugins/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('plugins/datetimepicker/script.js') }}"></script>

<script>
    $(document).ready(function() {
        $('.datepicker').datetimepicker({
            "allowInputToggle": true,
            "showClose": true,
            "showClear": true,
            "showTodayButton": true,
            "useCurrent": false,
            "ignoreReadonly": true,
            "format": "DD-MM-YYYY",
        })

        $('#assign_date, #due_date').datetimepicker({
            "allowInputToggle": true,
            "showClose": true,
            "showClear": true,
            "showTodayButton": true,
            "useCurrent": false,
            "ignoreReadonly": true,
            "daysOfWeekDisabled": [5, 6],
            "format": "MM/DD/YYYY hh:mm:ss A",
        });

        $('#hrm_employee_id, #employee_id').select2({
            placeholder: 'Search Employee',
            allowClear: true,
            ajax: {
                dataType: 'json',
                url: "{{ url('/join_employee_list') }}",
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
                    }
                },
                cache: true
            }
        });

        $('#hrm_employee_task_type_id').select2({
            placeholder: 'Search Task Type',
            allowClear: true,
            ajax: {
                dataType: 'json',
                url: "{{ url('/hrm_employee_task_type_list') }}",
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
                    }
                },
                cache: true
            }
        });

        // Call the datatable..
        function assetTable() {
            var table = $('#taskDatatable').DataTable({
                destroy:    	true,
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
                aoColumnDefs: [{ "bVisible": false, "aTargets": [0] }],

                ajax: {
                    url: "{{ url('get-employee_task_management-data') }}",
                    type: "POST",
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    dataType: "json",
                    data: function (query) {
                        query.from_date = $('#from_date').val()
                        query.to_date = $('#to_date').val()
                        query.employee_id = $('#employee_id').val()
                        query.priority = $('#priority').val()
                        query.choose = $("input:radio[name='choose']:checked").val()
                    }
                },
                columns: [
                    { data: "id" },
                    { data: "task_name" },
                    { data: "status" },
                    { data: "employee_name" },
                    { data: "task_type_name" },
                    { data: "assign_date" },
                    { data: "due_date" },
                    { data: "duration" },
                    { data: "Priority" },
                    { data: "Link" },
                ],
                order: [[0, 'desc']]
            });

            $("#additionalSearchForm").click(function(e) {
                e.preventDefault()
                table.draw(true);
            });

            $('#taskDatatable').on('click', '.showme', function(e) {
                e.preventDefault()

                $('#task_id').val($(this).data('task_id'));
                $('#task_name').val($(this).data('task_name'));
                $('#assign_id').val($(this).data('assign_id'));

                $('#assign_date').val($(this).data('assign_date'));
                $('#due_date').val($(this).data('due_date'));
                $('#priorityModal').val($(this).data('priority'));



                var taskType = new Option($(this).data('task_type_name'), $(this).data('hrm_employee_task_type_id'), true, true);
                $('#hrm_employee_task_type_id').append(taskType).trigger('change.select2');

                var hrmEmployee = new Option($(this).data('employee_name'), $(this).data('hrm_employee_id'), true, true);
                $('#hrm_employee_id').append(hrmEmployee).trigger('change.select2');

                $('#taskModal').modal('show');
            });
        }

        assetTable()


        $('#reset-filter').click(function(e) {
            e.preventDefault();

            $("input:radio[name='choose']:checked").prop('checked', false)

            var hrmEmployee = new Option('', '', true, true);
            $('#employee_id').append(hrmEmployee).trigger('change.select2');

            $('#priority').val('')

            assetTable()
        })

        $("#taskModal").on("hidden.bs.modal", function () {
            $('#task_id').val('');
            $('#task_name').val('');
            $('#assign_id').val('');

            $('#assign_date').val('');
            $('#due_date').val('');
            $('#priority').val('');

            $('#hrm_employee_task_type_id').val('');
            $('#hrm_employee_task_type_id').text('');

            $('#hrm_employee_id').val('');
            $('#hrm_employee_id').text('');
        });
    })
</script>
@stop
