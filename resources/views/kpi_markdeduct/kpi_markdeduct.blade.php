@extends('layouts.main')
<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}"></script>
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@endsection
<!-- content -->
@section('content')
<div class="box box-default">
  <div class="box-header with-border">
    <h3 class="box-title">KPI Mark Deduction List</h3>

    <div class="row">
        <div class="col-md-3">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal" style="margin-top: 20px">
                Create New
            </button>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label>Select Year</label>
                <select class="form-control" id="hrm_kpi_assesment_date_id" style="width: 100%;" required>
                </select>
            </div>
        </div>
    </div>

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>

  <div class="box-body">
    <div class="row">
      <div class="form-group col-lg-10 col-md-10 col-xs-12" style="overflow: auto;">
        <table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th style="width: 45%">Assessment Year</th>
              <th style="width: 20%">File Type</th>
              <th style="width: 15%">Point Deduct</th>
              <!-- <th style="width: 10%">Edit</th> -->
              <th style="width: 10%">Delete</th>
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
<script src="{{asset('bootstrap/js/bootstrap.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>

<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

<script src="{{asset('js/fileinput.js')}}"></script>

<script>

$(document).ready(function($) {


    $('#start_date').datepicker({
      autoclose: true
    });
    $('#end_date').datepicker({
      autoclose: true
    });



      $('#hrm_kpi_assesment_date_id').select2({
        placeholder: 'Select Year/Month',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: '{{URL::to('/')}}/kpi_assesment_date_list_data',
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
    }).on('select2:select', function() {
        loadData()
    }).on('select2:unselect', function() {
        loadData()
    });

    $('#hrm_kpi_assesment_date_id2').select2({
              placeholder: 'Select Year/Month',
              allowClear: true,
              ajax: {
                dataType: 'json',
                url: '{{URL::to('/')}}/kpi_assesment_date_list_data',
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


    $('#file_type').select2({
              placeholder: 'Enter File Type',
              allowClear: true,
              ajax: {
                dataType: 'json',
                url: '{{URL::to('/')}}/file_type_list',
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



    loadData()
    
    function loadData() {
        $.ajax({
          type:   'POST',
          url :   "{{URL::to('/')}}/kpi_markdeduct_list",
          headers:{'X-CSRF-TOKEN': '{{ csrf_token() }}'},
          dataType: 'json',
          data: {
                hrm_kpi_assesment_date_id: $('#hrm_kpi_assesment_date_id option:selected').val()
            },
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
                { "data": "date_year" },
                { "data": "file_type_name" },
                { "data": "point" },
                // { "data": "Link",
                //  "mRender": function (data, type, full) {
                //  return '<a  data-id="'+full.id+'" data-hrm_kpi_assesment_date_id="'+full.hrm_kpi_assesment_date_id+'" data-date_year="'+full.date_year+'" data-hrm_file_type_id="'+full.hrm_file_type_id+'" data-file_type_name="'+full.file_type_name+'" data-point="'+full.point+'"   class="btn btn-primary btn-flat btn-sm showme"><span class="glyphicon glyphicon-edit ">Edit</a>';
                //  }
                // },
                { "data": "Link",
                  "mRender": function (data, type, full) {
                      return '<a href="{{URL::to('/')}}/kpi_markdeduct/'+full.id+'/cancel"  onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-sm btn-flat"><span class="glyphicon glyphicon-trash">Delete</a>';
                  }
                },

              ],
              "order": [[0,'asc']]
              });
          }
        });
    }


   $('#list_table').on('click', '.showme', function(e){
        $('#id').val($(this).data('id'));
        $('#file_type').append('<option value='+$(this).data('hrm_file_type_id')+'>'+$(this).data('file_type_name')+'</option>');
        $('#hrm_kpi_assesment_date_id').append('<option value='+$(this).data('hrm_kpi_assesment_date_id')+'>'+$(this).data('date_year')+'</option>');
        $('#point').val($(this).data('point'));

        $('#exampleModal').modal('show');
    });


  $('#exampleModal').on('hidden.bs.modal', function () {
   location.reload();
  })

});

</script>



@endsection
<!-- Modal -->
<div class="modal fade" id="exampleModal"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Create New Mark Deduction Name</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal" method="POST" action="{{url('kpi_markdeduct')}}">
        {{ csrf_field() }}

        <input type="text" name="id" id="id" hidden="">

        <div class="modal-body">

          <div class="form-group has-feedback {{ $errors->has('start_date') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
            <label class="col-lg-3 control-label">Select Year</label>
            <div class="col-lg-9">
              <select class="form-control" id="hrm_kpi_assesment_date_id2" name="hrm_kpi_assesment_date_id" style="width: 100%;" required>
              </select>
            </div>
          </div>

          <div class="form-group has-feedback {{ $errors->has('start_date') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
            <label class="col-lg-3 control-label">Select File Type</label>
            <div class="col-lg-9">
              <select class="form-control" id="file_type" name="file_type" style="width: 100%;" required>
              </select>
            </div>
          </div>


          <div class="form-group has-feedback {{ $errors->has('start_date') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
            <label class="col-lg-3 control-label">Point Deduct</label>
            <div class="col-lg-9">
            <input class="form-control" type="number" step="0.01" placeholder="Point" id="point"  name="point">
            </div>
          </div>



        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <input type="submit" class="btn btn-primary"   value="Save">
        </div>
      </form>
    </div>
  </div>
</div>
