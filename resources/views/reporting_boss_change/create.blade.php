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
@if (session('alert-warning'))
    <div class="alert alert-warning">
        {{ session('alert-warning') }}
    </div>
@endif

@section('content')
<div class="box box-default">
  <div class="box-header with-border">
    <h3 class="box-title">Report Boss Change</h3>

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>

  <form  method="POST" action="{{url('reporting_boss_change')}}" id="submitForm">
    {{ csrf_field() }}
    <div class="row">
      <div class="form-group has-feedback {{ $errors->has('location') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
        <div class="col-lg-12 col-md-12 col-xs-12">
          <label>Location Name</label>
          <select  class="form-control" name="location" id="location">
            @foreach ($user_location as $keys)
            @if($keys->default_location == 1)
            <option value={{$keys->id}} selected> {{$keys->location_name}}</option>
            @endif
            <option value={{$keys->id}}> {{$keys->location_name}}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="col-lg-2">
            <div class="form-group">
                <label for="manage_by">Managed By</label>
                <select class="form-control changed_value" id="manage_by" name="manage_by" style="width: 100%;"  required> </select>
            </div>
       </div>


        <div class="col-lg-2">
               <div class="form-group">
                    <label id="" style="color: green;"> Checked Count : <span id="check_count"></span></label>
               </div>
       </div>



  </div>
  <div class="box-body">
    <div class="row">


      <div class="form-group col-lg-12 col-md-12 col-xs-12">

        <table id="designation_list_table" class=" cell-border table table-bordered table-hover "  cellspacing="0" width="100%">
          <thead>
            <tr>
            <th><input name="select_all" value="1" id="example-select-all" type="checkbox" />_All</th>
            <th>Employee Name</th>
            <th>Unique Code</th>
            <th>Designation</th>
            <th>Department</th>
            <th>Joining</th>
            <th>Contact</th>
            <th>Job Placement</th>
            <th>Reporting Boss</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>

      <div class="col-md-12">

        <div class="col-lg-2">
            <div class="form-group">
                <label for="new_manage_by">New Managed By</label>
                <select class="form-control" id="new_manage_by" name="new_manage_by" style="width: 100%;"  required>
                </select>
            </div>
        </div>
        <div class="col-lg-2" style="margin-top: 25px;">
            {{-- <input type="submit"  value="Submit" class=" btn-sm btn-success block btn-flat btn" style="margin-left: 15px; padding: 7px 10px; color: black; border:1px solid gray;"> --}}

            <button type="submit" id="submitBtn" class="btn btn-primary btn-sm submitBtn cab" name="">
                <span class="ladda-label">Transfer</span>
                <i class="fa-solid fa-bolt"></i>
            </button>
        </div>
      </div>
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


    $('#date_from').datepicker({
         autoclose: true,
         minViewMode: 1,
         format: 'dd-mm-yyyy'
    });

    $("#location").on('change', function() {
        $("#manage_by").val(null).trigger('change');
        $("#new_manage_by").val(null).trigger('change');
    });

    $('#manage_by').select2({
        placeholder:'Enter Manage From Employee',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: "{{URL::to('/')}}/join_employee_list",
            delay: 250,
            data: function(params) {
                return {
                    term: params.term,
                    apply_old_info:1,
                    location: $('#location option:selected').val()
                }
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        }
    });

    $('#new_manage_by').select2({
        placeholder:'Enter an Employee Name',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: "{{URL::to('/')}}/join_employee_list",
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
                    results: data,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        }
    });


    $(document).on('change','#location, #manage_by',function(){

    //   if ($("#location").val() == null){
    //     alert('select Location');
    //     return fasle;
    //   }

    //   if ($("#manage_by").val() == null){
    //     alert('Manage By');
    //     return fasle;
    //   }

      $.ajax({
        type:   'POST',
        url :   "{{URL::to('/')}}/reportingboss_employeelist",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
        data:   {
                   hrm_location_id   : $("#location").val(),
                   hrm_manage_by_id  : $("#manage_by").val()
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
              "scrollX": false,
              "scrollY": 500,
              "data":     dataSet,
              "columns": [
                { "data": "checkbox",
                        "mRender": function (data, type, full) {
                        return '<input type="checkbox" class="check_count" name="id[]" value="'+full.id+'">';
                    }
                },
                { "data": "employee_name" },
                { "data": "Unique_Code" },
                { "data": "location_name" },
                { "data": "designation_name" },
                { "data": "depertment_name" },
                { "data": "joining_date" },
                { "data": "contact_number" },
                { "data": "manage_by_name" },
              ]
            });
        }
      });

    });

   // Handle click on "Select all" control
   $('#example-select-all').on('click', function(){
      // Check/uncheck all checkboxes in the table
      var rows = table.rows({ 'search': 'applied' }).nodes();
      $('input[type="checkbox"]', rows).prop('checked', this.checked);


      var checkCount = $('input[type="checkbox"]:checked', rows).length;
     $("#check_count").html(checkCount);

   });


 // Handle click on checkbox to set state of "Select all" control
   $('#designation_list_table tbody').on('change', 'input[type="checkbox"]', function(){
      // If checkbox is not checked
        var checkCount = $('#designation_list_table tbody input[type="checkbox"]:checked').length;
        // Display the count in the element with ID #check_count
        $("#check_count").html(checkCount);
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


//    $(document).on('change','#manage_by, #new_manage_by',function(){
//         var new_manage_by = $("#new_manage_by").val();
//         var manage_by = $("#manage_by").val();

//         if(manage_by == new_manage_by){
//             $('#submitBtn').prop('disabled', true);
//             alert('From Reporing boss and To Reporting boss can not same.');
//         }else{
//             $('#submitBtn').prop('disabled', false);
//         }
//    });

   $('#submitForm').on('submit', function(e) {
        e.preventDefault();

        var selectedEmployees = $('#designation_list_table tbody input[type="checkbox"]:checked').length;
        if (selectedEmployees === 0) {
            alert('Please select at least one employee.');
            return;  // Stop form submission
        }

        var manage_by =$("#manage_by").val();
        var new_manage_by =$("#new_manage_by").val();

        if (manage_by === new_manage_by) {
            alert('Manage by and new manage by can not same.');
            return;  // Stop form submission
        }

        var formData = {
            location: $('#location').val(),
            manage_by: $('#manage_by').val(),
            new_manage_by: $('#new_manage_by').val(),
            employee_ids: $('input[name="id[]"]:checked').map(function() {
                return this.value;
            }).get(),
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: "{{ route('reporting_boss_change.store') }}",
            type: "POST",
            data: formData,
            success: function(response) {
                console.log(response);
                if(response.success == false){
                    alert(response.error_messages);
                }else{
                    // location.reload();
                    window.location.href = "{{ URL::to('reporting_boss_change') }}";
                }
            },
            error: function(xhr, status, error) {
                // Handle error, e.g., display error messages
                alert('An error occurred: ' + xhr.responseText);
            }
        });
    });





});
</script>
@endsection
