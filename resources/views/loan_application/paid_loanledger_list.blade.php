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
    <h3 class="box-title">Full Paid Loan Ledger List</h3>
       

  <div class="box-header with-border">

      <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
           <select   id="location" name="location"  class="col-xs-2"   required>
                      @foreach ($default_user_location as $keys)
                          <option value={{$keys->id}} selected>{{$keys->location_name}}</option>
                      @endforeach
            </select>   
      </div>


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
              <th style="width: 25%">Employee Name</th>
              <th style="width: 15%">Designation</th>
              <th style="width: 10%">Loan Amount</th>
              <th style="width: 10%">Paid</th>
              <th style="width: 10%">Remaining</th>
              <th style="width: 15%">Loan Type</th>
              <th style="width: 10%">Details</th>
            </tr>
          </thead>
          <tbody style="font-size: 14px;">
          </tbody>
        </table>
      </div>
    </div>
  </div>


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




 dataLoad = function(){
  $.ajax({
          type:   'POST',
          url :   "{{URL::to('/')}}/paidloanledgersummary_list",
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



</script>

@endsection