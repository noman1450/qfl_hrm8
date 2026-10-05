<!-- create_bloodgroup -->
@extends('layouts.main')
@section('styles')
<link rel="stylesheet" href="{{ asset('plugins/datatables/dataTables.bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datepicker/datepicker3.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/timepicker/bootstrap-timepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2/select2.min.css') }}">

<style>
    /*This is for Coloring Heading */
    .with-border {
        animation-name: header;
        animation-duration: 6s;
        animation-iteration-count: infinite;
    }

    @keyframes header {
        0% {
            background-color: #99ccff;
        }

        25% {
            background-color: #ff9980;
        }

        50% {
            background-color: #ffe0b3;
        }

        100% {
            background-color: #8cd98c;
        }
    }

    /*End This is for Coloring Heading */


    /*This is for Text Box Style*/
    input[type="text"],
    select.form-control {
        background: transparent;
        border: none;
        border-bottom: 1px solid #000000;
        -webkit-box-shadow: none;
        box-shadow: none;
        border-radius: 0;
    }

    input[type="text"]:focus,
    select.form-control:focus {
        -webkit-box-shadow: none;
        box-shadow: none;
    }

    /*Hide DataTable search box */
    .dataTables_filter {
        display: none;
    }
</style>
@endsection

@section('content')
<section class="content">

    <!-- <p style="text-align:center; font-size: 30px; color: red; font-weight: bold; background-color: black;";><marquee>The system will automatically shut down on 10th July 2025 at 6:00 PM due to 3 months of pending bills.</marquee></p> -->

    @can('MainDashboardOption')

    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua">
                <div class="inner" style="display:flex;align-items:center;gap:10px;">
                    <div style="border-right:1px solid;padding-right:10px;">
                        <h3>{{ $dashboard_data[0]->location }}</h3>
                        <h4>Location</h4>
                    </div>

                    <div>
                        <h3>{{ $dashboard_data[0]->total_employee }}</h3>
                        <h4>Total Employee</h4>
                    </div>
                </div>
                <div class="icon">
                    <i class="ion ion-location"></i>
                </div>
                <a href="{{ URL::to('/common_dashboard/1') }}" class="small-box-footer">More info <i
                        class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-red">
                <div class="inner" style="display:flex;align-items:center;gap:10px;">
                    <div style="border-right:1px solid;padding-right:10px;">
                        <h3>{{ $dashboard_data[0]->count_category }}</h3>
                        <h4>Category</h4>
                    </div>

                    <div>
                        <h3>{{ $dashboard_data[0]->department }}</h3>
                        <h4>Total Department</h4>
                    </div>
                </div>
                <div class="icon">
                    <i class="ion ion-location"></i>
                </div>
                <a href="{{ URL::to('/common_dashboard/2') }}" class="small-box-footer">More info <i
                        class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>{{ $probation_employee[0]->probation_employee }}</h3>
                    <h4>Probation Employee</h4>
                </div>
                <div class="icon">
                    <i class="fa fa-group"></i>
                </div>
                <a href="{{ URL::to('/common_dashboard/3') }}" class="small-box-footer">More info <i
                        class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3>{{ $dashboard_data[0]->designation }}</h3>
                    <h4>Total Designation</h4>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="{{ URL::to('/common_dashboard/4') }}" class="small-box-footer">More info <i
                        class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

    </div>


        {{-- <div class="col-lg-3 col-xs-6">

                <div class="small-box bg-red">
                        <div class="inner">
                            <h3>{{$old_resign_data[0]->old_resign_data}}<sup style="font-size: 20px"></sup></h3>
                    <h4>Previous Month Resign</h4>
                    </div>
                    <div class="icon">
                    <i class="fa fa-codepen"></i>
                    </div>
                    <a href="{{ URL::to('/common_dashboard/5')}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>

            </div>

            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-green">
                <div class="inner">
                    <h3>{{ $old_join[0]->old_join}}</h3>
                    <h4>Previous Month Join</h4>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="{{ URL::to('/common_dashboard/6')}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-red">
                <div class="inner">
                    <h3>{{$new_resign_data[0]->new_resign_data}}<sup style="font-size: 20px"></sup></h3>
                    <h4>This Month Resign</h4>
                </div>
                <div class="icon">
                    <i class="fa fa-codepen"></i>
                </div>
                <a href="{{ URL::to('/common_dashboard/5')}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-green">
                <div class="inner">
                    <h3>{{ $new_join[0]->new_join}}</h3>
                    <h4>This Month New Join</h4>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="{{ URL::to('/common_dashboard/6')}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>

        --}}


    <!--           <div class="row ">

                <div class="col-md-6 mt-3">
                    <div class="card shadow border-0 mt-3">
                        <div class="card-body" style="--bs-card-spacer-y: 10px; --bs-card-spacer-x: 10px">
                            <div class="box box-primary">
                                <div class="box-header with-border" style="display: flex">
                                    <h5 class="box-title" style="font-size:16px;margin:auto">Last 15 Days Present</h5>

                                </div>
                                <div class="box-body ">
                                    <canvas id="ontimeChart" class="canvas" style="width:100%"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mt-3">
                    <div class="card shadow border-0 mt-3">
                        <div class="card-body" style="--bs-card-spacer-y: 10px; --bs-card-spacer-x: 10px">
                            <div class="box box-primary">
                                <div class="box-header with-border" style="display: flex">
                                    <h5 class="box-title" style="font-size:16px;margin:auto">Last 15 Days Absent</h5>

                                </div>
                                <div class="box-body ">
                                    <canvas id="absentChart" class="canvas" style="width:100%"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->

     @can('DashboardCostToTheCompany')
            <div class="row">
                
                <div class="form-group  col-lg-12 col-md-12 col-xs-12" style="font-size: 12px">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Cost To the company</h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                        class="fa fa-minus"></i></button>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-3 col-xs-12 form-group">
                            <label><span style="color:red;">Filter</span> </label>
                            <select class="form-control col-lg-12 onchange" id="filter_name_id" name="filter_name_id"
                                style="width: 100%;" required>
                            </select>
                        </div>



                        <div class="box-body">
                            <table id="cost_to_the_summary_list_datatable" class="table table-bordered table-hover"
                                cellspacing="0" width="100%">
                                <thead style="font-size: 12px;">
                                    <tr>
                                        <th style="width: 10%">Location Name</th>
                                        <th style="width: 10%">CountEmp</th>
                                        <th style="width: 05%">Gross Salary</th>
                                        <th style="width: 05%">Additional Pay</th>
                                        <th style="width: 05%">Total Cost</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 12px;">
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>

                            </table>
                        </div>
                    </div>
                </div>
                <div>
                

            </div>
    @endcan

    <div class="row">
                <div class="form-group  col-lg-12 col-md-12 col-xs-12" style="font-size: 12px">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Attendance Summary</h3>
                            <div class="box-tools pull-right">
                                <input type="text" class="form-control pull-right onchange bg-yellow" id="filter_date"
                                    data-date-format="dd-mm-yyyy" value="{{ date('d-m-Y') }}" readonly>
                            </div>
                        </div>

                        <div class="box-body">
                            <table id="attendance_summary_table" class="table table-bordered table-hover " cellspacing="0"
                                width="100%">
                                <thead>
                                    <tr style="text-align: center;">
                                        <th style="width: 20%">Location Name</th>
                                        <th style="width: 10%">Total Employee</th>
                                        <th style="width: 10%">OnTime</th>
                                        <th style="width: 10%">Late</th>
                                        <th style="width: 10%">Absent</th>
                                        <th style="width: 10%">EarlyOut</th>
                                        <th style="width: 10%">TotalLeave</th>
                                        <th style="width: 10%">OSD</th>
                                        <th style="width: 10%">Holiday</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>


                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>

                            </table>
                        </div>
                    </div>
                </div>
          
            @can('LeaveAproval')
                <div class="col-xs-4">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Pending Leave Request</h3>
                        </div>

                        <div class="box-body">
                            <table id="pending_leave_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th style="width: 70%">Location</th>
                                        <th style="width: 30%">Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($leave_pending as $item)
                                    <tr>
                                        <td> <a href="{{ url('leaveapprove?location_id=' . $item->hrm_location_id) }}">
                                                {{ $item->location_name }}
                                            </a>
                                        </td>
                                        <td style="text-align: end">{{ $item->pending_leave}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th style="text-align: end">{{ $leave_pending->sum('pending_leave') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endcan
            

            @can('PendingManualAttendance')
                <div class="col-xs-4">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Pending Manual Attendance</h3>
                        </div>

                        <div class="box-body">
                            <table id="pending_attendance_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th style="width: 70%">Location</th>
                                        <th style="width: 30%">Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th style="text-align: end">0</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endcan

            @can('EmployeeJoiningApproval')
                <div class="col-xs-4">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Pending Join</h3>
                        </div>

                        <div class="box-body">
                            <table id="pending_join_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th style="width: 80%">Location</th>
                                        <th style="width: 20%">Join</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th style="text-align: end">0</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endcan

            


            <div class="row ">
                <!--              <div class="col-md-6 mt-3">
                            <div class="card shadow border-0 mt-3">
                                <div class="card-body" style="--bs-card-spacer-y: 10px; --bs-card-spacer-x: 10px">
                                    <div class="box box-primary">
                                        <div class="box-header with-border" style="display: flex">
                                            <h5 class="box-title" style="font-size:16px;margin:auto">Last 7 Days Leave</h5>

                                        </div>
                                        <div class="box-body ">
                                            <canvas id="leaveChart" class="canvas" style="width:100%"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->

                {{-- <div class="col-md-6 mt-3">
                            <div class="box box-primary">
                                <div class="card shadow border-0 mt-3 set-height">
                                    <div class="card-body" style="--bs-card-spacer-y: 10px; --bs-card-spacer-x: 10px">
                                        <div class="box box-primary">
                                            <div class="box-header with-border" style="display: flex">
                                                <h5 class="box-title" style="font-size:16px;margin:auto">This Month Continued Late
                                                    <span class="badge-style">({{ count($continued_late) }})</span>

                </h5>
            </div>

            <div class="box-body mt-2 mb-2">
                <div class="set-inner-height" style="width:100%;height:300px;overflow-y:auto;">
                    <div class="card" style="width: 100%;">
                        <ul class="list-group list-group-flush">
                            @forelse ($continued_late as $data)
                            <li class="list-group-item">
                                <strong>{{ $data->employee_name }}</strong>
                                ({{ $data->employee_code }})
                                <br>
                                Late: {{ $data->late_this_month }} days<br>
                            </li>
                            @empty
                            <li class="list-group-item text-danger text-center">No late records
                                beyond 3 days!</li>
                            @endforelse
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>
    --}}



    </div>

    <div class="row">
        <div class="col-md-4 mt-3">

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Resign Vs Join</h3>
                    <div class="box-tools pull-right">
                        <input type="text" class="form-control pull-right bg-yellow" id="resign_join_graph_month"
                            value="{{ date('M-Y') }}" readonly>
                    </div>
                </div>
                <div class="box-body ">
                    <canvas id="resignJoinChart" style="width:100%;max-width:600px"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-4 mt-3">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">This Month Resign</h3>

                    <div class="box-tools pull-right">
                        <input type="text" class="form-control pull-right onchange bg-yellow" id="month_date"
                            value="{{ date('M-Y') }}" readonly>
                    </div>
                </div>

                <div class="box-body ">
                    <table id="resign_month_table" class="table table-bordered table-hover  " cellspacing="0"
                        width="100%">
                        <thead>
                            <tr>
                                <th style="width: 80%">Location</th>
                                <th style="width: 20%">Resign</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>

                        <tfoot>
                            <tr>
                                <th></th>
                                <th style="text-align: end">0</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xs-4">
            <div class="box box-primary d-flex justify-content-center">

                <div class="box-header with-border">
                    <h3 class="box-title">This Month Join</h3>

                    <div class="box-tools pull-right" style="width: 35%;float: right;">
                        <input type="text" class="form-control pull-right onchange bg-yellow" id="month_date_2"
                            value="{{ date('M-Y') }}" readonly>
                    </div>
                </div>

                <div class="box-body ">
                    <table id="join_month_table" class="table table-bordered table-hover  " cellspacing="0"
                        width="100%">
                        <thead>
                            <tr>
                                <th style="width: 80%">Location</th>
                                <th style="width: 20%">Join</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>

                        <tfoot>
                            <tr>
                                <th></th>
                                <th style="text-align: end">0</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        
    </div>

    {{-- <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Probation Employee(s) are waiting for confirmation</h3>

                  <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                  </div>
                </div>
                <div class="box-body">
                  <table id="list_table" class="table table-bordered table-hover " cellspacing="0" width="100%" >
                    <thead>
                      <tr>
                        <th style="width: 30%">Employee Name</th>
                        <th style="width: 15%">Department</th>
                        <th style="width: 10%">Designation</th>
                        <th style="width: 15%">Location</th>
                        <th style="width: 10%">Joining Date</th>
                        <th style="width: 10%">Probable Confirm Date</th>
                        <th style="width: 10%">Action</th>

                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
              </div> --}}
    </div>

    <div class="col-lg-5 col-md-8 col-xs-12" style="font-size: 12px">

        <!-- <div class="card shadow border-0 mt-3 set-table-height">
                        <div class="card-body " style="--bs-card-spacer-y: 10px; --bs-card-spacer-x: 10px">
                            <div class="box box-primary">

                                <div class="box-header with-border" style="display: flex">

                                    <h5 class="box-title" style="font-size:16px;">Daily Attendance Pie Chart</h5>



                                    <input type="text" style="width: 35%;margin-left:auto"
                                        class="did-floating-input " data-mask data-inputmask="'alias': ''"
                                        id="attendence_pie_month" name="attendence_pie_month"
                                        value="{{ date('d-M-Y') }}" placeholder=" ">

                                    <select name="num_percentage" id="num_percentage">
                                        <option value="1">%</option>
                                        <option value="2">Num</option>
                                    </select>

                                </div>

                                <div class="box-body mt-2 set-inner-table-height d-flex justify-content-center">
                                    <canvas id="attendencePieChart" class="canvas" style="width:100%"></canvas>
                                </div>
                            </div>
                        </div>
                    </div> -->

        <div class="row">
            

            
            </div>
        </div>

        <div class="row">
          

            {{-- <div class="box box-primary">
                        <div class="card shadow border-0 mt-3">
                            <div class="card-body" style="--bs-card-spacer-y: 10px; --bs-card-spacer-x: 10px">
                                <div class="box box-primary">
                                    <div class="box-header with-border " style="display: flex">
                                        <h5 class="box-title" style="font-size:16px;margin:auto">This Month Continued Absent
                                            <span class="badge-style">({{ count($continued_absent) }})</span></h5>
        </div>
        <div class="box-body mt-2 mb-2">
            <div style="width:100%;height:300px;overflow-y:auto;">
                <div class="card" style="width: 100%;">
                    <ul class="list-group list-group-flush">
                        @forelse ($continued_absent as $data)
                        <li class="list-group-item">
                            <strong>{{ $data->employee_name }}</strong>
                            ({{ $data->employee_code }})
                            <br>
                            Days: {{ $data->days }}<br>
                            Last Present: {{ $data->last_present }}
                        </li>
                        @empty
                        <li class="list-group-item text-danger text-center">No data found!</li>
                        @endforelse
                    </ul>
                </div>

            </div>
        </div>
    </div>
    </div>
    </div>

    </div>
    --}}





    {{-- <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Resignations Overview</h3>
                <div class="box-tools pull-right">
                    <input type="text" class="form-control pull-right bg-yellow" id="resign_graph_month" value="{{ date('M-Y') }}" readonly>
    </div>
    </div>
    <div class="box-body ">
        <canvas id="resignChart" style="width:100%;max-width:600px"></canvas>
    </div>
    </div> --}}



    {{-- <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">New Employee Joining</h3>
                <div class="box-tools pull-right">
                    <input type="text" class="form-control pull-right bg-yellow" id="join_graph_month" value="{{ date('M-Y') }}" readonly>
    </div>
    </div>
    <div class="box-body ">
        <canvas id="joinChart" style="width:100%;max-width:600px"></canvas>
    </div>
    </div> --}}


    
    </div>

    {{-- <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Personal Work Note</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                        class="fa fa-minus"></i></button>
                            </div>
                        </div>

                        <div class="box-body ">
                            <div class="col-md-10 col-xs-10">
                                <input type="text" class="form-control" maxlength="145" name="my_note" id="my_note"
                                    placeholder="Type Your Work Note.." autofocus>
                            </div>

                            <div class="col-md-2 col-xs-2">
                                <button type="button" id="add" class="fa fa-plus bg-aqua btn"
                                    style="font-size:20px;color:green"></button>
                            </div>

                            <table id="mynote_table" class="table table-bordered table-hover  " cellspacing="0"
                                width="100%">
                                <thead>
                                    <tr>
                                        <th style="width: 95%">Particulars</th>
                                        <th style="width: 5%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div> --}}

    {{-- <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Shortcut Link </h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
          </div>
          <div class="box-body">
            <div class="row">
              <div class="form-group col-lg-12 col-md-12 col-xs-12" style="font-size: 12px;">
                @foreach ($data as $key)
                    <ul>
                        <li><a href="{{$key->link_address}}"> {{$key->title}}</a></li>
    </ul>
    @endforeach
    </div>
    </div>
    </div>
    </div> --}}
    </div>
    </div>
    @endcan

    @endsection
    @section('script')
    <script src="{{ asset('plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('plugins/daterangepicker/moment.min.js') }}"></script>
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('plugins/datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('js/Chart.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="{{ asset('plugins/select2/select2.full.min.js') }}"></script>

    <script>
        $(document).ready(function($) {
            $('#filter_date').datepicker({
                autoclose: true
            });

            $('#resign_graph_month').datepicker({
                autoclose: true,
                format: "M-yyyy",
                minViewMode: 1
            });

            $('#resign_join_graph_month').datepicker({
                autoclose: true,
                format: "M-yyyy",
                minViewMode: 1
            });

            $('#join_graph_month').datepicker({
                autoclose: true,
                format: "M-yyyy",
                minViewMode: 1
            });

            $('#month_date').datepicker({
                autoclose: true,
                format: "M-yyyy",
                minViewMode: 1
            });

            $('#month_date_2').datepicker({
                autoclose: true,
                format: "M-yyyy",
                minViewMode: 1
            });

            $('#attendence_pie_month').datepicker({
                autoclose: true,
                format: "dd-M-yyyy"
            });

            $custom_filter = select2Dropdown("#filter_name_id", "{{ url('/filter_name_list') }}",
                "Enter Filter Name");

            $custom_filter.on('select2:close', function(e) {
                getCostdataLoad();
            });

            $custom_filter.on('select2:unselect', function(e) {
                getCostdataLoad();
            });

            function attendanceSummaryTable(params) {
                attendance_summary_table = $('#attendance_summary_table').DataTable({
                    destroy: true,
                    paging: true,
                    searching: true,
                    ordering: true,
                    bInfo: true,
                    // scrollX: true,
                    aoColumnDefs: [{
                        "className": "text-center",
                        "targets": [1, 2, 3, 4, 5, 6, 7, 8]
                    }],

                    "footerCallback": function(row, data, start, end, display) {
                        var api = this.api(),
                            data;
                        var intVal = function(i) {
                            return typeof i === 'string' ?
                                i.replace(/[\$,]/g, '') * 1 :
                                typeof i === 'number' ?
                                i : 0;
                        };


                        total_employee = api
                            .column(1)
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);


                        ontime = api
                            .column(2)
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);

                        late = api
                            .column(3)
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);
                        absent = api
                            .column(4)
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);
                        early_out = api
                            .column(5)
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);
                        total_leave = api
                            .column(6)
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);
                        osd = api
                            .column(7)
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);
                        holiday = api
                            .column(8)
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);




                        $(api.column(0).footer()).html('Total : ');
                        $(api.column(1).footer()).html(total_employee);
                        $(api.column(2).footer()).html(ontime);
                        $(api.column(3).footer()).html(late);
                        $(api.column(4).footer()).html(absent);
                        $(api.column(5).footer()).html(early_out);
                        $(api.column(6).footer()).html(total_leave);
                        $(api.column(7).footer()).html(osd);
                        $(api.column(8).footer()).html(holiday);
                    },

                    ajax: {
                        url: "{{ url('attendance_summary_by_location') }}",
                        type: "POST",
                        dataType: "json",
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            filter_date: $('#filter_date').val()
                        }
                    },

                    columns: [{
                            "data": "BranchName"
                        },
                        {
                            "data": "total_employee"
                        },
                        {
                            "data": "ontime"
                        },
                        {
                            "data": "late"
                        },
                        {
                            "data": "absent"
                        },
                        {
                            "data": "early_out"
                        },
                        {
                            "data": "total_leave"
                        },
                        {
                            "data": "osd"
                        },
                        {
                            "data": "holiday"
                        },
                    ],
                });
            }

            attendanceSummaryTable()

            $("#filter_date").change(function() {
                attendanceSummaryTable();
            });

            $("#month_date").change(function() {
                resignThisMonthTable()
            });

            resignThisMonthTable()

            function resignThisMonthTable() {
                $('#resign_month_table').DataTable({
                    destroy: true,
                    paging: true,
                    searching: true,
                    ordering: true,
                    bInfo: true,
                    aoColumnDefs: [{
                        "className": "text-right",
                        "targets": [1]
                    }],

                    "footerCallback": function(row, data, start, end, display) {
                        var api = this.api(),
                            data;
                        var intVal = function(i) {
                            return typeof i === 'string' ?
                                i.replace(/[\$,]/g, '') * 1 :
                                typeof i === 'number' ?
                                i : 0;
                        };

                        total_resign = api
                            .column(1)
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);

                        $(api.column(0).footer()).html('Total: ');
                        $(api.column(1).footer()).html(total_resign);
                    },

                    ajax: {
                        url: "{{ url('this_month_resign_employee') }}",
                        type: "POST",
                        dataType: "json",
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            month_date: $('#month_date').val()
                        }
                    },

                    columns: [{
                            "data": "Link"
                        },
                        {
                            "data": "total_resign"
                        },
                    ],
                });
            }

            $("#month_date_2").change(function() {
                joinThisMonthTable()
            });

            joinThisMonthTable()

            function joinThisMonthTable() {
                $('#join_month_table').DataTable({
                    destroy: true,
                    paging: true,
                    searching: true,
                    ordering: true,
                    bInfo: true,
                    aoColumnDefs: [{
                        "className": "text-right",
                        "targets": [1]
                    }],

                    "footerCallback": function(row, data, start, end, display) {
                        var api = this.api(),
                            data;
                        var intVal = function(i) {
                            return typeof i === 'string' ?
                                i.replace(/[\$,]/g, '') * 1 :
                                typeof i === 'number' ?
                                i : 0;
                        };

                        total_resign = api
                            .column(1)
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);

                        $(api.column(0).footer()).html('Total: ');
                        $(api.column(1).footer()).html(total_resign);
                    },

                    ajax: {
                        url: "{{ url('this_month_join_employee') }}",
                        type: "POST",
                        dataType: "json",
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            month_date: $('#month_date_2').val()
                        }
                    },

                    columns: [{
                            "data": "Link"
                        },
                        {
                            "data": "total_join"
                        },
                    ],
                });
            }

            loadPendingJoin()

            function loadPendingJoin() {
                $.ajax({
                    url: "{{ url('pending_join') }}",
                    type: "POST",
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        var html = '';
                        var total = 0;
                        $.each(data.data, function(i, item) {
                            total += parseInt(item.total_employee);
                            html += '<tr>' +
                                '<td>' + item.Link + '</td>' +
                                '<td style="text-align: end">' + item.total_employee + '</td>' +
                                '</tr>';
                        });
                        $('#pending_join_table tbody').html(html);
                        $('#pending_join_table tfoot th').eq(1).html(total);
                    }
                });
            }

            loadPendingAttendance()

            function loadPendingAttendance() {
                $.ajax({
                    url: "{{ url('pending_attendance_request') }}",
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        var html = '';
                        var total = 0;
                        $.each(data, function(i, item) {
                            total += parseInt(item.pending_attendance);
                            html += '<tr>' +
                                '<td><a href="{{ url('pending_manual_attendance') }}?location=' + item.hrm_location_id + '">' + item.location_name + '</a></td>' +
                                '<td style="text-align: end">' + item.pending_attendance + '</td>' +
                                '</tr>';
                        });
                        $('#pending_attendance_table tbody').html(html);
                        $('#pending_attendance_table tfoot th').eq(1).html(total);
                    }
                });
            }

            $.ajax({
                type: 'POST',
                url: "{{ URL::to('/') }}/provision_employeelist",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(data) {
                    var dataSet = data.data;
                    table = $('#list_table').DataTable({
                        destroy: true,
                        paging: true,
                        searching: true,
                        ordering: true,
                        bInfo: true,
                        scrollX: true,

                        "data": dataSet,
                        "columns": [{
                                "data": "Link",
                                "mRender": function(data, type, full) {
                                    return '<a target="_blank"  href="{{ URL::to('employeeinfo') }}/' +
                                        full.id + '">' + full.employee_name + '</a>';
                                }
                            },
                            {
                                "data": "depertment_name"
                            },
                            {
                                "data": "designation_name"
                            },
                            {
                                "data": "location_name"
                            },
                            {
                                "data": "joining_date"
                            },
                            {
                                "data": "probable_date"
                            },
                            {
                                "data": "Link",
                                "mRender": function(data, type, full) {
                                    return '<a href="{{ URL::to(' / ') }}/probation/' +
                                        full.id +
                                        '"  class="btn btn-info btn-sm btn-flat"><span class="glyphicon glyphicon-ok">Pending</a>';
                                }
                            },


                        ],
                        "order": [
                            [1, 'asc']
                        ]
                    });
                }
            });

            $('#add').click(function(event) {
                var _token = $("input[name='_token']").val();
                var mynote = $("#my_note").val();
                $.ajax({
                    type: 'POST',
                    url: '{{ URL::to(' / ') }}/mynote_store',
                    data: {
                        _token: _token,
                        mynote: mynote
                    },
                    success: function(data) {
                        if (data.massages == true) {
                            $("#my_note").val('');
                            dataLoad();
                        } else {

                        }
                    }
                });
            });


            dataLoadMynote = function() {

                $.ajax({
                    type: 'POST',
                    url: "{{ URL::to('/') }}/mynote_list",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(data) {
                        var dataSet = data.data;
                        mytable = $('#mynote_table').DataTable({
                            destroy: true,
                            paging: false,
                            searching: false,
                            ordering: true,
                            bInfo: false,
                            // columnDefs: [
                            // { "width": "150px", "targets": [0] },
                            // { "width": "40px", "targets": [1] }
                            // ],
                            // fixedColumns: true,
                            "data": dataSet,
                            "columns": [{
                                    "data": "my_note"
                                },
                                {
                                    "data": "Link",
                                    "mRender": function(data, type, full) {
                                        return '<button type="button" class="fa fa-trash delete-button btn btn-flat"  style="font-size:14px;color:red" id="' +
                                            full.id + '" ></button>';
                                    }
                                },
                            ],
                            "order": [
                                [1, 'asc']
                            ]
                        });
                    }
                });
            }

            dataLoadMynote();


            $('#mynote_table tbody').on('click', '.delete-button', function() {
                var delete_object = $(this).parents('tr');
                $.ajax({
                    method: 'GET',
                    url: '{{ URL::to(' / ') }}/my_note/' + $(this).attr('id') + '/cancel',
                    dataType: 'json',
                    success: function(data) {
                        console.log(data.massages);
                        if (data.massages == true) {
                            mytable.row(delete_object).remove().draw();

                        } else {

                        }
                    }
                });
            })

            getCostdataLoad = function() {
                console.log('getCostdataLoad called');
                var table = $('#cost_to_the_summary_list_datatable').DataTable({
                    "destroy": true,
                    "processing": true,
                    "serverSide": false,
                    "searching": false,
                    "ordering": false,
                    "bInfo": true,
                    "paging": false,
                    "pageLength": 100,
                    "width": '100%',
                    "scrollY": 'calc(70vh - 340px)',
                    "dom": 'Bfrtip',
                    buttons: [
                        'excel', 'pdf'
                    ],
                    "footerCallback": function(row, data, start, end, display) {
                        var api = this.api(),
                            data;
                        var intVal = function(i) {
                            return typeof i === 'string' ?
                                i.replace(/[\$,]/g, '') * 1 :
                                typeof i === 'number' ?
                                i : 0;
                        };


                        total_amount9 = api
                            .column(2)
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);


                        total_amount10 = api
                            .column(3)
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);

                        total_amount11 = api
                            .column(4)
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);




                        $(api.column(2).footer()).html(total_amount9);
                        $(api.column(3).footer()).html(total_amount10);
                        $(api.column(4).footer()).html(total_amount11);
                    },

                    "ajax": {
                        "url": "{{ URL::to('/') }}/cost_to_the_company_summary_data",
                        "type": "POST",
                        "headers": {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        "data": {
                            month_from: $("#month_from").val(),
                            month_to: $("#month_to").val(),
                            hrm_location_id: $("#location option:selected").val(),
                            hrm_category_id: $("#employee_category option:selected").val(),
                            filter_name_id: $('#filter_name_id option:selected').val()

                        }
                    },

                    "columns": [

                        {
                            "data": "Link",
                            "mRender": function(data, type, full) {
                                return '<a   href="{{ URL::to(' / ') }}/cost_to_the_company_location?year_month=' +
                                    full.year_months + '&filter_name_id=' + $('#filter_name_id')
                                    .val() + '&filter_name=' + $(
                                        '#filter_name_id option:selected').text() + '">' + full
                                    .location_name + '</a>';
                            }
                        },
                        {
                            "data": "NoOfEmployee"
                        },
                        {
                            "data": "gross_salary"
                        },
                        {
                            "data": "additional_payment"
                        },
                        {
                            "data": "existing_cost"
                        }



                    ]
                });

            };

            getCostdataLoad();


            $(document).on('change', '#resign_join_graph_month', function() {
                loadResignJoinChartData();
            });

            function loadResignJoinChartData() {
                const month_date = $("#resign_join_graph_month").val();
                const url = `{{ url('this_month_resign_join_employee_for_graph') }}?month_date=${month_date}`;

                $.get(url, function(returnData) {
                    const levels = returnData.map(item => item.month);
                    const totalResignData = returnData.map(item => item.total_resign);
                    const totalJoinData = returnData.map(item => item.total_join);
                    // console.log( levels,resign_data,join_data)
                    dyanmicResignJoinChart('#resignJoinChart', levels, totalResignData, totalJoinData,
                        'Resign VS Join Employees Overview');
                });
            }

            loadResignJoinChartData()

            function dyanmicResignJoinChart(id, labels, resignData, joinData, title) {
                const ctx = $(id)[0].getContext('2d');
                if (window.myChart) {
                    window.myChart.destroy();
                }

                window.myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                                label: 'Resign',
                                data: resignData,
                                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Join',
                                data: joinData,
                                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            datalabels: {
                                anchor: 'center', // Place labels inside the bars
                                align: 'center', // Align text in the middle
                                color: 'white', // Change text color for visibility
                                font: {
                                    weight: 'bold',
                                    size: 14
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    },
                    plugins: [ChartDataLabels]
                });
            }


            // function lastFifteenOnTime() {

            //     const url = `{{ url('last_fifteen_ontime_graph') }}`;

            //     $.get(url, function(returnData) {

            //         const ontime = returnData.map(item => item.ontime);
            //         const punchDate = returnData.map(item => item.punche_date);

            //         // Get the canvas
            //         const ctx = document.getElementById('ontimeChart');

            //         new Chart(ctx, {
            //             type: 'bar',
            //             data: {
            //                 labels: punchDate,
            //                 datasets: [{
            //                     label: 'Present',
            //                     data: ontime,
            //                     borderWidth: 1,
            //                     backgroundColor: '#248f24', // Bar color
            //                 }]
            //             },
            //             options: {
            //                 responsive: true,
            //                 plugins: {
            //                     datalabels: {
            //                         anchor: 'center', // Place labels inside the bars
            //                         align: 'center', // Align text in the middle
            //                         color: 'white', // Change text color for visibility
            //                         font: {
            //                             weight: 'bold',
            //                             size: 14
            //                         }
            //                     }
            //                 },
            //                 scales: {
            //                     y: {
            //                         beginAtZero: true
            //                     }
            //                 }
            //             },
            //             plugins: [ChartDataLabels] // Enable DataLabels plugin
            //         });

            //     });

            // }
            // lastFifteenOnTime();



            // function lastFifteenAbsent() {

            //     const url = `{{ url('last_fifteen_absent_graph') }}`;

            //     $.get(url, function(returnData) {

            //         const absent = returnData.map(item => item.absent);
            //         const punchDate = returnData.map(item => item.punche_date);

            //         //Chart code
            //         const ctx = document.getElementById('absentChart');
            //         new Chart(ctx, {
            //             type: 'bar',
            //             data: {
            //                 labels: punchDate,
            //                 datasets: [{
            //                     label: 'Absent',
            //                     data: absent,
            //                     borderWidth: 1,
            //                     backgroundColor: '#ff3333', // Bar color
            //                 }]
            //             },
            //             options: {
            //                 responsive: true,
            //                 plugins: {
            //                     datalabels: {
            //                         anchor: 'center', // Place labels inside the bars
            //                         align: 'center', // Align text in the middle
            //                         color: 'white', // Change text color for visibility
            //                         font: {
            //                             weight: 'bold',
            //                             size: 14
            //                         }
            //                     }
            //                 },
            //                 scales: {
            //                     y: {
            //                         beginAtZero: true
            //                     }
            //                 }
            //             },
            //             plugins: [ChartDataLabels] // Enable DataLabels plugin

            //         });

            //     });

            // }
            // lastFifteenAbsent();

            // function lastFifteenLeave() {

            //     const url = `{{ url('last_fifteen_leave_graph') }}`;

            //     $.get(url, function(returnData) {

            //         const leavedays = returnData.map(item => item.leavedays);
            //         const punchDate = returnData.map(item => item.punche_datee);

            //         //Chart code
            //         const ctx = document.getElementById('leaveChart');
            //         new Chart(ctx, {
            //             type: 'bar',
            //             data: {
            //                 labels: punchDate,
            //                 datasets: [{
            //                     label: 'Leave',
            //                     data: leavedays,
            //                     borderWidth: 1
            //                 }]
            //             },
            //             options: {
            //                 responsive: true,
            //                 plugins: {
            //                     datalabels: {
            //                         anchor: 'center', // Place labels inside the bars
            //                         align: 'center', // Align text in the middle
            //                         color: 'white', // Change text color for visibility
            //                         font: {
            //                             weight: 'bold',
            //                             size: 14
            //                         }
            //                     }
            //                 },
            //                 scales: {
            //                     y: {
            //                         beginAtZero: true
            //                     }
            //                 }
            //             },
            //             plugins: [ChartDataLabels] // Enable DataLabels plugin
            //         });

            //     });

            // }
            // lastFifteenLeave();


            // $(document).on('change', '#attendence_pie_month, #num_percentage', function() {
            //     if ($("#attendence_pie_month").length) {
            //         attendencePieChart();
            //     }
            // });

            // let attendenceChartInstance = null;

            // function attendencePieChart() {

            //     const num_percentage = $("#num_percentage").val();
            //     const att_month_date = $("#attendence_pie_month").val();
            //     const url =
            //         `{{ url('attendence_pie_chart') }}?month_date=${att_month_date}&&num_percentage=${num_percentage}`;

            //     $.get(url, function(returnData) {



            //         const colorCode = returnData.map(item => item.color_code);
            //         const alies = returnData.map(item => item.alies);


            //         const attendenceDate = returnData.map(item => item.attendance_date);


            //         let value = [];

            //         if (returnData.length > 0) {
            //             if (returnData[0].hasOwnProperty('ontime')) {
            //                 value = returnData.map(item => item.ontime);
            //             } else if (returnData[0].hasOwnProperty('percentage')) {
            //                 value = returnData.map(item => item.percentage);
            //             } else {
            //                 console.log("Dataset does not contain 'ontime' or 'percentage'");
            //             }
            //         }

            //         if (attendenceChartInstance !== null) {
            //             attendenceChartInstance.destroy();
            //         }

            //         // Chart code
            //         const ctx = document.getElementById('attendencePieChart');
            //         attendenceChartInstance = new Chart(ctx, {
            //             type: 'pie',
            //             data: {
            //                 labels: alies,
            //                 datasets: [{
            //                     label: ' ',
            //                     data: value,
            //                     backgroundColor: colorCode,
            //                     borderWidth: 1
            //                 }]
            //             },
            //             options: {
            //                 responsive: true,
            //                 plugins: {
            //                     datalabels: {
            //                         anchor: 'center',
            //                         align: 'end',
            //                         color: 'black',
            //                         rotation: 315,
            //                         font: {
            //                             weight: 'bold',
            //                             size: 14
            //                         },
            //                         formatter: function(value, context) {
            //                             if (value.toString().includes('.')) {
            //                                 return value + '%'; // If decimal exists, add '%'
            //                             } else {
            //                                 return value;
            //                             }
            //                         }
            //                     },
            //                     legend: {
            //                         position: 'right',
            //                     },

            //                 }
            //             },
            //             plugins: [ChartDataLabels]
            //         });

            //     });

            // }
            // attendencePieChart();



        });
    </script>
    @endsection