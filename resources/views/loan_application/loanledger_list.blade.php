<!-- employee_list -->
@extends('layouts.main')
<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}"></script>
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@endsection
<!-- content -->
@section('content')
<div class="box box-default">
  <div class="box-header with-border">
    <h3 class="box-title">Loan Ledger List (Employee Wise)</h3>



  <div class="box-header with-border">

      <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
           <select   id="location" name="location"  class="col-xs-2"   required>
                      @foreach ($default_user_location as $keys)
                          <option value={{$keys->id}} selected>{{$keys->location_name}}</option>
                      @endforeach
            </select>
      </div>

          <a href="{{ URL::to('paidloanledger')}}"><input type="button" value="Full Paid Loan Ledger" class="btn-success btn btn-sm button pull-right btn-flat" style="font-size: 12px; font-weight: bold;background-color: red;  "></a>


    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>



  </div>

  </div>

  <div class="box-body">
    <div class="row">

      <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">
        <table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
          <thead>
            <tr style="font-size: 14px;">
              <th ></th>
              <th style="width: 20%">Employee Name</th>
              <th style="width: 15%">Designation</th>
              <th style="width: 10%">Loan Amount</th>
              <th style="width: 10%">Paid</th>
              <th style="width: 10%">Remaining</th>
              <th style="width: 15%">Loan Type</th>
              <th style="width: 8%">Action</th>
              <th style="width: 8%">Details</th>
            </tr>
          </thead>
          <tbody style="font-size: 14px;">
          </tbody>
        </table>
      </div>
    </div>
  </div>

<!-- Start Modal -->
<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" style="border-bottom: 0px;height: 50px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
          <h3 class="modal-title">Loan Payment Form</h3>
        </div>

        <div class="modal-body">
          {!! Form::open(array('url' => 'loanpayment', 'id' => 'add-account-group-form')) !!}

          <div class="row">

              <div class="col-md-12">

                <div class="form-group">
                  <div class="col-md-12">
                    <label class="control-label">Employee Name</label>
                  <input class="form-control" type="text" id="employee_name" name="employee_name" readonly>
                  </div>
                </div>
              </div>

              <div class="col-md-12">
                    <div class="col-md-6">
                      <label class="control-label">Loan Amount</label>
                          <input class="form-control" type="text" id="loan_amount" name="loan_amount" readonly>
                    </div>
                    <div class="col-md-6">
                      <label class="control-label">Paid Amount</label>
                      <input class="form-control" type="text" id="payment" name="payment"  readonly>
                    </div>
              </div>

              <div class="col-md-12">
                    <div class="col-md-6">
                      <label class="control-label">Remaining Loan Amount</label>
                      <input class="form-control" type="text" id="remaining_amount" name="remaining_amount"  readonly>
                    </div>
              </div>
            <input type="text" id="loan_application_id" name="loan_application_id"  hidden>

              <div class="col-md-12">

                 <div class="form-group">
                   <div class="col-md-12">
                            <label class="control-label">Payment Amount</label>
                            <input class="form-control" type="text" id="payment_amount" name="payment_amount" onkeypress="return isNumberKey(event)"  placeholder="Payment Amount" required>
                   </div>
                 </div>

                 <div class="form-group">
                   <div class="col-md-12">
                            <label class="control-label">Comment</label>
                            <input class="form-control" type="text" id="comment" name="comment" placeholder="Write Comment" required>
                   </div>
                 </div>


                <div class="form-group">
                  <div class="col-md-12">
                    <label class="control-label">Payment Date</label>
                    <input class="form-control" type="text" id="payment_date" name="payment_date" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" placeholder="dd-mm-yyyy">
                  </div>


                </div>


               </div>
          </div>

          <div class="modal-footer">
            <div class="col-lg-12 entry_panel_body ">
              <h3></h3>
              <button type="submit" class="btn btn-success btn-flat" id="add-account-group">Payment</button>
              <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Close</button>
            </div>
          </div>
          {!! Form::close() !!}
        </div>

      </div>
    </div>
  </div>

<!-- End Modal -->

</div>
@endsection


@section('script')

<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>


<script>

$(document).ready(function($) {

  $('#payment_date').datepicker({
      autoclose: true
  });

 dataLoad = function(){
  $.ajax({
          type:   'POST',
          url :   "{{URL::to('/')}}/loanledgersummary_list",
          headers:{
                   'X-CSRF-TOKEN': '{{ csrf_token() }}'
                  },
          data:   {
                 location: $("#location").val(),
                },

          dataType: 'json',
          success: function(data) {
            var dataSet = data.data;
            table = $('#list_table').DataTable( {
            destroy:    true,
            paging:     true,
            searching:  true,
            ordering:   true,
            bInfo:      true,
            "data":     dataSet,
          "columns": [
            { "data": "Link",
            "mRender": function (data, type, full) {
            return '<img src="{{asset('employee_image')}}/'+full.Images+'" style="height:30px; width:30px; border-radius: 30px;"/>';
            }
          },
            { "data": "Link",
            "mRender": function (data, type, full) {
            return '<a target="_blank" href="{{URL::to('/')}}/employeeinfo/'+full.hrm_employee_id+'">'+full.employee_name+'</a>';
            }
          },
            { "data": "designation_name" },
            { "data": "loan_amount" },
            { "data": "payment" },
            { "data": "remaining_amount" },
            { "data": "loan_type" },
            { "data": "Link",
            "mRender": function (data, type, full) {
            return '<a  data-id="'+full.id+'" data-employee_name="'+full.employee_name+'" data-loan_amount ="'+full.loan_amount+'"  data-payment="'+full.payment+'" data-remaining_amount="'+full.remaining_amount+'" data-paid_amount="'+full.paid_amount+'" data-loan_application_id="'+full.loan_application_id+'"    class="btn btn-primary btn-single btn-sm showme">Payment</a>';
            }
          },
          { "data": "Link",
            "mRender": function (data, type, full) {
                return '<a href="{{URL::to('/')}}/loanledgerdetails/'+full.loan_application_id+'"  class="btn btn-success btn-sm btn-single" target="_blank"><span  ">View Details</a>';
            }
          },

        ],
             "order": [[0,'asc']]
      });
    }
  });

  $('#list_table').on('click', '.showme', function(e){
      $('#employee_name').val($(this).data('employee_name'));
      $('#apply_date').val($(this).data('apply_date'));
      $('#payment').val($(this).data('payment'));
      $('#remaining_amount').val($(this).data('remaining_amount'));
      $('#loan_amount').val($(this).data('loan_amount'));
      $('#loan_application_id').val($(this).data('loan_application_id'));

      $('#modal_form').modal('show');
  });

}


 dataLoad();

       $role= $('#location').select2({
          placeholder: 'Choose Location',
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

        $role.on('select2:select', function (e) {
            dataLoad();
        });

        $role.on('select2:unselect', function (e) {
            $('#location').val(null).trigger("change");
            dataLoad();
        });


});

function isNumberKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode != 46 &&(charCode < 48 || charCode > 57)))
        return false;
    return true;
}

</script>

@endsection
