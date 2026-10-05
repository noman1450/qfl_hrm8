<!-- location_list -->
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
		<h3 class="box-title">OT Process List</h3>

        <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
            <a href="{{URL::to('ot_process_create')}}"><input type="button" value="Create New" class="btn-success btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold;"></a>

            <a href="{{URL::to('ot_process_generate')}}"><input type="button" value="Fianlly Generated" class="btn-info btn btn-sm button pull-right btn-flat" style="font-size: 12px; font-weight: bold;"></a>
        </div>

        <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
            <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
                <select class="form-control year onchange" id="year" name="year" style="width: 100%;" required>
                    <option value={{$running_month_year[0]->year_id}} selected>{{$running_month_year[0]->year_id}}</option>
                </select>
            </div>

            <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
                <select  class="form-control col-lg-12 onchange" id="month_name" name="month_name" style="width: 100%;" >
                    <option value={{$running_month_year[0]->hrm_month_id}} selected>{{$running_month_year[0]->month_name}}</option>
                </select>
            </div>

            <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
                <select  class="form-control col-lg-12" id="location" name="location" style="width: 100%;"  required>
                    <option value={{$running_month_year[0]->hrm_location_id}} selected>{{$running_month_year[0]->location_name}}</option>
                </select>
            </div>
        </div>

		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>

	<div class="box-body">
		<div class="row">
	        <div class="form-group col-lg-10 col-md-10 col-xs-12" style="overflow: auto;">
				<table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th style="width: 30%">Location Name</th>
                            <th style="width: 10%">Year</th>
                            <th style="width: 10%">Month</th>
                            <!-- <th style="width: 15%">Declaration Date</th> -->
                            <!-- <th style="width: 15%">Payment Date</th> -->
                            <th style="width: 10%">Status</th>
                            <th style="width: 10%">User</th>
							<th style="width: 10%">Create Time</th>
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

@section('script')
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

<script>
$(document).ready(function($) {

    for (i = new Date().getFullYear(); i > 2017; i--){
      $('.year').append($('<option />').val(i).html(i));
    }

$role= $('#location').select2({
      placeholder: 'Choose Location Mandatory',
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


    $(".onchange").change(function(){
        dataLoad();
    });

$('#month_name').select2({
      placeholder: 'Enter Month  Name',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/monthlist',
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








dataLoad = function(){

      $.ajax({
        type:   'POST',
        url :   "{{URL::to('/')}}/otprocess_list",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
      "data":   {
                           year: $("#year").val(),
                           month: $("#month_name").val(),
                           location: $("#location").val(),
                           hrm_salary_generate_master_id : $("#salary_master").val(),
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
              { "data": "location_name" },
              { "data": "year_id" },
              { "data": "month_name" },
              // { "data": "declaration_date" },
              // { "data": "payment_date" },
              { "data": "status" },
              { "data": "user_name" },
              { "data": "created_at" },
              { "data": "Link",
                "mRender": function (data, type, full) {

                    if(full.process_status=="1"){
                         return '<a href="{{URL::to('/')}}/deleteotprocess/'+full.id+'/cancel"  onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-sm btn-flat"><span class="glyphicon glyphicon-trash">Delete</a>';
                     }else{
                         return '';
                     }

                }
              },

            ],
            "order": [[0,'asc']]
            });
        }
      })
      };

dataLoad();

});

</script>

@endsection
