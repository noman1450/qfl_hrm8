<table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>
                <input type="checkbox" id="checked_all">
            </th>
            <th style="width: 22%">Employee Name</th>
            <th style="width: 5%">Unique Code</th>
            <th style="width: 15%">Designation</th>
            <th style="width: 13%">Department</th>
            <th style="width: 12%">Joining</th>
            <th style="width: 10%">Contact</th>
            <th style="width: 13%">Job Placement</th>
            <th style="width: 13%">Reporting Boss</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataset as $data)
        <tr>
            <td>
                <input type="checkbox" class="checkbox-item" id="checked_all" name="employee_ids[]" value="{{ $data->id }}">
            </td>
            <td>
                <a target="_blank" href="{{ URL::to('/') }}/employeeinfo/{{ $data->id }}">{{ $data->employee_name }}</a>
            </td>
            <td>{{ $data->Unique_Code }}</td>
            <td>{{ $data->designation_name }}</td>
            <td>{{ $data->depertment_name }}</td>
            <td>{{ $data->joining_date }}</td>
            <td>{{ $data->contact_number }}</td>
            <td>{{ $data->location_name }}</td>
            <td>{{ $data->manage_by_name }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<script>
    $('#checked_all').on('change', function() {
        console.log('checked');
        var isChecked = $(this).is(':checked');
        $('.checkbox-item').prop('checked', isChecked);
    });

    // If any checkbox is unchecked, uncheck the "Select All" checkbox
    $('.checkbox-item').on('change', function() {
        if (!$(this).is(':checked')) {
            $('#checked_all').prop('checked', false);
        }
        // If all checkboxes are checked, check the "Select All" checkbox
        if ($('.checkbox-item:checked').length === $('.checkbox-item').length) {
            $('#checked_all').prop('checked', true);
        }
    });
</script>
