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
            <div class="col-md-12">
                <div style="text-align: center;">
                    <h4 class="" style="font-weight:bold;">{{ $company_name }}</h4>
                    <h5 class="" style="font-weight:bold;">{{ $address }}</h5>
                    <h5 class="" style="font-weight:bold;" >{{ $title }}</h5>
                </div>

                <div style="display:flex; align-items:center;justify-content:space-between;margin-top:30px">
                    <div class="form-group">
                        <label for="location" style="margin-right: 15px">Location: </label>
                        <select name="location" id="location" class="form-control" style="width:400px;">
                            <option value="{{ $location->id }}">{{ $location->location_name }}</option>
                        </select>
                    </div>

                    <div style="display: flex; align-items: center; gap: 10px">
                        <label>Date:</label>
                        <input type="text" class="form-control pull-right onchange" id="filter_date" data-date-format="dd-mm-yyyy" value="{{ date('d-m-Y', strtotime(request('filter_date')) ) }}" readonly>
                    </div>
                </div>

                <table id="list_table" class="table table-bordered table-hover " cellspacing="0" width="100%" >
                    <thead>
                            <tr style="text-align: center;">
                                <th style="width: 20%">Department</th>
                                <th style="width: 10%">Total Employee</th>
                                <th style="width: 10%">OnTime</th>
                                <th style="width: 10%">Late</th>
                                <th style="width: 10%">Absent</th>
                                <th style="width: 10%">EarlyOut</th>
                                <th style="width: 10%">TotalLeave</th>
                                <th style="width: 10%">OSD</th>
                                <th style="width: 10%">Holiday</th>
                            </tr>


                    </thead>
                        <tbody>
                        </tbody>

                        <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>

                    </table>
                </div>
		    </div>
	    </div>
    </form>
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

    $location = $('#location').select2({
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

    $location.on('select2:select', function (e) {
        dataLoad();
    });


    $location.on('select2:unselect', function (e) {
        $('#location').val(null).trigger("change");
        dataLoad();
    });

    dataLoad();

    function dataLoad() {
        var table = $('#list_table').DataTable({
            destroy: true,
            paging: false,
            searching: true,
            ordering: true,
            bInfo: true,
            aoColumnDefs: [
                    { "className": "text-center", "targets": [1, 2, 3, 4, 5,6,7,8] }
                ],

                "footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;
                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '')*1 :
                            typeof i === 'number' ?
                                i : 0;
                    };


                    total_employee = api
                        .column(1)
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        },0);


                    ontime = api
                        .column(2)
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        },0);

                    late = api
                        .column(3)
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        },0);
                    absent = api
                        .column(4)
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        },0);
                    early_out = api
                        .column(5)
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        },0);
                    total_leave = api
                        .column(6)
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        },0);
                    osd = api
                        .column(7)
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        },0);
                    holiday = api
                        .column(8)
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        },0);
                    $( api.column( 0 ).footer() ).html('Total : ');
                    $( api.column( 1 ).footer() ).html(total_employee);
                    $( api.column( 2 ).footer() ).html(ontime);
                    $( api.column( 3 ).footer() ).html(late);
                    $( api.column( 4 ).footer() ).html(absent);
                    $( api.column( 5 ).footer() ).html(early_out);
                    $( api.column( 6 ).footer() ).html(total_leave);
                    $( api.column( 7 ).footer() ).html(osd);
                    $( api.column( 8 ).footer() ).html(holiday);
                },

            ajax: {
                url: "{{ url('attendance_summary_data_location') }}",
                type: "GET",
                dataType: "json",
                data: {
                    filter_date: $('#filter_date').val(),
                    hrm_location_id: $('#location option:selected').val()
                }
            },
            columns: [
                { "data": "DepartmentName" },
                { "data": "total_employee" },
                { "data": "ontime" },
                { "data": "late" },
                { "data": "absent" },
                { "data": "early_out" },
                { "data": "total_leave" },
                { "data": "osd" },
                { "data": "holiday" },
            ],

        });
    }
})
</script>
@endsection
