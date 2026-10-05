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
                        Daily Completed Task List
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
                            <div class="col-12">
                                <div class="form-group">
                                    <button type="button" class="btn btn-info pull-right" id="printData" style="display:none">
                                        <i class="fa fa-print"></i>
                                        Print
                                    </button>
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
                                <div class="form-group">
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
                                                <th style="width: 15%">Task Name</th>
                                                <th style="width: 15%">Employee Name</th>
                                                <th style="width: 10%">Assigned Date</th>
                                                <th style="width: 10%">Complete Date</th>
                                                <th style="width: 20%">Comments</th>
                                                <th style="width: 10%">Status</th>
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

    $employee = $('#employee_id').select2({
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

    $employee.on('select2:select', function(e) {
        $('#printData').show()
    })
    $employee.on('select2:unselect', function(e) {
        $('#printData').hide()
    })

	// Call the datatable..
	var table = $('#pendingTaskDatatable').DataTable({
      destroy:    	  true,
      responsive:     true,
      processing:     true,
      serverSide:     true,
      paging:         false,
      lengthChange:   true,
      searching:      true,
      ordering:       true,
      info:           true,
      autoWidth:      false,
      width:          "100%",

      ajax: {
        url: "{{ url('get-daily_complete_task_list-data') }}",
        type: "POST",
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        dataType: "json",
        data: function (query) {
            query.from_date = $('#from_date').val()
            query.to_date = $('#to_date').val()
            query.employee_id = $('#employee_id').val()
            query.priority = $('#priority').val()
        }
      },
      columns: [
        { data: "task_name" },
        { data: "employee_name" },
        { data: "assign_date" },
        { data: "complete_date" },
        { data: "comments" },
        { data: "status" },
        { data: "Priority" },
      ],
      order: [[0, 'desc']]
	});

    $("#additionalSearchForm").click(function(e) {
        e.preventDefault()
        table.draw(true);
    });

    $(document).on('click', '#printData', function(e) {
        e.preventDefault();

        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();
        var employee_id = $("#employee_id").val();

        $.ajax({
            type: 'GET',
            url: "{{ url('/daily_complete_task_print') }}",

            data: { from_date: from_date, to_date: to_date, employee_id: employee_id },

            dataType: 'html',

            success: function(data) {
                $('body').prepend(`<div id="printThis">${data}</div>`);
                var prtContent = document.getElementById("printThis");
                $('#printThis').remove();

                var WinPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');

                setTimeout(function() {
                    WinPrint.document.write(prtContent.innerHTML);
                    WinPrint.document.close();
                    WinPrint.focus();
                    WinPrint.print();
                    WinPrint.close();
                }, 250);
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    })
</script>
@stop
