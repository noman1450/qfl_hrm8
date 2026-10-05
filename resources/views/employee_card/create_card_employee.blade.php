<!-- create_employee -->
<!-- create_marital_status -->
@extends('layouts.main')

@section('styles')
    <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@endsection


@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Create Attendance Punch/Card Code</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


    <form class="form-horizontal" method="POST" action="{{url('employeecard')}}">
        {{ csrf_field() }}


	<div class="box-body">
		<div class="row">


		<div class="col-lg-8 col-md-8 col-xs-12 personal-info">


        <div class="form-group has-feedback {{ $errors->has('employee_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
            <label class="col-lg-3 control-label">Employee Name</label>
            <div class="col-lg-9">

                <select style="width: 100%;" class="form-control select2" id="employee_name" name="employee_name" required>
                </select>


                @if ($errors->has('employee_name'))
                    <span class="help-block">
                        <strong>{{ $errors->first('employee_name') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group has-feedback {{ $errors->has('card_code') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
            <label class="col-lg-3 control-label">Card Code</label>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="card_code" placeholder="card code.." value="{{ old('card_code') }}" required autofocus >
                @if ($errors->has('card_code'))
                    <span class="help-block">
                        <strong>{{ $errors->first('card_code') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group has-feedback {{ $errors->has('old_code') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
            <label class="col-lg-3 control-label">Old Code</label>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="old_code" placeholder="Old Code.." value="{{ old('old_code') }}"  autofocus >

            </div>
        </div>


        {{-- <div class="form-group has-feedback {{ $errors->has('device_id') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
            <label class="col-lg-3 control-label">Device Id</label>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="device_id" placeholder="Device Id.." value="{{ old('device_id') }}"  autofocus >
            </div>
        </div> --}}

		<div class="form-group">
				<label class="col-md-3 control-label"></label>
				<div class="col-md-8">
					<input type="submit" class="btn btn-success block btn-flat" value="Submit">
					<span></span>
					<!-- <input type="reset" class="btn block btn-flat btn-default" value="Cancel"> -->
				</div>
			</div>

		</div>
	</div>


		</form>
</div>
@endsection


@section('script')
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('js/fileinput.js')}}"></script>
<script>
$(document).ready(function($) {



    $('#employee_name').select2({
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
