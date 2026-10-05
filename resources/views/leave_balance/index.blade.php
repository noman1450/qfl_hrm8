<!-- employee_list -->
@extends('layouts.main')

<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap.min.css">
@endsection

<!-- content -->
@section('content')
<div class="box box-default">
	<div class="box-header with-border">
		<h3 class="box-title">Leave Balance</h3>

        <div class="col-xs-12" style="padding-top: 20px">


              <div class="col-xs-2 col-lg-2 col-md-2">
                    <label for="leave_year">Leave Year</label>
                   <select  class="form-control col-lg-12" id="leave_year" name="leave_year" style="width: 100%;" >
                    @foreach ($leave_years as $item)
                    <option value={{$item->id}}>{{$item->leave_year}}</option>
                    @endforeach
                    </select>
              </div>
              <div class="col-lg-2">
                <div class="form-group">
                    <label for="location">Location</label>
                    <select class="form-control" id="location" style="width: 100%;"  required>
                        @foreach ($default_user_location as $keys)
                            <option value="{{ $keys->id }}" selected>{{$keys->location_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-lg-2">
                <div class="form-group">
                    <label for="designation_id">Designation</label>
                    <select class="form-control" id="designation_id" style="width: 100%;"  required>

                    </select>
                </div>
            </div>

            <div class="col-lg-2">
                <div class="form-group">
                    <label for="department_id">Department</label>
                    <select class="form-control" id="department_id" style="width: 100%;"  required>

                    </select>
                </div>
            </div>

            <div class="col-lg-2">
                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select class="form-control" id="category_id" style="width: 100%;"  required>

                    </select>
                </div>
            </div>

            <div class="col-lg-2">
                <div class="form-group">
                    <label for="section_id">Sub-department</label>
                    <select class="form-control" id="section_id" style="width: 100%;"  required>

                    </select>
                </div>
            </div>

            <div class="col-lg-2">
                <div class="form-group">
                    <label for="employee_type_id">Employee Type</label>
                    <select class="form-control" id="employee_type_id" style="width: 100%;"  required>

                    </select>
                </div>
            </div>

              <div class="col-xs-3 col-lg-3 col-md-3 ">
                  <label for="employee_name">Employee Name</label>
                    <select style="width: 100%;" class="form-control select2" id="employee_name" name="employee_name" required>
                    </select>
              </div>

              <div class="col-xs-2 col-lg-2 col-md-2">
                      <input type="button" id="search" value="Search" class="btn-sm block btn-flat btn "  style="margin-top:25px">
              </div>

        </div>

		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


	<div class="box-body">
		<div class="row">

        <div class="col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">
				<table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
					<thead>
						<tr>
                            <th style="width: 30%">Employee Name</th>
							<th style="width: 20%">Designation Name</th>
							<th style="width: 15%">Total Leave</th>
                            <th style="width: 20%">Used Leave</th>
                            <th style="width: 15%">Balance</th>
                            <th>Details</th>
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
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
$(document).ready(function($) {

    $designation = select2Dropdown("#designation_id", "{{ url('/designation_list_data') }}", "Enter Designation Name");
    $department = select2Dropdown("#department_id", "{{ url('/depertment_list_data') }}", "Enter Department Name");
    $category = select2Dropdown("#category_id", "{{ url('/category_list_data') }}", "Enter Category Name");
    $section = select2Dropdown("#section_id", "{{ url('/section_list_data') }}", "Enter Sub-Department Name");
    $employee_type = select2Dropdown("#employee_type_id", "{{ url('/employeestatus_list_data') }}", "Enter Employee Type");


  for (i = new Date().getFullYear(); i > 2016; i--){
    $('.year').append($('<option>').val(i).html(i));
  }

   $('#location').select2({
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


    $("#search").click(function(){
      dataLoad();
    });


    dataLoad = function(){

      $.ajax({
        type:   'GET',
        url :   "{{URL::to('/leave_balance')}}",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },

          data:   {

                   employee_name: $("#employee_name").val(),
                   leave_year: $("#leave_year").val(),
                    location: $("#location").val(),
                    redflagStatus:$('input[name=redflagStatus]:checked').val(),
                    designation_id: $('#designation_id option:selected').val(),
                    department_id: $('#department_id option:selected').val(),
                    category_id: $('#category_id option:selected').val(),
                    section_id: $('#section_id option:selected').val(),
                    employee_type_id: $('#employee_type_id option:selected').val(),

                },


        dataType: 'json',
        success: function(data) {
            var dataSet = data.data;
            table = $('#list_table').DataTable( {
              destroy:    true,
              paging:     false,
              searching:  false,
              ordering:   true,
              bInfo:      true,
              "data":     dataSet,
              dom: 'Bfrtip',
               buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i> Excel',
                titleAttr: 'Export to Excel',
                className: 'btn btn-success btn-sm'
            },

        ],

            "columns": [

              { "data": "Link",
                  "mRender": function (data, type, full) {
                    return '<a target="_blank" href="{{URL::to('/')}}/employeeinfo/'+full.hrm_employee_id+'">'+full.employee_name+'</a>';
                }
              },
              { "data": "designation_name" },
              { "data": "total_leave" },
              { "data": "used" },
              { "data": "balance" },
              { "data": "Details",
                    "mRender": function (data, type, full) {

                        var leave_year = $("#leave_year").val();

                        return '<a target="_blank" class="btn btn-info btn-sm btn-flat" ' +
                            'href="{{ URL::to('/') }}/emp_leave_balance/' + full.hrm_employee_id + '/' + leave_year + '">' +
                            'Details</a>';
                    }
              },

            ],
            "order": [[0,'asc']]
            });
        }
      });


  }

});

</script>

@endsection
