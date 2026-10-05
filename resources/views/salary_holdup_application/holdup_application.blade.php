<!-- designation_list -->
@extends('layouts.main')

<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@endsection

<!-- content -->
@section('content')
<div class="box box-default">
	<div class="box-header with-border">
        <h3 class="box-title">Holdup Application List</h3>

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
                    <a href="#pending" data-toggle="tab" class="statusId" data-status="2">
                        Pending List
                    </a>
                </li>

                <li class="">
                    <a href="#approved" data-toggle="tab" class="statusId" data-status="1">
                        Approved List
                    </a>
                </li>
                <li class="">
                    <a href="#rejected" data-toggle="tab" class="statusId" data-status="0">
                        Rejected List
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="pending">
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title">Application Pending List</h3>
                            <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
                                <a href="{{ URL::to('holdup_application/create')}}"><input type="button" value="Create New" class="btn-success btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold;"></a>
                            </div>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>


                        <div class="box-body">
                       <div class="row">
                         <div class="col-md-10" style="overflow: auto;">
                              <table id="holdup_application_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
                                  <thead>
                                      <tr>
                                          <th style="width: 20%">Employee Name</th>
                                          <th style="width: 15%">Holdup Types Name</th>
                                          <th style="width: 10%">Month</th>
                                          <th style="width: 30%">Reason</th>
                                          <th style="width: 30%">Approve Status</th>
                                          <th style="width: 10%">Actions</th>
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

                <div class="tab-pane" id="approved">
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title">Application Approved List</h3>
                            <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
                                <a href="{{ URL::to('holdup_application/create')}}"><input type="button" value="Create New" class="btn-success btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold;"></a>
                            </div>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>


                        <div class="box-body">
                       <div class="row">
                         <div class="col-md-10" style="overflow: auto;">
                              <table id="holdup_application_table_1" class="table table-bordered table-hover" cellspacing="0" width="100%">
                                  <thead>
                                      <tr>
                                          <th style="width: 20%">Employee Name</th>
                                          <th style="width: 15%">Holdup Types Name</th>
                                          <th style="width: 10%">Month</th>
                                          <th style="width: 30%">Reason</th>
                                          <th style="width: 30%">Approve Status</th>
                                          <th style="width: 10%">Actions</th>
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

                <div class="tab-pane" id="rejected">
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title">Application Rejected List</h3>
                            <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
                                <a href="{{ URL::to('holdup_application/create')}}"><input type="button" value="Create New" class="btn-success btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold;"></a>
                            </div>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>


                        <div class="box-body">
                       <div class="row">
                         <div class="col-md-10" style="overflow: auto;">
                              <table id="holdup_application_table_2" class="table table-bordered table-hover" cellspacing="0" width="100%">
                                  <thead>
                                      <tr>
                                          <th style="width: 20%">Employee Name</th>
                                          <th style="width: 15%">Holdup Types Name</th>
                                          <th style="width: 10%">Month</th>
                                          <th style="width: 30%">Reason</th>
                                          <th style="width: 30%">Approve Status</th>
                                          <th style="width: 10%">Actions</th>
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
	</div>
</div>


<!----Modal---->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Approve / Reject</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <input type="hidden" name="id" id="id">
        <div class="modal-body">
            <div class="form-group">
              <label for="month" class="col-form-label">Month:</label> <span id="month"></span>
            </div>
            <div class="form-group">
              <label for="employeeName" class="col-form-label">Employee Name:</label> <span id="employeeName"></span>
            </div>
            <div class="form-group">
              <label for="employeeName" class="col-form-label">Salary Holdup Type:</label> <span id="holdupType"></span>
            </div>
            <div class="form-group">
              <label for="employeeName" class="col-form-label">Reason:</label> <span id="reason"></span>
            </div>
            <div class="form-group">
              <label for="employeeName" class="col-form-label">Active Status:</label> <span id="status"></span>
            </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm btn-flat" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary btn-sm btn-flat actionbtn" data-val="approve">Approve</button>
          <button type="button" class="btn btn-danger btn-sm btn-flat actionbtn" data-val="reject">Reject</button>
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

