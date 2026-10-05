<!-- location_list -->
@extends('layouts.main')
<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
@endsection
<!-- content -->
@section('content')
<div class="box box-default">
  <div class="box-header with-border">
    <h3 class="box-title">Shift Role Assign</h3>
    
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>

    
    <div class="box-body">
      <form  method="POST" action="{{url('shiftroleassigntoemployee')}}">
      {{ csrf_field() }}
      <div class="row">
          

          <div class="col-lg-12 col-md-12 col-xs-12 form-group">  
            <div class="col-lg-6 col-md-6 col-xs-12">  
                <label>Shift Role List</label>
                <select class="form-control" id="shiftrole" name="shiftrole" style="width: 100%;"  required>
                </select>                
            </div>
          </div>

<!--           <div class="col-lg-12 col-md-12 col-xs-12 form-group current-shift">  
            <div class="col-lg-6 col-md-6 col-xs-12">  
                <label>Current Shift</label>
                <select class="form-control" id="current_shift" name="current_shift" style="width: 100%;"  required>
                </select>                
            </div>
          </div>
 -->
          <div class="col-lg-12 col-md-12 col-xs-12 form-group">  
            <div class="col-lg-6 col-md-6 col-xs-12">  
                <label>Apply New Shift</label>
                <select class="form-control" id="working_shift" name="working_shift" style="width: 100%;"  required>
                </select>                
            </div>
          </div>



          <div class="col-lg-12 col-md-12 col-xs-12 form-group">  
                <div class="col-lg-6 col-md-6 col-xs-12">  
                      <label>Apply Date</label>
                      <div class="input-group date">
                          <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                          </div>                
                          <input type="text" class="form-control pull-right" id="apply_date" name="apply_date" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('apply_date') }}" required readonly>
                        @if ($errors->has('apply_date'))
                            <span class="help-block">
                                <strong>{{ $errors->first('apply_date') }}</strong>
                            </span>
                        @endif                  
                      </div>                
                </div>
          </div>



          <div class="col-lg-12 col-md-12 col-xs-12 form-group">  
            <div class="col-lg-6 col-md-6 col-xs-12">                              
            <input type="submit"  value="Shift Assign" class=" btn-sm btn-success block btn-flat btn" style="padding: 7px 10px; color: black; border:1px solid gray;"> 
            </div>
          </div>            
      </div>
      </form>
    </div>


                        

  </div>
</div>

  @endsection

  
  <!-- script -->
  @section('script')
  
  <script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
  <script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
  <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
  <script src="{{asset('bootstrap/js/bootstrap.js')}}"></script>
  <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
  <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
  <script>

$(document).ready(function($) {

    $('#apply_date').datepicker({
       // startDate: new Date() ,
       autoclose: true
    });

    $(".current-shift").hide();

    $role = $('#shiftrole').select2({
      placeholder: 'Enter a Shiftrole',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/shiftrole_list_data',
        delay: 250,
        data: function(params) {
          return {
            term: params.term
          }
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data
          };
        },
        cache: true
      }
    });

    // $role.on("select2:select", function (e) {
    //     $.ajax({
    //         url: '{{URL::to('/')}}/getcurrentshift',
    //         method: 'POST',
    //         headers:{'X-CSRF-TOKEN': '{{ csrf_token() }}'}, 
    //         data:   {shiftrole: $("#shiftrole").val()}, 
    //         dataType: 'json',
    //         success: function(data) {
    //           if(data != ''){
    //             $('#current_shift').append('<option value="' + data[0].id + '">' + data[0].shift_name + '</option>');
    //             $(".current-shift").show();
    //           }else{
    //             $(".current-shift").hide();
    //           }
    //         }
    //     });      
    // });

    // $role.on("select2:unselect", function (e) { 
    //   $(".current-shift").hide();
    // });


    $('#working_shift').select2({
      placeholder: 'Enter working shift',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/shift_list_data',
        delay: 250,
        data: function(params) {
          return {
            term: params.term
          }
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data
          };
        },
        cache: true
      }
    }); 




 $.ajax({
        type:   'POST', 
        url :   "{{URL::to('/')}}/shiftrole_currentshift",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
        data:   {
                  generate_type: 2,                  
                },         
        dataType: 'json',
        success: function(data) {
            var dataSet = data.data;
            table = $('#list_table2').DataTable( {
              destroy:    true,
              paging:     true,
              searching:  true,
              ordering:   true,
              bInfo:      true,  
              "data":     dataSet,

            "columns": [
              { "data": "shift_role_name" },
              { "data": "shift_name" },
              { "data": "start_date" },
     

            ],
            "order": [[0,'asc']]
            });
        }
      }); 

});

</script>

@endsection

