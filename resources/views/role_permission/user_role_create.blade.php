  @extends('layouts.main')
  @section('styles')
  <!-- DataTables -->
  <link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker-bs3.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/plugins/iCheck/all.css')}}">
  @endsection
  @section('content')

  <section class="content">
    <!-- SELECT2 EXAMPLE -->
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">User Role assigned page</h3>

        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
        </div>
      </div>
      <!-- /.box-header -->

      {!! Form::open(['method'=>'POST', 'action'=>['PermissionsController@add_user_role'], 'id'=>'add-new-customer-form']) !!}
      
      <div class="box-body">
        <h2>User info</h2>
        <div class="row">
          <div class="col-md-4">

            <div class="form-group">
              <label>User Id</label>
              <input type="number" id="user-id"name="user_id" class="form-control"placeholder="{{$user->id}}" value="{{$user->id}}" readonly>
            </div>

          </div>
          <div class="col-md-4">

            <div class="form-group">
              <label>User Name</label>
              <input type="text" id="user-name"name="user_name" class="form-control"placeholder="{{$user->name}}"  value="{{$user->name}}" readonly>
            </div>

          </div>

          <div class="col-md-4" >
            <div class="form-group">
              <label>User Email Address</label>
              <input type="email" id="email"name="email" class="form-control"placeholder="{{$user->email}}" value="{{$user->email}}" readonly>
            </div>
          </div>
      </div>
      

          <div class="box-body">
            <h3>Assign Roles</h3>
             <div class="box box-success">
              <div class="box-header">
              <h3 class="box-title">Check</h3>
            </div>
            <div class="box-body">
              <!-- Minimal style -->

              <!-- checkbox -->
              <div class="form-group">
                <ul>
                
                @foreach($roles as $role)
                  @if($role->role_name!=null)
                  <li>
                    <label>

                          @if(isset($role) AND $role->role_id==$role->assigned_role_id)
                              <input type="checkbox" class="minimal" value="{{$role->role_id}}" name="role[{{$role->role_id}}]" checked>
                              {{$role->role_name}}
                          @else
                               <input type="checkbox" class="minimal" value="{{$role->role_id}}" name="role[{{$role->role_id}}]" >
                              {{$role->role_name}}
                          @endif 

                    </label>
                   </li> 
                  @endif  
                @endforeach

                </ul>
                

              
              </div>

              <!-- radio -->
             
              <!-- Minimal red style -->


              

            </div>
            
          </div>
            
                  <!-- ./col -->
                  <button type="submit" class="btn btn-primary center-block btn-flat">Submit</button>
                </div>
                
                

                {!! Form::close() !!}
              </section>


            </div>
          </div> 


          
          <!-- /.box-body -->

        </div>
        <!-- /.box -->

      </section>

      @if (count($errors) > 0)
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif



      @endsection



      @section('script')
      <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
      <script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
      <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
      <script src="{{asset('plugins/timepicker/bootstrap-timepicker.min.js')}}"></script>
      <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
      <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
      <script src="{{asset('dist/js/utils.js')}}"></script>

      <script type="text/javascript">
      $( document ).ready(function() {

       
        //iCheck for checkbox and radio inputs
    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
      checkboxClass: 'icheckbox_minimal-blue',
      radioClass: 'iradio_minimal-blue'
    });
    //Red color scheme for iCheck
    $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
      checkboxClass: 'icheckbox_minimal-red',
      radioClass: 'iradio_minimal-red'
    });
    //Flat red color scheme for iCheck
    $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass: 'iradio_flat-green'
    });


        

        


});
  </script>
  @endsection


