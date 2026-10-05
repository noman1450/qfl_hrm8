<!-- create_employee -->
<!-- create_marital_status -->
@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}"></script>
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables/jquery.dataTables.min.css')}}">


<style>
.kv-avatar .krajee-default.file-preview-frame,.kv-avatar .krajee-default.file-preview-frame:hover {
    margin: 0;
    padding: 0;
    border: none;
    box-shadow: none;
    text-align: center;
}
.kv-avatar {
    display: inline-block;
}
.kv-avatar .file-input {
    display: table-cell;
    width: 213px;
}
.kv-reqd {
    color: red;
    font-family: monospace;
    font-weight: normal;
}
.btn-secondary {
	margin-top: 5px;
}
.btn-file{
	margin-top: 5px;
}
</style>
@endsection


@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Edit Employee Holiday</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


    {!! Form::open(array('route' => array('holiday_process.update', $edit_data[0]->id), 'onkeypress'=> "return event.keyCode != 13;", 'files'=>true, 'id' => 'top-entrypanel-validation', 'method'=>'PUT')) !!}
    {{ csrf_field() }}


	<div class="box-body">
		<div class="row">


			<div class="col-lg-8 col-md-8 col-xs-12 personal-info">

		        <div class="form-group has-feedback {{ $errors->has('location_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
		            <label class="col-lg-3 control-label">Location Name</label>
		            <div class="col-lg-9">

						<select style="width: 100%;" class="form-control select2" id="location_name" name="location_name" required>
		                	<option value="{{$edit_data[0]->hrm_location_id}}">{{$edit_data[0]->location_name}}</option>

						</select>

			            @if ($errors->has('location_name'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('location_name') }}</strong>
			                </span>
			            @endif
		            </div>
		        </div>



		        <div class="form-group has-feedback {{ $errors->has('year') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
		            <label class="col-lg-3 control-label">Holiday Year</label>
		            <div class="col-lg-9">
						<select style="width: 100%;" class="form-control select2" id="year" name="year" required>
							@foreach ($leavetypeyear as $keys)
							<option value={{$keys->id}}>{{$keys->leave_year}}</option>
							@endforeach
						</select>

			            @if ($errors->has('year'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('year') }}</strong>
			                </span>
			            @endif
		            </div>
		        </div>


		        <div class="form-group has-feedback {{ $errors->has('holiday_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
		            <label class="col-lg-3 control-label">Holiday Name</label>
		            <div class="col-lg-9">

						<select style="width: 100%;" class="form-control select2" id="holiday_name" name="holiday_name" required>
		                	<option value="{{$edit_data[0]->hrm_holiday_id}}">{{$edit_data[0]->holiday_name}}</option>

						</select>

			            @if ($errors->has('holiday_name'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('holiday_name') }}</strong>
			                </span>
			            @endif
		            </div>
		        </div>

               <div class="form-group has-feedback {{ $errors->has('date_from') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
		            <label class="col-lg-3 control-label">Date From</label>
		            <div class="col-lg-9">
		            <div class="input-group date">
		                <div class="input-group-addon">
		                  <i class="fa fa-calendar"></i>
		                </div>

						  {{-- <input type="text" class="form-control" id="date_from" name="date_from" data-inputmask="'alias': 'dd-mm-yyyy'" data-mask  value="{{  date('d-m-Y', strtotime(str_replace('/', '-', $edit_data[0]->date_from ))) }}" > --}}
                          <input type="text" class="form-control pull-right" id="date_from" name="date_from" data-date-format="dd-mm-yyyy" value="{{ date('d-m-Y', strtotime(str_replace('/', '-', $edit_data[0]->date_from ))) }}" required readonly>


			            @if ($errors->has('date_from'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('date_from') }}</strong>
			                </span>
			            @endif
		            </div>
		            </div>
		        </div>

		         <div class="form-group has-feedback {{ $errors->has('date_to') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
		            <label class="col-lg-3 control-label">Date To</label>
		            <div class="col-lg-9">
		            <div class="input-group date">
		                <div class="input-group-addon">
		                  <i class="fa fa-calendar"></i>
		                </div>

						  {{-- <input type="text" class="form-control" id="date_to" name="date_to" data-inputmask="'alias': 'dd-mm-yyyy'" data-mask  value="{{  date('d-m-Y', strtotime(str_replace('/', '-', $edit_data[0]->date_to ))) }}" > --}}
                          <input type="text" class="form-control pull-right" id="date_to" name="date_to" data-date-format="dd-mm-yyyy" value="{{ date('d-m-Y', strtotime(str_replace('/', '-', $edit_data[0]->date_to ))) }}" required readonly>

			            @if ($errors->has('date_to'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('date_to') }}</strong>
			                </span>
			            @endif
		            </div>
		            </div>
		        </div>

		        <div class="form-group has-feedback {{ $errors->has('apply_type') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
		            <label class="col-lg-3 control-label">Apply Type</label>
		            <div class="col-lg-9">

						<select style="width: 100%;" class="form-control select2" id="apply_type" name="apply_type" required>

							@if($edit_data[0]->apply_type==1)
								<option value="1" selected>All Employee</option>
								<option value="2">Selected Employee</option>
							@else
								<option value="1">All Employee</option>
								<option value="2" selected>Selected Employee</option>
							@endif

						</select>

			            @if ($errors->has('apply_type'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('apply_type') }}</strong>
			                </span>
			            @endif
		            </div>
		        </div>
		</div>

        <div class="row col-lg-10 col-md-10 col-xs-12 selected_employee" >

          <div class="col-lg-3 col-md-3 col-xs-12 form-group" >
            <select class="form-control plant_name" id="plant_name" name="plant_name" style="width: 100%;" >
            </select>
          </div>
          <div class="col-lg-3 col-md-3 col-xs-12 form-group" >
            <select class="form-control" id="working_shift" name="working_shift" style="width: 100%;" >
            </select>
          </div>
          <div class="col-lg-3 col-md-3 col-xs-12 form-group" >
            <select class="form-control" id="department" name="department" style="width: 100%;" >
            </select>
          </div>

          <div class="col-lg-3 col-md-3 col-xs-12 form-group" >
            <select class="form-control" id="designation" name="designation" style="width: 100%;" >
            </select>
          </div>

          <div class="col-lg-3 col-md-3 col-xs-12 form-group" >
            <select class="form-control" id="section" name="section" style="width: 100%;" >
            </select>
          </div>

          <div class="col-lg-1 col-md-1 col-xs-12 form-group" >
            <input type="button" id="search" value="Search" class=" btn-sm block btn-flat btn"  style="float: right;">
          </div>

          <div class="form-group col-lg-12 col-md-12 col-xs-12">
            <table id="designation_list_table" class="table table-striped table-bordered cell-border"    width="100%">
              <thead >
                <tr >

                  <th style="width: 3%"><input name="select_all" value="1" id="example-select-all" type="checkbox" /></th>
                  <th style="width: 20%">Employee Name</th>
                  <th style="width: 15%">Department</th>
                  <th style="width: 15%">Designation</th>
                  <th style="width: 15%">Running Shift</th>
                </tr>
              </thead>
              <tbody>
              	@if($edit_data[0]->apply_type==2)
	              	@foreach($dtls_data as $key)
	              	<tr>
						<td>
							<input type="checkbox"  name="id[]" value="{{$key->id}}" checked>
						</td>
	              		<td>{{$key->employee_name}}</td>
	              		<td>{{$key->depertment_name}}</td>
	              		<td>{{$key->designation_name}}</td>
	              		<td>{{$key->shift_name}}</td>
	              	</tr>
	              	@endforeach
	             @endif
              </tbody>
            </table>
          </div>



        </div>

			<div class="form-group">
				<label class="col-md-3 control-label"></label>
				<div class="col-md-8">
					<input type="submit" class="btn btn-success block btn-flat" style="margin-left:428px;" value="Submit">
					<span></span>
					<!-- <input type="reset" class="btn block btn-flat btn-default" value="Cancel"> -->
				</div>
			</div>


	</div>


	{!! Form::close() !!}
