<style>
    table#requsitionDetailsTable > tbody > tr:not(:first-child) > td:not(:first-child) {
        text-align: center;
    }
    .select2-container .select2-search--inline .select2-search__field {
        padding: 0 10px !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #3c8dbc;
        border-color: #367fa9;
        padding: 1px 10px;
        color: #fff;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        margin-right: 5px;
        color: rgba(255,255,255,0.7);
    }
</style>



<form class="dynamicFormSubmit" redirectUrl="{{ route('cv-job-requsitions.index') }}" action="{{ is_null($jobRequsition) ? route('cv-job-requsitions.store') : route('cv-job-requsitions.update', $jobRequsition) }}" method="post" enctype="multipart/form-data">
    {{ csrf_field() }}

    @if(!is_null($jobRequsition)) {{ method_field('patch') }} @endif


 <h4 class="frmMsg"></h4>

    <div class="box-body" style="padding-left: 20px;padding-right: 20px">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="hrm_depertment_id">Department <span class="text-danger">*</span></label>
                            <select id="hrm_depertment_id" name="hrm_depertment_id" class="form-control required hrm_depertment_id" style="width: 100%">
                                @if (!is_null($jobRequsition))
                                    <option value="{{ $jobRequsition->hrm_depertment_id }}">{{ $jobRequsition->deparment->depertment_name }}</option>
                                @endif
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="hrm_designation_id">Designation <span class="text-danger">*</span></label>
                            <select id="hrm_designation_id" name="hrm_designation_id" class="form-control required hrm_designation_id" style="width: 100%">
                                @if (!is_null($jobRequsition))
                                    <option value="{{ $jobRequsition->hrm_designation_id }}">{{ $jobRequsition->designation->designation_name }}</option>
                                @endif
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                </div>

                <input type="text" id="allow_points_calculation"  hidden="">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="job_title">Job Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control required" value="{{ old('job_title') ?? (!is_null($jobRequsition) ? $jobRequsition->job_title : null) }}" id="job_title" name="job_title" placeholder="Job Title..">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="vacancy">Number of vacancy <span class="text-danger">*</span></label>
                            <input type="text" class="form-control required" value="{{ old('vacancy') ?? (!is_null($jobRequsition) ? $jobRequsition->vacancy : null) }}" id="vacancy" name="vacancy" placeholder="Number of vacancy..">
                        </div>
                    </div>
                </div>
                

                <div class="form-group">
                    <label for="job_context">Job Context <span class="text-danger">*</span></label>
                    <input type="text" class="form-control required" value="{{ old('job_context') ?? (!is_null($jobRequsition) ? $jobRequsition->job_context : null) }}" id="job_context" maxlength="350" name="job_context" placeholder="Job Context..">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="employment_status">Employment Status <span class="text-danger">*</span></label>
                            <select class="form-control required" name="employment_status">
                                <option {{ !is_null($jobRequsition) ? ($jobRequsition->employment_status === 'Full Time' ? 'selected' : null) : null }}>Full Time</option>
                                <option {{ !is_null($jobRequsition) ? ($jobRequsition->employment_status === 'Part Time' ? 'selected' : null) : null }}>Part Time</option>
                                <option {{ !is_null($jobRequsition) ? ($jobRequsition->employment_status === 'Contractual' ? 'selected' : null) : null }}>Contractual</option>
                                <option {{ !is_null($jobRequsition) ? ($jobRequsition->employment_status === 'Internship' ? 'selected' : null) : null }}>Internship</option>
                                <option {{ !is_null($jobRequsition) ? ($jobRequsition->employment_status === 'Freelance' ? 'selected' : null) : null }}>Freelance</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="job_level">Job Level <span class="text-danger">*</span></label>
                            <select class="form-control required" name="job_level">
                                <option {{ !is_null($jobRequsition) ? ($jobRequsition->job_level === 'Entry' ? 'selected' : null) : null }}>Entry</option>
                                <option {{ !is_null($jobRequsition) ? ($jobRequsition->job_level === 'Mid' ? 'selected' : null) : null }}>Mid</option>
                                <option {{ !is_null($jobRequsition) ? ($jobRequsition->job_level === 'Top' ? 'selected' : null) : null }}>Top</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">                    
                        <div class="form-group">
                            <label for="work_place">Work Place <span class="text-danger">*</span></label>
                            <select class="form-control required" name="work_place">
                                <option {{ !is_null($jobRequsition) ? ($jobRequsition->work_place === 'N/A' ? 'selected' : null) : null }}>N/A</option>
                                <option {{ !is_null($jobRequsition) ? ($jobRequsition->work_place === 'Work at office' ? 'selected' : null) : null }}>Work at office</option>
                                <option {{ !is_null($jobRequsition) ? ($jobRequsition->work_place === 'Work from home' ? 'selected' : null) : null }}>Work from home</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="job_location">Job Location <span class="text-danger">*</span></label>
                            <select class="form-control required" name="job_location">
                                <option {{ !is_null($jobRequsition) ? ($jobRequsition->job_location === 'Head Office' ? 'selected' : null) : null }}>Head Office</option>
                                <option {{ !is_null($jobRequsition) ? ($jobRequsition->job_location === 'Factory' ? 'selected' : null) : null }}>Factory</option>
                                <option {{ !is_null($jobRequsition) ? ($jobRequsition->job_location === 'Anywhere in Bangladesh' ? 'selected' : null) : null }}>Anywhere in Bangladesh</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">                    
                        <div class="form-group">
                            <label for="lunch_facilities">Lunch facilities <span class="text-danger">*</span></label>
                            <select class="form-control required" name="lunch_facilities">
                                <option {{ !is_null($jobRequsition) ? ($jobRequsition->lunch_facilities === 'N/A' ? 'selected' : null) : null }}>N/A</option>
                                <option {{ !is_null($jobRequsition) ? ($jobRequsition->lunch_facilities === 'Full subsidize' ? 'selected' : null) : null }}>Full subsidize</option>
                                <option {{ !is_null($jobRequsition) ? ($jobRequsition->lunch_facilities === 'Partially subsidize' ? 'selected' : null) : null }}>Partially subsidize</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="salary_review">Salary Review <span class="text-danger">*</span></label>
                            <select class="form-control required" name="salary_review">
                                <option {{ !is_null($jobRequsition) ? ($jobRequsition->salary_review === 'N/A' ? 'selected' : null) : null }}>N/A</option>
                                <option {{ !is_null($jobRequsition) ? ($jobRequsition->salary_review === 'Half year' ? 'selected' : null) : null }}>Half year</option>
                                <option {{ !is_null($jobRequsition) ? ($jobRequsition->salary_review === 'Yearly' ? 'selected' : null) : null }}>Yearly</option>                                
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">                    
                        <div class="form-group">
                            <label for="festival_bonus">Festival bonus <span class="text-danger">*</span></label>
                            <select class="form-control required" name="festival_bonus">
                                <option {{ !is_null($jobRequsition) ? ($jobRequsition->festival_bonus === 'N/A' ? 'selected' : null) : null }}>N/A</option>
                                <option {{ !is_null($jobRequsition) ? ($jobRequsition->festival_bonus === '1' ? 'selected' : null) : null }}>1</option>
                                <option {{ !is_null($jobRequsition) ? ($jobRequsition->festival_bonus === '2' ? 'selected' : null) : null }}>2</option>
                                <option {{ !is_null($jobRequsition) ? ($jobRequsition->festival_bonus === '3' ? 'selected' : null) : null }}>3</option>
                                <option {{ !is_null($jobRequsition) ? ($jobRequsition->festival_bonus === '4' ? 'selected' : null) : null }}>4</option>
                                <option {{ !is_null($jobRequsition) ? ($jobRequsition->festival_bonus === '5' ? 'selected' : null) : null }}>5</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="gender">Gender <span class="text-danger">*</span></label>
                            <select class="form-control required" name="gender">
                                <option {{ !is_null($jobRequsition) ? ($jobRequsition->gender === 'Male' ? 'selected' : null) : null }}>Male</option>
                                <option {{ !is_null($jobRequsition) ? ($jobRequsition->gender === 'Female' ? 'selected' : null) : null }}>Female</option>                             
                                <option {{ !is_null($jobRequsition) ? ($jobRequsition->gender === 'Male & Female' ? 'selected' : null) : null }}>Male & Female</option>                             
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="published_date">Published Date <span class="text-danger">*</span></label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
        
                                <input type="text" class="form-control required datepicker" id="published_date" name="published_date"  value="{{ old('published_date') ?? (!is_null($jobRequsition) ? date('d-m-Y', strtotime($jobRequsition->published_date)) : date('d-m-Y')) }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ending_date">Application Deadline <span class="text-danger">*</span></label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
        
                                <input type="text" class="form-control required datepicker" id="ending_date" name="ending_date" value="{{ old('ending_date') ?? (!is_null($jobRequsition) ? date('d-m-Y', strtotime($jobRequsition->ending_date)) : date('d-m-Y')) }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="salary_range">Salary Range <span class="text-danger">*</span></label>
                            <input type="text" class="form-control required" value="{{ old('salary_range') ?? (!is_null($jobRequsition) ? $jobRequsition->salary_range : null) }}" id="salary_range" name="salary_range" placeholder="Salary Range..">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="age_range">Age Range <span class="text-danger">*</span></label>
                            <input type="text" class="form-control required" value="{{ old('age_range') ?? (!is_null($jobRequsition) ? $jobRequsition->age_range : null) }}" id="age_range" name="age_range" placeholder="Age Range..">
                        </div>
                    </div>
                </div>                
            </div>        

            <div class="col-md-6">

                <div class="form-group hidden">
                    <label for="is_active">Is Active</label>
                    <select id="is_active" class="form-control" name="is_active">
                        <option value="1" {{ !is_null($jobRequsition) ? ($jobRequsition->is_active === 1 ? 'selected' : null) : null }}>Active</option>
                        <option value="0" {{ !is_null($jobRequsition) ? ($jobRequsition->is_active === 0 ? 'selected' : null) : null }}>Inactive</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 25px">
                    <label for="hrm_cv_compensation_id">Compensation (optional)</label>
                    <select id="hrm_cv_compensation_id" name="hrm_cv_compensation_id[]" multiple class="form-control ooh_problem_types_id">
                        @if(!is_null($jobRequsition))
                            @foreach ($compensations as $compensation)
                                <option value="{{ $compensation->id }}" selected>{{ $compensation->compensation_name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>




                @if ($errors->has('hrm_cv_job_description_id.0'))
                    <span class="help-block">
                        <p class="text-danger">{{ $errors->first('hrm_cv_job_description_id.0') }}</p>
                    </span>
                @endif

                @if ($errors->has('point.0'))
                    <span class="help-block">
                        <p class="text-danger">{{ $errors->first('point.0') }}</p>
                    </span>
                @endif

                <div class="row" style="margin-top: -10px">
                    <div>
                        <div style="text-align:left;width:100%;padding-left:15px">
                            <label style="color: red;">Points must be fillup with 100 marks **</label>
                        </div>
                    </div>
                </div>

             
                <div class="col-12" style="width: 100%; padding-right: 0;padding-left: 0">
                    <table id="requsitionDetailsTable" class="table table-bordered table-hover">
                        <thead class="bg-info">
                            <tr>
                                <th style="width: 70%">Job Description</th>
                                <th style="width: 20%">Point</th>
                                <th style="width: 10%">Action</th>
                            </tr>

                            <tr>
                                <th>
                                    <select class="form-control jobDescription" id="jobDescription" style="width: 100%">
                                    </select>
                                </th>
                                <th>
                                    <input type="number" class="form-control" id="point" placeholder="Point.." style="width:100%;text-align:center;">
                                </th>
                                <th>
                                    <button type="button" id="addMore" class="btn btn-primary btn-block btn-flat">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody> 

                            @if (!is_null($jobRequsition))
                                @foreach ($jobRequsitionDetails as $product)
                                    <tr>
                                        <th>
                                            <select class="form-control jobDescription" name="hrm_cv_job_description_id[]">
                                                <option value="{{ $product->hrm_cv_job_description_id }}" selected>{{ $product->jobDescription->group->job_description_group_name}} | {{ $product->jobDescription->job_description }}</option>
                                            </select>
                                        </th>

                                        @if($product->jobDescription->allow_points_calculation==1)
                                            <th>
                                                <input type="text" class="form-control totalPoint"   name="point[]" value="{{ $product->point }}" placeholder="Point.." style="width: 100%;text-align: center; "  >
                                            </th>
                                        @else
                                            <th>
                                                <input type="text" class="form-control totalPoint"   name="point[]" value="{{ $product->point }}" placeholder="Point.." style="width: 100%;text-align: center;color: red; "  readonly>
                                            </th>

                                        @endif



                                        <th>
                                            <button type="button" class="btn btn-danger btn-block btn-flat delete-button"><i class="fa fa-trash-o"></i> Del</button>
                                        </th>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>

                        <tfoot>
                            <tr>
                                <th class="text-right"></th>
                                <th class="text-center"></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="box-footer" style="padding-right: 0">
            <button type="submit" class="btn btn-primary pull-right" id="submitButton" disabled>Submit</button>
        </div>
    </div>
    <!-- /.box-body -->

</form>

@section('script')
  <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
  <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
  <script src="{{asset('utility.js')}}"></script>

  <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>

  <script>
    $(document).ready(function($) {
        var totalcalculat;

        var requsitionDetailsTable = $('#requsitionDetailsTable').DataTable({
            "paging":       false,
            "lengthChange": true,
            "searching":    false,
            "ordering":     false,
            "info":         false,
            "autoWidth":    false,
            "width":        "100%",
            footerCallback: function(row, data, start, end, display) {
                api = this.api(), data;
            },

        });

        // var point100 = 100;

        // totalCalculate = function () {

        //     totalPoint = 0;

        //     $(".totalPoint").each(function() {

        //         if (!isNaN(this.value) && this.value.length != 0) {
        //             totalPoint += parseInt(this.value);                  
        //         }

        //         if (totalPoint != point100) {
        //             $('#submitButton').prop('disabled', true)
        //         } else {
        //             $('#submitButton').prop('disabled', false)
        //         }

        //         if (point100 < totalPoint) {
        //             $('#submitButton').prop('disabled', true)
        //             alert('You can not add more then 100')
        //             $(this).find('td:eq(1)').find("input[name='point[]']").val(0);
        //         }
        //     });

        //     $(api.column(0).footer()).html("Total");
        //     $(api.column(1).footer()).html(totalPoint);
        // };
        



        totalCalculate = function(){
           
            totalPoint = 0;
            var point100 = 100;

              $(".totalPoint").each(function () {
              
                    //add only if the value is number
                    if (!isNaN(this.value) && this.value.length != 0) {
                        totalPoint += parseFloat(this.value);
                    }

                    if (totalPoint != point100) {
                        $('#submitButton').prop('disabled', true)
                    } else {
                        $('#submitButton').prop('disabled', false)
                    }

                    if (point100 < totalPoint) {
                        $('#submitButton').prop('disabled', true)
                        alert('You can not add more then 100')
                        $(this).find('td:eq(1)').find("input[name='point[]']").val(0);
                    }

              });   


            $(api.column(0).footer()).html("Total");
            $(api.column(1).footer()).html(totalPoint);        
        };  

        totalCalculate();




        $('#requsitionDetailsTable tbody').on( 'keyup', 'tr', function () {
            totalCalculate()
        })


       var data = [];
        $('#addMore').click(function(event) {
            event.preventDefault();

            jobDescription = $("#jobDescription").val();
            point = $('#point').val();
            allow_points = $('#allow_points_calculation').val();

            if(isBlank(jobDescription)){
                alert("Job Description can not be empty");
                return;
            }

            if (isBlank(point)){
                alert("Point can not be empty");
                return;
            }

            // console.log(allow_points);

            var pointinputtextbox ;
            if(allow_points==1){
                // console.log('YES');
                   pointinputtextbox= `<input type="text" class="form-control totalPoint"  style="width:100%;text-align:center;" name="point[]" value="${point}">`;
               }else{
                // console.log('NO');

                   pointinputtextbox= `<input type="text" class="form-control totalPoint "  style="width:100%;text-align:center;color:red;" name="point[]" value="${point}" readonly>`;
            }

            // console.log(pointinputtextbox);

            var entry = [
                $("#jobDescription option:selected").text(),
                pointinputtextbox,
                `<button type="button" class="btn btn-danger btn-block btn-flat delete-button"><i class="fa fa-trash-o"></i> Del</button>
                 <input type="hidden" name="hrm_cv_job_description_id[]" value="${$("#jobDescription option:selected").val()}">`,
                 jobDescription,

            ];


            var product       = entry[3];

            var booleanValue  = false;

            if(data.length >= 1){
              for(i=0; i<data.length; i++){
                if(data[i][3] == product){
                  booleanValue = true;
                }
              }
            }


            if(booleanValue){
              alert("This Job Description has already been added");
              return;
            }

            data.push(entry);  

            requsitionDetailsTable.row.add(entry).draw(false);

            $("#jobDescription").text(""),
            $("#jobDescription").val(""),
            $("#point").val("");

            totalCalculate()
        });

        // $('#requsitionDetailsTable tbody').on( 'click', '.delete-button', function () {
        //     requsitionDetailsTable.row($(this).parents('tr')).remove().draw();
        //     totalCalculate()
        // });


        $('#requsitionDetailsTable tbody').on('click', '.delete-button', function () {
            var index = requsitionDetailsTable
            .row( $(this).parents('tr') )
            .index();
                //remove index from data
                data.splice(index,1);
                requsitionDetailsTable
                .row( $(this).parents('tr') )
                .remove()
                .draw();

            totalCalculate();
        });



        $('#published_date').datepicker({
            autoclose: true,
            format: 'dd-mm-yyyy',
            orientation: "auto",
        });
        $('#ending_date').datepicker({
            autoclose: true,
            format: 'dd-mm-yyyy',
            orientation: "auto",
        });

        var departmentName;
        var $department = $('#hrm_depertment_id').select2({
            placeholder: 'Search Department',
            allowClear: true,
            ajax: {
                dataType: 'json',
                url: '{{ URL::to('/') }}/depertment_list_data',
                headers:{ 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
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
        $department.on("select2:select", function (e) {
            departmentName = $(this).select2('data')['0']['text'];
            //jobdescriptionDataLoad();

          
        })

        var $designation = $('#hrm_designation_id').select2({
            placeholder: 'Search Designation',
            allowClear: true,
            ajax: {
                dataType: 'json',
                url: '{{ URL::to('/') }}/designation_list_data',
                headers:{ 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
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

        $designation.on("select2:select", function (e) {
            var designationName = $(this).select2('data')['0']['text'];
            $("#job_title").val(designationName+' ('+departmentName+')');
        })

        var $jobDescription =  $('#jobDescription').select2({
            placeholder: 'Search Description',
            allowClear: true,
            width: '100%',
            ajax: {
                dataType: 'json', 
                url: '{{ URL::to('/') }}/job_desctiption_list_data',
                headers:{ 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                // delay: 250,
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

        $jobDescription.on("select2:select", function (e) {
            var allow_points = $(this).select2('data')['0']['allow_points_calculation'];

            if(allow_points==0){
                $("#point").val(0);
                $("#allow_points_calculation").val(allow_points);
                $("#point").prop("readonly", true);
            }else{
                $("#point").val(10);
                $("#point").prop("readonly", false);
                $("#allow_points_calculation").val(allow_points);

            }

        })




        

       function jobdescriptionDataLoad() {
        var table = $('#requsitionDetailsTable').DataTable( {
            "destroy":      true,
            "paging":       false,
            "lengthChange": true,
            "searching":    false,
            "ordering":     false,
            "info":         false,
            "autoWidth":    false,
            "width":        "100%",
           "ajax": {
               "url": "{{URL::to('/')}}/dept_wise_job_description",
               "type": "POST",
               "headers":{
                   'X-CSRF-TOKEN': '{{ csrf_token() }}'
                   },  
               "data":   {
                   hrm_depertment_id: $("#hrm_depertment_id").val(),
                }                
           },

           "columns": [
             { "data": "job_description" },
             { "data": "job_description" },
             { "data": "job_description" },
           ],
           "order": [[0, 'asc']]
         });
       };

       $('#hrm_cv_compensation_id').select2({
            placeholder: 'Search',
            allowClear: true,
            ajax: {
                dataType: 'json',
                url: '{{ URL::to('/') }}/compensation_drop_list',
                headers:{ 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
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

<script>
  $(document).on('submit', '.dynamicFormSubmit', function (e) {
        e.preventDefault();
        var isValid = 0;
        $('.required').each(function () {
            $(this).keyup(function () {
                $(this).css("border", "1px solid #ccc");
            });
            $(this).change(function () {
                $(this).next('span').css("border", "1px solid #ccc");
            });
            if ($(this).val() == "") {
                $(this).css("border", "1px solid red");
                $(this).next('span').css("border", "1px solid red");
                $(this).next('.chosen-container').css("border", "1px solid red");
                isValid = 1;
                //return false;
            } else {
                $(this).css("border", "1px solid #ccc");
                $(this).next('span').css("border", "1px solid #ccc");
                $(this).next('.chosen-container').css("border", "1px solid #ccc");
            }
        });
        if (isValid == 0) {
            if (confirm("Are You Sure?")) {
                var postUrl = $('.dynamicFormSubmit').attr('action');
                var redirectUrl = $('.dynamicFormSubmit').attr('redirectUrl');
                var formData = new FormData($('.dynamicFormSubmit')[0]);
                $(".submit-button").attr("disabled", true);
                e.preventDefault();
                //
                $.ajax({
                    type: "POST",
                    url: postUrl,
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    beforeSend: function () {
                        $(".loadingImg").html("<img src='/img/loader-small.gif' />");
                        //$(".loadingImg").html('<i class="fa fa-refresh fa-spin"></i>');
                    },
                    success: function (data) {
                        /*Check form Validation*/
                        if(data['error'] != ''){
                            errors = data['error'];
                            $.each(errors, function (i, error) {
                                var el = $(document).find('[name="'+i+'"]');
                                el.after($('<p style="color: red;">'+error[0]+'</p>'));
                            });
                        }
                        /*Show Message*/
                        var messageClass = data['status'] == 'Y' ? "success" : 'danger';
                        var messageType = data['status'] == 'Y' ? "Success" : 'Warning';
                        var message = data['message'];
                        $(".frmMsg").html("");
                        $(".frmMsg").html("<div class='alert alert-"+ messageClass + " alert-dismissible'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>"+ messageType +"!</strong> "+ message +"</div>");
                        $('html, body').animate({ scrollTop: 0 }, 'slow');
                        $(".loadingImg").html("");
                        //
                        if(data['status'] == 'Y'){
                            setTimeout(function(){// wait for 2 secs(2)
                                if (redirectUrl !== '' && typeof redirectUrl !== 'undefined') {
                                    window.location.href = redirectUrl;
                                } else {
                                    location.reload();
                                }
                            }, 100);
                        }else{
                            $(".submit-button").removeAttr("disabled");//--
                            setTimeout(function(){// wait for 2 secs(2)
                                $(".frmMsg").html("");
                            }, 1000);
                        }
                    }
                });
            } else {
                return false;
            }
        } else {
            return false;
        }
    });
</script>





@stop
