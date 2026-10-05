@extends('layouts.main')

@section('title', 'Details')

@push('styles')
<style>
    strong {
        font-size: 14px;
    }

    .cancel-update-btns {
        display: none;
        align-items: center;
        gap: 5px;
        justify-content: flex-end;
    }

    @media (max-width: 768px) {
        .summery__div {
            margin-top: 10px;
        }
    }

    .card {
        border-radius: 10px;
        border: 1px solid #eaeaea;
    }

    .card-header {
        background: #f8f9fa;
        font-size: 15px;
        font-weight: 600;
    }

</style>
@endpush

@section('content')

<div class="box box-default" style="margin:10px; padding:10px">

    <!-- HEADER -->

    <div class="box-header with-border">
        <div style="display:flex !important; justify-content:space-between !important; align-items:center !important; width:100% !important;">

            <h3 style="margin:0;">
                Leave Year {{ $hrm_leave_years->leave_year ?? '' }}
            </h3>

            <a href="{{ route('emp_leave_balance.pdf', [$employee->id, $hrm_leave_years->id]) }}" class="btn btn-danger btn-sm" target="_blank">
                <i class="fa fa-print"></i> PDF
            </a>

        </div>
    </div>

    <div class="box-body px-3 pb-3">

        <!-- ================= ROW 1 ================= -->
        <div class="row g-3 mb-3">

            <!-- EMPLOYEE INFO -->
            <div class="col-md-6" style="border: 1px solid #eaeaea; padding: 10px">
                <div class="card shadow-sm ">
                    <h4 style="">Employee Info</h4>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-4">Name</div>
                            <div class="col-md-8">
                                <div class="d-flex align-items-center gap-3">

                                    <div class="d-flex flex-column">
                                        <strong>{{ $employee->employee_name ?? '' }}</strong>
                                    </div>

                                </div>
                            </div>
                        </div>


                        <hr style="margin: 8px 0">

                        <div class="row">
                            <div class="col-md-4">Employee Code</div>
                            <div class="col-md-8">
                                <strong>{{ $employee->employee_code ?? '' }}</strong>
                            </div>
                        </div>

                        <hr style="margin: 8px 0">

                        <div class="row">
                            <div class="col-md-4">Department</div>
                            <div class="col-md-8">
                                <strong>{{ $employee->depertment_name ?? '' }}</strong>
                            </div>
                        </div>

                        <hr style="margin: 8px 0">

                        <div class="row">
                            <div class="col-md-4">Designation</div>
                            <div class="col-md-8">
                                <strong>{{ $employee->designation_name ?? '' }}</strong>
                            </div>
                        </div>

                        <hr style="margin: 8px 0">

                        <div class="row">
                            <div class="col-md-4">Joining Date</div>
                            <div class="col-md-8">
                                <strong>{{ $employee->joining_date ?? '' }}</strong>
                            </div>
                        </div>
                        <hr style="margin: 8px 0">

                        <div class="row">
                            <div class="col-md-4">Confirmation Date</div>
                            <div class="col-md-8">
                                <strong>{{ $employee->confirmation_date ?? '' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SUMMARY -->
            <div class="col-md-6" id="reload-area" style="border : 1px solid #eaeaea; padding: 10px">
                <div class="card shadow-sm h-100" id="reload-card">

                    <h4>Leave Summary</h4>

                    <div class="card-body">

                        <div id="show-message"></div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Leave Name</th>
                                        <th>Balance</th>
                                        <th>Used</th>
                                        <th>Remaining</th>

                                    </tr>
                                </thead>

                                @php
                                    $totalLeaveBalance = 0;
                                    $totalLeaveUsed = 0;
                                    $totalLeaveRemaining = 0;
                                @endphp

                                <tbody>
                                    @foreach ($employeeLeaveDetails as $leave)
                                        <tr>
                                            <td>{{ $leave->leave_type }}</td>

                                            <td>{{ $leave->total_leave }}</td>

                                            <td>{{ $leave->used }}</td>
                                            <td>{{ $leave->balance }}</td>

                                        </tr>

                                        @php
                                            $totalLeaveBalance += $leave->total_leave;
                                            $totalLeaveUsed += $leave->used;
                                            $totalLeaveRemaining += $leave->balance;
                                        @endphp
                                    @endforeach
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <td><strong>Total</strong></td>
                                        <td><strong>{{ number_format($totalLeaveBalance, 2) }}</strong></td>
                                        <td><strong>{{ number_format($totalLeaveUsed, 2) }}</strong></td>
                                        <td><strong>{{ number_format($totalLeaveRemaining, 2) }}</strong></td>

                                    </tr>
                                </tfoot>

                            </table>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <!-- ================= ROW 2 ================= -->
        <div class="row " style="margin-top: 30px">

            <div class="col-12">
                <div class="card shadow-sm" style="border: 1px solid #eaeaea">

                    <h4 class="card-header" style="margin-left: 10px">Leave Details</h4>

                    <div class="card-body table-responsive">

                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date From</th>
                                    <th>Date To</th>
                                    <th>Duration</th>
                                    <th>Leave Name</th>
                                    <th>Comment</th>
                                    <th>Payment Mode</th>
                                    <th>Boss Comment</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($details as $dLeave)
                                    <tr>
                                        <td>{{ $dLeave->date_from }}</td>
                                        <td>{{ $dLeave->date_to }}</td>
                                        <td>{{ $dLeave->days }}</td>
                                        <td>{{ $dLeave->leave_type }}</td>
                                        <td>{{ $dLeave->comment }}</td>
                                        <td>{{ $dLeave->payment_mode }}</td>
                                        <td>{{ $dLeave->boss_note }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-danger">
                                            No data found!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>

                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

@endsection
