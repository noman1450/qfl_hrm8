@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@endsection

@section('content')
<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">Leave Requests Pending Approval</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>

    @if ($errors->any())
        <div class="row" style="padding: 0 10px">
            <div class="col-xs-12">
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

  <div class="col-lg-3 col-md-3 col-xs-12 form-group" >
    <select  class="form-control col-lg-12" id="location" name="location" style="width: 100%;">
        @if(isset($hrm_location))
            <option value="{{ $hrm_location->id }}"  selected>{{ $hrm_location->location_name }}</option>
        @endif
    </select>
  </div>

  <div class="col-lg-3 col-md-3 col-xs-12 form-group" >
    <select style="width: 100%;" class="form-control select2" id="employee_leave_type" name="employee_leave_type">
    </select>
  </div>

  <div class="col-lg-3 col-md-3 col-xs-12 form-group" >
    <select style="width: 100%;" class="form-control select2" id="employee_name" name="employee_name">
    </select>
  </div>

  <div class="col-lg-2 col-md-3 col-xs-12 form-group" >
    <select style="width: 100%;" class="form-control select2" id="payment_mode" name="payment_mode">
        <option value="">Payment Mode (All)</option>
        <option value="1">With Pay</option>
        <option value="2">Without Pay</option>
    </select>
  </div>




    {{-- Start Modal --}}
    <div class="modal fade" id="modal_form" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="border-bottom: 0px;height: 50px;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">Leave Approve Modal</h3>
                </div>

                <div class="modal-body">
                    <form action="{{ url('/leaveapprove') }}" method="post" style="margin: 0;">
                        @csrf

                        <div class="panel" style="border: 1px solid #00000012; box-shadow: none;">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        Name
                                    </div>
                                    <div class="col-md-8">
                                        <div style="display: flex; align-items: center; column-gap: 20px;">
                                            <img src="" alt="" style="height: 50px; width: 50px; border-radius: 9999px;" id="employeeImage">

                                            <div style="display: flex; flex-direction: column; row-gap: 5px;">
                                                <strong style="font-size: 14px" id="employee_name"></strong>
                                                <span style="font-size: 13px" id="category_name"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr style="margin-top: 10px; margin-bottom: 10px;">

                                <div class="row">
                                    <div class="col-md-4">
                                        Department
                                    </div>
                                    <div class="col-md-8">
                                        <strong style="font-size: 14px" id="department_name"></strong>
                                    </div>
                                </div>

                                <hr style="margin-top: 10px; margin-bottom: 10px;">

                                <div class="row">
                                    <div class="col-md-4">
                                        Designation
                                    </div>
                                    <div class="col-md-8">
                                        <strong style="font-size: 14px" id="designation_name"></strong>
                                    </div>
                                </div>

                                <hr style="margin-top: 10px; margin-bottom: 10px;">

                                <div class="row">
                                    <div class="col-md-4">
                                        Leave Type
                                    </div>
                                    <div class="col-md-8">
                                        <strong style="font-size: 14px" id="leave_type"></strong>
                                    </div>
                                </div>

                                <hr style="margin-top: 10px; margin-bottom: 10px;">

                                <div class="row">
                                    <div class="col-md-4">
                                        Leave Days
                                    </div>
                                    <div class="col-md-8">
                                        <div style="display: flex; align-items: flex-start; flex-direction: column; gap: 10px; flex-wrap: wrap;">
                                            <strong style="font-size: 14px" id="leave_days"></strong>

                                            <span>From <code id="date_from"></code> To <code id="date_to"></code></span>
                                        </div>
                                    </div>
                                </div>

                                <hr style="margin-top: 10px; margin-bottom: 10px;">

                                <div class="row">
                                    <div class="col-md-4">
                                        Payment Mode
                                    </div>
                                    <div class="col-md-8">
                                        <strong style="font-size: 14px" id="payment_mode"></strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="forward">Forward</label>
                                    <select id="forward" class="form-control" name="forward" required>
                                        <option value="2">Final Approve</option>
                                        <option value="1">Forward</option>
                                    </select>
                                </div>

                                <div class="forward_class">
                                    <label for="forward_employee_name">Approve By(Forward)</label>
                                    <select style="width: 100%;" id="forward_employee_name" name="forward_employee_name">
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="comment">Comment</label>
                                    <textarea class="form-control" placeholder="Write Comment" id="comment" name="comment" rows="5"></textarea>
                                </div>
                            </div>

                            <input type="text" id="hrm_leave_application_id" name="hrm_leave_application_id" hidden>
                            <input type="text" id="hrm_leave_approve_id" name="hrm_leave_approve_id" hidden>
                        </div>
                        @can('LeaveAprovalAction')
                            <div class="modal-footer" style="padding-right: 0;">
                                <button type="submit" class="btn btn-sm btn-success" id="action" name="action" value="1">Accept</button>
                                <button type="submit" class="btn btn-sm btn-danger" id="reject" name="action" value="2">Reject</button>
                                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        @endcan
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- End Modal --}}


    <div class="box-body">
        <form action="{{ url('multiple-approve') }}" method="post" style="margin-bottom: 0">
            @csrf
            <div class="row">
                <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">
                    <table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr style="font-size: 14px;">
                                <th style="width: 5%">
                                    <input type="checkbox" id="checkAll" />_All
                                </th>
                                <!-- <th></th> -->
                                <th>Employee Name</th>
                                <th>Joining Date</th>
                                <th>Confirm Date</th>
                                <th>Location Name</th>
                                <th>Leave Type</th>
                                <th>Date From</th>
                                <th>Date To</th>
                                <th>Pay Mode</th>
                                <th>Days</th>
                                <th>Comment</th>
                                <th>Apply To</th>
                                <th>Print</th>
                                <th style="width: 5%">Action</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 14px;">
                        </tbody>
                    </table>
                </div>
                @can('LeaveAprovalAction')
                <div class="col-xs-12">
                    <div style="display:flex;align-items:center;justify-content:center;gap:15px;">
                        <button type="submit" onclick="return confirm('Are you sure to approve this..?')" class="btn btn-sm btn-success" id="action" name="action" value="1">Accept</button>
                        <button type="submit" onclick="return confirm('Are you sure to reject this..?')" class="btn btn-sm btn-danger" id="reject" name="action" value="2">Reject</button>
                    </div>
                </div>
                 @endcan
            </div>
        </form>
    </div>
