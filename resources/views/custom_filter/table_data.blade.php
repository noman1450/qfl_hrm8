
<table class="table table-bordered table-hover" style="margin-bottom:0">
    <thead>
        <tr>
            @foreach($tableColumns as $field)
                @if ($field === 'id')
                    <th>#</th>
                @elseif ($field === 'valid' || $field === 'users_id' || $field === 'created_at' || $field === 'updated_at' || $field === 'hrm_location_id')
                    @continue
                @else
                    <th>{{ ucwords(str_replace('_', ' ', $field)) }}</th>
                @endif
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($tableData as $table)
            <tr>
                @foreach($tableColumns as $field)
                    @if ($field === 'id')
                        <td>
                            @isset($filterMasterDetails)
                                <input type="checkbox" name="ref_ids[]" {{ in_array($table->$field, $filterMasterDetails) ? 'checked' : null }} value="{{ $table->$field }}">
                            @else
                                <input type="checkbox" name="ref_ids[]" value="{{ $table->$field }}">
                            @endisset
                        </td>
                    @elseif ($field === 'valid' || $field === 'users_id' || $field === 'created_at' || $field === 'updated_at' || $field === 'hrm_location_id')
                        @continue
                    @else
                        <td>{{ $table->$field }}</td>
                    @endif
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
