@extends('layouts.main')

@section('styles')
  <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@endsection

@section('content')
<div class="box box-default">
  <div class="box-header with-border">
    <h3 class="box-title">Weekly Holiday Config List</h3>

     <h5 style="color: red;">* Default Friday is Setup for every location</h5>
     <!-- <h5 style="color: red;">* Default Setup (Including OT Eligibility) </h5> -->
     <!-- <h5 style="color: blue;">** If any Location change the default setup please change from here</h5> -->
    


    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
      <a href="{{ URL::to('weekly_holiday_config_create') }}" class="btn-success btn btn-sm button pull-left btn-flat" style="font-size: 12px; font-weight: bold;">
        Create or Update
      </a>
    </div>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>
  
  <div class="box-body">
    <div class="row">
      <div class="form-group col-lg-8 col-md-8 col-xs-12">
        <table id="config_list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th style="">Location Name</th>
              <th style="">Days Name</th>
              <th style="">Start Date</th>
              <th style="">End Date</th>
              <th style="">Is Active</th>
      
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
  <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>

  <script>
    $('#config_list_table').DataTable({
        destroy:    	  true,
        responsive:     true,
        processing:     true,
        serverSide:     true,
        paging:         true,
        lengthChange:   true,
        searching:      true,
        ordering:       true,
        info:           true,
        autoWidth:      false,
        width:          "100%",

        ajax: {
          url: '{{ URL::to('/') }}/weekly_holiday_config_data_list',
          type: "POST",
          headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}, 
          dataType: "json",
        },
        columns: [                                     
          { data: "location_name" },
          { data: "days_name" },
          { data: "start_date" },
          { data: "end_date" },
          { data: "isActive" },
        ],
        order: [
          [0, 'desc']
        ]
    });
  </script>
@endsection