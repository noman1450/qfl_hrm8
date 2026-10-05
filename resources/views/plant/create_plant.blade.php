<!-- create_designation -->
@extends('layouts.main')
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@endsection
@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Create Plant</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>
	<form class="form-horizontal" method="POST" action="{{url('plant')}}">
		{{ csrf_field() }}
		<div class="box-body">
			<div class="row">
				<div class="form-group has-feedback {{ $errors->has('plant_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<div class="col-lg-6">
						<label>Plant Name</label>
						<input type="text" class="form-control" name="plant_name" placeholder="Plant Name.." value="{{ old('plant_name') }}" required autofocus >
						@if ($errors->has('plant_name'))
						<span class="help-block">
							<strong>{{ $errors->first('plant_name') }}</strong>
						</span>
						@endif
					</div>
				</div>
				<div class="form-group has-feedback {{ $errors->has('holiday_description') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<div class="col-lg-6">
						<label>Location Name</label>
						<select class="form-control" id= "location" name="location">
						</select>
						
					</div>
				</div>
			</div>
		</div>
		<div class="box-footer" style="border-top: 0px solid #f4f4f4;">
			<div class="row">
				<div class="form-group col-lg-12 col-md-12 col-xs-12">
					<div class="col-lg-6">
						<button type="submit" class="btn btn-success block btn-flat btn pull-right" >Submit</button>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
@endsection


@section('script')
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

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
