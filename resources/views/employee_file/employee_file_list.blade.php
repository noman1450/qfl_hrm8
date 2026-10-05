@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">

<style>
  .modal-full {
      min-width: 100%;
      margin: 0;
  }
  .modal-full .modal-content {
      min-height: 100vh;
  }
  img{
    width: 210px;
    height: 210px;
  }

</style>



@endsection

@section('content')



<div class="box box-default">
  <div class="box-header with-border">
    <h3 class="box-title">Employee  File Listddd </h3>

      {{-- @permission('DocumentArchiveCreateEditDelete') --}}
      <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
        <button type="button" class=" btn btn-sm button pull-left btn-flat btn-success" data-toggle="modal" data-target="#modal_create_file_type" style="font-size: 12px; font-weight: bold;">Create New</button>
      </div>
      {{-- @endpermission --}}

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>


  <div class="box-body">
    <div class="row">
        <div class="col-md-12">
            @if(session('error'))
                <div class="alert alert-danger"  id="errorAlert">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </div>
    <div class="row">



              <div class="form-group col-lg-12 col-md-12 col-xs-12">

                  <div class="col-lg-4 col-md-4 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;" >
                    <label class="class="control-label" >Employee Name</label>
                    <select style="width: 100%;" class="form-control select2" id="employee_id" name="employee_id" required readonly>
                        <option value="{{$employee_data[0]->id}}">{{$employee_data[0]->employee_name}}</option>
                    </select>
                  </div>

                  <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
                    <label class="class="control-label" >File Type
                    <span style="color:blue;font-size: 10px; "><input type="checkbox" name="date_range" value="1">Apply Date Range </span>
                    </label>
                    <select style="width: 100%;" class="form-control select2 filetype" id="file_type" name="file_type" required>
                    </select>
                  </div>

                  <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
                  <label>Date From</label>
                          <div class="input-group date">
                              <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                              </div>
                              <input type="text" class="form-control pull-right onchange" id="date_from" name="date_from" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('date_from') }}" required readonly>
                          </div>
                  </div>

                  <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
                  <label>Date To</label>

                          <div class="input-group date">
                              <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                              </div>
                              <input type="text" class="form-control pull-right onchange" id="date_to" name="date_to" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('date_to') }}" required readonly>
                          </div>
                  </div>

                <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="padding-left: 0px; padding-top: 35px;">
                    <a href="{{ URL::to('/document_download/' . $employee_data[0]->id) }}"  class="btn btn-success btn-sm btn-flat">
                        <span class="glyphicon glyphicon-download-alt"></span> Download
                    </a>
                </div>



              </div>

              <div class="box-body">
                <div class="row">
                  <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">
                    <table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <!-- <th></th> -->
                          <!-- <th style="width: 20%">Employee Name</th> -->
                              <th style="width: 20%">Activity Date</th>
                              <th style="width: 20%">File Type</th>
                              <th style="width: 20%">File Title</th>
                              <th style="width: 20%">Note</th>
                              <th style="width: 10%">View</th>
                              <th style="width: 10%">Download</th>
                               @can('DocumentArchiveCreateEditDelete')
                              <th style="width: 10%">Edit</th>
                              <th style="width: 10%">Delete</th>
                              @endcan

                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

    </div>
  </div>

  <!--  Create New Salary Grade Modal-->
<div class="modal fade" id="modal_create_file_type" tabindex="-1" role="dialog" aria-hidden="true">
  <!-- <div class="modal-dialog modal-lg" style="width: 1300px;"> -->
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="groupAddLabel">Create {{$employee_data[0]->employee_name}} New File </h4>
      </div>

      <form  action="{{ URL::to('document_archive') }}" class="form-horizontal" method="post" enctype="multipart/form-data">

        {{ csrf_field() }}
        <div class="modal-body">

          <div class="row">

              <input type="hidden" name="status" value="2">

               <div class="col-md-12">

                      <label >Activity Date</label>
                      <div class="input-group date">
                          <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                          </div>
                          <input type="text" class="form-control pull-right" id="attached_date" name="attached_date" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('attached_date') }}" required readonly>
                      </div>

                </div>


              <div class="col-md-12">
                <label class="control-label">Employee Name</label>
                <select class="form-control" id="employee_name" name="employee_name"  required>
                   <option value="{{$employee_data[0]->id}}">{{$employee_data[0]->employee_name}}</option>
                </select>

              </div>


              <div class="col-md-12">
                <label class="control-label">File Type</label>
                <select class="form-control" id="file_types" name="file_type"  required>
                    @foreach($file_type as $keys)
                       <option value={{$keys->id}}>{{$keys->file_type_name}}</option>
                    @endforeach
                </select>
              </div>


              <div class="col-md-12">
                <label class="control-label">File Title</label>
                <input type="text" class="form-control" name="file_title" placeholder="File Title" value="{{ old('file_title') }}"  autofocus >
              </div>

              <div class="col-md-12">
                <label class="control-label">Note</label>
                <input type="text" class="form-control" name="note" placeholder="Note.." value="{{ old('note') }}"  autofocus >
              </div>

            <div class="col-md-12">
              <label class="control-label">Add File</label>

              <input type="file" name="image" />
              {{ csrf_field() }}
              <br/>
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




    <!-- modal -->
  <div id="load_file" class="modal animated bounceIn"  role="dialog" aria-labelledby="myModalLabel"
  aria-hidden="true">

      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close" >
                  <span aria-hidden="true">×</span>
                  </button>
              </div>
              <div class="modal-body">
                  <div class="h_iframe" id="fileshow" style="text-align: center;">

                  </div>

              </div>
              <div class="modal-footer">
                  <button class="btn btn-secondary"
                  data-dismiss="modal">
                  close
                  </button>

              </div>
          </div>
      </div>
  </div>
    <!-- modal -->


