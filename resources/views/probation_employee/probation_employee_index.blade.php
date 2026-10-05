<!-- active_employee_list -->
<!-- employee_list -->
@extends('layouts.main')

<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">


<!-- Buttons CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">



<style type="text/css">

.noflag
{
      background: url('{{URL::to('/')}}/dist/img/redflag.jpg') no-repeat center center;
      cursor: pointer;
      width: 80px;
      height: 25px;
      border:1px solid green;
      opacity: 0.1;

}

.noflag:hover
{
  /*background: url('{{URL::to('/')}}/dist/img/details_open.png') no-repeat center center;*/
  background: url('{{URL::to('/')}}/dist/img/redflag.jpg') no-repeat center center;
      cursor: pointer;
      width: 80px;
      height: 25px;
      border:1px solid red;
      opacity: 1;


}

.redflag{
      background: url('{{URL::to('/')}}/dist/img/redflag.jpg') no-repeat center center;
      cursor: pointer;
      width: 80px;
      height: 25px;
      border:1px solid white;
}

</style>


@endsection



<!-- content -->
@section('content')

    <div>
      <div id="alert-danger"></div>
      <div id="alert-success"></div>
    </div>



<div class="box box-default">
	<div class="box-header with-border">
		<h3 class="box-title">Probation Employee List</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>

	<div class="box-body">
        <div class="row">

            <div class="col-lg-2">
                <div class="form-group">
                    <label for="location">Location</label>
                    <select class="form-control" id="location" style="width: 100%;"  required>

                    </select>
                </div>
            </div>

            <div class="col-lg-2">
                <div class="form-group">
                    <label for="designation_id">Designation</label>
                    <select class="form-control" id="designation_id" style="width: 100%;"  required>

                    </select>
                </div>
            </div>

            <div class="col-lg-2">
                <div class="form-group">
                    <label for="department_id">Department</label>
                    <select class="form-control" id="department_id" style="width: 100%;"  required>

                    </select>
                </div>
            </div>

            <div class="col-lg-2">
                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select class="form-control" id="category_id" style="width: 100%;"  required>

                    </select>
                </div>
            </div>

            <div class="col-lg-2">
                <div class="form-group">
                    <label for="section_id">Sub-department</label>
                    <select class="form-control" id="section_id" style="width: 100%;"  required>

                    </select>
                </div>
            </div>


        </div>

		<div class="row">
	     <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">
				<table id="probation_employee_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
					<thead>
						<tr>
                            <th></th>
                            <th></th>
                            <th style="width: 5%"></th>
                            <th style="width: 22%">Employee Name</th>
                            <th style="width: 5%">Unique Code</th>
                            <th style="width: 15%">Designation</th>
                            <th style="width: 13%">Department</th>
                            <th style="width: 12%">Joining</th>
                            <th style="width: 12%">Probable Confirmation Date</th>
                            <th style="width: 12%">Remaining Days</th>
                            <th style="width: 10%">Contact</th>
                            <th style="width: 13%">Job Placement</th>
                            <th style="width: 05%">Action</th>
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
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>


<!-- Buttons JS -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>

<!-- JSZip (Excel Export এর জন্য প্রয়োজন) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<!-- Excel Export -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

<!-- (Optional) Print Button -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>


<script>


