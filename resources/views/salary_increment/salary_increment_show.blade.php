@if ($increment)
    <div class="row">
        <div class="col-xs-6">
            <b>Note:</b> {{ $increment->note }}
        </div>
        <div class="col-xs-6">
            <b>Effective Date:</b> {{ $increment->effective_date }}
        </div>
        <div class="col-xs-6">
            <b>Location:</b> {{ $increment->location_name }}
        </div>
        <div class="col-xs-6">
            <b>Total Employee:</b>
            <span class="label label-primary">{{ $total_employee }}</span>
        </div>
    </div>
    <div class="row" style="margin-top: 10px;">
        <div class="col-xs-12 text-right">
            <a href="{{ URL::to('/') }}/export_salaryincrement/{{ $increment->id }}"
            class="btn btn-success btn-sm btn-flat">
            <span class="glyphicon glyphicon-save"></span> Export Excel
            </a>
        </div>
    </div>
    <hr>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Employee Name</th>
                <th>Joining Date</th>
                <th>Designation</th>
                <th>Salary Amount</th>
                <th>Increase Amount</th>
                <th>New Salary</th>
                <th>Grade Name</th>
                <th>Last Increment Note</th>
            </tr>
        </thead>
        <tbody>
            @forelse($details as $key => $row)
                <tr>
                    <td>{{ $row->employee_name }}</td>
                    <td>{{ $row->joining_date }}</td>
                    <td>{{ $row->designation_name }}</td>
                    <td>{{ $row->salary_amount }}</td>
                    <td>{{ $row->increase_amount }}</td>
                    <td>{{ $row->new_salary_amount }}</td>
                    <td>{{ $row->grade_name }}</td>
                    <td>{{ $row->notes }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No data found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@else
    <div class="alert alert-warning">Data not found.</div>
@endif