</div>

@endsection



@section('script')


<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>


<script>
$(document).ready(function($) {

  $('#attached_date').datepicker({
      autoclose: true
  });

    setTimeout(function () {
        $('#errorAlert').fadeOut('slow', function () {
            $(this).alert('close'); // Bootstrap close
        });
    }, 2000);

  $('#list_table').on('click', '.showme', function(e){
    event.preventDefault();
      $.ajax({
          type: 'GET',
          url : '{{URL::to('/')}}/document_archive/'+$(this).data('hrm_employee_file_id')+'/view',
          success : function (data) {
            console.log(data);
            $("#fileshow").html(data);
          }
      });
    // $("#load_file").animatedModal();
    $('#load_file').modal('show');
  });


  $role= $('#file_type').select2({
        placeholder: 'Enter File Type',
        allowClear: true,
        ajax: {
          dataType: 'json',
          url: '{{URL::to('/')}}/file_type_list',
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
        $('#file_type').val(null).trigger("change");
        dataLoad();
    });


    $('#date_from').datepicker({
      autoclose: true
    });

    $('#date_to').datepicker({
      autoclose: true
    });

    $(".onchange").change(function(){
        dataLoad();
    });



    dataLoad = function(){

      if ($("#file_type").val() == null){
        file_type = 0;
      }else{
        file_type = $("#file_type").val();
      }

      if ($("#employee_id").val() == null){
        employee_id = 0;
      }else{
        employee_id = $("#employee_id").val();
      }


      $.ajax({
        type:   'POST',
        url :   "{{URL::to('/')}}/employeewisefile_list",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
        data:   {

            file_type: file_type,
            employee_id:employee_id,
            date_range:$('input[name=date_range]:checked').val(),
            date_from :$("#date_from").val(),
            date_to   :$("#date_to").val(),

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

              // { "data": "Link",
              //   "mRender": function (data, type, full) {
              //     return '<img src="{{asset('employee_image')}}/'+full.Images+'" style="height:30px; width:30px; border-radius: 30px;"/>';
              //   }
              // },
              // { "data": "Link",
              //   "mRender": function (data, type, full) {
              //       return '<a target="_blank" href="{{URL::to('/')}}/employeeinfo/'+full.id+'">'+full.employee_name+'</a>';
              //   }
              // },
              { "data": "attached_date" },
              { "data": "file_type_name" },
              { "data": "file_title" },
              { "data": "note" },

              { "data": "Link",
                "mRender": function (data, type, full) {
                return '<a data-hrm_employee_file_id="'+full.hrm_employee_file_id+'" class="btn btn-info btn-sm btn-flat showme"><span class="glyphicon glyphicon-resize-full"></span> View</a>';
                }
              },

              { "data": "Link",
                "mRender": function (data, type, full) {
                    return '<a href="{{URL::to('/')}}/document_archive/'+full.hrm_employee_file_id+'/download" class="btn btn-success btn-sm btn-flat"><span class="glyphicon glyphicon-download-alt"></span> Download</a>';
                }
              },

              @can('DocumentArchiveCreateEditDelete')

              { "data": "Link",
                "mRender": function (data, type, full) {
                    return '<a href="{{URL::to('/')}}/document_archive/'+full.hrm_employee_file_id+'/edit"> <span class="glyphicon glyphicon-edit"></span> Edit</a>';
                }
              },
              { "data": "Link",
                "mRender": function (data, type, full) {
                    return '<a href="{{URL::to('/')}}/document_archive/'+full.hrm_employee_file_id+'/cancel"  onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-sm btn-flat"><span class="glyphicon glyphicon-trash"></span> Delete</a>';
                }
              },
              @endcan

            ],
            "order": [[0,'asc']]
            });
        }
      });



}

$('input[type=checkbox][name=date_range]').change(function() {
      dataLoad();
});
dataLoad();

});

</script>

@endsection

