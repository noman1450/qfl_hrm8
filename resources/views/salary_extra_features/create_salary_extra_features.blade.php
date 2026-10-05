<!-- location_list -->
@extends('layouts.main')
<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

@endsection
<!-- content -->
@section('content')
<div class="box box-default">
  <div class="box-header with-border">
    <h3 class="box-title">Create Salary Advance Adjust</h3>

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>

      <form  id="salaryExtraFeaturesForm" method="POST" action="{{url('salaryextrafeatures')}}">
            {{ csrf_field() }}

        <div class="row">
            <div class="col-md-12">
                <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-2 col-md-2 col-xs-12">
                    <label>Month From</label>
                    <input type="text" class="form-control"  placeholder="Month From" name="date_from" id="date_from" value=""  readonly required>
                  </div>

                  <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-2 col-md-12 col-xs-12">
                    <label>Month To</label>
                    <input type="text" class="form-control" placeholder="Month To" name="date_to" id="date_to" readonly required>
                  </div>

                  <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-2 col-md-12 col-xs-12">
                    <label>Purpose</label>
                    <input type="text" class="form-control"  placeholder="Purpose" name="purpose" id="purpose" value=""  required>
                  </div>

                  <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-2 col-md-12 col-xs-12">
                    <label>Adjust Type</label>
                         <select class="form-control" id="adjust_salay_type" name="adjust_salay_type" style="width: 100%;" required>
                              <option value="">Select</option>
                              <option value="1">Gross Salary</option>
                              <option value="2">Amount</option>
                          </select>
                  </div>

                  <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-2 col-md-12 col-xs-12">
                    <label>Percentage(%)</label>
                    <input type="number" class="form-control checkValid" placeholder="percentage" name="percentage" id="percentage" step="any" required readonly>
                  </div>

                  <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-2 col-md-12 col-xs-12">
                    <label>Amount</label>
                    <input type="number" class="form-control checkValid" placeholder="amount" name="amount" id="amount"  required readonly>
                  </div>

                <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-2 col-md-12 col-xs-12">
                    <label>Adjust With Salary Head</label>
                         <select class="form-control" id="salary_head" name="salary_head" style="width: 100%;" required>
                    </select>
                </div>

                <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-2 col-md-12 col-xs-12">
                    <label>Office</label>
                    <select class="form-control" id="location" name="location" style="width: 100%;" >
                          @foreach ($user_location as $keys)
                              @if($keys->default_location == 1)
                                <option value={{$keys->id}} selected> {{$keys->location_name}}</option>
                              @endif
                                <option value={{$keys->id}}> {{$keys->location_name}}</option>
                          @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-3 col-xs-12 form-group">
                    <label>Department</label>
                    <select class="form-control" id="department" name="department" style="width: 100%;" >
                    </select>
                </div>

                <div class="col-lg-2 col-md-3 col-xs-12 form-group">
                    <label>Designation</label>
                    <select class="form-control" id="designation" name="designation" style="width: 100%;" >
                    </select>
                </div>

                <div class="col-lg-2 col-md-2 col-xs-12 form-group">
                    <label>Employee Name</label>
                    <select class="form-control" id="employee_name" name="employee_name" style="width: 100%;" >
                    </select>
                </div>

                <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 25px;">
                    <input type="button" id="search" value="Search" class=" btn-sm block btn-flat btn btn-primary" style="margin-right: 15px; padding: 7px 10px;">
                </div>
            </div>
        </div>


      <div class="box-body">
        <div class="row">
          <div class="form-group col-lg-12 col-md-12 col-xs-12">
            <table id="designation_list_table" class=" cell-border table table-bordered table-hover "  cellspacing="0" width="100%">
              <thead>
                <tr>
                 <th  style="width: 5%"><input name="select_all" value="1" id="example-select-all" type="checkbox" />All</th>
                  <th style="width: 20%">Employee Name</th>
                  <th style="width: 15%">Deparment</th>
                  <th style="width: 15%">Designation</th>
                  <th style="width: 15%">Location</th>
                  <th style="width: 15%">Purpose</th>
                  <th style="width: 15%">Gross Salary</th>
                  <th style="width: 15%">Amount(Tk.)</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>

         <input type="submit"  value="Submit" class=" btn-sm btn-success block btn-flat btn" style="margin-left: 15px; padding: 7px 10px; color: black; border:1px solid gray;">

        </div>
      </div>

    </form>


</div>

  @endsection


  <!-- script -->
@section('script')
  <script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
  <script src="{{asset('bootstrap/js/bootstrap.js')}}"></script>
  <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
  <script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
  <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
  <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
  <script src="{{asset('dist/js/jquery.inputmask.bundle.js')}}"></script>


<script>

