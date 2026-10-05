<!-- designation_list -->
@extends('layouts.main')

<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@endsection

<!-- content -->
@section('content')
<div class="box box-default">
	<div class="box-header with-border">
		<h3 class="box-title">Plant With Sub-Department List</h3>
	    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
	        <a href="{{ URL::to('plantwithsection/create')}}"><input type="button" value="Create New" class="btn-success btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold;"></a>
	    </div>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>
	

	<div class="box-body">
   <div class="row">
     <div class="col-md-10" style="overflow: auto;">
  		<table id="designation_list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
  			<thead>
  				<tr>
  					<th style="width: 30%">Plant Name</th>
  					<th style="width: 50%">Sub-Department</th>
  					<!-- <th style="width: 10%">Edit</th> -->
  					<th style="width: 10%">Deactive</th>
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

<script>
$(document).ready(function($) {

      $.ajax({
        type:   'POST', 
        url :   "{{URL::to('/')}}/plantwithsection_list",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
              { "data": "plant_name" },
              { "data": "section_name" },
              // { "data": "Link",
              //   "mRender": function (data, type, full) {
              //     return '<a href="{{URL::to('/')}}/plant/'+full.id+'/edit"> <span class="glyphicon glyphicon-edit"></span> Edit</a>';
              //   }
              // },                       
              
              { "data": "Link",
                "mRender": function (data, type, full) {
                   if(full.valid == 1 ){
                    return '<a href="{{URL::to('/')}}/plantwithsection/'+full.id+'/cancel"  onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-sm btn-flat"><span class="glyphicon glyphicon-ok">Deactive</a>';
                  }else{
                    return '<a href="{{URL::to('/')}}/plantwithsection/'+full.id+'/reactive"  onclick="return confirm(\'Do you really want to Reactive?\');" class="btn btn-warning btn-sm btn-flat"><span class="glyphicon glyphicon-ok">Re-active</a>';

                  }
                }
              },



            ],
            "order": [[0,'asc']]
            });
        }
      }); 
});

</script>

@endsection