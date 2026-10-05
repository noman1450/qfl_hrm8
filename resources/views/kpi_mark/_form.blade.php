<form class="dynamicFormSubmit" action="{{ is_null($kpiMark) ? route('kpi_mark.store') : route('kpi_mark.update', encrypt($kpiMark->id)) }}" data-table-name="#mark_setup_table" method="post">

    @if (! is_null($kpiMark)) @method('PATCH') @endif

    @if (is_null($kpiMark))
        <input type="hidden" name="hrm_kpi_set_config_id" value="{{ $hrm_kpi_set_config_id }}">
    @endif

    <div class="form-group">
        <label>Mark Name</label>
        <input class="form-control" type="text" placeholder="Excellent/Very Good" value="{{ old('mark_name', $kpiMark->description ?? null) }}" id="mark_name"  name="mark_name">
    </div>

    <div class="form-group">
        <label>Point</label>
        <input class="form-control" type="number" step="0.01" placeholder="Point" value="{{ old('point', $kpiMark->point ?? null) }}" id="point"  name="point">
    </div>

    <div style="display: flex; justify-content: flex-end;">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>
