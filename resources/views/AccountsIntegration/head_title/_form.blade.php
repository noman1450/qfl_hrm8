<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

<form class="dynamicFormSubmit" data-table-name="#headTitleDatatable" action="{{ is_null($headTitle) ? route('account_head_title.store') : route('account_head_title.update', encrypt($headTitle->id)) }}" method="post">
    @csrf

    @if (!is_null($headTitle)) @method('patch') @endif

    <div class="box-body">

        <div class="form-group">
            <label for="head_name">Head Name</label>
            <input type="text" class="form-control" name="head_name" value="{{ old('head_name', $headTitle->head_name ?? null) }}" id="head_name" placeholder="Head Name" style="width: 100%;" autocomplete="head_name">
        </div>


        
        <div class="form-group">
            <label>Under Main Head</label>
            <select class="form-control" name="hrm_acc_head_title_id" id="hrm_acc_head_title_id">
                @if (!is_null($headTitle))
                    <option value="{{ $headTitle->hrm_acc_head_title_id }}">{{ $headTitle->parent }}</option>
                @endif

                <option value=""></option>
            </select>
        </div>


        @if (!is_null($headTitle))
            <div class="form-group" id="hideJournalType" style="{{ $headTitle->hrm_acc_head_title_id ? 'display:none' : 'display:block' }}">
                <label for="">Journal Type</label>
                <select class="form-control hrm_acc_journal_type_id" name="hrm_acc_journal_type_id" >
                    <option value="{{ $headTitle->hrm_acc_journal_type_id }}">{{ $headTitle->journal_type_name }}</option>

                    <option value=""></option>
                </select>
            </div>
        @else
            <div class="form-group" id="hideJournalType">
                <label for="">Journal Type</label>
                <select class="form-control hrm_acc_journal_type_id" name="hrm_acc_journal_type_id" >
                    <option value=""></option>
                </select>
            </div>
        @endif





        <div class="form-group">
            <label for="status">Status</label>
                <select class="form-control" name="status" id="status">

                @if (!is_null($headTitle))

                    @if($headTitle->status == 'Dr'){
                        <option value="{{ $headTitle->status }}" selected>{{ $headTitle->status }}</option>
                        <option value="Cr">Cr</option>
                    }@endif
                    @if($headTitle->status == 'Cr'){
                        <option value="Dr">Dr</option>
                        <option value="Cr" selected>Cr</option>
                    }@endif
                @else
                    <option value="Dr">Dr</option>
                    <option value="Cr">Cr</option>  

                @endif


           
                </select>

        </div>

        <div class="form-group">
            <label for="isCasualWorker">
                <input type="checkbox" name="isCasualWorker" @if (!is_null($headTitle)) {{ $headTitle->isCasualWorker == 1 ? 'checked' : null }}  @endif id="isCasualWorker">
                 Apply For Casual Worker
            </label>
        </div>


        <div class="form-group">
            <label for="isSalaryAllow">
                <input type="checkbox" name="isSalaryAllow" @if (!is_null($headTitle)) {{ $headTitle->isSalaryAllow == 1 ? 'checked' : null }}  @endif id="isSalaryAllow">
                Apply Addition & Deduction(Salary Head)
            </label>
        </div>


        <div class="form-group">
            <label for="isCompanyContribute">
                <input type="checkbox" name="isCompanyContribute" @if (!is_null($headTitle)) {{ $headTitle->isCompanyContribute == 1 ? 'checked' : null }}  @endif id="isCompanyContribute">
                + Add- PF Company Contribution
            </label>
        </div>


        @if (!is_null($headTitle))
            <div class="form-group hidden">
                <label for="is_active">
                    <input type="checkbox" name="is_active" {{ $headTitle->is_active == 1 ? 'checked' : null }} id="is_active">
                    Is Active
                </label>
            </div>
        @endif

    </div>

    <div class="box-footer">
        <input type="submit" class="btn btn-success btn-flat pull-right submitBtn" value="Submit" style="margin-right: 10px;">
    </div>

</form>



<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

<script>
    $("#hrm_acc_head_title_id").select2({
        placeholder: "Search Head",
        width: '100%',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: "{{ route('account_head_title.dropdown') }}",
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
    }).on('select2:select', function() {
        $('#hideJournalType').hide()
        $(".hrm_acc_journal_type_id").val(null).trigger('change');
    }).on('select2:unselect', function() {
        $('#hideJournalType').show()
    });





    $(".hrm_acc_journal_type_id").select2({
        placeholder: "Search Journal Type",
        width: '100%',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: "{{ route('account_journal_type.dropdown') }}",
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

    // var permission_table = $('#permission_table').DataTable({
    //     paging:       false,
    //     lengthChange: true,
    //     searching:    false,
    //     ordering:     false,
    //     info:         false,
    //     autoWidth:    false,
    //     "width":        "100%"
    // });

    // $('#addMore').click(addMore);

    // $('#head_name').keypress(e => {
    //     (e.key === 'Enter' && e.keyCode === 13)
    //         ? addMore(e)
    //         : !1
    // })

    // function addMore(event) {
    //     event.preventDefault();

    //     var head_name = $('#head_name').val(),
    //         hrm_acc_journal_type_id = $('#hrm_acc_journal_type_id').val()

    //     if (isBlank(hrm_acc_journal_type_id)) {
    //         alert('journal type can not be empty')

    //         $('#hrm_acc_journal_type_id').focus()

    //         $('#hrm_acc_journal_type_id').select2('open')

    //         return
    //     }

    //     if (isBlank(head_name)) {
    //         alert('head name can not be empty')

    //         $('#head_name').focus()

    //         return
    //     }

    //     var entry = [
    //         `
    //             ${$('#hrm_acc_journal_type_id option:selected').text()}
    //             <input type="hidden" name="hrm_acc_journal_type_id[]" value="${hrm_acc_journal_type_id}">
    //         `,

    //         `<input type="text" class="form-control required" name="head_name[]" id="head_name.0" value="${head_name}" placeholder="Head Name" style="width: 100%;">`,

    //         `<button type="button" class="btn btn-danger btn-block btn-flat btn-sm delete-button"><i class="fa fa-trash-o"></i> Del</button>`,
    //     ];

    //     $('#head_name').val('')
    //     $("#hrm_acc_journal_type_id").text(""),
    //     $("#hrm_acc_journal_type_id").val(""),

    //     permission_table.row.add(entry).draw(false);

    //     $('#hrm_acc_journal_type_id').focus()

    //     $('#hrm_acc_journal_type_id').select2('open')
    // }

    // $('#permission_table tbody').on( 'click', '.delete-button', function () {
    //     permission_table.row($(this).parents('tr')).remove().draw();
    // });
</script>
