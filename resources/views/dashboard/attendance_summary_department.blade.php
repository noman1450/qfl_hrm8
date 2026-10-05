@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
@endsection

@section('content')
<div class="box box-primary">
	<div class="box-body">
		<div class="row">
            <div class="col-md-10">
                <div style="text-align: center;">
                    <h4 class="" style="font-weight:bold;">{{ $company_name }}</h4>
                    <h5 class="" style="font-weight:bold;">{{ $address }}</h5>
                    <h5 class="" style="font-weight:bold;" >{{ $title }}</h5>
                </div>

                <div style="display:flex; align-items:center;justify-content:space-between;margin-top:30px">

       
                    <div class="form-group">
                        <label for="hrm_location_id" style="margin-right: 15px">Location: </label>
                        <select name="hrm_location_id" id="hrm_location_id" class="form-control" style="width:400px;">
                        
                            <option value="{{ request()->hrm_location_id }}">{{ request()->location_name }}</option>

                        </select>
                    </div>


                    <div class="form-group">
                        <label for="department" style="margin-right: 15px">Department: </label>
                        <select name="department" id="department" class="form-control" style="width:400px;">
                            <option value="{{ $department->id }}">{{ $department->depertment_name }}</option>
                        </select>
                    </div>

                    <div style="display: flex; align-items: center; gap: 10px">
                        <label>Date:</label>
                        <input type="text" class="form-control pull-right onchange" id="filter_date" data-date-format="dd-mm-yyyy" value="{{ date('d-m-Y', strtotime(request('filter_date')) ) }}" readonly>
                    </div>
                </div>

                <table id="list_table" class="table table-bordered table-hover " cellspacing="0" width="100%" >
                    <thead>
                        <tr>
                            <th style="width: 17%">Name</th>
                            <th style="width: 13%">Designation</th>
                            <th style="width: 13%">Department</th>
                            <th style="width: 13%">Location</th>
                            <th style="width: 10%">Status</th>
                            <th style="width: 6%">IN Time</th>
                            <th style="width: 6%">OUT Time</th>
                            <!-- <th style="width: 6%">Work Hour</th> -->
                            <th style="width: 6%">Late Time</th>
                            <th style="width: 6%">OT</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/bootstrapvalidator/bootstrapValidator.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

<script>
$(document).ready( function () {
    $('#filter_date').datepicker({
        autoclose: true
    });

    $department = $('#department').select2({
        placeholder: 'Enter a Department',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: "{{ url('/depertment_list_data')}}",
            delay: 250,
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
            cache: true
        }
    });

    $department.on('select2:select', function (e) {
        dataLoad();
    });


    $department.on('select2:unselect', function (e) {
        $('#department').val(null).trigger("change");
        dataLoad();
    });



   $hrm_location_id = $('#hrm_location_id').select2({
        placeholder: 'Enter a location',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: "{{ url('/location_list_data')}}",
            delay: 250,
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
            cache: true
        }
    });

    $hrm_location_id.on('select2:select', function (e) {
        dataLoad();
    });


    $hrm_location_id.on('select2:unselect', function (e) {
        $('#hrm_location_id').val(null).trigger("change");
        dataLoad();
    });




    dataLoad();

    $("#filter_date").change(function(){
        dataLoad();
    });



    function dataLoad() {
        var table = $('#list_table').DataTable({
            destroy: true,
            paging: false,
            searching: true,
            ordering: true,
            bInfo: true,
            // scrollX: true,

            ajax: {
                url: "{{ url('attendance_summary_data_department') }}",
                type: "GET",
                dataType: "json",
                data: {
                    filter_date: $('#filter_date').val(),
                    hrm_location_id: $('#hrm_location_id').val(),
                    hrm_depertment_id: $('#department').val(),
                }
            },
            columns: [
                { "data": "employee_name" },
                { "data": "designation_name" },
                { "data": "depertment_name" },
                { "data": "location_name" },
                { "data": "attendance_status" },
                { "data": "in_time" },
                { "data": "out_time" },
                // { "data": "work_hour" },
                { "data": "late_time" },
                { "data": "overtime_time" },

            ],
        });
    }
})
</script>
@endsection
