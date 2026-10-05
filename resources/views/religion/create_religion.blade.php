<!-- create_religion -->
@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}"></script>
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
@endsection


@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Create Religion</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>


    {!! Form::open(array('route'=>'religion.store', 'onkeypress'=> "return event.keyCode != 13;", 'files'=>true, 'id'=>'frm_religion')) !!}
    
    {{ csrf_field() }}

	<div class="box-body">
		<div class="row">

	        <div class="form-group has-feedback {{ $errors->has('religion') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
	            <div class="col-lg-6">  
	              	<label>Religion Name</label>
	                <input type="text" class="form-control" name="religion" placeholder="Religion Name.." value="{{ old('religion') }}" required autofocus >
		            @if ($errors->has('religion'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('religion') }}</strong>
		                </span>
		            @endif 	                
	            </div>
	        </div>	
		</div>
	</div>

<!--     <div class="col-lg-6 col-md-6 col-xs-12" id="alert-success">  
        <div class="col-lg-12">
        </div>
    </div>
   

    <div class="col-lg-6 col-md-6 col-xs-12" id="alert-danger">  
        <div class="col-lg-12">
        </div>
    </div>
 -->

	<div class="box-footer" style="border-top: 0px solid #f4f4f4;">
		<div class="row">
	        <div class="form-group col-lg-12 col-md-12 col-xs-12">   
	            <div class="col-lg-6">  
	                <button type="submit" class="btn btn-success block btn-flat btn pull-center" >Submit</button>

	            </div>
	        </div>			
        </div>			
	</div>

	{!! Form::close() !!}
</div>
@endsection


@section('script')


<script>

	    $( "#frm_religion" ).submit(function(event){
	        event.preventDefault();

	        if(confirm('Do you want to save?')){
				var $form   = $( this ),
				url         = $form.attr( "action" ); 
				token 		= $("[name='_token']").val();
				$.ajax({
					type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
					url         : url, // the url where we want to POST
					data 		: $form.serialize(),
					dataType    : 'json', // what type of data do we expect back from the server
					encode      : true,
					_token 		: token
				})
				.done(function(data) {  
					console.log(data);
					if(data['success']) {

						window.location.replace("{{ URL::to('religion')}}");
						var erreurs ='<div class="alert alert-success"><ul>';
		                $.each(data.errors, function(i,error){	
		                    erreurs += '<li>'+error+'</li>';
		                });
		                erreurs += '</ul></div>';
		                $('#alert-success').html(erreurs);
					}else{
		                var erreurs ='<div class="alert alert-danger"><ul>';
		                $.each(data.errors, function(i,error){	
		                    erreurs += '<li>'+error+'</li>';
		                });
		                erreurs += '</ul></div>';
		                $('#alert-danger').html(erreurs);
					}
				});
		    }else{
		    	return;
		    }        
	    }); 





</script>

@endsection
