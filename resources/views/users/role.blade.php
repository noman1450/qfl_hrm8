@extends('layouts.main')

@section('styles')
    <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@endsection

@section('content')
    <section class="content">
        @if (session('success'))
            <div class="row">
                <div class="alert alert-success">
                    {{ session('success') }}

                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        @endif

        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Role List</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                </div>
            </div>

            <div class="box-body">
                <div class="row">
                    <div class="box-body">
                        <div class="form-group">
                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <div class="box-header with-border">
                            <a href="{{ url()->to('/role/create') }}" class="col-lg-1 col-md-1 col-xs-1 btn btn-success " >+Create New Role</a>
                        </div>

                        <div class="col-md-6" style="padding-bottom: 10px;">
                            <table id="list_table" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th style="width:20%;">Role Name</th>
                                    <th style="width:20%;">Edit</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
@section('script')
    <script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
    <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
    <script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('js/fileinput.js')}}"></script>
    <script>

        $(document).ready(function($) {
            $.ajax({
                type:   'POST',
                url :   "{{URL::to('/')}}/role_list",
                headers:{
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(data) {
                    var dataSet = data.data;
                    table = $('#list_table').DataTable( {
                        destroy:    true,
                        paging:     false,
                        searching:  false,
                        ordering:   true,
                        bInfo:      false,
                        "data":     dataSet,
                        "columns": [

                            { "data": "name" },
                            { "data": "Link",
                                "mRender": function (data, type, full) {
                                    //return '<a href="{{ url('/')}}/role/'+full.id+'/show" class="btn btn-sm btn-info" style="margin-right:3px"> <span class="glyphicon glyphicon-eye-open"></span> Show</a>' +
                                    return '<a href="{{ url('/')}}/role/'+full.id+'/edit" class="btn btn-sm btn-primary" style="margin-right:3px"> <span class="glyphicon glyphicon-edit"></span> Edit</a>' +
                                    '<a href="{{ url('/')}}/role/'+full.id+'/delete" class="btn btn-sm btn-danger" onclick="return confirm(\'Are sure to delete this..!\')"> <span class="glyphicon glyphicon-trash"></span> Delete</a>';
                                }
                            },
                        ],
                        "order": [[0,'asc']]
                    });
                }
            });
        });
    </script>
@endsection









