<!-- employee_list -->
@extends('layouts.main')
<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}"></script>
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
@endsection
<!-- content -->
@section('content')
<div class="box box-default">
  <div class="box-header with-border">
    <h3 class="box-title">Loan Approved List</h3>

  <div class="box-header with-border">

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
            <tr style="font-size: 12px;">
              <th></th>
              <th style="width: 20%">Employee Name</th>
              <th style="width: 8%">Start Date</th>
              <th style="width: 8%">Loan Amount</th>
              <th style="width: 8%">Opening Paid</th>
              <th style="width: 8%">Opening Remaining</th>
              <th style="width: 8%">Instalment Size</th>
              <th style="width: 8%">No Of Installment</th>
              <th style="width: 15%">Loan Type</th>
              <th style="width: 8%">Apply date</th>
              <th style="width: 5%">Delete</th>
              <th style="width: 5%">Action</th>
            </tr>
          </thead>
          <tbody style="font-size: 12px;">
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
          <h3 class="modal-title">Re-Schedule Loan</h3>
        </div>

        <div class="modal-body">
          {!! Form::open(array('url' => 'rescheduleloan', 'id' => 'add-account-group-form')) !!}

          <div class="row">

              <div class="col-md-12">

                <div class="form-group">
                  <div class="col-md-12">
                    <label class="control-label">Employee Name</label>
                  <input class="form-control" type="text" id="employee_name" name="employee_name" readonly>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-md-12">
                    <label class="control-label">Apply Date</label>
                    <input class="form-control" type="text" id="apply_date" name="apply_date" readonly>
                  </div>
                </div>

              </div>



              <div class="col-md-12">

                    <div class="col-md-6">
                      <label class="control-label">Opening Loan Amount</label>
                          <input class="form-control" type="text" id="opening_loan_amount" name="opening_loan_amount" readonly>
                    </div>

                    <div class="col-md-6">
                      <label class="control-label">Paid Amount</label>
                      <input class="form-control" type="text" id="paid_amount" name="paid_amount" readonly>
                    </div>


              </div>

              <div class="col-md-12">

                    <div class="col-md-6">
                      <label class="control-label">Remaining Loan Amount</label>
                      <input class="form-control" type="text" id="remaining_amount" name="remaining_amount" readonly>
                    </div>

              </div>



              <div class="col-md-12">

                    <div class="col-md-6">
                      <label class="control-label">Installment Amount</label>
                      <input class="form-control" type="text" id="installment_amount" name="installment_amount" readonly>
                    </div>

                    <div class="col-md-6">
                      <label class="control-label">No Of Installment</label>
                      <input class="form-control" type="text" id="no_of_installment" name="no_of_installment" readonly>
                    </div>

              </div>

              <div class="col-md-12">
                    <div class="col-md-6">
                      <label class="control-label">Effective Date</label>
                      <input class="form-control"  type="text" id="installment_start_date" name="installment_start_date" >
                    </div>
              </div>


                  <input type="text" id="id" name="id"  hidden>

              <div class="col-md-12">

                 <div class="form-group">
                   <div class="col-md-12">
                            <label class="control-label">Comment</label>
                            <input class="form-control" type="text" id="comment" name="comment" placeholder="Write Comment" >
                   </div>
                 </div>

               </div>
          </div>

          <div class="modal-footer">
            <div class="col-lg-12 entry_panel_body ">
              <h3></h3>
              <button type="submit" class="btn btn-success btn-flat" id="add-account-group">Update</button>
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


<script src="{{asset('js/fileinput.js')}}"></script>

<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>



<script>

$(document).ready(function($) {

  $('#installment_start_date').datepicker({
      autoclose: true,
      minViewMode: 1,
      format: 'yyyy-mm-dd'
  });

});

</script>

<script>

$(document).ready(function($) {
  $.ajax({
          type:   'POST',
          url :   "{{URL::to('/')}}/loanapplication_list",
          headers:{
                   'X-CSRF-TOKEN': '{{ csrf_token() }}'
                  },
          data:   {
                  status: 2,
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
            { "data": "installment_start_date" },
            { "data": "opening_loan_amount" },
            { "data": "paid_amount" },
            { "data": "remaining_amount" },
            { "data": "installment_size" },
            { "data": "no_of_installment" },
            { "data": "loan_type" },
            { "data": "apply_date" },
            { "data": "Link",
              "mRender": function (data, type, full) {
                  return '<a href="{{URL::to('/')}}/loanapplication/'+full.id+'/cancel"  onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-sm btn-single"><span >Delete</a>';
              }
            },
            { "data": "Link",
            "mRender": function (data, type, full) {
            return '<a  data-id="'+full.id+'" data-employee_name="'+full.employee_name+'"   data-apply_date="'+full.apply_date+'" data-opening_loan_amount ="'+full.opening_loan_amount+'"  data-no_of_installment="'+full.no_of_installment+'" data-remaining_amount="'+full.remaining_amount+'" data-paid_amount="'+full.paid_amount+'" data-installment_start_date="'+full.installment_start_date+'" data-installment_size="'+full.installment_size+'"   class="btn btn-primary btn-single btn-sm showme">Re-Schedule</a>';
            }
          },

        ],
             "order": [[0,'asc']]
      });
    }
  });

// pora dekbo
  $('#list_table').on('click', '.showme', function(e){
      $('#employee_name').val($(this).data('employee_name'));
      $('#apply_date').val($(this).data('apply_date'));
      $('#opening_loan_amount').val($(this).data('opening_loan_amount'));
      $('#paid_amount').val($(this).data('paid_amount'));
      $('#remaining_amount').val($(this).data('remaining_amount'));
      $('#installment_amount').val($(this).data('installment_size'));
      $('#no_of_installment').val($(this).data('no_of_installment'));
      $('#installment_start_date').val($(this).data('installment_start_date'));
      $('#salary_head').val($(this).data('salary_head'));
      $('#id').val($(this).data('id'));



      $('#modal_form').modal('show');
  });



});


</script>



@endsection
