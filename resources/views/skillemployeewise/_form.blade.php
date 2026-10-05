{{ csrf_field() }}
	<div class="box-body">

		<div class="row" >
      <div class="col-lg-6 col-md-6 col-sm-12">
        <div class="form-group has-feedback {{ $errors->has('employee_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
          <label class="col-lg-3 control-label">Employee Name</label>
          <div class="col-lg-9">
            <select style="width: 100%;" class="form-control select2" id="employee_name" name="hrm_employee_id" required autofocus >
            </select>
            <input class="hidden" type="text" id="employee_id">
          </div>
        </div>

        <div class="col-lg-12 col-md-12 col-xs-12" style="padding: 27px;">
          <table id="chk-box-table" class="table table-bordered table-hover">
            <thead>
              <tr style="background-color: #199ddb;">
                <th style="text-align: center; width:20%;">Skill</th>
                <th style="text-align: center; width:20%;">Check Box</th>
                <th style="text-align: center; width:60%;">Description</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>

        <div class="row" style="padding-right: 30px;">
          <div class="form-group  col-lg-12 col-md-12 col-xs-12">
            <div class="col-lg-12">
              <input type="submit" id="btnSubmit" class="btn btn-success block btn-flat pull-right" value="Submit">
            </div>
          </div>
        </div>
      </div>
    </div>


	</div>

