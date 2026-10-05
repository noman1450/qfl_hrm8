@extends('layouts.main')
<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<!-- <link rel="stylesheet" href="{{asset('plugins/datatables/jquery.dataTables.min.css')}}"> -->
<!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css"> -->


@endsection
<!-- content -->
@section('content')
<div class="box box-default">
  <div class="box-header with-border">
    <h3 class="box-title">Edit CW Employee Information</h3>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>
  
  <div class="box-body">

    {!! Form::open(array('route' => array('cwemployeeinfo.update', $master_data[0]->id), 'onkeypress'=> "return event.keyCode != 13;", 'files'=>true, 'id' => 'top-entrypanel-validation', 'method'=>'PUT')) !!}  
    {{ csrf_field() }}


      <div class="row">
        

            <input type="hidden" name="employee_name" value="{{$master_data[0]->employee_id}}" >
            <div class="form-group has-feedback {{ $errors->has('employee_name') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">  
              <div class="col-lg-12 col-md-12 col-xs-12">  
                <label>Employee Name</label>
                <input type="text" class="form-control"  value="{{$master_data[0]->employee_name}}"  disabled>
              </div>
            </div>


            <div class="form-group has-feedback {{ $errors->has('depertment_name') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">  
              <div class="col-lg-12 col-md-12 col-xs-12">  
              <label>Department</label>
                <input  class="form-control" type="text" name="depertment_name"  value="{{$master_data[0]->depertment_name}}" required disabled>
              </div>
            </div>



            <div class="form-group has-feedback {{ $errors->has('designation_name') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">  
              <div class="col-lg-12 col-md-12 col-xs-12">  
                <label>Designation</label>
                <input  class="form-control" type="text" name="designation_name"  value="{{$master_data[0]->designation_name}}" required disabled>
              </div>
            </div>

<!--             <div class="form-group has-feedback {{ $errors->has('salary_amount') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">  
              <div class="col-lg-12 col-md-12 col-xs-12">  
                <label>Basic/Gross Salary</label>
                <input  class="form-control" onchange="calculation()" type="text" id="salary_amount"  name="salary_amount"  value="{{$master_data[0]->salary_amount}}" required>
                
                @if ($errors->has('salary_amount'))
                  <span class="help-block">
                    <strong>{{ $errors->first('salary_amount') }}</strong>
                  </span>
                @endif 

              </div>
            </div> -->

          <div class="form-group has-feedback {{ $errors->has('accounts_code') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">  
              <div class="col-lg-12 col-md-12 col-xs-12">  
                <label>Acoounts Code</label>
                <input  class="form-control" onchange="cal()" type="text"  name="accounts_code"  value="{{$master_data[0]->accounts_code}}" placeholder="Accounts Code" >
                
                @if ($errors->has('accounts_code'))
                  <span class="help-block">
                    <strong>{{ $errors->first('accounts_code') }}</strong>
                  </span>
                @endif 

              </div>
            </div>


            <div class="form-group has-feedback {{ $errors->has('payment_mode') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">  
              <div class="col-lg-12 col-md-12 col-xs-12">  
                <label>Payment Mode</label>
                <select class="form-control" name="payment_mode" id="payment_mode" >

                      @if ($master_data[0]->payment_mode == 1)
                      <option value="1" selected>Cash</option>
                      <option value="2">Bank</option>
                      @else
                      <option value="1">Cash</option>
                      <option value="2" selected>Bank</option>
                      @endif

                </select>

                @if ($errors->has('payment_mode'))
                  <span class="help-block">
                    <strong>{{ $errors->first('payment_mode') }}</strong>
                  </span>
                @endif 

              </div>
            </div>


            <div class="form-group has-feedback {{ $errors->has('bank_name') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12  bank">  
              <div class="col-lg-12 col-md-12 col-xs-12">  
            

                <label>Bank Name</label>

                
                <select class="form-control" name="bank_name" id="bank_name" >
                    
                    @foreach ($bank_name as $keys)
                      @if ($master_data[0]->hrm_bank_id == $keys->id)
                        <option value={{$keys->id}} selected>{{$keys->bank_name}}</option>
                      @else
                        <option value={{$keys->id}}>{{$keys->bank_name}}</option>
                      @endif
                    @endforeach

                </select>

               <!-- <button type="button" class=" btn btn-sm button pull-left btn-flat" data-toggle="modal" data-target="#modal_create_file_type" style="font-size: 12px; font-weight: bold;">+</button> -->
                      

                @if ($errors->has('bank_name'))
                  <span class="help-block">
                    <strong>{{ $errors->first('bank_name') }}</strong>
                  </span>
                @endif 

              </div>
            </div>

            <div class="form-group has-feedback {{ $errors->has('account_no') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12  bank">  
              <div class="col-lg-12 col-md-12 col-xs-12">  
                <label>Account No</label>
                <input  class="form-control" type="text" onkeyup="this.value=this.value.replace(/[^\d]/,'')"  name="account_no"  value="{{$master_data[0]->account_no}}" >
                
                @if ($errors->has('account_no'))
                  <span class="help-block">
                    <strong>{{ $errors->first('account_no') }}</strong>
                  </span>
                @endif 

              </div>
            </div>




           <div class="form-group has-feedback {{ $errors->has('by_bank_percent') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12  bank">  
              <div class="col-lg-12 col-md-12 col-xs-12">  
                <label>By Bank Payment (%)</label>
              </div>
              <div class="col-lg-12 col-md-12 col-xs-12">  
                <input  class="form-control" type="text"  id="by_bank_percent" name="by_bank_percent"  value="{{$master_data[0]->by_bank_percent}}" >
              </div>

            </div>

            <input type="text" name="id" value="{{$master_data[0]->id}}" hidden>
            




      </div>



