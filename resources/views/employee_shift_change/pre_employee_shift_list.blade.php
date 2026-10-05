<!-- shift_list -->

@extends('layouts.main')

<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
@endsection

<!-- content -->
@section('content')
<div class="box box-default">
	<div class="box-header with-border">
		<h3 class="box-title">Previous Employee Shift Change list</h3>
     

	    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">


      <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">  
        <a href="{{ URL::to('pre_changeemployeeshift')}}"> <input type="button" value="Change Employee Shift " class="btn-success btn-sm block btn-flat btn" style="margin-right: 15px;"></a>
      </div> 


      <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">  
              <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>                
                  <input type="text" class="form-control pull-right onchange" id="process_date" name="process_date" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('process_date') }}" required readonly>
              </div>                  
      </div> 

      <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">  
            <select style="width: 100%;" class="form-control select2" id="employee_name" name="employee_name" required>
            </select>
         
      </div>




      </div>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>
	

	<div class="box-body">
    <div style="overflow: auto;">
		<table id="designation_list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
			<thead>
				<tr>
          <th style="width: 30%">Employee Name</th>
          <th style="width: 20%">Location Name</th>
          {{-- <th style="width: 15%">Department</th> --}}
          {{-- <th style="width: 20%">Designation</th> --}}
					<th style="width: 25%">Shift Name</th>
          <th style="width: 15%">Change Date</th>
					<th style="width: 10%">Action</th>
					<!-- <th style="width: 10%">End Time</th> -->
				</tr>

			</thead>
			<tbody>
			</tbody>
		</table>
    </div>
	</div>
</div>

@endsection
<!-- 175.29.166.86 -->


<!-- script -->
@section('script')
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

<script>
$(document).ready(function($) {

    $('#process_date').datepicker({
      // startDate: new Date() ,  
      autoclose: true
    });     


    $role = $('#employee_name').select2({
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

    $role.on('select2:select', function (e) {
        dataLoad();
    });

    $role.on('select2:unselect', function (e) {
        $('#employee_name').val(null).trigger("change");
        dataLoad();
    });

    $(".onchange").change(function(){
        dataLoad();
    });

    dataLoad = function(){

      if ($("#employee_name").val() == null){
        employee_name = 0;
      }else{
        employee_name = $("#employee_name").val();
      }

      $.ajax({
        type:   'POST', 
        url :   "{{URL::to('/')}}/pre_changeemployeeshiftlist",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },   
        data:   {
           process_date: $("#process_date").val(),
           employee_name: employee_name,
           
        },           
        dataType: 'json',
        success: function(data) {
            var dataSet = data.data;
            table = $('#designation_list_table').DataTable( {
              destroy:    true,
              paging:     true,
              searching:  true,
              ordering:   true,
              bInfo:      false,  
              "data":     dataSet,

            "columns": [
              { "data": "Link",
                "mRender": function (data, type, full) {
                    return '<a target="_blank"  href="{{URL::to('/')}}/employeeinfo/'+full.hrm_employee_id+'">'+full.employee_name+'</a>';
                }
              },

              { "data": "location_name" },
              // { "data": "depertment_name" },
              // { "data": "designation_name" },
              { "data": "shift_name" },
              { "data": "punch_date" },
              { "data": "Link", name: 'action', orderable: false, searchable: false},
            ],
            "order": [[0,'asc']]
            });
        }
      }); 

    }


dataLoad();

});

</script>

@endsection