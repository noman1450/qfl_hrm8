<!-- attandance_list -->
@extends('layouts.main')
<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<style type="text/css">
  .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
      padding: 5px;
  }

  table.dataTable thead > tr > th {
      padding-right: 25px;
  }
  .table>tbody{
    font-size: small;
  }
  .table>thead{
    font-size: smaller;
  }

  td.details-control {
      background: url('{{URL::to('/')}}/dist/img/details_open.png') no-repeat center center;
      cursor: pointer;
  }
  tr.shown td.details-control {
      background: url('{{URL::to('/')}}/dist/img/details_close.png') no-repeat center center;
  }
</style>
@endsection


@section('content')
<div class="box box-default">
  <div class="box-header with-border">
    <h3 class="box-title">Salary Increment & Promotion List</h3>


      <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
        @can('EmployeeIncrementPromotionManage')
            <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
              <div class="input-group date">
                  <a href="{{ URL::to('salaryincrement/create')}}"><input type="button" value="Create New" class="btn-success btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold;"></a>
              </div>
          </div>

        @endcan
          
          <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
            <select class="form-control" id="location" name="location" style="width: 100%;" required>

               

            </select>
          </div>

          <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
              <div class="input-group date">
                  <a href="{{ URL::to('salaryincrementpreviousdata')}}"><input type="button" value="Previous data" class=" btn btn-sm button pull-right btn-flat" style="font-size: 12px; font-weight: bold;"></a>
              </div>
          </div>



        </div>

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>

  <div class="box-body">
    <div class="row">
      <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">
        <table id="designation_list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
          <thead>
            <tr>
            
              <th style="width: 20%">Note</th>
              <th style="width: 15%">Effective Month</th>
              <th style="width: 15%">Location</th>
              <th style="width: 15%">Apply For</th>
              <th style="width: 15%">Status</th>
              <th style="width: 25%">Action</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>
<div class="modal fade" id="viewModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Salary Increment Details</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body" id="viewModalBody">
                <div class="text-center">
                    Loading...
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>

<script>
    function format(d) {
      return '<table class="table table-bordered table-hover table-striped" cellspacing="0" width="100%">'+
            '<tr>'+
              '<td>'+"Employee Name"+'</td>'+
              '<td>'+"Joining Date"+'</td>'+
              '<td>'+"Designation Name"+'</td>'+
              '<td>'+"Salary Amount"+'</td>'+
              '<td>'+"Increase Amount"+'</td>'+
              '<td>'+"New Salary"+'</td>'+
            '</tr>'+
            '<tr>'+
              '<td>'+(d.employee_name || '')+'</td>'+
              '<td>'+(d.joining_date || '')+'</td>'+
              '<td>'+(d.designation_name || '')+'</td>'+
              '<td>'+(d.salary_amount || '')+'</td>'+
              '<td>'+(d.increase_amount || '')+'</td>'+
              '<td>'+(d.new_salary_amount || '')+'</td>'+
            '</tr>'+
          '</table>';
    }

    $(document).ready(function($) {

        var permission = @json(auth()->user()->can('EmployeeIncrementPromotionManage'));
        var table;

        $('#process_date').datepicker({
            autoclose: true
        });

        $role = $('#location').select2({
            placeholder: 'Select a location',
            allowClear: true,
            ajax: {
                dataType: 'json',
                url: '{{URL::to('/')}}/location_list_data',
                delay: 250,
                data: function(params) {
                    return { term: params.term };
                },
                processResults: function (data) {
                    return { results: data };
                },
                cache: true
            }
        });

        $role.on('select2:select select2:unselect', function (e) {
            if (e.type === 'select2:unselect') {
                $('#location').val(null).trigger("change");
            }
            dataLoad();
        });

        $(".onchange").change(function(){
            dataLoad();
        });

        dataLoad = function(){
            var location_id = $("#location").val() || 0;

            $.ajax({
                type: 'POST',
                url: "{{URL::to('/')}}/role_salaryincrement_listdata",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    punch_date: $("#process_date").val(),
                    location: location_id,
                    status: 1
                },
                dataType: 'json',
                success: function(data) {
                    var dataSet = data.data;

                    table = $('#designation_list_table').DataTable({
                        destroy: true,
                        paging: true,
                        searching: true,
                        ordering: true,
                        bInfo: false,
                        data: dataSet,
                        columns: [
                            { "data": "note" },
                            { "data": "effective_month" },
                            { "data": "location_name" },
                            { "data": "apply_for" },
                            { "data": "status" },
                            { 
                                "data": null,
                                "className": "text-center", // Ei line ti add kora hoyeche
                                "mRender": function (data, type, full) {
                                    var actions = '';
                                    if(permission) {
                                        actions += '<a href="{{URL::to('/')}}/salaryincrement/'+full.id+'/edit" class="btn btn-warning btn-sm btn-flat" style="margin-right: 3px;"><span class="glyphicon glyphicon-edit"></span> Edit</a>';
                                        
                                        // actions += '<a href="{{URL::to('/')}}/incrementApproved/'+full.id+'/'+full.hrm_location_id+'" onclick="return confirm(\'Do you want to Approve?\');" class="btn btn-info btn-sm btn-flat" style="margin-right:3px;">Approve</a>';

                                        actions += '<a href="{{URL::to('/')}}/salaryincrement/'+full.id+'/cancel" onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-sm btn-flat" style="margin-right:3px;"><span class="glyphicon glyphicon-trash"></span> Delete</a>';
                                    }
                                    actions += '<button class="btn btn-success btn-sm btn-flat btn-view" data-url="{{ url("salaryincrement") }}/' + full.id + '">View</button>';
                                    return actions;
                                }
                            }
                        ],
                        order: [[ 1, 'asc' ]]
                    });
                }
            });
        };

        dataLoad();

        $('#designation_list_table tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = table.row( tr );
            if ( row.child.isShown() ) {
                row.child.hide();
                tr.removeClass('shown');
            } else {
                row.child( format(row.data()) ).show();
                tr.addClass('shown');
            }
        });

        $(document).on('click', '.btn-view', function () {
            var url = $(this).data('url');
            $('#viewModal').modal('show');
            $('#viewModalBody').html('<div class="text-center">Loading...</div>');

            $.get(url, function (response) {
                $('#viewModalBody').html(response);
            });
        });
    });
</script>
@endsection