<!-- End Master Data -->



      <div class="form-group">
         
         <div class="row">

            <div class="col-md-12">
              <input type="submit" class="btn btn-success block btn-flat  pull-right" style="width: 15%;" value="Submit">
            </div>

        </div>
      </div>


    
{!! Form::close() !!} 

  </div>
</div>

  @endsection

  
  <!-- script -->
  @section('script')
  <script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
  <script src="{{asset('bootstrap/js/bootstrap.js')}}"></script>
  <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
  <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

<script>

var temp_basic_salary;

$(document).ready(function() {
deduction_total = 0;
addition_total = 0;
$('.bank').hide();


 if($('#payment_mode').val() == 2){
      $('.bank').show();
        $("#by_bank_percent").val(100);
    }else{
      $('.bank').hide();
      $("#by_bank_percent").val(0);
  }



  $('#payment_mode').on('change', function(){
    if($('#payment_mode').val() == 2){
      $('.bank').show();
       $("#by_bank_percent").val(100);
    }else{
      $('.bank').hide(); 
      $("#by_bank_percent").val(0);
      
    }
  });





// Start Addition Datatable js

  list_table = $('#list_table').DataTable( {
        "searching": false,
        "paging": false,
        "ordering": false,
        "autoWidth": false,
        "bInfo": false,
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };

        }
    } );

    var sum = 0;
    $(".txt").each(function(){
      if(!isNaN(this.value) && this.value !=0){
        sum +=parseFloat(this.value);
      }
    });

    $("#addition_total").html(sum)
    addition_total = sum;
    net_total();
    // console.log(addition_total);
  $(".txt").each(function(){
    $(this).keyup(function(){
      var columnnumber = ($('#list_table tbody td').length/$('#list_table tbody tr').length);
      var rowCount = $('#list_table tbody tr').length - 1;
      calculateSum(columnnumber,rowCount);
    });
  });




function calculateSum(column,rowCount){

  var sum = 0;
  $(".txt").each(function(){
    if(!isNaN(this.value) && this.value !=0){
      sum +=parseFloat(this.value);
    }
  });

  sumQ = [];
  for (var i = 1; i<(column); i++){
    sumQ[i] = 0;
    $('td:nth-child('+(i+1)+')').find(".txt").each(function(){

      if(!isNaN(this.value) && this.value !=0){
        sumQ[i] +=parseFloat(this.value);
      }       
     $("#addition_total").html(sumQ[i]);
     addition_total = sumQ[i];
     net_total();
     // console.log(addition_total);
    });
  }

}
// End Addition Datatable js




