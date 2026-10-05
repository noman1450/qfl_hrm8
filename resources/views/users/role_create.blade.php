@extends('layouts.main')

@section('styles')
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

@section('content')
<div class="box box-default">
    <div class="box-body">
        <div class="box-header with-border">
            <h3 class="box-title">Create New Role</h3>
            <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
            </div>
            <div class="box-tools pull-right">
                <a type="button" href="{{ url()->to('/role') }}" class="btn btn-box-tool"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
            </div>
        </div>
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ url('/role') }}" method="post">
            @csrf
            <div class="row">
                <div class="col-12">
                    <div style="margin-bottom:25px;margin-left:35px;">
                        <button type="button" class="btn btn-primary" id="checkAll">Check All</button>
                        <button type="button" class="btn btn-primary" id="uncheckAll">Uncheck All</button>
                    </div>
                </div>

                <div class="col-12">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" style="margin-left: 35px;">
                                <strong>Role Name:</strong>
                                <input type="text" name="name" placeholder="Role Name" class="form-control">
                            </div>
                        </div>
                    </div>

                </div>


                <div class="col-12">
                    <div id="treeview_container" class="hummingbird-treeview" style="padding: 0 40px 0 0;">
                        {!! $permissionTreeView !!}
                    </div>

                    <div class="col-md-12">
                        <input type="submit" class="btn btn-success btn-flat pull-right mybutton submitBtn" value="Submit" id="btnSubmit">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('script')
    <script src="{{ asset('treeview-checkbox/hummingbird-treeview.js') }}"></script>

    <script>
        $("#treeview").hummingbird();

        $("#checkAll").click(function() {
            $("#treeview").hummingbird("checkAll");
        });

        $("#uncheckAll").click(function() {
            $("#treeview").hummingbird("uncheckAll");
        });
    </script>
@endsection
