<!-- employee_list -->
@extends('layouts.main')

<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">


<style type="text/css">

  td.details-control {
      background: url('{{URL::to('/')}}/dist/img/details_open.png') no-repeat center center;
      cursor: pointer;
  }
  tr.shown td.details-control {
      background: url('{{URL::to('/')}}/dist/img/details_close.png') no-repeat center center;
  }


</style>


@endsection

<!-- content -->
@section('content')
<div class="box box-default">
	<div class="box-header with-border">
		<h3 class="box-title">Pending Employee Holiday List ....</h3>
	    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
	        <a href="{{ URL::to('holiday_process/create')}}"><input type="button" value="Create New" class="btn-success btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold;"></a>

          <a href="{{ URL::to('approvedholidaylist')}}"><input type="button" value="Processed/Approved Holiday List" class="btn-info btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold; margin-left: 15px;"></a>


	    </div>

        <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
          <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
              <select class="form-control" id="location" name="location" style="width: 100%;" required>
                  @foreach ($default_user_location as $keys)
                      <option value={{$keys->id}} selected>{{$keys->location_name}}</option>
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
				<table id="list_table" class="table table-bordered table-hover cell-border" cellspacing="0" width="100%">
					<thead>
						<tr>
                            <th style="width: 0%"></th>
                            <th style="width: 03%"></th>
                            <th style="width: 10%">Location</th>
							<th style="width: 10%">Year</th>
							<th style="width: 15%">Holiday Name</th>
                            <th style="width: 11%">Date From</th>
                            <th style="width: 10%">Date To</th>
                            <th style="width:  4%">Days</th>
							<th style="width: 11%">Apply For</th>
                            <th style="width: 10%">Edit</th>
                            <th style="width: 10%">Delete</th>
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
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

<script>
    function format(d) {
      // console.log(d);
      return '<table  width="100%" >'+
            '<tr>'+
              '<td>'+"Employee Name"+'</td>'+
              '<td>'+"Department"+'</td>'+
              '<td>'+"Designation"+'</td>'+
              '<td>'+"Plant Name"+'</td>'+
            '</tr>'+
            '<tr>'+
              '<td>'+d.employee_name+'</td>'+
              '<td>'+d.department_name+'</td>'+
              '<td>'+d.designation_name+'</td>'+
              '<td>'+d.plant_name+'</td>'+
            '</tr>'+
          '</table>';
    }


$(document).ready(function($) {


    function list_table() {
      $.ajax({
        type:   'POST',
        url :   "{{URL::to('/')}}/employeeholiday_list",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
        dataType: 'json',
          data: {
             location: $("#location").val()
          },
        success: function(data) {
            var dataSet = data.data;
            table = $('#list_table').DataTable( {
              destroy:    true,
              paging:     true,
              searching:  true,
              ordering:   true,
              bInfo:      false,
              "data":     dataSet,
              "aoColumnDefs": [{ "bVisible": false, "aTargets": [0] }],
            "columns": [
              { "data": "id" },
              {
                "className":      'details-control',
                "orderable":      true,
                "data":           null,
                "defaultContent": ''
              },

              { "data": "location_name" },
              { "data": "leave_year" },
              { "data": "holiday_name" },
              { "data": "date_from" },
              { "data": "date_to" },
              { "data": "days" },
              { "data": "apply_type" },

              { "data": "Link",
                "mRender": function (data, type, full) {
                  return '<a href="{{URL::to('/')}}/holiday_process/'+full.id+'/edit"> <span class="glyphicon glyphicon-edit"></span> Edit</a>';
                }
              },

              { "data": "Link",
                  "mRender": function (data, type, full) {
                      return '<a href="{{URL::to('/')}}/holiday_process/'+full.id+'/deletepending"  onclick="return confirm(\'Do you want to Delete?\');" class="btn btn-danger btn-sm btn-flat"><span class="glyphicon glyphicon-trash">Delete</a>';
                  }
              },

              { "data": "Link",
                  "mRender": function (data, type, full) {
                      return '<a href="{{URL::to('/')}}/holiday_process/'+full.id+'/action"  onclick="return confirm(\'Do you want to Approve?\');" class="btn btn-info btn-sm btn-flat"><span class="glyphicon glyphicon-ok">Approve</a>';
                  }
              },

            ],
            "order": [[0,'desc']]
            });
        }
      });



    $('#list_table tbody').on('click', 'td.details-control', function () {
      var tr = $(this).closest('tr');
      var row = table.row( tr );
      if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format(row.data()) ).show();
            tr.addClass('shown');
        }
    });

  }
  list_table();
        $location = $('#location').select2({
            placeholder: 'Enter a location mandatory',
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

        $location.on('select2:select', function (e) {
            list_table();
        });




});

</script>

@endsection
