@extends('layouts.main')

@section('styles')
    <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
@stop

@section('content')
<div class="box box-default">
    <div class="box-body">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#Asset_List" data-toggle="tab" aria-expanded="true">
                        Task Type List
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

                <div class="tab-pane active" id="Asset_List">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-1">
                                <a href="#" data-toggle="modal" data-target="#taskModal" class="btn-success btn btn-sm button pull-left" style="font-size:12px;font-weight:bold;margin-bottom:15px">
                                    Task Type Create
                                </a>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow:auto;">
                                    <table id="taskDatatable" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th style="width: 12%">Task Type Name</th>
                                                <th style="width: 10%">Actions</th>
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


<div class="modal fade" id="taskModal" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            {!! Form::open(['method'=>'POST', 'action'=>['EmployeeTaskTypeController@store'], 'onkeypress' => "return event.keyCode != 13;", 'id'=>'jobDescriptionForm']) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="groupAddLabel">Add/Update</h4>
                </div>
                <div class="modal-body" style="padding: 0px;">
                    <div class="col-lg-12 entry_panel_body ">
                        <input type="hidden" class="form-control" id="task_type_id" name="task_type_id"/>
                    </div>
                </div>

                <div class="modal-body" style="margin-top: -20px">
                    <label>Task Type Name <span class="text-danger">*</span></label>
                    <input class="form-control" name="task_type_name" id="task_type_name" placeholder="Task Type Name.." required>
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
    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
    <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>

<script>

    $('.datepicker').datepicker({
        autoclose: true,
        format: 'dd-mm-yyyy'
    })

    // $('#employee_id').select2({
    //     placeholder: 'Search Employee',
    //     allowClear: true,
    //     ajax: {
    //         dataType: 'json',
    //         url: "{{ url('/join_employee_list') }}",
    //         delay: 250,
    //         data: function(params) {
    //             return {
    //                 term: params.term
    //             }
    //         },
    //         processResults: function (data, params) {
    //             params.page = params.page || 1;
    //             return {
    //                 results: data
    //             }
    //         },
    //         cache: true
    //     }
    // });

	// Call the datatable..
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

        ajax: {
            url: "{{ url('get-employee_task_type-data') }}",
            type: "POST",
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            dataType: "json",
        },
        columns: [
            { data: "task_type_name" },
            { data: "Link" },
        ],
        order: [[0, 'desc']]
	});

    // $("#additionalSearchForm").click(function(e) {
    //     e.preventDefault()
    //     table.draw(true);
    // });

    $('#taskDatatable').on('click', '.showme', function() {
        $('#task_type_id').val($(this).data('task_type_id'));
        $('#task_type_name').val($(this).data('task_type_name'));

        $('#taskModal').modal('show');
    });
</script>
@stop
