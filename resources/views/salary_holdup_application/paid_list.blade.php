@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@endsection

@section('content')
<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">Hold Salary Paid List</h3>
    </div>

    <div class="box-body">
        <table id="paid_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Holdup Type</th>
                    <th>Month</th>
                    <th>Reason</th>
                    <th>Paid By</th>
                    <th>Paid Datetime</th>

                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('script')
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script>
$(document).ready(function() {
    $('#paid_table').DataTable({
        ajax: {
            url: "{{ url('get-holdup-paid-list-data') }}",
            type: "POST",
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        },
        columns: [
            { data: 'employee_name' },
            { data: 'holdup_types_name' },
            { data: 'date' },
            { data: 'note' },
            { data: 'paid_by_username' },
            { data: 'paid_date' }
        ]
    });
});

</script>
@endsection

