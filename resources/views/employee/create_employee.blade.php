<!-- create_employee -->
<!-- create_marital_status -->
@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}"></script>
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">



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
		<h3 class="box-title">Create Employee Information</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>

    <div class="col-lg-12 col-md-12 col-xs-12">

	    <div class="col-lg-6 col-md-6 col-xs-12">
	       <div id="alert-message"></div>
	    </div>

    </div>




    {!! Form::open(array('route'=>'employee.store', 'onkeypress'=> "return event.keyCode != 13;", 'files'=>true, 'id'=>'frm_employee')) !!}
    {{ csrf_field() }}

	<div class="box-body">
		<div class="row">

<!-- 			<div class="col-lg-3 col-md-3 col-xs-12">
				<div class="text-center has-feedback {{ $errors->has('marital_status') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
					<div class="col-lg-12">
					<img src="//placehold.it/100" class="avatar img-circle" alt="avatar">
					<h6>Upload a different photo...</h6>
					<input type="file" class="form-control">
					</div>
				</div>
			</div> -->

			<div class="col-lg-8 col-md-8 col-xs-12 personal-info">

		        <div class="form-group has-feedback {{ $errors->has('employee_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
		            <label class="col-lg-3 control-label">Employee Name</label>
		            <div class="col-lg-9">
		                <input type="text" class="form-control" name="employee_name" placeholder="Employee Name.." value="{{ old('employee_name') }}" required autofocus >
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
		                <input type="text" class="form-control" name="nickname" placeholder="nickname" value="{{ old('nickname') }}"  autofocus >
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
		                <input type="text" class="form-control" name="present_address" placeholder="Present Address.." value="{{ old('present_address') }}" required autofocus >
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
		                <input type="text" class="form-control" name="permanent_address" placeholder="Permanent Address.." value="{{ old('permanent_address') }}" required autofocus >
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
		                <input type="text" class="form-control" name="contact_number" placeholder="Contact Number.." value="{{ old('contact_number') }}" required >
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
		                <input type="text" class="form-control" name="email" placeholder="Email.." value="{{ old('email') }}" required >
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
		                    <option value="1">Male</option>
		                    <option value="2">Female</option>
		                </select>
			            @if ($errors->has('gender'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('gender') }}</strong>
			                </span>
			            @endif
		            </div>
		        </div>


		        <div class="form-group has-feedback {{ $errors->has('religion') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12" >
		            <label class="col-lg-3 control-label">Religion</label>
		            <div class="col-lg-9">
		                <select class="form-control" name="religion" style="width: 100%;" required>
		                    <!-- <option value="">Select Value</option> -->
		                    @foreach ($religion as $keys)
		                    	@if ($errors->has('religion') == $keys->id)
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

		        <div class="form-group has-feedback {{ $errors->has('marital_status') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12" >
		            <label class="col-lg-3 control-label">Marital Status</label>
		            <div class="col-lg-9">
		                <select class="form-control" name="marital_status" style="width: 100%;" required>
		                    <!-- <option value="">Select Value</option> -->
		                    @foreach ($marital_status as $keys)
		                    	<option value={{$keys->id}}>{{$keys->marital_status}}</option>
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
		                    <option value="">Select Value</option>
		                    @foreach ($blood_group as $keys)
		                    	<option value={{$keys->id}}>{{$keys->blood_group}}</option>
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
		                <!-- <input type="text" class="form-control pull-right" id="date_of_birth" name="date_of_birth" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('date_of_birth') }}" required > -->

		                 <input type="text" class="form-control" id="date_of_birth" name="date_of_birth" data-inputmask="'alias': 'dd-mm-yyyy'" data-mask value="{{date('d-m-Y')}}">

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
		                <input type="text" class="form-control" name="nid" placeholder="NID/Smart Card.." value="{{ old('nid') }}" >
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
		                <input type="text" class="form-control" name="tin" placeholder="TIN" value="{{ old('tin') }}" >
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
		                <input type="text" class="form-control" name="father_name" placeholder="Father's Name.." value="{{ old('father_name') }}" >
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
		                <input type="text" class="form-control" name="mothers_name" placeholder="Mother's Name.." value="{{ old('mothers_name') }}" >
			            @if ($errors->has('mothers_name'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('mothers_name') }}</strong>
			                </span>
			            @endif
		            </div>
		        </div>


		        <div class="form-group has-feedback {{ $errors->has('education_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
		            <label class="col-lg-3 control-label">Highest Education</label>
		            <div class="col-lg-9">
		            		<select id="education_name" name="hrm_education_id" class="form-control" required>

		            		</select>


			            @if ($errors->has('education_name'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('education_name') }}</strong>
			                </span>
			            @endif
		            </div>
		        </div>

			</div>

			<div class="col-lg-4 col-md-4 col-xs-12 text-center">
				<div class="kv-avatar">
						<div class="file-loading">
							<!-- <input id="avatar-1" name="avatar-1" type="file" required> -->
							<input id="avatar-1" name="images" type="file">
						</div>
					</div>
				<div class="kv-avatar-hint"><small>Select file < 1500 KB</small></div>
			</div>

			<div class="form-group">
				<label class="col-md-3 control-label"></label>
				<div class="col-md-8">
					<input type="submit" class="btn btn-success block btn-flat pull-left" value="Submit" id="btnSubmit">
					<span></span>
					<!-- <input type="reset" class="btn block btn-flat btn-default" value="Cancel"> -->
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
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>


<script src="{{asset('js/fileinput.js')}}"></script>
<script>

  $(function () {
    //Datemask dd/mm/yyyy
    $("#datemask").inputmask("dd-mm-yyyy", {"placeholder": "dd-mm-yyyy"});
    //Money Euro
    $("[data-mask]").inputmask();

  });




$(document).ready(function($) {

    // $('#date_of_birth').datepicker({
    //   // startDate: new Date() ,
    //   autoclose: true
    // });


  var btnCust = '<button type="button"  class="btn btn-secondary" title="Add picture tags" ' +
      'onclick="alert(\'Call your custom code here.\')">' +
      '<i class="glyphicon glyphicon-tag"></i>' +
      '</button>';
  $("#avatar-1").fileinput({
      overwriteInitial: true,
      maxFileSize: 1500,
      showClose: false,
      showCaption: false,
      browseLabel: '',
      removeLabel: '',
      browseIcon: '<i class="glyphicon glyphicon-folder-open"></i>',
      removeIcon: '<i class="glyphicon glyphicon-remove"></i>',
      removeTitle: 'Cancel or reset changes',
      elErrorContainer: '#kv-avatar-errors-1',
      msgErrorClass: 'alert alert-block alert-danger',
      defaultPreviewContent: '<img src="{{asset('img/default_avatar_male.jpg')}}" alt="Your Avatar">',
      layoutTemplates: {main2: '{preview} ' +  btnCust + ' {remove} {browse}'},
      allowedFileExtensions: ["jpg", "png", "gif"]
  });
});
</script>

<script type="text/javascript">

    $('#education_name').select2({
      placeholder: 'Enter Education Name',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/education_list_data',
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


    $('#frm_employee').on('submit',(function(e) {
        e.preventDefault();

	    if(confirm('Do you want to Submit?')){
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


@endsection
