<!-- create_shift -->
@extends('layouts.main')

@section('styles')
{{-- <link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}"></script> --}}
{{-- <link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}"> --}}
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

@endsection


@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Create Shift</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


    {!! Form::open(array('route'=>'shift.store', 'onkeypress'=> "return event.keyCode != 13;", 'files'=>true, 'id'=>'frm_shift')) !!}
    {{ csrf_field() }}

	<div class="box-body">
		<div class="row">


	      <!--   <div class="form-group has-feedback {{ $errors->has('holiday_description') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
	            <div class="col-lg-6">
    			<label>Location Name</label>
					<select class="form-control" id= "location" name="location">
					</select>

	            </div>
	        </div> -->

	        <div class="form-group has-feedback {{ $errors->has('shift') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
	            <div class="col-lg-6">
	              	<label>Shift Name</label>
	                <input type="text" class="form-control" name="shift" placeholder="Shift Name.." value="{{ old('shift') }}" required autofocus >
		            @if ($errors->has('shift'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('shift') }}</strong>
		                </span>
		            @endif
	            </div>
	        </div>

	        <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
	            <div class="col-lg-6">
	              	<label>Description</label>
	                <input type="text" class="form-control" name="description" placeholder="Description.." value="{{ old('description') }}" required>
		            @if ($errors->has('description'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('description') }}</strong>
		                </span>
		            @endif
	            </div>
	        </div>


	        <div class="form-group  col-lg-12 col-md-12 col-xs-12">
	            <div class="col-lg-3 has-feedback {{ $errors->has('start_time') ? ' has-error' : '' }}  bootstrap-timepicker">
	              	<label>Start Time</label>
	                  <div class="input-group">
	                    <input type="text" class="form-control time" id="start_time" name="start_time" value="{{ old('start_time') }}" readonly>
	                    <div class="input-group-addon">
	                      <i class="fa fa-clock-o"></i>
	                    </div>
	                  </div>
		            @if ($errors->has('start_time'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('start_time') }}</strong>
		                </span>
		            @endif
	            </div>

	            <div class="col-lg-3 has-feedback {{ $errors->has('end_time') ? ' has-error' : '' }}  bootstrap-timepicker">
	              	<label>End Time</label>
	                  <div class="input-group">
	                    <input type="text" class="form-control time" id="end_time" name="end_time" value="{{ old('end_time') }}" readonly>
	                    <div class="input-group-addon">
	                      <i class="fa fa-clock-o"></i>
	                    </div>
	                  </div>
		            @if ($errors->has('end_time'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('end_time') }}</strong>
		                </span>
		            @endif
	            </div>

	        </div>

	        <div class="form-group  has-feedback {{ $errors->has('working_hours') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
	            <div class="col-lg-3 bootstrap-timepicker">
	              	<label>Working Hours</label>
	                  <div class="input-group">
	                    <input type="text" class="form-control" id="working_hours" name="working_hours" readonly>
	                    <div class="input-group-addon">
	                      <i class="fa fa-clock-o"></i>
	                    </div>
	                  </div>
		            @if ($errors->has('working_hours'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('working_hours') }}</strong>
		                </span>
		            @endif
	            </div>

	        </div>

		</div>
	</div>

	<div class="box-footer" style="border-top: 0px solid #f4f4f4;">
		<div class="row">
	        <div class="form-group col-lg-12 col-md-12 col-xs-12">
	            <div class="col-lg-6">
	                <button type="submit" class="btn btn-success block btn-flat btn pull-center" >Submit</button>
	            </div>
	        </div>
        </div>
	</div>

	{!! Form::close() !!}
</div>
@endsection


@section('script')

{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script> --}}
{{-- <script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script> --}}
{{-- <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script> --}}
<script src="{{asset('plugins/timepicker/bootstrap-timepicker.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

<script>

$( document ).ready(function() {

    $("#start_time").timepicker({
      showMeridian:false,
      showSeconds:true,
      showInputs: false
    });

    $("#end_time").timepicker({
      showMeridian:false,
      showSeconds:true,
      showInputs: false
    });


	$(".time").change(function(){
    	$("#working_hours").val(timeDiff($("#start_time").val(),$("#end_time").val()))
	});

	// Convert h:m:s to seconds
	function hmsToSecs(s) {
	  var b = s.split(':');
	  return b[0]*3.6e3 + b[1]*60 + +b[2];
	}

	// Convert seconds to hh:mm:ss
	function secsToHMS(n) {
	  function z(n){return (n<10? '0':'') + n;}
	  var sign = n < 0? '-' : '';
	  n = Math.abs(n);
	  return sign + z(n/3.6e3|0) + ':' + z(n%3.6e3/60|0) + ':' + z(n%60);
	}

	// Calculate time difference between two times
	// start and finish in hh:mm:ss
	// If finish is less than start, assume it's the following day
	function timeDiff(start, finish) {
	  var s = hmsToSecs(start);
	  var f = hmsToSecs(finish);
	  // If finish is less than start, assume is next day
	  // so add 24hr worth of seconds
	  if (f < s) f += 8.64e4;

	  return secsToHMS(f - s);
	}
	$("#working_hours").val(timeDiff($("#start_time").val(),$("#end_time").val()))


});
</script>



<script>

	 $('#location').select2({
      placeholder: 'Enter a location',
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
</script>
@endsection
