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
    <h3 class="box-title">Salary Head Group List</h3>
    
    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
      <button type="button" class="btn-success btn btn-sm button pull-left btn-flat" data-toggle="modal" data-target="#modal_create_salary_grade" style="font-size: 12px; font-weight: bold;">Create New</button>
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
              <th style="width: 40%">Salary Head Grade</th>
              <th style="width: 20%">Generate Type</th>
              <th style="width: 20%">Group Status</th>
              <th style="width: 10%">Edit</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<!--  Create New Salary Grade Modal-->
<div class="modal fade" id="modal_create_salary_grade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="groupAddLabel">Add New Salary Group Head</h4>
      </div>
      
      <form method="POST" action="{{url('salaryheadgroup')}}">
        {{ csrf_field() }}
        <div class="modal-body">
          
          <div class="row">
            
            <div class="form-group">
              <div class="col-md-12">
                <label>Head Group Name</label>
                <input class="form-control" type="text" placeholder="Salary Head Group Name" name="group_name" required>
              </div>
            </div>

            <div class="form-group">
              <div class="col-md-12">
                <label>Generate Type</label>
                  <select class="form-control" name="generate_type">
                    <option value="1">Addition</option>
                    <option value="2">Deduction</option>
                  </select>
              </div>
            </div>

            <div class="form-group">
              <div class="col-md-12">
                <label>Group Status</label>
                  <select class="form-control" name="group_status">
                    <option value="1">Salary</option>
                    <option value="2">Bonus</option>
                    <option value="3">Loan</option>
                    <option value="4">PF</option>
                    <option value="5">Additional Salary</option>
                    <option value="6">Additional Deduction Salary</option>
                  </select>
              </div>
            </div> 
            
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" id="button_sunmit" class="btn btn-success btn-flat">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- End of Create New Salary Grade Modal-->


<!--  Edit Salary Grade Modal -->
<div class="modal fade" id="modal_form" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="border-bottom: 0px;height: 50px;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Edit Salary Grade Name</h4>
      </div>
      <div class="modal-body">
        {!! Form::open(array('url' => 'salaryheadgroupupdate', 'id' => 'add-account-group-form')) !!}
        
        <div class="row">
          
          <input class="hidden" type="text" id="id" name="id" hidden>
          
          
          <div class="form-group">
            <div class="col-md-12">
              <label>Head Group Name</label>
              <input class="form-control" type="text" placeholder="Salary Head Group Name"  id="group_name" name="group_name" required>
            </div>
          </div>

          <div class="form-group">
            <div class="col-md-12">
              <label>Generate Type</label>
                <select class="form-control" name="generate_type" id="generate_type_id">
                  <option value="1">Addition</option>
                  <option value="2">Deduction</option>
                </select>
            </div>
          </div>

          <div class="form-group">
            <div class="col-md-12">
              <label>Group Status</label>
                <select class="form-control" name="group_status" id="group_status_id">
                  <option value="1">Salary</option>
                  <option value="2">Bonus</option>
                  <option value="3">loan</option>
                  <option value="4">PF</option>
                  <option value="5">Additional Salary</option>
                  <option value="6">Additional Deduction Salary</option>

                </select>
            </div>
          </div> 
        
          
          <div class="modal-footer">
            <div class="col-lg-12 entry_panel_body ">
              <h3></h3>
              <button type="submit" class="btn btn-success btn-flat" id="add-account-group">Submit</button>
              <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Close</button>
            </div>
          </div>
          
        </div>
        {!! Form::close() !!}
        
      </div>
    </div>
  </div>
  </div>
  <!-- End  Edit Salary Grade Modal-->
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
        url :   "{{URL::to('/')}}/salaryheadgroup_list",
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
              bInfo:      false,  
              "data":     dataSet,

            "columns": [
              { "data": "group_name" },
              { "data": "generate_type" },
              { "data": "group_status" },
              { "data": "Link",
              "mRender": function (data, type, full) {

              
              if (full.is_delete === 1) {  
              return '<a  data-id="'+full.id+'" data-group_name="'+full.group_name+'" data-generate_type_id="'+full.generate_type_id+'" data-group_status_id="'+full.group_status_id+'" class="glyphicon glyphicon-edit btn btn-primary btn-flat btn-sm showme">Edit</a>';
              }else{
               return '-';
              }            
              }
              },                       
              // { "data": "Link",
              //   "mRender": function (data, type, full) {
              //       return '<a href="{{URL::to('/')}}/salaryheadgroup/'+full.id+'/cancel"  onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-sm btn-flat"><span class="glyphicon glyphicon-trash">Delete</a>';
              //   }
              // },

            ],
            "order": [[0,'asc']]
            });
        }
      }); 


       $('#list_table').on('click', '.showme', function(e){
            $('#group_name').val($(this).data('group_name'));
            $('#generate_type').val($(this).data('generate_type_id'));
            $('#group_status').val($(this).data('group_status_id'));
            $('#id').val($(this).data('id'));

            $('#modal_form').modal('show');
        });



});

</script>

@endsection