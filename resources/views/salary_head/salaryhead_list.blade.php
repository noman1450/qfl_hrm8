<!-- location_list -->
@extends('layouts.main')
<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@endsection
<!-- content -->
@section('content')
<div class="box box-default">
  <div class="box-header with-border">
    <h3 class="box-title">Salary Head List</h3>
    
    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">

          <a href="{{ URL::to('salaryhead/create')}}"><input type="button" value="Add New Salary Head" class="btn-success btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold;"></a>


    </div>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>
  
  <div class="box-body">
    <div class="row">
      

      <div class="form-group col-lg-6 col-md-6 col-xs-12">
        <label class="form-control" style="color: black; background-color: gray">Addition List</label>
        <div style="overflow: auto;">
        <table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th style="width: 22%">Add Type</th>
              <th style="width: 30%">Group Name</th>
              <th style="width: 30%">Salary Head</th>
              <th style="width: 10%">Edit</th>
              <!-- <th style="width: 10%">Delete</th> -->
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
        </div>
      </div>

      <div class="form-group col-lg-6 col-md-6 col-xs-12">
        <label class="form-control" style="color: black; background-color: gray">Deduction List</label>
        <div style="overflow: auto;">

        <table id="list_table2" class="table table-bordered table-hover" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th style="width: 22%">Add Type</th>
              <th style="width: 30%">Group Name</th>
              <th style="width: 30%">Salary Head</th>
              <th style="width: 10%">Edit</th>
              <!-- <th style="width: 10%">Delete</th> -->
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
        </div>
        
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

  <script>

$(document).ready(function($) {
      $.ajax({
        type:   'POST', 
        url :   "{{URL::to('/')}}/salaryhead_list",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
        data:   {
                  generate_type: 1,                  
                },        
        dataType: 'json',
        success: function(data) {
            var dataSet = data.data;
            table = $('#list_table').DataTable( {
              destroy:    true,
              paging:     false,
              searching:  true,
              ordering:   false,
              bInfo:      false,  
              "data":     dataSet,

            "columns": [
              { "data": "generate_type" },
              { "data": "group_name" },
              { "data": "salary_head" },
              { "data": "Link",
                "mRender": function (data, type, full) {
                  
                if(full.is_delete==1){
                    return '<a href="{{URL::to('/')}}/salaryhead/'+full.id+'/edit"> <span class="glyphicon glyphicon-edit"></span> Edit</a>';
                  }else{
                    return '-';
                }

                }
              },                     
              // { "data": "Link",
              //   "mRender": function (data, type, full) {
              //       return '<a href="{{URL::to('/')}}/salaryhead/'+full.id+'/cancel"  onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-sm btn-flat"><span class="glyphicon glyphicon-trash">Delete</a>';
              //   }
              // },

            ],
            "order": [[0,'asc']]
            });
        }
      }); 



 $.ajax({
        type:   'POST', 
        url :   "{{URL::to('/')}}/salaryhead_list",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
        data:   {
                  generate_type: 2,                  
                },         
        dataType: 'json',
        success: function(data) {
            var dataSet = data.data;
            table = $('#list_table2').DataTable( {
              destroy:    true,
              paging:     false,
              searching:  true,
              ordering:   false,
              bInfo:      false,  
              "data":     dataSet,

            "columns": [
              { "data": "generate_type" },
              { "data": "group_name" },
              { "data": "salary_head" },
              { "data": "Link",
                "mRender": function (data, type, full) {
                  if(full.is_delete==1){
                    return '<a href="{{URL::to('/')}}/salaryhead/'+full.id+'/edit"> <span class="glyphicon glyphicon-edit"></span> Edit</a>';
                  }else{
                    return '-';
                  }
                }
              },                     
              // { "data": "Link",
              //   "mRender": function (data, type, full) {
              //       return '<a href="{{URL::to('/')}}/salaryhead/'+full.id+'/cancel"  onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-sm btn-flat"><span class="glyphicon glyphicon-trash">Delete</a>';
              //   }
              // },

            ],
            "order": [[0,'asc']]
            });
        }
      }); 

});

</script>

@endsection