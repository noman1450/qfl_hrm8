<!-- employee_list -->
@extends('layouts.main')
<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
@endsection
<!-- content -->
@section('content')
<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">Resignation Approved List</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-header with-border">


        <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
            <label>Filter</label>
            <select class="form-control" id="filter" name="filter[" style="width: 100%;" required>
                <option value="0"> Resign Date </option>
                <option value="1"> Joining Date </option>
                <option value="2"> Confirm Date </option>
                <option value="3"> Joining & Resign Date </option>
                <option value="4"> Confirm & Resign Date </option>
            </select>
        </div>
        <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
            <label>Date From</label>
            <div class="input-group date">
                <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control pull-right onchange" id="date_from" name="date_from" data-date-format="dd-mm-yyyy" value="{{ request()->has('from_date') ? date('d-m-Y', strtotime(request()->from_date)) : date('d-m-Y') }}" required readonly>
            </div>
            @error('date_from')
            <span style="color:darkred">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
            <label>Date To</label>
            <div class="input-group date">
                <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control pull-right onchange" id="date_to" name="date_to" data-date-format="dd-mm-yyyy" value="{{ request()->has('to_date') ? date('d-m-Y', strtotime(request()->to_date)) : date('d-m-Y') }}" required readonly>
            </div>
            @error('date_to')
            <span style="color:darkred">{{ $message }}</span>
            @enderror
        </div>


        <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
            <label>Location Name</label>
            <select class="form-control" id="location" name="location[]" multiple="multiple" style="width: 100%;" required>
                @if (request()->has('location_id'))
                    <option value="{{ request()->location_id }}" selected>{{ request()->location_name }}</option>
                @endif
                {{-- @foreach ($default_user_location as $keys)
                    <option value="{{ $keys->id }}" >{{$keys->location_name}}</option>
                @endforeach --}}
            </select>
            @error('location')
            <span style="color:darkred">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
            <label>Category</label>
            <select class="form-control" id="category" name="category[]" multiple="multiple" style="width: 100%;" required>
            </select>
        </div>

        <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
            <label>Department</label>
            <select class="form-control" id="hrm_depertment_id" name="hrm_depertment_id"  style="width: 100%;" required>
            </select>
        </div>
        <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
            <label>Designation</label>
            <select class="form-control" id="hrm_designation_id" name="hrm_designation_id"  style="width: 100%;" required>
            </select>
        </div>
        <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
            <label>Resignation Type</label>
            <select class="form-control" id="resignation_list" name="resignation_type_id" required>
            </select>
        </div>

        <div class="col-lg-4 col-md-4 col-xs-12 form-group" style="margin-top: 32px;">
            <input type="button" id="search" value="Search" class="btn btn-warning " >
            <a href="{{ route('resignapproved_export') }}" id="resignapproved_export" class="btn btn-primary " >Export</a>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">
                <table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th style="width: 20%">Employee Name</th>
                            <th style="width: 10%">Department</th>
                            <th style="width: 10%">Designation</th>
                            <th style="width: 10%">Resignation Type</th>
                            <th style="width: 10%">Joining Date</th>
                            <th style="width: 10%">Confirm Date</th>
                            <th style="width: 10%">Apply Date</th>
                            <th style="width: 10%;color: red;">Effictive Date</th>
                            <th style="width: 10%">Comment</th>
                            <th style="width: 10%">Status</th>
                            <th style="width: 10%">Approvrd By </th>
                            <th style="width: 10%">Location Name </th>
                            {{-- @permission('EmployeeResignApprove') --}}
                            <!-- <th style="width: 10%">Rejoin</th> -->
                            {{-- @endpermission --}}
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
<!-- script -->
@section('script')
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('bootstrap/js/bootstrap.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
{{-- <script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script> --}}
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script>

$(document).ready(function($) {
    $('#date_from').datepicker({
      autoclose: true
    });

    $('#date_to').datepicker({
      autoclose: true
    });

    $("#search").click(function(){
      dataLoad();
    });

   dataLoad = function(){
        $.ajax({
          type:   'POST',
          url :   "{{URL::to('/')}}/resignapprovedlist",
          headers:{
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                  },
          data:   {
            location: $("#location").val(),
            category: $("#category").val(),
            date_from :$("#date_from").val(),
            date_to   :$("#date_to").val(),
            hrm_depertment_id   :$("#hrm_depertment_id").val(),
            hrm_designation_id   :$("#hrm_designation_id").val(),
            hrm_resignation_type_id   :$("#resignation_list").val(),
            filter   :$("#filter").val(),

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
                      return '<a target="_blank" href="{{URL::to('/')}}/employeeinfo/'+full.employee_id+'">'+full.employee_name+'</a>';
                  }
                },
                // { "data": "employee_name" },
                { "data": "depertment_name" },
                { "data": "designation_name" },
                { "data": "resignation_type" },
                { "data": "joining_date" },
                { "data": "confirmation_date" },
                { "data": "apply_date" },
                { "data": "effective_date" },
                { "data": "comment" },
                { "data": "status" },
                { "data": "name" },
                { "data": "location_name" },
                // @permission('EmployeeResignApprove')
                // { "data": "Link",
                //   "mRender": function (data, type, full) {
                //       return '<a href="{{URL::to('/')}}/employeeresign/'+full.id+'/rejoin"  onclick="return confirm(\'Do you really want to Rejoin this employee?\');" class="btn btn-warning btn-sm btn-flat"><span class="glyphicon glyphicon-share-alt"></span> Rejoin</a>';
                //   }
                // },
                // { "data": "Link",
                //   "mRender": function (data, type, full) {
                //       return '<a href="{{URL::to('/')}}/employee_rejoin/'+full.id+'"  class="btn btn-warning btn-sm btn-flat"><span class="glyphicon glyphicon-share-alt"></span> Rejoin</a>';
                //   }
                // },

                // @endpermission

              ],
              "order": [[0,'asc']]
              });
          }
        });
  }

  @if (request()->has('location_id'))
    dataLoad();
   @endif

  // dataLoad();

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

  // $role.on('select2:select', function (e) {
  //     dataLoad();
  // });

  // $role.on('select2:unselect', function (e) {
  //     $('#location').val(null).trigger("change");
  //     dataLoad();
  // });



  $category=$('#category').select2({
        placeholder: 'Enter Employee Category',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: '{{URL::to('/')}}/category_list_data',
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

  // $category.on('select2:select', function (e) {
  //     dataLoad();
  // });

  // $category.on('select2:unselect', function (e) {
  //     $('#category').val(null).trigger("change");
  //     dataLoad();
  // });


    $('#hrm_depertment_id').select2({
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

    $('#hrm_designation_id').select2({
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





  $('#resignapproved_export').on('click', function(e) {
        e.preventDefault();

        var query = {
            location: $('#location').val(),
            date_from: $('#date_from').val(),
            date_to: $('#date_to').val(),
            category: $('#category').val(),
            hrm_depertment_id: $('#hrm_depertment_id').val(),
            hrm_designation_id: $('#hrm_designation_id').val(),
            filter: $('#filter').val()
        };

        let url = "{{ route('resignapproved_export') }}?" + $.param(query)

        window.location = url;
    });


    $('#resignation_list').select2({
        placeholder: 'Resignation Type',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: '{{URL::to('/')}}/resignation_list_data',
            delay: 250,
            data: function (params) {
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


});

</script>
@endsection
