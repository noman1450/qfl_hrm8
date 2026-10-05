@extends('layouts.main')

@section('styles')
  <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
  <style>

  body {font-family: Arial;}

  /* Style the tab */
  .tab {
    overflow: hidden;
    border: 1px solid #ccc;
    background-color: #f1f1f1;
  }

  /* Style the buttons inside the tab */
  .tab button {
    background-color: inherit;
    float: left;
    border: none;
    outline: none;
    cursor: pointer;
    padding: 14px 16px;
    transition: 0.3s;
    font-size: 17px;
  }

  /* Change background color of buttons on hover */
  .tab button:hover {
    background-color: #ddd;
  }

  /* Create an active/current tablink class */
  .tab button.active {
    background-color: #ccc;
  }

  /* Style the tab content */
  .tabcontent {
    display: none;
    padding: 6px 12px;
    border: 1px solid #ccc;
    border-top: none;
  }
  .kv-avatar .krajee-default.file-preview-frame,.kv-avatar .krajee-default.file-preview-frame:hover {
    margin: 0;
    padding: 0;
    border: none;
    box-shadow: none;
    text-align: center;
  }
  .kv-avatar {
      display: inline-block;
  }
  .kv-avatar .file-input {
      display: table-cell;
      width: 213px;
  }
  .kv-reqd {
      color: red;
      font-family: monospace;
      font-weight: normal;
  }
  .btn-secondary {
    margin-top: 5px;
  }
  .btn-file{
    margin-top: 5px;	
  }


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
@stop

@section('content')
  
  <div class="box box-default">
    <div class="box-body">
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          <li class="active" >
            <a href="#Asset_List" data-toggle="tab" aria-expanded="true"  onclick='return check(1)'>
              CV Drop List
            </a>
          </li>

          <li >
            <a href="#InterviewList" data-toggle="tab" aria-expanded="true" onclick='return check(2)'>
              Interview List
            </a>
          </li>

          <li >
            <a href="#ReserveList" data-toggle="tab" aria-expanded="true" onclick='return check(3)'>
              Reserve List
            </a>
          </li>


        </ul>

      <div class="tab-content">
          <div class="tab-pane active" id="Asset_List">
                <h4 style="color: red;">Pending List</h4>
          </div>
          <div class="tab-pane " id="InterviewList">
                <h4 style="color: red;">Interview List</h4>
          </div>
          <div class="tab-pane " id="ReserveList">
                <h4 style="color: red;">Reserve List</h4>
          </div>
      </div>





      <div class="row" style="margin-left:10px; ">
          

          <div class="col-lg-3 col-md-3 col-xs-12 form-group" >  
              <label>Position Name</label>
              <select class="form-control position_id" id="position_id" name="position_id" style="width: 100%;" >
              </select>                 
          </div>

          <div class="col-lg-3 col-md-3 col-xs-12 form-group" >  
              <input type="radio"  name="point_type" value=">=" checked="checked">
              <label  style="color: green;">Search Upper Points</label>
              <input type="radio" name="point_type" value="<=">
              <label  style="color: red;">Search Lower Points</label>
              <input type="Number" placeholder="Input Points" id="points" name="points" class="form-control" oninput="myFunction()">               
          </div>

        </div>




          
            <div class="box-body">
                <div class="row">
                  <div class="col-md-12"> 
                    <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow:auto;">   	
                      <table id="cvDropListDatatable" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                          <th>#</th>
                          <th style="width: 15%">Position For</th>
                          <th style="width: 7%">Apply Date</th>
                          <th style="width: 12%">Published & Dadeline</th>
                          <th style="width: 10%">Applicant</th>
                          <th style="width: 10%">Contact</th>
                          <th style="width: 10%">Email</th>
                          <th style="width: 12%">Present Address</th>
                          <th style="width: 8%;color: red;">Gain Points</th>
                          <th style="width: 15%">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
