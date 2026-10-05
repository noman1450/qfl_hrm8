
@extends('layouts.main')
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

@endsection
@section('content')

  
  <div class="box box-primary">
    <div class="box-header with-border">
      <h3 class="box-title">OT Process Generate</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      </div>
    </div>
    <form class="form-horizontal" method="POST" action="{{url('manual_ot_generate_submit')}}">
    {{ csrf_field() }}
    <div class="box-body">
      
      <div class="row">
        <div class="col-md-6 col-xs-12 col-lg-6">
          <div class="form-group col-lg-12 col-md-12 col-xs-12">
            <div id="alert-danger"></div>
          </div>
          <div class="form-group col-lg-12 col-md-12 col-xs-12">
            <div id="alert-success"></div>
          </div>


          <div class="form-group has-feedback {{ $errors->has('location') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
            <label class="col-lg-3 control-label">Location</label>
            <div class="col-lg-9">
              
              <select  class="form-control col-lg-12 location" id="location" name="location" style="width: 100%;" >
                @foreach ($default_user_location as $keys)
                <option value={{$keys->id}} selected>{{$keys->location_name}}</option>
                @endforeach
              </select>
              
            </div>
          </div>
          <div class="form-group has-feedback {{ $errors->has('year') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
            <label class="col-lg-3 control-label">Year</label>
            <div class="col-lg-9">
              <select  class="form-control col-lg-12 year" id="year" name="year" style="width: 100%;" >
              </select>
            </div>
          </div>
          <div class="form-group has-feedback {{ $errors->has('month_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
            <label class="col-lg-3 control-label">Month Name</label>
            <div class="col-lg-9">
              
              <select  class="form-control col-lg-12" id="month_name" name="month_name" style="width: 100%;" >
                @foreach ($month as $keys)
                <option value={{$keys->id}}>{{$keys->month_name}}</option>
                @endforeach
              </select>
              
            </div>
          </div>
          <div class="col-lg-12 col-md-12 col-xs-12">
            <div class="col-lg-6 col-md-6">
          <!--     <button type="button" class="btn   btn-flat pull-left "  data-toggle="modal" data-target="#salary_process_delete">Delete Process</button> -->
            </div>
            <div class="col-lg-6 col-md-6">
              <input type="submit" class="btn btn-success btn-flat pull-right" value="Submit" >
            </div>
            
          </div>
        </div>
      </div>
      
    </div>
      </form>
  </div>

