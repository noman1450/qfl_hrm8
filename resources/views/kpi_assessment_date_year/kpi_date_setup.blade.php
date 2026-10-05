@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}"></script>
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@endsection

@section('content')
<div class="box box-default">
  <div class="box-header with-border">
    <h3 class="box-title">KPI Date Setup List</h3>

    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
      Create Year
      </button>
    </div>

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>

  <div class="box-body">
    <div class="row">
      <div class="form-group col-lg-8 col-md-8 col-xs-12">
        <table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th style="width: 30%">Assessment Year</th>
              <th style="width: 20%">Start Date</th>
              <th style="width: 20%">End Date</th>
              <!-- <th style="width: 10%">Edit</th> -->
              <!-- <th style="width: 10%">Delete</th> -->
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



    var $year_select2 = $('#hrm_kpi_assesment_year_id').select2({
            placeholder: 'Enter Year',
            allowClear: true,
            ajax: {
              dataType: 'json',
              url: '{{URL::to('/')}}/kpi_daterange_listdata',
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


    $.ajax({
      type:   'POST',
      url :   "{{URL::to('/')}}/kpi_date_list",
      headers:{
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
            { "data": "description" },
            { "data": "start_date" },
            { "data": "end_date" },
            // { "data": "Link",
            //  "mRender": function (data, type, full) {
            //  return '<a  data-id="'+full.id+'" data-start_date="'+full.start_date+'" data-end_date="'+full.end_date+'" data-hrm_kpi_assesment_year_id="'+full.hrm_kpi_assesment_year_id+'" data-description="'+full.description+'"   class="btn btn-primary btn-flat btn-sm showme"><span class="glyphicon glyphicon-edit ">Edit</a>';
            //  }
            // },
            // { "data": "Link",
            //   "mRender": function (data, type, full) {
            //       return '<a href="{{URL::to('/')}}/kpi_date_setup/'+full.id+'/cancel"  onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-sm btn-flat"><span class="glyphicon glyphicon-trash">Delete</a>';
            //   }
            // },

          ],
          "order": [[0,'asc']]
          });
      }
    });


   $('#list_table').on('click', '.showme', function(e){
        $('#id').val($(this).data('id'));
        $('#start_date').val($(this).data('start_date'));
        $('#end_date').val($(this).data('end_date'));
        $('#hrm_kpi_assesment_year_id').append('<option value='+$(this).data('hrm_kpi_assesment_year_id')+'>'+$(this).data('description')+'</option>');
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
        <h5 class="modal-title" id="exampleModalLabel">Create New Date Range</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal" method="POST" action="{{url('kpi_assessment_date_store')}}">
        {{ csrf_field() }}

        <input type="text" name="id" id="id" hidden="">

        <div class="modal-body">
          <div class="form-group has-feedback {{ $errors->has('start_date') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
            <label class="col-lg-3 control-label">Select Year</label>
            <div class="col-lg-9">
              <select class="form-control" id="hrm_kpi_assesment_year_id" name="hrm_kpi_assesment_year_id" style="width: 100%;" required>
              </select>
            </div>
          </div>
          <div class="form-group has-feedback {{ $errors->has('start_date') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
            <label class="col-lg-3 control-label">Start From</label>
            <div class="col-lg-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control pull-right" id="start_date" name="start_date" data-date-format="yyyy-mm-dd" value="{{date('Y-m-d')}}" value="{{ old('start_date') }}" required readonly>
                @if ($errors->has('start_date'))
                <span class="help-block">
                  <strong>{{ $errors->first('start_date') }}</strong>
                </span>
                @endif
              </div>
            </div>
          </div>
          <div class="form-group has-feedback {{ $errors->has('end_date') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
            <label class="col-lg-3 control-label">End Date</label>
            <div class="col-lg-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control pull-right" id="end_date" name="end_date" data-date-format="yyyy-mm-dd" value="{{date('Y-m-d')}}" value="{{ old('end_date') }}" required readonly>
                @if ($errors->has('end_date'))
                <span class="help-block">
                  <strong>{{ $errors->first('end_date') }}</strong>
                </span>
                @endif
              </div>
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
