@extends('layouts.main')

@section('styles')
    <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@endsection

@section('content')

<section class="content">
    <div class="row" style="margin-top: -15px">
        <div class="col-12">
            <h4>Create Permissions</h4>
        </div>
    </div>

    <div class="row">
        @if (session('type') && session('message'))
            <div class="col-12">
                <div class="alert alert-{{ session('type') }}">
                    {{ session('message') }}
                </div>
            </div>
        @endif

        <div class="col-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse">
                            <i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <form action="{{ route('permission.store') }}" method="post" onkeypress="return event.keyCode != 13;">
                                @csrf
                                <div class="box-body">
                                    <div class="form-group">
                                        <label>Under</label>
                                        <select class="form-control" name="permission_id" id="permission_id">

                                            <option value=""></option>
                                        </select>
                                    </div>


                                    <div class="form-group" style="margin-top:25px">
                                        <div class="col-12" style="padding-right:0;padding-left:0">
                                            <div class="col-12" style="padding-right: 0;padding-left: 0">
                                                <table id="permission_table" class="table table-bordered table-hover">
                                                    <thead class="bg-info">
                                                        <tr>
                                                            <th>Permission Name <span class="text-danger">*</span></th>
                                                            <th>Action <span class="text-danger">*</span></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <th>
                                                                <input type="text" class="form-control" id="permission_name" placeholder="Permission Name" style="width: 100%;" autocomplete="permission_name">
                                                            </th>
                                                            <th>
                                                                <button type="button" id="addMore" class="btn btn-primary btn-block btn-flat btn-sm"><i class="fa fa-plus"></i> Add</button>
                                                            </th>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
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
    </div>
</section>
@stop

@section('script')
    <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>

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

        var permission_table = $('#permission_table').DataTable({
            paging:       false,
            lengthChange: true,
            searching:    false,
            ordering:     false,
            info:         false,
            autoWidth:    false,
            "width":        "100%"
        });

        $('#addMore').click(addMore);

        $('#permission_name').keypress(e => {
            (e.key === 'Enter' && e.keyCode === 13)
                ? addMore(e)
                : !1
        })

        function addMore(event) {
            event.preventDefault();

            permission_name = $('#permission_name').val()

            if (permission_name === '') {
                alert('Permission name can not be empty')

                $('#permission_name').focus()

                return
            }

            var entry = [
                `<input type="text" class="form-control required" name="name[]" id="name.0" value="${permission_name}" placeholder="Permission Name" style="width: 100%;">`,

                `<button type="button" class="btn btn-danger btn-block btn-flat btn-sm delete-button"><i class="fa fa-trash-o"></i> Del</button>`,
            ];

            $('#permission_name').val('')

            permission_table.row.add(entry).draw(false);

            $('#permission_name').focus()
        }

        $('#permission_table tbody').on( 'click', '.delete-button', function () {
            permission_table.row($(this).parents('tr')).remove().draw();
        });
    </script>
@endsection
