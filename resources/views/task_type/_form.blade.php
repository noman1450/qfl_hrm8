@if (session('message'))
    <div class="alert alert-success">
        {{ session('message') }}

        <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
    </div>
@endif

<form action="{{ route('employee_task_management.assign_task', encrypt($task->id)) }}" method="post">
    @csrf

    <div class="box-body" style="padding-left: 20px;padding-right: 20px">
        <div class="row">
            <div class="col-xs-8">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Assign Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right" id="assign_date" name="assign_date" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('process_date') }}" required readonly>
                            </div>

                            @error('assign_date')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Employee <span class="text-danger">*</span></label>
                            <select name="hrm_employee_id" id="hrm_employee_id" class="form-control">
                            </select>

                            @error('hrm_employee_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Priority <span class="text-danger">*</span></label>
                            <select name="priority" id="priority" class="form-control">
                                <option>Low</option>
                                <option>Medium</option>
                                <option>High</option>
                            </select>

                            @error('hrm_employee_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box-footer" style="padding-right: 0">
            <button type="submit" class="btn btn-primary pull-right" id="submitButton">Submit</button>
        </div>
    </div>
    <!-- /.box-body -->
</form>

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>

<script>
$(document).ready(function($) {
    $('#assign_date').datepicker({
        autoclose: true,
        // endDate: '+0d',
    });

    $('#hrm_employee_id').select2({
    	placeholder: 'Enter an Employee Name',
    	allowClear: true,
    		ajax: {
		        dataType: 'json',
		        url: "{{URL::to('/')}}/join_employee_list",
		        delay: 250,
    			data: function(params) {
                    return {
                        term: params.term
                    }
    			},

		        processResults: function (data, params) {
		            params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
		        },

		        cache: true
    		}
        });
    });
</script>
@endsection
