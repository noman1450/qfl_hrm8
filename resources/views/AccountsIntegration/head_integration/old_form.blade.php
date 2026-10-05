<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

<form class="dynamicFormSubmit" data-table-name="#headIntegrationDatatable" action="{{ is_null($headIntegration) ? route('account_head_integration.store') : route('account_head_integration.update', encrypt($headIntegration->id)) }}" method="post">
    @csrf

    @if (!is_null($headIntegration)) @method('patch') @endif

    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <label>Head Name</label>
            </div>

            <div class="col-md-6">
                <label>Acc Link Api</label>
            </div>
        </div>

        @if (is_null($headIntegration))
            @foreach ($parentHeads as $idx => $head)
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" value="{{ $head->head_name }}" disabled>
                            <input type="hidden" class="form-control" name="hrm_acc_head_title_id[]" value="{{ $head->id }}">
                        </div>
                    </div>

<!--                     <div class="col-md-6">
                        <div class="form-group">
                            <select class="form-control accounts_ledger_head_id" name="accounts_ledger_head_id[]" id="accounts_ledger_head_id.{{ $idx }}">
                                <option value="">Select</option>

                                @foreach (['One', 'Two', 'Three', 'Four', 'Five'] as $key => $item)
                                    <option value="{{ $key + 1 }}|{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
 -->

                    <div class="col-md-6">
                        <div class="form-group">
                            <select class="form-control accounts_ledger_head_id" name="accounts_ledger_head_id" id="accounts_ledger_head_id">
                            </select>
                        </div>
                    </div>



                </div>
            @endforeach
        @else
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" value="{{ $headIntegration->head_name }}" disabled>
                        <input type="hidden" class="form-control" name="hrm_acc_head_title_id" value="{{ $headIntegration->hrm_acc_head_title_id }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <select class="form-control accounts_ledger_head_id" name="accounts_ledger_head_id" id="accounts_ledger_head_id">
                            <option value="">Select</option>

                            @foreach (['One', 'Two', 'Three', 'Four', 'Five'] as $key => $item)
                                <option {{ $headIntegration->accounts_ledger_head_id.'|'.$headIntegration->accounts_ledger_head_name === ($key + 1).'|'.$item ? 'selected' : null }} value="{{ $key + 1 }}|{{ $item }}">{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="box-footer">
        <input type="submit" class="btn btn-success btn-flat pull-right submitBtn" value="Submit" style="margin-right: 10px;">
    </div>

</form>


<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

<script>
    $("#accounts_ledger_head_id").select2({
        placeholder: "Search Accounts Ledger Head",
        width: '100%',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: "{{ route('getAccountsLedgerHeadId') }}",
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
