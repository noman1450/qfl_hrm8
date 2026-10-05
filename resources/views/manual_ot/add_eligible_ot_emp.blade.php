@extends('layouts.main')
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables/jquery.dataTables.min.css')}}">
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
    <div class="box-title" style="margin-bottom: 20px;display: flex; align-items: center; justify-content: space-between;">
        <span>+ Add New OT Employee</span>

        <a href="{{ url('/eligible_ot_emp') }}" class="btn btn-info">Back To List</a>
    </div>

    <div class="row" >
      <form  method="POST" action="{{url('submit_eligible_ot_emp')}}">
        {{ csrf_field() }}
        
        <div class="col-lg-3 col-md-3 col-xs-12 form-group">
          <label>Location Name</label>
          <select class="form-control" id="location" name="location" style="width: 100%;" >
            @foreach ($location as $keys)
              @if($keys->id==$default_user_location[0]->id)
                <option value={{$default_user_location[0]->id}} selected>{{$default_user_location[0]->location_name}}</option>
              @else
                <option value={{$keys->id}}>{{$keys->location_name}}</option>
              @endif
            @endforeach
          </select>
        </div>


        <div class="col-lg-3 col-md-3 col-xs-12 form-group">
          <label>OT Date</label>
          <div class="input-group date">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" class="form-control pull-right" id="ot_date" name="ot_date" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('ot_date') }}" required readonly>

          </div>
        </div>
        <div class="col-lg-3 col-md-3 col-xs-12">
          <label>Allow OT (hh:mm)</label>
          <div class="input-group bootstrap-timepicker">
            <input type="text" class="form-control allow_ot" id="allow_ot" name="allow_ot" value="02:00" >
            <div class="input-group-addon">
              <i class="fa fa-clock-o"></i>
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-md-3 col-xs-12">
          <label>Department</label>
          <select class="form-control" id="department" name="department" style="width: 100%;" >
          </select>
        </div>

        <div class="col-lg-12 col-md-12 col-xs-12">

        </div>
        <div class="col-lg-3 col-md-3 col-xs-12">
          <label>Category</label>
          <select class="form-control" id="category" name="category" style="width: 100%;" >
          </select>
        </div>

        <div class="col-lg-3 col-md-3 col-xs-12">
          <label>Sub-Department</label>
          <select class="form-control" id="section" name="section" style="width: 100%;" >
          </select>
        </div>

        <div class="col-lg-3 col-md-3 col-xs-12" style="padding-top:25px;">
          <input type="button" id="search" value="Search" class=" btn-sm block btn-flat btn" style="background-color: #EEEEEE; color: black; border:1px solid gray;">
        </div>



        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
        <div class="form-group col-lg-12 col-md-12 col-xs-12">
          <table id="designation_list_table" class=" cell-border table table-bordered table-hover "  cellspacing="0" width="100%">
            <thead>
              <tr>
                <th  style="width: 5%"><input name="select_all" value="1" id="example-select-all" type="checkbox" />_All</th>
                <th style="width: 20%">Employee Name</th>
                <th style="width: 15%">Department</th>
                <th style="width: 10%">Designation</th>
                <th style="width: 15%">Sub-Department</th>
                <th style="width: 15%">Allow OT Time</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>

        <input type="submit" value="Submit" disabled class="btn-sm btn-success block btn-flat btn pull-right" id="submitBtn" style="margin-right: 20px">
      </form>
    </div>
  </div>
</div>
@endsection
@section('script')
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('dist/js/jquery.inputmask.bundle.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('plugins/timepicker/bootstrap-timepicker.min.js')}}"></script>
<script>

$(document).ready(function($) {


    $('#ot_date').datepicker({
      autoclose: true
    });


    $(".allow_ot").timepicker({
      showMeridian:false,
      showSeconds:false,
      showInputs: false
    });


 $('#department').select2({
      placeholder: 'Enter a Department',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/depertment_list_data',
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


 $('#section').select2({
      placeholder: 'Enter a Section',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/section_list_data',
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



    $('#category').select2({
      placeholder: 'Enter Employee Category',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/category_list_data',
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


    $("#search").click(function(){

      // if ($("#location").val() == null){
      //   location_id=0;
      // }else{
      //   location_id = $("#location").val();
      // }

      if ($("#month_name").val() == null){
        month_name=0;
      }else{
        month_name = $("#month_name").val();
      }

      if ($("#year").val() == null){
        year = 0;
      }else{
        year = $("#year").val();
      }


      if ($("#department").val() == null){
        department_id = 0;
      }else{
        department_id = $("#department").val();
      }

      if ($("#category").val() == null){
        category_id = 0;
      }else{
        category_id = $("#category").val();
      }


      if ($("#section").val() == null){
        section_id = 0;
      }else{
        section_id = $("#section").val();
      }

      $.ajax({
        type:   'GET',
        url :   "{{URL::to('/')}}/eligible_ot_view_data",
        // headers:{
        //           'X-CSRF-TOKEN': '{{ csrf_token() }}'
        //         },
        data:   {
                    ot_date    : $("#ot_date").val(),
                   hrm_location_id: $("#location").val(),
                   department_id : department_id,
                   category_id   : category_id,
                   allow_ot    : $("#allow_ot").val(),
                   section_id    : section_id,
                },

        dataType: 'json',
        success: function(data) {
          var dataSet = data.data;
            table = $('#designation_list_table').DataTable( {
              destroy:    true,
              paging:     false,
              searching:  false,
              ordering:   true,
              bInfo:      false,
              "data":     dataSet,

            "initComplete": function () {
                $(".allow_ot").inputmask("hh:mm", {
                  placeholder: "HH:MM",
                  insertMode: false,
                  showMaskOnHover: false,
                  hourFormat: "24"
                });

                $(".allow_ot").timepicker({
                  showMeridian:false,
                  showSeconds:false,
                  showInputs: false
                });



              },


              "columns": [

              { "data": "checkbox",
                      "mRender": function (data, type, full) {
                      $("#hrm_ot_process_master_id").val(full.hrm_ot_process_master_id);
                      return '<input type="checkbox" class="hello" name="id[]" value="'+full.id+'">';
              }
              },
              { "data": "employee_name" },
              { "data": "depertment_name" },
              { "data": "designation_name" },
              { "data": "section_name" },
              { "data": "text",
                      "mRender": function (data, type, full) {
                      return '<input type="text" class="allow_ot bootstrap-timepicker"   name="allow_ot['+full.id+']"  value="'+full.allow_ot+'">';

              }
              },
              ],
              order: [ 1, 'asc' ]
            });
        }
      });
    // }

    });

    $('#example-select-all').on('click', function(){
        var rows = table.rows({ 'search': 'applied' }).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);

        if ($('input.hello:checked').length > 0) {
            $('#submitBtn').prop('disabled', false)
        } else {
            $('#submitBtn').prop('disabled', true)
        }
    });


   $('#designation_list_table tbody').on('change', 'input.hello', function(){
        if(!this.checked) {
            var el = $('#example-select-all').get(0);
            if(el && el.checked && ('indeterminate' in el)){
                el.indeterminate = true;
            }
        }

        if ($('input.hello:checked').length > 0) {
            $('#submitBtn').prop('disabled', false)
        } else {
            $('#submitBtn').prop('disabled', true)
        }
   });



});

</script>
