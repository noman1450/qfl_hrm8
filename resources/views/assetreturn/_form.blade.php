{{ csrf_field() }}
<div class="box-body">
    <div class="row" style="margin-bottom: 20px">
        <div class="col-md-6">
            <div style="margin-left: 30px">
                <label>
                    <input type="radio" name="choose" value="employee" checked data-id="choose_employee"> Employee
                </label>

                <label>
                    <input type="radio" name="choose" value="department" data-id="choose_department"> Deparment
                </label>
            </div>
        </div>
    </div>

	<div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group has-feedback {{ $errors->has('employee_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                <label class="col-lg-3 control-label">Return Date </label>
                <div class="col-md-9">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control pull-right" id="return_date" name="return_date" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('return_date') }}" required readonly>
                    </div>
                </div>
            </div>

            <div class="form-group has-feedback {{ $errors->has('employee_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12" id="choose_employee" style="display:none">
                <label class="col-lg-3 control-label">Employee Name</label>
                <div class="col-lg-9">
                    <select style="width: 100%;" class="form-control select2" id="employee_name" name="hrm_employee_id">
                    </select>
                    <input class="hidden" type="text" id="employee_id">
                </div>
            </div>

            <div class="form-group has-feedback {{ $errors->has('employee_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12" id="choose_department" style="display:none">
                <label class="col-lg-3 control-label">Department</label>
                <div class="col-lg-9">
                    <select style="width: 100%;" class="form-control select2" id="hrm_depertments_id" name="hrm_depertments_id">
                    </select>
                </div>
            </div>

            <div class="form-group has-feedback {{ $errors->has('employee_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                <label class="col-lg-3 control-label">Asset</label>
                <div class="col-lg-9">
                    <select style="width: 100%;" class="form-control select2" id="asset_id" name="hrm_asset_assign_master_id" required>
                        @if($form_type == 'edit')
                            <option value="{{$data[0]->hrm_asset_id}}">{{$data[0]->asset}} </option>
                        @endif
                    </select>
                </div>
            </div>

            <div class="form-group has-feedback {{ $errors->has('employee_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                <label class="col-lg-3 control-label">Note</label>
                <div class="col-lg-9">
                    <input type="text" id="note" name="note" placeholder="Type Note..." class="form-control" required>
                </div>
            </div>

            <div class="form-group has-feedback {{ $errors->has('employee_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                <label class="col-lg-3 control-label">Return Type</label>
                <div class="col-lg-9">
                    <select style="width: 100%;" class="form-control select2" id="asset_return_type_id" name="asset_return_type_id" required>
                        @if($form_type == 'edit')
                            <option value="{{$data[0]->return_type_id}}">{{$data[0]->return_type_id}} </option>
                        @endif
                    </select>
                </div>
            </div>
        </div>

        <div class="row" style="padding-right: 30px;">
            <div class="form-group  col-lg-6 col-md-6 col-xs-12">
                <div class="col-lg-12">
                    <input type="submit" id="btnSubmit" class="btn btn-success block btn-flat pull-right" value="Submit">
                </div>
            </div>
        </div>
    </div>
</div>

