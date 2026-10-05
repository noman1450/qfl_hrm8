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
		<h3 class="box-title">Processed Increment & Promotion List</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>

    <div class="col-lg-3 col-md-3 col-xs-12 form-group" >
      <label>Location</label>
        {{-- <span style="margin-left: 50px;color:blue; "><input type="checkbox" name="date_range" value="1">Apply Date Range</label></span> --}}
        <select  class="form-control col-lg-12" id="location" name="location" style="width: 100%;"  required>
                    @foreach ($default_user_location as $keys)
                        <option value={{$keys->id}} >{{$keys->location_name}}</option>
                    @endforeach
        </select>
    </div>
    <div class="col-lg-2 col-md-2 col-xs-6 form-group" >
        <label>Status</label>
          {{-- <span style="margin-left: 50px;color:blue; "><input type="checkbox" name="date_range" value="1">Apply Date Range</label></span> --}}
          <select  class="form-control col-lg-12" id="status" name="status" style="width: 100%;"  required>
                     <option value="1">Increment</option>
                     <option value="2">Promotion</option>
          </select>
      </div>
    <div class="col-lg-2 col-md-3 col-xs-6">
        <label>Month From</label>
        <input type="text" class="form-control"  placeholder="Month From" name="date_from" id="date_from" value="{{ date('M Y') }}"  readonly required>
      </div>
      <div class="col-lg-2 col-md-3 col-xs-6">
        <label> Month To</label>
        <input type="text" class="form-control"  placeholder="Month To" name="date_to" id="date_to" value="{{ date('M Y') }}"  readonly required>
      </div>
      <div class="col-lg-1 col-md-1">

        <input type="button" class="btn btn-sm btn-success" id="search" style="margin-top: 25px" value="Search">
      </div>





	<div class="box-body">
		<div class="row">
	        <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">
				<table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th style="width: 5%"></th>
							<th style="width: 5%">Employee Code</th>
							<th style="width: 20%">Name</th>
							<th style="width: 10%">Joining Date</th>
              <th style="width: 15%">Increment Date</th>
							<th style="width: 10%">Location</th>
							<th style="width: 10%">Designation</th>
              <th style="width: 10%">Depertment</th>
              <th style="width: 10%">Category</th>
              <th style="width: 10%">Status</th>
              <th style="width: 10%">Salary</th>
              <!-- <th style="width: 8%">Joining Date</th> -->
              <!-- <th style="width: 8%">Confirmation Date</th> -->
              <!-- <th style="width: 8%">New Location</th> -->
              <th style="width: 10%">Increase</th>
							<th style="width: 10%">New Salary</th>
							<th style="width: 10%">New Designation</th>
              <th style="width: 10%">Last Increment Note</th>
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
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
$(document).ready(function($) {

    var search = false;

    $(document).on('click','#search',function(){
        dataLoad();
    })

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

    // $role.on('select2:select', function (e) {
    //     dataLoad();
    // });

    // $role.on('select2:unselect', function (e) {
    //     $('#location').val(null).trigger("change");
    //     dataLoad();
    // });


    $('#date_from').datepicker({
       autoclose: true,
       minViewMode: 1,
       format: "MM yyyy",
    });

    $('#date_to').datepicker({
       autoclose: true,
       minViewMode: 1,
       format: "MM yyyy",
    });





  $(".onchange").change(function(){
        dataLoad();
  });

  dataLoad = function(){

      $.ajax({
        type:   'POST',
        url :   "{{URL::to('/')}}/past_salaryincrement_listdata",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
        "data":   {location  :$("#location").val(),
                   date_from :$("#date_from").val(),
                   date_to :$("#date_to").val(),
                   status :$("#status").val(),
                //    search :search,
                 },
        dataType: 'json',
        success: function(data) {
            var dataSet = data.data;
            table = $('#list_table').DataTable( {
              destroy:    true,
              paging:     false,
              searching:  true,
              ordering:   true,
              bInfo:      true,
              "data":     dataSet,
              dom: 'Bfrtip',  // B = Buttons
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Increment & Promotion List',   // Excel file title
                        text: 'Excel',  // Button text
                        className: 'btn btn-success'
                    }
                ],

            "columns": [

              { "data": "Link",
                "mRender": function (data, type, full) {
                  return '<img src="{{asset('employee_image')}}/'+full.Images+'" style="height:30px; width:30px; border-radius: 30px;"/>';
                }
              },
               {
                data: "employee_code",
                visible: false,
                searchable: false
              },
              { "data": "Link",
              "mRender": function (data, type, full) {
              return '<a target="_blank"  href="{{URL::to('/')}}/employeeinfo/'+full.employee_id+'">'+full.employee_name+'</a>';
              }
              },
              { "data": "joining_date" },
              { "data": "effective_month" },
              { "data": "location_name" },
              { "data": "designation_name" },
              { "data": "depertment_name" },
              { "data": "category_name" },
              { "data": "apply_for" },
              // { "data": "joining_date" },
              // { "data": "confirmation_date" },
              { "data": "salary_amount" },
              { "data": "increase_amount" },
              { "data": "new_salary_amount" },
              { "data": "new_designation_name" },
              { "data": "notes" },
              { "data": "Link",
                "mRender": function (data, type, full) {
                    return '<a href="{{URL::to('/')}}/salaryincrement/'+full.hrm_salary_increment_details_id+'/approved_delete"  onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-sm btn-flat"><span class="glyphicon glyphicon-trash">Delete</a>';
                }
              },

            ],
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
