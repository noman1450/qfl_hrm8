<table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
    <thead>
        @foreach ($columns as $column)
            @if ($column === 'hrm_shift_id' || $column === 'hrm_probation_period_id')
                @continue
            @else
                <th>{{ str_replace(' ', '', ucwords(str_replace('_', ' ', $column))) }}</th>
            @endif
        @endforeach
    </thead>
    <tbody>
        @foreach ($employees as $employee)
            <tr>
                @foreach($columns as $field)
                    @if ($field === 'hrm_shift_id' || $field === 'hrm_probation_period_id')
                        @continue
                    @else
                        <td>{{ $employee->$field }}</td>
                    @endif
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
