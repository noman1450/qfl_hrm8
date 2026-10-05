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
    <h3 class="box-title">Leave Year List</h3>
    
      <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
          <a href="{{ URL::to('leaveyear/create')}}"><input type="button" value="Create New" class="btn-success btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold;"></a>
      </div>

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>
  
  <div class="box-body">
    <div class="row">
      <div class="form-group col-lg-8 col-md-8 col-xs-12" style="overflow: auto;">
        <table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th style="width: 20%">Year</th>              
              <th style="width: 20%">Date From </th>
              <th style="width: 20%">Date To</th>
              <th style="width: 20%">Edit</th>
              <th style="width: 20%">Delete</th>
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

  <script>

$(document).ready(function($) {
      $.ajax({
        type:   'POST', 
        url :   "{{URL::to('/')}}/leaveyear_list",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
              { "data": "leave_year" },
              { "data": "date_from" },
              { "data": "date_to" },
              { 
                  "data": "Link",
                  "mRender": function (data, type, full) {
                     
                      let today = new Date();
                      today.setHours(0,0,0,0); 

                    
                      let dateTo = new Date(full.date_to);

                      if (dateTo >= today) {
                          return '<a href="{{ url('/') }}/leaveyear/' + full.id + '/edit"> <span class="glyphicon glyphicon-edit"></span> Edit</a>';
                      }

                      return '';
                  }
              }  , 
              { 
                  "data": "Link",
                  "mRender": function (data, type, full) {
                     
                      let today = new Date();
                      today.setHours(0,0,0,0); 

                    
                      let dateTo = new Date(full.date_to);

                      if (dateTo >= today) {
                          return '<a href="{{URL::to('/')}}/leaveyear/'+full.id+'/cancel"  onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-sm btn-flat"><span class="glyphicon glyphicon-trash">Delete</a>';
                      }

                      return '';
                  }
              }  ,                     
              

            ],
            "order": [[0,'asc']]
            });
        }
      }); 


});

</script>

@endsection