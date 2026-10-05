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
                        Pending Task List
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

                            <div class="col-md-2" style="padding-right: 1px;">
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
                                    <table id="pendingTaskDatatable" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th style="width: 25%">Task Name</th>
                                                <th style="width: 25%">Employee Name</th>
                                                <th style="width: 10%">Assigned Date</th>
                                                <th style="width: 10%">Due Date</th>
                                                <th style="width: 15%">Duration</th>
                                                <th style="width: 10%">Priority</th>
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

    $('#employee_id').select2({
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

    // Call the datatable..
    function pendingTable() {
        var table = $('#pendingTaskDatatable').DataTable({
            destroy:    	  true,
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
                url: "{{ url('get-pending_task_list-data') }}",
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
                { data: "task_name" },
                { data: "employee_name" },
                { data: "assign_date" },
                { data: "due_date" },
                { data: "duration" },
                { data: "Priority" },
            ],
            order: [[0, 'desc']]
        });
    
        $("#additionalSearchForm").click(function(e) {
            e.preventDefault()
            table.draw(true);
        });
    }

    pendingTable();

    $('#reset-filter').click(function(e) {
        e.preventDefault();

        $("input:radio[name='choose']:checked").prop('checked', false)

        var hrmEmployee = new Option('', '', true, true);
        $('#employee_id').append(hrmEmployee).trigger('change.select2');

        $('#priority').val('')

        pendingTable()
    })
</script>
@stop
