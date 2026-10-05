@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">


@endsection

@section('content')
<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">Hold Salary Unpaid List</h3>
    </div>

    <div class="box-body">
        <table id="unpaid_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Holdup Type</th>
                    <th>Month</th>
                    <th>Reason</th>
                    <th>Paid Status </th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

{{-- modal --}}
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
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
                    <label for="holdupType" class="col-form-label">Salary Holdup Type:</label> <span id="holdupType"></span>
                </div>
                <div class="form-group">
                    <label for="reason" class="col-form-label">Reason:</label> <span id="reason"></span>
                </div>
                <div class="form-group">
                    <label for="status" class="col-form-label">Active Status:</label> <span id="status"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm btn-flat" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btn-sm btn-flat actionbtn" data-val="approve">Paid</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>

<script>
$(document).ready(function() {
    var table = $('#unpaid_table').DataTable({
        ajax: {
            url: "{{ url('holdup-application-lists') }}",
            type: "POST",
            data: { status: 0 },
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        },
        columns: [
            { data: 'employee_name' },
            { data: 'holdup_types_name' },
            { data: 'date' },
            { data: 'note' },
            {
                data: 'is_paid',
                render: function(data) {
                    return data == 1 ? 'Paid' : 'Unpaid';
                }
            },
            {
                data: 'approved_status',
                render: function(data) {
                    return data == 1 ? 'Approved' : 'Pending';
                }
            },
            {
                data: 'id',
                render: function(data) {
                    return `<button class="btn btn-primary btn-sm actionDataBtn" data-id="${data}" data-toggle="modal" data-target="#exampleModal">Action</button>`;
                }
            }
        ]
    });

    $(document).on("click", ".actionDataBtn", function() {
    var id = $(this).data('id');

    $.ajax({
        url: "{{ url('holdup_application_get_data') }}",
        type: 'POST',
        data: {
            id: id,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            console.log(response);


            $("#employeeName").text(response.employee_name);
            $("#holdupType").text(response.holdup_types_name);
            $("#month").text(response.date);
            $("#reason").text(response.note);
            $("#status").text(response.approved_status);
            $("#id").val(response.id);
        }
    });
});

$('.actionbtn').on('click', function() {
    var id = $('#id').val();
    var action = $(this).data('val');

    $.ajax({
        url: "{{ url('holdup_paid_action') }}",
        type: 'POST',
        data: {
            id: id,
            action: action,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            }
        }
    });
});

});
</script>

@endsection
