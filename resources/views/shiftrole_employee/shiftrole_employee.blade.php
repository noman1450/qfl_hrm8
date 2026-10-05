<!-- attandance_list -->
@extends('layouts.main')
<!-- styles -->
@section('styles')

<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables/jquery.dataTables.min.css')}}">

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
  }

  td.details-control {
      background: url('{{URL::to('/')}}/dist/img/details_open.png') no-repeat center center;
      cursor: pointer;
  }
  tr.shown td.details-control {
      background: url('{{URL::to('/')}}/dist/img/details_close.png') no-repeat center center;
  } 
</style>
@endsection


@section('content')
<div class="box box-default">
  <div class="box-header with-border">
    <h3 class="box-title">Add Employee Shift Role</h3>
 
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>

      <!-- <div class="row" style="margin-left:10px; "> -->
      <div class="box-body">
        <div class="row">
        <form  method="POST" action="{{url('shiftroleemployee')}}">
        {{ csrf_field() }} 
          <!-- <div class="form-group col-lg-12 col-md-12 col-xs-12"> -->
            
            <div class="col-lg-6 col-md-6 col-xs-12 form-group">  
                <select class="form-control select2" id="shiftrole" name="shiftrole" style="width: 100%;"  required>
                </select>                 
            </div>

<!--             <div class="col-lg-6 col-md-6 col-xs-12 form-group">
              <input type="button" id="search" value="Search" class=" btn-sm block btn-flat btn" style="margin-right: 15px; padding: 7px 10px;background-color: #EEEEEE; color: black; border:1px solid gray;">                  
            </div> -->
      <div class="col-lg-6 col-md-6 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
          <a href="{{ URL::to('shiftroleemployeelist')}}"><input type="button" value="Role Wise Employee List" class="btn-success btn btn-sm button pull-right btn-flat" style="font-size: 12px; font-weight: bold;"></a>
      </div>

            <div class="col-lg-12 col-md-12 col-xs-12">    
              <table id="list_table" class=" cell-border table table-bordered table-hover "  cellspacing="0" width="100%">
                <thead>
                  <tr>
                   <th  style="width: 5%"><input name="select_all" value="1" id="example-select-all" type="checkbox" /> All</th>
                    <th style="width: 20%">Employee Name</th>
                    <th style="width: 10%">Emp. Code</th>
                    <th style="width: 15%">Department</th>
                    <th style="width: 15%">Designation</th>
                    <th style="width: 15%">Shift Name</th>
                    <th style="width: 10%">Start Time</th>
                    <th style="width: 10%">End Time</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>

            <div class="col-lg-12 col-md-12 col-xs-12">    
              <input type="submit"  value="Submit" class=" btn-sm btn-success block btn-flat btn"> 
            </div>
          </form> 
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
            url :   "{{URL::to('/')}}/remaining_shiftrole_data",
            headers:{
                      'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }, 
            data:   {
                      shiftrole: $("#shiftrole").val(),   
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

                "columns": [
                  { "data": "checkbox",
                          "mRender": function (data, type, full) {
                          return '<input type="checkbox" name="id[]" value="'+full.id+'">';
                  }
                  },
                  { "data": "employee_name" },
                  { "data": "card_code" },
                  { "data": "depertment_name" },
                  { "data": "designation_name" },
                  { "data": "shift_name" },
                  { "data": "start_time" },
                  { "data": "end_time" },


                ],
                "order": [[0,'asc']]
                });
            }
          })
  }





  function isBlank(str) {
    return (!str || /^\s*$/.test(str));
  }

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


   $('#example-select-all').on('click', function(){
      var rows = table.rows({ 'search': 'applied' }).nodes();
      $('input[type="checkbox"]', rows).prop('checked', this.checked);
   });


   $('#list_table tbody').on('change', 'input[type="checkbox"]', function(){
      if(!this.checked){
         var el = $('#example-select-all').get(0);
         if(el && el.checked && ('indeterminate' in el)){
            el.indeterminate = true;
         }
      }
   });
});



</script>

@endsection