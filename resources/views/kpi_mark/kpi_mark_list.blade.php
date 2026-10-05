@extends('layouts.main')
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@endsection
@section('content')
<div class="box box-default">
  <div class="box-header with-border">
    <h3 class="box-title">KPI Mark List</h3>

    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
      Create New
      </button>
    </div>

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>

  <div class="box-body">
    <div class="row">
      <div class="form-group col-lg-7 col-md-7 col-xs-12" style="overflow: auto;">
        <table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th style="width: 40%">Mark Name</th>
              <th style="width: 30%">Point</th>
              <!-- <th style="width: 15%">Edit</th> -->
              <th style="width: 15%">Delete</th>
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
        url :   "{{URL::to('/')}}/kpi_marks_list",
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
              { "data": "point" },
              // { "data": "Link",
              //  "mRender": function (data, type, full) {
              //  return '<a  data-id="'+full.id+'" data-description="'+full.description+'" data-point="'+full.point+'"  class="btn btn-primary btn-flat btn-sm showme"><span class="glyphicon glyphicon-edit">Edit</a>';
              //  }
              // },
              { "data": "Link",
                "mRender": function (data, type, full) {
                    return '<a href="{{URL::to('/')}}/kpi_mark/'+full.id+'/cancel"  onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-flat btn-sm"><span class="glyphicon glyphicon-trash">Delete</a>';
                }
              },

            ],
            "order": [[0,'asc']]
            });
        }
      });


     $('#list_table').on('click', '.showme', function(e){
          $('#mark_name').val($(this).data('description'));
          $('#point').val($(this).data('point'));
          $('#id').val($(this).data('id'));
          $('#exampleModal').modal('show');
      });

    $('#exampleModal').on('hidden.bs.modal', function () {
     location.reload();
    })



});

</script>
@endsection
