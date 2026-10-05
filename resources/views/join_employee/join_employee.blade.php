@extends('layouts.main')

@section('styles')

<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

<!-- <link rel="stylesheet" href="../../bower_components/select2/dist/css/select2.min.css"> -->
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
		<h3 class="box-title">Employee Official Information</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>






    {!! Form::open(array('route'=>'employeejoin.store', 'onkeypress'=> "return event.keyCode != 13;", 'files'=>true, 'id'=>'frm_employee')) !!}
    {{ csrf_field() }}

    	<div class="box-body">
    		<div class="row">
    			<div class="col-lg-9 col-md-9 col-xs-12">
			        
			        <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('employee_name') ? ' has-error' : '' }}">  
		            	<label class="control-label">Employee Name</label>
		                <select class="form-control select2" name="employee_name" style="width: 100%;" >
		                	<option value="{{$employee->id}}">{{$employee->employee_name}}</option>
		                </select>

			            @if ($errors->has('employee_name'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('employee_name') }}</strong>
			                </span>
			            @endif 	                
			        </div>    			

			        <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('employee_code') ? ' has-error' : '' }}">  
		            	<label class="control-label">Employee Code</label>
		                <input type="text" class="form-control" name="employee_code" placeholder="Employee Code" value="{{ old('employee_code') }}" required  >
			            @if ($errors->has('employee_code'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('employee_code') }}</strong>
			                </span>
			            @endif 	                
			        </div>

			        <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('depertment') ? ' has-error' : '' }}">  
		            	<label class="control-label">Department</label>
		                <select class="form-control select2" id="depertment" name="depertment" style="width: 100%;" >
		                </select>
			            @if ($errors->has('depertment'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('depertment') }}</strong>
			                </span>
			            @endif 		                	                
			        </div>    			

			        <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('designation') ? ' has-error' : '' }}">  
		            	<label class="control-label">Designation</label>
		                <select class="form-control" id="designation" name="designation" style="width: 100%;" required>
		                </select>
			            @if ($errors->has('designation'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('designation') }}</strong>
			                </span>
			            @endif 		                	                
			        </div> 

			        <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('category') ? ' has-error' : '' }}" >  
		            	<label class="control-label">Employee Category</label>
		                <select class="form-control" id="category" name="category" style="width: 100%;" required>
		                	<!-- <option value="39">39</option> -->
		                </select>
			            @if ($errors->has('category'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('category') }}</strong>
			                </span>
			            @endif 		                	                
			        </div>

			        <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('jod_location') ? ' has-error' : '' }}">  
		            	<label class="control-label">Job Placement</label>
		                <select class="form-control" id="jod_location" name="jod_location" style="width: 100%;" required>
		                </select>
			            @if ($errors->has('jod_location'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('jod_location') }}</strong>
			                </span>
			            @endif 		                	                
			        </div>


			        <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('section') ? ' has-error' : '' }}" >  
		            	<label class="control-label">Sub-department</label>
		                <select class="form-control" id="section" name="section" style="width: 100%;" required>
		                	<!-- <option value="43">43</option> -->
		                </select>
			            @if ($errors->has('section'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('section') }}</strong>
			                </span>
			            @endif 		                	                
			        </div>


			        <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('working_shift') ? ' has-error' : '' }}">  
		            	<label class="control-label">Working Shift</label>
		                <select class="form-control" id="working_shift" name="working_shift" style="width: 100%;" required>
		                </select>
			            @if ($errors->has('working_shift'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('working_shift') }}</strong>
			                </span>
			            @endif 		                	                
			        </div>

			        <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('overtime') ? ' has-error' : '' }}">  
		            	<label class="control-label">Overtime</label>
		                <select class="form-control" name="overtime" style="width: 100%;" required>
		                	<option value="0">No</option>
		                	<option value="1">Yes</option>
		                </select>
			            @if ($errors->has('overtime'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('overtime') }}</strong>
			                </span>
			            @endif 		                	                
			        </div>

			        <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('insurance') ? ' has-error' : '' }}">  
		            	<label class="control-label">Insurance</label>
		                <select class="form-control" name="insurance" style="width: 100%;" required>
		                	<option value="0">No</option>
		                	<option value="1">Yes</option>
		                </select>
			            @if ($errors->has('insurance'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('insurance') }}</strong>
			                </span>
			            @endif 		                	                
			        </div>


	                <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('plant_name') ? ' has-error' : '' }}">  
	                  <label class="control-label">Plant/Work Place</label>
	                    <select class="form-control" id="plant_name" name="plant_name" style="width: 100%;" required>
	                    </select>
	                  @if ($errors->has('plant_name'))
	                      <span class="help-block">
	                          <strong>{{ $errors->first('plant_name') }}</strong>
	                      </span>
	                  @endif                                      
	                </div>


		           <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('manage_by') ? ' has-error' : '' }}">  
	            	<label class="control-label">Reporting to</label>
	                <select class="form-control" id="manage_by" name="manage_by" style="width: 100%;" required>
	                </select>
		            @if ($errors->has('manage_by'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('manage_by') }}</strong>
		                </span>
		            @endif 		                	                
		           </div>




		           <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('basic_salary') ? ' has-error' : '' }}">  
	            	<label class="control-label">Gross Salary</label>
	                <input type="text" class="form-control" name="basic_salary" placeholder="Basic/Gross Salary" value="{{ old('employee_name') }}" required  >
		            @if ($errors->has('basic_salary'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('basic_salary') }}</strong>
		                </span>
		            @endif 	                
		         </div> 

	              @if ($payroll_module == 1)
	                <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('salary_grade') ? ' has-error' : '' }}">  
	                    <label class="control-label">Salary Grade</label>
	                      <select class="form-control" id="salary_grade" name="salary_grade" style="width: 100%;" required>
	                      </select>
	                    @if ($errors->has('salary_grade'))
	                        <span class="help-block">
	                            <strong>{{ $errors->first('salary_grade') }}</strong>
	                        </span>
	                    @endif                  
	                </div> 
	              @endif

		          <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('joining_date') ? ' has-error' : '' }}">  
	            	<label class="control-label">Joining Date</label>
		            <div class="input-group date">
		                <div class="input-group-addon">
		                  <i class="fa fa-calendar"></i>
		                </div>		            
		                <!-- <input type="text" class="form-control pull-right" id="joining_date" name="joining_date" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('joining_date') }}" required readonly> -->
                  <input type="text" class="form-control" id="joining_date" name="joining_date" data-inputmask="'alias': 'dd-mm-yyyy'" data-mask value="{{date('d-m-Y')}}">

			            @if ($errors->has('joining_date'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('joining_date') }}</strong>
			                </span>
			            @endif 	                
		            </div>	                
		          </div>  



		           <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('employeestatus') ? ' has-error' : '' }}">  
	            	<label class="control-label">Employee Status</label>
	                <select class="form-control" id="employeestatus" name="employeestatus" style="width: 100%;" required>
	                </select>
		            @if ($errors->has('employeestatus'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('employeestatus') }}</strong>
		                </span>
		            @endif
		          </div>

		           <div class="col-lg-6 col-md-6 col-xs-12 form-group probation has-feedback {{ $errors->has('employeestatus') ? ' has-error' : '' }}">  
		            	<label class="control-label">Probation Period (Months)</label>
		                <select class="form-control" id="probation_period" name="probation_period" style="width: 100%;" required>
								@foreach ($probation as $probation)
										<option value="{{$probation->id}}">{{$probation->period}}</option>
								@endforeach
		                </select>
		          </div>



		         <div class="col-lg-6 col-md-6 col-xs-12 form-group confirm has-feedback {{ $errors->has('confirmation_date') ? ' has-error' : '' }}">  
	            	<label class="control-label">Confirmation Date</label>
		            <div class="input-group date">
		                <div class="input-group-addon">
		                  <i class="fa fa-calendar"></i>
		                </div>		            
                       <input type="text" class="form-control" id="confirmation_date" name="confirmation_date" data-inputmask="'alias': 'dd-mm-yyyy'" data-mask value="{{date('d-m-Y')}}">

				            @if ($errors->has('confirmation_date'))
				                <span class="help-block">
				                    <strong>{{ $errors->first('confirmation_date') }}</strong>
				                </span>
				            @endif 	                
			            </div>	                
		         </div> 

    	    </div>


			<div class="col-lg-3 col-md-3 col-xs-12">
				<div class="col-lg-12 col-md-12 col-xs-12 text-center">
					    <img  id="blah" src="{{asset('employee_image/'.$employee->Images)}}" alt="your image" />        
                        <input  type='file' name="images" value="{{$employee->Images}}" onchange="readURL(this);" />
				</div>    			
			</div>


			<div class="col-lg-12 col-md-12 col-xs-12 form-group">

				<h4 class="panel-title">
				<i class="glyphicon glyphicon-plus-sign"></i>
				<a role="button" style="color:green;" onclick="myFunction()">
				Add Card Info
				</a>
				</h4>
					<div id="myDIV" >

	                    <div class="col-lg-4 col-md-4 col-xs-12 ">  
						           <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('card_no') ? ' has-error' : '' }}">  
						            	<label class="control-label">Card No</label>
						                <input type="text" class="form-control" id="card_no"  name="card_no" placeholder="****">
						          </div>

						           <div class="col-lg-6 col-md-6 col-xs-12 form-group has-feedback {{ $errors->has('device_id') ? ' has-error' : '' }}">  
						            	<label class="control-label">Device Id</label>
						                <input type="text" class="form-control" id="device_id" name="device_id" placeholder="1">
						          </div>


						</div>
					</div>
			</div> 



				<div class="col-lg-12 col-md-12 col-xs-12 form-group">

					<div class="col-lg-12 col-md-12 col-xs-12">
						<div id="alert-message"></div>
					</div>

					<div class="col-lg-12 col-md-12 col-xs-12">
						<input type="submit" class="btn btn-success btn-flat pull-right" value="Submit" id="btnSubmit" style="margin-right: 10px;">
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

