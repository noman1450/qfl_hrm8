<!-- shift_list -->

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
		<h3 class="box-title">Employee Current Shift List</h3>


	    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">

      {{-- @permission('EmployeeShiftChange') --}}
        <a href="{{ URL::to('change_employeeshift')}}" > <input type="button" value="Change Employee Shift." class="btn-success btn-sm block btn-flat btn" style="margin-right: 15px;"></a>
      {{-- @endpermission --}}

      {{-- @permission('EmployeeShiftChangeMultiple') --}}
        <a href="{{ URL::to('changeshift_multiple')}}" > <input type="button" value="Change Shift Multiple" class="btn-success btn-sm block btn-flat btn" style="margin-right: 15px;" ></a>
      {{-- @endpermission --}}


        <a href="{{ URL::to('employeeshiftlist_old')}}">    <input type="button" value="Old Shift"   class="btn-info btn-sm block btn-flat btn"></a>

        <select id="location" name="location" class="form-control" style="width: 235px; display: inline;"  required>
            @foreach ($default_user_location as $keys)
                <option value={{$keys->id}} selected>{{$keys->location_name}}</option>
            @endforeach
        </select>


      </div>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


	<div class="box-body" >
    <div style="overflow: auto;">
		<table id="designation_list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
			<thead>
				<tr>
                    <th></th>
                    <th style="width: 20%">Employee Name</th>
                    <th style="width: 15%">Department</th>
                    <th style="width: 20%">Designation</th>
					<th style="width: 13%">Shift Name</th>
                    <th style="width: 12%">Start Date</th>
					<th style="width: 10%">Start Time</th>
                    <th style="width: 10%">End Time</th>
					<th style="width: 10%">Edit</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
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
          <h3 class="modal-title">Employee Shift Date Change</h3>
        </div>
        <div class="modal-body">
          {!! Form::open(array('url' => 'shiftdatechange_single', 'id' => 'add-account-group-form')) !!}
          <div class="row">
            <div class="col-md-6">

                  <div class="form-group">
                      <div class="col-md-12">
                         <label class="control-label">Employee Name</label>
                      </div>
                      <input class="form-control"  id="employee_name" type="text"   readonly>
                  </div>

                  <div class="form-group">
                      <div class="col-md-12">
                          <label class="control-label">Department</label>
                      </div>
                      <input class="form-control" type="text" id="department_name"  readonly>
                  </div>

                  <div class="form-group">
                      <div class="col-md-12">
                        <label class="control-label">Designation</label>
                      </div>
                      <input class="form-control" type="text" id="designation_name"  readonly>
                  </div>

            </div>

            <div class="col-md-6">

                  <div class="form-group">
                      <div class="col-md-12">
                        <label class="control-label">Start Date</label>
                      </div>
                       <input type="text" class="form-control pull-right" id="start_date" name="start_date"  placeholder="yyyy-mm-dd" required >

                  </div>

            </div>
            <input type="text" id="hrm_employee_shift_id" name="hrm_employee_shift_id"  hidden>
          </div>

          <div class="modal-footer">
            <div class="col-lg-12 entry_panel_body ">
              <button type="submit" name="action" value="2" class="btn btn-success btn-flat" id="add-account-group">Submit</button>
            </div>
          </div>

          {!! Form::close() !!}
        </div>
      </div>
    </div>
  </div>
<!-- End Modal -->






@endsection
<!-- 175.29.166.86 -->


<!-- script -->
@section('script')
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('bootstrap/js/bootstrap.js')}}"></script>



<script>
$(document).ready(function($) {


dataLoad = function(){

          var table = $('#designation_list_table').DataTable( {
            "destroy":    true,
            "processing": true,
            "serverSide": true,
            "searching":  true,
            "ordering":   true,
            "bInfo":      true,
            "paging":     true,
            "aoColumnDefs": [{ "bVisible": false, "aTargets": [0] }],
            "ajax": {
                "url": "{{URL::to('/')}}/employeecurrentshiftlist",
                "type": "POST",
                "headers":{'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                "data":   {location: $("#location").val()}
            },
            "columns": [
              { "data": "employee_name" },
              { "data": "Link",
                "mRender": function (data, type, full) {
                    return '<a target="_blank"  href="{{URL::to('/')}}/employeeinfo/'+full.id+'">'+full.employee_name+'</a>';
                }
              },
              { "data": "depertment_name" },
              { "data": "designation_name" },
              { "data": "shift_name" },
              { "data": "start_date" },
              { "data": "start_time" },
              { "data": "end_time" },
              { "data": "Link",
              "mRender": function (data, type, full) {

                if(full.new_join_employee == 'NewJoin' ){
                    return '<a data-id="'+full.id+'"  data-employee_name="'+full.employee_name+'" data-depertment_name="'+full.depertment_name+'" data-designation_name="'+full.designation_name+'"  data-start_date="'+full.start_date+'" data-hrm_employee_shift_id="'+full.hrm_employee_shift_id+'"    class="btn btn-primary btn-single btn-sm showme">Edit</a>';
                } else {
                    return '';
                }
              }
            }
             ],
            "order": [[0, 'asc']]
          });


       }

      $('#designation_list_table').on('click', '.showme', function(e){
        $('#employee_name').val($(this).data('employee_name'));
        $('#department_name').val($(this).data('depertment_name'));
        $('#hrm_employee_shift_id').val($(this).data('hrm_employee_shift_id'));
        $('#designation_name').val($(this).data('designation_name'));
        $('#start_date').val($(this).data('start_date'));
        $('#modal_form').modal('show');
      });


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



});

</script>















@endsection
