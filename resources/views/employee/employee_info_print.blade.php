@extends('layouts.main')
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

<style type="text/css">
	/*body {padding:3%;background:#fff}*/

	img{
		width: 210px;
		height: 210px;
	}

	input[type=file]{
		padding:10px;
		/*background:#2d2d2d;*/
	}

	.bold-off {
	    font-weight: normal !important;
	}
	.panel-heading {
	    padding: 0
	}
	.panel-heading a {
	    display: block;
	    padding: 20px 10px;
	}
	.panel-heading a.collapsed {
	    background: #fff
	}
	.panel-heading a {
	    background: #f7f7f7;
	    border-radius: 5px;
	}
	.panel-heading a:after {
	    content: '-'
	}
	.panel-heading a.collapsed:after {
	    content: '+'
	}
	.nav.nav-tabs li a,
	.nav.nav-tabs li.active > a:hover,
	.nav.nav-tabs li.active > a:active,
	.nav.nav-tabs li.active > a:focus {
	    border-bottom-width: 0px;
	    outline: none;
	}
	.nav.nav-tabs li a {
	    padding-top: 20px;
	    padding-bottom: 20px;
	}
	.tab-pane {
	    background: #fff;
	    padding: 10px;
	    border: 1px solid #ddd;
	    margin-top: -1px;
	}

	/* used for sidebar tab/collapse*/
	@media (max-width: 991px) {
	  .visible-tabs {
	    display: none;
	  }
	}

	@media (min-width: 992px) {
	  .visible-tabs {
	    display: block !important;
	  }
	}

	@media (min-width: 992px) {
	  .hidden-tabs {
	    display: none !important;
	  }
	}

</style>

@endsection

@section('content')


<!-- <div class="box box-primary"> -->

