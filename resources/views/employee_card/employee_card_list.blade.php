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
		<h3 class="box-title">Employee Attendance Punch/Card Code List</h3>
	    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
        {{-- @permission('EmployeeCardCreateEditDelete') --}}
	        <a href="{{ URL::to('employeecard/create')}}"><input type="button" value="Add Card Info" class="btn-success btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold;"></a>
	      {{-- @endpermission --}}
      </div>

		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


  <div class="col-lg-3 col-md-3 col-xs-12 form-group" >
    <select  class="form-control col-lg-12" id="location" name="location" style="width: 100%;"  required>

              @foreach ($default_user_location as $keys)
                  <option value={{$keys->id}} selected>{{$keys->location_name}}</option>
              @endforeach


    </select>
 </div>



	<div class="box-body">
		<div class="row">
	        <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">
				<table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%" >
					<thead>
						<tr>
							<th style="width: 5%"></th>
							<th style="width: 20%">Employee Name</th>
							<th style="width: 10%">Card Code</th>
                            <th style="width: 10%">Device Id</th>
                            <th style="width: 10%">Designation</th>
                            <th style="width: 10%">Department</th>
							<th style="width: 10%">Branch</th>
                            {{-- @permission('EmployeeCardCreateEditDelete') --}}
							<th style="width: 5%">Edit</th>
							<th style="width: 5%">Delete</th>
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

@endsection



<!-- script -->
@section('script')
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>


<script>
$(document).ready(function($) {

    dataLoad = function(){
        $.ajax({
            type:   'POST',
            url :   "{{URL::to('/')}}/employeecard_list",
            headers:{
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
            data:   {
            location: $("#location").val(),
            },
            dataType: 'json',
            success: function(data) {
                var dataSet = data.data;
                table = $('#list_table').DataTable({
                    destroy:    true,
                    paging:     true,
                    searching:  true,
                    ordering:   true,
                    bInfo:      true,
                    "data":     dataSet,

                    "columns": [

                    { "data": "Link",
                        "mRender": function (data, type, full) {
                            return '<img src="{{asset('employee_image')}}/'+full.Images+'" style="height:30px; width:30px; border-radius: 30px;"/>';
                        }
                    },
                    { "data": "Link",
                        "mRender": function (data, type, full) {
                            return '<a target="_blank" href="{{URL::to('/')}}/employeeinfo/'+full.id+'">'+full.employee_name+'</a>';
                        }
                    },
                    { "data": "card_code" },
                    { "data": "device_id" },
                    { "data": "designation_name" },
                    { "data": "depertment_name" },
                    { "data": "location_name" },
                    //   @permission('EmployeeCardCreateEditDelete')
                    { "data": "Link",
                        "mRender": function (data, type, full) {
                            return '<a href="{{URL::to('/')}}/employeecard/'+full.id+'/edit"> <span class="glyphicon glyphicon-edit"></span> Edit</a>';
                        }
                    },

                    { "data": "Link",
                        "mRender": function (data, type, full) {
                            return '<a href="{{URL::to('/')}}/employeecard/'+full.cardtableid+'/cancel"  onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-sm btn-flat"><span class="glyphicon glyphicon-trash"></span> Delete</a>';
                        }
                    },
            //   @endpermission

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






});

</script>

@endsection
