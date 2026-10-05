<link rel="stylesheet" href="{{ asset('plugins/datepicker/datepicker3.css') }}">

<h5 style="font-weight: bold">Employee Name: {{ $employee->employee_name }}</h5>
<h5>Designation: {{ $employee->designation_name }}</h5>
<h5>Department: {{ $employee->depertment_name }}</h5>
<br>
<form action="{{ route('weekend_config.store') }}" method="post">
    @csrf

    <input type="hidden" name="employee_id" value="{{ $employee->id }}">
    <input type="hidden" name="hrm_location_id" value="{{ $employee->hrm_location_id }}">

    <div class="row">
        <div class="col-md-3">
            <div class="text-black text-bold m-0">Weekend</div>
            @foreach($days as $day)
                <div class="d-block">
                    <input type="checkbox" name="weekends[]"
                           @foreach(explode(',', $employee->hrm_days_name_id) as $hrm_days_name_id)
                               {{ $day->id == $hrm_days_name_id ? 'checked' : '' }}
                           @endforeach
                           value="{{ $day->id }}"> {{ $day->days_name }}
                </div>
            @endforeach
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label for="start_date">Start Date</label>
                <div class="input-group date">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control pull-right" id="start_date" name="start_date"
                           data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}"
                           value="{{ old('start_date') }}" required readonly>
                </div>
            </div>
        </div>

        <div class="col-xs-12">
            <button type="submit" class="btn-primary pull-right">Submit</button>
        </div>
    </div>
</form>

<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>

<script>
    $(document).ready(function ($) {
        $('#start_date').datepicker({
            autoclose: true
        });
    });

</script>
