<link rel="stylesheet" href="{{ asset('plugins/select2/select2.min.css') }}">

<form class="dynamicFormSubmit" action="{{ is_null($kpiConfig) ? route('kpi_config.store') : route('kpi_config.update', encrypt($kpiConfig->id)) }}" data-table-name="#kpiConfigDatatable" method="post">

    @if (! is_null($kpiConfig)) @method('PATCH') @endif


    <div class="form-group">
        <label for="title">Title</label>
        <input type="text" class="form-control" name="title" id="title" placeholder="Title" value="{{ old('title', $kpiConfig->title ?? null) }}">
    </div>

    <div class="form-group">
        <div id="hrm_kpi_assesment_date_id">
            <label for="hrm_kpi_assesment_date">Financial Year</label>
            <select class="form-control" name="hrm_kpi_assesment_date_id" id="hrm_kpi_assesment_date">
                @if (!is_null($kpiConfig))
                    <option value="{{ $kpiConfig->hrm_kpi_assesment_date_id }}">{{ $kpiConfig->financial_year }}</option>
                @endif
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="tag_with_department">Evalution Department with</label>
        <select class="form-control" id="tag_with_department" name="tag_with_department" style="width: 100%;">
            @foreach ($dynamicTables as $table)
                <option value="{{ $table->id }}" {{ !is_null($kpiConfig) ? ($kpiConfig->tag_with_department == $table->id ? 'selected' : null) : null }}>{{ $table->display_name }}</option>
            @endforeach
        </select>
    </div>

    <div style="display: flex; justify-content: flex-end;">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>


<script src="{{ asset('plugins/select2/select2.full.min.js') }}"></script>

<script>
    select2Dropdown(
        "#hrm_kpi_assesment_date",
        "{{ url('kpi_assesment_date_list_data') }}",
        "Select Year/Month"
    )

    @if (!is_null($kpiConfig))
        if (boolean(@json($kpiConfig->is_confirmed))) {
            location.reload()
        }
    @endif

    function boolean(params) {
        if (params == 1 || params == true) {
            return true;
        }

        return false;
    }
</script>
