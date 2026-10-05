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
		<h3 class="box-title">Create Employee Wise Holiday Against Leave</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


    {!! Form::open(array('route'=>'holidayagainstleave.store', 'onkeypress'=> "return event.keyCode != 13;", 'files'=>true, 'id'=>'frm_employeeleave')) !!}
    {{ csrf_field() }}

	<div class="box-body">
		<div class="row">


			<div class="col-lg-8 col-md-8 col-xs-12 personal-info">




		        <div class="form-group has-feedback {{ $errors->has('year') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Year</label>
		            <div class="col-lg-9">
		  	          
						<select style="width: 100%;" class="form-control year" id="year" name="year" required>
						</select>

			            @if ($errors->has('year'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('year') }}</strong>
			                </span>
			            @endif 	                
		            </div>
		        </div>

		       <div class="form-group has-feedback {{ $errors->has('month') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Month  Name</label>
		            <div class="col-lg-9">
		  	          
                   <select  class="form-control col-lg-12" id="month_name" name="month_name" style="width: 100%;" >
                    @foreach ($month as $keys)
                    <option value={{$keys->id}}>{{$keys->month_name}}</option>
                    @endforeach
                    </select> 

			            @if ($errors->has('month'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('month') }}</strong>
			                </span>
			            @endif 	                
		            </div>
		        </div>





		        <div class="form-group has-feedback {{ $errors->has('employee_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
		            <label class="col-lg-3 control-label">Employee Name</label>
		            <div class="col-lg-9">
		  	          
						<select style="width: 100%;" class="form-control select2" id="employee_name" name="employee_name" required>
						</select>

			            @if ($errors->has('employee_name'))
			                <span class="help-block">
			                    <strong>{{ $errors->first('employee_name') }}</strong>
			                </span>
			            @endif 	                
		            </div>
		        </div>

	          <div class="form-group">
	            <input type="button" id="search" value="Search Work On Holiday" class="btn-sm block btn-flat btn" style="margin-left: 597px; border: .5px solid" >                 
	          </div>



			<div class="col-lg-12 col-md-12 col-xs-12" >
				
					<div class="form-group col-lg-12 col-md-12 col-xs-12">
						<table id="list_table" class=" table table-bordered table-hover" cellspacing="0" width="100%">
							<thead style="background-color: #3C8DBC; color: white; ">
								<tr>
									<th style="width: 5%">Select</th>
									<th style="width: 20%">Date</th>
									<th style="width: 15%">Holiday</th>
									<th style="width: 20%">In Time</th>
									<th style="width: 20%">Out Time</th>
									<th style="width: 25%">Over Time</th>
								</tr>
							</thead>
							<tbody ">
							</tbody>
						</table>
					
					
				</div>
				
			</div>


			<div class="form-group">
				<label class="col-md-3 control-label"></label>
				<div class="col-md-8">
					<input type="submit" class="btn btn-success block btn-flat" value="Submit" style="margin-left: 470px; border: .5px solid">
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
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>

<script src="{{asset('js/fileinput.js')}}"></script>
<script>
$(document).ready(function($) {


  for (i = new Date().getFullYear(); i > 2015; i--){
    $('.year').append($('<option />').val(i).html(i));
  }


    $("#search").click(function(){
      dataLoad();
    });


    dataLoad = function(){


		    $.ajax({
						type:   'POST',
						url :   "{{URL::to('/')}}/employeewiseholidaylist",
						headers:{
						'X-CSRF-TOKEN': '{{ csrf_token() }}'
						},

				        data:   {
				           year: $("#year").val(),
				           month: $("#month_name").val(),
				           employee_name: $("#employee_name").val(),
				           
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

										
										{ "data": "checkbox",
												"mRender": function (data, type, full) {
												return '<input type="checkbox" name="selecteddate[]" value="'+full.punche_date+'">';
											}
										},
										{ "data": "punche_date" },
										{ "data": "dayName" },
										{ "data": "in_time" },
										{ "data": "out_time" },
										{ "data": "overtime_time" },

							],
							"order": [[0,'asc']]
						});
				}
			});

    }


   $('#employee_name').select2({
    	placeholder: 'Enter an Employee Name',
    	allowClear: true,
    		ajax: {
		        dataType: 'json',
		        url: "{{URL::to('/')}}/join_employee_list",
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




});
</script>
@endsection