// Start deduction Datatable js
  list_table2 = $('#list_table2').DataTable( {
        "searching": false,
        "paging": false,
        "ordering": false,
        "autoWidth": false,
        "bInfo": false,
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };

        }
    } );

    var sum = 0;
    $(".de_txt").each(function(){
      if(!isNaN(this.value) && this.value !=0){
        sum +=parseFloat(this.value);
      }
    });
    $("#deduction_total").html(sum)
    deduction_total = sum;
    net_total();

  $(".de_txt").each(function(){
    $(this).keyup(function(){
      var columnnumber = ($('#list_table2 tbody td').length/$('#list_table2 tbody tr').length);
      var rowCount = $('#list_table2 tbody tr').length - 1;
      calculateSumDeduction (columnnumber,rowCount);
    });

  });




function calculateSumDeduction(column,rowCount){

  var sum = 0;

  $(".de_txt").each(function(){
    if(!isNaN(this.value) && this.value !=0){
      sum +=parseFloat(this.value);
    }
  });
  sumQ = [];
  for (var i = 1; i<(column); i++){
    sumQ[i] = 0;
    $('td:nth-child('+(i+1)+')').find(".de_txt").each(function(){

      if(!isNaN(this.value) && this.value !=0){
        sumQ[i] +=parseFloat(this.value);
      }       
      // console.log(sumQ[i]);
      $("#deduction_total").html(sumQ[i]);
      // $("#netsalary").val(sumQ[i]);
      deduction_total = sumQ[i];
      net_total();
    });
  }

}


function net_total(){

  $("#netsalary").val(addition_total - deduction_total);


}



  $('#list_table tbody').on( 'keyup', 'tr', function () {  
    var rate        = $(this).find('td:eq(1)').find('input').val();
    var amount      = $("#salary_amount").val();

    if($(this).find('td:eq(2)').find(":selected").val() == 1){
      $(this).find('td:eq(3)').find('input').val(amount*rate/100);
    }else{
      $(this).find('td:eq(3)').find('input').val(rate);
    }

      var columnnumber = ($('#list_table tbody td').length/$('#list_table tbody tr').length);
      var rowCount = $('#list_table tbody tr').length - 1;
      calculateSum(columnnumber,rowCount);


  });

  $('#list_table tbody').on( 'click', 'tr', function () {  
    var rate        = $(this).find('td:eq(1)').find('input').val();
    var amount      = $("#salary_amount").val();
    if($(this).find('td:eq(2)').find(":selected").val() == 1){
      $(this).find('td:eq(3)').find('input').val(amount*rate/100);
    }else{
      $(this).find('td:eq(3)').find('input').val(rate);
    }
     
      var columnnumber = ($('#list_table tbody td').length/$('#list_table tbody tr').length);
      var rowCount = $('#list_table tbody tr').length - 1;
      calculateSum(columnnumber,rowCount);

  });


  $('#salary_amount').on('keyup', function(){

    var rowCount = $('#list_table tbody tr').length;
    for (i = 0; i < rowCount; i++){
      var rate        = list_table.cell(i,1).nodes().to$().find('input').val();
      var amount      = $("#salary_amount").val();
      if(list_table.cell(i,2).nodes().to$().find(':selected').val() == 1){
        list_table.cell(i,3).nodes().to$().find('input').val(amount*rate/100);
      }else{
        list_table.cell(i,3).nodes().to$().find('input').val(rate);
      }
    }

    // temp_basic_salary = ($("#salary_amount").val()*list_table.cell(0,1).nodes().to$().find('input').val()/100);
    temp_basic_salary = ($("#basic_salary").val());

    var provident_fund_amount = $('#provident_fund_amount').val();
    var provident_fund_type = $('#provident_fund_type').val();
    var provident_fund_actualamount = $('#provident_fund_actualamount').val();

    // console.log(provident_fund_type, provident_fund_amount);
    if(provident_fund_type == 1){
      provident_fund_actualamount = temp_basic_salary*provident_fund_amount/100;
    }else{
      provident_fund_actualamount = provident_fund_amount;
    }
    $('#provident_fund_actualamount').val(provident_fund_actualamount);

    var rowCount2 = $('#list_table2 tbody tr').length;
    console.log(rowCount2);


      var columnnumber = ($('#list_table tbody td').length/$('#list_table tbody tr').length);
      var rowCount = $('#list_table tbody tr').length - 1;
      calculateSum(columnnumber,rowCount);



  });






});

</script>




  @endsection