<!-- employee_list -->
@extends('layouts.main')

<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@endsection

<!-- content -->
@section('content')
<div class="box box-default">
  <div class="box-header with-border">
    <h3 class="box-title">Leave Rejected List</h3>
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
              <th style="width: 20%">Employee Name</th>
              <th style="width: 15%">Leave Type</th>
              <th style="width:  4%">Days</th>
              <th style="width: 11%">Date From</th>
              <th style="width: 10%">Date To</th>
              <th style="width: 25%">Comment</th>
              <th style="width: 10%">Status</th>
              <th style="width: 10%">Rejected By</th>
              <th style="width: 10%">Re-active</th>
              <th style="width: 10%">Print</th>
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
        type: 'GET',
        url: "{{ url('my_leaverejectedlist') }}",
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

              { "data": "Link",
                "mRender": function (data, type, full) {
                  return '<img src="{{asset('employee_image')}}/'+full.Images+'" style="height:30px; width:30px; border-radius: 30px;"/>';
                }
              },
              { "data": "Link",
                  "mRender": function (data, type, full) {
                    return '<a target="_blank" href="{{URL::to('/')}}/employeeinfo/'+full.id+'">'+full.employee_name+'</a>';
                }
              },
              { "data": "leave_type" },
              { "data": "days" },
              { "data": "date_from" },
              { "data": "date_to" },
              { "data": "comment" },
              { "data": "status" },
              { "data": "apply_to" },
              { "data": "Link",
                "mRender": function (data, type, full) {
                    return '<a href="{{URL::to('/')}}/leaverejectedlist/'+full.hrm_leave_approve_id+'/reactive"  onclick="return confirm(\'Do you really want to Reactive?\');" class="btn btn-warning btn-sm btn-flat"><span class="glyphicon glyphicon-share-alt"></span> Re-active</a>';
                }
              },
              { "data": "Link",
                "mRender": function (data, type, full) {
                    return '<a href="{{URL::to('/')}}/print_leave_form/'+full.leave_app_id+'"  target="_blank" class="btn btn-info btn-sm btn-flat"><span class="glyphicon glyphicon-print"></span> Print</a>';
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