$(document).ready(function($) {

dataLoad = function(){

          var table = $('#probation_employee_table').DataTable( {
            "destroy":    true,
            "processing": true,
            "serverSide": true,
            "searching":  true,
            "ordering":   true,
            "bInfo":      true,
            "paging":     true,
            dom: 'lBfrtip',   // l = length menu, B = buttons, f = search, r = processing, t = table, i = info, p = pagination
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export Excel'
                }
            ],
            "aoColumnDefs": [{ "bVisible": false, "aTargets": [0,1] }],
            "ajax": {
                "url": "{{URL::to('/')}}/probation_employee_list",
                "type": "POST",
                "headers":{'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                "data":   {
                    location: $("#location").val(),
                    redflagStatus:$('input[name=redflagStatus]:checked').val(),
                    designation_id: $('#designation_id option:selected').val(),
                    department_id: $('#department_id option:selected').val(),
                    category_id: $('#category_id option:selected').val(),
                    section_id: $('#section_id option:selected').val(),
                    employee_type_id: $('#employee_type_id option:selected').val(),
              }
            },

            "columns": [
              { "data": "priority" },
              { "data": "employee_name" },
              {
                "render": function (data, type, JsonResultRow, meta) {
                    return '<img src="{{asset('employee_image')}}/'+JsonResultRow.Images+'" style="height:30px; width:30px; border-radius: 30px;"/>';
                }
              },
              { "data": "Link",
                "mRender": function (data, type, full) {
                    return '<a target="_blank"  href="{{URL::to('/')}}/employeeinfo/'+full.id+'">'+full.employee_name+'</a>';
                }
              },
              { "data": "Unique_Code" },
              { "data": "designation_name" },
              { "data": "depertment_name" },
              { "data": "joining_date" },
              { "data": "probable_date" },
              { "data": "RemainingDays" },
              { "data": "contact_number" },
              { "data": "location_name" },
              { "data": "Link", name: 'action', orderable: false, searchable: false},
            ],
            "order": [[0, 'asc']]
          });



        }



   dataLoad();

   $role= $('#location').select2({
      placeholder: 'Choose Location',
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

    $designation = select2Dropdown("#designation_id", "{{ url('/designation_list_data') }}", "Enter Designation Name");
    $department = select2Dropdown("#department_id", "{{ url('/depertment_list_data') }}", "Enter Department Name");
    $category = select2Dropdown("#category_id", "{{ url('/category_list_data') }}", "Enter Category Name");
    $section = select2Dropdown("#section_id", "{{ url('/section_list_data') }}", "Enter Sub-Department Name");
    $employee_type = select2Dropdown("#employee_type_id", "{{ url('/employeestatus_list_data') }}", "Enter Employee Type");

    $designation.on('select2:select', function (e) {
        dataLoad();
    });

    $designation.on('select2:unselect', function (e) {
        $('#designation_id').val(null).trigger("change");
        dataLoad();
    });

    $department.on('select2:select', function (e) {
        dataLoad();
    });

    $department.on('select2:unselect', function (e) {
        $('#department_id').val(null).trigger("change");
        dataLoad();
    });

    $category.on('select2:select', function (e) {
        dataLoad();
    });

    $category.on('select2:unselect', function (e) {
        $('#category_id').val(null).trigger("change");
        dataLoad();
    });

    $section.on('select2:select', function (e) {
        dataLoad();
    });

    $section.on('select2:unselect', function (e) {
        $('#section_id').val(null).trigger("change");
        dataLoad();
    });

    $employee_type.on('select2:select', function (e) {
        dataLoad();
    });

    $employee_type.on('select2:unselect', function (e) {
        $('#employee_type_id').val(null).trigger("change");
        dataLoad();
    });

    $('#list_table tbody').on('click','.requestredflag',function(){
        $(this).toggleClass('noflag redflag');

        var add_object = $(this).parents('tr');
        if(confirm('Do you want to submit?')){
        $.ajax({
            method: 'GET',
            url: '{{URL::to('/')}}/employeejoin/'+$(this).attr('id')+'/requestredflag',
            dataType: 'json',
            success: function(data) {
              if(data['success']) {
                var erreurs ='<div class="alert alert-success"><ul>';
                    erreurs += '<li>'+data.messages+'</li>';
                    erreurs += '</ul></div>';
                $('#alert-success').html(erreurs);
                $('#alert-success').show(0).delay(1000).hide(0);
              }else{
                  var erreurs ='<div class="alert alert-danger"><ul>';
                  $.each(data.errors, function(i,error){
                      erreurs += '<li>'+error+'</li>';
                  });
                  erreurs += '</ul></div>';
                  $('#alert-danger').html(erreurs);
                  $('#alert-danger').show(0).delay(1000).hide(0);
              }
            }
        });
        }else{
          return;
        }
    })
});
</script>

<script>
    $(document).ready(function () {
          $('input[type=radio][name=redflagStatus]').change(function() {
              dataLoad();
              // if (this.value == '1') {
              //     alert("Allot Thai Gayo Bhai");
              // }
              // else if (this.value == '2') {
              //     alert("Transfer Thai Gayo");
              // }
          });
    });
</script>
@endsection