</div>
@endsection


@section('script')
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('bootstrap/js/bootstrap.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script>

$(document).ready(function($) {
    let path = "{{ url('/') }}";


  dataLoad = function(){
                $.ajax({
                    type: 'POST',
                    url: "{{URL::to('/')}}/waitingleave_approve_list",
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    dataType: 'json',
                    data:   {
                        location: $("#location").val(),
                        employee_id: $("#employee_name").val(),
                        employee_leave_type_id: $("#employee_leave_type").val(),
                        payment_mode: $("#payment_mode").val(),
                    },

                    success: function(data) {
                        var dataSet = data.data;
                        table = $('#list_table').DataTable({
                            destroy:    true,
                            paging:     false,
                            searching:  true,
                            ordering:   true,
                            scrollX: false,
                            scrollCollapse: true,
                            scrollY: '100vh',
                            bInfo:      true,
                            "data":     dataSet,
                            "columns": [
                                { "data": "Checkbox",
                                    "mRender": function (data, type, full) {
                                        return `
                                            <input type="checkbox" class="row-check" name="leave_app_id[]" value="${full.hrm_leave_application_id}" />
                                            <input type="checkbox" class="leave_approve_id" name="leave_approve_id[]" value="${full.hrm_leave_approve_id}" hidden />
                                        `;
                                    },
                                    orderable: false, searchable: false
                                },
                                // { "data": "Link",
                                //     "mRender": function (data, type, full) {
                                //         return '<img src="{{asset('employee_image')}}/'+full.Images+'" style="height:30px; width:30px; border-radius: 30px;"/>';
                                //     }
                                // },
                                // { "data": "Link",
                                //     "mRender": function (data, type, full) {
                                //         return '<a target="_blank" href="{{URL::to('/')}}/employeeinfo/'+full.id+'">'+full.employee_name+'</a>';
                                //     }
                                // },
                                { "data": "employee_name" },
                                { "data": "joining_date" },
                                { "data": "confirmation_date" },
                                { "data": "location_name" },
                                { "data": "leave_type" },
                                { "data": "date_from" },
                                { "data": "date_to" },
                                { "data": "payment_mode" },
                                { "data": "days" },
                                { "data": "comment" },
                                { "data": "apply_to" },
                                { "data": "Link",
                                    "mRender": function (data, type, full) {
                                        return '<a href="{{URL::to('/')}}/print_leave_form/'+full.hrm_leave_application_id+'"  target="_blank" class="btn btn-info btn-sm btn-flat"><span class="glyphicon glyphicon-print"></span> Print</a>';
                                    }
                                },
                                { "data": "Link",
                                    "mRender": function (data, type, full) {leave_type
                                        return `<a
                                            data-hrm_leave_approve_id="${full.hrm_leave_approve_id}"
                                            data-hrm_leave_application_id="${full.hrm_leave_application_id}"
                                            data-employee_name="${full.employee_name}"
                                            data-depertment_name="${full.depertment_name}"
                                            data-leave_type="${full.leave_type}"
                                            data-days="${full.days}"
                                            data-image="${path}/employee_image/${full.Images}"
                                            data-designation="${full.designation_name}"
                                            data-category="${full.category_name ?? 'N/A'}"
                                            data-date_from="${full.date_from}"
                                            data-date_to="${full.date_to}"
                                            data-payment_mode="${full.payment_mode}"
                                            class="btn btn-primary btn-single btn-sm showme"
                                        >Action</a>`;
                                    }
                                },
                            ],

                            "order": [[1,'asc']]
                        });
                    }
                });
    };


    dataLoad();

    $('#list_table').on('click', '.showme', function (e) {
        $('#employee_name').text($(this).data('employee_name'));
        $('#employeeImage').attr('src', $(this).data('image'));

        $('#department_name').text($(this).data('depertment_name'));
        $('#designation_name').text($(this).data('designation'));
        $('#category_name').text($(this).data('category'));

        $('#leave_type').text($(this).data('leave_type'));
        $('#date_from').text($(this).data('date_from'));
        $('#date_to').text($(this).data('date_to'));
        $('#payment_mode').text($(this).data('payment_mode'));

        $('#leave_days').html(`${$(this).data('days')} <small style="font-weight:500">day(s)</small>`);

        $('#hrm_leave_application_id').val($(this).data('hrm_leave_application_id'));
        $('#hrm_leave_approve_id').val($(this).data('hrm_leave_approve_id'));


        $('#modal_form').modal('show');
    });

    $('.forward_class').hide();

    if($("#forward").val() == 1) {
        $('.forward_class').show();
    } else {
        $('.forward_class').hide();
    }

    $('#forward').on('change', function() {
        if($("#forward").val() == 1) {
            $('.forward_class').show();
        } else {
            $('.forward_class').hide();
        }
    });

    $('#forward_employee_name').select2({
        placeholder: 'Enter Forward Person Name',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: "{{URL::to('/')}}/userlist",
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
                }
            },

            cache: true
        }
    });

    $(document).on('click', '#checkAll', function() {
        $('input.row-check, input.leave_approve_id').not(this).prop('checked', this.checked);
    })

    $(document).on('click', 'input.row-check', function() {
        $(this).siblings('input.leave_approve_id').prop('checked', this.checked);
    })

    $('#employee_name').select2({
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

    $('#employee_leave_type').select2({
            placeholder: 'Enter a Leave type',
            allowClear: true,
                ajax: {
                    dataType: 'json',
                    url: "{{URL::to('/')}}/employee_leave_type",
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

    $role= $('#location').select2({
          placeholder: 'Choose Location',
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

        $role.on('select2:select', function (e) {
            dataLoad();
        });

        $role.on('select2:unselect', function (e) {
            $('#location').val(null).trigger("change");
            dataLoad();
        });

        $(document).on('change','.select2',function(){
            dataLoad();
        })


});
</script>
@endsection
