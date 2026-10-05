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

  <form class="form-horizontal" method="POST" action="{{url('kpi_employee_final_submit')}}">
    {{ csrf_field() }}
  <div class="box-header with-border">
    <h3 class="box-title">Employee KPI List</h3>

      <!--<div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
                <a href="{{ URL::to('kpi_employee/create')}}"><input type="button" value="Create New" class="btn-success btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold;"></a>
            </div>
       -->
      <div class="col-xs-12 col-md-12" style="padding-left: 0px; padding-top: 10px;">
         <div class="col-xs-4 col-md-4">
             <select class="form-control" id="hrm_kpi_assesment_date_id" name="hrm_kpi_assesment_date_id" style="width: 100%;" required>
             </select>
          </div>
      </div>


    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>

  <div class="box-body">
    <div class="row">
      <div class="form-group col-lg-12 col-md-12 col-xs-12">
        <table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th style="width: 5%">Dtls</th>
              <th style="width: 20%">Employee Name</th>
              <th style="width: 15%">Designation</th>
              <th style="width: 15%">Department</th>
              {{-- <th style="width: 10%">Total Points</th> --}}
              <th style="width: 25%">Evaluation Date</th>
              <th style="width: 5%">Edit</th>
              <!-- <th style="width: 5%">Print</th> -->
              <th style="width: 5%">Delete</th>
              <!-- <th style="width: 5%">Final Submit</th> -->
              <th style="width: 10%;color: red;"><input name="select_all" value="1" id="example-select-all" type="checkbox" /> Final Submit</th>

            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
      <div class="form-group col-lg-12 col-md-12 col-xs-12">
          <div class="col-lg-12">
              <button type="submit" class="btn btn-success block btn-flat btn pull-right" >Submit</button>
          </div>
      </div>
    </div>
  </div>
 </form>
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
    function format(d) {
      console.log(d);
      return '<table class="table table-bordered table-hover" cellspacing="0" width="100%">'+
            '<tr>'+
              '<td>'+"Task Name"+'</td>'+
              // '<td>'+"Mark"+'</td>'+
              // '<td>'+"Point"+'</td>'+

            '</tr>'+
            '<tr>'+
              '<td>'+d.task_name+'</td>'+
              // '<td>'+d.marks+'</td>'+
              // '<td>'+d.point+'</td>'+
            '</tr>'+
          '</table>';
    }
$(document).ready(function($) {

      var $assesment = $('#hrm_kpi_assesment_date_id').select2({
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

        $assesment.on("select2:select", function (e) {
            dataLoad();
        });

        $assesment.on("select2:unselect", function (e) {
            dataLoad();
        })



    dataLoad = function(){
      $.ajax({
        type:   'POST',
        url :   "{{URL::to('/')}}/kpi_employee_list_data",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
        data:   {
                  hrm_kpi_assesment_date_id : $("#hrm_kpi_assesment_date_id").val(),
        },
        dataType: 'json',
        success: function(data) {
          var dataSet = data.data;
            table = $('#list_table').DataTable( {
              destroy:    true,
              paging:     false,
              searching:  false,
              ordering:   true,
              bInfo:      true,
              "data":     dataSet,
              "columns": [
              {
                "className":      'details-control',
                "orderable":      true,
                "data":           null,
                "defaultContent": ''
              },
              { "data": "employee_name" },
              { "data": "designation_name" },
              { "data": "depertment_name" },
              // { "data": "total_points" },
              { "data": "kpi_year" },
              { "data": "Link",
                "mRender": function (data, type, full) {
                  return '<a href="{{URL::to('/')}}/kpi_employee/'+full.id+'/edit"> <span class="glyphicon glyphicon-edit"></span> Edit</a>';
                }
              },
              // { "data": "Link",
              //   "mRender": function (data, type, full) {
              //     return '<a target="_blank" href="{{URL::to('/')}}/kpi_employee/'+full.id+'/print"> <span class="glyphicon glyphicon-print"></span> Print</a>';
              //   }
              // },
              { "data": "Link",
                "mRender": function (data, type, full) {
                    return '<a href="{{URL::to('/')}}/kpi_employee/'+full.id+'/cancel"  onclick="return confirm(\'Do you really want to DELETE?\');" class=""><span class="glyphicon glyphicon-trash">Delete</a>';
                }
              },
              { "data": "Link",
                  "mRender": function (data, type, full) {
                    return '<input type="checkbox" name="id[]" value="'+full.id+'">';
                  }
              },

              ],
              // "order": [[1, 'asc']]
              order: [ 1, 'asc' ]
            });
        }
      });
     }



    $('#list_table tbody').on('click', 'td.details-control', function () {
      var tr = $(this).closest('tr');
      var row = table.row( tr );
      if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format(row.data()) ).show();
            tr.addClass('shown');
        }
    });

     $('#example-select-all').on('click', function(){
        var rows = table.rows({ 'search': 'applied' }).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);
     });


     $('#list_table tbody').on('change', 'input[type="checkbox"]', function(){
        if(!this.checked){
           var el = $('#example-select-all').get(0);
           if(el && el.checked && ('indeterminate' in el)){
              el.indeterminate = true;
           }
        }
     });


});
</script>
