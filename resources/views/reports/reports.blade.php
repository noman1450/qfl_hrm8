@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
</script>
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

<style type="text/css">
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        color: black;
    }

</style>

@endsection
@section('content')


<div class="box box-primary">

    <div class="box-header with-border">
        <h3 class="box-title">HRM Report
            <span style="font-size: 10px;color: blue; ">
                <input class="pull-right" type="checkbox" id="apply_old_info" name="apply_old_info" value="1">Active Old Employee
            </span>
        </h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>

    <div class="box-body">
        <div class="row">

            @can('ReportDailyAttendance')
            <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="col-md-10">
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_daily_attendance">Daily Attendance Report</button>
                    </div>
                </div>
            </div>
            @endcan

            @can('ReportDepartmentWiseSummary')
            <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="col-md-10">
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_department_wise_summary">Department Wise Summary Report</button>
                    </div>
                </div>
            </div>
            @endcan

            @can('ReportAttendanceMissingOut')
            <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="col-md-10">
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_missing_out">Attendance Missing Out Report</button>
                    </div>
                </div>
            </div>
            @endcan

            @can('ReportMonthlyAttendanceSummary')
            <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="col-md-10">
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_attendance_summary">Monthly Attendance Summary</button>
                    </div>
                </div>
            </div>
            @endcan

            @can('ReportMonthlyAttendanceDetails')
            <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="col-md-10">
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_attendance_details">Monthly Attendance Details Report</button>
                    </div>
                </div>
            </div>
            @endcan

            @can('ReportMonthlyAttendanceDetailsWithInOut')
            <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="col-md-10">
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_attendance_details_in_out">Monthly Attendance Details With In Out Report</button>
                    </div>
                </div>
            </div>
            @endcan


            @can('ReportEmployeeLog')
            <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="col-md-10">
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_employee_log">Employee Log Report</button>
                    </div>
                </div>
            </div>
            @endcan

            @can('ReportMonthlyOverTime')
            <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="col-md-10">
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_overtime_report">Monthly Overtime Report</button>
                    </div>
                </div>
            </div>
            @endcan

            @can('ReportEmployeeWiseJobCard')
            <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="col-md-10">
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_jobcard_report">Monthly Employee Wise Job Card</button>
                    </div>
                </div>
            </div>
            @endcan


            @can('ReportMonthlyOTSheet')
            <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="col-md-10">
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_monthly_ot_sheet">Monthly OT Sheet</button>
                    </div>
                </div>
            </div>
            @endcan

            @can('ReportIndividualLeaveLedger')
            <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="col-md-10">
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_individual_leave_ledger">Individual Leave Ledger</button>
                    </div>
                </div>
            </div>
            @endcan

            @can('ReportEmployeeLeaveSummary')
            <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="col-md-10">
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_employee_leave_summary">Employee Leave Summary</button>
                    </div>
                </div>
            </div>
            @endcan

            <!--<div class="col-lg-6 col-md-6 col-xs-12">
        <div class="col-md-10">
          <div class="form-group">
            <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_compare_OT_sheet">Compare OT Report</button>
          </div>
        </div>
      </div>-->

            @can('ReportCategoryWiseEmployeeList')
            <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="col-md-10">
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_categorywise_man_power">Category Wise Employee List</button>
                    </div>
                </div>
            </div>
            @endcan



            @can('ReportManPower')
            <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="col-md-10">
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_man_power">Manpower Report</button>
                    </div>
                </div>
            </div>
            @endcan

            @can('ReportBloodGroupWise')
            <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="col-md-10">
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_bloodgroup">Blood Group Wise Report</button>
                    </div>
                </div>
            </div>
            @endcan



        </div>
    </div>

</div>

@if (Config::get('module_config.payroll_module') == 1)

<div class="box box-primary">

    <div class="box-header with-border">
        <h3 class="box-title">Salary And Bonus Reports</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>

    <div class="box-body">
        <div class="row">

            @can('ReportSalarySheet')
            <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="col-md-10">
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_salary">Salary Sheet</button>
                    </div>
                </div>
            </div>
            @endcan

            @can('ReportMonthlyTopSheetAmg')
            <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="col-md-10">
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_monthly_topSheet_amg">Salary Top Sheet</button>
                    </div>
                </div>
            </div>
            @endcan



            @can('ReportMonthlyTopSheet')
            <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="col-md-10">
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_monthly_topSheet">Monthly Top Sheet Reports</button>
                    </div>
                </div>
            </div>
            @endcan

            @can('ReportSalaryHeadWise')
            <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="col-md-10">
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_salaryHeadWiseReport">Salary Head Wise Report</button>
                    </div>
                </div>
            </div>
            @endcan





            @can('ReportCWSalarySheet')
            <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="col-md-10">
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_cw_salary">CW Salary Sheet</button>
                    </div>
                </div>
            </div>
            @endcan

            @can('ReportBonusSheet')
            <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="col-md-10">
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_bonus">Bonus Sheet</button>
                    </div>
                </div>
            </div>
            @endcan

            @can('ReportBonusTopSheet')
            <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="col-md-10">
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_bonus_topsheet">Bonus Top Sheet</button>
                    </div>
                </div>
            </div>
            @endcan




            @can('ReportBankSalarySheet')
            <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="col-md-10">
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_bank_salary_sheet">Bank Salary Sheet</button>
                    </div>
                </div>
            </div>
            @endcan

            <!--       <div class="col-lg-6 col-md-6 col-xs-12">
        <div class="col-md-10">
          <div class="form-group">
            <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_compare_salary_sheet">Compare Salary Report</button>
          </div>
        </div>
      </div> -->

            @can('ReportOutOfPocket')
            <!--       <div class="col-lg-6 col-md-6 col-xs-12">
        <div class="col-md-10">
          <div class="form-group">
            <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_out_of_pocket">Out Of Pocket</button>
          </div>
        </div>
      </div> -->
            @endcan

            @can('ReportFBSheet')
            <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="col-md-10">
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_fb_salary">Fringe Benefit Sheet</button>
                    </div>
                </div>
            </div>
            @endcan


            @can('ReportMonthlyHouseRent')
            <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="col-md-10">
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_hrm_fringbenefit_locationwise">Fringe Benefit (Location Wise)</button>
                    </div>
                </div>
            </div>
            @endcan


            @can('ReportFixedAllowance')
            <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="col-md-10">
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_fixed_allowance">Fringe Benefit (Designation Wise)</button>
                    </div>
                </div>
            </div>
            @endcan


            @can('ReportFixedAllowance')
            <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="col-md-10">
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_FringBenefit_EmployeeWise">Fringe Benefit (Employee Wise)</button>
                    </div>
                </div>
            </div>
            @endcan


            @can('ReportFixedAllowance')
            <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="col-md-10">
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_FringBenefit_TopSheet">Fringe Benefit (Top Sheet)</button>
                    </div>
                </div>
            </div>
            @endcan









            @can('ReportCarAllowance')
            <!--       <div class="col-lg-6 col-md-6 col-xs-12">
        <div class="col-md-10">
          <div class="form-group">
            <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_car_allowance">Monthly Car Allowance Report</button>
          </div>
        </div>
      </div> -->
            @endcan


            @can('ReportSalaryReview')
            <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="col-md-10">
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_salary_review">Cost To The Company & Salary Review</button>
                    </div>
                </div>
            </div>
            @endcan




            <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="col-md-10">
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary btn-lg btn-flat" data-toggle="modal" data-target="#modal_pf_report">Provident Fund Report</button>
                    </div>
                </div>
            </div>

             <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="col-md-10">
                    <div class="form-group">
                        <a href="{{ url('cost-to-the-company') }}" target="_blank" class="btn btn-block btn-primary btn-lg btn-flat">Cost to The Company (Employee Segment Wise)</a>
                       
                    </div>
                </div>
            </div>


        </div>
    </div>

</div>
@endif


