<!-- marital_status_list -->
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
		<h3 class="box-title">Current Month Mobile App User</h3>


     <div class="col-xs-12" >
           

           <div class="col-lg-3 col-md-3 col-xs-12 form-group" >
              <select  class="form-control col-lg-12 year onchange" id="year" name="year" style="width: 100%;"  >
                <option value="{{$cyear}}">{{$cyear}}</option>
              </select>
            </div>

            <div class="col-lg-3 col-md-3 col-xs-12 form-group" >
              <select  class="form-control col-lg-12 onchange month_name" id="month_name" name="month_name" style="width: 100%;"  >
                <option value="{{$cmonth->id}}">{{$cmonth->month_name}}</option>
              </select>
            </div>

           <div class="col-lg-3 col-md-3 col-xs-12 form-group" >
            <div>
                  
                 <input type="" name="total_user" id="total_user"  style="font-size: 20px; text-align: center;color: red; font-weight: bold;" readonly="">
              
            </div>
                  
            </div>
           <div class="col-lg-3 col-md-3 col-xs-12 form-group" >
            </div>


    </div>

    <div>





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
              <th></th>
              <th style="width: 5%"></th>
              <th style="width: 22%">Employee Name</th>
              <th style="width: 5%">Unique Code</th>
              <th style="width: 15%">Designation</th>
              <th style="width: 13%">Department</th>
              <th style="width: 12%">Joining</th>
              <th style="width: 10%">Contact</th>
              <th style="width: 13%">Job Placement</th>
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
<script>
$(document).ready(function($) {

    for (i = new Date().getFullYear(); i >= 2018; i--){
         $('.year').append($('<option />').val(i).html(i));
    }

  $transport =  $('#month_name').select2({
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


    $transport.on("select2:select", function (e) {
      get_app_user_data();
      dataLoad();
    });

    $transport.on("select2:unselect", function (e) { 
      get_app_user_data();
      dataLoad();
    });






    $("#year").change(function () {
      get_app_user_data();
      dataLoad();
    });


      get_app_user_data = function(){
      
        $.ajax({ 
          type: 'POST', 
          url: "{{URL::to('/')}}/get_current_month_app_user",
          headers:{'X-CSRF-TOKEN': '{{ csrf_token() }}'},
          data:   {  
                year_id  : $('.year option:selected').val(),
                month_id : $('.month_name option:selected').val()
              }, 
          dataType: 'json',
          success: function(data){ 
            if (data !=''){
              $('#total_user').val(data.app_user_data[0].count_id);


            }else{
            
              $('#total_user').val(0);

            }
        }
      });

    };

dataLoad = function(){
         var table = $('#list_table').DataTable( {
            "destroy":    true,
            "processing": true,
            "serverSide": true,
            "searching":  true,
            "ordering":   true,
            "bInfo":      true,
            "paging":     true,
            "aoColumnDefs": [{ "bVisible": false, "aTargets": [0,1] }],
            "ajax": {
                "url": "{{URL::to('/')}}/get_current_month_app_user_list",
                "type": "POST",
                "headers":{'X-CSRF-TOKEN': '{{ csrf_token() }}'},  
                "data":   {year_id: $("#year").val(),
                           month_id: $("#month_name").val(),

              }                
            },

            "columns": [
              { "data": "priority" },                     
              { "data": "employee_name" },                     
              {
                "render": function (data, type, JsonResultRow, meta) {
                    return '<img src="{{asset('employee_image')}}/'+JsonResultRow.Images+'" style="height:30px; width:30px; border-radius: 30px;"/>';
                }
              },               
              { "data": "Link",
                "mRender": function (data, type, full) {
                    return '<a target="_blank"  href="{{URL::to('/')}}/employeeinfo/'+full.id+'">'+full.employee_name+'</a>';
                }
              },
              { "data": "Unique_Code" },                     
              { "data": "designation_name" },                     
              { "data": "depertment_name" },                     
              { "data": "joining_date" },  
              { "data": "contact_number" },                     
              { "data": "location_name" },                     
            ],
            "order": [[0, 'asc']]
          });

   }



  dataLoad();
  get_app_user_data();


});

</script>

@endsection