<!-- 	<div class="box-header with-border">
		<div class="box-tools pull-right">

			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>

		</div>
	</div> -->

	<div class="row">


            <div class="tab-content">
					<div class="box-body " >


						<div class="row">


						<div class="col-lg-12 col-md-12 col-xs-12 ">
							<div style="text-align: center;">
								 <img src="{{asset('dist/img/com-logo.jpg')}}" class="img-circle" alt="User Image" style="height: 60px;width: 100px; display: block;margin-left: auto; margin-right: auto;">
								<h4 style="margin-left: 20px; color: #007F3D!important">{{Config::get('configaration.company_name')}}</h4>
								<h6 style="margin-left: 20px; color: black!important">{{Config::get('configaration.company_address')}}</h5>
								<h6 style="margin-left: 20px; color: black!important"><b>Employee Profile</b></h6>
							</div>
							<h6 style="margin-left: 20px;"><label >Personal Information</label></h6>

						    <div class="col-lg-8 col-md-8 col-xs-8">

						    <table class=" table-bordered ">
						    	<tr>
						    		<td  class="col-lg-3 col-md-3 col-xs-5  ">Employee Name</td>
						    		<td class="col-lg-9 col-md-9 col-xs-7 ">{{$employee->employee_name}}</td>
						    	</tr>
								<tr>
						    		<td  class="col-lg-3 col-md-3 col-xs-5 ">Nickname</td>
						    		<td class="col-lg-9 col-md-9 col-xs-7 ">{{$employee->nickname}}</td>
						    	</tr>

								<tr >
									<td class="col-lg-3 col-md-3 col-xs-5 control-label">Present Address</td>
									<td class="col-lg-9 col-md-9 col-xs-7 control-label">{{$employee->present_address}}</td>
							    </tr>
								<tr >
									<td class="col-lg-3 col-md-3 col-xs-5 control-label">Permanent Address</td>
									<td class="col-lg-9 col-md-9 col-xs-7 control-label">{{$employee->permanent_address}}</td>
								</tr>

								<tr >
									<td class="col-lg-3 col-md-3 col-xs-5 control-label">Contact Number</td>
									<td class="col-lg-9 col-md-9 col-xs-7 control-label">{{$employee->contact_number}}</td>
								</tr>

								<tr class="{{ $errors->has('email') ? ' has-error' : '' }} ">
									<td class="col-lg-3 col-md-3 col-xs-5 control-label">Email</td>
									<td class="col-lg-9 col-md-9 col-xs-7 control-label">{{$employee->email}}</td>
								</tr>

								<tr class="{{ $errors->has('gender') ? ' has-error' : '' }} ">
									<td class="col-lg-3 col-md-3 col-xs-5 control-label">Gender</td>
									@if ($employee->gender == 1)
									<td class="col-lg-9 col-md-9 col-xs-7 control-label">Male</td>
									@else
									<td class="col-lg-9 col-md-9 col-xs-7 control-label">Female</td>
									@endif
								</tr>


								<tr class="{{ $errors->has('religion') ? ' has-error' : '' }} ">
									<td class="col-lg-3 col-md-3 col-xs-5 control-label">Religion</td>
									<td class="col-lg-9 col-md-9 col-xs-7 control-label">{{$editemployee[0]->religion}}</td>
								</tr>

								<tr class="{{ $errors->has('marital_status') ? ' has-error' : '' }} ">
									<td class="col-lg-3 col-md-3 col-xs-5 control-label">Marital Status</td>
									<td class="col-lg-9 col-md-9 col-xs-7 control-label">{{$editemployee[0]->marital_status}}</td>
								</tr>


								<tr class="{{ $errors->has('blood_group') ? ' has-error' : '' }} ">
									<td class="col-lg-3 col-md-3 col-xs-5 control-label">Blood Group</td>
									<td class="col-lg-9 col-md-9 col-xs-7 control-label">{{$editemployee[0]->blood_group}}</td>
								</tr>

								<tr class="{{ $errors->has('date_of_birth') ? ' has-error' : '' }} ">
									<td class="col-lg-3 col-md-3 col-xs-5 control-label">Date of Birth</td>
									<td class="col-lg-9 col-md-9 col-xs-7 control-label">{{  date('d-m-Y', strtotime(str_replace('-', '/', $employee->dob ))) }}</td>
								</tr>

								<tr class="{{ $errors->has('nid') ? ' has-error' : '' }} ">
									<td class="col-lg-3 col-md-3 col-xs-5 control-label">NID / Smart Card</td>
									<td class="col-lg-9 col-md-9 col-xs-7 control-label">{{$employee->nid}}</td>
								</tr>

								<tr class="{{ $errors->has('tin') ? ' has-error' : '' }} ">
									<td class="col-lg-3 col-md-3 col-xs-5 control-label">TIN</td>
									<td class="col-lg-9 col-md-9 col-xs-7 control-label">{{$employee->tin}}</td>
								</tr>

								<tr class="{{ $errors->has('father_name') ? ' has-error' : '' }} ">
									<td class="col-lg-3 col-md-3 col-xs-5 control-label">Father's Name</td>
									<td class="col-lg-9 col-md-9 col-xs-7 control-label">{{$employee->father_name}}</td>
								</tr>

								<tr class="{{ $errors->has('mothers_name') ? ' has-error' : '' }} ">
									<td class="col-lg-3 col-md-3 col-xs-5 control-label">Mother's Name</td>
									<td class="col-lg-9 col-md-9 col-xs-7 control-label">{{$employee->mother_name}}</td>
								</tr>

								<tr class="{{ $errors->has('last_education') ? ' has-error' : '' }} ">
									<td class="col-lg-3 col-md-3 col-xs-5 control-label">Highest Education</td>
									<td class="col-lg-9 col-md-9 col-xs-7 control-label">{{$editemployee[0]->education_name}}</td>
								</tr>
    							</table>
							</div>

							<div class="col-lg-4 col-md-4 col-xs-4 text-center">
								<img  id="blah" src="{{asset('employee_image/'.$employee->Images)}}" alt="your image" />
							</div>

							</div>

							<div class="col-lg-12 col-md-12 col-xs-12 ">

							<h6 style="margin-left: 20px;"><label >Official Information</label></h6>
	    					<div class="col-lg-12 col-md-12 col-xs-12 ">
	    					<table class=" table-bordered ">
						    	<tr>
						    		<td  class="col-lg-3 col-md-3 col-xs-2  ">Emp. Code</td>
						    		<td class="col-lg-3 col-md-3 col-xs-4 ">{{$editemployee[0]->employee_code}}</td>
						    		<td  class="col-lg-3 col-md-3 col-xs-2 ">Official Email</td>
						    		<td class="col-lg-3 col-md-3 col-xs-4 ">{{$editemployee[0]->official_email}}</td>
						    	</tr>

						    	<tr>
						    		<td  class="col-lg-3 col-md-3 col-xs-2  ">Official Contact</td>
						    		<td class="col-lg-3 col-md-3 col-xs-4 ">{{$editemployee[0]->official_contact_no}}</td>
						    		<td  class="col-lg-3 col-md-3 col-xs-2 ">Department</td>
						    		<td class="col-lg-3 col-md-3 col-xs-4 ">{{$editemployee[0]->depertment_name}}</td>
						    	</tr>




								<tr>
						    		<td  class="col-lg-3 col-md-3 col-xs-2 ">Designation</td>
						    		<td class="col-lg-3 col-md-3 col-xs-4 ">{{$editemployee[0]->designation_name}}</td>
						    		<td  class="col-lg-3 col-md-3 col-xs-2 ">Category</td>
						    		<td class="col-lg-3 col-md-3 col-xs-4 ">{{$editemployee[0]->category_name}}</td>
						    	</tr>

								<tr >
									<td class="col-lg-3 col-md-3 col-xs-2">Sub-department</td>
									<td class="col-lg-3 col-md-3 col-xs-4">{{$editemployee[0]->section_name}}</td>
									<td class="col-lg-3 col-md-3 col-xs-2">Job Placement</td>
									<td class="col-lg-3 col-md-3 col-xs-4 ">{{$editemployee[0]->location_name}}</td>
								</tr>


								<tr  >
									<td class="col-lg-3 col-md-3 col-xs-2">Working Shift</td>
									<td class="col-lg-3 col-md-3 col-xs-4">{{$editemployee[0]->shift_name}}</td>
									<td class="col-lg-3 col-md-3 col-xs-2">Overtime</td>
									@if ($editemployee[0]->overtime_status ?? old('overtime_status') == 1)
									<td class="col-lg-3 col-md-3 col-xs-4 ">Yes</td>
									@else
									<td class="col-lg-3 col-md-3 col-xs-4 ">No</td>
									@endif
								</tr>

								<tr>
									<td class="col-lg-3 col-md-3 col-xs-2">Insurance</td>
									@if ($editemployee[0]->insurance ?? old('insurance') == 1)
									<td class="col-lg-3 col-md-3 col-xs-4 ">Yes</td>
									@else
									<td class="col-lg-3 col-md-3 col-xs-4 ">No</td>
									@endif
									<td class="col-lg-3 col-md-3 col-xs-2">Work Place</td>
									<td class="col-lg-3 col-md-3 col-xs-4 ">{{$editemployee[0]->plant_name}}</td>
								</tr>

								<tr >
									<td class="col-lg-3 col-md-3 col-xs-2">Reporting to</td>
									<td class="col-lg-3 col-md-3 col-xs-4">{{$editemployee[0]->manage_by_name}}</td>
									<td class="col-lg-3 col-md-3 col-xs-2">Emp. Type</td>
									<td class="col-lg-3 col-md-3 col-xs-4 ">{{$editemployee[0]->employeestatus_name}}</td>
								</tr>

								<tr >
									<td class="col-lg-3 col-md-3 col-xs-2">Gross Salary</td>
									<td class="col-lg-3 col-md-3 col-xs-4">{{$editemployee[0]->basic_salary}}</td>
									<td class="col-lg-3 col-md-3 col-xs-2">Joining Date</td>
									<td class="col-lg-3 col-md-3 col-xs-4 ">{{  date('d-m-Y', strtotime(str_replace('-', '/', $editemployee[0]->joining_date ))) }}</td>
								</tr>

								<tr >
									<td class="col-lg-3 col-md-3 col-xs-2">Salary Grade</td>
									<td class="col-lg-3 col-md-3 col-xs-4">
										@if (Config::get('module_config.payroll_module') == 1)
											{{ isset($employee_salary_grade[0]) ? $employee_salary_grade[0]->grade_name : '' }}
	 									@endif
									</td>
									<td class="col-lg-3 col-md-3 col-xs-2">Confirm.Date</td>
									<td class="col-lg-3 col-md-3 col-xs-4 ">
										@if($editemployee[0]->employeestatus_id==1)
											Not yet confirmed
										@else
										{{  date('d-m-Y', strtotime(str_replace('-', '/', $editemployee[0]->confirmation_date ))) }}
										@endif

									</td>
								</tr>

								<tr >
									<td class="col-lg-3 col-md-3 col-xs-2">Probation Period</td>
									<td class="col-lg-3 col-md-3 col-xs-4">
										@if($editemployee[0]->employeestatus_id==1)
											{{$editemployee[0]->period}}
										@else
											N/A
										@endif
									</td>
									<td class="col-lg-3 col-md-3 col-xs-2">Job Duration</td>
									<td class="col-lg-3 col-md-3 col-xs-4 ">
										@if($editemployee[0]->employeestatus_id==1)
											Calculate After Confirmation
										@else
											{{$editemployee[0]->jobduration}}
										@endif

									</td>
								</tr>




						    </table>
							</div>

							</div>
						</div>

					</div>


              	<!-- <div class="tab-pane" >

					<div class="box-body tabcontent" id="documents">

						<div class="box-body">
							<div class="row">
						        <div class="form-group ">

						          <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
						            <label control-label">File Type</label>
						            <select style="width: 100%;" class="form-control select2" id="file_type" name="file_type" required>
						            </select>

						          </div>

									<table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th style="width: 20%">Date</th>
												<th style="width: 20%">File Type</th>
												<th style="width: 20%">File Title</th>
												<th style="width: 50%">Note</th>
												<th style="width: 5%">View</th>
												<th style="width: 5%">Download</th>
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
              	</div> -->

              	<!-- <div class="tab-pane" id="tab-4">
					<div class="box-body tabcontent" id="history">

						<div class="box-body">
							<div class="row">
						        <div class="form-group ">


									<table id="list_table_history" class="table table-bordered table-hover" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th style="width: 0%"></th>
												<th style="width: 20%">Employee Activity</th>
												<th style="width: 10%">Start Date</th>
												<th style="width: 10%">End Date</th>
												<th style="width: 15%">Location</th>
												<th style="width: 15%">Department</th>
												<th style="width: 15%">Designation</th>
												<th style="width: 15%">Salary</th>
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
              	</div> -->


            </div>

    </div>
    <!--/.container -->
