@extends('layouts.main')
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

@endsection
@section('content')
<div class="box box-default">
  <div class="box-header with-border">
    <h3 class="box-title">Attendance Device Config List</h3>
    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
      <button type="button" class="btn-success btn btn-sm button pull-left btn-flat" data-toggle="modal" data-target="#modal_create_salary_grade" style="font-size: 12px; font-weight: bold;">Create New Config</button>
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
              <th style="width: 50%">Location Name</th>
              <th style="width: 20%">Device Name</th>
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


<div class="modal fade" id="modal_create_salary_grade" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="groupAddLabel">Interval Time Create</h4>
      </div>
      
      <form method="POST" action="{{url('device_config_store')}}">
        {{ csrf_field() }}
        <div class="modal-body">
          
          <div class="row">
            
            <div class="form-group">
              <div class="col-md-12">
                <label>Location</label>
                <select  class="form-control location" name="location" style="width: 100%;" required>
                </select>
              </div>
            </div>

            <div class="form-group">
              <div class="col-md-12">
                <label>Attendace Device Name</label>
                <select  class="form-control" name="device_name" style="width: 100%;" required>
                  @foreach($device as $data)
                    <option value="{{$data->id}}" >{{$data->description}}</option>
                  @endforeach

                </select>
              </div>
            </div>

            
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" id="button_sunmit" class="btn btn-success btn-flat">Submit</button>
        </div>
      </form>
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
      $.ajax({
        type:   'POST', 
        url :   "{{URL::to('/')}}/device_config_list",
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
              bInfo:      false,  
              "data":     dataSet,

            "columns": [
              { "data": "location_name" },
              { "data": "description" },
              { "data": "Link",
                render: function (data, type, full) {
                    return '<a href="{{URL::to('/')}}/device_config/'+full.id+'/cancel"  onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-sm btn-flat"><span class="glyphicon glyphicon-trash">Delete</a>';
                }
              },
                     


            ],
            "order": [[0,'asc']]
            });
        }
      }); 


       $('#list_table').on('click', '.showme', function(e){
            $('#grade_name').val($(this).data('grade_name'));
            $('#id').val($(this).data('id'));

            $('#modal_form').modal('show');
        });



   $('.location').select2({
      placeholder: 'Select a Location',
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



});

</script>

@endsection