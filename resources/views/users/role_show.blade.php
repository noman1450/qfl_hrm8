@extends('layouts.main')

@section('styles')

    <style>
        /* ————————————————————–
        Tree core styles
        */

        ul li {
            list-style-type: none;
        }
        .my-tree { margin: 1em; }

        .my-tree input {
            position: absolute;
            clip: rect(0, 0, 0, 0);
        }

        .my-tree input ~ ul { display: none; }

        .my-tree input:checked ~ ul {
            display: block;
            margin-left: -30px;
            margin-top: -5px;
        }

        /* ————————————————————–
        Tree rows
        */
        .my-tree li {
            line-height: 1.2;
            position: relative;
            padding: 0 0 1em 1em;
        }

        .my-tree ul li { padding: 1em 0 0 1em; }

        .my-tree > li:last-child { padding-bottom: 0; }

        /* ————————————————————–
        Tree labels
        */
        .my-tree_label {
            position: relative;
            display: inline-block;
            background: #fff;
        }

        label.my-tree_label { cursor: pointer; }

        label.my-tree_label:hover { color: #666; }

        /* ————————————————————–
        Tree expanded icon
        */
        label.my-tree_label:before {
            background: #000;
            color: #fff;
            position: relative;
            z-index: 1;
            float: left;
            margin: 0 1em 0 -2em;
            width: 1em;
            height: 1em;
            border-radius: 1em;
            content: '+';
            text-align: center;
            line-height: 1em;

        }

        :checked ~ label.my-tree_label:before {
            content: '–';
            line-height: .9em;
            margin-right: 25px;
        }

        span.my-tree_label {
            padding-left: 10px;
        }

        /* ————————————————————–
        Tree branches
        */
        .my-tree li:before {
            position: absolute;
            top: 0;
            bottom: 0;
            left: -.5em;
            display: block;
            width: 0;
            border-left: 1px solid #777;
            content: "";
        }

        .my-tree_label:after {
            position: absolute;
            top: 0;
            left: -1.5em;
            display: block;
            height: 0.5em;
            width: 1.8em;
            border-bottom: 1px solid #777;
            border-left: 1px solid #777;
            border-radius: 0 0 0 .3em;
            content: '';
        }

        label.my-tree_label:after { border-bottom: 0; }

        :checked ~ label.my-tree_label:after {
            border-radius: 0 .3em 0 0;
            border-top: 1px solid #777;
            border-right: 1px solid #777;
            border-bottom: 0;
            border-left: 0;
            bottom: 0;
            top: 0.5em;
            height: auto;
        }

        .my-tree li:last-child:before {
            height: 1em;
            bottom: auto;
        }

        .my-tree > li:last-child:before { display: none; }
    </style>

@endsection

@section('content')
<div class="box box-default">
    <div class="box-body">
        <div class="row" style="margin-bottom: 30px">
            <div class="col-lg-12">
                <div style="text-align: right">
                    <a class="btn btn-primary" href="{{ url()->to('/role') }}"> Back</a>
                </div>

                <div style="margin-left: 35px">
                    <h2>Details for (<span class="text-info">{{ $role->name }}</span>) Role</h2>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-lg-12">
                <div id="treeview_container" class="hummingbird-treeview" style="padding: 0 40px 0 0;">
                    {!! $permissionTreeView !!}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
