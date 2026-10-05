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
		<h3 class="box-title">Employee Inactive List</h3>
	    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
          <a href="{{ URL::to('employeeinactive/create')}}"><input type="button" value="Create New" class="btn-success btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold;"></a>
          <a href="{{ URL::to('reactive')}}"><input type="button" value="Reactive List" class="btn-info btn btn-sm button pull-left btn-flat" style="font-size: 12px;  margin-left: 20px; font-weight: bold;"></a>
 

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
              <th style="width: 12%">Designation</th>
              <th style="width: 12%">Date From</th>
							<th style="width: 15%">Date To</th>
							<th style="width: 18%">Comment</th>
              <th style="width: 10%">Status</th>
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
        url :   "{{URL::to('/')}}/employeeinactive_list",
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
              bInfo:      true,  
              "data":     dataSet,

            "columns": [
              
              { "data": "Link",
                "mRender": function (data, type, full) {
                  return '<img src="{{asset('employee_image')}}/'+full.Images+'" style="height:30px; width:30px; border-radius: 30px;"/>';
                }
              },
              // { "data": "Link",
              //     "mRender": function (data, type, full) {
              //       if(full.status == 0 ){
              //       return '<a target="_blank" href="{{URL::to('/')}}/employeeinfo/'+full.employee_id+'">'+full.employee_name+'</a>';}
              //       else{
              //       return '<a  href="">'+full.employee_name+'</a>';
              //       }
              //   }
              // },
              { "data": "Link",
                  "mRender": function (data, type, full) {
                    return '<a target="_blank" href="{{URL::to('/')}}/employeeinfo/'+full.employee_id+'">'+full.employee_name+'</a>';
                }
              },                              
              { "data": "depertment_name" },                     
              { "data": "designation_name" },                     
              { "data": "date_from" },                     
              { "data": "date_to" },  
              { "data": "comment" },
              { "data": "Link",
                "mRender": function (data, type, full) {
                if(full.status == 1 ){
                  return '<a href="{{URL::to('/')}}/employeeinactive/'+full.id+'/reactive"   class="btn btn-success btn-sm btn-flat"><span class="glyphicon glyphicon-share-alt"></span> Reactive</a>';
                }else{
                  return '<input type="button" value="Processing.." class="btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold;color: gray;">';
                }
              }
            },

            { "data": "Link",
                "mRender": function (data, type, full) {
                    return '<a href="{{URL::to('/')}}/employeeinactive/'+full.id+'/cancel"  onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-sm btn-flat"><span class="glyphicon glyphicon-trash"></span> Delete</a>';
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

</script>

@endsection