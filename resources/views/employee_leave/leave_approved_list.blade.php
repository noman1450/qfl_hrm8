@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">

@endsection

@section('content')
<div class="box box-default">
  <div class="box-header with-border">
    <h3 class="box-title">Leave Approved List</h3>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>

    <div class="col-lg-3 col-md-3 col-xs-12 form-group">
        <select class="form-control" id="location" name="location" style="width: 100%;" required>
            @foreach ($default_user_location as $keys)
                <option value={{$keys->id}} >{{$keys->location_name}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-lg-3 col-md-3 col-xs-12 form-group">
        <select class="form-control" id="employee_name" name="employee_name" style="width: 100%;" required>
        </select>
    </div>

    <div class="row">
        <div class="col-md-2">
            <input type="text" class="form-control onchange" id="date_from" name="date_from" data-date-format="dd-mm-yyyy" value="{{ date('01-m-Y') }}" required readonly>
        </div>

        <div class="col-md-2">
            <input type="text" class="form-control onchange" id="date_to" name="date_to" data-date-format="dd-mm-yyyy" value="{{ date('t-m-Y') }}" required readonly>
        </div>
    </div>



  <div class="box-body">
    <div class="row">
          <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">
        <table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th style="width: 5%"></th>
              <th style="width: 20%">Employee Name</th>
              <th style="width: 12%">Leave Type</th>
              <th style="width: 10%">Apply Date</th>
              <th style="width: 20%">Date Range</th>
              <th style="width: 03%">Days</th>
              <th style="width: 03%">Duration</th>
              <th style="width: 5%">Comment</th>
              <th style="width: 10%">Status</th>
              <th style="width: 10%">Approved By</th>
              <th style="width: 05%">Print</th>
              <th style="width: 05%">Action</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

@endsection

@section('script')
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script> 

<script>
$(document).ready(function($) {

    $('#date_from').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true
    });


    $('#date_to').datepicker({
        autoclose: true
    });

  dataLoad = function(){
      $.ajax({
        type: 'POST',
        url: "{{URL::to('/')}}/leaveapproved_list",
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        data:   {
          location: $("#location").val(),
          date_from: $("#date_from").val(),
          date_to: $("#date_to").val(),
          employeeid: $("#employee_name").val(),
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
                    return '<a target="_blank" href="{{URL::to('/')}}/employeeinfo/'+full.id+'">'+full.employee_name+'</a>';
                }
              },
              { "data": "leave_type" },
              { "data": "applied" },
              { "data": "date_range" },
              { "data": "days" },
              { "data": "duration" },
              { "data": "comment" },
              { "data": "status" },
              { "data": "apply_to" },
              { "data": "Link",
                "mRender": function (data, type, full) {
                    return '<a href="{{URL::to('/')}}/print_leave_form/'+full.leave_app_id+'"  target="_blank" class="btn btn-info btn-sm btn-flat"><span class="glyphicon glyphicon-print"></span> Print</a>';
                }
              },
              { "data": "Link",
                "mRender": function (data, type, full) {
                    return '<a href="{{URL::to('/')}}/employeeleave/'+full.leave_app_id+'/delete"  onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-sm btn-flat"><span class="glyphicon glyphicon-trash"></span> Delete</a>';
                }
              },
            ],
            "order": [[0,'asc']]
            });
        }
      });

}

    $(".onchange").change(function(){
        dataLoad();
    });


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


               $em_role = $('#employee_name').select2({
                  placeholder: 'Enter an Employee Name',
                  allowClear: true,
                    ajax: {
                        dataType: 'json',
                        url: "{{URL::to('/')}}/join_employee_list",
                        delay: 250,
                      data: function(params) {
                          return {
                            term: params.term
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

              $em_role.on('select2:select', function (e) {
                  dataLoad();
              });

              $em_role.on('select2:unselect', function (e) {
                  $('#employee_name').val(null).trigger("change");
                  dataLoad();
              });



});

</script>

@endsection
