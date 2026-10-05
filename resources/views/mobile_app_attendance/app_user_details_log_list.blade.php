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
    <h3 class="box-title">Mobile App Attandance List Data</h3>
     

      <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
          <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">  
              <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>                
                  <input type="text" class="form-control pull-right onchange" id="process_date" name="process_date" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('joining_date') }}" required readonly>
              </div>                  
          </div> 


          <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">  
              <select class="form-control" id="location" name="location" style="width: 100%;" required>
                  @foreach ($default_user_location as $keys)
                      <option value={{$keys->id}} selected>{{$keys->location_name}}</option>
                  @endforeach
              </select>                 
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
              <th style="width: 05%"></th>
              <th style="width: 20%">Employee Name</th>
              <th style="width: 40%">Emp. Summary</th>
              <!-- <th style="width: 20%">Designation</th> -->
              <th style="width: 10%">Status</th>
              <th style="width: 10%">Punch Time</th>
              <!-- <th style="width: 15%">End Time</th> -->
              <th style="width: 15%">Action</th>
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
<!-- <script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script> -->
<!-- <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script> -->
<!-- <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script> -->
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>



<script>

$(document).ready(function($) {
    $('#process_date').datepicker({
      autoclose: true
    });

  $role=  $('#location').select2({
      placeholder: 'Enter a location mandatory',
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

    $(".onchange").change(function(){
        dataLoad();
    });


    dataLoad = function(){

      if ($("#location").val() == null){
        location_id = 0;
      }else{
        location_id = $("#location").val();
      }


      $.ajax({
        type:   'POST', 
        url :   "{{URL::to('/')}}/get_app_user_details_log_list",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },        
        data:   {
                   punch_date: $("#process_date").val(),
                   location: location_id,
                   // punch_date: $("#date-from").val(),
                   // dateto:   $("#date-to").val()
                },          
        dataType: 'json',
        success: function(data) {
          var dataSet = data.data;
            table = $('#designation_list_table').DataTable( {
              destroy:    true,
              paging:     true,
              searching:  true,
              ordering:   true,
              bInfo:      true,  
              "data":     dataSet,
              "columns": [

              {
                "render": function (data, type, JsonResultRow, meta) {
                    return '<img src="{{asset('employee_image')}}/'+JsonResultRow.Images+'" style="height:30px; width:30px; border-radius: 30px;"/>';
                }
              }, 

              { "data": "Link",
                "mRender": function (data, type, full) {
                    return '<a target="_blank"  href="{{URL::to('/')}}/employeeinfo/'+full.hrm_employee_id+'">'+full.employee_name+'</a>';
                }
              },
              { "data": "employee_summary" },
              // { "data": "StatusInOut", name: 'StatusInOut', orderable: false, searchable: false},
              { "data": "status_result" },
              { "data": "date_time" },
              //{ "data": "date_time" },
              //{ "data": "Link",
              //   "mRender": function (data, type, full) {
              //       return '<button type="button"  class="btn btn-info btn-block click-button btn-flat" id="'+full.hrm_employee_id+'" >Show</button>';
              //   }
              // },
              { "data": "Link", name: 'link', orderable: false, searchable: false},

              ],
              // "order": [[1, 'asc']]
              order: [ 4, 'desc' ]
            });
        }
      }); 
    } 

    dataLoad();   

});






      $(document).on("click", ".modalLink", function (e) {
        e.preventDefault();


        var modal_size = $(this).attr('modal_size');

        if (modal_size!=='' && typeof modal_size !== typeof undefined && modal_size !== false) {
            $("#modalSize").addClass(modal_size);
        } else{
            $("#modalSize").addClass('modal-md');
        }


        var title = $(this).attr('title');
        $("#showDetaildModalTile").text(title);
        

        $.ajax({
            type: "GET",
            url: $(this).attr('href'),
            beforeSend: function () {
                $(".loadingImg").html("<img src='{{ asset('/img/loader-small.gif') }}' />");
                $("#showDetaildModalBody").html("");
            },
            success: function (data) {
                setTimeout(() => {
                    $(".loadingImg").html("");
                    $("#showDetaildModalBody").html(data);
                    $("#showDetaildModal").modal('show');
                }, 1000)
            }
        });










    });






</script>


    <div class="modal fade" id="showDetaildModal" data-backdrop="static">
        <div id="modalSize" class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                    <h3 class="modal-title" id="showDetaildModalTile"></h3>
                </div>
                <div class="modal-body" id="showDetaildModalBody"></div>
                <div class="modal-footer">
                    <a data-dismiss="modal" class="btn btn-default" href="#">Close</a>
                </div>
            </div>
        </div>
    </div>

