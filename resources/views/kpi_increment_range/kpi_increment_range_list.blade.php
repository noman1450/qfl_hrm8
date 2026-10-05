@extends('layouts.main')
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

@endsection
@section('content')
<div class="box box-default">
  <div class="box-header with-border">
    <h3 class="box-title">KPI Increment Range List</h3>

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
      <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">
        <table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th style="width: 40%">Date/Year</th>
              <th style="width: 15%">Entry Status</th>
              <th style="width: 20%">Mark Range</th>
              <th style="width: 15%">Increment/Promotion</th>
              <!-- <th style="width: 5%">Edit</th> -->
              <th style="width: 5%">Delete</th>
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

  <script>

$(document).ready(function($) {


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


    loadData()

    function loadData() {
        $.ajax({
            type:   'POST',
            url :   "{{URL::to('/')}}/kpi_increment_range_list",
            headers:{ 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
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
                        { "data": "entry_statuss" },
                        { "data": "number_range" },
                        { "data": "increment_amounts" },
                        // { "data": "Link",
                        //  "mRender": function (data, type, full) {
                        //  return '<a  data-id="'+full.id+'" data-entry_status="'+full.entry_status+'" data-number_from="'+full.number_from+'" data-number_to="'+full.number_to+'" data-increment_amount="'+full.increment_amount+'" data-increment_amount_type="'+full.increment_amount_type+'" data-date_year="'+full.date_year+'" data-hrm_kpi_assesment_date_id="'+full.hrm_kpi_assesment_date_id+'"   class="btn btn-primary btn-flat btn-sm showme"><span class="glyphicon glyphicon-edit">Edit</a>';
                        //  }
                        // },
                        { "data": "Link",
                            "mRender": function (data, type, full) {
                                return '<a href="{{URL::to('/')}}/kpi_increment_range/'+full.id+'/cancel"  onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-flat btn-sm"><span class="glyphicon glyphicon-trash">Delete</a>';
                            }
                        },
                    ],

                    "order": [[0,'asc']]
                });
            }
        });
    }


      if($("#entry_status").val() == 1){
          $('.DurationForOne').show();
      }else{
          $('.DurationForOne').hide();
      }

      $('#entry_status').on('change', function(){
        if($("#entry_status").val() == 1){
          $('.DurationForOne').show();
        }else{
          $('.DurationForOne').hide();
        }
      });



     $('#list_table').on('click', '.showme', function(e){
          $('#number_from').val($(this).data('number_from'));
          $('#number_to').val($(this).data('number_to'));
          $('#increment_amount').val($(this).data('increment_amount'));
          $('#increment_amount_type').val($(this).data('increment_amount_type'));
          $('#entry_status').val($(this).data('entry_status'));
          $('#id').val($(this).data('id'));
          $('#hrm_kpi_assesment_date_id').append('<option value='+$(this).data('hrm_kpi_assesment_date_id')+'>'+$(this).data('date_year')+'</option>');


          $('#exampleModal').modal('show');
      });

    $('#exampleModal').on('hidden.bs.modal', function () {
     location.reload();
    })



});

</script>


@endsection

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Create New Increment Range</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
      </div>
      {!! Form::open(array('route'=>'kpi_increment_range.store', 'onkeypress'=> "return event.keyCode != 13;", 'files'=>true, 'id'=>'frm_leave')) !!}
      {{ csrf_field() }}
      <div class="modal-body">

        <input type="text" name="id" id="id" hidden="">

      <div class="form-group has-feedback {{ $errors->has('task_department_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
        <div class="col-lg-12 col-md-12 col-xs-12">
          <label>Select Year</label>
          <select class="form-control" id="hrm_kpi_assesment_date_id2" name="hrm_kpi_assesment_date_id" style="width: 100%;" required>
          </select>
        </div>
      </div>


        <div class="form-group has-feedback {{ $errors->has('date_to') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
          <label class="col-lg-12 control-label">Entry For</label>
          <div class="col-lg-12">
              <select  name="entry_status" id="entry_status" class="form-control">
                <option value="1">Increment</option>
                <option value="2">Promotion</option>
              </select>

          </div>
        </div>

        <div class="form-group has-feedback {{ $errors->has('date_to') ? ' has-error' : '' }} ">
          <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="col-lg-6">
                    <label class="control-label">Number From</label>
                    <input class="form-control" type="number" step="0.01" placeholder="Number From" id="number_from"  name="number_from" required="">
                </div>
                <div class="col-lg-6">
                     <label class="control-label">Number To</label>
                     <input class="form-control" type="number" step="0.01" placeholder="Number To" id="number_to"  name="number_to" required="">
                </div>

          </div>
        </div>

        <div class="form-group DurationForOne has-feedback {{ $errors->has('date_to') ? ' has-error' : '' }} ">
          <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="col-lg-6">
                    <label class="control-label">Percentage Amount</label>
                    <input class="form-control" type="number" step="0.01" placeholder="Percentage Amount" id="increment_amount"  name="increment_amount">
                </div>
                <div class="col-lg-6">
                    <label class="control-label">Amount Type</label>
                    <select  name="increment_amount_type" class="form-control">
                        <option value="1">%</option>
                        <option value="2">Tk.</option>
                    </select>
                </div>

          </div>
        </div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <input  type="submit"  class="btn btn-primary"   value="Save">
      </div>

      {!! Form::close() !!}
    </div>
  </div>
</div>
