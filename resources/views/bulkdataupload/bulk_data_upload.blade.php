<!-- create_employee -->
<!-- create_marital_status -->
@extends('layouts.main')
@section('styles')
@endsection
@section('content')
<div class="box box-primary">
  
  <div class="box-header with-border">
    <h3 class="box-title">Bulk Data Upload</h3>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>
  <div class="box-body">
    <div class="row">
      
      <div class="col-lg-6 col-md-6 col-xs-12">
        
        <div  style="padding-left: 0px; padding-top: 10px;">
          <a href="{{ URL::to('employeebulkdataupload')}}"><input type="button" value="Employee Bulk Data Upload" class="btn-primary btn btn-sm button pull-left btn-flat" style="font-size: 20px; font-weight: bold;"></a>
        </div>

<!--         <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
          <a href="{{ URL::to('fingercarddataupload')}}"><input type="button" value="Finger/Card Data Upload" class="btn-success btn btn-sm button pull-left btn-flat" style="font-size: 20px; font-weight: bold;"></a>
        </div>
 -->
<!--         <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
          <a href="{{ URL::to('employeesalaryinsert')}}"><input type="button" value="Employee Salary Amount Insert" class="btn-success btn btn-sm button pull-left btn-flat" style="font-size: 20px; font-weight: bold;"></a>
        </div>
 -->


      </div>
    </div>
  </div>
</div>
@endsection
@section('script')
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script> -->
<script src="{{asset('js/fileinput.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
@endsection