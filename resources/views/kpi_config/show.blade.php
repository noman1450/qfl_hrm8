@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{ asset('plugins/datatables/dataTables.bootstrap.css') }}">

<style>
.selectedText {
    color: red;
}
</style>
@endsection

@section('content')
<div class="box">
    <div class="box-header">
        <h3 style="display: flex; flex-direction: column; gap: 10px;">
            <span>{{ $hrm_kpi_set_config->title }}</span>
            <span style="font-size: 15px">{{ $hrm_kpi_set_config->financial_year }}</span>
        </h3>
    </div>

    <div class="box-body">
        <div class="nav-tabs-custom" style="margin-bottom: 0; box-shadow: unset">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#task_setup" data-toggle="tab" aria-expanded="true">
                        Task Setup
                        <small id="task-count">
                            <span id="task-count-reload">
                                ({{ count($selectedTasks) ?? 0 }})
                            </span>
                        </small>
                    </a>
                </li>
                <li class=""><a href="#mark_setup" data-toggle="tab" aria-expanded="false">Mark Setup</a></li>
                <li class=""><a href="#department_setup" data-toggle="tab" aria-expanded="false">Department Setup</a></li>
                <li class=""><a href="#final_submit" data-toggle="tab" aria-expanded="false">Final Submit</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="task_setup">
                    <div class="row">
                        <div class="col-md-7">
                            <table class="table table-bordered table-hover" id="list_table_task" cellspacing="0" width="100%">
                                <thead>
                                    <tr style="background: #DCDCDC;">
                                        @if (! $hrm_kpi_set_config->is_confirmed)
                                            <th style="width: 10%">
                                                <input id="select-all-task" type="checkbox" data-store-url="{{ route('tasks.store', ['all' => true]) }}" data-remove-url="{{ route('tasks.remove', ['all' => true]) }}" data-hrm_kpi_set_config_id="{{ $hrm_kpi_set_config->id }}" /> All
                                            </th>
                                        @endif

                                        <th style="width: 45%">Task Type</th>
                                        <th style="width: 45%">Task Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasks as $task)
                                        <tr>
                                            @if (! $hrm_kpi_set_config->is_confirmed)
                                                <td>
                                                    <input type="checkbox" class="task_id" value="{{ $task->id }}" {{ array_key_exists($task->id, $selectedTasks) ? 'checked' : null }} data-store-url="{{ route('tasks.store') }}" data-remove-url="{{ route('tasks.remove') }}" data-hrm_kpi_set_config_id="{{ $hrm_kpi_set_config->id }}">
                                                </td>
                                            @endif

                                            <td>{{ $task->task_type_name }}</td>
                                            <td>{{ $task->description }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <td colspan="3">Total {{ count($tasks) }} Task</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="mark_setup">
                    <div class="row">
                        @if (! $hrm_kpi_set_config->is_confirmed)
                            <div class="col-xs-12">
                                <a href="{{ route('kpi_mark.create') }}?hrm_kpi_set_config_id={{ encrypt($hrm_kpi_set_config->id) }}" class="btn btn-primary modalLink" data-title="Create Mark" footer-none style="margin-bottom: 15px;">
                                    Create
                                </a>
                            </div>
                        @endif

                        <div class="col-md-7">
                            <table id="mark_setup_table" class="table table-bordered table-hover" cellspacing="0" width="100%" style="margin-bottom: 0">
                                <thead>
                                    <tr>
                                        <th style="width: 40%">Mark Name</th>
                                        <th style="width: 30%">Point</th>
                                        @if (! $hrm_kpi_set_config->is_confirmed)
                                            <th style="width: 15%">Action</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="department_setup">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered table-hover" id="list_table_department" cellspacing="0" width="100%">
                                <thead>
                                    <tr  style="background: #DCDCDC;">
                                        @if (! $hrm_kpi_set_config->is_confirmed)
                                            @if ($hrm_kpi_set_config->tag_with_department == 1)
                                                <th style="width: 10%">
                                                    <input id="select-all-department" type="checkbox" data-store-url="{{ route('departments.store', ['all' => true]) }}" data-remove-url="{{ route('departments.remove', ['all' => true]) }}" data-hrm_kpi_set_config_id="{{ $hrm_kpi_set_config->id }}" /> All
                                                </th>
                                            @else
                                                <th style="width: 10%"></th>
                                            @endif
                                        @endif

                                        <th style="width: 45%">Department Name</th>

                                        @if ($hrm_kpi_set_config->tag_with_department != 1)
                                            @if (! $hrm_kpi_set_config->is_confirmed)
                                                <th style="width: 15%"></th>

                                                <th style="width: 15%"></th>
                                            @endif
                                            <th style="width: 15%"></th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($departments as $department)
                                        <tr class="selected-bg">
                                            @if (! $hrm_kpi_set_config->is_confirmed)
                                                @if ($hrm_kpi_set_config->tag_with_department == 1)
                                                    <td>
                                                        <input type="checkbox" class="department_id" value="{{ $department->id }}" {{ array_key_exists($department->id, $selectedDepartments) ? 'checked' : null }} data-store-url="{{ route('departments.store') }}" data-remove-url="{{ route('departments.remove') }}" data-hrm_kpi_set_config_id="{{ $hrm_kpi_set_config->id }}">
                                                    </td>
                                                @else
                                                    <td>
                                                        <input class="disabled-check" type="checkbox" {{ array_key_exists($department->id, $selectedDepartments) ? 'checked' : null }} disabled />
                                                    </td>
                                                @endif
                                            @endif

                                            <td>
                                                {{ $department->depertment_name }}
                                                @if ($hrm_kpi_set_config->tag_with_department != 1)
                                                    <small class="selectedText">{{ array_key_exists($department->id, $selectedDepartments) ? '(selected)' : null }}</small>
                                                @endif
                                            </td>

                                            @if ($hrm_kpi_set_config->tag_with_department != 1)
                                                @if (! $hrm_kpi_set_config->is_confirmed)
                                                    <td style="text-align:center;padding:0" class="departments_remove">
                                                        @if (array_key_exists($department->id, $selectedDepartments))
                                                            <a href="{{ route('departments.remove.not_na', ['hrm_kpi_set_config_id' => $hrm_kpi_set_config->id, 'hrm_depertment_id' => $department->id]) }}" class="department_remove" style="display:block;padding:8px;color:red">Remove Me</a>
                                                        @endif
                                                    </td>

                                                    <td style="text-align:center;padding:0">
                                                        <a
                                                            href="{{ route('departments.set', ['hrm_kpi_set_config_id' => $hrm_kpi_set_config->id, 'hrm_depertment_id' => $department->id]) }}"
                                                            class="department_set" style="display:block;padding:8px;color:#22c55e"
                                                            data-hrm_kpi_set_config_id="{{ $hrm_kpi_set_config->id }}"
                                                            data-hrm_depertment_id="{{ $department->id }}"
                                                            data-depertment_name="{{ $department->depertment_name }}"
                                                        >
                                                            Set Me
                                                        </a>
                                                    </td>
                                                @endif

                                                <td style="text-align:center;padding:0" class="departments_details">
                                                    @if (array_key_exists($department->id, $selectedDepartments))
                                                        <a href="{{ route('departments.detail', ['hrm_kpi_set_config_id' => $hrm_kpi_set_config->id, 'depertment_name' => $department->depertment_name, 'hrm_depertment_id' => $department->id]) }}" class="departments_detail" style="display:block;padding:8px;color:#0ea5e9">Config Details</a>
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <div id="renderTableData"></div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="final_submit">
                    <div class="row" id="reload-area">
                        <div class="col-md-6">
                            <h2 style="margin: 0">Task Setup</h2>

                            <table class="table table-bordered table-hover" id="list_table_task2" cellspacing="0" width="100%">
                                <thead>
                                    <tr style="background: #DCDCDC;">
                                        <th style="width: 45%">Task Type</th>
                                        <th style="width: 45%">Task Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($finalTasks as $finalTask)
                                        <tr>
                                            <td>{{ $finalTask->task_type_name }}</td>
                                            <td>{{ $finalTask->description }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h2 style="margin: 0">Mark Setup</h2>

                            <table class="table table-bordered table-hover" id="list_table_task2" cellspacing="0" width="100%">
                                <thead>
                                    <tr style="background: #DCDCDC;">
                                        <th>Mark Name</th>
                                        <th>Point</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($finalMarks as $finalMark)
                                        <tr>
                                            <td>{{ $finalMark->description }}</td>
                                            <td>{{ $finalMark->point }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="col-xs-12">
                            <h2>Department Setup</h2>
                        </div>

                        <div class="col-md-6">
                            <table class="table table-bordered table-hover" id="list_table_department2" cellspacing="0" width="100%">
                                <thead>
                                    <tr  style="background: #DCDCDC;">
                                        <th style="width: 45%">Department Name</th>

                                        @if ($hrm_kpi_set_config->tag_with_department != 1)
                                            <th style="width: 15%"></th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($departments as $department)
                                        @if (array_key_exists($department->id, $selectedDepartments))
                                            <tr class="selected-bg">
                                                <td>{{ $department->depertment_name }}</td>

                                                @if ($hrm_kpi_set_config->tag_with_department != 1)
                                                    <td style="text-align:center;padding:0" class="departments_details">
                                                        <a href="{{ route('departments.detail', ['hrm_kpi_set_config_id' => $hrm_kpi_set_config->id, 'depertment_name' => $department->depertment_name, 'hrm_depertment_id' => $department->id]) }}" class="departments_detail2" style="display:block;padding:8px;color:#0ea5e9">Config Details</a>
                                                    </td>
                                                @endif
                                            </tr>
                                        @else
                                            @continue
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>

                            @if (! $hrm_kpi_set_config->is_confirmed)
                                <button type="button" class="btn btn-success pull-right" style="margin-top: 20px;" id="finalSubmit">Final Submit</button>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <div id="renderTableData2"></div>
                        </div>

                        @if (! $hrm_kpi_set_config->is_confirmed)
                            <div class="col-xs-12 text-center">
                                <h3 class="text-danger">Nothing will be changed after the final submit.</h3>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js') }}"></script>

<script>

    $('.nav-tabs a').on('shown.bs.tab', function(event){
        var target = $(event.target);

        if ($(target).attr('href') === '#final_submit') {
            $("#final_submit").load(location.href + " #reload-area");
        }
    });

    // Task setup
    $(document).on('click', '#select-all-task', function() {
        let storeUrl = $(this).data('store-url');
        let removeUrl = $(this).data('remove-url');

        let hrm_kpi_set_config_id = $(this).data('hrm_kpi_set_config_id');
        let hrm_kpi_task_id = [];

        $.each($('.task_id'), function(_, task) {
            hrm_kpi_task_id.push($(task).val())
        })

        if ($(this).is(':checked')) {
            $.get(storeUrl, {
                hrm_kpi_set_config_id,
                hrm_kpi_task_id
            }).then(data => {
                $("#task-count").load(location.href + " #task-count-reload");

                bootoast({
                    type:'success',
                    message: data.message,
                    timeout: 2000,
                    timeoutProgress: false
                });
            })
        } else {
            $.get(removeUrl, {
                hrm_kpi_set_config_id
            }).then(data => {
                $("#task-count").load(location.href + " #task-count-reload");

                bootoast({
                    type:'success',
                    message: data.message,
                    timeout: 2000,
                    timeoutProgress: false
                });
            })
        }
    })

    $(document).on('click', '.task_id', function() {
        let storeUrl = $(this).data('store-url');
        let removeUrl = $(this).data('remove-url');

        let hrm_kpi_set_config_id = $(this).data('hrm_kpi_set_config_id');
        let hrm_kpi_task_id = $(this).val();

        if ($(this).is(':checked')) {
            $.get(storeUrl, {
                hrm_kpi_set_config_id,
                hrm_kpi_task_id
            }).then(data => {
                $("#task-count").load(location.href + " #task-count-reload");

                bootoast({
                    type:'success',
                    message: data.message,
                    timeout: 2000,
                    timeoutProgress: false
                });
            })
        } else {
            $.get(removeUrl, {
                hrm_kpi_set_config_id,
                hrm_kpi_task_id
            }).then(data => {
                $("#task-count").load(location.href + " #task-count-reload");
                
                bootoast({
                    type:'success',
                    message: data.message,
                    timeout: 2000,
                    timeoutProgress: false
                });
            })
        }
    })
    // !Task setup

    // Department setup
    $(document).on('click', '#select-all-department', function() {
        let storeUrl = $(this).data('store-url');
        let removeUrl = $(this).data('remove-url');

        let hrm_kpi_set_config_id = $(this).data('hrm_kpi_set_config_id');
        let hrm_depertment_id = [];

        $.each($('.department_id'), function(_, department) {
            hrm_depertment_id.push($(department).val())
        })

        if ($(this).is(':checked')) {
            $.get(storeUrl, {
                hrm_kpi_set_config_id,
                hrm_depertment_id
            }).then(data => {
                bootoast({
                    type:'success',
                    message: data.message,
                    timeout: 2000,
                    timeoutProgress: false
                });
            })
        } else {
            $.get(removeUrl, {
                hrm_kpi_set_config_id,
                hrm_depertment_id
            }).then(data => {
                bootoast({
                    type:'success',
                    message: data.message,
                    timeout: 2000,
                    timeoutProgress: false
                });
            })
        }
    })

    $(document).on('click', '.department_id', function() {
        let storeUrl = $(this).data('store-url');
        let removeUrl = $(this).data('remove-url');

        let hrm_kpi_set_config_id = $(this).data('hrm_kpi_set_config_id');
        let hrm_depertment_id = $(this).val();

        if ($(this).is(':checked')) {
            $.get(storeUrl, {
                hrm_kpi_set_config_id,
                hrm_depertment_id
            }).then(data => {
                bootoast({
                    type:'success',
                    message: data.message,
                    timeout: 2000,
                    timeoutProgress: false
                });
            })
        } else {
            $.get(removeUrl, {
                hrm_kpi_set_config_id,
                hrm_depertment_id
            }).then(data => {
                bootoast({
                    type:'success',
                    message: data.message,
                    timeout: 2000,
                    timeoutProgress: false
                });
            })
        }
    })

    $(document).on('click', '.department_set', function(e) {
        e.preventDefault()

        let hrm_kpi_set_config_id = $(this).data('hrm_kpi_set_config_id'),
            hrm_depertment_id = $(this).data('hrm_depertment_id'),
            depertment_name = $(this).data('depertment_name');

        let detailUrl = `{{ route('departments.detail') }}?hrm_kpi_set_config_id=${hrm_kpi_set_config_id}&depertment_name=${depertment_name}&hrm_depertment_id=${hrm_depertment_id}`;
        let removeUrl = `{{ route('departments.remove.not_na') }}?hrm_kpi_set_config_id=${hrm_kpi_set_config_id}&hrm_depertment_id=${hrm_depertment_id}`;

        $.get($(this).attr('href'))
            .then(data => {
                $(this).parents('tr').find('.disabled-check').prop('checked', true);
                $(this).parents('tr').find('.selectedText').text('(selected)');

                $(this).parents('tr').find('.departments_remove').html(`
                    <a href="${removeUrl}" class="department_remove" style="display:block;padding:8px;color:red">Remove Me</a>
                `);

                $(this).parents('tr').find('.departments_details').html(`
                    <a href="${detailUrl}" class="departments_detail" style="display:block;padding:8px;color:#0ea5e9">Show Details</a>
                `);

                bootoast({
                    type:'success',
                    message: data.message,
                    timeout: 2000,
                    timeoutProgress: false
                });
            })
    })

    $(document).on('click', '.department_remove', function(e) {
        e.preventDefault()

        $.get($(this).attr('href'))
            .then(data => {
                $(this).parents('tr').find('.disabled-check').prop('checked', false);
                $(this).parents('tr').find('.selectedText').text('');

                $(this).parents('tr').find('.departments_details').html('');

                $(this).parents('tr').find('.departments_remove').html('');

                bootoast({
                    type:'success',
                    message: data.message,
                    timeout: 2000,
                    timeoutProgress: false
                });
            })
    })

    $(document).on('click', '.departments_detail', function(e) {
        e.preventDefault()

        $('tr.selected-bg').removeAttr('style')

        $(this).parents('tr.selected-bg').css("background-color", "#bef264")

        $.get($(this).attr('href'))
            .then(data => {
                $('#renderTableData').html(`<table class="table table-bordered table-hover" id="list_table_category" cellspacing="0" width="100%">
                    <thead>
                        <tr style="background: #DCDCDC;">
                            ${data.config_is_confirmed == 0 ? `<th style="width: 10%"></th>` : ''}

                            <th style="width: 80%"><span style="font-weight:normal">Set config for</span> <span style="color:red;font-size:18px;">${data.department_name}</span> <span style="font-weight:normal">${data.display_name}</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.tableData.map(table => {
                            return `<tr>
                                ${data.config_is_confirmed == 0 ?
                                    `<td>
                                        <input type="checkbox" class="table_name_id" value="${table.id}" ${array_key_exists(table.id, data.selectedDeptWith) ? 'checked' : ''} data-store-url="{{ route('categories.store') }}" data-remove-url="{{ route('categories.remove') }}" data-hrm_kpi_set_config_department_id="${data.hrm_kpi_set_config_department_id}">
                                    </td>`
                                : ''}

                                <td>${table.table_field_name}</td>
                            </tr>`
                        }).join('')}
                    </tbody>
                </table>`)
        })
    })

    $(document).on('click', '.departments_detail2', function(e) {
        e.preventDefault()

        $('tr.selected-bg').removeAttr('style')

        $(this).parents('tr.selected-bg').css("background-color", "#bef264")

        $.get($(this).attr('href'))
            .then(data => {
                $('#renderTableData2').html(`<table class="table table-bordered table-hover" id="list_table_category" cellspacing="0" width="100%">
                    <thead>
                        <tr style="background: #DCDCDC;">
                            <th style="width: 80%"><span style="font-weight:normal">Set config for</span> <span style="color:red;font-size:18px;">${data.department_name}</span> <span style="font-weight:normal">${data.display_name}</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.tableData.map(table => {
                            if (array_key_exists(table.id, data.selectedDeptWith)) {
                                return `<tr>
                                    <td>${table.table_field_name}</td>
                                </tr>`
                            }

                            return '';
                        }).join('')}
                    </tbody>
                </table>`)
        })
    })

    // category...
    // $(document).on('click', '#select-all-task', function() {
    //     let storeUrl = $(this).data('store-url');
    //     let removeUrl = $(this).data('remove-url');

    //     let hrm_kpi_set_config_id = $(this).data('hrm_kpi_set_config_id');
    //     let hrm_kpi_task_id = [];

    //     $.each($('.task_id'), function(_, task) {
    //         hrm_kpi_task_id.push($(task).val())
    //     })

    //     if ($(this).is(':checked')) {
    //         $.get(storeUrl, {
    //             hrm_kpi_set_config_id,
    //             hrm_kpi_task_id
    //         }).then(data => {
    //             $(".frmMsg").html(`<div class="alert alert-success alert-dismissible">
    //                 <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Success!</strong> ${data.message}</div>`);
    //         })
    //     } else {
    //         $.get(removeUrl, {
    //             hrm_kpi_set_config_id
    //         }).then(data => {
    //             $(".frmMsg").html(`<div class="alert alert-success alert-dismissible">
    //                 <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Success!</strong> ${data.message}</div>`);
    //         })
    //     }

    //     setTimeout(function() {
    //         $(".frmMsg").html("");
    //     }, 1000);
    // })

    $(document).on('click', '.table_name_id', function() {
        let storeUrl = $(this).data('store-url');
        let removeUrl = $(this).data('remove-url');

        let hrm_kpi_set_config_department_id = $(this).data('hrm_kpi_set_config_department_id');
        let ref_id = $(this).val();

        if ($(this).is(':checked')) {
            $.get(storeUrl, {
                hrm_kpi_set_config_department_id,
                ref_id
            }).then(data => {
                bootoast({
                    type:'success',
                    message: data.message,
                    timeout: 2000,
                    timeoutProgress: false
                });
            })
        } else {
            $.get(removeUrl, {
                hrm_kpi_set_config_department_id,
                ref_id
            }).then(data => {
                bootoast({
                    type:'success',
                    message: data.message,
                    timeout: 2000,
                    timeoutProgress: false
                });
            })
        }
    })
    // !Department setup


    function markSetupTable() {
        table = $('#mark_setup_table').DataTable({
            destroy:    true,
            paging:     true,
            searching:  true,
            ordering:   true,
            bInfo:      true,
            ajax: {
                url: "{{ route('kpi_mark.index') }}",
                type: "GET",
                dataType: 'json',
                data: {
                    hrm_kpi_set_config_id: @json(encrypt($hrm_kpi_set_config->id))
                }
            },

            "columns": [
                { "data": "description" },
                { "data": "point" },
                @if (! $hrm_kpi_set_config->is_confirmed)
                    { "data": "Link", name: 'action', orderable: false, searchable: false },
                @endif
            ],

            "order": [[0,'asc']]
        });
    }

    markSetupTable()

    confirmationWithAjaxReload({
        selector: '.deleteMark',
        refreshTable: markSetupTable,
    })

    checkAll('#select-all-task', '#list_table_task');

    checkAll('#select-all-department', '#list_table_department');

    function checkAll(selector, tableId) {
        $(selector).on('click', function() {
            $('input[type="checkbox"]').prop('checked', this.checked);
        });


        $(`${tableId} tbody`).on('change', 'input[type="checkbox"]', function() {
            if(!this.checked){
                var el = $(selector).get(0);
                if(el && el.checked && ('indeterminate' in el)) {
                    el.indeterminate = true;
                }
            }
        });
    }

    function array_key_exists(key, arr) {
        return Object.keys(arr).some(k => k == key)
    }

    $(document).on('click', '#finalSubmit', function(e) {
        e.preventDefault();

        if (confirm('Are you sure.!')) {
            $.post("{{ route('config-final-submit') }}", {
                hrm_kpi_set_config_id: @json($hrm_kpi_set_config->id)
            }).then(data => {
                $(".frmMsg").html(`<div class="alert alert-success alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Success!</strong> ${data.message}</div>`);

                setTimeout(() => {
                    location.reload()
                }, 1500);
            })
        }
    })
</script>
@endsection
