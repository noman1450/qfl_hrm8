
<form class="dynamicFormSubmit" data-table-name="#journalTypeDatatable" action="{{ is_null($journal) ? route('account_journal_type.store') : route('account_journal_type.update', encrypt($journal->id)) }}" method="post">
    @csrf

    @if (!is_null($journal)) @method('patch')  @endif

    <div class="box-body">
        <div class="form-group">
            <label>Journal Type Name</label>
            <input type="text" name="journal_type_name" class="form-control" id="journal_type_name" value="{{ old('journal_type_name', $journal->journal_type_name ?? null) }}" placeholder="Journal Type Name">
        </div>


    </div>

    <div class="box-footer">
        <input type="submit" class="btn btn-success btn-flat pull-right submitBtn" value="Submit" style="margin-right: 10px;">
    </div>

</form>


<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

<script>


    $("#hrm_salary_head_id").select2({
        placeholder: "Search Salary Head",
        width: '100%',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: "{{ url('salary_head_list') }}",
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
