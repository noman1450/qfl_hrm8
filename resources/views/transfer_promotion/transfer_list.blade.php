<!-- employee_list -->
@extends('layouts.main')

<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

@endsection

<!-- content -->
@section('content')
<div class="box box-default">
	<div class="box-header with-border">
		<h3 class="box-title">Transfer List</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>

        <div class="col-lg-3 col-md-3 col-xs-12 form-group" >
          <a href="{{ URL::to('employeetransfercreate')}}"><input type="button" value="Create New" class="btn-success btn btn-sm button pull-left btn-flat" ></a>
      </div>

    <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
      <label>Location
        <span style="margin-left: 50px;color:blue; "><input type="checkbox" name="date_range" value="1">Apply Date Range</label></span>
      <select  class="form-control col-lg-12" id="location" name="location" style="width: 100%;"  required>
      </select>
    </div>

      <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;"> 
      <label>Date From</label> 
              <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>                
                  <input type="text" class="form-control pull-right onchange" id="date_from" name="date_from" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('date_from') }}" required readonly>
              </div>                  
      </div> 

      <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">  
      <label>Date To</label> 

              <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>                
                  <input type="text" class="form-control pull-right onchange" id="date_to" name="date_to" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('date_to') }}" required readonly>
              </div>                  
      </div> 

	

	<div class="box-body">
		<div class="row">
	        <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">   	
				<table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th style="width: 5%"></th>
							<th style="width: 8%">Name</th>
                            <th style="width: 8%">Transfer Date</th>
							<th style="width: 8%">Unique Code</th>
							<th style="width: 8%;color: darkred;">Location</th>
                            <th style="width: 8%;color: darkred;">Department</th>
                            <th style="width: 8%;color: darkred;">Designation</th>
                            <!-- <th style="width: 8%">Joining Date</th> -->
                            <!-- <th style="width: 8%">Confirmation Date</th> -->
                            <th class="transfer-green" style="width: 8%;">New Location</th>
                            <th class="transfer-green" style="width: 8%;">New Dept.</th>
							<th class="transfer-green" style="width: 8%;">New Designation</th>
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
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>


<script>
$(document).ready(function($) {


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


    $('#date_from').datepicker({
      autoclose: true
    });     

    $('#date_to').datepicker({
      autoclose: true
    });     

  $(".onchange").change(function(){
        dataLoad();
  });

  dataLoad = function(){

      $.ajax({
        type:   'POST', 
        url :   "{{URL::to('/')}}/promotionandtransfer_list",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
        "data":   {location  :$("#location").val(),
                   date_range:$('input[name=date_range]:checked').val(),
                   date_from :$("#date_from").val(),
                   date_to   :$("#date_to").val(),
                   activity_status_id:2,
                 },              
        dataType: 'json',
        success: function(data) {
            var dataSet = data.data;
            table = $('#list_table').DataTable( {
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
              return '<a target="_blank"  href="{{URL::to('/')}}/employeeinfo/'+full.employee_id+'">'+full.employee_name+'</a>';
              }
              },                    
              { "data": "activity_date" }, 
              { "data": "Unique_Code" },                     
              { "data": "location_name" },                     
              { "data": "depertment_name" }, 
              { "data": "alis" }, 
              // { "data": "joining_date" }, 
              // { "data": "confirmation_date" }, 
              { "data": "newlocation_name" }, 
              { "data": "newdepertment_name" }, 
              { "data": "newdesignation_name" }, 
            ],
            "order": [[0,'asc']]
            });
        }
      }); 
    }

       $('input[type=checkbox][name=date_range]').change(function() {
              dataLoad();
       });

        dataLoad();


});

</script>

@endsection