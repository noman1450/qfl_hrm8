<!-- create_designation -->
@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@endsection


@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Update Holdup Application</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>

    <form class="form-horizontal" method="POST" action="{{ route('holdup_application.update',  $edit_data->id) }}">
        @csrf
        @method('PUT')
        <div class="box-body">
            <div class="row">

                <div class="col-lg-8 col-md-8 col-xs-12 personal-info">

                    <div class="form-group has-feedback {{ $errors->has('date') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                        <label class="col-lg-3 control-label" >Select Month </label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" value="{{ $edit_data->date }}" name="date" id="date" readonly >
                            @if ($errors->has('date'))
                            <span class="help-block">
                                <strong>{{ $errors->first('date') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group has-feedback {{ $errors->has('employee_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                        <label class="col-lg-3 control-label">Employee Name</label>
                        <div class="col-lg-9">
                            <select style="width: 100%;" class="form-control select2"  disabled autofocus >
                                <option value="{{ $edit_data->employee_id }}">{{ $edit_data->employee_name }}</option>
                            </select>
                            @if ($errors->has('employee_name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('employee_name') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group has-feedback {{ $errors->has('holdup_types') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                        <label class="col-lg-3 control-label">Salary Holdup Types</label>
                        <div class="col-lg-9">
                            <select style="width: 100%;" class="form-control select2" id="holdup_types" name="holdup_types"  autofocus >
                                <option value="{{ $edit_data->holdup_id }}">{{ $edit_data->holdup_types_name }}</option>
                            </select>
                            @if ($errors->has('holdup_types'))
                            <span class="help-block">
                                <strong>{{ $errors->first('holdup_types') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group has-feedback {{ $errors->has('note') ? ' has-error' : '' }}  col-lg-12 col-md-12 col-xs-12">
                        <label class="col-lg-3 control-label" >Reason </label>
                        <div class="col-lg-9">
                            <textarea class="form-control" name="note" id="note" cols="30" rows="5" placeholder="Write Reason for Holdup Application">{{ $edit_data->note }}</textarea>

                            @if ($errors->has('note'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('note') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>


                    <div class="form-group  col-lg-12 col-md-12 col-xs-12">
                        <div class="col-lg-12">
                            <input type="submit" class="btn btn-success block btn-flat pull-right" value="Update">
                        </div>
                    </div>

                </div>
            </div>


        </div>

    </form>
</div>
@endsection

@section('script')

<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script>
    $(document).ready(function () {
        $('#date').datepicker({
            autoclose: true,
            minViewMode: 1,
            format: 'MM-yyyy'
        });

        $('#employee_name').select2({
            placeholder: 'Enter an Employee Name',
            allowClear: true,
            ajax: {
                dataType: 'json',
                url: "{{URL::to('/')}}/join_employee_list",
                delay: 250,
                data: function (params) {
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

        $('#holdup_types').select2({
            placeholder: 'Enter an Salary Holdup Type',
            allowClear: true,
            ajax: {
                dataType: 'json',
                url: "{{URL::to('/')}}/salary_holdup_types_list",
                delay: 250,
                data: function (params) {
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
