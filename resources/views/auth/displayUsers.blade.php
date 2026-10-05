@extends('layout.main')
@section('styles')
<!-- DataTables -->
 <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@endsection
@section('content')
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">User List</h3>
            </div>

            <a href="{{ URL::to('auth/register')}}"><input type="button" class="btn-success btn-sm block btn-flat btn" style="margin-right: 15px;"  value="Create New User">
            </a>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="users-data" class="table table-bordered table-hover">
                <thead>
                <tr>
                  <th>User Name</th>
                  <th>Email</th>
                  <th>Current Role</th>
                  <th>Edit</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
@endsection

@section('script')
<!-- DataTables -->
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script>



$(document).ready(function($) {

      var table = $('#users-data').DataTable( {
          // destroy:        true,
          paging:         true,
          searching:      true,
          ordering:       true,
          bInfo:          false,
          "ajax": "{{URL::to('/')}}/auth/getusers",
          "columns": [
          // {
          //   "className":      'details-control',
          //   "orderable":      false,
          //   "data":           null,
          //   "defaultContent": ''
          // },
              { "data": "username" },
              { "data": "email" },  
              { "data": "role" },
              { "data": "Link",
                "mRender": function (data, type, full) {
                  return '<a href="{{URL::to('/')}}/permission/'+full.id+'/submit_user_role" target="_blank" > <span class="glyphicon glyphicon-edit"></span> Edit</a>';
                }
              },      
            ],
            "order": [[1, 'asc']]
        });
});

</script>
@endsection

