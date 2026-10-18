@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

@endsection

@section('content')
<div class="box box-default">

	<div class="box-header with-border">
		<h3 class="box-title">Multiple Employee Resign (Auto Approved)</h3>
	    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
          <a href="{{ URL::to('employeeresign')}}"><input type="button" value="Resign List" class="btn-info btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold;"></a>
          <a href="{{ URL::to('resignapproved')}}"><input type="button" value="Approved List" class="btn-primary btn btn-sm button pull-left btn-flat" style="font-size: 12px;  margin-left: 20px; font-weight: bold;"></a>
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
            <form action="{{ url('multiple_resign') }}" method="post" style="margin-bottom: 0">
                @csrf
                <div class="form-group col-lg-4 col-md-4 col-xs-12">
                    <label>Resignation Type</label>
                    <select style="width: 100%;" class="form-control select2" id="hrm_resignation_type_id" name="hrm_resignation_type_id" required>
                    </select>
                </div>

                <div class="form-group col-lg-8 col-md-8 col-xs-12">
                    <label>Comment</label>
                    <textarea class="form-control" name="comment" id="comment" placeholder="Comment"></textarea>
                </div>

	            <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">
					<table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th style="width: 5%">
                                    <input type="checkbox" id="checkAll" /> All
                                </th>
								<th></th>
								<th style="width: 22%">Employee Name</th>
								<th style="width: 12%">Department</th>
                                <th style="width: 12%">Designation</th>
                                <th style="width: 12%">Location</th>
								<th style="width: 15%">Joining Date</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>

                <div class="col-xs-12" style="text-align: center; margin-bottom: 20px;">
                    <button type="submit" onclick="return confirm('Are you sure? Selected employees will be RESIGNED & AUTO APPROVED with joining date as resign date...!')" class="btn btn-danger btn-flat" style="font-size: 14px; font-weight: bold;">Submit Multiple Resign</button>
                </div>
            </form>
		</div>
	</div>

</div>
@endsection



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
        url :   "{{URL::to('/')}}/multiple_resign_list",
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
              bInfo:      false,
              "data":     dataSet,

            "columns": [

              { "data": "employee_id",
                "orderable": false,
                "mRender": function (data, type, full) {
                  return '<input type="checkbox" class="row-check" name="employee_ids[]" value="'+full.employee_id+'">';
                }
              },
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
              { "data": "location_name" },
              { "data": "joining_date" },
            ],
            "order": [[2,'asc']]
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


    $('#hrm_resignation_type_id').select2({
        placeholder: 'Resignation Type',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: '{{URL::to('/')}}/resignation_list_data',
            delay: 250,
            data: function (params) {
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


    $(document).on('click', '#checkAll', function() {
        $('input.row-check').prop('checked', this.checked);
    });

    $(document).on('change', 'input.row-check', function() {
        if(!this.checked){
            $('#checkAll').prop('checked', false);
        }else{
            if($('input.row-check').length == $('input.row-check:checked').length){
                $('#checkAll').prop('checked', true);
            }
        }
    });


});

</script>

@endsection
