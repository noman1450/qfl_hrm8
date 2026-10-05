@extends('layouts.main')
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@endsection
@section('content')
<div class="box box-default">
  <div class="box-header with-border">
    <h3 class="box-title">KPI Task List</h3>
    
    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
      Create New
      </button>

      <button type="button" class="btn pull-right" data-toggle="modal" data-target="#exampleModal2">
      Task Type Create
      </button>

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
              <th style="width: 30%">Task Type</th>
              <th style="width: 50%">Task Name</th>
              <th style="width: 10%">Edit</th>
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
  <script src="{{asset('bootstrap/js/bootstrap.js')}}"></script>
  <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>

  <script>

$(document).ready(function($) {
      $.ajax({
        type:   'POST', 
        url :   "{{URL::to('/')}}/kpi_task_list",
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
              { "data": "task_type_name" },
              { "data": "description" },
              { "data": "Link",
               "mRender": function (data, type, full) {
               return '<a  data-id="'+full.id+'" data-description="'+full.description+'" data-hrm_kpi_task_type_id="'+full.hrm_kpi_task_type_id+'"  class="btn btn-primary btn-flat btn-sm showme"><span class="glyphicon glyphicon-edit">Edit</a>';            
               }
              },                      
              { "data": "Link",
                "mRender": function (data, type, full) {
                    return '<a href="{{URL::to('/')}}/kpi_task/'+full.id+'/cancel"  onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-flat btn-sm"><span class="glyphicon glyphicon-trash">Delete</a>';
                }
              },

            ],
            "order": [[0,'asc']]
            });
        }
      }); 


     $('#list_table').on('click', '.showme', function(e){
          $('#task_name').val($(this).data('description'));
          $('#id').val($(this).data('id'));
          $('#exampleModal').modal('show');
          $('#hrm_kpi_task_type').val($(this).data('hrm_kpi_task_type_id'));


      });

    $('#exampleModal').on('hidden.bs.modal', function () {
     location.reload();
    })
    $('#exampleModal2').on('hidden.bs.modal', function () {
     location.reload();
    })



});

</script>


@endsection

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Create New Task Name</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
      </div>
      {!! Form::open(array('route'=>'kpi_task.store', 'onkeypress'=> "return event.keyCode != 13;", 'files'=>true, 'id'=>'frm_leave')) !!}
      {{ csrf_field() }}
      <div class="modal-body">
        


        <input type="text" name="id" id="id" hidden="">
        
        <div class="form-group has-feedback {{ $errors->has('date_to') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
          <label class="col-lg-12 control-label">Task Name</label>
          <div class="col-lg-12">
              <input class="form-control" type="text" placeholder="Task Name" id="task_name" style="width: 100%" name="task_name">
          </div>
        </div>

        <div class="form-group has-feedback {{ $errors->has('date_to') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
          <label class="col-lg-12 control-label">Task Type</label>
          <div class="col-lg-12">
            <select class="form-control" id="hrm_kpi_task_type" name="hrm_kpi_task_type">
              @foreach($task_type as $keys)
                <option value="{{$keys->id}}">{{$keys->task_type_name}}</option>
              @endforeach
            </select>
          </div>
        </div>


      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <input type="submit" class="btn btn-primary"   value="Save">
      </div>
      {!! Form::close() !!}
    </div>
  </div>
</div>


<div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Create New Task Type</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal" method="POST" action="{{url('kpi_task_type')}}">
      {{ csrf_field() }}
      <div class="modal-body">
        
        <div class="form-group has-feedback {{ $errors->has('date_to') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
          <!-- <label class="col-lg-12 control-label">Task Type Name</label> -->
          <div class="col-lg-12">
              <input class="form-control" type="text" placeholder="Task Type Name" id="task_type_name" style="width: 100%" name="task_type_name">
          </div>
        </div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <input type="submit" class="btn btn-primary"   value="Save">
      </div>
      </form>
    </div>
  </div>
</div>