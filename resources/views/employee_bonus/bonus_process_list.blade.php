@extends('layouts.main')
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">


@endsection
<!-- content -->
@section('content')
<div class="box box-default">
  <div class="box-header with-border">
    <h3 class="box-title">Bonus Process List </h3>

    <div class="col-xs-12" style="text-align: center;">
      <a href="{{URL::to('bonusprocess/create')}}" ><input type="button" value="Create New" class="btn-success btn btn-sm button btn-flat" style="font-size: 12px; font-weight: bold; width: 158px;"></a>
      <a href="{{URL::to('bonusprocesscw')}}"><input type="button" value="Create CW" class="btn-warning btn btn-sm button  btn-flat" style="font-size: 12px; font-weight: bold;width: 158px;"></a>
      <button type="button" class="btn-danger btn btn-sm button  btn-flat "  style="font-size: 12px; font-weight: bold;width: 158px;" data-toggle="modal" data-target="#bonus_process_delete">Delete Processed Bonus</button>
    </div>

    <div class="col-xs-12" style="text-align: center; margin-top: 5px;">

      <div class="col-lg-3">
        <select  class="onchange form-control" id="pending_status"  style="width: 100%;" name="pending_status"   required>
          <option value="1">Pending</option>
          <option value="2">Generated</option>
          <option value="3">-All-</option>
        </select>
      </div>
      <div class="col-lg-3">
        <select  class="form-control" id="declaration_date" style="width: 100%;" name="declaration_date"   required>
        </select>
      </div>
      <div class="col-lg-6">
      <div class="row" style="text-align:left;">

            <div class="col-md-4" style="margin-top: 15px;">

            <label style="color:red;"> Apply Date Range</label>
            <input type="checkbox" name="apply_daterange" id="apply_daterange"  class="onchange">
            </div>

            <div class="col-md-4" style="margin-top: 15px;">
              <label>Date From</label>
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control pull-right onchange date" name="date_from" id="date_from" placeholder="checkin"  data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('date') }}"  readonly>
              </div>
            </div>

            <div class="col-md-4" style="margin-top: 15px;">
              <label>Date To</label>
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control pull-right onchange date" name="date_to"  id="date_to"  placeholder="checkin"  data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('date') }}"  readonly>
              </div>
            </div>


      </div>



      </div>
    </div>

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>


  <!-- Modal for Delete Salary Process  -->
<div class="modal fade" id="bonus_process_delete"  role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h3 class="modal-title" id="groupAddLabel">Delete Bonus Process</h3>
        </div>

        {!! Form::open(['method'=>'POST', 'action'=>['EmployeeBonusController@deletebonusprocess'], 'id'=>'frm_process_delete' ]) !!}
        <div class="modal-body">

          <div class="row">
            <div class="form-group has-feedback {{ $errors->has('hrm_employee_bonus_master_id') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
              <label class="col-lg-3 control-label">Bonus Name</label>
              <div class="col-lg-9">

                <select  class="form-control col-lg-12 bonus_master"  name="hrm_employee_bonus_master_id"  style="width: 100%;" required>
                </select>

              </div>
            </div>
            <div class="form-group has-feedback {{ $errors->has('bonus_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
              <label class="col-lg-3 control-label">Write the delete reson </label>
              <div class="col-lg-9">

                <input type="text" name="note" class="form-control"  maxlength="95" placeholder="Write delete reson.. (for history store)" required>

              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" id="button_sunmit" class="btn btn-danger btn-flat">Delete</button>
        </div>
        {!! Form::close() !!}
      </div>
    </div>
  </div>
  <!-- End for Delete Salary Process  -->

  <div class="box-body">
    <div class="row">
      <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">
        <table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%" style="font-size: 14px;">
          <thead>
            <tr>
              <th style="width: 10%">Location Name</th>
              <th style="width: 10%">Apply</th>
              <th style="width: 10%">Declaration Date</th>
              <th style="width: 10%">Bonus Name</th>
              <th style="width: 10%">Based Month</th>
              <th style="width: 10%">% of Gross/TK.</th>
              <th style="width: 20%">Payment Term</th>
              <th style="width: 10%">Total Bonus</th>
              <th style="width: 10%">Status</th>
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


<!-- script -->
@section('script')
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="https://cdn.datatables.net/buttons/2.4.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.0/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.0/js/buttons.print.min.js"></script>









<script>

$('.date').datepicker({
    autoclose: true
});


$(document).ready(function($) {





$role= $('#declaration_date').select2({
      placeholder: 'SELECT Declaration Date...',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/process_bonus_date_list',
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
        $('#declaration_date').val(null).trigger("change");
        dataLoad();
    });



  $('.onchange').on('change', function(){
      dataLoad();
  });



    $('.bonus_master').select2({
      placeholder: 'Enter Bonus Name',
      allowClear: true,
        ajax: {
            dataType: 'json',
            url: "{{URL::to('/')}}/process_bonus_list",
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



        function dataLoad() {
            table = $('#list_table').DataTable( {
                "destroy":    true,
                "processing": true,
                "serverSide": false,
                "searching":  true,
                "ordering":   true,
                "bInfo":      true,
                "paging":     true,
                "footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;
                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '')*1 :
                            typeof i === 'number' ?
                                i : 0;
                    };

                    total_amount = api
                        .column(7)
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        },0);

                    $( api.column( 6 ).footer() ).html('Total Bonus:');
                    $( api.column( 7 ).footer() ).html(total_amount);
                },
                ajax: {
                    url: "{{ url('/bonusprocess_list') }}",
                    type: "POST",
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},

                    data: function (query) {
                        query.declaration_date = $("#declaration_date").val()
                        query.pending_status = $("#pending_status").val()
                        query.date_from = $("#date_from").val()
                        query.date_to = $("#date_to").val()
                        query.apply_daterange = $('#apply_daterange').is(':checked')

                    }
                },
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                columns: [
                    { "data": "location_name" },
                    { "data": "apply_for" },
                    { "data": "declaration_date" },
                    { "data": "bonus_name" },
                    { "data": "based_on_month" },
                    { "data": "amount_type" },
                    { "data": "payment_term" },
                    { "data": "bonus_amount" },
                    { "data": "status" },
                ],
                "order": [2, 'desc']
            });
        }


dataLoad();

});

</script>


@endsection

