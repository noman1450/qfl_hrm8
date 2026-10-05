<!-- create_employee -->
@extends('layouts.main')
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<style>
img{
width: 210px;
height: 210px;
}
input[type=file]{
	padding:10px;
	/*background:#2d2d2d;*/
}
</style>
@endsection
@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Edit Employee Information</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>

    <div class="col-lg-12 col-md-12 col-xs-12">

	    <div class="col-lg-6 col-md-6 col-xs-12">
	       <div id="alert-message"></div>
	       <!-- <div id="alert-success1"></div> -->
	    </div>

    </div>


	{!! Form::open(array('route' => array('employee.update', $employee->id), 'onkeypress'=> "return event.keyCode != 13;", 'files'=>true, 'id' => 'frm_employee', 'method'=>'PUT')) !!}
	{{ csrf_field() }}
	<div class="box-body">
		<div class="row">
			<div class="col-lg-8 col-md-8 col-xs-12 personal-info">
				<div class="form-group has-feedback {{ $errors->has('employee_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<label class="col-lg-3 control-label">Employee Name</label>
					<div class="col-lg-9">
						<input type="text" class="form-control" name="employee_name" placeholder="Employee Name.." value="{{$employee->employee_name ?? old('employee_name') }}" required autofocus >
						@if ($errors->has('employee_name'))
						<span class="help-block">
							<strong>{{ $errors->first('employee_name') }}</strong>
						</span>
						@endif
					</div>
				</div>

				<div class="form-group has-feedback {{ $errors->has('nickname') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<label class="col-lg-3 control-label">Nickname</label>
					<div class="col-lg-9">
						<input type="text" class="form-control" name="nickname" placeholder="nickname" value="{{$employee->nickname ?? old('nickname') }}"  autofocus >
						@if ($errors->has('nickname'))
						<span class="help-block">
							<strong>{{ $errors->first('nickname') }}</strong>
						</span>
						@endif
					</div>
				</div>



				<div class="form-group has-feedback {{ $errors->has('present_address') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<label class="col-lg-3 control-label">Present Address</label>
					<div class="col-lg-9">
						<input type="text" class="form-control" name="present_address" placeholder="Present Address.." value="{{ $employee->present_address ?? old('present_address') }}" required autofocus >
						@if ($errors->has('present_address'))
						<span class="help-block">
							<strong>{{ $errors->first('present_address') }}</strong>
						</span>
						@endif
					</div>
				</div>
				<div class="form-group has-feedback {{ $errors->has('permanent_address') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<label class="col-lg-3 control-label">Permanent Address</label>
					<div class="col-lg-9">
						<input type="text" class="form-control" name="permanent_address" placeholder="Permanent Address.." value="{{ $employee->permanent_address ?? old('permanent_address') }}" required autofocus >
						@if ($errors->has('permanent_address'))
						<span class="help-block">
							<strong>{{ $errors->first('permanent_address') }}</strong>
						</span>
						@endif
					</div>
				</div>
				<div class="form-group has-feedback {{ $errors->has('contact_number') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<label class="col-lg-3 control-label">Contact Number</label>
					<div class="col-lg-9">
						<input type="text" class="form-control" name="contact_number" placeholder="Contact Number.." value="{{ $employee->contact_number ?? old('contact_number') }}" required >
						@if ($errors->has('contact_number'))
						<span class="help-block">
							<strong>{{ $errors->first('contact_number') }}</strong>
						</span>
						@endif
					</div>
				</div>
				<div class="form-group has-feedback {{ $errors->has('email') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<label class="col-lg-3 control-label">Email</label>
					<div class="col-lg-9">
						<input type="text" class="form-control" name="email" placeholder="Email.." value="{{ $employee->email ?? old('email') }}" required >
						@if ($errors->has('email'))
						<span class="help-block">
							<strong>{{ $errors->first('email') }}</strong>
						</span>
						@endif
					</div>
				</div>
				<div class="form-group has-feedback {{ $errors->has('gender') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<label class="col-lg-3 control-label">Gender</label>
					<div class="col-lg-9">
						<select class="form-control" name="gender" style="width: 100%;" required>
							@if ($employee->gender ?? old('gender') == 1)
							<option value="1" selected>Male</option>
							<option value="2">Female</option>
							@else
							<option value="1">Male</option>
							<option value="2" selected>Female</option>
							@endif
						</select>
						@if ($errors->has('gender'))
						<span class="help-block">
							<strong>{{ $errors->first('gender') }}</strong>
						</span>
						@endif
					</div>
				</div>
				<div class="form-group has-feedback {{ $errors->has('religion') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<label class="col-lg-3 control-label">Religion</label>
					<div class="col-lg-9">
						<select class="form-control" name="religion" style="width: 100%;" required>
							@foreach ($religion as $keys)
								@if ($employee->hrm_religion_id == $keys->id)
									<option value={{$keys->id}} selected>{{$keys->religion}}</option>
								@else
									<option value={{$keys->id}}>{{$keys->religion}}</option>
								@endif
							@endforeach

						</select>
						@if ($errors->has('religion'))
						<span class="help-block">
							<strong>{{ $errors->first('religion') }}</strong>
						</span>
						@endif
					</div>
				</div>
				<div class="form-group has-feedback {{ $errors->has('marital_status') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<label class="col-lg-3 control-label">Marital Status</label>
					<div class="col-lg-9">
						<select class="form-control" name="marital_status" style="width: 100%;" required>
							@foreach ($marital_status as $keys)
							@if ($employee->hrm_marital_status_id == $keys->id)
							<option value={{$keys->id}} selected>{{$keys->marital_status}}</option>
							@else
							<option value={{$keys->id}}>{{$keys->marital_status}}</option>
							@endif
							@endforeach
						</select>
						@if ($errors->has('marital_status'))
						<span class="help-block">
							<strong>{{ $errors->first('marital_status') }}</strong>
						</span>
						@endif
					</div>
				</div>
				<div class="form-group has-feedback {{ $errors->has('blood_group') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<label class="col-lg-3 control-label">Blood Group</label>
					<div class="col-lg-9">
						<select class="form-control" name="blood_group" style="width: 100%;" required>
							@foreach ($blood_group as $keys)
							@if ($employee->hrm_blood_group_id == $keys->id)
							<option value={{$keys->id}} selected>{{$keys->blood_group}}</option>
							@else
							<option value={{$keys->id}}>{{$keys->blood_group}}</option>
							@endif
							@endforeach
						</select>
						@if ($errors->has('blood_group'))
						<span class="help-block">
							<strong>{{ $errors->first('blood_group') }}</strong>
						</span>
						@endif
					</div>
				</div>
				<div class="form-group has-feedback {{ $errors->has('date_of_birth') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<label class="col-lg-3 control-label">Date of Birth</label>
					<div class="col-lg-9">
						<div class="input-group date">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>


                             <input type="text" class="form-control" id="date_of_birth" name="date_of_birth" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask  value="{{  date('d/m/Y', strtotime(str_replace('/', '-', $employee->dob ))) }}" >

							@if ($errors->has('date_of_birth'))
							<span class="help-block">
								<strong>{{ $errors->first('date_of_birth') }}</strong>
							</span>
							@endif
						</div>
					</div>
				</div>
				<div class="form-group has-feedback {{ $errors->has('nid') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<label class="col-lg-3 control-label">NID / Smart Card</label>
					<div class="col-lg-9">
						<input type="text" class="form-control" name="nid" placeholder="NID/Smart Card.." value="{{ $employee->nid ?? old('nid') }}" >
						@if ($errors->has('nid'))
						<span class="help-block">
							<strong>{{ $errors->first('nid') }}</strong>
						</span>
						@endif
					</div>
				</div>

				<div class="form-group has-feedback {{ $errors->has('tin') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<label class="col-lg-3 control-label">TIN</label>
					<div class="col-lg-9">
						<input type="text" class="form-control" name="tin" placeholder="TIN" value="{{ $employee->tin ?? old('tin') }}" >
						@if ($errors->has('tin'))
						<span class="help-block">
							<strong>{{ $errors->first('tin') }}</strong>
						</span>
						@endif
					</div>
				</div>

				<div class="form-group has-feedback {{ $errors->has('father_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<label class="col-lg-3 control-label">Father's Name</label>
					<div class="col-lg-9">
						<input type="text" class="form-control" name="father_name" placeholder="Father's Name.." value="{{ $employee->father_name }}" >
						@if ($errors->has('father_name'))
						<span class="help-block">
							<strong>{{ $errors->first('father_name') }}</strong>
						</span>
						@endif
					</div>
				</div>
				<div class="form-group has-feedback {{ $errors->has('mothers_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<label class="col-lg-3 control-label">Mother's Name</label>
					<div class="col-lg-9">
						<input type="text" class="form-control" name="mothers_name" placeholder="Mother's Name.." value="{{ $employee->mother_name }}" >
						@if ($errors->has('mothers_name'))
						<span class="help-block">
							<strong>{{ $errors->first('mothers_name') }}</strong>
						</span>
						@endif
					</div>
				</div>

				<div class="form-group has-feedback {{ $errors->has('last_education') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<label class="col-lg-3 control-label">Highest Education</label>
					<div class="col-lg-9">
						<select class="form-control" id="education_name" name="hrm_education_id" style="width: 100%;" required>
							@foreach ($education as $keys)
							@if ($employee->hrm_education_id == $keys->id)
							<option value={{$keys->id}} selected>{{$keys->education_name}}</option>
							@else
							<option value={{$keys->id}}>{{$keys->education_name}}</option>
							@endif
							@endforeach
						</select>
						@if ($errors->has('last_education'))
						<span class="help-block">
							<strong>{{ $errors->first('last_education') }}</strong>
						</span>
						@endif
					</div>
				</div>


			</div>
			<div class="col-lg-4 col-md-4 col-xs-12 text-center">
				<img  id="blah" src="{{asset('employee_image/'.$employee->Images)}}" alt="your image" />
				<input  type='file' name="images" value="{{$employee->Images}}" onchange="readURL(this);" />

			</div>
			<div class="form-group">
				<label class="col-md-3 control-label"></label>
				<div class="col-md-8">
					<input type="submit" class="btn btn-success block btn-flat" value="Update" id="btnSubmit">
					<span></span>
				</div>
			</div>
		</div>
	</div>
	{!! Form::close() !!}