$(document).ready(function($) {
    $(document).on('input','.checkValid',function(){
        if($(this).val()<0){
            alert("Enter Valid Amount");
            $(this).val('');
            return;
        }
    });


    $(document).on('input', '.dateto', function() {
        let inputValue = $(this).val();

        // Regular expression to match a valid number (with optional decimal)
        let validNumberPattern = /^\d*\.?\d*$/;

        // Check if the value matches the pattern and is a valid number
        if (!validNumberPattern.test(inputValue)) {
            alert("Enter a valid number");
            $(this).val(''); // Clear the invalid input
            return;
        }

        // Check if the value is negative
        if (Number(inputValue) < 0) {
            alert("Enter a valid positive number");
            $(this).val(''); // Clear the invalid input
            return;
        }
    });


    $('#salaryExtraFeaturesForm').on('submit', function(e) {
        let isChecked = $('input[name="id[]"]:checked').length > 0;
        if (!isChecked) {
            alert('Please select at least one checkbox.');
            e.preventDefault(); // Prevent form submission
        }
    });

    $(document).on('change','#adjust_salay_type',function(){
        let adjustType = Number($("#adjust_salay_type").val());
        if(adjustType == 1){
            $("#percentage").prop('readonly',false)
            $("#amount").val('')
            $("#amount").prop('readonly',true)
        }else{
            $("#percentage").prop('readonly',true)
            $("#amount").prop('readonly',false)
            $("#percentage").val('')

        }
    });

     $('#date_from').datepicker({
        autoclose: true,
        format: "M-yyyy",
        viewMode: "months",
        minViewMode: "months"
     });

    $('#date_to').datepicker({
        autoclose: true,
        format: "M-yyyy",
        viewMode: "months",
        minViewMode: "months"
    });



 $('#salary_head').select2({
      placeholder: 'Enter a Salary Head',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/salary_head_list',
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


 $('#department').select2({
      placeholder: 'Enter department',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/depertment_list_data',
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

    $('#designation').select2({
      placeholder: 'Enter designation',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/designation_list_data',
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

    $('#employee_name').select2({
      placeholder: 'Enter Employee Name',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/join_employee_list',
        delay: 250,
        data: function(params) {
          return {
            term: params.term,
            location: $('#location option:selected').val()
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



 $("#search").click(function(){


    let  adjust_type  = $("#adjust_salay_type").val();
    let  percentage  = $("#percentage").val();
    if(adjust_type == 2){
        if ($("#amount").val() == ''){
          alert("Please Fillup Amount");
          return;
      }
    }

    if(adjust_type == 1){
        if ($("#percentage").val() == ''){
          alert("Please Fillup Percentage");
          return;
      }
    }


      if ($("#purpose").val() == ''){
          alert("Please Fillup Purpose");
          return;
      }

     if ($("#salary_head").val() == null){
          alert("Please Select Salary Head");
       return;
      }

      if ($("#adjust_salay_type").val() == ''){
          alert("Please Select Adjust Type");
       return;
      }

      if ($("#date_from").val() == ''){
          alert("Please Select Month From");
       return;
      }

      if ($("#date_to").val() == ''){
          alert("Please Select Month To");
       return;
      }

      if ($("#location").val() == null){
        location_id=0;
      }else{
        location_id = $("#location").val();
      }

      if ($("#employee_name").val() == null){
        employee_id=0;
      }else{
        employee_id = $("#employee_name").val();
      }

      if ($("#department").val() == null){
        department_id = 0;
      }else{
        department_id = $("#department").val();
      }

      if ($("#designation").val() == null){
        designation_id = 0;
      }else{
        designation_id = $("#designation").val();
      }

      $.ajax({
        type:   'POST',
        url :   "{{URL::to('/')}}/salary_extra_listdata",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
        data:   {
                   punch_date: $("#process_date").val(),
                   location      : location_id,
                   department_id : department_id,
                   designation_id: designation_id,
                   employee_id   : employee_id,
                   purpose       : $("#purpose").val(),
                   amount        : $("#amount").val(),
                   adjust_type  : adjust_type,
                   percentage  : percentage,

                },
        dataType: 'json',
        success: function(data) {
          var dataSet = data.data;
            table = $('#designation_list_table').DataTable( {
              destroy:    true,
              paging:     false,
              searching:  true,
              ordering:   true,
              bInfo:      true,
              "data":     dataSet,

              "columns": [

              { "data": "checkbox",
                      "mRender": function (data, type, full) {
                      return '<input type="checkbox" name="id[]" value="'+full.id+'">';
              }
              },
              { "data": "employee_name" },
              { "data": "depertment_name" },
              { "data": "designation_name" },
              { "data": "location_name" },
              { "data": "purpose" },
              { "data": "basic_salary" },
              { "data": "text",
                      "mRender": function (data, type, full) {
                      return '<input type="number" class="dateto"   name="amount['+full.id+']"  value="'+full.amount+'" step="any">';
              }
              },
              ],
              order: [ 1, 'asc' ]
            });
        }
      });
    // }

    });

   // Handle click on "Select all" control
   $('#example-select-all').on('click', function(){
      // Check/uncheck all checkboxes in the table
      var rows = table.rows({ 'search': 'applied' }).nodes();
      $('input[type="checkbox"]', rows).prop('checked', this.checked);
   });


 // Handle click on checkbox to set state of "Select all" control
   $('#designation_list_table tbody').on('change', 'input[type="checkbox"]', function(){
      // If checkbox is not checked
      if(!this.checked){
         var el = $('#example-select-all').get(0);
         // If "Select all" control is checked and has 'indeterminate' property
         if(el && el.checked && ('indeterminate' in el)){
            // Set visual state of "Select all" control
            // as 'indeterminate'
            el.indeterminate = true;
         }
      }
   });
});


</script>

@endsection
