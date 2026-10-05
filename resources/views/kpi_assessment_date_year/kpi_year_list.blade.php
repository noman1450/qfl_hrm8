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
    <h3 class="box-title">KPI Year Setup List</h3>
    
    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
      Create Year
      </button>
    </div>
    
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>
  
  <div class="box-body">
    <div class="row">
      <div class="form-group col-lg-5 col-md-5 col-xs-12" style="overflow: auto;">
        <table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th style="width: 80%">Assessment Year</th>
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
            url :   "{{URL::to('/')}}/kpi_year_list",
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
                  { "data": "description" },
                  { "data": "Link",
                   "mRender": function (data, type, full) {
                   return '<a  data-id="'+full.id+'" data-description="'+full.description+'"  class="btn btn-primary btn-flat btn-sm showme"><span class="glyphicon glyphicon-edit">Edit</a>';            
                   }
                  }, 

                  // { "data": "Link",
                  //   "mRender": function (data, type, full) {
                  //       return '<a href="{{URL::to('/')}}/kpi_year/'+full.id+'/cancel"  onclick="return confirm(\'Do you really want to   DELETE?\');" class="btn btn-danger btn-sm btn-flat"><span class="glyphicon glyphicon-trash">Delete</a>';
                  //   }
                  // },

                ],
                "order": [[0,'asc']]
                });
            }
          }); 


         $('#list_table').on('click', '.showme', function(e){
              $('#assessment_year').val($(this).data('description'));
              $('#id').val($(this).data('id'));
              $('#exampleModal').modal('show');
          });

        $('#exampleModal').on('hidden.bs.modal', function () {
         location.reload();
        })



    });

</script>


@endsection
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Create New Year</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
      </div>
      {!! Form::open(array('route'=>'kpi_assessment_date_year.store', 'onkeypress'=> "return event.keyCode != 13;", 'files'=>true, 'id'=>'frm_leave')) !!}
      {{ csrf_field() }}
      <div class="modal-body">
        
        <div class="form-group has-feedback {{ $errors->has('date_to') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
          <label class="col-lg-4 control-label">Assessment Year</label>
          <div class="col-lg-8">
            <div class="input-group date">
              <input type="text" name="id" id="id" hidden="">
              <input class="form-control" type="text" placeholder="2020 0r 2020-2021" id="assessment_year" name="assessment_year">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <!-- <button type="button" class="btn btn-primary">Save</button> -->
        <input type="submit" class="btn btn-primary"   value="Save">
      </div>
      {!! Form::close() !!}
    </div>
  </div>
</div>
<!--End Modal -->