<!-- 
          <div class="tab-pane" id="InterviewList">
            <div class="box-body">
                <div class="row">
                  <div class="col-md-12"> 
                    <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow:auto;">     
                      <table id="InterviewListDatatable" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                          <th>#</th>
                          <th style="width: 15%">Position For</th>
                          <th style="width: 7%">Apply Date</th>
                          <th style="width: 12%">Published & Dadeline</th>
                          <th style="width: 10%">Applicant</th>
                          <th style="width: 10%">Contact</th>
                          <th style="width: 10%">Email</th>
                          <th style="width: 12%">Present Address</th>
                          <th style="width: 8%;color: red;">Gain Points</th>
                          <th style="width: 15%">Actions</th>
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


          <div class="tab-pane" id="ReserveList">
            <div class="box-body">
                <div class="row">
                  <div class="col-md-12"> 
                    <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow:auto;">     
                      <table id="ReserveListDatatable" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                          <th>#</th>
                          <th style="width: 15%">Position For</th>
                          <th style="width: 7%">Apply Date</th>
                          <th style="width: 12%">Published & Dadeline</th>
                          <th style="width: 10%">Applicant</th>
                          <th style="width: 10%">Contact</th>
                          <th style="width: 10%">Email</th>
                          <th style="width: 12%">Present Address</th>
                          <th style="width: 8%;color: red;">Gain Points</th>
                          <th style="width: 15%">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
          </div> -->




        <!-- </div> -->
      </div>
    </div>
  </div>


  <div class="modal fade" id="showDetaildModal" data-backdrop="static">
    <div id="modalSize" class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h3 class="modal-title" style="font-size: 18px" id="showDetaildModalTile"></h3>
            </div>
            <div class="modal-body" id="showDetaildModalBody"></div>
            <div class="modal-footer">
                <a data-dismiss="modal" class="btn btn-default" href="#">Close</a>
            </div>
        </div>
    </div>
</div>
@stop

@section('script')
  <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
  <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

  <script>

    var statusValue = 1;

    function check(id)
    {
        statusValue = id;
        dataLoad();
    }




    function format(d) {
      console.log(d);
      return '<table class="table table-bordered table-hover" cellspacing="0" width="100%">'+
            '<tr>'+
              '<td>'+"Job Description"+'</td>'+
              '<td>'+"Note"+'</td>'+
            '</tr>'+
            '<tr>'+
              '<td>'+d.job_description+'</td>'+
              '<td>'+d.note+'</td>'+
            '</tr>'+  
          '</table>';
    }


     $position= $('#position_id').select2({
          placeholder: 'Select Position Name',
          allowClear: true,
          ajax: {
            dataType: 'json',
            url: '{{URL::to('/')}}/cv_job_requsition_list_data',
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
    

    $position.on('select2:select', function (e) {
        dataLoad();
    });

    $position.on('select2:unselect', function (e) {
        $('#position_id').val(null).trigger("change");
        dataLoad();
    });

    function myFunction() {
      dataLoad();
    }


    $('input[type=radio][name=point_type]').change(function() {
        dataLoad();
    });





    function dataLoad(){
      $.ajax({
        type:   'POST', 
        url: "{{ route('cv-drop-list.datatableList') }}",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },        
        data:   {
                   status: statusValue,
                   position_id: $('#position_id').val(),
                   points: $('#points').val(),
                   point_type:$('input[name=point_type]:checked').val(),
                },          
        dataType: 'json',
        success: function(data) {
          var dataSet = data.data;
          
            table = $('#cvDropListDatatable').DataTable( {
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
              { data: "position" },
              { data: "cv_submission_date" },
              { data: "dateline" },
              { data: "applicant_name" },
              { data: "applicant_contact_no" },
              { data: "applicant_email" },
              { data: "present_address" },
              { data: "points" },
              { data: "Link" },
              ],
              // "order": [[1, 'asc']]
              order: [ 1, 'asc' ]
            });
        }
      }); 
    };
    dataLoad()

    $('#cvDropListDatatable tbody').on('click', 'td.details-control', function () {
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



  $(document).on("click", ".modalLink", function (e) {
    e.preventDefault();
    var modal_size = $(this).attr('data-modal-size');

    if (modal_size!=='' && typeof modal_size !== typeof undefined && modal_size !== false) {
        $("#modalSize").addClass(modal_size);
    } else{
        $("#modalSize").addClass('modal-md');
    }
    var title = $(this).attr('title');
    $("#showDetaildModalTile").text(title);
    //
    $.ajax({
        type: "GET",
        url: $(this).attr('href'),
        beforeSend: function () {
            $(".loadingImg").html("<img src='/img/loader-small.gif' />");
        },
        success: function (data) {

          $(".loadingImg").html("");
          $("#showDetaildModalBody").html(data);
          $("#showDetaildModal").modal('show');
        }
    });
  });
</script>
@stop