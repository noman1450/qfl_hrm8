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
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Assign Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control" id="assign_date" name="assign_date" readonly>
                            </div>

                            @error('assign_date')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Due Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control" id="due_date" name="due_date" readonly>
                            </div>

                            @error('due_date')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Employee <span class="text-danger">*</span></label>
                            <select name="hrm_employee_id" id="hrm_employee_id" class="form-control">
                            </select>

                            @error('hrm_employee_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
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