<!-- </div> -->

@endsection

@section('script')
<script src="{{asset('js/fileinput.js')}}"></script>

<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script>
$(document).ready(function($) {




    dataLoad = function(){


      if ($("#file_type").val() == null){
        file_type = 0;
      }else{
        file_type = $("#file_type").val();
      }

      if ($("#employee_id").val() == null){
        employee_id = 0;
      }else{
        employee_id = $("#employee_id").val();
      }


      	$.ajax({
	        type:   'POST',
	        url :   "{{URL::to('/')}}/employeewisefile_list",
	        headers:{
	                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
	                },
	        data:   {
	           // date_from: $("#date_from").val(),
	           // date_to: $("#date_to").val(),
	           file_type: file_type,
	           employee_id:employee_id,
	        },
	        dataType: 'json',
	        success: function(data) {
	            var dataSet = data.data;
	            table = $('#list_table').DataTable( {
	              destroy:    true,
	              paging:     false,
	              searching:  false,
	              ordering:   true,
	              bInfo:      false,
	              "data":     dataSet,

	            "columns": [

	              { "data": "attached_date" },
	              { "data": "file_type_name" },
	              { "data": "file_title" },
	              { "data": "note" },
	              { "data": "Link",
	                "mRender": function (data, type, full) {
	                return '<a data-hrm_employee_file_id="'+full.hrm_employee_file_id+'" class="btn btn-info btn-sm btn-flat showme"><span class="glyphicon glyphicon-resize-full"></span> View</a>';
	                }
	              },
	              { "data": "Link",
	                "mRender": function (data, type, full) {
	                    return '<a href="{{URL::to('/')}}/document_archive/'+full.hrm_employee_file_id+'/download" class="btn btn-success btn-sm btn-flat"><span class="glyphicon "></span> Download</a>';
	                }
	              },
	            ],
	            "order": [[0,'asc']]
	            });
	        }
      	});
    }

	dataLoad();


	$file_type = $('#file_type').select2({
		      placeholder: 'Enter File Type',
		      allowClear: true,
		      ajax: {
		        dataType: 'json',
		        url: '{{URL::to('/')}}/file_type_list',
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

	$file_type.on('select2:select', function (e) {
	    dataLoad();
	});

	$file_type.on('select2:unselect', function (e) {
	    $('#file_type').val(null).trigger("change");
	    dataLoad();
	});


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

	function openCity(evt, cityName) {
	    // Declare all variables
	    var i, tabcontent, tablinks;

	    // Get all elements with class="tabcontent" and hide them
	    tabcontent = document.getElementsByClassName("tabcontent");
	    for (i = 0; i < tabcontent.length; i++) {
	        tabcontent[i].style.display = "none";
	    }

	    // Get all elements with class="tablinks" and remove the class "active"
	    tablinks = document.getElementsByClassName("tablinks");
	    for (i = 0; i < tablinks.length; i++) {
	        tablinks[i].className = tablinks[i].className.replace(" active", "");
	    }

	    // Show the current tab, and add an "active" class to the button that opened the tab
	    document.getElementById(cityName).style.display = "block";
	    evt.currentTarget.className += " active";
	}


      	$.ajax({
	        type:   'POST',
	        url :   "{{URL::to('/')}}/employeehistory_list",
	        headers:{
	                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
	                },
	        data:   {
	           employee_id:employee_id,
	        },
	        dataType: 'json',
	        success: function(data) {
	            var dataSet = data.data;
	            table = $('#list_table_history').DataTable( {
	              destroy:    true,
	              paging:     false,
	              searching:  false,
	              ordering:   true,
	              bInfo:      false,
	              "data":     dataSet,
	            "columnDefs": [
                  {
                      "targets": [ 0 ],
                      "visible": false,
                      "searchable": true
                  }
                 ],
	            "columns": [

	              { "data": "id" },
	              { "data": "description" },
	              { "data": "start_date" },
	              { "data": "end_date" },
	              { "data": "location_name" },
	              { "data": "depertment_name" },
	              { "data": "designation_name" },
	              { "data": "basic_salary" },
	            ],
	            "order": [[0,'asc']]
	            });
	        }
      	});




});

</script>


<script type="text/javascript">

$(document).ready(function($) {

  $('#list_table').on('click', '.showme', function(e){
    event.preventDefault();
      $.ajax({
          type: 'GET',
          url : '{{URL::to('/')}}/document_archive/'+$(this).data('hrm_employee_file_id')+'/view',
          success : function (data) {
            console.log(data);
            $("#fileshow").html(data);
          }
      });
    // $("#load_file").animatedModal();
    $('#load_file').modal('show');
  });

});
</script>




    <!-- modal -->
  <div id="load_file" class="modal animated bounceIn" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  aria-hidden="true">

      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close" >
                  <span aria-hidden="true">×</span>
                  </button>
              </div>
              <div class="modal-body">
                  <div class="h_iframe" id="fileshow" style="text-align: center;">

                  </div>

              </div>
              <div class="modal-footer">
                  <button class="btn btn-secondary"
                  data-dismiss="modal">
                  close
                  </button>

              </div>
          </div>
      </div>
  </div>

@endsection
