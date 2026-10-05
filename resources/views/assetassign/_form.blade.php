{{ csrf_field() }}
<div class="box-body">
    <div class="row" style="margin-bottom: 20px">
        <div class="col-md-6">
            <div style="margin-left: 30px">
                <label>
                    <input type="radio" name="choose" value="employee" {{ $form_type === 'edit' ? ($emp ? 'checked' : null) : 'checked' }} data-id="choose_employee"> Employee
                </label>

                <label>
                    <input type="radio" name="choose" value="department" {{ $form_type === 'edit' ? (! $emp ? 'checked' : null) : null }} data-id="choose_department"> Deparment
                </label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div id="choose_employee" {{ $form_type === 'edit' ? ($emp ? 'style=display:block' : 'style=display:none') : 'style=display:none' }}>
                <div class="form-group has-feedback {{ $errors->has('employee_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                    <label class="col-lg-3 control-label">Employee Name</label>
                    <div class="col-lg-9">
                        <select style="width: 100%;" class="form-control select2" id="employee_name" name="hrm_employee_id">
                            @if($form_type === 'edit' && $emp)
                                <option value="{{$data[0]->employee_id}}">{{$data[0]->employee_name}} </option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group  col-lg-12 col-md-12 col-xs-12">
                    <label class="col-lg-3 control-label">Department</label>
                    <div class="col-lg-9">
                        <input type="text" class="form-control" id="department" value="{{ old('department',$data[0]->department??null) }}" readonly>
                    </div>
                </div>
                <div class="form-group  col-lg-12 col-md-12 col-xs-12">
                    <label class="col-lg-3 control-label">Designation</label>
                    <div class="col-lg-9">
                        <input type="text" class="form-control" id="designation" value="{{ old('designation',$data[0]->designation??null) }}" readonly>
                    </div>
                </div>
            </div>

            <div id="choose_department" {{ $form_type === 'edit' ? (! $emp ? 'style=display:block' : 'style=display:none') : 'style=display:none' }}>
                <div class="form-group has-feedback {{ $errors->has('employee_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                    <label class="col-lg-3 control-label">Department</label>
                    <div class="col-lg-9">
                        <select style="width: 100%;" class="form-control select2" id="hrm_depertments_id" name="hrm_depertments_id">
                            @if($form_type === 'edit' && ! $emp)
                                <option value="{{$data[0]->hrm_depertment_id}}">{{$data[0]->depertment_name}} </option>
                            @endif
                        </select>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group has-feedback {{ $errors->has('employee_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                <label class="col-lg-3 control-label">Assign Date </label>
                <div class="col-md-9">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control pull-right" id="assign_date" name="assign_date" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('assign_date') }}" required readonly>
                    </div>
                </div>
            </div>

            <div class="form-group has-feedback {{ $errors->has('employee_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
                <label class="col-lg-3 control-label">Asset</label>
                <div class="col-lg-9">
                    <select style="width: 100%;" class="form-control select2" id="asset_id" name="product_id" required autofocus >
                        @if($form_type == 'edit')
                            <option value="{{$data[0]->hrm_asset_id}}">{{$data[0]->asset}} </option>
                        @endif
                    </select>
                    @if($form_type == 'create')
                        <input class="hidden" type="text" id="asset_store_ledger_id" name="asset_store_ledger_id">
                        <input class="hidden" type="text" id="product_id" name="product_id">
                    @endif
                    @if($form_type == 'edit')
                        <input class="hidden" type="text" id="asset_store_ledger_id" name="asset_store_ledger_id" value={{$data[0]->ledger_id}} >
                        <input class="hidden" type="text" id="product_id" name="product_id" value={{$data[0]->hrm_asset_id}}>
                    @endif
                </div>
            </div>

            <div class="form-group  col-lg-12 col-md-12 col-xs-12">
                <label class="col-lg-3 control-label">Price</label>
                <div class="col-lg-9">
                    <input type="text" class="form-control" id="price" name="price" value="{{ old('price',$data[0]->price??null) }}" readonly>
                </div>
            </div>

            <div class="form-group  col-lg-12 col-md-12 col-xs-12">
                <label class="col-lg-3 control-label">Available Quantity</label>
                <div class="col-lg-9">
                    <input type="text" class="form-control" id="current_stock" name="current_stock" value="{{ old('current_stock',$stock[0]->current_stock??null) }}" readonly>
                </div>
            </div>

            <div class="form-group  col-lg-12 col-md-12 col-xs-12">
                <label class="col-lg-3 control-label">Assign Quantity</label>
                <div class="col-lg-9">
                    <input type="number" class="form-control" id="qty_stockout" name="qty_stockout" value="{{ old('qty_stockout', $data[0]->qty_stockout??null) }}" required>
                </div>
            </div>


            <div class="form-group  col-lg-12 col-md-12 col-xs-12">
                <label class="col-lg-3 control-label">Serial</label>
                <div class="col-lg-9">
                    <input type="text" class="form-control" id="serial_no" name="serial_no" value="{{ old('serial_no',$data[0]->serial_no??null) }}" readonly>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group  col-lg-12 col-md-12 col-xs-12">
                <div class="col-lg-12">
                    <input type="submit" id="btnSubmit" class="btn btn-success block btn-flat pull-right" value="Submit">
                </div>
            </div>
        </div>
    </div>
</div>
