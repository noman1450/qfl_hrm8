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
    <h3 class="box-title">Loan Ledger Details List (Employee Wise)</h3>
      <div class="box-header with-border">

        <h3>{{$data[0]->employee_name}}</h3>
        <h4>{{$data[0]->designation_name}}</h4>
        <h4>{{$data[0]->depertment_name}}</h4>
        <label>Loan Type : </label> <label>{{$data[0]->loan_type}}</label>
        <div>
          <h3>Summary</h3>
          
            <table class="table table-bordered table-hover " style="border: 1px solid #ddd;">
                <thead style="background-color:#4CAF50; color: white;">
                    <th>Loan Amount</th>
                    <th>Paid</th>
                    <th>Remaining Loan</th>
                    <th>Instalment Size</th>
                    <th>No Of Instalment</th>
                    <th>Approved Date</th>
                </thead>
                <tbody style="background-color:#f2f2f2;">
                    <td>{{$data[0]->opening_loan}}</td>
                    <td>{{$data[0]->paid_loan}}</td>
                    <td>{{$data[0]->remaining_loan}}</td>
                    <td>{{$data[0]->installment_size}}</td>
                    <td>{{$data[0]->no_of_installment}}</td>
                    <td>{{$data[0]->approved_date}}</td>
                </tbody>
            </table>


        </div>
        <div>
        <h3>Details</h3>

        <table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
          <thead >
            <tr style="font-size: 14px;">
              <th style="width: 10%">Month & Year</th>
              <th style="width: 10%">Description</th>
              <th style="width: 10%">Loan Amount</th>
              <th style="width: 10%">Paid</th>
              <th style="width: 10%">Action</th>
            </tr>
          </thead>
          <tbody style="font-size: 14px;">

              @foreach ($data as $keys)
                <tr>
                  <td>{{$keys->month_year}}</td>
                  <td>{{$keys->narration}}</td>
                  <td>{{$keys->loan_amount}}</td>
                  <td>{{$keys->payment}}</td>

                  @if($keys->entry_status==2)
                     <td>
                     <a href="{{ URL::to('loanledger_cancel')}}/{{$keys->id}}" onclick="return confirm(\'Do you really want to DELETE?\');"> <input type="button" value="Delete"   class="btn-danger btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold;"></a>
                     </td>
                  @else
                   
                     <td>
                     <input type="button" value="Delete" class="btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold;color: gray;">
                     </td>

                  @endif

                </tr>
              @endforeach

          </tbody>
        </table>
        </div>

      </div>
  </div>
</div>
@endsection

@section('script')
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('bootstrap/js/bootstrap.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('js/fileinput.js')}}"></script>

@endsection