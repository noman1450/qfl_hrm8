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
<div class="row">
    <div class="col-md-12">
        <div id="show-message"></div>
    </div>
</div>


<div class="box box-default">
    <form  method="POST" action="{{ url('submit_manual_process_ot_edit') }}" id="submit_manual_process_ot">
        {{ csrf_field() }}

        <input type="hidden" name="hrm_ot_process_master_id"  id="hrm_ot_process_master_id" value="">

        <div class="box-header with-border">
            <h3 class="box-title">Manual OT View</h3>

            <div class="row" style="margin-left:10px; ">
                <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
                    <select class="form-control" id="location" name="location" style="width: 100%;" >
                        @foreach ($location as $keys)
                           @if(!empty($default_user_location[0]) && $keys->id == $default_user_location[0]->hrm_location_id)
                            <option value="{{ $default_user_location[0]->hrm_location_id }}" selected>
                                {{ $default_user_location[0]->location_name }}
                            </option>
                            @else
                                <option value="{{$keys->id}}">{{$keys->location_name}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
                    <select class="form-control year" id="year" name="year" style="width: 100%;" >
                    </select>
                </div>

                <div class="col-lg-2 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
                    {{-- <select class="form-control" id="month_name" name="month_name" style="width: 100%;" >
                        <option value="{{ $default_user_location[0]->hrm_month_id }}" selected>{{$default_user_location[0]->month_name}}</option>
                    </select> --}}

                    <select class="form-control" id="month_name" name="month_name" style="width: 100%;">
                        @if(isset($default_user_location[0]))
                            <option value="{{ $default_user_location[0]->hrm_month_id }}" selected>
                                {{ $default_user_location[0]->month_name }}
                            </option>
                        @else
                            <option value="" disabled selected>Select Month</option>
                        @endif
                    </select>
                </div>

                <div class="col-lg-2 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
                    <select class="form-control" id="payment_id" style="width: 100%;" >
                        <option value="null">Select</option>
                        <option value="1">Cash</option>
                        <option value="2">Bank</option>
                    </select>
                </div>
            </div>

            <div class="row" style="margin-left:10px;" >
                <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
                    <select class="form-control" id="department" name="department" style="width: 100%;" >
                    </select>
                </div>

                <div class="col-lg-2 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
                    <select class="form-control category" id="category" name="category" style="width: 100%;" >
                    </select>
                </div>

                <div class="col-lg-2 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
                    <select class="form-control" id="section" name="section" style="width: 100%;" >
                    </select>
                </div>

                <div class="col-lg-2 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
                    <select class="form-control" id="bank_id" style="width: 100%;" >
                    </select>
                </div>

                <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
                    <input type="button" id="search" value="Search" class=" btn-sm block btn-flat btn" style="margin-right: 15px; padding: 7px 10px;background-color: #EEEEEE; color: black; border:1px solid gray;">
                </div>
            </div>

            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>

        <div class="box-body">
            <div class="row">
                <div class="form-group col-lg-12 col-md-12 col-xs-12">
                    <table id="designation_list_table" class=" cell-border table table-bordered table-hover "  cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th  style="width: 5%"><input name="select_all" value="1" id="example-select-all" type="checkbox" />_All</th>
                                <th style="width: 20%">Employee Name</th>
                                <th>Deparment</th>
                                <th>Designation</th>
                                <th>Actual OT</th>
                                <th style="width: 5%">Net OT</th>
                                <th style="width: 5%">Rate</th>
                                <th style="width: 5%">Add. Amt</th>
                                <th style="width: 5%">Deduct Amt</th>
                                <th>Pay Mode</th>
                                <th>Bank Name</th>
                                <th>Pay Account</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

                <div class="form-group col-lg-12 col-md-12 col-xs-12">
                    <input type="submit" value="Submit" class="btn-sm btn-success block btn-flat btn pull-right" style="">
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('dist/js/jquery.inputmask.bundle.js')}}"></script>

