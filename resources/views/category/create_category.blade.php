<!-- create_category -->
@extends('layouts.main')
@section('styles')
@endsection
@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Create Category</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>
	<form class="form-horizontal" method="POST" action="{{url('category')}}">
		{{ csrf_field() }}
		<div class="box-body">
			<div class="row">
				<div class="form-group has-feedback {{ $errors->has('category') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<div class="col-lg-6">
						<label>Category</label>
						<input type="text" class="form-control" name="category" placeholder="Category.." value="{{ old('category') }}" required autofocus >
						@if ($errors->has('category'))
						<span class="help-block">
							<strong>{{ $errors->first('category') }}</strong>
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

				<div class="form-group has-feedback {{ $errors->has('ot_rate') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<div class="col-lg-6">
						<label>OT Rate</label>
						<input type="number" step="0.01" class="form-control" name="ot_rate" placeholder="OT Rate" value="{{ $category->category_description ?? old('ot_rate') }}" >
						@if ($errors->has('ot_rate'))
						<span class="help-block">
							<strong>{{ $errors->first('ot_rate') }}</strong>
						</span>
						@endif
					</div>
				</div>

				<div class="form-group has-feedback {{ $errors->has('priority') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<div class="col-lg-6">
						<label>Priority</label>
						<input type="number" step="0.01" class="form-control" name="priority" placeholder="Priority" value="{{ $category->priority ?? old('priority') }}" >
						@if ($errors->has('priority'))
						<span class="help-block">
							<strong>{{ $errors->first('priority') }}</strong>
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
						<button type="submit" class="btn btn-success block btn-flat btn pull-right" >Submit</button>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
@endsection
@section('script')
@endsection
