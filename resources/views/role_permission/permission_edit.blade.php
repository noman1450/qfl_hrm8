<!-- create_designation -->
@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

@endsection


@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Edit Permission </h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>

	<div class="box-body">
		<div class="row">
            <div class="row">
                <div class="col-md-6">
                    <form action="{{ route('permission.update', $permission->id) }}" method="post" onkeypress="return event.keyCode != 13;">
                        @method('patch')
                        @csrf
                        <div class="box-body">
                            <div class="form-group">
                                <label>Permission Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $permission->name }}" placeholder="Permisssion Name">
                            </div>

                            <div class="form-group">
                                <label>Under</label>
                                <select class="form-control" name="permission_id" id="permission_id">
                                    <option value="{{ $permission->parent_id }}">{{ $permission->under }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="box-footer">
                            <input type="submit" class="btn btn-success btn-flat pull-right submitBtn" value="Submit" style="margin-right: 10px;">
                        </div>

                    </form>
                </div>
            </div>
		</div>
	</div>
</div>
@endsection


@section('script')
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

<script>

$("#permission_id").select2({
    placeholder: "Search Permission",
    width: '100%',
    allowClear: true,
    ajax: {
        dataType: 'json',
        url: "{{ url('permissions_drop_list') }}",
        delay: 100,
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
    },
});

</script>
@endsection
