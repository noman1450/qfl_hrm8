@extends('layouts.main')
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<style type="text/css">
/*body {padding:3%;background:#fff}*/
img{
width: 210px;
height: 210px;
}
input[type=file]{
padding:10px;
/*background:#2d2d2d;*/
}
.bold-off {
font-weight: normal !important;
}
.panel-heading {
padding: 0
}
.panel-heading a {
display: block;
padding: 20px 10px;
}
.panel-heading a.collapsed {
background: #fff
}
.panel-heading a {
background: #f7f7f7;
border-radius: 5px;
}
.panel-heading a:after {
content: '-'
}
.panel-heading a.collapsed:after {
content: '+'
}
.nav.nav-tabs li a,
.nav.nav-tabs li.active > a:hover,
.nav.nav-tabs li.active > a:active,
.nav.nav-tabs li.active > a:focus {
border-bottom-width: 0px;
outline: none;
}
.nav.nav-tabs li a {
padding-top: 20px;
padding-bottom: 20px;
}
.tab-pane {
background: #fff;
padding: 10px;
border: 1px solid #ddd;
margin-top: -1px;
}
/* used for sidebar tab/collapse*/
@media (max-width: 991px) {
.visible-tabs {
display: none;
}
}
@media (min-width: 992px) {
.visible-tabs {
display: block !important;
}
}
@media (min-width: 992px) {
.hidden-tabs {
display: none !important;
}
}
</style>
@endsection
@section('content')
<!-- <div class="box box-primary"> -->
<!--  <div class="box-header with-border">
  <div class="box-tools pull-right">
    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
  </div>
</div> -->

