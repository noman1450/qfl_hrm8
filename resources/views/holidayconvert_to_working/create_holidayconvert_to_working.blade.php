<!-- create_employee -->
<!-- create_marital_status -->
@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}"></script>
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">


<style>
.kv-avatar .krajee-default.file-preview-frame,.kv-avatar .krajee-default.file-preview-frame:hover {
    margin: 0;
    padding: 0;
    border: none;
    box-shadow: none;
    text-align: center;
}
.kv-avatar {
    display: inline-block;
}
.kv-avatar .file-input {
    display: table-cell;
    width: 213px;
}
.kv-reqd {
    color: red;
    font-family: monospace;
    font-weight: normal;
}
.btn-secondary {
	margin-top: 5px;
}
.btn-file{
	margin-top: 5px;	
}
</style>
@endsection


@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Create Date Convert</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


    {!! Form::open(array('route'=>'holiday_convert.store', 'onkeypress'=> "return event.keyCode != 13;", 'files'=>true, 'id'=>'frm_employeeleave')) !!}
    {{ csrf_field() }}

	<div class="box-body">
		<div class="row">


			<div class="col-lg-6 col-md-6 col-xs-12 personal-info">


		        <div class="form-group has-feedback {{ $errors->has('location_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Location</label>
		            <div class="col-lg-9">
		  	          
						<select style="width: 100%;" class="form-control select2" id="location_name" name="location_name" required>
						</select>

			            @if ($errors->has('location_name'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('location_name') }}</strong>
			                </span>
			            @endif 	                
		            </div>
		        </div>



               <div class="form-group has-feedback {{ $errors->has('date_from') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Action Date</label>
		            <div class="col-lg-9">
		            <div class="input-group date">
		                <div class="input-group-addon">
		                  <i class="fa fa-calendar"></i>
		                </div>		            
		                <input type="text" class="form-control pull-right" id="action_date" name="action_date" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('action_date') }}" required readonly>
			            @if ($errors->has('action_date'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('action_date') }}</strong>
			                </span>
			            @endif 	                
		            </div>
		            </div>
		        </div>


               <div class="form-group has-feedback {{ $errors->has('date_from') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Purpose</label>
		            <div class="col-lg-9">
		            <input type="text" class="form-control" name="purpose" placeholder="Purpose" required>
		            </div>
		        </div>




		        <div class="form-group has-feedback {{ $errors->has('apply_type') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Apply Type</label>
		            <div class="col-lg-9">
		  	          
						<select style="width: 100%;" class="form-control select2" id="apply_type" name="apply_type" required>
							<option value="1">Make Holiday to Working</option>
							<option value="2">Make Working Day to Holiday</option>
						</select>

			            @if ($errors->has('apply_type'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('apply_type') }}</strong>
			                </span>
			            @endif 	                
		            </div>
		        </div>
		       


			<div class="form-group">
				<label class="col-md-3 control-label"></label>
				<div class="col-md-9">
					<input type="submit" class="btn btn-success block btn-flat pull-right"  value="Submit">
					<span></span>
				</div>
			</div>

		</div>
	</div>


	{!! Form::close() !!}
</div>
@endsection


@section('script')
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>


<script src="{{asset('js/fileinput.js')}}"></script>
<script>
$(document).ready(function($) {


    $('#action_date').datepicker({
      autoclose: true
    });

    $('#location_name').select2({
      placeholder: 'Enter a Location Name',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/location_list_data',
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
          };
        },
        cache: true
      }
    });





});
</script>
@endsection
