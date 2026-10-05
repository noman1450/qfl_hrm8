<!-- employee_list -->
@extends('layouts.main')

<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

@endsection

<!-- content -->
@section('content')
<div class="box box-default">

	<div class="box-header with-border">
		<h3 class="box-title">Employee Resignation List</h3>
	    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
	        {{-- @permission('EmployeeResignCreate') --}}
          <a href="{{ URL::to('employeeresign/create')}}"><input type="button" value="Create New" class="btn-success btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold;"></a>
          {{-- @endpermission --}}
          <a href="{{ URL::to('resignapproved')}}"><input type="button" value="Approved List" class="btn-info btn btn-sm button pull-left btn-flat" style="font-size: 12px;  margin-left: 20px; font-weight: bold;"></a>
          <div class="col-lg-3 col-md-3 col-xs-12 form-group">
            <select class="form-control" id="location" name="location" style="width: 100%;" required>

                @foreach ($default_user_location as $keys)
                      <option value={{$keys->id}} >{{$keys->location_name}}</option>
                @endforeach

            </select>
          </div>
      </div>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


	<div class="box-body">
		<div class="row">
	        <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">
				<table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th></th>
							<th style="width: 20%">Employee Name</th>
							<th style="width: 12%">Department</th>
							<th style="width: 12%">Resignation Type</th>
                            <th style="width: 12%">Designation</th>
                            <th style="width: 12%">Apply Date</th>
							<th style="width: 15%">Effictive From</th>
							<th style="width: 18%">Comment</th>
                            {{-- @permission('EmployeeResignApprove') --}}
                            <th style="width: 10%">Action</th>
                            {{-- @endpermission --}}
                            {{-- @permission('EmployeeResignCreate') --}}
                                            <th style="width: 10%">Delete</th>
                            {{-- @endpermission --}}
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>

</div>







<!-- Start Modal -->
  <div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" style="border-bottom: 0px;height: 50px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
          <h3 class="modal-title">Resign Approve Form</h3>
        </div>
        <div class="modal-body" style="padding-bottom:0px;">
          {!! Form::open(array('url' => 'resignapprovesubmit', 'id' => 'add-account-group-form')) !!}

            <input type="hidden" name="employee_id"  id="employee_id">
            <input type="text" id="id" name="id"  hidden>

            <div class="panel" style="border: 1px solid #00000012; box-shadow: none;">
                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-1" style="margin-left: 38%;">
                            <div style="display: flex;column-gap: 20px;">
                                <img src="" alt="" style="height: 70px; width: 70px; border-radius: 9999px;" id="Images">
                            </div>
                        </div>
                    </div>

                    <hr style="margin-top: 10px; margin-bottom: 10px;">
                    <div class="row">
                        <div class="col-md-4"> Name </div>
                        <div class="col-md-8">
                            <div style="display: flex; align-items: center; column-gap: 20px;">
                                <div style="display: flex; flex-direction: column; row-gap: 5px;">
                                    <strong style="font-size: 14px" id="employee_name"></strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr style="margin-top: 10px; margin-bottom: 10px;">
                    <div class="row">
                        <div class="col-md-4">  Department </div>
                        <div class="col-md-8">
                            <strong style="font-size: 14px" id="department_name"></strong>
                        </div>
                    </div>

                    <hr style="margin-top: 10px; margin-bottom: 10px;">
                    <div class="row">
                        <div class="col-md-4">   Designation </div>
                        <div class="col-md-8">
                            <strong style="font-size: 14px" id="designation_name"></strong>
                        </div>
                    </div>

                    <hr style="margin-top: 10px; margin-bottom: 10px;">
                    <div class="row">
                        <div class="col-md-4"> Location  </div>
                        <div class="col-md-8">
                            <strong style="font-size: 14px" id="location_name"></strong>
                        </div>
                    </div>

                    <hr style="margin-top: 10px; margin-bottom: 10px;">
                    <div class="row">
                        <div class="col-md-4">  Resignation Type </div>
                        <div class="col-md-8">
                            <strong style="font-size: 14px" id="resignation_type"></strong>
                        </div>
                    </div>

                    <hr style="margin-top: 10px; margin-bottom: 10px;">
                    <div class="row">
                        <div class="col-md-4"> Effective Date </div>
                        <div class="col-md-4">
                            <strong style="font-size: 14px; color: #0829df;" id="effective_date"></strong>
                            {{-- <input type="text" class="form-control pull-right" id="effective_date" name="effective_date"  placeholder="dd-mm-yyyy" required > --}}
                        </div>
                    </div>

                    <hr style="margin-top: 10px; margin-bottom: 10px;">
                    <div class="row">
                        <div class="col-md-4">  Comment  </div>
                        <div class="col-md-8">
                            <textarea type="text" class="form-control" name="comment" id="comment" placeholder="Comment"></textarea>
                        </div>
                    </div>
                </div>

                <hr style="margin-bottom: 0px; padding-bottom: 0px;">
                <div style="margin-top: 0px; padding-top: 0px;">
                        <h4 class="text-center" style="color: red; text-align: center;"><b>N.B. Loan Amount : <span id="total_remain_loan"></span></b> </h4>
                </div>

            </div>
          </div>

          <div class="modal-footer" style="margin-top: 0px; padding-top:0px;">
            <div class="col-lg-12 entry_panel_body ">
              <h5 style="color: red; text-align: left;font-size: 15px;">N.B. If Action is <b> Approved </b>, This Employee will be <b> permanently Deactive </b> right now, So carefully handle !!! </h5>

              <button type="submit" name="action" value="2" class="btn btn-success btn-flat" id="add-account-group">Aprove</button>
              <button type="submit" name="action" value="3" class="btn btn-danger btn-flat" id="add-account-group">Reject</button>
              <button type="submit" name="action" value="4" class="btn btn-info btn-flat" id="add-account-group">Aprove & Inter Company Transfer</button>


              <!-- <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Close</button> -->
            </div>
          </div>

          {!! Form::close() !!}
        </div>
      </div>
    </div>
  </div>
