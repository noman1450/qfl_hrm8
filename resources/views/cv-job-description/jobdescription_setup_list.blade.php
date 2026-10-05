@extends('layouts.main')

@section('styles')
  <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
  <style>

  body {font-family: Arial;}

  /* Style the tab */
  .tab {
    overflow: hidden;
    border: 1px solid #ccc;
    background-color: #f1f1f1;
  }

  /* Style the buttons inside the tab */
  .tab button {
    background-color: inherit;
    float: left;
    border: none;
    outline: none;
    cursor: pointer;
    padding: 14px 16px;
    transition: 0.3s;
    font-size: 17px;
  }

  /* Change background color of buttons on hover */
  .tab button:hover {
    background-color: #ddd;
  }

  /* Create an active/current tablink class */
  .tab button.active {
    background-color: #ccc;
  }

  /* Style the tab content */
  .tabcontent {
    display: none;
    padding: 6px 12px;
    border: 1px solid #ccc;
    border-top: none;
  }
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
@stop

@section('content')

  <div class="box box-default">
    <div class="box-body">
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          	<li class="active">
			  	<a href="#jobDescription" data-toggle="tab" aria-expanded="true">
				  Job Description
				</a>
			</li>
          	<li>
				<a href="#jobDescriptionGroup" data-toggle="tab" aria-expanded="false">
					Job Description Group Setup
				</a>
			</li>

			<li>
				<a href="#Compensation" data-toggle="tab" aria-expanded="false">
					Compensation
				</a>
			</li>
        </ul>

        <div class="tab-content">
          <div class="tab-pane active" id="jobDescription">
            <div class="box-body">
              <div class="row">
                <div class="box-header with-border">
                	<button button type="button" class="btn-success btn btn-sm button pull-left" data-toggle="modal" data-target="#jobDescriptionModal" style="font-size: 12px; font-weight: bold">Add Job Description</button>
                </div>

                <div class="modal fade" id="jobDescriptionModal" role="dialog" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
						{!! Form::open(['method'=>'POST', 'action'=>['CVJobDescriptionController@store'], 'onkeypress' => "return event.keyCode != 13;", 'id'=>'jobDescriptionForm']) !!}
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<h4 class="modal-title" id="groupAddLabel">Add/Update</h4>
							</div>
							<div class="modal-body" style="padding: 0px;">
								<div class="col-lg-12 entry_panel_body ">
									<input type="hidden" class="form-control" id="desc_id" name="desc_id"/>
								</div>
							</div>

							<div class="modal-body" style="margin-top: -20px">
								<label>Preferred Department <span class="text-danger">*</span></label>
								<select class="form-control select2" name="hrm_depertment_id" id="hrm_depertment_id" required>
								</select>
							</div>


							<div class="modal-body" style="margin-top: -20px">
								<label>Description Group <span class="text-danger">*</span></label>
								<select class="form-control select2" name="hrm_cv_job_description_group_id" id="hrm_cv_job_description_group_id" required>
								</select>
							</div>

							<div class="modal-body" style="margin-top: -20px">
								<label>Job Description <span class="text-danger">*</span></label>
								<textarea class="form-control" id="job_description" name="job_description" placeholder="Job Description..." maxlength="245" required></textarea>
							</div>

							<div class="modal-footer">
								<div style="display:flex;align-items:center;justify-content:space-between">
									<div class="checkbox">
										<label>
										  <input type="checkbox" id="allow_points_calculation" checked name="allow_points_calculation" style="margin-top: 2px"> Allow points calculation
										</label>
									  </div>

									  <div>
										<button type="button" class="btn btn-default closeId" data-dismiss="modal">Close</button>
										<input type="submit" class="btn btn-success btn-flat pull-right" value="Submit" id="jobdsBtn">
									  </div>
								</div>
							</div>
						{!! Form::close() !!}
                    </div>
                  </div>
                </div>
              </div>
			</div>

			<div class="box-body">
				<div class="row">
				  <div class="col-md-12">
					  <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">
						  <table id="jobDescriptionDatatable" class="table table-bordered table-hover" cellspacing="0" width="100%">
							<thead>
							  <tr>
								<th style="width: 20%">Job Group</th>
								<th style="width: 30%">Description</th>
								<th style="width: 20%">Pre.Department</th>
								<th style="width: 10%">Allow Points</th>
								<th style="width: 10%">Actions</th>
							  </tr>
							</thead>
							<tbody>
							</tbody>
						  </table>
						</div>
				  </div>
				</div>
			  </div>
          </div>

          <div class="tab-pane" id="jobDescriptionGroup">
            <div class="box-body">
              <div class="row">
                <div class="box-header with-border">
                	<button button type="button" class="btn-success btn btn-sm button pull-left" data-toggle="modal" data-target="#jobDescriptionGroupModal" data-whatever="@mdo" style="font-size: 12px; font-weight: bold">Add Job Description Group</button>
                </div>

                <div class="modal fade" id="jobDescriptionGroupModal" tabindex="-1" role="dialog" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
						{!! Form::open(['method'=>'POST', 'action'=>['CVJobDescriptionGroupSetupController@store'], 'onkeypress'=> "return event.keyCode != 13;", 'id'=>'jobDescriptionGroupForm']) !!}
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<h4 class="modal-title" id="groupAddLabel">Add/Update</h4>
							</div>
							<div class="modal-body" style="padding: 0px;">
								<div class="col-lg-12 entry_panel_body ">
									<input type="hidden" class="form-control" id="job_description_id" name="job_description_id"/>
								</div>
							</div>
							<div class="modal-body" style="padding: 0px;">
								<div class="col-lg-12 entry_panel_body ">
								<input type="text" class="form-control" id="job_description_group_name" name="job_description_group_name" placeholder="Job Description Group Name" style="width: 100%; margin-left: 5px; margin-top: 10px; margin-bottom: 10px;" required/>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default closeId" data-dismiss="modal">Close</button>
								<input type="submit" class="btn btn-success btn-flat pull-right" value="Submit" id="btnSubmit">
							</div>
						{!! Form::close() !!}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="box-body">
              <div class="row">
                <div class="col-md-6">
					<div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">
						<table id="jobDescriptionGroupDatatable" class="table table-bordered table-hover" cellspacing="0" width="100%">
						  <thead>
							<tr>
							  <th style="width: 35%">Job Description</th>
							  <th style="width: 25%">Actions</th>
							</tr>
						  </thead>
						  <tbody>
						  </tbody>
						</table>
					  </div>
                </div>
              </div>
            </div>
		  </div>


		  <div class="tab-pane" id="Compensation">
            <div class="box-body">
              <div class="row">
                <div class="box-header with-border">
					<button button type="button" class="btn-success btn btn-sm button pull-left" data-toggle="modal" data-target="#CompensationModal" style="font-size: 12px; font-weight: bold">
						Add Compensation
					</button>
                </div>

                <div class="modal fade" id="CompensationModal" role="dialog" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
						{!! Form::open(['method'=>'POST', 'action'=>['CVJobDescriptionGroupSetupController@compensationStore'], 'onkeypress'=> "return event.keyCode != 13;", 'id'=>'CompensationForm']) !!}
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<h4 class="modal-title" id="CompensationAddLabel">Add/Update</h4>
							</div>
							<div class="modal-body" style="padding: 0px;">
								<div class="col-lg-12 entry_panel_body ">
									<input type="hidden" class="form-control" id="compensation_id" name="compensation_id"/>
								</div>
							</div>
							<div class="modal-body" style="padding: 0px;">
								<div class="col-lg-12 entry_panel_body ">
									<input type="text" class="form-control" id="compensation_name" name="compensation_name" placeholder="Compensation Name" style="width: 100%; margin-left: 5px; margin-top: 10px; margin-bottom: 10px;" required/>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default closeId" data-dismiss="modal">Close</button>
								<input type="submit" class="btn btn-success btn-flat pull-right" value="Submit" id="CompensationBtn">
							</div>
						{!! Form::close() !!}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="box-body">
              <div class="row">
                <div class="col-md-6">
					<div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">
						<table id="compensationDatatable" class="table table-bordered table-hover" cellspacing="0" width="100%">
						  <thead>
							<tr>
							  <th style="width: 35%">Compensation</th>
							  <th style="width: 25%">Actions</th>
							</tr>
						  </thead>
						  <tbody>
						  </tbody>
						</table>
					</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@stop