<!-- Modal for Compare salary Report -->
<div class="modal fade" id="modal_compare_salary_sheet" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">Compare Salary Report</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@compare_salary_report'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
            <div class="modal-body">

                <div class="row">

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Location</label>
                            <select class="form-control" name="location" style="width: 100%;">
                              <option value="0">-- All Location --</option>
                            <option value="999">-- All Depot --</option>
                                @foreach ($location as $keys)
                                    <option value={{$keys->id}}>{{$keys->location_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>



                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Previous Month</label>
                            <select class="form-control" name="previous_month" style="width: 100%;">
                                @foreach ($month as $keys)
                                @if($keys->id==$keys->c_month)
                                <option value={{$keys->id}} selected>{{$keys->month_name}}</option>
                                @else
                                <option value={{$keys->id}}>{{$keys->month_name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Year</label>
                            <select class="form-control year" name="previous_year" style="width: 100%;">

                                <?php
                   for($i = date('Y') ; $i >= 2018; $i--){
                      echo "<option value=".$i.">".$i."</option>";
                   }
                ?>

                            </select>
                        </div>

                    </div>



                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Current Month</label>
                            <select class="form-control" name="month" style="width: 100%;">
                                @foreach ($month as $keys)
                                @if($keys->id==$keys->c_month)
                                <option value={{$keys->id}} selected>{{$keys->month_name}}</option>
                                @else
                                <option value={{$keys->id}}>{{$keys->month_name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Year</label>
                            <select class="form-control year" name="year" style="width: 100%;">
                                <?php
                   for($i = date('Y') ; $i >= 2018; $i--){
                      echo "<option value=".$i.">".$i."</option>";
                   }
                ?>

                            </select>
                        </div>

                    </div>



                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Department</label>
                            <select class="form-control depertment" name="depertment" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Generate Type</label>
                            <select class="form-control" name="generate_type" style="width: 100%;">
                                <option value="pdf">PDF</option>
                                <option value="xls">Excel</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-flat">View</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!--End Modal for Compare salary Repor -->




<!-- Modal for Out Of Pocket Report -->
<div class="modal fade" id="modal_out_of_pocket" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">Out of Pocket Report</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@outofpocket_report'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
            <div class="modal-body">

                <div class="row">

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Location</label>
                            <select class="form-control location" name="location" style="width: 100%;">
                                {{-- <option value="0">-ALL-</option>
                @foreach ($location as $keys)
                      <option value={{$keys->id}}>{{$keys->location_name}}</option>
                                @endforeach --}}
                            </select>
                        </div>
                    </div>



                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Month</label>
                            <select class="form-control" name="month" style="width: 100%;">
                                @foreach ($month as $keys)
                                @if($keys->id == $keys->c_month)
                                <option value={{$keys->id}} selected>{{$keys->month_name}}</option>
                                @else
                                <option value={{$keys->id}}>{{$keys->month_name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Year</label>
                            <select class="form-control year" name="year" style="width: 100%;">
                                <?php
                   for($i = date('Y') ; $i >= 2018; $i--){
                      echo "<option value=".$i.">".$i."</option>";
                   }
                ?>


                            </select>
                        </div>

                    </div>


                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Generate Type</label>
                            <select class="form-control" name="generate_type" style="width: 100%;">
                                <option value="pdf">PDF</option>
                                <option value="xls">Excel</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-flat">View</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!--End Modal for Out Of Pocket Report -->



<!-- Modal for Out Of Pocket Report -->
<div class="modal fade" id="modal_hrm_fringbenefit_locationwise" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">Fringe Benefit (Location Wise)</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@hrm_fringbenefit_locationwise'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
            <div class="modal-body">

                <div class="row">

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Location</label>
                            <select class="form-control" name="location" style="width: 100%;">
                                {{-- <option value="0">-ALL-</option> --}}
                                @foreach ($location as $keys)
                                <option value={{$keys->id}}>{{$keys->location_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Choose Fringe Benefit Head</label>
                            <select class="form-control salary_head_fb" name="salary_head" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Month</label>
                            <select class="form-control" name="month" style="width: 100%;">
                                @foreach ($month as $keys)
                                @if($keys->id==$keys->c_month)
                                <option value={{$keys->id}} selected>{{$keys->month_name}}</option>
                                @else
                                <option value={{$keys->id}}>{{$keys->month_name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Year</label>
                            <select class="form-control year" name="year" style="width: 100%;">

                                <?php
                   for($i = date('Y') ; $i >= 2018; $i--){
                      echo "<option value=".$i.">".$i."</option>";
                   }
                ?>

                            </select>
                        </div>

                    </div>


                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Generate Type</label>
                            <select class="form-control" name="generate_type" style="width: 100%;">
                                <option value="pdf">PDF</option>
                                <option value="xls">Excel</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-flat">View</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>



<div class="modal fade" id="modal_fixed_allowance" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">Fringe Benefit (Designation Wise)</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@fringbenefit_designationwise'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
            <div class="modal-body">

                <div class="row">

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Location</label>
                            <select class="form-control" name="location" style="width: 100%;">
                                <option value="0">-ALL-</option>
                                @foreach ($location as $keys)
                                <option value={{$keys->id}}>{{$keys->location_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Choose Fringe Benefit Head</label>
                            <select class="form-control salary_head_fb" name="salary_head" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Month</label>
                            <select class="form-control" name="month" style="width: 100%;">
                                @foreach ($month as $keys)
                                @if($keys->id==$keys->c_month)
                                <option value={{$keys->id}} selected>{{$keys->month_name}}</option>
                                @else
                                <option value={{$keys->id}}>{{$keys->month_name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Year</label>
                            <select class="form-control year" name="year" style="width: 100%;">

                                <?php
                   for($i = date('Y') ; $i >= 2018; $i--){
                      echo "<option value=".$i.">".$i."</option>";
                   }
                ?>

                            </select>
                        </div>

                    </div>


                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Generate Type</label>
                            <select class="form-control" name="generate_type" style="width: 100%;">
                                <option value="pdf">PDF</option>
                                <option value="xls">Excel</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-flat">View</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>


<div class="modal fade" id="modal_FringBenefit_EmployeeWise" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">Fringe Benefit (Employee Wise)</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@fringbenefit_employeewise'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
            <div class="modal-body">

                <div class="row">

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Location</label>
                            <select class="form-control" name="location" style="width: 100%;">
                                <option value="0">-ALL-</option>
                                @foreach ($location as $keys)
                                <option value={{$keys->id}}>{{$keys->location_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Choose Fringe Benefit Head</label>
                            <select class="form-control salary_head_fb" name="salary_head" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Month</label>
                            <select class="form-control" name="month" style="width: 100%;">
                                @foreach ($month as $keys)
                                @if($keys->id==$keys->c_month)
                                <option value={{$keys->id}} selected>{{$keys->month_name}}</option>
                                @else
                                <option value={{$keys->id}}>{{$keys->month_name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Year</label>
                            <select class="form-control year" name="year" style="width: 100%;">

                                <?php
                   for($i = date('Y') ; $i >= 2018; $i--){
                      echo "<option value=".$i.">".$i."</option>";
                   }
                ?>

                            </select>
                        </div>

                    </div>


                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Generate Type</label>
                            <select class="form-control" name="generate_type" style="width: 100%;">
                                <option value="pdf">PDF</option>
                                <option value="xls">Excel</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-flat">View</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>




<div class="modal fade" id="modal_FringBenefit_TopSheet" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">Fringe Benefit (Top Sheet)</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@fring_benefit_topsheet'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
            <div class="modal-body">

                <div class="row">

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Location</label>
                            <select class="form-control" name="location" style="width: 100%;">
                                <option value="999">-ALL With Depot Summary-</option>
                                <option value="0">-ALL-</option>
                                <option value="777">-Only Depot-</option>
                                @foreach ($location as $keys)
                                <option value={{$keys->id}}>{{$keys->location_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <!--           <div class="form-group">
              <div class="col-md-12">
                <label>Choose Fringe Benefit Head</label>
                  <select class="form-control salary_head_fb" name="salary_head" style="width: 100%;" >
                  </select>
              </div>
          </div> -->


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Month</label>
                            <select class="form-control" name="month" style="width: 100%;">
                                @foreach ($month as $keys)
                                @if($keys->id==$keys->c_month)
                                <option value={{$keys->id}} selected>{{$keys->month_name}}</option>
                                @else
                                <option value={{$keys->id}}>{{$keys->month_name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Year</label>
                            <select class="form-control year" name="year" style="width: 100%;">

                                <?php
                   for($i = date('Y') ; $i >= 2018; $i--){
                      echo "<option value=".$i.">".$i."</option>";
                   }
                ?>

                            </select>
                        </div>

                    </div>


                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Generate Type</label>
                            <select class="form-control" name="generate_type" style="width: 100%;">
                                <option value="pdf">PDF</option>
                                <option value="xls">Excel</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-flat">View</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>




<div class="modal fade" id="modal_car_allowance" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">Monthly Car Allowance Report</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@monthly_carallowance'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
            <div class="modal-body">

                <div class="row">

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Location</label>
                            <select class="form-control location" name="location" style="width: 100%;">
                                {{-- <option value="0">-ALL-</option>
                @foreach ($location as $keys)
                <option value={{$keys->id}}>{{$keys->location_name}}</option>
                                @endforeach --}}
                            </select>
                        </div>
                    </div>



                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Month</label>
                            <select class="form-control" name="month" style="width: 100%;">
                                @foreach ($month as $keys)
                                @if($keys->id==$keys->c_month)
                                <option value={{$keys->id}} selected>{{$keys->month_name}}</option>
                                @else
                                <option value={{$keys->id}}>{{$keys->month_name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Year</label>
                            <select class="form-control year" name="year" style="width: 100%;">

                                <?php
                   for($i = date('Y') ; $i >= 2018; $i--){
                      echo "<option value=".$i.">".$i."</option>";
                   }
                ?>

                            </select>
                        </div>

                    </div>


                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Generate Type</label>
                            <select class="form-control" name="generate_type" style="width: 100%;">
                                <option value="pdf">PDF</option>
                                <option value="xls">Excel</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-flat">View</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!--End Modal for Out Of Pocket Report -->


<!-- Modal for Out Of Pocket Report -->
<div class="modal fade" id="modal_salaryHeadWiseReport" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">Salary Head Wise Report</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@salary_headwise_report'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
            <div class="modal-body">

                <div class="row">

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Location</label>
                            <select class="form-control" name="location" style="width: 100%;">
                                <option value="0">-ALL-</option>
                                @foreach ($location as $keys)
                                <option value={{$keys->id}}>{{$keys->location_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Month From</label>
                            <input type="text" class="form-control month_view" placeholder="Month From" name="date_from" value="{{ date('01-m-Y', strtotime("- 1 month")) }}" readonly required>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Month To</label>
                            <input type="text" class="form-control month_view" placeholder="Month To" name="date_to" value="{{ date('01-m-Y', strtotime("- 1 month")) }}" readonly required>

                            </select>
                        </div>
                    </div>

                    <!--          <div class="form-group">
            <div class="col-md-6">
              <label>Month</label>
               <select  class="form-control" name="month" style="width: 100%;" >
                @foreach ($month as $keys)
                    @if($keys->id==$keys->c_month)
                      <option value={{$keys->id}} selected>{{$keys->month_name}}</option>
                    @else
                      <option value={{$keys->id}}>{{$keys->month_name}}</option>
                    @endif
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label>Year</label>
              <select  class="form-control year"  name="year" style="width: 100%;" >

                <?php
                   for($i = date('Y') ; $i >= 2018; $i--){
                      echo "<option value=".$i.">".$i."</option>";
                   }
                ?>

              </select>
            </div>

          </div>-->


                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Salary Head</label>
                            <select class="form-control salary_head" name="salary_head[]" multiple="multiple" style="width: 100%;">
                            </select>
                        </div>
                    </div>



                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Employee Category</label>
                            <select class="form-control category" name="category" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Generate Type</label>
                            <select class="form-control" name="generate_type" style="width: 100%;">
                                <option value="pdf">PDF</option>
                                <option value="xls">Excel</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-flat">View</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!--End Modal for Out Of Pocket Report -->


<!-- Modal for Monthly Top Sheet Report -->
<div class="modal fade" id="modal_monthly_topSheet" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">Monthly Top Sheet Report</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@monthly_topSheet'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
            <div class="modal-body">

                <div class="row">

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Location</label>
                            <select class="form-control" name="location" style="width: 100%;">
                                <option value="0">-ALL-</option>
                                @foreach ($location as $keys)
                                <option value={{$keys->id}}>{{$keys->location_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>



                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Month</label>
                            <select class="form-control" name="month" style="width: 100%;">
                                @foreach ($month as $keys)
                                @if($keys->id==$keys->c_month)
                                <option value={{$keys->id}} selected>{{$keys->month_name}}</option>
                                @else
                                <option value={{$keys->id}}>{{$keys->month_name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Year</label>
                            <select class="form-control year" name="year" style="width: 100%;">
                                <?php
                   for($i = date('Y') ; $i >= 2018; $i--){
                      echo "<option value=".$i.">".$i."</option>";
                   }
                ?>

                            </select>
                        </div>

                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Employee Category</label>
                            <select class="form-control category" name="category" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Choose Sheetss</label>
                            <select class="form-control apply_for" name="apply_for" style="width: 100%;">
                                <option value="1">Salary Top Sheet(All)</option>
                                <option value="2">Depot Top Sheet</option>
                            </select>
                        </div>
                    </div>



                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Generate Type</label>
                            <select class="form-control" name="generate_type" style="width: 100%;">
                                <option value="pdf">PDF</option>
                                <option value="xls">Excel</option>
                            </select>
                        </div>
                    </div>



                    <div class="col-md-12 form-group">
                        <input type="radio" name="with_pf_status" value="1"> <span style="color: red">With PF</span>
                        <input type="radio" name="with_pf_status" value="0" checked="checked"><span>Without PF</span>
                    </div>



                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-flat">View</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!--End Modal for Out Of Pocket Report -->





<!-- Modal for modal_monthly_topSheet_amg-->
<div class="modal fade" id="modal_monthly_topSheet_amg" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">Monthly Top Sheet Report</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@monthly_topSheet_amg'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
            <div class="modal-body">

                <div class="row">

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Location</label>
                            <select class="form-control" name="location" style="width: 100%;">
                                <option value="0">-ALL-</option>
                                @foreach ($location as $keys)
                                <option value={{$keys->id}}>{{$keys->location_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>




                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Month</label>
                            <select class="form-control" name="month" id="hrm_month_id_slap" style="width: 100%;">
                                @foreach ($month as $keys)
                                @if($keys->id==$keys->c_month)
                                <option value={{$keys->id}} selected>{{$keys->month_name}}</option>
                                @else
                                <option value={{$keys->id}}>{{$keys->month_name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Year</label>
                            <select class="form-control year" name="year" id="year_id_slap" style="width: 100%;">
                                <?php
                   for($i = date('Y') ; $i >= 2018; $i--){
                      echo "<option value=".$i.">".$i."</option>";
                   }
                ?>

                            </select>
                        </div>

                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Sub-Department</label>
                            <select class="form-control section" name="section" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Choose Sheet</label>
                            <select class="form-control apply_for" name="apply_for" style="width: 100%;">
                                <option value="1">Salary Top Sheet(All)</option>
                                <!-- <option value="2">Depot Top Sheet</option> -->
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Select Slap</label>
                            <select class="form-control slap_name" name="slap_name" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Generate Type</label>
                            <select class="form-control" name="generate_type" style="width: 100%;">
                                <option value="pdf">PDF</option>
                                <option value="xls">Excel</option>
                            </select>
                        </div>
                    </div>


                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-flat">View</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!--End Modal for Out Of Pocket Report -->






<!-- Modal for Out Of Pocket Report -->
<div class="modal fade" id="modal_bonus_topsheet" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">Bonus Top Sheet Report</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@bonus_topSheet'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
            <div class="modal-body">

                <div class="row">

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Declaration Date</label>
                            <select class="form-control declaration_date" name="declaration_date" style="width: 100%;" required="">
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Employee Category</label>
                            <select class="form-control category" name="category" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Choose Sheet</label>
                            <select class="form-control apply_for" name="apply_for" style="width: 100%;">
                                <option value="1">Bonus Top Sheet(All)</option>
                                <option value="2">Depot Top Sheet</option>
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Generate Type</label>
                            <select class="form-control" name="generate_type" style="width: 100%;">
                                <option value="pdf">PDF</option>
                                <option value="xls">Excel</option>
                            </select>
                        </div>
                    </div>




                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-flat">View</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!--End Modal for Out Of Pocket Report -->

<!-- Modal for Compare salary Report -->
{{-- <div class="modal fade" id="modal_compare_OT_sheet" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h3 class="modal-title" id="groupAddLabel">Compare OT Report</h3>
      </div>

      {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@compare_OT_report'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
      <div class="modal-body">

        <div class="row">

          <div class="form-group">
            <div class="col-md-12">
              <label>Location</label>
              <select  class="form-control location" name="location" style="width: 100%;" >
                @foreach ($location as $keys)
                <option value={{$keys->id}}>{{$keys->location_name}}</option>
@endforeach
</select>
</div>
</div>



<div class="form-group">
    <div class="col-md-6">
        <label>Previous Month</label>
        <select class="form-control" name="previous_month" style="width: 100%;">
            @foreach ($month as $keys)
            @if($keys->id==$keys->c_month)
            <option value={{$keys->id}} selected>{{$keys->month_name}}</option>
            @else
            <option value={{$keys->id}}>{{$keys->month_name}}</option>
            @endif
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label>Year</label>
        <select class="form-control year" name="previous_year" style="width: 100%;">

            <?php
                   for($i = date('Y') ; $i >= 2018; $i--){
                      echo "<option value=".$i.">".$i."</option>";
                   }
                ?>

        </select>
    </div>

</div>



<div class="form-group">
    <div class="col-md-6">
        <label>Current Month</label>
        <select class="form-control" name="month" style="width: 100%;">
            @foreach ($month as $keys)
            @if($keys->id==$keys->c_month)
            <option value={{$keys->id}} selected>{{$keys->month_name}}</option>
            @else
            <option value={{$keys->id}}>{{$keys->month_name}}</option>
            @endif
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label>Year</label>
        <select class="form-control year" name="year" style="width: 100%;">

            <?php
                   for($i = date('Y') ; $i >= 2018; $i--){
                      echo "<option value=".$i.">".$i."</option>";
                   }
                ?>

        </select>
    </div>

</div>



<div class="form-group">
    <div class="col-md-12">
        <label>Department</label>
        <select class="form-control depertment" name="depertment" style="width: 100%;">
        </select>
    </div>
</div>

<div class="form-group">
    <div class="col-md-12">
        <label>Generate Type</label>
        <select class="form-control" name="generate_type" style="width: 100%;">
            <option value="pdf">PDF</option>
            <option value="xls">Excel</option>
        </select>
    </div>
</div>

</div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary btn-flat">View</button>
</div>
{!! Form::close() !!}
</div>
</div>
</div> --}}
<!--End Modal for d -->




<!-- Modal for Daily Attendance Report -->
<div class="modal fade" id="modal_daily_attendance" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">Daily Attendance Report</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@attendance_report'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
            <div class="modal-body">

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Location</label>
                            <select class="form-control" id="attendanceLocation" name="location" style="width: 100%;">
                                @foreach ($location as $keys)
                                <option value={{ $keys->id }} {{ $keys->id == 1 ? 'selected' : '' }}>{{ $keys->location_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Employee Name</label>
                            <select class="form-control employee_name" name="employee_name" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Sub-department</label>
                            <select class="form-control section" name="section" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Department</label>
                            <select class="form-control depertment" name="depertment" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <!-- <div class="form-group">
            <div class="col-md-6">
              <label>Designation</label>
              <select class="form-control designation" name="designation" style="width: 100%;" >
              </select>
            </div>
          </div> -->

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Employee Category</label>
                            <select class="form-control category" name="category" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Employee Shift</label>
                            <select class="form-control working_shift" name="working_shift" style="width: 100%;">
                            </select>
                        </div>
                    </div>



                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Report Type</label>
                            <select class="form-control" name="report_status" style="width: 100%;">
                                <option value="0">All</option>
                                @foreach ($status as $keys)
                                <option value={{$keys->id}}>{{$keys->attendance_status}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Generate Type</label>
                            <select class="form-control" name="generate_type" style="width: 100%;">
                                <option value="pdf">PDF</option>
                                <option value="xls">Excel</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right onchange date" name="date" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" style="width: 100%;" readonly>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-flat">View</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!--End Modal for daily Attendance Report -->


<!-- Modal for Department Wise Summary Report -->
<div class="modal fade" id="modal_department_wise_summary" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">Department Wise Summary Report</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@departmentwisesummary'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
            <div class="modal-body">

                <div class="row">

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Location</label>
                            <select class="form-control" name="location" style="width: 100%;">
                                @foreach ($location as $keys)
                                <option value={{$keys->id}} {{ $keys->id == 1 ? 'selected' : '' }}>{{$keys->location_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Department</label>
                            <select class="form-control depertment" name="depertment" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6" style="margin-top: 15px;">
                            <label>Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right onchange date" name="date" placeholder="checkin" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('date') }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Generate Type</label>
                            <select class="form-control" name="generate_type" style="width: 100%;">
                                <option value="pdf">PDF</option>
                                <option value="xls">Excel</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-flat">View</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!--End Modal for Department Wise Summary Report -->


<!-- Modal for modal_missing_out -->
<div class="modal fade" id="modal_missing_out" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">Attendance Missing Out Report</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@missingoutreport'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
            <div class="modal-body">

                <div class="row">

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Location</label>
                            <select class="form-control" name="location" style="width: 100%;">
                                @foreach ($location as $keys)
                                <option value={{ $keys->id }} {{ $keys->id == 1 ? 'selected' : '' }}>{{$keys->location_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Department</label>
                            <select class="form-control depertment" name="depertment" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <!--           <div class="form-group">
            <div class="col-md-12">
              <label>Employee Name</label>
              <select class="form-control employee_name" name="employee_name" style="width: 100%;" >
              </select>
            </div>
          </div>
 -->


                    <div class="form-group">
                        <div class="col-md-6" style="margin-top: 15px;">
                            <label>Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right onchange date" name="date" placeholder="checkin" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('date') }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Generate Type</label>
                            <select class="form-control" name="generate_type" style="width: 100%;">
                                <option value="pdf">PDF</option>
                                <option value="xls">Excel</option>
                            </select>
                        </div>
                    </div>


                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-flat">View</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!--modal_missing_out -->


<!-- Modal for category Wise Man Power Report -->
<div class="modal fade" id="modal_categorywise_man_power" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">Category Wise Employee List</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@categorywise_manpower_report'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
            <div class="modal-body">

                <div class="row">

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Location</label>
                            <select class="form-control location" name="location" style="width: 100%;">
                                <option value="0">-- All --</option>
                                @foreach ($location as $keys)
                                <option value={{$keys->id}}>{{$keys->location_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Department</label>
                            <select class="form-control depertment" name="depertment" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Designation</label>
                            <select class="form-control designation" name="designation" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Employee Shift</label>
                            <select class="form-control working_shift" name="working_shift" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Employee Category</label>
                            <select class="form-control category" name="category" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Employee Name</label>
                            <select class="form-control employee_name" name="employee_name" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Generate Type</label>
                            <select class="form-control" name="generate_type" style="width: 100%;">
                                <option value="pdf">PDF</option>
                                <option value="xls">Excel</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-flat">View</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!--End Modal for category Wise Man Power -->



<!-- Modal for Employee Log Report -->
<div class="modal fade" id="modal_employee_log" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">Employee Log Report</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@employeelog_report'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
            <div class="modal-body">

                <div class="row">


                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Employee Name</label>
                            <select class="form-control employee_name" name="employee_name" style="width: 100%;" required>
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-6" style="margin-top: 15px;">
                            <label>Date From</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right onchange date" name="date_from" placeholder="checkin" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('date') }}" readonly>
                            </div>
                        </div>

                        <div class="col-md-6" style="margin-top: 15px;">
                            <label>Date To</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right onchange date" name="date_to" placeholder="checkin" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('date') }}" readonly>
                            </div>
                        </div>
                    </div>



                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Generate Type</label>
                            <select class="form-control" name="generate_type" style="width: 100%;">
                                <option value="pdf">PDF</option>
                                <option value="xls">Excel</option>
                            </select>
                        </div>
                    </div>



                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-flat">View</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!--End Modal for daily Attendance Report -->






<!-- Modal for Employee Log Report -->
<div class="modal fade" id="modal_salary_review" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">Salary Review</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@salary_review'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
            <div class="modal-body">

                <div class="row">

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Report Name</label>
                            <select class="form-control" name="report_name" style="width: 100%;">
                                <option value="1">-- Cost to the Company --</option>
                                <option value="2">-- Salary Review --</option>
                                <option value="3">-- Salary Review Top Sheet --</option>
                            </select>
                        </div>

                    </div>




                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Location</label>
                            <select class="form-control location" name="location" style="width: 100%;">
                                <option value="0">-- All Location --</option>
                                <option value="999">-- All Depot --</option>
                                @foreach ($location as $keys)
                                <option value={{$keys->id}}>{{$keys->location_name}}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>




                    <div class="form-group">

                        <div class="col-md-6">
                            <label>Month</label>
                            <select class="form-control" name="month" style="width: 100%;">
                                @foreach ($month as $keys)

                                @if($keys->id==$keys->c_month)
                                <option value={{$keys->id}} selected>{{$keys->month_name}}</option>
                                @else
                                <option value={{$keys->id}}>{{$keys->month_name}}</option>
                                @endif

                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Year</label>
                            <select class="form-control year" name="year" style="width: 100%;">
                                <?php
                   for($i = date('Y') ; $i >= 2020; $i--){
                      echo "<option value=".$i.">".$i."</option>";
                   }
                ?>
                            </select>
                        </div>

                    </div>


                    <div class="form-group hidden">
                        <div class="col-md-6" style="margin-top: 15px;">
                            <label>Date From</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right onchange date" name="date_from" placeholder="checkin" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('date') }}" readonly>
                            </div>
                        </div>

                        <div class="col-md-6" style="margin-top: 15px;">
                            <label>Date To</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right onchange date" name="date_to" placeholder="checkin" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('date') }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Department</label>
                            <select class="form-control depertment" name="depertment" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Designation</label>
                            <select class="form-control designation" name="designation" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="col-md-6">
                        <label>Employee Category</label>
                        <select class="form-control category" name="category" style="width: 100%;">
                        </select>
                    </div>


                    <div class="col-md-6">
                        <label>Sub-Department</label>
                        <select class="form-control section" name="section" style="width: 100%;">
                        </select>
                    </div>



                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Employee Name</label>
                            <select class="form-control employee_name" name="employee_name" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="form-group">

                        <div class="col-md-6">
                            <label>Employee Type</label>
                            <select class="js-example-basic-multiple form-control" name="employee_type[]" multiple="multiple" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12"></div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Increment %</label>
                            <input type="number" name="increment_amount" class="form-control" value="0">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Ex-Gratia Bonus %</label>
                            <input type="number" name="exgratia_bonus" class="form-control" value="0">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Festival Bonus % (For permanent= 60 ) </label>
                            <input type="number" name="festival_bonus" class="form-control" value="0">
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>PF% (For permanent= 10 )</label>
                            <input type="number" name="pf" class="form-control" value="0">
                        </div>
                    </div>






                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Generate Type</label>
                            <select class="form-control" name="generate_type" style="width: 100%;">
                                <option value="pdf">PDF</option>
                                <option value="xls">Excel</option>
                            </select>
                        </div>
                    </div>



                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-flat">View</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!--End Modal for daily Attendance Report -->












<!-- Modal for Individual Leave Ledger -->
<div class="modal fade" id="modal_individual_leave_ledger" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">Individual Leave Ledger</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@individual_leave_ledger_report'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
            <div class="modal-body">

                <div class="row">

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Leave Year</label>
                            <select class="form-control" name="hrm_leave_years_id" style="width: 100%;">
                                @foreach ($leave_years as $keys)
                                <option value={{$keys->id}}>{{$keys->leave_year}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Employee Name</label>
                            <select class="form-control employee_name" name="employee_name" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Generate Type</label>
                            <select class="form-control" name="generate_type" style="width: 100%;">
                                <option value="pdf">PDF</option>
                                <option value="xls">Excel</option>
                            </select>
                        </div>
                    </div>


                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-flat">View</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!--End Modal for  Individual Leave Ledger -->


<!-- Modal for Employee Leave Summary -->
<div class="modal fade" id="modal_employee_leave_summary" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">Employee Leave Summary</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@employee_leave_summary_report'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
            <div class="modal-body">

                <div class="row">

                    <div class="col-md-6">
                        <label>Location</label>
                        <select class="form-control location" name="location" style="width: 100%;">
                            {{-- <option value="0">-- All --</option> --}}
                            @foreach ($location as $keys)
                            <option value={{$keys->id}}>{{$keys->location_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Leave Year</label>
                        <select class="form-control" name="hrm_leave_years_id" style="width: 100%;">
                            @foreach ($leave_years as $keys)
                            <option value={{$keys->id}}>{{$keys->leave_year}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Department</label>
                        <select class="form-control depertment" name="depertment" style="width: 100%;">
                        </select>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Sub-department</label>
                            <select class="form-control section" name="section" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label>Designation</label>
                        <select class="form-control designation" name="designation" style="width: 100%;">
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Employee Name</label>
                        <select class="form-control employee_name" name="employee_name" style="width: 100%;">
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Employee Type</label>
                        <select class="js-example-basic-multiple" name="employee_type[]" multiple="multiple" style="width: 100%;">
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Employee Category</label>
                        <select class="form-control category" name="category" style="width: 100%;">
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Plant</label>
                        <select class="form-control plant_name" name="plant_name" style="width: 100%;">
                        </select>
                    </div>


                    <div class="col-md-6">
                        <label>Leave Type</label>
                        <select class="form-control leave_type" name="leave_type" style="width: 100%;">
                        </select>
                    </div>


                    <div class="col-md-6">
                        <label>Payment Mode</label>
                        <select class="form-control payment_mode" name="payment_mode" style="width: 100%;">
                            <option value="0">-All-</option>
                            <option value="1">Pay</option>
                            <option value="2">Without Pay</option>
                        </select>
                    </div>





                    <div class="col-md-6">
                        <label>Generate Type</label>
                        <select class="form-control" name="generate_type" style="width: 100%;">
                            <option value="pdf">PDF</option>
                            <option value="xls">Excel</option>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary btn-flat">View</button>
                    </div>

                </div>



            </div>
        </div>

        {!! Form::close() !!}
    </div>
</div>
</div>
<!--End Modal for  Employee Leave Summary -->




<!-- Modal for Man Power Report -->
<div class="modal fade" id="modal_man_power" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">Manpower Report</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@manpower_report'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
            <div class="modal-body">

                <div class="row">

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Location</label>
                            <select class="form-control location" name="location" style="width: 100%;">
                                <option value="0">-- All --</option>
                                <option value="999">-- All Depot --</option>
                                @foreach ($location as $keys)
                                <option value={{$keys->id}}>{{$keys->location_name}}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>


                    <div class="form-group">

                        <div class="col-md-6">
                            <label>Department</label>
                            <select class="form-control depertment" name="depertment" style="width: 100%;">
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Designation</label>
                            <select class="form-control designation" name="designation" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Employee Shift</label>
                            <select class="form-control working_shift" name="working_shift" style="width: 100%;">
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Employee Category</label>
                            <select class="form-control category" name="category[]" multiple="multiple" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Plant</label>
                            <select class="form-control plant_name" name="plant_name" style="width: 100%;">
                            </select>
                        </div>
                        <div class="col-md-6" hidden>
                            <label>Religion</label>
                            <select class="form-control religion" name="religion" style="width: 100%;">
                            </select>
                        </div>


                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Employee Name</label>
                            <select class="form-control employee_name" name="employee_name" style="width: 100%;">
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Employee Type</label>
                            <select class="js-example-basic-multiple" name="employee_type[]" multiple="multiple" style="width: 100%;">
                            </select>
                        </div>


                        <div class="form-group">
                            <div class="col-md-6">
                                <label>Sub-department</label>
                                <select class="form-control section" name="section" style="width: 100%;">
                                </select>
                            </div>
                        </div>



                    </div>

                    <div class="form-group">

                        <!--             <div class="col-md-4">
                   <label>Within Month(Join/Conf.)</label>
                   <input type="number" name="counting_month" placeholder="how much month?" class="form-control">
            </div>

            <div class="col-md-4">
                   <label>Before This Day</label>
                   <input type="text" name="befor_this_day" placeholder="dd-mm-yyyy" class="form-control">
            </div> -->

                        <div class="col-md-12" style="margin-top: 20px;">
                            <input type="checkbox" name="manpower_daterange" value="1">
                            <label style="color:red;"> Apply Date Range</label>
                        </div>

                        <div class="col-md-4" style="margin-top: 15px;">
                            <label>Date From</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right onchange date" name="date_from" placeholder="checkin" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('date') }}" readonly>
                            </div>
                        </div>

                        <div class="col-md-4" style="margin-top: 15px;">
                            <label>Date To</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right onchange date" name="date_to" placeholder="checkin" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" value="{{ old('date') }}" readonly>
                            </div>
                        </div>

                        <div class="col-md-4" style="margin-top: 20px;">
                            Joining Date
                            <input type="radio" name="joining" value="joining_date">
                        </div>
                        <div class="col-md-4">
                            Confirmation Date
                            <input type="radio" name="joining" value="confirmation_date">
                        </div>


                    </div>



                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Generate Type</label>
                            <select class="form-control" name="generate_type" style="width: 100%;">
                                <option value="pdf">PDF</option>
                                <option value="xls">Excel</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-flat">View</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!--End Modal for Man Power -->




<!-- Modal for Man Power Report -->
<div class="modal fade" id="modal_bloodgroup" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">Blood Group Wise Report</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@bloodgroup_report'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
            <div class="modal-body">

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Location</label>
                            <select class="form-control location" name="location" style="width: 100%;">
                                <option value="0">-- All --</option>
                                <option value="777">-Only Depot-</option>
                                @foreach ($location as $keys)
                                <option value={{$keys->id}}>{{$keys->location_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Department</label>
                            <select class="form-control depertment" name="depertment" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <!--           <div class="form-group">
            <div class="col-md-12">
              <label>Designation</label>
              <select class="form-control designation" name="designation" style="width: 100%;" >
              </select>
            </div>
          </div>

 -->

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Sub-department</label>
                            <select class="form-control section" name="section" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Employee Category</label>
                            <select class="form-control category" name="category" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Employee Name</label>
                            <select class="form-control employee_name" name="employee_name" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Blood Group Name</label>
                            <select class="form-control bloodgroup_name" name="bloodgroup_name" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Generate Type</label>
                            <select class="form-control" name="generate_type" style="width: 100%;">
                                <option value="pdf">PDF</option>
                                <option value="xls">Excel</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-flat">View</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!--End Modal for Man Power -->



<!-- Modal for Attendance Summary Report -->
<div class="modal fade" id="modal_attendance_summary" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">Monthly Attendance Summary</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@monthly_attendancesummary_report'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
            <div class="modal-body">
            <div class="row">
                    <div class="mb-3" style="margin-left: 15px;">

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="date_range" id="date_range5" value="1" >
                            <label class="form-check-label" for="date_range5">Date Range</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="date_range" id="year_month5" value="2" checked>
                            <label class="form-check-label" for="year_month5">Year Month</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group dateRangeField" >
                        <div class="col-md-6">
                            <label>From Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right onchange date" name="from_date" data-date-format="dd-mm-yyyy" value="{{date('01-m-Y')}}" style="width: 100%;" readonly>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <label>To Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right onchange date" name="to_date" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" style="width: 100%;" readonly>
                            </div>
                        </div>
                    </div>
                    </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Month</label>
                            <select class="form-control" name="month" style="width: 100%;">
                                @foreach ($month as $keys)
                                @if($keys->id==$keys->c_month)
                                <option value={{$keys->id}} selected>{{$keys->month_name}}</option>
                                @else
                                <option value={{$keys->id}}>{{$keys->month_name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Year</label>
                            <select class="form-control year" name="year" style="width: 100%;">
                                <?php
                   for($i = date('Y') ; $i >= 2018; $i--){
                      echo "<option value=".$i.">".$i."</option>";
                   }
                ?>

                            </select>
                        </div>

                    </div>


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Location</label>
                            <select class="form-control location" name="location" style="width: 100%;">
                                {{-- <option value="0">-- All --</option> --}}
                                @foreach ($location as $keys)
                                <option value={{$keys->id}}>{{$keys->location_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Department</label>
                            <select class="form-control depertment" name="depertment" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <!--           <div class="form-group">
            <div class="col-md-12">
              <label>Designation</label>
              <select class="form-control designation" name="designation" style="width: 100%;" >
              </select>
            </div>
          </div> -->

                    <!--           <div class="form-group">
            <div class="col-md-12">
              <label>Employee Shift</label>
              <select class="form-control working_shift" name="working_shift" style="width: 100%;" >
              </select>
            </div>
          </div> -->

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Sub-department</label>
                            <select class="form-control section" name="section" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Employee Category</label>
                            <select class="form-control category" name="category" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Employee Name</label>
                            <select class="form-control employee_name" name="employee_name" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Generate Type</label>
                            <select class="form-control" name="generate_type" style="width: 100%;">
                                <option value="pdf">PDF</option>
                                <option value="xls">Excel</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Report For</label>
                            <select class="form-control" name="report_for" style="width: 100%;">
                                <option value="1">Regular Employee</option>
                                <option value="2">Casual Worker</option>
                            </select>
                        </div>
                    </div>




                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-flat">View</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!--End Modal for Attendance Summary -->

<!-- Modal for Attendance Summary Report -->
<div class="modal fade" id="modal_attendance_details" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">Monthly Attendance Details</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@monthly_attendancedetails_report'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
            <div class="modal-body">

               <div class="row">
                    <div class="mb-3" style="margin-left: 15px;">

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="date_range" id="date_range" value="1" >
                            <label class="form-check-label" for="date_range">Date Range</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="date_range" id="year_month" value="2" checked>
                            <label class="form-check-label" for="year_month">Year Month</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group dateRangeField" >
                        <div class="col-md-6">
                            <label>From Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right onchange date" name="from_date" data-date-format="dd-mm-yyyy" value="{{date('01-m-Y')}}" style="width: 100%;" readonly>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <label>To Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right onchange date" name="to_date" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" style="width: 100%;" readonly>
                            </div>
                        </div>
                    </div>
                      <div class="form-group yearMonthField" style="display: none;">

                        <div class="col-md-6">
                            <label>Month</label>
                            <select class="form-control" name="month" style="width: 100%;">
                                @foreach ($month as $keys)
                                @if($keys->id==$keys->c_month)
                                <option value={{$keys->id}} selected>{{$keys->month_name}}</option>
                                @else
                                <option value={{$keys->id}}>{{$keys->month_name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Year</label>
                            <select class="form-control year" name="year" style="width: 100%;">
                                <?php
                   for($i = date('Y') ; $i >= 2018; $i--){
                      echo "<option value=".$i.">".$i."</option>";
                   }
                ?>

                            </select>
                        </div>

                    </div>


                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Location</label>
                            <select class="form-control location" name="location" style="width: 100%;">
                                @foreach ($location as $keys)
                                <option value={{$keys->id}}>{{$keys->location_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Department</label>
                            <select class="form-control depertment" name="depertment" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Sub-department</label>
                            <select class="form-control section" name="section" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Employee Category</label>
                            <select class="form-control category" name="category" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Employee Name</label>
                            <select class="form-control employee_name" name="employee_name" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Generate Type</label>
                            <select class="form-control" name="generate_type" style="width: 100%;">
                                <option value="pdf">PDF</option>
                                <option value="xls">Excel</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-flat">View</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!--End Modal for Attendance Summary -->


<!-- Modal for Attendance Summary Report -->
<div class="modal fade" id="modal_attendance_details_in_out" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">Monthly Attendance Details With In Out</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@monthly_attendancedetails_with_inout'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
            <div class="modal-body">
            <div class="row">
                    <div class="mb-3" style="margin-left: 15px;">

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="date_range" id="date_range7" value="1" >
                            <label class="form-check-label" for="date_range7">Date Range</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="date_range" id="year_month7" value="2" checked>
                            <label class="form-check-label" for="year_month7">Year Month</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group dateRangeField" >
                        <div class="col-md-6">
                            <label>From Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right onchange date" name="from_date" data-date-format="dd-mm-yyyy" value="{{date('01-m-Y')}}" style="width: 100%;" readonly>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <label>To Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right onchange date" name="to_date" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" style="width: 100%;" readonly>
                            </div>
                        </div>
                    </div>
                    </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Month</label>
                            <select class="form-control" name="month" style="width: 100%;">
                                @foreach ($month as $keys)
                                @if($keys->id==$keys->c_month)
                                <option value={{$keys->id}} selected>{{$keys->month_name}}</option>
                                @else
                                <option value={{$keys->id}}>{{$keys->month_name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Year</label>
                            <select class="form-control year" name="year" style="width: 100%;">
                                <?php
                   for($i = date('Y') ; $i >= 2018; $i--){
                      echo "<option value=".$i.">".$i."</option>";
                   }
                ?>

                            </select>
                        </div>

                    </div>


                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Location</label>
                            <select class="form-control location" name="location" style="width: 100%;">
                                @foreach ($location as $keys)
                                <option value={{$keys->id}}>{{$keys->location_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Department</label>
                            <select class="form-control depertment" name="depertment" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Sub-department</label>
                            <select class="form-control section" name="section" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Employee Category</label>
                            <select class="form-control category" name="category" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Employee Name</label>
                            <select class="form-control employee_name" name="employee_name" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Generate Type</label>
                            <select class="form-control" name="generate_type" style="width: 100%;">
                                <option value="pdf">PDF</option>
                                <option value="xls">Excel</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-flat">View</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!--End Modal for Attendance Summary -->





<!-- Modal for Over Time Report -->
<div class="modal fade" id="modal_overtime_report" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">Monthly Overtime Report</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@monthly_overtime_report'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
            <div class="modal-body">
                <div class="row">
                    <div class="mb-3" style="margin-left: 15px;">

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="date_range" id="date_range1" value="1" >
                            <label class="form-check-label" for="date_range1">Date Range</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="date_range" id="year_month1" value="2" checked>
                            <label class="form-check-label" for="year_month1">Year Month</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group dateRangeField" >
                        <div class="col-md-6">
                            <label>From Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right onchange date" name="from_date" data-date-format="dd-mm-yyyy" value="{{date('01-m-Y')}}" style="width: 100%;" readonly>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <label>To Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right onchange date" name="to_date" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" style="width: 100%;" readonly>
                            </div>
                        </div>
                    </div>
                      <div class="form-group yearMonthField" style="display: none;">
                      <div class="col-md-6">
                          <label>Month</label>
                          <select class="form-control" name="month" style="width: 100%;">
                              @foreach ($month as $keys)
                              @if($keys->id==$keys->c_month)
                              <option value={{$keys->id}} selected>{{$keys->month_name}}</option>
                              @else
                              <option value={{$keys->id}}>{{$keys->month_name}}</option>
                              @endif
                              @endforeach
                          </select>
                      </div>

                      <div class="col-md-6">
                          <label>Year</label>
                          <select class="form-control year" name="year" style="width: 100%;">
                              <?php
                              for($i = date('Y') ; $i >= 2018; $i--){
                                echo "<option value=".$i.">".$i."</option>";
                              }
                          ?>

                          </select>
                      </div>
                    </div>



                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Location</label>
                            <select class="form-control location" name="location" style="width: 100%;">
                                {{-- <option value="0">-- All --</option> --}}
                                @foreach ($location as $keys)
                                <option value={{$keys->id}}>{{$keys->location_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Department</label>
                            <select class="form-control depertment" name="depertment" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Sub-Department</label>
                            <select class="form-control section" name="section" style="width: 100%;">
                            </select>
                        </div>
                    </div>




                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Designation</label>
                            <select class="form-control designation" name="designation" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    {{-- <div class="form-group">
            <div class="col-md-6">
              <label>Employee Shift</label>
              <select class="form-control working_shift" name="working_shift" style="width: 100%;" >
              </select>
            </div>
          </div> --}}


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Employee Category</label>
                            <select class="form-control category" name="category" style="width: 100%;">
                            </select>
                        </div>
                    </div>




                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Employee Name</label>
                            <select class="form-control employee_name" name="employee_name" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Report For</label>
                            <select class="form-control" name="report_for" style="width: 100%;">
                                <option value="2">OT Summary</option>
                                <option value="1">OT Details</option>
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Generate Type</label>
                            <select class="form-control" name="generate_type" style="width: 100%;">
                                <option value="pdf">PDF</option>
                                <option value="xls">Excel</option>
                            </select>
                        </div>
                    </div>


                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-flat">View</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<!--End Modal for Over Time Report -->




<!-- Modal for OT Sheet Report -->
<div class="modal fade" id="modal_monthly_ot_sheet" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">Monthly Overtime Sheet (QFL)</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@monthly_ot_sheet'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
            <div class="modal-body">

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Month</label>
                            <select class="form-control" name="month" style="width: 100%;">
                                @foreach ($month as $keys)
                                @if($keys->id==$keys->c_month)
                                <option value={{$keys->id}} selected>{{$keys->month_name}}</option>
                                @else
                                <option value={{$keys->id}}>{{$keys->month_name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Year</label>
                            <select class="form-control year" name="year" style="width: 100%;">
                                <?php
                                    for($i = date('Y') ; $i >= 2018; $i--){
                                        echo "<option value=".$i.">".$i."</option>";
                                    }
                                ?>

                            </select>
                        </div>

                    </div>


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Location</label>
                            <select class="form-control location" name="location" style="width: 100%;">
                                @foreach ($location as $keys)
                                <option value={{$keys->id}}>{{$keys->location_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Department</label>
                            <select class="form-control depertment" name="depertment" style="width: 100%;">
                            </select>
                        </div>
                    </div>



                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Sub-Department</label>
                            <select class="form-control section" name="section" style="width: 100%;">
                            </select>
                        </div>
                    </div>




                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Designation</label>
                            <select class="form-control designation" name="designation" style="width: 100%;">
                            </select>
                        </div>
                    </div>




                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Employee Category</label>
                            <select class="form-control category" name="category" style="width: 100%;">
                            </select>
                        </div>
                    </div>



                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Employee Name</label>
                            <select class="form-control employee_name" name="employee_name" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Report For</label>
                            <select class="form-control" name="report_for" style="width: 100%;">
                                <option value="1">OT Sheet Details</option>
                                <option value="2">OT Top Sheet</option>
                                <option value="3">OT Bank Sheet</option>
                            </select>
                        </div>
                    </div>




                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Generate Type</label>
                            <select class="form-control" name="generate_type" style="width: 100%;">
                                <option value="pdf">PDF</option>
                                <option value="xls">Excel</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Payment Mode</label>
                            <select class="form-control payment_mode" name="payment_mode" style="width: 100%;">
                                <option value="0">==Both==</option>
                                <option value="1">==Cash==</option>
                                <option value="2">==Bank==</option>
                            </select>
                        </div>
                    </div>


                    <div class="form-group ">
                        <div class="col-md-6">
                            <label>Bank Name</label>
                            <select class="form-control bank_name" name="bank_name" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="col-md-12 form-group">
                        <input type="radio" name="all_location" value="0" checked="checked"> <span style="color: red">Single</span>
                        <input type="radio" name="all_location" value="1"><span>All Location</span>
                    </div>




                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-flat">View</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<!--End Modal for Over Time Report -->


<!-- Modal for Job Card Report -->
<div class="modal fade" id="modal_jobcard_report" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">Employee Wise Job Card Report</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@monthly_jobcard_report'], 'id'=>'job_card_report','target' => '_blank' ]) !!}
            <div class="modal-body">

                <!-- <div class="row"> -->
                <div class="row">
                    <div class="mb-3" style="margin-left: 15px;">

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="date_range" id="date_range2" value="1" >
                            <label class="form-check-label" for="date_range2">Date Range</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="date_range" id="year_month2" value="2" checked>
                            <label class="form-check-label" for="year_month2">Year Month</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group dateRangeField" >
                        <div class="col-md-6">
                            <label>From Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right onchange date" name="from_date" data-date-format="dd-mm-yyyy" value="{{date('01-m-Y')}}" style="width: 100%;" readonly>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <label>To Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right onchange date" name="to_date" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}" style="width: 100%;" readonly>
                            </div>
                        </div>
                    </div>
                      <div class="form-group yearMonthField" style="display: none;">
                    <div class="col-md-6">
                        <label>Month</label>
                        <select class="form-control" name="month" style="width: 100%;">
                            @foreach ($month as $keys)
                            @if($keys->id==$keys->c_month)
                            <option value={{$keys->id}} selected>{{$keys->month_name}}</option>
                            @else
                            <option value={{$keys->id}}>{{$keys->month_name}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Year</label>
                        <select class="form-control year" name="year" style="width: 100%;">
                            <?php
                   for($i = date('Y') ; $i >= 2018; $i--){
                      echo "<option value=".$i.">".$i."</option>";
                   }
                ?>

                        </select>
                    </div>
                    </div>

                    <!-- </div> -->

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Location</label>
                            <select class="form-control location" name="location" id="location" style="width: 100%;">
                                @foreach ($location as $keys)
                                <option value={{$keys->id}}>{{$keys->location_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Department</label>
                            <select class="form-control depertment" name="depertment" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Sub-department</label>
                            <select class="form-control section" name="section" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Employee Category</label>
                            <select class="form-control category" name="category" style="width: 100%;">
                            </select>

                        </div>
                    </div>



                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Employee Name</label>
                            <select class="employee_name" name="employee_name[]" multiple="multiple" style="width: 100%;">
                            </select>

                        </div>
                    </div>




                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Generate Type</label>
                            <select class="form-control" name="generate_type" style="width: 100%;">
                                <option value="pdf">PDF</option>
                                <option value="xls">Excel</option>
                            </select>
                        </div>
                    </div>




                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Report For</label>
                            <select class="form-control" name="report_for" style="width: 100%;">
                                <option value="1">General</option>
                                <option value="2">Casual Worker</option>
                            </select>
                        </div>
                    </div>



                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-flat">View</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<!--End Modal for Over Time Report -->









<!-- Modal for Salary Report -->
<div class="modal fade" id="modal_salary" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">Salary Report</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@salaryreport'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
            <div class="modal-body">

                <div class="row">

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Location</label>
                            <select class="form-control locationforsalary location" name="location" id="locationforsalary" style="width: 100%;">
                                <option value="{{$default_user_location[0]->id}}">{{$default_user_location[0]->location_name}}</option>
                            </select>
                        </div>
                    </div>


                    <div class="form-group">

                        <div class="col-md-3">
                            <label>Month</label>
                            <select class="form-control" name="month" id="salary_report_month" style="width: 100%;">
                                @foreach ($month as $keys)

                                @if($keys->id==$keys->c_month)
                                <option value={{$keys->id}} selected>{{$keys->month_name}}</option>
                                @else
                                <option value={{$keys->id}}>{{$keys->month_name}}</option>
                                @endif

                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>Year</label>
                            <select class="form-control yearforsalary" id="yearforsalary" name="year" style="width: 100%;">
                                <?php
                                for($i = date('Y') ; $i >= 2018; $i--){
                                    echo "<option value=".$i.">".$i."</option>";
                                }
                                ?>
                            </select>
                        </div>

                    </div>




                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Department</label>
                            <select class="form-control depertment" name="depertment" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Designation</label>
                            <select class="form-control designation" name="designation" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 hidden">
                        <label>Employee Shift</label>
                        <select class="form-control working_shift" name="working_shift" style="width: 100%;">
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Employee Category</label>
                        <select class="form-control category" name="category" style="width: 100%;">
                        </select>
                    </div>


                    <div class="col-md-6">
                        <label>Sub-Department</label>
                        <select class="form-control section" name="section" style="width: 100%;">
                        </select>
                    </div>



                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Employee Name</label>
                            <select class="form-control employee_name" name="employee_name" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Payment Mode</label>
                            <select class="form-control payment_mode" name="payment_mode" style="width: 100%;">
                                <option value="0">==Both==</option>
                                <option value="1">==Cash==</option>
                                <option value="2">==Bank==</option>
                            </select>
                        </div>
                    </div>


                    <div class="form-group payment_mode_result">
                        <div class="col-md-6">
                            <label>Bank Name</label>
                            <select class="form-control bank_name" name="bank_name" style="width: 100%;">
                            </select>
                        </div>
                    </div>




                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Declaration Date</label>
                            <select class="form-control" name="salary_master" id="salary_master" style="width: 100%;" required>
                            </select>
                        </div>
                    </div>





                    <!--           <div class="form-group">
            <div class="col-md-6">
              <label>Report Type</label>
              <select  class="form-control" name="report_type" style="width: 100%;" >
                <option value="1">==Salary Sheet==</option>
                <option value="2">==Fringe Benefit==</option>
              </select>
            </div>
          </div> -->

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Generate Type</label>
                            <select class="form-control" name="generate_type" style="width: 100%;">
                                <option value="pdf">PDF</option>
                                <option value="xls">Excel</option>
                            </select>
                        </div>
                    </div>

                     <div class="form-group">
                        <div class="col-md-6">
                            <label>Employee Status</label>
                            <select class="form-control employee_status" multiple="multiple" name="employee_status[]" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-flat">View</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!--End Modal for Salary Report -->

<!-- Modal for Salary Report -->
<div class="modal fade" id="modal_fb_salary" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">Fringe Benefit Report</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@fringebenefitreport'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
            <div class="modal-body">

                <div class="row">

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Location</label>
                            {{-- <select  class="form-control location" name="location"  style="width: 100%;" >
                <option value="{{$default_user_location[0]->id}}">{{$default_user_location[0]->location_name}}</option>
                            </select> --}}

                            <select class="form-control location" name="location" style="width: 100%;">
                                <option value="0">-- All --</option>
                                <option value="777">-Only Depot-</option>
                                @foreach ($location as $keys)
                                <option value={{$keys->id}}>{{$keys->location_name}}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>


                    <div class="form-group">

                        <div class="col-md-3">
                            <label>Month</label>
                            <select class="form-control" name="month" id="month" style="width: 100%;">
                                @foreach ($month as $keys)
                                @if($keys->id==$keys->c_month)
                                <option value={{$keys->id}} selected>{{$keys->month_name}}</option>
                                @else
                                <option value={{$keys->id}}>{{$keys->month_name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>Year</label>
                            <select class="form-control year" name="year" style="width: 100%;">
                                <?php
                   for($i = date('Y') ; $i >= 2018; $i--){
                      echo "<option value=".$i.">".$i."</option>";
                   }
                ?>
                            </select>
                        </div>

                    </div>


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Payment Mode</label>
                            <select class="form-control" name="payment_mode" style="width: 100%;">
                                <option value="0">==Both==</option>
                                <option value="1">==Cash==</option>
                                <option value="2">==Bank==</option>
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Department</label>
                            <select class="form-control depertment" name="depertment" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Designation</label>
                            <select class="form-control designation" name="designation" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 hidden">
                        <label>Employee Shift</label>
                        <select class="form-control working_shift" name="working_shift" style="width: 100%;">
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Employee Category</label>
                        <select class="form-control category" name="category" style="width: 100%;">
                        </select>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Employee Name</label>
                            <select class="form-control employee_name" name="employee_name" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Generate Type</label>
                            <select class="form-control" name="generate_type" style="width: 100%;">
                                <option value="pdf">PDF</option>
                                <option value="xls">Excel</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-flat">View</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!--End Modal for Salary Report -->

<!-- Modal for CW Salary Report -->
<div class="modal fade" id="modal_cw_salary" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">CW Salary Report</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@hrm_cw_salary_report'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
            <div class="modal-body">

                <div class="row">
                    <div class="form-group">

                        <div class="form-group">
                            <div class="col-md-12">
                                <label>Location</label>
                                <select class="form-control location" name="location" style="width: 100%;">
                                    @foreach ($location as $keys)
                                    <option value={{$keys->id}}>{{$keys->location_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>



                        <div class="col-md-6">
                            <label>Month</label>
                            <select class="form-control" name="month" style="width: 100%;">
                                @foreach ($month as $keys)
                                @if($keys->id==$keys->c_month)
                                <option value={{$keys->id}} selected>{{$keys->month_name}}</option>
                                @else
                                <option value={{$keys->id}}>{{$keys->month_name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Year</label>
                            <select class="form-control year" name="year" style="width: 100%;">
                                <?php
                   for($i = date('Y') ; $i >= 2018; $i--){
                      echo "<option value=".$i.">".$i."</option>";
                   }
                ?>

                            </select>
                        </div>

                    </div>




                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Plant Name</label>
                            <select class="form-control plant_name" name="plant_name" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Department</label>
                            <select class="form-control depertment" name="depertment" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <!-- <div class="form-group">
            <div class="col-md-12">
              <label>Designation</label>
              <select class="form-control designation" name="designation" style="width: 100%;" >
              </select>
            </div>
          </div> -->

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Sub-department</label>
                            <select class="form-control section" name="section" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Employee Name</label>
                            <select class="form-control employee_name" name="employee_name" style="width: 100%;">
                            </select>
                        </div>
                    </div>



                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Payment Mode</label>
                            <select class="form-control payment_mode" name="payment_mode" style="width: 100%;">
                                <option value="0">==Both==</option>
                                <option value="1">==Cash==</option>
                                <option value="2">==Bank==</option>
                            </select>
                        </div>
                    </div>


                    <!--
          <div class="form-group payment_mode_result">
            <div class="col-md-6">
              <label>Bank Name</label>
              <select class="form-control bank_name" name="bank_name" style="width: 100%;" >
              </select>
            </div>
          </div>
 -->


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Generate Type</label>
                            <select class="form-control" name="generate_type" style="width: 100%;">
                                <option value="pdf">PDF</option>
                                <option value="xls">Excel</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-flat">View</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!--End Modal for Salary Report -->





<!-- Modal for Bonus Report -->
<div class="modal fade" id="modal_bonus" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">Bonus Reports</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@bonusreport'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
            <div class="modal-body">

                <div class="row">



                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Bonus Name</label>
                            <select class="form-control bonus_master" name="hrm_employee_bonus_master_id" style="width: 100%;" required="">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Report Name</label>
                            <select class="form-control" name="report_number" style="width: 100%;">
                                <option value="1">Bonus Sheet </option>
                                <option value="2">Bank Bonus Sheet</option>
                                <option value="3">Bank Bonus Sheet Details</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Payment Mode</label>
                            <select class="form-control payment_mode" name="payment_mode" style="width: 100%;">
                                <option value="0">==Both==</option>
                                <option value="1">==Cash==</option>
                                <option value="2">==Bank==</option>
                            </select>
                        </div>
                    </div>


                    <div class="form-group ">
                        <div class="col-md-6">
                            <label>Bank Name</label>
                            <select class="form-control bank_name" name="bank_name" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Sub-Department</label>
                            <select class="form-control section" name="section" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="col-md-6">
                        <label>Employee Category</label>
                        <select class="form-control category" name="category" style="width: 100%;">
                        </select>
                    </div>



                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Department</label>
                            <select class="form-control depertment" name="depertment" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Designation</label>
                            <select class="form-control designation" name="designation" style="width: 100%;">
                            </select>
                        </div>
                    </div>



                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Employee Name</label>
                            <select class="form-control employee_name" name="employee_name" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>
                                <input type="checkbox" id="toggleLocatioDeclaration">
                                -ALL-
                            </label>
                            <select class="form-control toggleLocatioDeclaration" name="all_location" style="width:100%;display:none">
                                <option value="777">All Location</option>
                                <option value="999">All Depot</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6 toggleLocatioDeclaration" style="display:none">
                            <label>Declaration Date</label>
                            <select class="form-control declaration_date" name="declaration_date" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Employee Status</label>
                            <select class="form-control employee_status" multiple="multiple" name="employee_status[]" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Generate Type</label>
                            <select class="form-control" name="generate_type" style="width: 100%;">
                                <option value="pdf">PDF</option>
                                <option value="xls">Excel</option>
                            </select>
                        </div>
                    </div>






                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" value="1" name="view" class="btn">Summary View</button>
                <button type="submit" value="2" name="view" class="btn btn-primary btn-flat">View</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!--End Modal for Bonus Report -->




<!-- Modal for Bonus Report -->
<div class="modal fade" id="modal_pf_report" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">PF Reports</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@pfreport'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
            <div class="modal-body">

                <div class="row">


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Report Name</label>
                            <select class="form-control" name="report_number" style="width: 100%;">
                                <option value="1">PF Ledger Details </option>
                                <option value="2">PF Ledger Summary</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Location</label>
                            <select class="form-control location" name="location" style="width: 100%;">
                                <option value="0">-- All --</option>
                                @foreach ($location as $keys)
                                <option value={{$keys->id}}>{{$keys->location_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>




                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Month From</label>
                            <input type="text" class="form-control month_view" placeholder="Month From" name="date_from" value="" readonly required>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Month To</label>
                            <input type="text" class="form-control month_view" placeholder="Month To" name="date_to" readonly required>

                            </select>
                        </div>
                    </div>






                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Employee Name</label>
                            <select class="form-control employee_name" name="employee_name" style="width: 100%;">
                            </select>
                        </div>
                    </div>




                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Sub-Department</label>
                            <select class="form-control section" name="section" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="col-md-6">
                        <label>Employee Category</label>
                        <select class="form-control category" name="category" style="width: 100%;">
                        </select>
                    </div>



                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Department</label>
                            <select class="form-control depertment" name="depertment" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Designation</label>
                            <select class="form-control designation" name="designation" style="width: 100%;">
                            </select>
                        </div>
                    </div>






                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Generate Type</label>
                            <select class="form-control" name="generate_type" style="width: 100%;">
                                <option value="pdf">PDF</option>
                                <option value="xls">Excel</option>
                            </select>
                        </div>
                    </div>





                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-flat">View</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!--End Modal for Bonus Report -->










<!-- Modal for Bank Salary Report -->
<div class="modal fade" id="modal_bank_salary_sheet" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="groupAddLabel">Bank Salary Sheet</h3>
            </div>

            {!! Form::open(['method'=>'POST', 'action'=>['ReportsController@banksalaryreport'], 'id'=>'frm_attendance_report','target' => '_blank' ]) !!}
            <div class="modal-body">

                <div class="row">

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Report For</label>
                            <select class="form-control" name="report_for" style="width: 100%;">
                                <option value="1">General</option>
                                <option value="2">Casual Worker</option>
                                <option value="3">All Depot</option>
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Location</label>
                            <select class="form-control location" name="location" style="width: 100%;">
                                <option value="0">-- All --</option>
                                @foreach ($location as $keys)
                                <option value={{$keys->id}}>{{$keys->location_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>




                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Month</label>
                            <select class="form-control" id="month_bank_salary" name="month" style="width: 100%;">
                                @foreach ($month as $keys)
                                @if($keys->id==$keys->c_month)
                                <option value={{$keys->id}} selected>{{$keys->month_name}}</option>
                                @else
                                <option value={{$keys->id}}>{{$keys->month_name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Year</label>
                            <select class="form-control year" id="year_bank_salary" name="year" style="width: 100%;">
                                <?php
                   for($i = date('Y') ; $i >= 2018; $i--) {
                      echo "<option value=".$i.">".$i."</option>";
                   }
                ?>
                            </select>
                        </div>

                    </div>



                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Bank Name</label>
                            <select class="form-control" name="bank_name" style="width: 100%;">
                                <option value="0">-All Bank-</option>
                                @foreach ($bankname as $keys)
                                <option value={{$keys->id}}>{{$keys->bank_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Department</label>
                            <select class="form-control depertment" name="depertment" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Employee Category</label>
                            <select class="form-control category" name="category" style="width: 100%;">
                            </select>
                        </div>
                    </div>




                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Employee Name</label>
                            <select class="form-control employee_name" name="employee_name" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Select Slap</label>
                            <select class="form-control slap_name_bank_salary" name="slap_name" style="width: 100%;">
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Report Formate</label>
                            <select class="form-control" name="report_formate" style="width: 100%;">
                                <option value="1">Formate-01</option>
                                <option value="2">Formate-02</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label>Generate Type</label>
                            <select class="form-control" name="generate_type" style="width: 100%;">
                                <option value="pdf">PDF</option>
                                <option value="xls">Excel</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-flat">View</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!--End Modal for modal_bank_salary_sheet Report -->




@endsection

@section('script')
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>



<script>
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};

    $(document).ready(function() {


        $(".yearMonthField").show();
        $(".dateRangeField").hide();

        $('#job_card_report').on('submit', function(e) {

            let department = $(this).find('[name="depertment"]').val();
            let section    = $(this).find('[name="section"]').val();
            let category   = $(this).find('[name="category"]').val();
            let employee   = $(this).find('[name="employee_name[]"]').val();

              if (
                    department ||
                    section ||
                    category ||
                    (employee && employee.length > 0)
                ) {
                    return true;
                }

                e.preventDefault();
                alert('Please select Department or Section or Category or Employee.');
                return false;
            });

        $('input[name="date_range"]').on('change', function() {

            if ($(this).val() == "1") {

                $(".dateRangeField").show();
                $(".yearMonthField").hide();
            } else if ($(this).val() == "2") {
                $(".yearMonthField").show();
                $(".dateRangeField").hide();
            }
        });


        $(document).on('click', '#toggleLocatioDeclaration', function() {
            $(this).is(':checked') ?
                $('.toggleLocatioDeclaration').css({
                    'display': 'inline'
                }) :
                $('.toggleLocatioDeclaration').css({
                    'display': 'none'
                })
        })


        $('.js-example-basic-multiple').select2({
            placeholder: 'Enter Type'
            , ajax: {
                dataType: 'json'
                , url: "{{URL::to('employeestatus_list_data')}}"
                , delay: 250
                , data: function(params) {
                    return {
                        term: params.term
                    }
                }
                , processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data
                        , pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                }
                , cache: true
            }
        });

        $('.month_view').datepicker({
            autoclose: true
            , minViewMode: 1
            , format: 'dd-mm-yyyy'
        });

        $('.date').datepicker({
            autoclose: true
        });

        $('.date_from').datepicker({
            autoclose: true
        });

        $('.date_to').datepicker({
            autoclose: true
        });

        $("#attendanceLocation").on('change', function() {
            $(".employee_name").val(null).trigger('change');
        });

        $(`
        #modal_individual_leave_ledger,
        #modal_categorywise_man_power,
        #modal_attendance_details,
        #modal_attendance_summary,
        #modal_bank_salary_sheet,
        #modal_monthly_ot_sheet,
        #modal_overtime_report,
        #modal_jobcard_report,
        #modal_employee_log,
        #modal_bloodgroup,
        #modal_fb_salary,
        #modal_man_power,
        #modal_pf_report,
        #modal_salary,
        #modal_bonus
    `).on('shown.bs.modal', function() {
            $('#attendanceLocation').val("")
            $('.location option:contains("-- All --")').val(null).text('-- All --');
            $(".employee_name").val(null).trigger('change');
        });

        // $(`
        //     #modal_missing_out,
        //     #modal_daily_attendance
        // `).on('show.bs.modal', function() {
        //     $('[name=location]').val(1).trigger('change');
        // });

        $('.location').on('change', function(e) {
            $('.employee_name').select2({
                placeholder: 'Enter an Employee Name'
                , allowClear: true
                , ajax: {
                    dataType: 'json'
                    , url: "{{URL::to('join_employee_list')}}"
                    , delay: 250
                    , data: function(params) {
                        return {
                            term: params.term
                            , apply_old_info: $('input[name=apply_old_info]:checked').val()
                            , location: e.target.value
                        }
                    }
                    , processResults: function(data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data
                            , pagination: {
                                more: (params.page * 30) < data.total_count
                            }
                        };
                    }
                    , cache: true
                }
            });
        })

        $('.employee_name').select2({
            placeholder: 'Enter an Employee Name'
            , allowClear: true
            , ajax: {
                dataType: 'json'
                , url: "{{URL::to('join_employee_list')}}"
                , delay: 250
                , data: function(params) {
                    return {
                        term: params.term
                        , apply_old_info: $('input[name=apply_old_info]:checked').val()
                        , location: $('#attendanceLocation option:selected').val()
                    }
                }
                , processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data
                        , pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                }
                , cache: true
            }
        });

         $('.employee_status').select2({
            placeholder: 'Enter an Employee Status'
            , allowClear: true
            , ajax: {
                dataType: 'json'
                , url: "{{URL::to('employeestatus_list_data')}}"
                , delay: 250
                , data: function(params) {
                    return {
                        term: params.term
                        , apply_old_info: $('input[name=apply_old_info]:checked').val()
                        , location: $('#attendanceLocation option:selected').val()
                    }
                }
                , processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data
                        , pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                }
                , cache: true
            }
        });

        $('.salary_head').select2({
            ajax: {
                dataType: 'json'
                , url: "{{URL::to('salary_head_list')}}"
                , delay: 250
                , data: function(params) {
                    return {
                        term: params.term
                    }
                }
                , processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data
                        , pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                }
                , cache: true
            }
        });

        $('.salary_head_fb').select2({
            placeholder: 'Search'
            , allowClear: true
            , ajax: {
                dataType: 'json'
                , url: "{{URL::to('salary_head_list')}}"
                , delay: 250
                , data: function(params) {
                    return {
                        term: params.term
                        , fb: 1
                    }
                }
                , processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data
                        , pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                }
                , cache: true
            }
        });

        $('#locationforsalary').select2({
            placeholder: 'Choose Location Mandatory'
            , allowClear: true
            , ajax: {
                dataType: 'json'
                , url: "{{URL::to('location_list_data')}}"
                , delay: 250
                , data: function(params) {
                    return {
                        term: params.term
                    }
                }
                , processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data
                    };
                }
                , cache: true
            }
        });

        $('.bonus').select2({
            placeholder: 'Enter an Bonus'
            , allowClear: true
            , ajax: {
                dataType: 'json'
                , url: "{{URL::to('bonus_name_list')}}"
                , delay: 250
                , data: function(params) {
                    return {
                        term: params.term
                    }
                }
                , processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data
                        , pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                }
                , cache: true
            }
        });

        $('.bank_name').select2({
            placeholder: 'Enter a bank name'
            , allowClear: true
            , ajax: {
                dataType: 'json'
                , url: "{{URL::to('get_bank_list')}}"
                , delay: 250
                , data: function(params) {
                    return {
                        term: params.term
                    }
                }
                , processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data
                        , pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                }
                , cache: true
            }
        });

        $('.depertment').select2({
            placeholder: 'Enter department'
            , allowClear: true
            , ajax: {
                dataType: 'json',
                 url: "{{ url('depertment_list_data') }}",
                  delay: 250,
                   data: function(params) {
                    return {
                        term: params.term
                    }
                }
                , processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data
                    };
                }
                , cache: true
            }
        });

        $('.designation').select2({
            placeholder: 'Enter designation'
            , allowClear: true
            , ajax: {
                dataType: 'json'
                , url: "{{URL::to('designation_list_data')}}"
                , delay: 250
                , data: function(params) {
                    return {
                        term: params.term
                    }
                }
                , processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data
                    };
                }
                , cache: true
            }
        });

        $('.category').select2({
            placeholder: 'Enter Category'
            , allowClear: true
            , ajax: {
                dataType: 'json'
                , url: "{{URL::to('category_list_data')}}"
                , delay: 250
                , data: function(params) {
                    return {
                        term: params.term
                    }
                }
                , processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data
                    };
                }
                , cache: true
            }
        });

        $('.working_shift').select2({
            placeholder: 'Enter working shift'
            , allowClear: true
            , ajax: {
                dataType: 'json'
                , url: "{{URL::to('shift_list_data')}}"
                , delay: 250
                , data: function(params) {
                    return {
                        term: params.term
                    }
                }
                , processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data
                    };
                }
                , cache: true
            }
        });

        $('.bonus_master').select2({
            placeholder: 'Enter Bonus Name'
            , allowClear: true
            , ajax: {
                dataType: 'json'
                , url: "{{URL::to('process_bonus_list')}}"
                , delay: 250
                , data: function(params) {
                    return {
                        term: params.term
                    }
                }
                , processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data
                        , pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                }
                , cache: true
            }
        });

        $('.bloodgroup_name').select2({
            placeholder: 'Enter Blood Group Name'
            , allowClear: true
            , ajax: {
                dataType: 'json'
                , url: "{{URL::to('blood_group_list_data')}}"
                , delay: 250
                , data: function(params) {
                    return {
                        term: params.term
                    }
                }
                , processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data
                    };
                }
                , cache: true
            }
        });

        $('.plant_name').select2({
            placeholder: 'Enter Plant Name'
            , allowClear: true
            , ajax: {
                dataType: 'json'
                , url: "{{URL::to('plantname_list_data')}}"
                , delay: 250
                , data: function(params) {
                    return {
                        term: params.term
                    }
                }
                , processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data
                    };
                }
                , cache: true
            }
        });

        $('.section').select2({
            placeholder: 'Enter Sub-department'
            , allowClear: true
            , ajax: {
                dataType: 'json'
                , url: "{{ URL::to('section_list_data')}}"
                , delay: 250
                , data: function(params) {
                    return {
                        term: params.term
                    }
                }
                , processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data
                    };
                }
                , cache: true
            }
        });

        $('.religion').select2({
            placeholder: 'Enter Religion'
            , allowClear: true
            , ajax: {
                dataType: 'json'
                , url: "{{URL::to('religion_list_data')}}"
                , delay: 250
                , data: function(params) {
                    return {
                        term: params.term
                    }
                }
                , processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data
                    };
                }
                , cache: true
            }
        });

        setDeclarationDate();

        $(document).on('change', '#salary_report_month, #locationforsalary, #yearforsalary', function(e) {
            setDeclarationDate();
        })

        function setDeclarationDate() {
            let hrm_month_id = $('#salary_report_month option:selected').val();
            let year_id = $("#yearforsalary option:selected").val();
            let hrm_location_id = $('#locationforsalary option:selected').val();
            let status = 1;

            $.get("{{ URL::to('/') }}/salary_master_listdata?hrm_month_id=" + hrm_month_id + "&year_id=" + year_id + "&hrm_location_id=" + hrm_location_id + "&status=" + status)
                .then((response) => {
                    if (response.length == 0) {
                        $('#salary_master option').remove();
                    } else {
                        $('#salary_master option').remove();

                        let options;
                        response.forEach(res => {
                            options += `<option value="${res.id}">${res.text}</option>`;
                        })

                        $('#salary_master').append(options);
                    }
                })
        }


        // $masterdata = $('#salary_master').select2({
        //     placeholder: 'Declaration Date',
        //     allowClear: true,
        //     ajax: {
        //     dataType: 'json',
        //     url: '{{URL::to('/')}}/salary_master_listdata',
        //     delay: 250,
        //     data: function(params) {
        //         return {
        //             term: params.term,
        //             hrm_month_id: $('#salary_report_month option:selected').val(),
        //             year_id: $(".yearforsalary option:selected" ).val(),
        //             hrm_location_id:$('#locationforsalary').val(),
        //             status:1,
        //         }
        //     },
        //     processResults: function (data, params) {
        //         params.page = params.page || 1;
        //             return {
        //                 results: data
        //             };
        //         },
        //         cache: true
        //     }
        // });


        $('.leave_type').select2({
            placeholder: 'Enter a Leave type'
            , allowClear: true
            , ajax: {
                dataType: 'json'
                , url: "{{URL::to('employee_leave_type')}}"
                , delay: 250
                , data: function(params) {
                    return {
                        term: params.term
                    }
                }
                , processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data
                        , pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                }
                , cache: true
            }
        });

        $('.slap_name').select2({
            placeholder: 'Enter a Salary Slap'
            , allowClear: true
            , ajax: {
                dataType: 'json'
                , url: '{{URL::to(' / ')}}/slap_name_list_data'
                , delay: 250
                , data: function(params) {
                    return {
                        term: params.term
                        , hrm_month_id: $('#hrm_month_id_slap').val()
                        , year_id: $('#year_id_slap').val()
                    }
                }
                , processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data
                    };
                }
                , cache: true
            }
        });

        $('.slap_name_bank_salary').select2({
            placeholder: 'Enter a Salary Slap'
            , allowClear: true
            , ajax: {
                dataType: 'json'
                , url: '{{URL::to(' / ')}}/slap_name_list_data'
                , delay: 250
                , data: function(params) {
                    return {
                        term: params.term
                        , hrm_month_id: $('#month_bank_salary').val()
                        , year_id: $('#year_bank_salary').val()
                    }
                }
                , processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data
                    };
                }
                , cache: true
            }
        });

        $('.declaration_date').select2({
            placeholder: 'SELECT Declaration Date...'
            , allowClear: true
            , ajax: {
                dataType: 'json'
                , url: "{{URL::to('process_bonus_date_list')}}"
                , delay: 250
                , data: function(params) {
                    return {
                        term: params.term
                    }
                }
                , processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data
                    };
                }
                , cache: true
            }
        });

        $('.payment_mode_result').hide();

        if ($(".payment_mode").val() == 2) {
            $('.payment_mode_result').show();
        } else {
            $('.payment_mode_result').hide();
        }

        $('.payment_mode').on('change', function() {
            if ($(".payment_mode").val() == 2) {
                $('.payment_mode_result').show();
            } else {
                $('.payment_mode_result').hide();
            }
        });
    });

</script>
@endsection
