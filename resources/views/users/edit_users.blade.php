<!-- create_employee -->
<!-- create_marital_status -->
@extends('layouts.main')
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@endsection

@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Edit User</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


	<!-- <form class="form-horizontal" method="POST" action="{{url('usersupdate')}}"> -->

    {!! Form::open(array('route' => array('users.update', $edit_data[0]->id), 'onkeypress'=> "return event.keyCode != 13;", 'id' => 'top-entrypanel-validation', 'method'=>'PUT')) !!}
    {{ csrf_field() }}

		{{ csrf_field() }}
		<div class="box-body">
			<div class="row">
				<div class="col-md-12">

					@if ($errors->any())
					    <div class="alert alert-danger">
					        <ul>
					            @foreach ($errors->all() as $error)
					                <li>{{ $error }}</li>
					            @endforeach
					        </ul>
					    </div>
					@endif
					<div class="col-md-6" >
						<div class="col-md-12">
							<div class="col-xs-12 form-group has-feedback {{ $errors->has('username') ? ' has-error' : '' }}">
								<label class="control-label">User Name</label>
								<input type="text" class="form-control" name="username" value="{{$edit_data[0]->name}}"  required>
							</div>
							<div class="col-xs-12 form-group has-feedback {{ $errors->has('email') ? ' has-error' : '' }}">
								<label class="control-label">Email</label>
								<input type="text" class="form-control" name="email" value="{{$edit_data[0]->email}}" required readonly>
							</div>

							<div class="col-xs-12 form-group has-feedback {{ $errors->has('designation') ? ' has-error' : '' }}">
								<label class="control-label">Designation</label>
								<input type="text" class="form-control" name="designation" value="{{$edit_data[0]->designation}}" >
							</div>


								<input type="text" id="user_id" name="user_id" value="{{$edit_data[0]->id}}" hidden>

							<div class="col-xs-12 form-group has-feedback {{ $errors->has('employee_name') ? ' has-error' : '' }}">
								<label class="control-label">Employee Name</label>
								<select class="form-control" id="employee_name" name="employee_name"  >
									<option value="{{$edit_data[0]->employee_id}}">{{$edit_data[0]->employee_name}}</option>
								</select>
							</div>


							<div class="col-md-6 form-group has-feedback {{ $errors->has('userrole') ? ' has-error' : '' }}">
								<label class="control-label">User Role</label>
									<select class="form-control" id="userrole" name="userrole" required>
									    <option></option>
										@foreach ($role_list as $keys)
											@if($edit_data[0]->User_role_id==$keys->id)
											<option value={{$keys->id}} selected>{{$keys->display_name}}</option>
											@else
											<option value={{$keys->id}}>{{$keys->display_name}}</option>
											@endif
										@endforeach
									</select>
							</div>

                            <div class="col-md-6 form-group has-feedback">
								<label class="control-label">User Type</label>
                                <select class="form-control" id="user_type" name="user_type" required>
                                    <option value="1" {{ $edit_data[0]->user_type == 1 ? 'selected' : null }}>General-User</option>
                                    <option value="2" {{ $edit_data[0]->user_type == 2 ? 'selected' : null }}>Super-Admin(Access All)</option>
								</select>
							</div>

						</div>
					</div>
					<div class="col-md-6" >
						<div class="col-md-12">
							<div class="form-group col-lg-12 col-md-12 col-xs-12">
								<table id="list_table" class=" table table-bordered table-hover" cellspacing="0" width="100%">
									<thead style="background-color: #3C8DBC; color: white; ">
										<tr>
											<th style="width: 60%">Branch Name</th>
											<th style="width: 20%">Permission</th>
											<th style="width: 20%">Default</th>
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

			<div>
				 <input type="submit" class="btn btn-success btn-flat pull-right mybutton" value="Update" style="margin-right: 70px;">
			</div>


		</div>
	{!! Form::close() !!}
</div>
@endsection
@section('script')
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<!-- <script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script> -->
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('js/fileinput.js')}}"></script>
<script>
$(document).ready(function($) {
			$.ajax({
				type:   'POST',
				url :   "{{URL::to('/')}}/userslocationlist",
				headers:{ 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
				data   :{ user_id: $("#user_id").val(),},
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
						{ "data": "location_name" },
						{ "data": "checkbox",
							"mRender": function (data, type, full) {
								if(full.users_id > 0 ) {
									return '<input type="checkbox"  name="permissionlocation[]" checked value="'+full.id+'">';
								} else {
									return '<input type="checkbox"  name="permissionlocation[]" value="'+full.id+'">';
								}
							}
						},
						{ "data": "radio",
							"mRender": function (data, type, full) {
							    if(full.default_location == 1 ) {
									return '<input type="radio" name="defaultlocation[]" checked="checked" value="'+full.id+'" class="location">';
								} else {
									return '<input type="radio" name="defaultlocation[]" value="'+full.id+'" class="default">';
								}
							}
						}
					],
					"order": [[0,'asc']]
					});
				}
			});

            $(document).on('click', '.location', function (e) {
                var $this = $(this);

                if (! $this.is(':checked')) {
                    $this.parents('tr').find('input:radio').not(this).prop('checked', false);
                }
            })

            $(document).on('click', '.default', function (e) {
                var $this = $(this);
                $this.parents('tr').find('input:checkbox').not(this).prop('checked', this.checked);
            })

		$('#employee_name').select2({
		      placeholder: 'Enter Employee Name',
		      allowClear: true,
		      ajax: {
		        dataType: 'json',
		        url: '{{URL::to('/')}}/join_employee_list',
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










});
</script>


@endsection
