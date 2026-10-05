<!-- employee_list -->
@extends('layouts.main')

<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

<style type="text/css">

  td.details-control {
      background: url('{{URL::to('/')}}/dist/img/details_open.png') no-repeat center center;
      cursor: pointer;
  }
  tr.shown td.details-control {
      background: url('{{URL::to('/')}}/dist/img/details_close.png') no-repeat center center;
  }


</style>

@endsection

<!-- content -->
@section('content')
<div class="box box-default">
	<div class="box-header with-border">
        <h3 class="box-title">Salary Deduction List</h3>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible">
                {{ session('success') }}

                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible">
                {{ session('error') }}

                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            </div>
        @endif

        <div class="nav-tabs-custom" style="margin-top:20px">
            <ul class="nav nav-tabs" id="myTab">
                <li class="active">
                    <a href="#pending" data-toggle="tab">
                        Pending List
                    </a>
                </li>

                <li class="">
                    <a href="#processed" data-toggle="tab">
                        Processed List
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="pending">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ url()->to('salary_deduction_create')}}" class="btn-success btn btn-sm button pull-left btn-flat" style="font-size:12px;font-weight:bold;margin-top:20px">
                                Create New
                            </a>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="location">Location</label>
                                <select class="form-control" name="location" id="location"></select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="month">Month</label>
                                <select id="month" name="month" class="form-control">
                                    <option value=""></option>
                                    @foreach ($month as $keys)
                                        {{-- @if($keys->id == now()->month)
                                            <option value={{$keys->id}} selected>{{$keys->month_name}}</option>
                                        @else --}}
                                            <option value="{{ $keys->id }}">{{ $keys->month_name }}</option>
                                        {{-- @endif --}}
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="year">Year</label>
                                <select id="year" name="year" class="form-control">
                                    @for ($i = date('Y') ; $i >= 2018; $i--)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-12">
                            <table id="salry_deduction_list_table_pending" class="table table-bordered table-hover" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th style="width: 20%">Employee Name</th>
                                        <th style="width: 10%">Location</th>
                                        <th style="width: 7%">Month</th>
                                        <th style="width: 7%">Amount</th>
                                        <th style="width: 10%">Salary Head</th>
                                        <th style="width: 25%">Purpose</th>
                                        <th style="width: 15%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="processed">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="location2">Location</label>
                                <select class="form-control" name="location2" id="location2" style="width: 100%"></select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="month2">Month</label>
                                <select id="month2" name="month2" class="form-control" style="width: 100%">
                                    <option value=""></option>
                                    @foreach ($month as $keys)
                                        {{-- @if($keys->id == now()->month)
                                            <option value={{$keys->id}} selected>{{$keys->month_name}}</option>
                                        @else --}}
                                            <option value="{{ $keys->id }}">{{ $keys->month_name }}</option>
                                        {{-- @endif --}}
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="year2">Year</label>
                                <select id="year2" name="year2" class="form-control" style="width:100%">
                                    @for ($i = date('Y') ; $i >= 2018; $i--)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-12">
                            <table id="salry_deduction_list_table_processed" class="table table-bordered table-hover" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th style="width: 20%">Employee Name</th>
                                        <th style="width: 10%">Location</th>
                                        <th style="width: 7%">Month</th>
                                        <th style="width: 7%">Amount</th>
                                        <th style="width: 10%">Salary Head</th>
                                        <th style="width: 25%">Purpose</th>
                                        <th style="width: 20%">Action</th>
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
	</div>
</div>

@endsection



<!-- script -->
@section('script')
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

