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
		<h3 class="box-title">Employee Wise Holiday Against Leave List</h3>

        <div class="col-xs-12" style="padding-top:  20px">

              <div class="col-xs-3 col-lg-3 col-md-3" >
        	           <a href="{{ URL::to('holidayagainstleave/create')}}"><input type="button" value="Create New" class="btn-success btn btn-sm button  btn-flat" style="font-size: 12px; font-weight: bold;"></a>

                    <a href="{{ URL::to('holidayagainstleavehistory')}}" "><input type="button" value="Leave History" class="btn btn-sm button btn-flat" style="font-size: 12px; font-weight: bold;"></a>
               </div>

              <div class="col-xs-2 col-lg-2 col-md-2 ">
                    <select style="width: 100%;" class="form-control year" id="year" name="year" required>
                    </select>
              </div>

              <div class="col-xs-2 col-lg-2 col-md-2">
                   <select  class="form-control col-lg-12" id="month_name" name="month_name" style="width: 100%;" >
                    @foreach ($month as $keys)
                    <option value={{$keys->id}}>{{$keys->month_name}}</option>
                    @endforeach
                    </select>
              </div>

              <div class="col-xs-3 col-lg-3 col-md-3 ">
                    <select style="width: 100%;" class="form-control select2" id="employee_name" name="employee_name" required>
                    </select>
              </div>

              <div class="col-xs-2 col-lg-2 col-md-2">
                       <input type="button" id="search" value="Search" class="btn-sm block btn-flat btn"  >
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
							<th style="width: 15%">Holiday Name</th>
              <th style="width: 20%">In Out Time</th>
              <th style="width: 15%">Holiday Date</th>
							<th style="width: 10%">Delete</th>
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



  for (i = new Date().getFullYear(); i > 2016; i--){
    $('.year').append($('<option>').val(i).html(i));
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


    $("#search").click(function(){
      dataLoad();
    });


    dataLoad = function(){

      $.ajax({
        type:   'POST',
        url :   "{{URL::to('/')}}/holidayagainstleavelist",
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

              { "data": "Link",
                  "mRender": function (data, type, full) {
                    return '<a target="_blank" href="{{URL::to('/')}}/employeeinfo/'+full.hrm_employee_id+'">'+full.employee_name+'</a>';
                }
              },
              { "data": "designation_name" },
              { "data": "dayName" },
              { "data": "in_out_time" },
              { "data": "holiday_date" },
              { "data": "Link",
                "mRender": function (data, type, full) {
                    return '<a href="{{URL::to('/')}}/holidayagainstleave/'+full.id+'/cancel"  onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-sm btn-flat"><span class="glyphicon glyphicon-trash"></span> Delete</a>';
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
