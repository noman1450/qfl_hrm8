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
  </style>
@stop

@section('content')


  
  <div class="box box-default">
    <div class="box-body">
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          <li class="active">
			  	  <a href="#Asset_List" data-toggle="tab" aria-expanded="true">
				      Job Requsitions
				    </a>
			    </li>
        </ul>

        <div class="tab-content">
          <div class="tab-pane active" id="Asset_List">
            <div class="box-body">
              <div class="row">
                <div class="box-header with-border">
                	<a href="{{ route('cv-job-requsitions.create') }}" class="btn-success btn btn-sm button pull-left" style="font-size: 12px; font-weight: bold">Add Job Requsition</a>
                </div>
                </div>
                
                <div class="row">
                  <div class="col-md-12"> 
                    <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow:auto;">   	
                      <table id="jobRequsitionDatatable" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                          <th style="width: 12%">Job Title</th>
                          <th style="width: 8%">Publish Date</th>
                          <th style="width: 8%">Deadline</th>
                          <th style="width: 15%">Job Context</th>
                          <th style="width: 5%">Vacancy</th>
                          <th style="width: 5%">Salary</th>
                          <th style="width: 5%">Age</th>
                          <th style="width: 8%">Status</th>
                          <th style="width: 10%">Actions</th>
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
        </div>
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

            <form action="{{ route('approved-reject') }}" method="post">
              {{ csrf_field() }}

              <div class="modal-body" id="showDetaildModalBody"></div>

              <div class="modal-footer">

                <span class="buttonHide">
                  <button type="submit" name="status" value="2" class="btn btn-primary">Approve</button>
                  <button type="submit" name="status" value="0" class="btn btn-danger">Reject</button>
                </span>                  

                <a data-dismiss="modal" class="btn btn-default" style="margin-left: 5px" href="#">Close</a>
              </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('script')
  <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
  <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

  <script>

	// Call the datatable..
	$('#jobRequsitionDatatable').DataTable({
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
        url: "{{ route('job-requsitions.datatableList') }}",
        type: "POST",
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}, 
        dataType: "json",
      },
      columns: [                                     
        { data: "job_title" },
        { data: "published_date" },
        { data: "ending_date" },
        { data: "job_context" },
        { data: "vacancy" },
        { data: "salary_range" },
        { data: "age_range" },
        { data: "Status" },
        { data: "Link" },
      ],
      order: [[0, 'desc']]
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