<script>
    // function format(d) {
    //   return '<table  width="100%" >'+
    //         '<tr>'+
    //           '<td>'+"Employee Name"+'</td>'+
    //           '<td>'+"Department"+'</td>'+
    //           '<td>'+"Designation"+'</td>'+
    //           '<td>'+"Plant Name"+'</td>'+
    //         '</tr>'+
    //         '<tr>'+
    //           '<td>'+d.employee_name+'</td>'+
    //           '<td>'+d.department_name+'</td>'+
    //           '<td>'+d.designation_name+'</td>'+
    //           '<td>'+d.plant_name+'</td>'+
    //         '</tr>'+
    //       '</table>';
    // }

    $(document).ready(function($) {

        $location = $('#location').select2({
            placeholder: 'Enter a location',
            allowClear: true,
            ajax: {
                dataType: 'json',
                url: '{{URL::to('/')}}/location_list_data',
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
            pendingTable();
        });

        $location.on('select2:unselect', function (e) {
            $('#location').val(null).trigger("change");
            pendingTable();
        });

        $month = $("#month").select2({
            placeholder: 'Enter a month',
            allowClear: true,
        })

        $month.on('select2:select', function (e) {
            pendingTable();
        });

        $month.on('select2:unselect', function (e) {
            $('#month').val(null).trigger("change");
            pendingTable();
        });

        $year = $("#year").select2({
            placeholder: 'Enter a year',
            allowClear: true,
        })

        $year.on('select2:select', function (e) {
            pendingTable();
        });

        $year.on('select2:unselect', function (e) {
            $('#year').val(null).trigger("change");
            pendingTable();
        });

        function pendingTable() {
            $.ajax({
                type: 'POST',
                url : "{{ url('/salary_deduction_list_data?status=1') }}",
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                dataType: 'json',
                data: {
                   location: $("#location").val(),
                   month: $("#month").val(),
                   year: $('#year').val(),
                },
                success: function(data) {
                    var dataSet = data.data;
                    table = $('#salry_deduction_list_table_pending').DataTable({
                        destroy:    true,
                        paging:     true,
                        searching:  true,
                        ordering:   true,
                        bInfo:      false,
                        data:       dataSet,
                        "aoColumnDefs": [{ "bVisible": false, "aTargets": [0,1] }],
                        "columns": [
                            { "data": "id" },
                            { "data": "employee_name" },
                            {
                                "render": function (data, type, JsonResultRow, meta) {
                                    return '<img src="{{asset('employee_image')}}/'+JsonResultRow.Images+'" style="height:30px; width:30px; border-radius: 30px;"/>';
                                }
                            },
                            { "data": "Link",
                              "mRender": function (data, type, full) {
                                 return '<a target="_blank"  href="{{URL::to('/')}}/employeeinfo/'+full.hrm_employee_id+'">'+full.employee_name+'</a>';
                              }
                            },
                            { "data": "location_name" },
                            { "data": "month_from" },
                            { "data": "amount" },
                            { "data": "salary_head" },
                            { "data": "purpose" },
                            // { "data": "month_to" },
                            // { "data": "status" },
                            { "data": "Link",
                                "mRender": function (data, type, full) {

                                    if(full.is_active == 1 ) {
                                        return '<a href="{{ url()->to('/') }}/salary_deduction/'+full.id+'/edit" class="btn-sm btn text-primary"><span class="glyphicon glyphicon-edit"> Edit</a>' +
                                        '<a href="{{ url()->to('/') }}/salary_deduction/'+full.id+'/delete" onclick="return confirm(\'Do you really want to DELETE?\');" class="btn-sm btn text-danger"><span class="glyphicon glyphicon-trash"> Delete</a>';
                                    } else {
                                        return '<label for="html">Processed</label>';
                                    }
                                }
                            },
                        ],
                        "order": [[0,'asc']]
                    });
                }
            });
        }

        pendingTable()


        $location2 = $('#location2').select2({
            placeholder: 'Enter a location',
            allowClear: true,
            ajax: {
                dataType: 'json',
                url: '{{URL::to('/')}}/location_list_data',
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

        $location2.on('select2:select', function (e) {
            processedTable();
        });

        $location2.on('select2:unselect', function (e) {
            $('#location2').val(null).trigger("change");
            processedTable();
        });

        $month2 = $("#month2").select2({
            placeholder: 'Enter a month',
            allowClear: true,
        })

        $month2.on('select2:select', function (e) {
            processedTable();
        });

        $month2.on('select2:unselect', function (e) {
            $('#month2').val(null).trigger("change");
            processedTable();
        });

        $year2 = $("#year2").select2({
            placeholder: 'Enter a year',
            allowClear: true,
        })

        $year2.on('select2:select', function (e) {
            processedTable();
        });

        $year2.on('select2:unselect', function (e) {
            $('#year2').val(null).trigger("change");
            processedTable();
        });

        function processedTable() {
            $.ajax({
                type: 'POST',
                url : "{{ url('/salary_deduction_list_data?status=2') }}",
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                dataType: 'json',
                data: {
                    location: $("#location2").val(),
                    month: $("#month2").val(),
                    year: $('#year2').val(),
                },
                success: function(data) {
                    var dataSet = data.data;
                    table = $('#salry_deduction_list_table_processed').DataTable({
                        destroy:    true,
                        paging:     true,
                        searching:  true,
                        ordering:   true,
                        bInfo:      false,
                        data:       dataSet,
                        "aoColumnDefs": [{ "bVisible": false, "aTargets": [0,1] }],
                        "columns": [
                            { "data": "id" },
                            { "data": "employee_name" },
                            {
                                "render": function (data, type, JsonResultRow, meta) {
                                    return '<img src="{{asset('employee_image')}}/'+JsonResultRow.Images+'" style="height:30px; width:30px; border-radius: 30px;"/>';
                                }
                            },
                            { "data": "Link",
                              "mRender": function (data, type, full) {
                                 return '<a target="_blank"  href="{{URL::to('/')}}/employeeinfo/'+full.hrm_employee_id+'">'+full.employee_name+'</a>';
                              }
                            },
                            { "data": "location_name" },
                            { "data": "month_from" },
                            { "data": "amount" },
                            { "data": "salary_head" },
                            { "data": "purpose" },
                            // { "data": "month_to" },
                            // { "data": "status" },
                            { "data": "Link",
                                "mRender": function (data, type, full) {
                                    return '<label for="html">Processed</label>';
                                }
                            },
                        ],
                        "order": [[0,'asc']]
                    });
                }
            });
        }

        processedTable()
    });

</script>

@endsection
