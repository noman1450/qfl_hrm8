@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@endsection

@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Create Filter</h3>

		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>

	<form redirectUrl="{{ url('custom_filter') }}" action="{{ url('custom_filter') }}" class="dynamicFormSubmit" method="post">
		@csrf

		<div class="box-body">
			<div class="row">
                <div class="col-md-4">
                    <div class="form-group">
						<label>Filter Name</label>
						<input type="text" class="form-control required" name="filter_name" id="filter_name" placeholder="Filter Name" value="{{ old('filter_name') }}">
					</div>
				</div>

                <div class="col-md-4">
                    <div class="form-group">
						<label>Table Name</label>
                        <select name="hrm_custom_filter_id" id="hrm_custom_filter_id" class="form-control required"></select>
					</div>
				</div>
			</div>

            <div class="row">
                <div class="col-xs-12">
                    <div id="show-table-data"></div>
                </div>
            </div>
		</div>

		<div class="box-footer" style="border-top: 0px solid #f4f4f4;">
            <button type="submit" class="btn btn-success block btn-flat btn pull-right">Submit</button>
		</div>
	</form>
</div>
@endsection

@section('script')
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

<script>
$('#hrm_custom_filter_id').select2({
    placeholder: 'Enter Education Name',
    allowClear: true,
    ajax: {
        dataType: "json",
        url: "{{ url('/table_name_list') }}",
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
}).on('select2:select', function ({ params }) {
    const tableName = params.data.table_name

    const url = "{{ url('custom_filter/get-table-data') }}?" + $.param({ tableName });

    $.get(url).then(response => {
        $('#show-table-data').html(response)
    })
}).on('select2:unselect', function () {
    $('#show-table-data').html("")
});
</script>
@endsection