</div>
@endsection


@section('script')
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>


<script src="{{asset('js/fileinput.js')}}"></script>
<script>
$(document).ready(function($) {

    $('#applied').datepicker({
      autoclose: true
    });
    $('#date_from').datepicker({
      autoclose: true
    });
    $('#date_to').datepicker({
      autoclose: true
    });


    $('.selected_employee').hide();


	if($("#apply_type").val() == 2){
	    $('.selected_employee').show();
	}else{
	    $('.selected_employee').hide();
	}

	$('#apply_type').on('change', function(){
		if($("#apply_type").val() == 2){
			$('.selected_employee').show();
		}else{
			$('.selected_employee').hide();
		}
	});


    $('#location_name').select2({
      placeholder: 'Enter a Location Name',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/location_list_data',
        delay: 250,
        data: function(params) {
          return {
            term: params.term
          }
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data
          };
        },
        cache: true
      }
    });


    $('#holiday_name').select2({
      placeholder: 'Enter a Holiday Name',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/holiday_list_data',
        delay: 250,
        data: function(params) {
          return {
            term: params.term
          }
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data
          };
        },
        cache: true
      }
    });


  $('#employee_leave_type').select2({
    	placeholder: 'Enter a Leave type',
    	allowClear: true,
    		ajax: {
		        dataType: 'json',
		        url: "{{URL::to('/')}}/employee_leave_type",
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

  $('#section').select2({
      placeholder: 'Enter Sub-department',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/section_list_data',
        delay: 250,
        data: function(params) {
          return {
            term: params.term
          }
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data
          };
        },
        cache: true
      }
    });

  $('#applytousername').select2({
    	placeholder: 'Enter Approved By Name',
    	allowClear: true,
    		ajax: {
		        dataType: 'json',
		        url: "{{URL::to('/')}}/userlist",
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

$('#department').select2({
      placeholder: 'Enter department',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/depertment_list_data',
        delay: 250,
        data: function(params) {
          return {
            term: params.term
          }
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data
          };
        },
        cache: true
      }
    });

    $('#designation').select2({
      placeholder: 'Enter designation',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/designation_list_data',
        delay: 250,
        data: function(params) {
          return {
            term: params.term
          }
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data
          };
        },
        cache: true
      }
    });

    $('#working_shift').select2({
      placeholder: 'Enter working shift',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/shift_list_data',
        delay: 250,
        data: function(params) {
          return {
            term: params.term
          }
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data
          };
        },
        cache: true
      }
    });

    $('#plant_name').select2({
      placeholder: 'Enter Plant Name',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/plantname_list_data',
        delay: 250,
        data: function(params) {
          return {
            term: params.term
          }
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data
          };
        },
        cache: true
      }
    });


    $("#search").click(function(){

      if ($("#location_name").val() == null){
        location_id=0;
      }else{
        location_id = $("#location_name").val();
      }

      if ($("#working_shift").val() == null){
        working_shift=0;
      }else{
        working_shift = $("#working_shift").val();
      }

      if ($("#department").val() == null){
        department_id = 0;
      }else{
        department_id = $("#department").val();
      }

      if ($("#designation").val() == null){
        designation_id = 0;
      }else{
        designation_id = $("#designation").val();
      }
      if ($("#plant_name").val() == null){
        plant_name = 0;
      }else{
        plant_name = $("#plant_name").val();
      }

      if ($("#section").val() == null){
        section_id = 0;
      }else{
        section_id = $("#section").val();
      }




      $.ajax({
        type:   'POST',
        url :   "{{URL::to('/')}}/holidayfor_selectedemployee",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
        data:   {
                   // punch_date: $("#process_date").val(),
                   location      : location_id,
                   department_id : department_id,
                   designation_id: designation_id,
                   working_shift_id: working_shift,
                   section_id    : section_id,
                   plant_name: plant_name,
                },
        dataType: 'json',
        success: function(data) {
          var dataSet = data.data;
            table = $('#designation_list_table').DataTable( {
              destroy:    true,
              paging:     false,
              searching:  true,
              ordering:   true,
              bInfo:      true,
              "data":     dataSet,
              "columns": [

              { "data": "checkbox",
                      "mRender": function (data, type, full) {
                      return '<input type="checkbox"  name="id[]" value="'+full.id+'">';
              }
              },
              { "data": "employee_name" },
              { "data": "depertment_name" },
              { "data": "designation_name" },
              { "data": "shift_name" },
              ],
              order: [ 1, 'asc' ]
            });
        }
      });
    });

   $('#example-select-all').on('click', function(){
      var rows = table.rows({ 'search': 'applied' }).nodes();
      $('input[type="checkbox"]', rows).prop('checked', this.checked);
   });


   $('#designation_list_table tbody').on('change', 'input[type="checkbox"]', function(){
      if(!this.checked){
         var el = $('#example-select-all').get(0);
         if(el && el.checked && ('indeterminate' in el)){
            el.indeterminate = true;
         }
      }
   });



});
</script>
@endsection
