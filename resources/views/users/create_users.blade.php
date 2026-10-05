<!-- create_employee -->
<!-- create_marital_status -->
@extends('layouts.main')
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/bootstrapvalidator/bootstrapValidator.min.css')}}">

@endsection

@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Create New User</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


	<form class="form-horizontal" id="identicalForm"  method="POST" action="{{url('users')}}">
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
								<input type="text" class="form-control" name="username" placeholder="User Name"  required>
							</div>


							<div class="col-xs-12 form-group has-feedback {{ $errors->has('designation') ? ' has-error' : '' }}">
								<label class="control-label">Designation</label>
								<input type="text" class="form-control" name="designation" placeholder="Designation"  required>
							</div>


							<div class="col-xs-12 form-group has-feedback {{ $errors->has('email') ? ' has-error' : '' }}">
								<label class="control-label">Email</label>
								<input type="text" class="form-control" name="email" placeholder="Email" required>
							</div>


							<div class="col-xs-12 form-group has-feedback {{ $errors->has('password') ? ' has-error' : '' }}">
								<label class="control-label">Password</label>
								<input type="password" class="form-control" name="password" placeholder="Password at least 6 characters"  required>
							</div>
							<div class="col-xs-12 form-group has-feedback {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
								<label class="control-label">Confirm Password</label>
								<input type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password"  required>
							</div>


							<div class="col-xs-12 form-group has-feedback {{ $errors->has('employee_name') ? ' has-error' : '' }}">
								<label class="control-label">Employee Name</label>
								<select class="form-control" id="employee_name" name="employee_name" required>
								</select>
							</div>


							<div class="col-md-6 form-group has-feedback {{ $errors->has('userrole') ? ' has-error' : '' }}">
								<label class="control-label">User Role</label>
                                <select class="form-control" id="userrole" name="userrole" required>
                                    <option>-- Select Role --</option>
                                    @foreach ($role_list as $keys)
                                        <option value={{$keys->id}}>{{$keys->display_name}}</option>
                                    @endforeach
								</select>
							</div>

                            <div class="col-md-6 form-group has-feedback">
								<label class="control-label">User Type</label>
                                <select class="form-control" id="user_type" name="user_type" required>
                                    <option value="1">General-User</option>
                                    <option value="2">Super-Admin(Access All)</option>
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
				 <input type="submit" class="btn btn-success btn-flat pull-right mybutton" value="Submit" style="margin-right: 70px;">
			</div>


		</div>
	</form>
</div>
@endsection
@section('script')
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<!-- <script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script> -->
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('js/fileinput.js')}}"></script>
<script src="{{asset('plugins/bootstrapvalidator/bootstrapValidator.min.js')}}"></script>

<script>
$(document).ready(function($) {
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



    $.ajax({
        type:   'POST',
        url :   "{{URL::to('/')}}/location_list",
        headers:{
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
                { "data": "location_name" },
                { "data": "checkbox",
                    "mRender": function (data, type, full) {
                        return '<input type="checkbox" name="permissionlocation[]" value="'+full.id+'" class="location">';
                    }
                },
                { "data": "radio",
                    "mRender": function (data, type, full) {
                        return '<input type="radio" name="defaultlocation[]" value="'+full.id+'" class="default">';
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
});


$(document).ready(function() {
    $('#identicalForm').bootstrapValidator({
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            password: {
                validators: {
                    identical: {
                        field: 'password_confirmation',
                        message: 'The password and its confirm are not the same'
                    },
                    stringLength: {
                        min: 6,
                        message: 'Password minimum 6 characters'
                    }
                }
            },
            password_confirmation: {
                validators: {
                    identical: {
                        field: 'password',
                        message: 'The password and its confirm are not the same'
                    },
                    stringLength: {
                        min: 6,
                        message: 'Password minimum 6 characters'
                    }
                }
            }
        }
    });


});


</script>



@endsection
