@extends('layouts.main')

@section('styles')
    <link rel="stylesheet" href="{{ asset('plugins/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datetimepicker/style.css') }}">
@endsection

@section('content')
<div class="box box-default">
    <div class="box-body">
        <h4>Assign Task ({{ $task->task_name }})</h4>

        <div class="row">
            @include('task_management._form', ['task' => $task])
        </div>
    </div>
</div>
@endsection

@section('script')
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.min.js"></script>
    <script src="{{ asset('plugins/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('plugins/datetimepicker/script.js') }}"></script>

<script>

$(document).ready(function($) {
    $('#assign_date, #due_date').datetimepicker({
        "allowInputToggle": true,
        "showClose": true,
        "showClear": true,
        "showTodayButton": true,
        "ignoreReadonly": true,
        "daysOfWeekDisabled": [5, 6],
        "format": "MM/DD/YYYY hh:mm:ss A",
    });

    $('#hrm_employee_id').select2({
    	placeholder: 'Enter an Employee Name',
    	allowClear: true,
    		ajax: {
		        dataType: 'json',
		        url: "{{URL::to('/')}}/join_employee_list",
		        delay: 250,
    			data: function(params) {
                    return {
                        term: params.term
                    }
    			},

		        processResults: function (data, params) {
		            params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
		        },

		        cache: true
    		}
        });
    });
</script>
@endsection
