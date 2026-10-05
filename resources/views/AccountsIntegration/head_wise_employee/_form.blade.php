@section('styles')
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('treeview-checkbox/hummingbird-treeview.css') }}">

<style>
    .stylish-input-group .form-control{
        box-shadow:0 0 0;
        border-color:#ccc;
    }
    .stylish-input-group button{
        border:0;
        background:transparent;
    }
    .hummingbird-treeview label.parent-menu {
        font-weight: 600;
    }
</style>
@endsection

<form class="dynamicFormSubmit" redirectUrl="{{ route('account_head_wise_employee_setup.index') }}" action="{{ is_null($headWiseEmpSetup) ? route('account_head_wise_employee_setup.store') : route('account_head_wise_employee_setup.update', encrypt($headWiseEmpSetup->id)) }}" method="post">
    @csrf

    @if (!is_null($headWiseEmpSetup)) @method('patch') @endif

    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Head Name</label>

                    <select class="form-control hrm_acc_head_title_id" name="hrm_acc_head_title_id" id="hrm_acc_head_title_id">
                        @if (!is_null($headWiseEmpSetup))
                            <option value="{{ $headWiseEmpSetup->hrm_acc_head_title_id }}">{{ $headWiseEmpSetup->head_name }}</option>
                        @endif
                        <option value=""></option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <h3>Location</h3>
                <div style="margin-bottom:25px;margin-top:25px;margin-left:35px;">
                    <button type="button" class="btn btn-primary" id="checkAllLocation">Check All</button>
                    <button type="button" class="btn btn-primary" id="uncheckAllLocation">Uncheck All</button>
                </div>

                <div class="hummingbird-treeview">
                    {!! $locations !!}
                </div>
            </div>

            <div class="col-md-4">
                <h3>Category</h3>
                <div style="margin-bottom:25px;margin-top:25px;margin-left:35px;">
                    <button type="button" class="btn btn-primary" id="checkAllCategory">Check All</button>
                    <button type="button" class="btn btn-primary" id="uncheckAllCategory">Uncheck All</button>
                </div>

                <div class="hummingbird-treeview">
                    {!! $categories !!}
                </div>
            </div>

            <div class="col-md-4">
                <h3>Salary Head (N/A- CW)</h3>
                <div style="margin-bottom:25px;margin-top:25px;margin-left:35px;">
                    <button type="button" class="btn btn-primary" id="checkAllSalary">Check All</button>
                    <button type="button" class="btn btn-primary" id="uncheckAllSalary">Uncheck All</button>
                </div>

                <div class="hummingbird-treeview">
                    {!! $salaryHeads !!}
                </div>
            </div>
        </div>

        <div class="box-footer">
            <input type="submit" class="btn btn-success btn-flat pull-right submitBtn" value="Submit" style="margin-right: 10px;">
        </div>
    </div>
</form>


@section('script')
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{ asset('treeview-checkbox/hummingbird-treeview.js') }}"></script>

<script>
    $("#location").hummingbird();

    $("#checkAllLocation").click(function() {
        $("#location").hummingbird("checkAll");
    });

    $("#uncheckAllLocation").click(function() {
        $("#location").hummingbird("uncheckAll");
    });

    // Category
    $("#category").hummingbird();

    $("#checkAllCategory").click(function() {
        $("#category").hummingbird("checkAll");
    });

    $("#uncheckAllCategory").click(function() {
        $("#category").hummingbird("uncheckAll");
    });

    // Salary Head
    $("#salary-head").hummingbird();

    $("#checkAllSalary").click(function() {
        $("#salary-head").hummingbird("checkAll");
    });

    $("#uncheckAllSalary").click(function() {
        $("#salary-head").hummingbird("uncheckAll");
    });

    $("#hrm_acc_head_title_id").select2({
        placeholder: "Search Head",
        width: '100%',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: "{{ route('account_head_title.dropdown.parent') }}",
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