</div>
@endsection
@section('script')

<script src="{{asset('plugins/input-mask/jquery.inputmask.js')}}"></script>
<script src="{{asset('plugins/input-mask/jquery.inputmask.date.extensions.js')}}"></script>
<script src="{{asset('plugins/input-mask/jquery.inputmask.extensions.js')}}"></script>



<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('js/fileinput.js')}}"></script>
<script>

  $(function () {
    //Datemask dd/mm/yyyy
    $("#datemask").inputmask("dd-mm-yyyy", {"placeholder": "dd-mm-yyyy"});
    //Money Euro
    $("[data-mask]").inputmask();

  });
// $(document).ready(function($) {

// $('#date_of_birth').datepicker({
// 	autoclose: true
// });
// });



// $(document).ready(function (e) {
    $('#frm_employee').on('submit',(function(e) {
        e.preventDefault();

	    if(confirm('Do you want to Update?')){
	    	$("#btnSubmit").attr("disabled", true);
	    	$("#btnSubmit").val('Please wait..');


	        var formData = new FormData(this);

	        $.ajax({
	            type:'POST',
	            url: $(this).attr('action'),
	            data:formData,
	            cache:false,
	            contentType: false,
	            processData: false,
	            success:function(data){

						var erreurs ='<div class="alert alert-success"><ul>';
						erreurs += '<li>'+data.messages+'</li>';
						erreurs += '</ul></div>';
						$('#alert-message').html(erreurs);
						$('#alert-message').show(0).delay(4000).hide(0);

		    	        $('#btnSubmit').attr("disabled", false);
		                $("#btnSubmit").val('Submit');
						window.location.replace("{{ URL::to('employee')}}");


	            },
	            error: function(data){
						var erreurs ='<div class="alert alert-danger"><ul>';
						erreurs += '<li>'+data.messages+'</li>';
						erreurs += '</ul></div>';
						$('#alert-message').html(erreurs);
						$('#alert-message').show(0).delay(5000).hide(0);

						$('#btnSubmit').attr("disabled", false);
						$("#btnSubmit").val('Submit');


	            }
	        });

	 	}

    }));




</script>


<script type="text/javascript">

function readURL(input) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function (e) {
			$('#blah')
			.attr('src', e.target.result);
		};
		reader.readAsDataURL(input.files[0]);
	}
}
</script>
@endsection
