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
		<h3 class="box-title">Reset Password</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


	<form class="form-horizontal" id="identicalForm" method="POST" action="{{url('password_reset')}}">
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
		
							<input type="hidden" name="user_id" value="{{$user_data[0]->id}}">


							<div class="col-md-12 col-xs-12">
								<h2><label >{{$user_data[0]->name}}</label>	</h2>
							</div>
							<div class="col-xs-12">
								<h3><label >{{$user_data[0]->email}}</label>	</h3>
							</div>

							<div class="col-xs-12">
								<h3><label >{{$user_data[0]->designation}}</label>	</h3>
							</div>



							<div class="col-xs-12 form-group has-feedback {{ $errors->has('password') ? ' has-error' : '' }}">
								<label control-label">Password(New)</label>
								<input type="password" class="form-control" name="password" placeholder="Password at least 6 characters"  required>
							</div>
							<div class="col-xs-12 form-group has-feedback {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
								<label control-label">Confirm Password</label>
								<input type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password"  required>
							</div>

						

						<div>
							 <input type="submit" class="btn btn-success btn-flat pull-right mybutton" value="Submit" >
						</div>

					</div>

				</div>
			</div>


 

		</div>
	</form>
</div>
@endsection
@section('script')
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/bootstrapvalidator/bootstrapValidator.min.js')}}"></script>

<script>
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