<!-- End Modal -->




@endsection



<!-- script -->
@section('script')
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('bootstrap/js/bootstrap.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<!-- <script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script> -->
<!-- <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script> -->


<script>
$(document).ready(function($) {

    // $('#effictive_date').datepicker({
    //   autoclose: true
    // });


 dataLoad = function(){
      $.ajax({
        type:   'POST',
        url :   "{{URL::to('/')}}/employeeresign_list",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
        data:   {
          location: $("#location").val(),
        },
        dataType: 'json',
        success: function(data) {
            var dataSet = data.data;
            table = $('#list_table').DataTable( {
              destroy:    true,
              paging:     true,
              searching:  true,
              ordering:   true,
              bInfo:      false,
              "data":     dataSet,

            "columns": [

              { "data": "Link",
                "mRender": function (data, type, full) {
                  return '<img src="{{asset('employee_image')}}/'+full.Images+'" style="height:30px; width:30px; border-radius: 30px;"/>';
                }
              },
              { "data": "Link",
                  "mRender": function (data, type, full) {
                    return '<a target="_blank" href="{{URL::to('/')}}/employeeinfo/'+full.employee_id+'">'+full.employee_name+'</a>';
                }
              },
              { "data": "depertment_name" },
              { "data": "resignation_type" },
              { "data": "designation_name" },
              { "data": "apply_date" },
              { "data": "effective_date" },
              { "data": "comment" },
            //   @permission('EmployeeResignApprove')
              { "data": "Link",

              "mRender": function (data, type, full) {
                return '<a  data-id="'+full.id+'"  data-employee_name="'+full.employee_name+'" data-location_name="'+full.location_name+'" data-depertment_name="'+full.depertment_name+'" data-designation_name="'+full.designation_name+'" data-resignation_type="'+full.resignation_type+'"  data-effective_date="'+full.effective_date+'" data-employee_id="'+full.employee_id+'" data-total_remain_loan="'+full.remaining_amount+'" data-Images="'+full.Images+'"   class="btn btn-primary btn-single btn-sm showme">Action</a>';
              }
            },
            // @endpermission
            // @permission('EmployeeResignCreate')
              { "data": "Link",
                "mRender": function (data, type, full) {
                    return '<a href="{{URL::to('/')}}/employeeresign/'+full.id+'/cancel"  onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-sm btn-flat"><span class="glyphicon glyphicon-trash"></span> Delete</a>';
                }
              },
            // @endpermission
            ],
            "order": [[0,'asc']]
            });
        }
      });
    }

      dataLoad();

             $role= $('#location').select2({
                placeholder: 'Choose Location',
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

              $role.on('select2:select', function (e) {
                  dataLoad();
              });

              $role.on('select2:unselect', function (e) {
                  $('#location').val(null).trigger("change");
                  dataLoad();
              });


         $('#list_table').on('click', '.showme', function(e){
            let imageUrl = $(this).data('images');
            $('#Images').attr('src', `{{ asset('employee_image') }}/${imageUrl}`);
            $('#employee_name').html($(this).data('employee_name'));
            $('#department_name').html($(this).data('depertment_name'));
            $('#leave_type').html($(this).data('leave_type'));
            $('#id').val($(this).data('id'));
            $('#designation_name').html($(this).data('designation_name'));
            $('#resignation_type').html($(this).data('resignation_type'));
            $('#location_name').html($(this).data('location_name'));
            $('#effective_date').html($(this).data('effective_date'));
            $('#employee_id').val($(this).data('employee_id'));
            $('#total_remain_loan').html($(this).data('total_remain_loan'));
            $('#modal_form').modal('show');
          });


});

</script>

@endsection
