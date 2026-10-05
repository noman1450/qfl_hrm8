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

</style>
@endsection


@section('content')
<div class="box box-default">
	<div class="box-header with-border">
		<h3 class="box-title">Employee Id Card Print</h3>
 


	    <div class="row" style="margin-left:10px; ">

          <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">  
              <select class="form-control" id="location" name="location" style="width: 100%;" >
              @foreach ($default_user_location as $keys)
                  <option value={{$keys->id}} >{{$keys->location_name}}</option>
              @endforeach
              </select>                 
          </div>


          <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">  
              <select class="form-control employee_name" id="employee_name" name="employee_name" style="width: 100%;" >
              </select>                 
          </div>


          <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">  
              <select class="form-control working_shift" id="working_shift" name="working_shift" style="width: 100%;" >
              </select>                 
          </div>

          <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">  
              <select class="form-control" id="department" name="department" style="width: 100%;" >
              </select>                 
          </div>

          <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">  
              <select class="form-control" id="designation" name="designation" style="width: 100%;" >
              </select>    
          </div>


          <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
            <input type="button" id="search" value="Search" class=" btn-sm block btn-flat btn" style="margin-right: 15px; padding: 7px 10px;background-color: #EEEEEE; color: black; border:1px solid gray;">                  
          </div>



      	</div>


		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>

  <form  method="POST" action="{{url('submitemployeeidcard')}}">
        {{ csrf_field() }}    


	<div class="box-body">
		<div class="row">
			<div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">   	
				<table id="designation_list_table" class="table table-striped table-bordered"    width="100%">
					<thead >
						<tr>
             <th  style="width: 3%"><input name="select_all" value="1" id="example-select-all" type="checkbox" /></th>
							<th style="width: 20%">Employee Name</th>
							<th style="width: 15%">Department</th>
							<th style="width: 15%">Designation</th>
							<th style="width: 15%">Running Shift</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>

     <input type="submit"   value="Submit" class=" btn-sm btn-success block btn-flat btn" style="margin-left: 15px; padding: 7px 10px; color: black; border:1px solid gray;" > 

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
    
    // $('#process_date').datepicker({
    //   autoclose: true
    // });

    $employee = $('#employee_name').select2({
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

    $('#designation').select2({
      placeholder: 'Enter designation',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/designation_list_data',
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
 

    $("#search").click(function(){

      if ($("#employee_name").val() == null){
        employeeid = 0;
      }else{
        employeeid = $("#employee_name").val();
      }

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

      if ($("#designation").val() == null){
        designation_id = 0;
      }else{
        designation_id = $("#designation").val();
      }


      $.ajax({
        type:   'POST', 
        url :   "{{URL::to('/')}}/employeeidcardlistdata",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },        
        data:   {
                   employeeid    : employeeid,
                   location      : location_id,
                   department_id : department_id,
                   designation_id: designation_id,
                   working_shift_id: working_shift,
                   status:1,
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

              { "data": "checkbox",
                      "mRender": function (data, type, full) {
                      return '<input type="checkbox"  name="id[]" value="'+full.id+'">';
              }
              },
              { "data": "employee_name" },
              { "data": "depertment_name" },
              { "data": "designation_name" },
              { "data": "shift_name" },
              ],
              order: [ 1, 'asc' ]
            });
        }
      }); 
    });



   $('#example-select-all').on('click', function(){
      var rows = table.rows({ 'search': 'applied' }).nodes();
      $('input[type="checkbox"]', rows).prop('checked', this.checked);
   });


   $('#designation_list_table tbody').on('change', 'input[type="checkbox"]', function(){
      if(!this.checked){
         var el = $('#example-select-all').get(0);
         if(el && el.checked && ('indeterminate' in el)){
            el.indeterminate = true;
         }
      }
   });

// $('body').on('click', '#datatab tbody tr td.lastname', function () {

//   rowData = table.row( $(this).parents('tr') ).data();

//   console.log("First Name : ", rowData[0], "\t\tLast Name : ", rowData[1], "\t\tAge : ", rowData[2]);
//  });


    $('#designation_list_table tbody').on('click','.clickbutton',function(){      

        if(confirm('Do you want to submit?')){
        $.ajax({
            method: 'POST',
            url: '{{URL::to('/')}}/submitemployeeidcard',
            headers:{'X-CSRF-TOKEN': '{{ csrf_token() }}'},              
            dataType: 'json',
            data:   {id: $(this).attr('id'),
                  shift:  shift,
                  eff_date: eff_date,                  
                },   
            success: function(data) {
              if(data['success']) {
                var erreurs ='<div class="alert alert-success"><ul>';
                    erreurs += '<li>'+data.messages+'</li>';
                    erreurs += '</ul></div>';
                $('#alert-success').html(erreurs);   
                $('#alert-success').show(0).delay(1000).hide(0); 
              }else{
                  var erreurs ='<div class="alert alert-danger"><ul>';
                  $.each(data.errors, function(i,error){ 
                      erreurs += '<li>'+error+'</li>';
                  });
                  erreurs += '</ul></div>';
                  $('#alert-danger').html(erreurs);   
                  $('#alert-danger').show(0).delay(1000).hide(0);  
              }
            }
        });
        }else{
          return;
        }          
    })









});
</script>