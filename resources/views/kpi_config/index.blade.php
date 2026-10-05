@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@endsection

@section('content')
<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">KPI Set Config</h3>

        <div class="row">
            <div class="col-md-3">
                <a href="{{ route('kpi_config.create') }}" class="btn btn-primary modalLink" data-title="Create Config" footer-none>
                    Create
                </a>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label>Select Year</label>
                    <select class="form-control" id="hrm_kpi_assesment_date_id" style="width: 100%;" required>
                    </select>
                </div>
            </div>
        </div>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>

    <div class="box-body">
        <div class="row">
            <div class="col-lg-8">
                <table id="kpiConfigDatatable" class="table table-bordered table-hover" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th style="width: 30%">Title</th>
                            <th style="width: 30%">Financial Year</th>
                            <th style="width: 20%">Evalution Department with</th>
                            <th style="width: 20%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

<script>
    $('#hrm_kpi_assesment_date_id').select2({
        placeholder: 'Select Year/Month',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: '{{URL::to('/')}}/kpi_assesment_date_list_data',
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
    }).on('select2:select', function() {
        loadData()
    }).on('select2:unselect', function() {
        loadData()
    });

    loadData()

    function loadData() {
        table = $('#kpiConfigDatatable').DataTable({
            destroy:    true,
            paging:     true,
            searching:  true,
            ordering:   true,
            bInfo:      true,
            ajax: {
                url: "{{ route('kpi_config.index') }}",
                type: "GET",
                dataType: 'json',
                data: {
                    hrm_kpi_assesment_date_id: $('#hrm_kpi_assesment_date_id option:selected').val()
                },
            },

            "columns": [
                { "data": "title" },
                { "data": "financial_year" },
                { "data": "display_name" },
                { "data": "Link", name: 'action', orderable: false, searchable: false },
            ],

            "order": [[0,'asc']]
        });
    }
</script>
@endsection