@endsection
@section('script')
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script>
$(document).ready(function($) {

    for (i = new Date().getFullYear(); i > 2017; i--){
      $('.year').append($('<option />').val(i).html(i));
    }


   // $('#declation_date').datepicker({
   //    autoclose: true
   //  });

   //  $('#date_from').datepicker({
   //    autoclose: true
   //  });

   //  $('#date_to').datepicker({
   //    autoclose: true
   //  });


    $('.location').select2({
      placeholder: 'Enter a location',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/location_list_data',
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


    // $masterdata = $('#salary_master').select2({
    //       placeholder: 'Declaration Date',
    //       allowClear: true,
    //       ajax: {
    //         dataType: 'json',
    //         url: '{{URL::to('/')}}/salary_master_listdata',
    //         delay: 250,
    //         data: function(params) {
    //           return {
    //             term: params.term,
    //             hrm_month_id: $('#modalmonth_name').val(),
    //             year_id: $('#modalyear').val(),
    //             hrm_location_id:$('#modallocation').val(),
    //           }
    //         },
    //         processResults: function (data, params) {
    //           params.page = params.page || 1;
    //           return {
    //             results: data
    //           };
    //         },
    //         cache: true
    //       }
    //     });


    $( "#frm_process" ).submit(function(event){
        event.preventDefault();

        if(confirm('Do you want to submit?')){
          var $form   = $( this ),
          url         = $form.attr( "action" ); 
          token       = $("[name='_token']").val();
          $.ajax({
            type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
            url         : url, // the url where we want to POST
            data        : $form.serialize(),
            dataType    : 'json', // what type of data do we expect back from the server
            encode      : true,
            _token      : token
          })
          .done(function(data) {  
              console.log(data);
              if(data['success']) {
                var erreurs ='<div class="alert alert-success"><ul>';
                    erreurs += '<li>'+data.messages+'</li>';
                    erreurs += '</ul></div>';
                $('#alert-success').html(erreurs);   
                $('#alert-success').show(0).delay(4000).hide(0); 
              }else{
                  var erreurs ='<div class="alert alert-danger"><ul>';
                  $.each(data.errors, function(i,error){ 
                      erreurs += '<li>'+error+'</li>';
                  });
                  erreurs += '</ul></div>';
                  $('#alert-danger').html(erreurs);   
                  $('#alert-danger').show(0).delay(4000).hide(0);  
              }
          });
        }else{
          return;
        }      
    });



       $( "#frm_process_delete" ).submit(function(event){
        event.preventDefault();

        // if(confirm('Do you want to submit?')){
          var $form   = $( this ),
          url         = $form.attr( "action" ); 
          token       = $("[name='_token']").val();
          $.ajax({
            type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
            url         : url, // the url where we want to POST
            data        : $form.serialize(),
            dataType    : 'json', // what type of data do we expect back from the server
            encode      : true,
            _token      : token
          })
          .done(function(data) {  
              console.log(data);
              if(data['success']) {
                var erreurs ='<div class="alert alert-success"><ul>';
                    erreurs += '<li>'+data.messages+'</li>';
                    erreurs += '</ul></div>';
                // $('#salary_process_delete').hide(); 
                $('#alert-success1').html(erreurs);   
                $('#alert-success1').show(0).delay(4000).hide(0); 

              }else{
                  var erreurs ='<div class="alert alert-danger"><ul>';
                  $.each(data.errors, function(i,error){ 
                      erreurs += '<li>'+error+'</li>';
                  });
                  erreurs += '</ul></div>';
                // $('#salary_process_delete').hide(); 
                  $('#alert-danger1').html(erreurs);   
                  $('#alert-danger1').show(0).delay(4000).hide(0); 

              }
          });
        // }else{
        //   return;
        // }      
    });

});
</script>

 
@endsection
<!-- Modal for Delete Salary Process  -->
<!-- <div class="modal fade" id="salary_process_delete"  role="dialog" aria-hidden="true">
<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
      <h3 class="modal-title" id="groupAddLabel">Delete Salary Process</h3>
    </div>
    
    {!! Form::open(['method'=>'POST',  'id'=>'frm_process_delete' ]) !!}
    <div class="modal-body">
      
      <div class="row">
        <div class="form-group col-lg-12 col-md-12 col-xs-12">
          <div id="alert-danger1"></div>
        </div>
        <div class="form-group col-lg-12 col-md-12 col-xs-12">
          <div id="alert-success1"></div>
        </div>

        <div class="form-group has-feedback {{ $errors->has('location') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
          <label class="col-lg-3 control-label">Location</label>
          <div class="col-lg-9">
            
            <select  class="form-control col-lg-12 location" id="modallocation" name="location" style="width: 100%;" >
                @foreach ($default_user_location as $keys)
                 <option value={{$keys->id}} selected>{{$keys->location_name}}</option>
                @endforeach
            </select>
            
            
          </div>
        </div>

        <div class="form-group has-feedback {{ $errors->has('year') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
          <label class="col-lg-3 control-label">Year</label>
          <div class="col-lg-9">
            <select  class="form-control col-lg-12 year" id="modalyear" name="year" style="width: 100%;" >
            </select>
          </div>
        </div>
        
        <div class="form-group has-feedback {{ $errors->has('month_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
          <label class="col-lg-3 control-label">Month Name</label>
          <div class="col-lg-9">
            
            <select  class="form-control col-lg-12" id="modalmonth_name" name="month_name" style="width: 100%;" >
              @foreach ($month as $keys)
              <option value={{$keys->id}}>{{$keys->month_name}}</option>
              @endforeach
            </select>
            
          </div>
        </div>

        <div class="form-group has-feedback {{ $errors->has('month_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
             <label class="col-lg-3 control-label">Declaration Date</label>
        
              <div class="col-lg-9">
                <select  class="form-control col-lg-12" id="salary_master" name="salary_master" style="width: 100%;"  required>
                </select> 
              </div>  
        </div>


      </div>
    </div>
    <div class="modal-footer">
      <button type="submit" id="button_sunmit" class="btn btn-danger btn-flat">Delete</button>
    </div>
    {!! Form::close() !!}
  </div>
</div>
</div> -->
<!-- End for Delete Salary Process 