@section('script')
  <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
  <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

  <script>

	// Start Job Description Group Setup =========>
    $('#jobDescriptionGroupForm').on('submit',(function(e) {
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
            if (data.success == true) {
                $('#btnSubmit').attr("disabled", false);
                $("#btnSubmit").val('Submit');
				alert(data.message);

				$('#job_description_id').val("");
				$('#job_description_group_name').val("");

				jobDescriptionList();

				$('#jobDescriptionGroupModal').modal('hide')

            } else {
				$('#btnSubmit').attr("disabled", false);
				$("#btnSubmit").val('Submit');
            }
          }
      });
	}));


	// For Edit data when show modal
	$('#jobDescriptionGroupDatatable').on('click', '.showme', function() {
		$('#job_description_id').val($(this).data('job_description_id'));
		$('#job_description_group_name').val($(this).data('job_description_group_name'));
		$('#jobDescriptionGroupModal').modal('show');
	});

	// Empty form when modal close
	$('#jobDescriptionGroupModal').on('hidden.bs.modal', function() {
		$('#job_description_id').val("");
		$('#job_description_group_name').val("");
    })

	// Call the datatable..
	function jobDescriptionList() {
		$('#jobDescriptionGroupDatatable').DataTable({
			destroy:    true,
			responsive:     true,
			processing:     true,
			serverSide:     true,
			paging:         true,
			lengthChange:   true,
			searching:      true,
			ordering:       true,
			info:           true,
			autoWidth:      false,
			width:          "100%",

			ajax: {
				url: "{{ route('job-decription-group-list-data') }}",
				type: "POST",
				headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
				dataType: "json",
			},
			columns: [
				{ data: "job_description_group_name" },
				{ "data": "Link",
					"mRender": function(data, type, full) {
						return '<a  data-job_description_id="' + full.id + '" data-job_description_group_name="' + full.job_description_group_name + '"  class="btn btn-primary btn-flat btn-sm showme"> <span class="glyphicon glyphicon-edit">Edit</a>';
					}
				},
				// { "data": "Link",
				// 	"mRender": function (data, type, full) {
				// 	return '<form action="{{URL::to('/')}}/asset_type/'+full.id+'"  method="POST"> {{ csrf_field() }} {{ method_field('DELETE') }} <button type="submit" class="btn btn-danger" title="delete" onclick="return confirm(&#39;Are you sure you want to delete this item?&#39;);">Delete</button></form>';
				// 	}
				// },
			],
			order: [[0, 'desc']]
		});
	};

	jobDescriptionList();

	// End Job Description Group Setup =========>


	// ================================================================== //


	// Start Job Description ==========>

	$('#jobDescriptionForm').on('submit',(function(e) {
      e.preventDefault();
      $("#jobdsBtn").attr("disabled", true);
      $("#jobdsBtn").val('Please wait..');

      var formData = new FormData(this);

      $.ajax({
          type:'POST',
          url: $(this).attr('action'),
          data:formData,
          cache:false,
          contentType: false,
          processData: false,
          success:function(data){
            if (data.success == true) {
                $('#jobdsBtn').attr("disabled", false);
                $("#jobdsBtn").val('Submit');
				alert(data.message);

				$('#desc_id').val("");
				$('#hrm_depertment_id').val("");
				$('#hrm_depertment_id').text("");

				$('#hrm_cv_job_description_group_id').val("");
				$('#hrm_cv_job_description_group_id').text("");
				$('#job_description').val("");
				// $('#allow_points_calculation').val(1);

				jobDescriptionData();

				$('#jobDescriptionModal').modal('hide')

            } else {
				$('#jobdsBtn').attr("disabled", false);
				$("#jobdsBtn").val('Submit');
            }
          }
      });
	}));

	var $group_state = $('#hrm_cv_job_description_group_id').select2({
		placeholder: "Search Job Description Group",
		width: '100%',
		allowClear: true,
		ajax: {
			dataType: 'json',
			url: "{{ route('get-all-job-description-group') }}",
			delay: 100,
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
		},
    });

	var $department_state = $('#hrm_depertment_id').select2({
		placeholder: 'Search Department',
		width: '100%',
		allowClear: true,
		ajax: {
			dataType: 'json',
			url: '{{ URL::to('/') }}/depertment_list_data',
			headers:{ 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
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


	// Empty form when modal close
	$('#jobDescriptionModal').on('hidden.bs.modal', function () {
		$('#desc_id').val("");

		$('#hrm_depertment_id').val("");
		$('#hrm_depertment_id').text("");

		$('#hrm_cv_job_description_group_id').val("");
		$('#hrm_cv_job_description_group_id').text("");

		$('#job_description').val("");
		$("#jobdsBtn").attr("disabled", false);
		$("#jobdsBtn").val('Submit');
	})


	function jobDescriptionData() {
		$('#jobDescriptionDatatable').DataTable({
			destroy:    true,
			responsive:     true,
			processing:     true,
			serverSide:     true,
			paging:         true,
			lengthChange:   true,
			searching:      true,
			ordering:       true,
			info:           true,
			autoWidth:      false,
			width:          "100%",

			ajax: {
				url: "{{ route('job-decription-list-data') }}",
				type: "POST",
				headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
				dataType: "json",
			},
			columns: [
				{ data: "job_description_group_name" },
				{ data: "job_description" },
				{ data: "depertment_name" },
				{ data: "status_lable" },
				{ "data": "Link",
					"mRender": function(data, type, full) {
						return '<a  data-allow_points_calculation="'+ full.allow_points_calculation +'" data-desc_id="' + full.id + '" data-job_description="' + full.job_description + '" data-hrm_cv_job_description_group_id="'+ full.hrm_cv_job_description_group_id +'" data-job_description_group_name="'+ full.job_description_group_name +'" data-depertment_name="'+ full.depertment_name +'" data-hrm_depertment_id="'+ full.hrm_depertment_id +'"  class="btn btn-primary btn-sm showme"> <span class="glyphicon glyphicon-edit"></a>'+
							'<form action="{{ URL::to('/') }}/job-description-delete/'+full.id+'"  method="POST" style="display:inline"> {{ csrf_field() }} {{ method_field('DELETE') }} <button type="submit" class="btn btn-danger btn-sm" title="delete" onclick="return confirm(&#39;Are you sure you want to delete this item?&#39;);"><span class="glyphicon glyphicon-trash"></button></form>';
					}
				},
			],
			order: [[0, 'desc']]
		});

		// For Edit data when show modal
		$('#jobDescriptionDatatable').on('click', '.showme', function() {

			$('#desc_id').val($(this).data('desc_id'));
			$('#job_description').val($(this).data('job_description'));
			var point = $(this).data('allow_points_calculation');



			if (point === 1) {
				document.getElementById("allow_points_calculation").checked = true;
			} else {
				document.getElementById("allow_points_calculation").checked = false;
			}


			var $group_option_state = $('<option selected>'+$(this).data('job_description_group_name')+'</option>').val($(this).data('hrm_cv_job_description_group_id'));
			var $department_option_state = $('<option selected>'+$(this).data('depertment_name')+'</option>').val($(this).data('hrm_depertment_id'));

			$group_state.append($group_option_state).trigger('change');
			$department_state.append($department_option_state).trigger('change');

			$('#jobDescriptionModal').modal('show');
		});
	};

	jobDescriptionData();


	// ==================================>

	// Start Compensation setup
	$('#CompensationForm').on('submit', function (e) {
      	e.preventDefault();
      	$("#CompensationBtn").attr("disabled", true);
      	$("#CompensationBtn").val('Please wait..');

      	var formData = new FormData(this);

      	$.ajax({
          	type:'POST',
          	url: $(this).attr('action'),
          	data:formData,
          	cache:false,
          	contentType: false,
          	processData: false,
          	success:function(data){
            if (data.success == true) {
                $('#CompensationBtn').attr("disabled", false);
                $("#CompensationBtn").val('Submit');
				alert(data.message);

				$('#compensation_id').val("");
				$('#compensation_name').val("");

				compensationLoadData();

				$('#CompensationModal').modal('hide')

            } else {
				$('#CompensationBtn').attr("disabled", false);
				$("#CompensationBtn").val('Submit');
            }
          }
      	});
	});

	function compensationLoadData() {
		$('#compensationDatatable').DataTable({
			destroy:    	true,
			responsive:     true,
			processing:     true,
			serverSide:     true,
			paging:         true,
			lengthChange:   true,
			searching:      true,
			ordering:       true,
			info:           true,
			autoWidth:      false,
			width:          "100%",

			ajax: {
				url: "{{ route('job-compensation-list-data') }}",
				type: "POST",
				headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
				dataType: "json",
			},
			columns: [
				{ data: "compensation_name" },
				{ "data": "Link",
					"mRender": function(data, type, full) {
						return '<a  data-compensation_name="'+ full.compensation_name +'" data-compensation_id="' + full.id + '" class="btn btn-primary btn-sm showme"> <span class="glyphicon glyphicon-edit"></a>';
					}
				},
			],
			order: [[0, 'desc']]
		});

		// For Edit data when show modal
		$('#compensationDatatable').on('click', '.showme', function() {

			$('#compensation_id').val($(this).data('compensation_id'));
			$('#compensation_name').val($(this).data('compensation_name'));

			$('#CompensationModal').modal('show');
		});
	};

	compensationLoadData();
</script>
@stop
