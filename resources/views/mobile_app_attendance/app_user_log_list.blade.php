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



        </div>

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>

  <div class="box-body">
    <div class="row">
      <div class="form-group col-lg-6 col-md-6 col-xs-6" style="overflow: auto;">    
        <table id="designation_list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th style="width: 05%"></th>
              <th style="width: 20%">Employee Name</th>
              <th style="width: 10%">Emp. Summary</th>
              <!-- <th style="width: 20%">Designation</th> -->
              <!-- <th style="width: 10%">Punch Date</th> -->
              <th style="width: 10%">Start Time</th>
              <th style="width: 15%">End Time</th>
              <th style="width: 15%">MAP</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>


      <div class="form-group col-lg-6 col-md-6 col-xs-6">    
           <section id="global-opt">
            <script src="https://maps.google.com/maps/api/js?key=AIzaSyAbsKDTyrJOdItTD8nRVUb2mhfE2pHf7WE"
                    type="text/javascript"></script>

            <div id="map" style="width: 100%; height: 500px;">

            </div>
          
            <script type="text/javascript">
              


            
            </script>

         
        </section>
              


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
    function format(d) {
      console.log(d);
      return '<table class="table table-bordered table-hover" cellspacing="0" width="100%">'+
            '<tr>'+
              '<td>'+"In Time"+'</td>'+
              '<td>'+"Out Time"+'</td>'+
              '<td>'+"Working Hour"+'</td>'+
            '</tr>'+
            '<tr>'+
              '<td>'+d.indatetime+'</td>'+
              '<td>'+d.outdatetime+'</td>'+
              '<td>'+d.working_hour+'</td>'+
            '</tr>'+  
          '</table>';
    }
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
        url :   "{{URL::to('/')}}/attendancelistdata_forappuser",
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
                "className":      'details-control',
                "orderable":      true,
                "data":           null,
                "defaultContent": ''
              },


              { "data": "Link",
                "mRender": function (data, type, full) {
                    return '<a target="_blank"  href="{{URL::to('/')}}/employeeinfo/'+full.hrm_employee_id+'">'+full.employee_name+'</a>';
                }
              },
              { "data": "employee_summary" },
              // { "data": "designation_name" },
              // { "data": "punche_date" },
              { "data": "in_time" },
              { "data": "out_time" },
              { "data": "Link",
                "mRender": function (data, type, full) {
                    return '<button type="button"  class="btn btn-info btn-block click-button btn-flat" id="'+full.hrm_employee_id+'" >Show</button>';
                }
              },

              ],
              // "order": [[1, 'asc']]
              order: [ 1, 'asc' ]
            });
        }
      }); 
    } 

    dataLoad();   

    $('#designation_list_table tbody').on('click', 'td.details-control', function () {
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

    

    // console.log(locations);
    $('#designation_list_table tbody').on('click','.click-button',function(){

        var locations = [];
        var attendance_date = $('#process_date').val();
        
        $.ajax({
            method: 'GET',
            url: '{{URL::to('/')}}/getuserwisedata/'+$(this).attr('id')+'/'+ attendance_date +'/getdata',
            dataType: 'json',
            success: function(data) {
              for (i = 0; i < data.user_data.length; i++) {
                locations.push([
                  data.user_data[i].employee_name, 
                  data.user_data[i].lat, 
                  data.user_data[i].lon, 
                  0
                ]);
              }

              var map = new google.maps.Map(document.getElementById('map'), {
                  zoom: 11,
                  // center: new google.maps.LatLng(23.643999, 88.855637),
                  center: new google.maps.LatLng(data.user_data[0].lat, data.user_data[0].lon),
                  mapTypeId: google.maps.MapTypeId.ROADMAP
              });
              var iconBase = 'http://www.drug-international.com/r-marker.gif';
              var infowindow = new google.maps.InfoWindow();
              var marker, i;
              for (i = 0; i < locations.length; i++) {
                  marker = new google.maps.Marker({
                      position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                      animation: google.maps.Animation.DROP,
                      map: map,
                      icon: iconBase, //+ 'parking_lot_maps.png',
                      optimized: false
                  });
                  google.maps.event.addListener(marker, 'mouseover', (function (marker, i) {
                      return function () {
                          infowindow.setContent(locations[i][0]);
                          infowindow.open(map, marker);
                      }
                  })(marker, i));
              }
           }
        });        
    })






});
</script>