    @extends('layouts.main')
    @section('styles')
    <!-- DataTables -->
     <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
    @endsection        
    @section('content')

    <section class="content">
      <!-- SELECT2 EXAMPLE -->
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Roles Assign</h3>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
          </div>
        </div>
        <!-- /.box-header -->
        {!! Form::open(array('url' => '/permission/submit_user_role', 'id' => 'role-form')) !!}

        <div class="box-body">
            <!-- /.box-header -->
            <!-- <div class="box-body no-padding"> -->
            <div class="col-xs-12" style="padding-bottom: 10px;">            
              <!-- <table class="table table-striped" id="role_list"> -->
              <table id="role_list" class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>Users</th>
                    <th>Role</th>
                    <th>Edit</th>
                  </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                <tr>
                  <td><input value="{{$user->user_id}}" class="hidden">{{$user->username}}</td>
                  <td>
                    {{$user->name}}
                  </td>
                  <td><a href="{{URL::to('/')}}/permission/{{$user->user_id}}/user_role">
                  <span class="glyphicon glyphicon-edit"></span>
                  Edit</a></td>
                </tr>
                @endforeach
                </tbody>
              </table>
            </div>
            <!-- /.box-body -->
        </div>        

      </div>

      {!! Form::close() !!}
      <!-- /.box-body -->
      <div class="form-group">
       @if (count($errors) > 0)
       <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif
    </div>      
</section>

@endsection

  @section('script')
  <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>    
  <script>
    $(document).ready(function() {
      var table = $('#role_list').DataTable( {
          // paging:         false,
          // searching:      false,
          // ordering:       true,
          // bInfo:          false,            
      });      
    });      
  </script>


  <script>
  $("#role-form").validate({
   rules: {
      // simple rule, converted to {required:true}
      name: {"required":true, "minlength": 2}
    },
    messages: {
      name: {"required":"Please specify your username", "minlength": "minlength"}
    }
  });

  </script>
@endsection


