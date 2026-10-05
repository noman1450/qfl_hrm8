@extends('layouts.main')
@section('styles')

<!-- <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}"> -->
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables/jquery.dataTables.min.css')}}">
<!-- <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> -->

<!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css"> -->




<style type="text/css">
	.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
	    padding: 5px;
	}	

	table.dataTable thead > tr > th {
	    padding-right: 25px;
	}
	.table>tbody{
		font-size: small;
	}
	.table>thead{
		font-size: smaller;
    background-color: #C1C2C7;
	}

/*	td.details-control {
	    background: url('{{URL::to('/')}}/dist/img/details_open.png') no-repeat center center;
	    cursor: pointer;
	}
	tr.shown td.details-control {
	    background: url('{{URL::to('/')}}/dist/img/details_close.png') no-repeat center center;
	}	*/
</style>
@endsection


@section('content')
<div class="box box-default">
	<div class="box-header with-border">
		<h3 class="box-title">Raw Data Check</h3>
 


	    <div class="row" style="margin-left:10px; ">
	        
          <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">  
	        
              <div class="input-group date">
	                <div class="input-group-addon">
	                  <i class="fa fa-calendar"></i>
	                </div>		            
	                <input type="text" class="form-control pull-right onchange" id="process_date" name="process_date" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('process_date') }}" required readonly>
	            </div>	                
	        </div> 


          <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">  
              <select class="form-control" id="location" name="location" style="width: 100%;" >
              @foreach ($default_user_location as $keys)
                  <option value={{$keys->id}} selected>{{$keys->location_name}}</option>
              @endforeach
              </select>                 
          </div>


          <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;" hidden>  
              <select class="form-control working_shift" id="working_shift" name="working_shift" style="width: 100%;" >
              </select>                 
          </div>

          <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;" hidden>  
              <select class="form-control" id="department" name="department" style="width: 100%;" >
              </select>                 
          </div>

          <div class="col-lg-3 col-md-3 col-xs-12 form-group employee_name" style="padding-left: 0px; padding-top: 10px;" required>  
              <select class="form-control" id="employee_name" name="employee_name" style="width: 100%;" required>
              </select>    
          </div>


<!--           <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
            <input type="button" id="search" value="Search" class=" btn-sm block btn-flat btn" style="margin-right: 15px; padding: 7px 10px;background-color: #EEEEEE; color: black; border:1px solid gray;">                  
          </div> -->



      	</div>


		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>

  <div id="alert-danger"></div>
  <div id="alert-success"></div>


  <form  method="POST" action="{{url('submitmultiple_shiftchange')}}">
        {{ csrf_field() }}    


	<div class="box-body">
		<div class="row">
			<div class="form-group col-lg-12 col-md-12 col-xs-12">   	
				<table id="designation_list_table" class="table table-striped table-bordered"    width="100%">
					<thead >
						<tr>
							<th >Employee Name</th>
							<th >Department</th>
							<th >Designation</th>
							<th >Assign Shift</th>
              <!-- <th style="width: 9%">Last Change</th> -->
							<th style="color: red; ">Code</th>
              <th style="color: red; ">Punch Date</th>
              <th style="color: red;">Punch Time</th>
              <th >Data From </th>
              <th >valid</th>
              <th >is_new</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>


     <input type="submit"  value="Submit" class=" btn-sm btn-success block btn-flat btn hide" style="margin-left: 15px; padding: 7px 10px; color: black; border:1px solid gray;" > 

		</div>
	</div>

</form>




</div>
@endsection

@section('script')

<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('dist/js/jquery.inputmask.bundle.js')}}"></script>


<script>



$(document).ready(function($) {
    
    $('#process_date').datepicker({
      autoclose: true
    });



    // $('#location').select2({
    //   placeholder: 'Enter a location',
    //   allowClear: true,
    //   ajax: {
    //     dataType: 'json',
    //     url: '{{URL::to('/')}}/location_list_data',
    //     delay: 250,
    //     data: function(params) {
    //       return {
    //         term: params.term
    //       }
    //     },
    //     processResults: function (data, params) {
    //       params.page = params.page || 1;
    //       return {
    //         results: data
    //       };
    //     },
    //     cache: true
    //   }
    // });

 $('#department').select2({
      placeholder: 'Enter department',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/depertment_list_data',
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

    $role= $('#employee_name').select2({
      placeholder: 'Enter Employee Name',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/join_employee_list',
        delay: 250,
        data: function(params) {
          return {
            term: params.term,
            location: $('#location option:selected').val()
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
        $('#employee_name').val(null).trigger("change");
        dataLoad();
    });

    $(".onchange").change(function(){
        dataLoad();
    });




    $('.working_shift').select2({
      placeholder: 'Enter working shift',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/shift_list_data',
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
 

    // $("#search").click(function(){
      // dataLoad();
      dataLoad = function(){

      if ($("#location").val() == null){
      	location_id=0;
      }else{
      	location_id = $("#location").val();
      }

      if ($("#working_shift").val() == null){
        working_shift=0;
      }else{
        working_shift = $("#working_shift").val();
      }

      if ($("#department").val() == null){
        department_id = 0;
      }else{
        department_id = $("#department").val();
      }

      if ($("#employee_name").val() == null){
        // alert("Please Select Employee Name");
        // return false;
        employee_name = 0;
      }else{
        employee_name = $("#employee_name").val();
      }


      $.ajax({
        type:   'POST', 
        url :   "{{URL::to('/')}}/rawdatalist",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },        
        data:   {
                   punch_date: $("#process_date").val(),
                   location      : location_id,
                   department_id : department_id,
                   employee_id: employee_name,
                   working_shift_id: working_shift,
                   status:1,
                   // punch_date: $("#date-from").val(),
                   // dateto:   $("#date-to").val()
                },          
        dataType: 'json',
        success: function(data) {
          var dataSet = data.data;
            table = $('#designation_list_table').DataTable( {
              destroy:    true,
              paging:     false,
              searching:  true,
              ordering:   true,
              bInfo:      true,  
              "data":     dataSet,

              "columns": [

              { "data": "employee_name" },
              { "data": "depertment_name" },
              { "data": "designation_name" },
              { "data": "shift_name" },
              // { "data": "last_change" },
              { "data": "card_code" },
              { "data": "punch_date" },
              { "data": "punch_time" },
              { "data": "data_from" },
              { "data": "valid" },
              { "data": "is_new" },
              ],
              order: [ 1, 'asc' ]
            });
        }
      }); 
    // });
}

});
</script>