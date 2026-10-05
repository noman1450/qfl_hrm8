<!-- create_employee -->
<!-- create_marital_status -->
@extends('layouts.main')

@section('styles')
    <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">


    <style>

    </style>
@endsection


@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Employee Bulk Data - Import excel file into database</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="box-body">
            <div class="row">
                <div class="panel-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <h4>Download Employee Information Sample Excel File: </h4>
                    <div
                        style="display: flex; align-items: center; border: 4px solid #a1a1a1;margin-top: 15px;padding: 20px;">

                        <!-- <a href="{{ url('downloadExcel/xls') }}"><button class="btn btn-success btn-lg">Download Excel xls</button></a> -->
                        <a style="margin-right: 7rem; " href="{{ url('downloadExcel/xlsx') }}">
                            <button class="btn btn-success ">Download Sample Excel File 1</button>
                        </a>
                        <div>
                            <form action="{{ URL::to('importExcel') }}" class="form-horizontal" method="post"
                                  enctype="multipart/form-data">
                                @csrf
                                <input type="file" name="import_file"/>

                                <br/>

                                <button class="btn btn-primary">Import Excel File 1</button>
                            </form>
                        </div>
                        <!-- <a href="{{ url('downloadExcel/csv') }}"><button class="btn btn-success btn-lg">Download CSV</button></a> -->
                    </div>

                    <br><br><br><br>

                    <h4>Import Employee Information File:</h4>

                    <div
                        style="display: flex; align-items: center; border: 4px solid #a1a1a1;margin-top: 15px;padding: 20px;">

                        <a style="margin-right: 7rem;" href="{{ url('downloadExcel2/xlsx') }}">
                            <button class="btn btn-success ">Download Sample Excel File 2</button>
                        </a>

                        <form action="{{ URL::to('importExcel2') }}" class="form-horizontal" method="post"
                              enctype="multipart/form-data">
                            @csrf
                            <div style="margin-bottom: 1rem">
                                <label style="font-weight: normal; font-size: 12px" for="location">Select Job Location
                                    <i
                                        class="text-danger">*</i></label>
                                <select class="form-control col-lg-12" id="location" name="location"
                                        style="width: 100%; margin-bottom: 2rem">
{{--                                    <option value={{ old($location->id) }} selected>{{ old($location->location_name) }}</option>--}}
                                </select>
                                @error('location')
                                <span class="text-danger" style="font-size: 12px">{{ $message }}</span>
                                @enderror
                            </div>


                            <input type="file" name="import_file"/>
                            @error('import_file')
                            <span class="text-danger" style="font-size: 12px">{{ $message }}</span>
                            @enderror

                            <br/>

                            <button class="btn btn-primary">Import Excel File 2</button>
                        </form>
                    </div>
                </div>


                <br/>
            </div>
        </div>
    </div>
    </div>
@endsection


@section('script')
    <script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
    <script src="{{asset('js/fileinput.js')}}"></script>
    <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
    <script>
        $(document).ready(function ($) {
            $('#location').select2({
                placeholder: 'Choose Location',
                allowClear: true,
                ajax: {
                    dataType: 'json',
                    url: '{{URL::to('/')}}/location_list_data',
                    delay: 250,
                    data: function (params) {
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
                    cache: true
                }
            });
        });
    </script>
@endsection
