<h5 style="font-weight: bold">Employee Name: {{ $autoShifts->employee_name }}</h5>
<h5>Designation: {{ $autoShifts->designation_name }}</h5>
<h5>Department: {{ $autoShifts->depertment_name }}</h5>
<br>
<form class="form-group" action="{{ route('auto_shift.store') }}" method="post">
    @csrf

    <input type="hidden" name="employee_id" value="{{ $autoShifts->id }}">
    <input type="hidden" name="job_info_id" value="{{ $autoShifts->job_info_id }}">
    <div class="text-black text-bold m-0">Available Shifts</div>
    @foreach($hrm_shifts as $hrm_shift)
        <div class="d-block">
            <input type="checkbox" name="shifts[]"
                   @foreach(explode(',', $autoShifts->shift_id) as $shift)
                       {{ $hrm_shift->id == $shift ? 'checked' : '' }}
                   @endforeach
                   value="{{ $hrm_shift->id }}"> {{ $hrm_shift->shift_name }}
        </div>
    @endforeach

    <button type="submit" class="btn-primary pull-right" >Submit</button>
</form>