<input type="button" onclick="printDiv('printableArea')" value="Print" />
<div id="printableArea">
  
  <div class="row">
    <div class="tab-content">
      <div class="box-body " >
        <div class="row">
          <div class="col-lg-12 col-md-12 col-xs-12 ">
            <div style="text-align: center;">
              <img src="{{asset('dist/img/com-logo.jpg')}}" class="img-circle" alt="User Image" style="height: 60px;width: 100px; display: block;margin-left: auto; margin-right: auto;">
              <h4 style="margin-left: 20px; color: #007F3D!important">{{Config::get('configaration.company_name')}}</h4>
              <h6 style="margin-left: 20px; color: black!important">{{Config::get('configaration.company_address')}}</h5>
              <h4 style="margin-left: 20px; color: black!important"><b>Salary Slip</b></h4>
            </div>
            <h6 style="margin-left: 20px;"><label >Employee Information</label></h6>
            
            <div class="col-lg-6 col-md-6 col-xs-6">
              <table class=" table-bordered ">
                <tr>
                  <td  class="col-lg-3 col-md-3 col-xs-5  ">Employee Name</td>
                  <td class="col-lg-9 col-md-9 col-xs-7 ">{{$master_data[0]->employee_name}}</td>
                </tr>
                <tr>
                  <td  class="col-lg-3 col-md-3 col-xs-5 ">Department</td>
                  <td class="col-lg-9 col-md-9 col-xs-7 ">{{$master_data[0]->depertment_name}}</td>
                </tr>
                <tr >
                  <td class="col-lg-3 col-md-3 col-xs-5 control-label">Designation</td>
                  <td class="col-lg-9 col-md-9 col-xs-7 control-label">{{$master_data[0]->designation_name}}</td>
                </tr>
                <tr >
                  <td class="col-lg-3 col-md-3 col-xs-5 control-label">Location</td>
                  <td class="col-lg-9 col-md-9 col-xs-7 control-label">{{$master_data[0]->location_name}}</td>
                </tr>
                <tr >
                  <td class="col-lg-3 col-md-3 col-xs-5 control-label">Joining Date</td>
                  <td class="col-lg-9 col-md-9 col-xs-7 control-label">{{$master_data[0]->joining_date}}</td>
                </tr>
                
              </table>
            </div>
            <div class="col-lg-6 col-md-6 col-xs-6">
              <table class=" table-bordered ">
                <tr>
                  <td  class="col-lg-3 col-md-3 col-xs-5  ">Year</td>
                  <td class="col-lg-9 col-md-9 col-xs-7 ">{{$master_data[0]->year_id}}</td>
                </tr>
                <tr>
                  <td  class="col-lg-3 col-md-3 col-xs-5 ">Month Name</td>
                  <td class="col-lg-9 col-md-9 col-xs-7 ">{{$master_data[0]->month_name}}</td>
                </tr>
                <tr >
                  <td class="col-lg-3 col-md-3 col-xs-5 control-label">Total Days</td>
                  <td class="col-lg-9 col-md-9 col-xs-7 control-label">{{$master_data[0]->day_of_month}} days</td>
                </tr>
                <tr >
                  <td class="col-lg-3 col-md-3 col-xs-5 control-label">Payable Days</td>
                  <td class="col-lg-9 col-md-9 col-xs-7 control-label">{{$master_data[0]->total_present}} days</td>
                </tr>
                
                
              </table>
            </div>
            <!--               <div class="col-lg-4 col-md-4 col-xs-4 text-center">
              <img  id="blah" src="{{asset('employee_image/'.$master_data[0]->Images)}}" alt="your image" />
            </div> -->
            
          </div>
          <?php
          $addition_amount=0;
          $deduction_amount=0;
          ?>
          <div class="col-lg-6 col-md-6 col-xs-6 ">
            
            <h6 style="margin-left: 20px;"><label >Addition Amount</label></h6>
            <div class="col-lg-12 col-md-12 col-xs-12 ">
              <table class=" table-bordered ">
                <tr>
                  <td class="col-lg-6 col-md-6 col-xs-6  ">Salary head</td>
                  <td class="col-lg-6 col-md-6 col-xs-6  ">Amount</td>
                </tr>
                @foreach($addition as $add)
                <tr>
                  <td  class="col-lg-6 col-md-6 col-xs-6  ">{{$add->salary_head}}</td>
                  <td class="col-lg-6 col-md-6 col-xs-6 ">{{$add->actual_amount}}</td>
                </tr>
                @php $addition_amount=$addition_amount+$add->actual_amount @endphp
                @endforeach
                <tr>
                  <td  class="col-lg-6 col-md-6 col-xs-6  ">Total Addition</td>
                  <td class="col-lg-6 col-md-6 col-xs-6 ">
                    <?php echo number_format("$addition_amount",0) ?>
                  </td>
                </tr>
              </table>
            </div>
            
          </div>
          <div class="col-lg-6 col-md-6 col-xs-6 ">
            
            <h6 style="margin-left: 20px;"><label >Deduction Amount</label></h6>
            <div class="col-lg-12 col-md-12 col-xs-12 ">
              <table class=" table-bordered ">
                <tr>
                  <td class="col-lg-6 col-md-6 col-xs-6  ">Salary head</td>
                  <td class="col-lg-6 col-md-6 col-xs-6  ">Amount</td>
                </tr>
                @foreach($deduction as $add)
                <tr>
                  <td  class="col-lg-6 col-md-6 col-xs-6  ">{{$add->salary_head}}</td>
                  <td class="col-lg-6 col-md-6 col-xs-6 ">{{$add->actual_amount}}</td>
                </tr>
                @php $addition_amount=$addition_amount+$add->actual_amount @endphp
                @endforeach
                <tr>
                  <td  class="col-lg-6 col-md-6 col-xs-6  ">Total Deduction</td>
                  <td class="col-lg-6 col-md-6 col-xs-6 ">
                    <?php echo number_format("$addition_amount",0) ?>
                  </td>
                </tr>
              </table>
            </div>
            
          </div>
          <div class="col-lg-12 col-md-12 col-xs-12 ">
            <div>
              
              Net Amount : {{$master_data[0]->net_salary}}
            </div>
          </div>
          <div class="col-lg-12 col-md-12 col-xs-12 " style="margin-top: 70px;">
            <div>
              
              --------------
              <br>
              Admin
            </div>
          </div>
        </div>
      </div>
    </div>
    <script type="text/javascript">
    function printDiv(divName) {
    var printContents = document.getElementById(divName).innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    }
    </script>
    @endsection