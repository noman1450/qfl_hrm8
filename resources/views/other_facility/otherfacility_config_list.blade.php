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
    <h3 class="box-title">Employee Fringe Benefits List</h3>

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>

    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
        <a href="{{ URL::to('otherfacilityconfiq/create')}}"><input type="button" value="Create New" class="btn-success btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold;"></a>
    </div>

  </div>



  <div class="box-body">
    <div class="row">
        <div class="form-group col-lg-6 col-md-6 col-xs-12" style="overflow: auto;">    
        <table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th style="width: 05%">Dtls</th>
              <th style="width: 90%">Designation</th>
              <th style="width: 05%">Edit</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
      <div class="form-group col-lg-6 col-md-6 col-xs-12">
        
        <form  method="POST" action="{{url('otherfacilityconfiqstore')}}">
        {{ csrf_field() }}

          <div class="form-group">
            <div class="col-md-6">
              <label>Year <span style="color: red">*</span></label>
              <select  class="form-control" name="year" id="year" style="width: 100%;" required>
              </select>
            </div>

            <div class="col-md-6">
              <label>Month <span style="color: red">*</span></label>
              <select  class="form-control" name="month_name" id="month_name" style="width: 100%;" required>
              </select>
            </div>


          </div>




          <div class="form-group">
            <div class="col-md-12">
              <label>Apply Designation <span style="color: red">*</span></label>
              <select  class="form-control" name="designation" style="width: 100%;" >
                <option value="0">-ALL-</option>
                @foreach ($designation as $keys)
                <option value={{$keys->id}}>{{$keys->designation_name}}</option>
                @endforeach
              </select>
            </div>
          </div>


          
          <div class="form-group">
            <div class="col-md-12">
              <label>Apply Location <span style="color: red">*</span> </label>
              <select  class="form-control" name="location" style="width: 100%;" >
                <option value="0">-ALL-</option>
                <option value="999">-ALL Depot & Lab-</option>
                @foreach ($location as $keys)
                <option value={{$keys->id}}>{{$keys->location_name}}</option>
                @endforeach
              </select>
            </div>
          </div>



          <div class="form-group">
            <div class="col-md-12">
              <label>Including Employees Type <span style="color: red">*</span></label>
              <select  class="form-control" name="employeestatus[]" multiple="multiple"  id="employeestatus" style="width: 100%;"  required>
              </select>
            </div>
          </div>


          <div class="form-group">
            <div class="col-md-12">
              <label>Employee(Specific)</label>
              <select  class="form-control" name="employee_name"   id="employee_name"  style="width: 100%;">
              </select>
            </div>
          </div>



          <div class="form-group">
              <div class="col-md-12" style="padding-top: 10px;">
                <input type="submit" class=" btn btn-success btn-flat pull-right"  value="Submit" >
              </div>
          </div>

      </form>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>



<script>
    function format(d) {
      console.log(d);
      return '<table class="table table-bordered table-hover" cellspacing="0" width="100%">'+
            '<tr>'+
              '<td>'+"Salary Head"+'</td>'+
              '<td>'+"Status"+'</td>'+
              '<td>'+"Amount"+'</td>'+
              '<td>'+"Type"+'</td>'+
              // '<td>'+"Head Amount"+'</td>'+
            '</tr>'+
            '<tr>'+
              '<td>'+d.salary_head+'</td>'+
              '<td>'+d.Status+'</td>'+
              '<td>'+d.amount+'</td>'+
              '<td>'+d.amount_type+'</td>'+
              // '<td>'+d.salary_head_amount+'</td>'+
            '</tr>'+  
          '</table>';
    }
$(document).ready(function($) {


 
      for (i = new Date().getFullYear(); i > 2018; i--){
           $('#year').append($('<option />').val(i).html(i));
      }

      $('#month_name').select2({
        placeholder: 'Enter Month  Name',
        allowClear: true,
        ajax: {
          dataType: 'json',
          url: '{{URL::to('/')}}/monthlist',
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


    $('#employeestatus').select2({
      placeholder: 'Enter employee status',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/employeestatus_list_data',
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



  $('#employee_name').select2({
        placeholder:'Enter an Employee Name',
        allowClear: true,

          ajax: {
              dataType: 'json',
              url: "{{URL::to('/')}}/join_employee_list",
              delay: 250,         
            data: function(params) {
                return {
                  term: params.term,
                  apply_old_info:$('input[name=apply_old_info]:checked').val()
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




          var table = $('#list_table').DataTable( {
            "destroy":    true,
            "processing": true,
            "serverSide": true,
            "searching":  true,
            "ordering":   true,
            "bInfo":      true,
            "paging":     true,
            // "aoColumnDefs": [{ "bVisible": false, "aTargets": [1] }],
            "ajax": {
                "url": "{{URL::to('/')}}/otherfacilityconfiglistdata",
                "type": "POST",
                "headers":{'X-CSRF-TOKEN': '{{ csrf_token() }}'},  
            },
            "columns": [
             {
                "className":      'details-control',
                "orderable":      true,
                "data":           null,
                "defaultContent": ''
              },                                                   
              { "data": "designation_name" },  
              { "data": "Link", name: 'action', orderable: false, searchable: false},
             ],
            "order": [[1, 'asc']]
          });


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

});
</script>