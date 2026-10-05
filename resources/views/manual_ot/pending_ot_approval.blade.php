@extends('layouts.main')
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables/jquery.dataTables.min.css')}}">
<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
padding: 5px;
}
table.dataTable thead > tr > th {
padding-right: 25px;
}
.table>tbody{
font-size: small;
}
.table>thead{
font-size: smaller;
}
td.details-control {
background: url('{{URL::to('/')}}/dist/img/details_open.png') no-repeat center center;
cursor: pointer;
}
tr.shown td.details-control {
background: url('{{URL::to('/')}}/dist/img/details_close.png') no-repeat center center;
}
</style>
@endsection

@section('content')
<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">Pending OT Employee Approval</h3>



        <div class="row" >
            <div class="col-md-2">
                <div class="form-group">
                    <label>Location Name</label>
                    <select class="form-control onchange" id="location" name="location" style="width: 100%;" >
                        @foreach ($location as $keys)
                            {{-- @if($keys->id==$default_user_location[0]->id)
                                <option value={{$default_user_location[0]->id}} selected>{{$default_user_location[0]->location_name}}</option>
                            @else --}}
                                <option value={{$keys->id}}>{{$keys->location_name}}</option>
                            {{-- @endif --}}
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label>OT Date</label>
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control onchange pull-right" id="ot_date" name="ot_date" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" required readonly>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label>Department</label>
                    <select class="form-control onchange" id="department" name="department" style="width: 100%;" >
                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label>Category</label>
                    <select class="form-control onchange" id="category" name="category" style="width: 100%;" >
                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label>Sub-Department</label>
                    <select class="form-control onchange" id="section" name="section" style="width: 100%;" >
                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label>Emp Name</label>
                    <select class="form-control onchange" id="employee_name" name="employee_name" style="width: 100%;" >
                    </select>
                </div>
            </div>


            <div class="col-xs-12" id="show-message"></div>

            <div class="form-group col-lg-12 col-md-12 col-xs-12">
                <table id="designation_list_table" class=" cell-border table table-bordered table-hover "  cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th style="width: 20%">Employee Name</th>
                            <th style="width: 15%">Department</th>
                            <th style="width: 10%">Designation</th>
                            <th style="width: 15%">Sub-Department</th>
                            <th style="width: 15%">Allow OT Time</th>
                            <th style="width: 15%">Actual OT(Exceed)</th>
                            <th style="width: 15%">Action</th>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('dist/js/jquery.inputmask.bundle.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('plugins/timepicker/bootstrap-timepicker.min.js')}}"></script>
<script>
$(document).ready(function($) {
    $('#ot_date').datepicker({
        autoclose: true
    });

    $(document).on('change', '.onchange', function() {
        loadData()
    })

    $('#department').select2({
        placeholder: 'Enter a Department',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: '{{URL::to('/')}}/depertment_list_data',
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

    $('#section').select2({
        placeholder: 'Enter a Section',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: '{{URL::to('/')}}/section_list_data',
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


    $('#category').select2({
        placeholder: 'Enter Employee Category',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: '{{URL::to('/')}}/category_list_data',
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


    $employee = $('#employee_name').select2({
        placeholder: 'Enter an Employee Name',
        allowClear: true,
            ajax: {
                dataType: 'json',
                url: "{{URL::to('/')}}/join_employee_list",
                delay: 250,
                data: function(params) {
                  return {
                    term: params.term
                  }
                },
                processResults: function (data, params) {
                  params.page = params.page || 1;
                  return {
                    results: data,
                    pagination: {
                      more: (params.page * 30) < data.total_count
                    }
                  };
                },
                cache: true
            }
    });



    function loadData() {
        $.ajax({
            type:   'GET',
            url :   "{{URL::to('/')}}/pending_ot_approval",
            data: {
                hrm_location_id  :$("#location").val(),
                hrm_depertment_id:$("#department").val(),
                hrm_employee_id  :$("#employee_name").val(),
                hrm_category_id  :$("#category").val(),
                hrm_section_id   :$("#section").val(),
                ot_date          :$('#ot_date').val(),
            },

            dataType: 'json',
            success: function(data) {
                var dataSet = data.data;
                table = $('#designation_list_table').DataTable( {
                    destroy:    true,
                    paging:     false,
                    searching:  false,
                    ordering:   true,
                    bInfo:      false,
                    "data":     dataSet,

                    "initComplete": function () {
                        $(".allow_ot").inputmask("hh:mm", {
                            placeholder: "HH:MM",
                            insertMode: false,
                            showMaskOnHover: false,
                            hourFormat: "24"
                        });

                        // $(".allow_ot").timepicker({
                        //     showMeridian:false,
                        //     showSeconds:false,
                        //     showInputs: false
                        // });

                        $(document).on('blur', '.allow_ot', function() {
                            let allow_ot = $(this).val()

                            let exceed_ot_hour = $(this).parents('tr').find('.exceed_ot_hour').val()

                            if (allow_ot > exceed_ot_hour) {
                                alert('Ot time can not excced actual time')
                                
                                $(this).val(exceed_ot_hour)

                                return
                            }
                        })
                    },

                    "columns": [
                        {  "data": "employee_name" },
                        {  "data": "depertment_name" },
                        {  "data": "designation_name" },
                        {  "data": "section_name" },
                        {  "data": "eligible_ot_hour",
                            "mRender": function (data, type, full) {
                                return '<input type="text" class="allow_ot bootstrap-timepicker" name="allow_ot['+full.id+']"  value="'+full.eligible_ot_hour+'">';
                            }
                        },
                        {  "data": "exceed_ot_hour",
                            "mRender": function (data, type, full) {
                                return `<input type="text" class="allow_ot exceed_ot_hour bootstrap-timepicker" style="outline:none;border:none;background:transparent;pointer-events:none;" value="${full.exceed_ot_hour}">`;
                            }
                        },
                        {  "data": "delete",
                                "mRender": function (data, type, full) {
                                    return `
                                        <a href="{{ url('/update_date_wise_eligible_ot_pending') }}/${full.id}?ot_date=${$('#ot_date').val()}&action=delete" class="btn btn-sm btn-danger update_ot">Reject</a>
                                        <a href="{{ url('/update_date_wise_eligible_ot_pending') }}/${full.id}?ot_date=${$('#ot_date').val()}&action=update" class="btn btn-sm btn-success update_ot">Update</a>
                                    `;
                                }
                        }
                    ],
                    order: [ 1, 'asc' ]
                });
            }
        });
    }

    loadData()

    $('#designation_list_table tbody').on('click', '.update_ot', function(e) {
        e.preventDefault()

        if (confirm('Are you sure.?')) {
            let otTime = $(this).closest('tr').find('td:eq(4)').find('input').val();

            $.get($(this).attr('href'), { ot_time: otTime })
                .then(response => {
                    $('#show-message').html(`
                        <div class="alert alert-success" style="padding:10px;font-size:16px;">
                            ${response}
                        </div>
                    `)

                    loadData()

                    setTimeout(() => {
                        $('#show-message').html("")
                    }, 1000);
                })
        }

    })
});
</script>
