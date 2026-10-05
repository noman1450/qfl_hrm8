@extends('layouts.main')
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
@endsection
@section('content')
<div class="box box-default">
  <div class="box-header with-border">
    <h3 class="box-title">Employee File List Summary</h3>

    {{-- @permission('DocumentArchiveCreateEditDelete') --}}
    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
      <a href="{{ URL::to('document_archive/create/')}}"><input type="button" value="Create New" class="btn-success btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold;"></a>
    </div>
    {{-- @endpermission --}}

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>

  <div class="box-body">
    <div class="row">
        <div class="col-lg-2">
            <div class="form-group">
                <label for="location">Location</label>
                <select class="form-control" id="location" style="width: 100%;"  required>
                    @foreach ($default_user_location as $keys)
                        <option value={{$keys->id}} selected>{{$keys->location_name}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-2">
            <div class="form-group">
                <label for="designation_id">Designation</label>
                <select class="form-control" id="designation_id" style="width: 100%;"  required>

                </select>
            </div>
        </div>

        <div class="col-lg-2">
            <div class="form-group">
                <label for="department_id">Department</label>
                <select class="form-control" id="department_id" style="width: 100%;"  required>

                </select>
            </div>
        </div>

        <div class="col-lg-2">
            <div class="form-group">
                <label for="category_id">Category</label>
                <select class="form-control" id="category_id" style="width: 100%;"  required>

                </select>
            </div>
        </div>

        <div class="col-lg-2">
            <div class="form-group">
                <label for="section_id">Sub-department</label>
                <select class="form-control" id="section_id" style="width: 100%;"  required>

                </select>
            </div>
        </div>

        <div class="col-lg-2">
            <div class="form-group">
                <label for="employee_type_id">Employee Type</label>
                <select class="form-control" id="employee_type_id" style="width: 100%;"  required>

                </select>
            </div>
        </div>
    </div>

    <div class="row">
      <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">
        <table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th></th>
              <th></th>
              <th style="width: 15%">Employee Name</th>
              <th style="width: 10%">Unique Code</th>
              <th style="width: 10%">Department</th>
              <th style="width: 10%">Designation</th>
              <th style="width: 8%">Total File</th>
              <th style="width: 40%">Summary</th>
              <th style="width: 8%">View Details</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
          <tfoot>
            <tr>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
            </tr>
          </tfoot>
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
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script>
$(document).ready(function($) {

    dataLoad = function(){
        if ($("#location").val() == null) {
            location_id = 0;
        } else {
            location_id = $("#location").val();
        }

        if ($("#designation_id").val() == null) {
            designation_id = 0;
        } else {
            designation_id = $("#designation_id").val();
        }

        if ($("#department_id").val() == null) {
            department_id = 0;
        } else {
            department_id = $("#department_id").val();
        }

        if ($("#category_id").val() == null) {
            category_id = 0;
        } else {
            category_id = $("#category_id").val();
        }

        if ($("#section_id").val() == null) {
            section_id = 0;
        } else {
            section_id = $("#section_id").val();
        }

        if ($("#employee_type_id").val() == null) {
            employee_type_id = 0;
        } else {
            employee_type_id = $("#employee_type_id").val();
        }

      $.ajax({
        type:   'POST',
        url :   "{{URL::to('/')}}/summary_employeewisefile_list",
        headers:{ 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        data:   {
            location: location_id,
            designation_id: designation_id,
            department_id: department_id,
            category_id: category_id,
            section_id: section_id,
            employee_type_id: employee_type_id,
        },
        dataType: 'json',
        success: function(data) {
            var dataSet = data.data;
            table = $('#list_table').DataTable( {
                "destroy": true,
                "searching": true,
                "paging": true,
                "ordering": true,
                "autoWidth": true,
                "bInfo": true,
                aoColumnDefs: [{ "bVisible": false, "aTargets": [0] }],
                "data":     dataSet,
                "footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;
                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '')*1 :
                            typeof i === 'number' ?
                                i : 0;
                    };

                      total_amount = api
                        .column(5)
                        .data()
                        .reduce( function (a, b) {
                              return intVal(a) + intVal(b);
                        },0);

                  $( api.column( 4 ).footer() ).html('Total File:');
                  $( api.column( 5 ).footer() ).html(total_amount);
                },

                "columns": [
                  { "data": "priority" },
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
                  { "data": "unique_code" },
                  { "data": "depertment_name" },
                  { "data": "designation_name" },
                  { "data": "count_id" },
                  { "data": "file_type_name" },
                  { "data": "Link",
                    "mRender": function (data, type, full) {
                        return '<a href="{{URL::to('/')}}/document_archive/'+full.id+'/view_details" class="btn btn-success btn-sm btn-flat"><span class="glyphicon  glyphicon-ok"></span> View details</a>';
                    }
                  },
                ],

                "order": [[0,'asc']]
            });
        }
      });
    }

    dataLoad();

    $location= $('#location').select2({
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

    $location.on('select2:select', function (e) {
        dataLoad();
    });

    $location.on('select2:unselect', function (e) {
        $('#location').val(null).trigger("change");
        dataLoad();
    });

    $designation = select2Dropdown("#designation_id", "{{ url('/designation_list_data') }}", "Enter Designation Name");
    $department = select2Dropdown("#department_id", "{{ url('/depertment_list_data') }}", "Enter Department Name");
    $category = select2Dropdown("#category_id", "{{ url('/category_list_data') }}", "Enter Category Name");
    $section = select2Dropdown("#section_id", "{{ url('/section_list_data') }}", "Enter Sub-Department Name");
    $employee_type = select2Dropdown("#employee_type_id", "{{ url('/employeestatus_list_data') }}", "Enter Employee Type");

    $designation.on('select2:select', function (e) {
        dataLoad();
    });

    $designation.on('select2:unselect', function (e) {
        $('#designation_id').val(null).trigger("change");
        dataLoad();
    });

    $department.on('select2:select', function (e) {
        dataLoad();
    });

    $department.on('select2:unselect', function (e) {
        $('#department_id').val(null).trigger("change");
        dataLoad();
    });

    $category.on('select2:select', function (e) {
        dataLoad();
    });

    $category.on('select2:unselect', function (e) {
        $('#category_id').val(null).trigger("change");
        dataLoad();
    });

    $section.on('select2:select', function (e) {
        dataLoad();
    });

    $section.on('select2:unselect', function (e) {
        $('#section_id').val(null).trigger("change");
        dataLoad();
    });

    $employee_type.on('select2:select', function (e) {
        dataLoad();
    });

    $employee_type.on('select2:unselect', function (e) {
        $('#employee_type_id').val(null).trigger("change");
        dataLoad();
    });
});
</script>
@endsection