<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script>


  $(function () {
    //Datemask dd/mm/yyyy
    $("#datemask").inputmask("dd-mm-yyyy", {"placeholder": "dd-mm-yyyy"});
    //Money Euro
    $("[data-mask]").inputmask();

  });


$(document).ready(function($) {
    // $('#joining_date').datepicker({
    //   autoclose: true
    // });
    // $('#confirmation_date').datepicker({
    //   autoclose: true
    // });

    $('.probation').hide();
    $('.confirm').hide();
   

	$('#employeestatus').on('change', function(){
		  	
			if($("#employeestatus").val() == 1){
			    $('.probation').show(); 
			    $('.confirm').hide(); 

			}else{
			    $('.confirm').show();
			    $('.probation').hide(); 

			}

			if($("#employeestatus").val() == null){
		 		$('.probation').hide();
		    	$('.confirm').hide();
			}

	});







    $('#depertment').select2({
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

    $('#category').select2({
      placeholder: 'Enter Category',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/category_list_data',
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

    $('#manage_by').select2({
      placeholder: 'Enter manage by',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/employee_list_data',
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

    $('#employeestatus').select2({
      placeholder: 'Enter employee status',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/employeestatus_list_data',
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

    $('#jod_location').select2({
      placeholder: 'Enter Job Placement',
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
 

  $('#salary_grade').select2({
      placeholder: 'Enter a Salary Grade',
      allowClear: true,
        ajax: {
            dataType: 'json',
            url: '{{URL::to('/')}}/salarygrade_list',
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



// $('#myDIV').hide(); 

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

// console.log(data);
						if(!data.success){
							var erreurs ='<div class="alert alert-danger"><ul>';
								erreurs += '<li>'+data.errors+'</li>';
								erreurs += '</ul></div>';
							$('#alert-message').html(erreurs);   
							$('#alert-message').show(0).delay(5000).hide(0); 

							$('#btnSubmit').attr("disabled", false);
							$("#btnSubmit").val('Submit');
						}else{
							var erreurs ='<div class="alert alert-success"><ul>';
								erreurs += '<li>'+data.success+'</li>';
								erreurs += '</ul></div>';
							$('#alert-message').html(erreurs);   
							$('#alert-message').show(0).delay(4000).hide(0); 

							$('#btnSubmit').attr("disabled", false);
							$("#btnSubmit").val('Submit');
							window.location.replace("{{ URL::to('employeejoin')}}");
						
						}

	            },


	        });

	 	}

    }));






});
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



<script>
function myFunction() {
    var x = document.getElementById("myDIV");
    if (x.style.display === "none") {
        x.style.display = "block";
    } else {
        x.style.display = "none";
        $('#card_no').val(''); 
        $('#device_id').val(''); 
    }
}
</script>

@endsection