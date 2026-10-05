<!-- attandance_list -->
@extends('layouts.main')
<!-- styles -->
@section('styles')
<style type="text/css">
    .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
        padding: 5px;
    }
    .table>tbody{
        font-size: small;
    }
    .table>thead{
        font-size: smaller;
    }
    .table tr td input {
        width: 110px !important;
    }
</style>
@endsection


@section('content')
<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">Report Signatory</h3>
        {{-- <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div> --}}
    </div>

  <form  method="POST" action="{{url('report_signatory')}}">
        {{ csrf_field() }}
    <div class="box-body">
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table id="report_signatory_list_table" class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th style="width: 5%;">Report name</th>
                                <th style="width: 9%;">Signature1</th>
                                <th style="width: 9%;">Signature2</th>
                                <th style="width: 9%;">Signature3</th>
                                <th style="width: 9%;">Signature4</th>
                                <th style="width: 9%;">Signature5</th>
                                <th style="width: 9%;">Signature6</th>
                                <th style="width: 9%;">Signature7</th>
                                <th style="width: 9%;">Signature8</th>
                                <th style="width: 9%;">Signature9</th>
                                <th style="width: 9%;">Signature10</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataset as $data)
                            <tr>
                                <td>{{ $data->report_name }}</td>
                                <td><input type="text" name="report[{{ $data->id }}][sg1]" value="{{ $data->sg1 }}"></td>
                                <td><input type="text" name="report[{{ $data->id }}][sg2]" value="{{ $data->sg2 }}"></td>
                                <td><input type="text" name="report[{{ $data->id }}][sg3]" value="{{ $data->sg3 }}"></td>
                                <td><input type="text" name="report[{{ $data->id }}][sg4]" value="{{ $data->sg4 }}"></td>
                                <td><input type="text" name="report[{{ $data->id }}][sg5]" value="{{ $data->sg5 }}"></td>
                                <td><input type="text" name="report[{{ $data->id }}][sg6]" value="{{ $data->sg6 }}"></td>
                                <td><input type="text" name="report[{{ $data->id }}][sg7]" value="{{ $data->sg7 }}"></td>
                                <td><input type="text" name="report[{{ $data->id }}][sg8]" value="{{ $data->sg8 }}"></td>
                                <td><input type="text" name="report[{{ $data->id }}][sg9]" value="{{ $data->sg9 }}"></td>
                                <td><input type="text" name="report[{{ $data->id }}][sg10]" value="{{ $data->sg10 }}"></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        <input type="submit"  value="Submit" class=" btn-sm btn-success block btn-flat btn" style="margin-left: 15px; padding: 7px 10px; color: black; border:1px solid gray;">

        </div>
    </div>
</form>
</div>
@endsection
