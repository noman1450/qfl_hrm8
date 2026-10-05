<style>
    strong {
        font-size: 14px;
    }
</style>

<div class="row mb-5">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading" style="font-size: 18px">Employee Info</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
                        Name
                    </div>
                    <div class="col-md-8">
                        <div style="display: flex; align-items: center; column-gap: 20px;">
                            <img src="{{ asset('employee_image/'.$employee->Images) }}" alt="" style="height: 40px; width: 40px; border-radius: 9999px;">

                            <div style="display: flex; flex-direction: column; row-gap: 5px;">
                                <strong>{{ $employee->employee_name }}</strong>
                                <span>{{ $employee->category_name }}</span>
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
                        <strong>{{ $employee->depertment_name }}</strong>
                    </div>
                </div>

                <hr style="margin-top: 10px; margin-bottom: 10px;">

                <div class="row">
                    <div class="col-md-4">
                        Designation
                    </div>
                    <div class="col-md-8">
                        <strong>{{ $employee->designation_name }}</strong>
                    </div>
                </div>

                <hr style="margin-top: 10px; margin-bottom: 10px;">

                <div class="row">
                    <div class="col-md-4">
                        Mobile
                    </div>
                    <div class="col-md-8">
                        <strong>{{ $employee->contact_number }}</strong>
                    </div>
                </div>

                <hr style="margin-top: 10px; margin-bottom: 10px;">

                <div class="row">
                    <div class="col-md-4">
                        Email
                    </div>
                    <div class="col-md-8">
                        <strong>{{ $employee->email }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading" style="font-size: 18px">Summery</div>

            <div class="panel-body">
                @foreach ($monthly_summary as $summary)
                    <div class="row">
                        <div class="col-md-4">
                            {{ $summary->attendance_status }}
                        </div>
                        <div class="col-md-8">
                            <div style="display: flex; align-items: center; column-gap: 5px;">
                                {{-- <span style="height: 10px; width: 10px; border-radius: 9999px; background-color: #{{ $summary->color_code ?: '8b8888' }}"></span> --}}

                                <strong>{{ $summary->days }} days</strong>
                            </div>
                        </div>
                    </div>

                    @if (! $loop->last)
                        <hr style="margin-top: 10px; margin-bottom: 10px;">
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading" style="font-size: 18px">Details</div>

            <div class="panel-body">
                <div class="table-wrapper table-responsive">
                    <table id="dailyAttendanceDatatable" class="table table-hover" style="margin-bottom: 0">
                        <thead>
                            <tr>
                                <th class="text-center">Punch date</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">In Time</th>
                                <th class="text-center">Out Time</th>
                                <th class="text-center">Early Out</th>
                                <th class="text-center">Late Time</th>
                                <th class="text-center">Over Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($attendance_details as $attendance)
                                <tr>
                                    <td class="text-center">{{ date('d M, Y', strtotime($attendance->punche_date)) }}</td>
                                    <td class="text-center">
                                        {{ $attendance->attendance_status }}
                                    </td>
                                    <td class="text-center">
                                        {{ $attendance->in_time }}
                                    </td>
                                    <td class="text-center">
                                        {{ $attendance->out_time }}
                                    </td>
                                    <td class="text-center">{{ $attendance->early_out_time }}</td>
                                    <td class="text-center">{{ $attendance->late_time }}</td>
                                    <td class="text-center">{{ $attendance->overtime_time }}</td>
                                </tr>
                            @endforeach

                            <tr>
                                <th colspan="4"></th>
                                <th class="text-center">{{ $early_out_time }}</th>
                                <th class="text-center">{{ $late_time }}</th>
                                <th class="text-center">{{ $overtime_time }}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