<script>
$(document).ready(function($) {
    for (i = new Date().getFullYear(); i > 2018; i--){
        $('.year').append($('<option />').val(i).html(i));
    }

    $('#location').select2({
        placeholder: 'Choose Location Mandatory',
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
                }
            },

            cache: true
        }
    });

    $('#month_name').select2({
        placeholder: 'Enter Month  Name',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: '{{URL::to('/')}}/monthlist',
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
                }
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

    $('.category').select2({
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

    $('#bank_id').select2({
        placeholder: 'Select Bank',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: "{{ url('get_bank_list') }}",
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
                }
            },

            cache: true
        }
    });

    $("#search").click(function() {
        if ($("#location").val() == null){
            location_id=0;
        } else {
            location_id = $("#location").val();
        }

        if ($("#month_name").val() == null) {
            month_name=0;
        } else {
            month_name = $("#month_name").val();
        }

        if ($("#year").val() == null) {
            year = 0;
        } else {
            year = $("#year").val();
        }

        if ($("#department").val() == null) {
            department_id = 0;
        } else {
            department_id = $("#department").val();
        }

        if ($("#category").val() == null) {
            category_id = 0;
        } else {
            category_id = $("#category").val();
        }

        if ($("#section").val() == null) {
            section_id = 0;
        } else {
            section_id = $("#section").val();
        }

        if ($("#payment_id").val() == 'null') {
            payment_id = 0;
        } else {
            payment_id = $("#payment_id").val();
        }

        if ($("#bank_id").val() == null) {
            bank_id = 0;
        } else {
            bank_id = $("#bank_id").val();
        }

        $.ajax({
            type:   'POST',
            url :   "{{ url('manual_ot_view_data') }}",
            headers:{'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            data:   {
                punch_date    : $("#process_date").val(),
                location      : location_id,
                department_id : department_id,
                category_id   : category_id,
                month_name    : month_name,
                year          : year,
                section_id    : section_id,
                payment_id    : payment_id,
                bank_id       : bank_id,
            },

            dataType: 'json',
            success: function(data) {
                var dataSet = data.data;
                table = $('#designation_list_table').DataTable( {
                    destroy:    true,
                    paging:     false,
                    searching:  true,
                    ordering:   true,
                    bInfo:      false,
                    autoWidth: false,
                    "data":     dataSet,
                    drawCallback: function() {
                        $('.bank_name').select2({
                            placeholder: 'Search Bank',
                            allowClear: true,
                            ajax: {
                            dataType: 'json',
                            url: "{{ url('get_bank_list') }}",
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
                    },
                    "columns": [
                        { "data": "checkbox",
                            "mRender": function (data, type, full) {
                                $("#hrm_ot_process_master_id").val(full.hrm_ot_process_master_id);
                                return '<input type="checkbox" name="id[]" value="'+full.id+'">';
                            }
                        },
                        { "data": "employee_name" },
                        { "data": "depertment_name" },
                        { "data": "designation_name" },
                        { "data": "actual_overtime" },
                        { "data": "text",
                            "mRender": function (data, type, full) {
                                return '<input type="text" style="width:100%" class="net_overtime" name="net_overtime['+full.id+']"  value="'+full.net_overtime+'">';
                            }
                        },
                        { "data": "text",
                            "mRender": function (data, type, full) {
                                return '<input type="text" style="width:100%" class="rate" name="rate['+full.id+']" value="'+full.rate+'">';
                            }
                        },

                        { "data": "text",
                            "mRender": function (data, type, full) {
                                return '<input type="text" style="width:100%" class="addition_amt" name="addition_amt['+full.id+']" value="'+full.addition_amt+'">';
                            }
                        },

                        { "data": "text",
                            "mRender": function (data, type, full) {
                                return '<input type="text" style="width:100%" class="deduction_amt" name="deduction_amt['+full.id+']" value="'+full.deduction_amt+'">';
                            }
                        },

                        { "data": "payment_mode",
                            "mRender": function (data, type, full) {
                                return `<select class="payment_mode" style="width:100%" name="payment_mode[${full.id}]">
                                        <option value="1" ${data == 1 ? 'selected' : ''}>Cash</option>
                                        <option value="2" ${data == 2 ? 'selected' : ''}>Bank</option>
                                    </select>`;
                            }
                        },

                        { "data": "bank_name",
                            "mRender": function (data, type, full) {
                                return `<select class="bank_name" style="width:100%" name="bank_name[${full.id}]">
                                        <option value="${full.hrm_bank_id}">${full.bank_name}</option>
                                    </select>`;
                            }
                        },

                        { "data": "text",
                            "mRender": function (data, type, full) {
                                return '<input type="text" class="account_no" name="account_no['+full.id+']" value="'+full.account_no+'">';
                            }
                        },

                        { "data": "text",
                            "mRender": function (data, type, full) {
                                return `<button type="button" data-id="${full.id}" class="btn btn-success btn-sm update-row">Update</button>`;
                            }
                        },
                    ],

                    order: [ 1, 'asc' ]
                });
            }
        });
    });

    $(document).on('click', '.update-row', function(e) {
        e.preventDefault();

        $(this).prop('disabled', true);
        $(this).text('Updating...');

        let data = {
            net_overtime: $(this).parents('tr').find('.net_overtime').val(),
            rate: $(this).parents('tr').find('.rate').val(),
            addition_amt: $(this).parents('tr').find('.addition_amt').val(),
            deduction_amt: $(this).parents('tr').find('.deduction_amt').val(),
            payment_mode: $(this).parents('tr').find('.payment_mode option:selected').val(),
            bank_name: $(this).parents('tr').find('.bank_name option:selected').val(),
            account_no: $(this).parents('tr').find('.account_no').val(),
            employee_job_id: $(this).data('id'),
            hrm_ot_process_master_id: $('#hrm_ot_process_master_id').val(),
            _token: "{{ csrf_token() }}",
        }

        $.post("{{ url('submit_manual_process_ot_edit_single') }}", data)
            .then(() => {
                $(this).prop('disabled', false);
                $(this).text('Update');
            })
    })

    $('#example-select-all').on('click', function(){
        var rows = table.rows({ 'search': 'applied' }).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);
    });

    $('#designation_list_table tbody').on('change', 'input[type="checkbox"]', function(){
        if(!this.checked){
            var el = $('#example-select-all').get(0);
            if(el && el.checked && ('indeterminate' in el)){
            el.indeterminate = true;
            }
        }
    });

    $(document).on('submit', '#submit_manual_process_ot', function(e) {
        e.preventDefault();

        let formData = new FormData(this),
            postUrl = $(this).attr('action');

        $.ajax({
            type: "POST",
            url: postUrl,
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',

            success: function (data) {
                console.log(data);
                if (data.success == false) {
                    $('#show-message').html(`
                        <h1 style="font-size: 16px" class="alert alert-danger">
                            ${data.message}
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        </h1>
                    `)
                } else {
                    $('#show-message').html(`
                        <h1 style="font-size: 16px" class="alert alert-success">
                            ${data.message}
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        </h1>
                    `)
                }
            }
        });
    })
});
</script>
