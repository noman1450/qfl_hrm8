@extends('layouts.main')
@section('styles')
<link rel="stylesheet" href="{{ asset('plugins/datepicker/datepicker3.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables/dataTables.bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2/select2.min.css') }}">
<style type="text/css">
    .no-wrap {
        white-space: nowrap;
    }
</style>
@endsection

@section('content')
<div class="box box-default">
    <div class="box-header with-border">
		<h3 class="box-title">Employee List</h3>

		<div class="box-tools pull-right">
            <button type="button" class="btn btn-sm btn-default btn-outline-default mb-1 pull-right" id="excelExport">
                <span>Excel</span>
            </button>
			{{-- <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button> --}}
		</div>
	</div>

    <div class="box-body">
        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <label for="filter_name_id">Filter Name</label>
                    <select id="filter_name_id" class="form-control"></select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label for="location_id">Location</label>
                    <select id="location_id" class="form-control">
                        @if (request()->has('location_id'))
                            <option value="{{ request()->location_id }}" selected>{{ request()->location_name }}</option>
                        @endif

                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label for="designation_id">Designation</label>
                    <select id="designation_id" class="form-control"></select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label for="department_id">Department</label>
                    <select id="department_id" class="form-control"></select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select id="category_id" class="form-control"></select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label for="section_id">Sub-Department</label>
                    <select id="section_id" class="form-control"></select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label for="employee_id">Employee Name</label>
                    <select id="employee_id" class="form-control"></select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label for="employee_type_id">Employee Type</label>
                    <select id="employee_type_id" class="form-control"></select>
                </div>
            </div>



            <div class="col-md-2">
                <div class="form-group">
                    <label for="date_range" style="font-size: 12px; font-weight: 500;">Apply Date Range</label>
                    <input type="checkbox" id="date_range" {{ request()->has('location_id') ? 'checked' : null }} name="date_range" value="1">

                    <label>Date From</label>
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control pull-right onchange date" name="date_from" data-date-format="dd-mm-yyyy" value="{{ request()->has('from_date') ? date('d-m-Y', strtotime(request()->from_date)) : date('d-m-Y') }}" readonly>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label>Date To</label>
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control pull-right onchange date" name="date_to" data-date-format="dd-mm-yyyy" value="{{ request()->has('to_date') ? date('d-m-Y', strtotime(request()->to_date)) : date('d-m-Y') }}" readonly>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="activity">Activity</label>
                    <select id="activity" class="form-control">
                        <option value="1">Active</option>
                        <option value="0">Resign Employee</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group" style="margin-top: 15px; margin-bottom: 10px;">
                    <label style="display:block">
                        Joining
                        <input type="radio" name="joining" value="joining_date" {{ request()->has('joining') ? 'checked' : null }}>
                    </label>
                    <label style="display:block">
                        Confirmation
                        <input type="radio" name="joining" value="confirmation_date">
                    </label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <label for="payment_mode_id">Payment Mode</label>
                    <select id="payment_mode_id" class="form-control">
                        <option value=""> Select Payment Mode</option>
                        <option value="1"> Cash</option>
                        <option value="2"> Bank</option>
                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label for="salary_grade">Salary Grade</label>
                    <select class="form-control" id="salary_grade" name="salary_grade" required autofocus>
                    </select>
                </div>
            </div>

            <div class="col-md-2" id="bank_div" style="display: none">
                <div class="form-group">
                    <label for="bank_id">Select Bank</label>
                    <select  class="form-control col-lg-12 filter" id="bank_id" name="bank_id" style="width: 100%;">
                    </select>
                </div>
            </div>


            <div class="col-md-1">
                <div class="form-group" style="margin-top: 25px">
                    <button type="button" id="filter" class="btn btn-info btn-block">Search</button>
                </div>
            </div>

            {{-- <div class="col-md-1">
                <div class="form-group" style="margin-top: 25px">
                    <button type="button" class="btn btn-success" id="excelExport">
                        <span>Excel</span>
                    </button>
                </div>
            </div> --}}
        </div>

        <table id="list_table" class="table table-bordered table-hover" style="width:100%">
            <thead>
                <tr>
                    <th>UniqueCode</th>
                    <th>EmployeeName</th>
                    <th>Emp.Code</th>
                    <th>Email</th>
                    <th>ContactNumber</th>

                    <th>OfficialEmail</th>
                    <th>OfficialContactNumber</th>

                    <th>JobPlacement</th>
                    <th>DepartmentName</th>
                    <th>DesignationName</th>
                    <th>JoiningDate</th>
                    <th>Salary Grade</th>
                    <th>ConfirmationDate</th>
                    <th>Jobduration</th>

                    <th>GrossSalary</th>
                    <th>Insurance</th>
                    <th>OvertimeStatus</th>
                    <th>CategoryName</th>
                    <th>SubDepartment</th>
                    <th>ManageByName</th>

                    <th>PermanentAddress</th>
                    <th>PresentAddress</th>
                    <th>Gender</th>
                    <th>DateOfBirth</th>
                    <th>Nid</th>
                    <th>Tin</th>
                    <th>FatherName</th>
                    <th>MotherName</th>

                    <th>Religion</th>
                    <th>MaritalStatus</th>
                    <th>BloodGroup</th>
                    <th>EducationName</th>
                    <th>PlantName</th>
                    <th>EmploymentStatus</th>
                    <th>ShiftName</th>
                    <th>Period</th>
                    <th>ActiveStatus</th>
                    <th>ResignDate</th>
                    <th>Payment Mode</th>
                    <th>Bank</th>
                    <th>Bank Account</th>
                    <th>Last Increment Date</th>
                    <th>Last Promotion Date</th>


                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('script')
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('dist/js/tableToExcel.js')}}"></script>

