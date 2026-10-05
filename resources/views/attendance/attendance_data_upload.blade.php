<!-- create_employee -->
<!-- create_marital_status -->
@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

<style>

</style>
@endsection


@section('content')
<div class="box box-primary">

	<div class="box-header with-border">
		<h3 class="box-title">Attendance Data Upload</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>



		<div class="box-body">
			<div class="row">

						<div class="panel-body">


<!-- 								@if ($message = Session::get('success'))
								<div class="alert alert-success" role="alert">
									{{ Session::get('success') }}
								</div>
								@endif


								@if ($message = Session::get('error'))
								<div class="alert alert-danger" role="alert">
								{{ Session::get('error') }}
								</div>
								@endif
 -->

    <div class="form-group col-lg-12 col-md-12 col-xs-12">

	    <div class="form-group col-lg-6 col-md-6 col-xs-12">
	       <div id="alert-danger1"></div>
	       <div id="alert-success1"></div>
	    </div>

    </div>


							   <h4>Import Attendance Information File:</h4>
							 

  						    <form  method="POST" enctype="multipart/form-data" action="{{url('attendancedataupload')}}" class="form-horizontal" style="border: 4px solid #a1a1a1;margin-top: 20px;padding: 70px;" id="frm_data_upload">
                                {{ csrf_field() }}  

									<div class="col-lg-12 col-md-12 col-xs-12 form-group">
										<label class="control-label">Branch Location</label>
										<select class="form-control" id="location_id" name="location_id" style="width: 40%;" required>
										</select>

									</div>

									<div class="col-lg-12 col-md-12 col-xs-12 form-group">
						              	<input type="text"   class="form-control" name="short_description"         id="short_description"         placeholder="Location Device Information"  style="color: red;" readonly="" required>
						              	<input type="hidden" class="form-control" name="hrm_device_information_id" id="hrm_device_information_id" placeholder="Location Device Information"  style="color: red;" readonly="" required >
									</div>

									<div style="margin-top: 50px;">
										<input type="file" name="import_file" id="import_file"  required="" />
										{{ csrf_field() }}
										<br/>

                                       <input type="submit" class="btn btn-success btn-flat pull-left" value="Import" id="btnSubmit" style="margin-right: 10px;">

										<!-- <button class="btn btn-primary">Import</button> -->
									</div>
								</form>
								<br/>
							 

						</div>

			</div>
		</div>



</div>
@endsection


@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="{{asset('js/fileinput.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script>
$(document).ready(function($) {

	$location=$('#location_id').select2({
		      placeholder: 'Enter Branch location',
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

    $location.on('select2:select', function (e) {
     $("#short_description").val($(this).select2('data')['0']['short_description']);
     $("#hrm_device_information_id").val($(this).select2('data')['0']['hrm_device_information_id']);
    });


    $location.on('select2:unselect', function (e) {
    	console.log("HERE I AM");
        // $('#location_id').val(null).trigger("change");
        $('#short_description').val('');
        $('#hrm_device_information_id').val('');

        

    });




$('#frm_data_upload').on('submit',(function(e) {
       e.preventDefault();

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


				if(data.success == true) {
						
						var erreurs ='<div class="alert alert-success"><ul>';
						erreurs += '<li>'+data.messages+'</li>';
						erreurs += '</ul></div>';
						$('#alert-success1').html(erreurs);   
						$('#alert-success1').show(0).delay(4000).hide(0); 

						$('#btnSubmit').attr("disabled", false);
						$("#btnSubmit").val('Submit');

 				        $('#location_id').val(null).trigger("change");
						$('#import_file').val('');
						$('#short_description').val('');
						$('#hrm_device_information_id').val('');



				}else{

                        var erreurs ='<div class="alert alert-danger"><ul>';
						erreurs += '<li>'+data.messages+'</li>';
						erreurs += '</ul></div>';
						$('#alert-danger1').html(erreurs);   
						$('#alert-danger1').show(0).delay(8000).hide(0); 

	        	        $('#btnSubmit').attr("disabled", false);
                        $("#btnSubmit").val('Submit');

						
 				        // $('#location_id').val(null).trigger("change");
						// $('#import_file').val('');
						// $('#short_description').val('');
						// $('#hrm_device_information_id').val('');


				}

 	
		


           },
        
       });



   }));








});
</script>
@endsection
