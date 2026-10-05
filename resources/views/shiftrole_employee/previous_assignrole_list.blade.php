<!-- designation_list -->
@extends('layouts.main')

<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">

@endsection

<!-- content -->
@section('content')
<div class="box box-default">
	<div class="box-header with-border">
		<h3 class="box-title">Previous Shift Role Assign List</h3>

		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>
	
         <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">

          <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">  
              <label>Shift Role</label>
              <select class="form-control  " id="shiftrole" name="shiftrole" style="width: 100%;" required>
              </select>                 
          </div>

          <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">  
              <label>Start Date</label>
                      <div class="input-group date">
                          <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                          </div>                
                          <input type="text" class="form-control pull-right onchange" id="start_date" name="start_date" data-date-format="dd-mm-yyyy"  value="{{ old('start_date') }}" placeholder="dd-mm-yyyy" autocomplete="off">
                        @if ($errors->has('start_date'))
                            <span class="help-block">
                                <strong>{{ $errors->first('start_date') }}</strong>
                            </span>
                        @endif                  
                      </div>   
                  
              </select>   
          </div>

          <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">  
              <label>End Date</label>

                      <div class="input-group date">
                          <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                          </div>                
                          <input type="text" class="form-control pull-right onchange" id="end_date" name="end_date" data-date-format="dd-mm-yyyy"  value="{{ old('end_date') }}"  placeholder="dd-mm-yyyy" autocomplete="off">
                        @if ($errors->has('end_date'))
                            <span class="help-block">
                                <strong>{{ $errors->first('end_date') }}</strong>
                            </span>
                        @endif                  
                      </div>   
          </div>


        </div>

	<div class="box-body">
   <div class="row">
     <div class="col-md-12" style="overflow: auto;">
  		<table id="designation_list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
  			<thead>
  				<tr>
  					<th style="width: 25%">Role Name</th>
            <th style="width: 30%">Shift Name</th>
            <th style="width: 25%">Shifting Time</th>
  					<!-- <th style="width: 30%">End Time</th> -->
            <th style="width: 10%">Start Date</th>
  					<th style="width: 10%">End Date</th>
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
<!-- 175.29.166.86 -->


<!-- script -->
@section('script')
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>

<script>
$(document).ready(function($) {

   $('#start_date').datepicker({
       autoclose: true
    });

    $('#end_date').datepicker({
       autoclose: true
    });



    $role = $('#shiftrole').select2({
      placeholder: 'Enter a Shiftrole',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/shiftrole_list_data',
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
        $('#shiftrole').val(null).trigger("change");
        dataLoad();
    });

    $(".onchange").change(function(){
        dataLoad();
    });


    dataLoad = function(){

      $.ajax({
        type:   'POST', 
        url :   "{{URL::to('/')}}/previous_shiftrole_assign_list",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
        data:   {
                  shiftrole: $("#shiftrole").val(),      
                  start_date: $("#start_date").val(),      
                  end_date: $("#end_date").val(),      
                },

        dataType: 'json',
        success: function(data) {
            var dataSet = data.data;
            table = $('#designation_list_table').DataTable( {
              destroy:    true,
              paging:     true,
              searching:  true,
              ordering:   true,
              bInfo:      true,  
              "data":     dataSet,

            "columns": [
              { "data": "shift_role_name" },
              { "data": "shift_name" },
              { "data": "shift_time" },
              { "data": "start_date" },
              { "data": "end_date" }
            ],
            "order": [[0,'asc']]
            });
        }
      }); 

    }

   dataLoad();

});

</script>

@endsection