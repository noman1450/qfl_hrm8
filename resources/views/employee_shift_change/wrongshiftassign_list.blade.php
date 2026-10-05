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
		<h3 class="box-title">Wrong Shift Assign List</h3>
 


	    <div class="row" style="margin-left:10px; ">
	        
          <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">  
	        
              <div class="input-group date">
	                <div class="input-group-addon">
	                  <i class="fa fa-calendar"></i>
	                </div>		            
	                <input type="text" class="form-control pull-right" id="process_date" name="process_date" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('process_date') }}" required readonly>
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

          <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">  
              <select class="form-control" id="department" name="department" style="width: 100%;" >
              </select>                 
          </div>

          <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">  
              <select class="form-control" id="employee_name" name="employee_name" style="width: 100%;" >
              </select>    
          </div>

          <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">  
              <select class="form-control" id="section" name="section" style="width: 100%;" >
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

  <div id="alert-danger"></div>
  <div id="alert-success"></div>


  <form  method="POST" action="{{url('submitmultiple_shiftchange')}}">
        {{ csrf_field() }}    


	<div class="box-body">
		<div class="row">
			<div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">   	
				<table id="designation_list_table" class="table table-striped table-bordered"    width="100%">
					<thead >
						<tr>
             <!-- <th  style="width: 3%"><input name="select_all" value="1" id="example-select-all" type="checkbox" /></th> -->
              <th></th>
							<th style="width: 20%">Employee Name</th>
							<th style="width: 12%">Department</th>
              <th style="width: 12%">Designation</th>
							<th style="width: 10%">Category</th>
              <th style="width: 8%">In Time</th>
							<th style="width: 8%">Out Time</th>
							<th style="width: 20%">Current Assign Shift</th>
              <th style="width: 20%">Probabale Shift</th>
              <!-- <th style="width: 5%">Effective Date</th> -->
              <th style="width: 5%">Temporary</th>
              <th style="width: 5%">Permanent</th>
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



  $('#section').select2({
      placeholder: 'Enter Sub-department',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/section_list_data',
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

    $('#employee_name').select2({
      placeholder: 'Enter Employee Name',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/join_employee_list',
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
      // dataLoad();
      // dataLoad = function(){

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
        employee_name = 0;
      }else{
        employee_name = $("#employee_name").val();
      }
      
      if ($("#section").val() == null){
        section = 0;
      }else{
        section = $("#section").val();
      }


      $.ajax({
        type:   'POST', 
        url :   "{{URL::to('/')}}/wrongshiftassign_data",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },        
        data:   {
                   punch_date: $("#process_date").val(),
                   location      : location_id,
                   department_id : department_id,
                   employee_id: employee_name,
                   working_shift_id: working_shift,
                   section: section,
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
              "aoColumnDefs": [{ "bVisible": false, "aTargets": [0] }],  
              "data":     dataSet,
              // "columnDefs": [
              //     { "width": "2%", "targets": 0 },
              //     { "width": "15%", "targets": 1 },
              //     { "width": "15%", "targets": 2 },
              //     { "width": "15%", "targets": 3 },
              //     { "width": "15%", "targets": 4 },
              //     { "width": "15%", "targets": 5 },
              //     { "width": "15%", "targets": 6 },
              //     { "width": "3%", "targets": 7 },
              //     { "width": "5%", "targets": 8 },
              // ],
              "initComplete": function () {
                $( ".effect_date" ).datepicker({
                    autoclose: true,
                });                                
              },
              drawCallback: function() {
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
              },
              "columns": [

              // { "data": "checkbox",
              //         "mRender": function (data, type, full) {
              //         return '<input type="checkbox"  name="id[]" value="'+full.id+'">';
              // }
              // },
              { "data": "probable_shift_id" },
              { "data": "employee_name" },
              { "data": "depertment_name" },
              { "data": "designation_name" },
              { "data": "category_name" },
              { "data": "in_time" },
              { "data": "out_time" },
              { "data": "assign_shift" },
              { "data": "probable_shift" },
              // { "data": null,render:function(data,type,row){
              // return '<select class="form-control working_shift" style="width: 100%;" name="new_shift[]"></select>';
              // }},

              // { "data": "text",
              //         "mRender": function (data, type, full) {
              //         return '<input type="text" style="width:100%;" class="effect_date" data-date-format="dd-mm-yyyy" name="effect_date['+full.id+']"  value="'+full.punch_date+'">';
              //   }
              // },
              
              // { "data": "Link",
              //   "mRender": function (data, type, full) {
              //       return '<a href="{{URL::to('/')}}/multipleshift/'+full.id+'/change"  onclick="return confirm(\'Do you really want to Change?\');" class="btn btn-success btn-sm btn-flat" ><span class="glyphicon glyphicon-ok">Change</a>';
              //   }
              // },
             { "data": "button",
                "mRender": function (data, type, full) {
                  return '<input class="clickbutton btn btn-success btn-sm btn-flat" type="button" name="clickbutton" value="Temporary Change"  id="'+full.id+'" >';

                
              }
            },
            { "data": "button",
                "mRender": function (data, type, full) {
                  return '<input class="clickbuttonParmanent btn btn-info btn-sm btn-flat" type="button" name="clickbuttonParmanent" value="Parmanent Change"  id="'+full.id+'" >';

                
              }
            }
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

        var shift = table.row( $(this).parents('tr') ).data()['probable_shift_id'];

        var delete_object = $(this).parents('tr');
        // console.log(noman);
        if(confirm('Do you want to submit?')){
        $.ajax({
            method: 'POST',
            url: '{{URL::to('/')}}/wrong_to_correct_shift',
            headers:{'X-CSRF-TOKEN': '{{ csrf_token() }}'},              
            dataType: 'json',
            data:   {id   : $(this).attr('id'),
                     shift:  shift,
                     attendance_date: $("#process_date").val(),                 
                },   
            success: function(data) {
              if(data['success']) {
                    // alert(data.messages);
                // $('#alert-success').html(erreurs);   
                // $('#alert-success').show(0).delay(1000).hide(0); 
                table.row( delete_object ).remove().draw();
              }else{
                // var erreurs ='<div class="alert alert-danger"><ul>';
                    // erreurs += '<li>'+data.messages+'</li>';
                    // erreurs += '</ul></div>';
                     alert(data.messages);
                  // $('#alert-danger').html(erreurs);   
                  $('#alert-danger').show(0).delay(1000).hide(0);  
              }
            }
        });
        }else{
          return;
        }          
    })

    $('#designation_list_table tbody').on('click','.clickbuttonParmanent',function(){      

        var shift = table.row( $(this).parents('tr') ).data()['probable_shift_id'];

        var delete_object = $(this).parents('tr');
        // console.log(noman);
        if(confirm('Do you want to submit?')){
        $.ajax({
            method: 'POST',
            url: '{{URL::to('/')}}/wrong_to_correct_shift_permanent',
            headers:{'X-CSRF-TOKEN': '{{ csrf_token() }}'},              
            dataType: 'json',
            data:   {id   : $(this).attr('id'),
                     shift:  shift,
                     attendance_date: $("#process_date").val(),                 
                },   
            success: function(data) {
              if(data['success']) {
                    // alert(data.messages);
                // $('#alert-success').html(erreurs);   
                // $('#alert-success').show(0).delay(1000).hide(0); 
                table.row( delete_object ).remove().draw();
              }else{
                // var erreurs ='<div class="alert alert-danger"><ul>';
                    // erreurs += '<li>'+data.messages+'</li>';
                    // erreurs += '</ul></div>';
                     alert(data.messages);
                  // $('#alert-danger').html(erreurs);   
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