<script>
    $(document).ready(function(){

        $(document).on('change','#payment_mode_id',function(){
            if($(this).val() == 2){
                $("#bank_div").toggle(true)
            }else{
                selectAppendBank();
                $("#bank_div").toggle(false)
            }
        })

        const selectAppendBank = () => $("#bank_id").empty();

        $('#bank_id').select2({
            placeholder: 'Enter Bank',
            allowClear: true,
            ajax: {
            dataType: 'json',
            url: '{{URL::to('/')}}/get_bank_list',
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

        $('#list_table').DataTable({
            responsive: true,
            paging: true,
            ordering: true,
            searching: true
        });

        $(document).on('click', '#excelExport', function () {
            const dataTable = $('#list_table').DataTable();
            // Temporarily show all rows (to export full data)
            dataTable.page.len(-1).draw();

            let tableClone = $('#list_table').clone();
            // Remove DataTables-specific elements from the cloned table
            tableClone.find('thead th').removeClass('sorting sorting_asc sorting_desc');
            tableClone.find('tfoot').show(); // Ensure footer is visible, if used
            // Convert the cloned table to Excel
            TableToExcel.convert(tableClone[0], {
                name: `custom_employee_list.xlsx`,
                sheet: {
                    name: 'custom_employee_list'
                }
            });
            // Restore previous page length
            dataTable.page.len(10).draw(); // Adjust this if your page length is different
        });


    $('.date').datepicker({
        autoclose: true
    });

    $('.date_to').datepicker({
        autoclose: true
    });

    let requestData = {}
    var dataSet, table;

    $(document).on('click', '#filter', function() {
        requestData = {
            filter_name: $('#filter_name_id option:selected').val(),
            activity: $('#activity option:selected').val(),
            designation_id: $('#designation_id option:selected').val(),
            department_id: $('#department_id option:selected').val(),

            category_id: $('#category_id option:selected').val(),
            section_id: $('#section_id option:selected').val(),
            employee_id: $('#employee_id option:selected').val(),
            employee_type_id: $('#employee_type_id option:selected').val(),
            location_id: $('#location_id option:selected').val(),
            payment_mode_id: $('#payment_mode_id option:selected').val(),
            bank_id: $('#bank_id option:selected').val(),
            salary_grade: $('#salary_grade option:selected').val(),
        }

        if ($('#date_range').is(':checked')) {
            requestData.date_range = $('input[name=date_range]').val();
            requestData.date_from = $('input[name=date_from]').val();
            requestData.date_to = $('input[name=date_to]').val();
            requestData.joining = $('input[name="joining"]:checked').val();;
        }

        dataLoad();
    })

    select2Dropdown("#filter_name_id", "{{ url('/filter_name_list') }}", "Enter Filter Name");
    select2Dropdown("#designation_id", "{{ url('/designation_list_data') }}", "Enter Designation Name");
    select2Dropdown("#department_id", "{{ url('/depertment_list_data') }}", "Enter Department Name");

    select2Dropdown("#category_id", "{{ url('/category_list_data') }}", "Enter Category Name");
    select2Dropdown("#section_id", "{{ url('/section_list_data') }}", "Enter Sub-Department Name");
    select2Dropdown("#employee_id", "{{ url('/join_employee_list') }}", "Enter Employee Name");
    select2Dropdown("#employee_type_id", "{{ url('/employeestatus_list_data') }}", "Enter Employee Type");
    select2Dropdown("#location_id", "{{ url('/location_list_data') }}", "Enter Location Name");

    function dataLoad() {
        $.ajax({
            type: 'POST',
            url: "{{ url('/custom_employee_list_data') }}",
            dataType: 'json',
            data: requestData,

            beforeSend: function () {
                $("#list_table_processing").removeAttr('style');
            },

            success: function(data) {
                table = $('#list_table').DataTable({
                    "destroy":    true,
                    "processing": true,
                    // "serverSide": true,
                    "searching":  true,
                    "ordering":   true,
                    "bInfo":      true,
                    "paging":     false,
                    "scrollX":    true,
                    "scrollY":    'calc(100vh - 350px)',
                    "data":     data.data,
                    "columnDefs": [
                        { "className": "no-wrap", "targets":  "_all" } // 0 এবং 1 নং কলামের জন্য
                    ],
                    "columns": [
                        { "data": "Unique_Code" },
                        { "data": "employee_name" },
                        { "data": "employee_code" },
                        { "data": "email" },
                        { "data": "contact_number" },
                        { "data": "official_email" },
                        { "data": "official_contact_no" },
                        { "data": "job_placement" },
                        { "data": "department_name" },
                        { "data": "designation_name" },
                        { "data": "joining_date" },
                        { "data": "grade_name" },
                        { "data": "confirmation_date" },
                        { "data": "jobduration" },
                        { "data": "basic_salary" },
                        { "data": "insurance" },
                        { "data": "overtime_status" },
                        { "data": "category_name" },
                        { "data": "sub_department" },
                        { "data": "manage_by_name" },
                        { "data": "permanent_address" },
                        { "data": "present_address" },
                        { "data": "gender" },
                        { "data": "date_of_birth" },
                        { "data": "nid" },
                        { "data": "tin" },
                        { "data": "father_name" },
                        { "data": "mother_name" },
                        { "data": "religion" },
                        { "data": "marital_status" },
                        { "data": "blood_group" },
                        { "data": "education_name" },
                        { "data": "plant_name" },
                        { "data": "employment_status" },
                        { "data": "shift_name" },
                        { "data": "period" },
                        { "data": "active_status" },
                        { "data": "resign_date" },
                        { "data": "payment_mode" },
                        { "data": "bank_name" },
                        { "data": "account_no" },
                        { "data": "last_promotion_date" },
                        { "data": "last_increment_date" },
                    ],

                    "order": [0, 'asc']
                });

                $("#list_table_processing").css({ display: 'none' });
            }
        });
    }

    @if (request()->has('location_id'))
        $('#filter').trigger('click');
    @endif

    $(document).on('click', '.export-data', function(e) {
        e.preventDefault();

        var query = {
            filter_name_id: $('#filter_name_id option:selected').val(),
            designation_id: $('#designation_id option:selected').val(),
            department_id: $('#department_id option:selected').val(),
            activity: $('#activity option:selected').val(),

            category_id: $('#category_id option:selected').val(),
            section_id: $('#section_id option:selected').val(),
            employee_id: $('#employee_id option:selected').val(),
            employee_type_id: $('#employee_type_id option:selected').val(),
            location_id: $('#location_id option:selected').val(),
        };

        window.location = `${$(this).attr('href')}?${$.param(query)}`;
    });

    $('#salary_grade').select2({
        placeholder: 'Enter a Salary Grade',
        allowClear: true,
            ajax: {
                dataType: 'json',
                url: "{{URL::to('/')}}/salarygrade_list",
                delay: 250,
            data: function(params) {
                return {
                    term: params.term
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
})
</script>
@endsection
