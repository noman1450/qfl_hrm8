<!-- employee_list -->
@extends('layouts.main')

<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

<!-- <link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}"></script> -->
<!-- <link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}"> -->
@endsection

<!-- content -->
@section('content')
<div class="box box-default">
	<div class="box-header with-border">
		<h3 class="box-title">Probable Employee Long Service List</h3>
	    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
         
          <div class="col-lg-3 col-md-3 col-xs-12 form-group">  
            <select class="form-control" id="location" name="location" style="width: 100%;" required>
                @foreach ($default_user_location as $keys)
                      <option value={{$keys->id}} >{{$keys->location_name}}</option>
                @endforeach
            </select>                 
          </div>
        
         <div class="col-lg-3 col-md-3 col-xs-12 form-group">  
            <select class="form-control" onchange="myFunction()" id="duration" name="duration" style="width: 100%;" required>
                      <option value="0">-All-</option>
                      <option value="5">5</option>
                      <option value="10">10</option>
                      <option value="15">15</option>
                      <option value="20">20</option>
            </select>                 
          </div>

          <a href="{{ URL::to('service_completed_list')}}"><input type="button" value="Service Completed List" class="btn-info btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold;"></a>


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
							<th style="width: 15%">Department</th>
              <th style="width: 15%">Designation</th>
              <th style="width: 15%">Joining Date</th>
							<th style="width: 15%">Confirmation Date</th>
							<th style="width: 15%">Duration</th>
              <th style="width: 10%">Action</th>
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
<script src="{{asset('bootstrap/js/bootstrap.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>


<script>
$(document).ready(function($) {

    dataLoad = function(){
      $.ajax({
        type:   'POST', 
        url :   "{{URL::to('/')}}/employeelongservice_list",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }, 
        data:   {
          location: $("#location").val(),      
          duration: $("#duration").val(),      
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
              { "data": "designation_name" },                     
              { "data": "joining_date" },                     
              { "data": "confirmation_date" },  
              { "data": "jobduration" },
              { "data": "Link",
                "mRender": function (data, type, full) {
                  return '<a href="{{URL::to('/')}}/employeelongservice/'+full.id+'/createnew"   class="btn btn-success btn-sm btn-flat"><span class="glyphicon glyphicon-share-alt"></span> Create</a>';
                
              }
            },
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

function myFunction() {
  dataLoad();
}

</script>


@endsection