<script>
$(document).ready(function($) {


    var status_id = $('.statusId').attr('data-status');
    $(document).on("click",".statusId", function () {
        var status_id = $(this).attr('data-status');

        if (status_id == 2) {
            loadTable(status_id, '#holdup_application_table');
        } else if (status_id == 1) {
            loadTable(status_id, '#holdup_application_table_1');
        } else if (status_id == 0) {
            loadTable(status_id, '#holdup_application_table_2');
        }

    })



    function loadTable(status_id, selectedTable) {
        $.ajax({
        type:   'POST',
        url :   "{{URL::to('/')}}/holdup_application_list",
        data: {
            status: status_id
        },
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
        dataType: 'json',
        success: function(data) {
            var dataSet = data.data;
            table = $(selectedTable).DataTable( {
              destroy:    true,
              paging:     true,
              searching:  true,
              ordering:   true,
              bInfo:      true,
              "data":     dataSet,

            "columns": [
              { "data": "employee_name" },
              { "data": "holdup_types_name" },
              { "data": "date" },
              { "data": "note" },
              { "data": "Link",
                "mRender": function (data, type, full) {
                    if (full.approved_status == 2) {
                        return '<span class="badge badge-primary" style="background-color:#fcc109;color:#000">Pending</span>';
                    } else if (full.approved_status == 1) {
                        return '<span class="badge badge-primary" style="background-color:green">Approved</span>';
                    } else if (full.approved_status == 0) {
                        return '<span class="badge badge-primary" style="background-color:red">Rejected</span>';
                    }
                }
              },
            {
                "data": "Link",
                "mRender": function (data, type, full) {
                    if (full.approved_status == 2) {
                        return `
                        <div style="display:flex">
                        <button type="button" id="action" class="btn btn-primary btn-sm btn-flat"
                                data-toggle="modal" data-target="#exampleModal" data-id="${full.id}">
                            Action
                        </button>
                        <a href="{{URL::to('/')}}/holdup_application/${full.id}/edit" class="btn btn-warning btn-sm btn-flat" style="margin:0 3px">
                            <span class="glyphicon glyphicon-edit"></span> Edit
                        </a>
                        <a href="{{URL::to('/')}}/holdup_application/${full.id}/cancel"
                            onclick="return confirm('Do you really want to DELETE?');"
                            class="btn btn-danger btn-sm btn-flat">
                            <span class="glyphicon glyphicon-trash"></span> Delete
                        </a></div>`;
                    } else {
                        return '<a class="btn btn-danger btn-flat btn-sm" disabled>No Action </a>';
                    }
                }
            }

            ],
            "order": [[0,'asc']]
            });
        }
      });
    }
    loadTable(status_id, '#holdup_application_table');


    $(document).on("click", "#action", function () {
        var id = $(this).attr('data-id');

        $.ajax({
            type: "POST",
            url: "{{URL::to('/')}}/holdup_application_get_data",
            data: {
                id: id
            },
            headers:{
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            dataType: "json",
            success: function (response) {
                // console.log(response);
                $("#id").val(response.id);
                $("#month").text(response.date);
                $("#employeeName").text(response.employee_name);
                $("#holdupType").text(response.holdup_types_name);
                $("#reason").text(response.note);

                if (response.approved_status == 1) {
                    $("#status").html('<span class="badge bg-success" style="background-color:green">Approved</span>');
                } else if (response.approved_status == 2) {
                    $("#status").html('<span class="badge bg-warning" style="background-color:#fcc109;color:#000">Pending</span>');
                } else if(response.approved_status == 0) {
                    $("#status").html('<span class="badge bg-danger" style="background-color:red">Rejected</span>');
                }

            }
        });

    });

    $(document).on("click", ".actionbtn", function () {
        var actionType = $(this).attr('data-val');
        var id = $("#id").val();

        if (confirm('Are you sure to '+actionType+' the application?')) {
            $.ajax({
                type: "POST",
                url: "{{URL::to('/')}}/holdup_application_action",
                data: {
                    action_type: actionType,
                    id: id
                },
                headers:{
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                dataType: "json",
                success: function (response) {
                    console.log(response);

                    $("#exampleModal .close").click();

                    loadTable(status_id, '#holdup_application_table');

                }
            });
        }

    });

});

</script>

@endsection
