<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>HRM</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{asset('bootstrap/css/bootstrap.min.css')}}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('bower_components/font-awesome/css/font-awesome.min.css')}}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{asset('bower_components/Ionicons/css/ionicons.min.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('dist/css/AdminLTE.css')}}">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
    folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{asset('dist/css/skins/_all-skins.min.css')}}">
    <!-- select2 css -->

    <link rel="stylesheet" href="{{ asset('toaster/bootoast.css') }}">

    @yield('styles')


    <style>
        .modal-dialog-centered {
            display: -ms-flexbox;
            display: flex;
            -ms-flex-align: center;
            align-items: center;
            min-height: calc(100% - (0.5rem * 2));
        }

        /* @media (min-width: 992px) { */
        .modal-xl {
            width: 95%;
            max-width: 1440px;
        }

        /* } */

        @media (min-width: 576px) {
            .modal-dialog-centered {
                min-height: calc(100% - (1.75rem * 2));
            }
        }
    </style>

</head>
{{-- <body class="hold-transition trpo fixed sidebar-mini"> --}}
<body class="hold-transition skin-blue fixed sidebar-mini">

<div class="wrapper">
    <header class="main-header app-bg-prymary">
        <!-- Logo -->
        <a href="{{URL::to('/home')}}" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>H</b>R</span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b>HR</b>M</span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <span class="hidden-xs">{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="user-footer">
                                <div class="pull-right">
                                    <a href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                          style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>

                            <li class="user-footer">
                                <div class="pull-right">
                                    <a href="{{URL::to('resetmypassword')}}"> <i class="fa "></i>Change Password</a>
                                </div>
                            </li>

                            <li class="user-footer">
                                <div class="pull-right">
                                    <a href="{{URL::to('/favourite_link')}}"> <i class="fa "></i>Shortcut Link Setup</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <ul class="sidebar-menu" data-widget="tree">
                <li class="header">MAIN NAVIGATION</li>

                @can('MainDashboardOption')
                    <li {!! Request::is('home', 'attendance_summary_data_location', 'attendance_summary_data_department') ? ' class="active"' : null !!}>
                        <a href="{{URL::to('/home')}}">
                            <i class="fa fa-dashboard"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                @endcan
                @can('MainAdminOption')
                    <li {!! Request::is('role','role/*','defaultsalaryheadsetup', 'employee_joining_approvals','report_signatory', 'employee_joining_approvals/*', 'add_new_employee','add_new_employee/*', 'plant','plant/*','designation','designation/*','depertment','depertment/*','category','category/*','shift','shift/*','bloodgroup','bloodgroup/*','religion','religion/*','resignation_type','resignation_type/*','maritalstatus','maritalstatus/*','employee','location','section','section/*','employeejoin','leavetype','leavetype/*','holiday','holiday/*','bulkdataupload','employeebulkdataupload','employeecard','employeetransfer','reporting_boss_change','reporting_boss_change/create','reportingboss_change_list','employeeresign','salarygrade','salaryheadgroup','salaryhead','salaryhead/*','salarygradesetup','salarygradesetup/*','leaveyear','leaveyear/*','leavetypeyear','leavetypeyear/*','shiftrole','shiftrole/*','employeecard/create','employee/create','bonus','bonus/create','employeepromotion','plant','plant_details','users','user_role','assigned_roles','role_permission','role','permission','permission/create','users/create','salaryincrement','salaryincrementpreviousdata','employeeinactive','employeeinactive/create','reactive','employeeresign/create','resignapproved','employeeidcard','employeelongservice','employeelongservice/create','employeetransfercreate','employeepromotioncreate','submitemployeeidcard','service_completed_list','plantwiseemployeelist','plantwiseemployeeadd','plantwithsection','plantwithsection/create','interval_time','device_config','overtime_config','weekly_holiday_config','weekly_holiday_config_create','salaryincrement/create', 'weekend_config','custom_filter','custom_employee_list', 'salary_holdup', 'salary_holdup/*', 'probation_employee') ? ' class="active treeview"' : ' class="treeview"' !!}>
                        <a href="#">
                            <i class="fa fa-user-md"></i>
                            <span>Admin</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @can('AdminConfiguration')
                                <li {!! Request::is('defaultsalaryheadsetup','plant','plant/create','designation','report_signatory','designation/*','plant/create','depertment','depertment/create','category','category/create','shift','shift/create','bloodgroup','bloodgroup/create','religion','religion/create','resignation_type','resignation_type/*','maritalstatus','maritalstatus/create','location','location/create','section','section/create','leavetype','leavetype/create','holiday','holiday/create','bulkdataupload','employeebulkdataupload','leaveyear','leaveyear/create','leavetypeyear','leavetypeyear/create','shiftrole','shiftrole/create','salarygrade','salaryheadgroup','salaryhead','salaryhead/*','salarygradesetup','salarygradesetup/*','bonus','bonus/create','plantwithsection','plantwithsection/create','interval_time','device_config','overtime_config','weekly_holiday_config','weekly_holiday_config_create', 'weekend_config', 'salary_holdup', 'salary_holdup/*') ? ' class="active treeview"' : ' class="treeview"' !!} >
                                    <a href="#"><i class="fa fa-random"></i> Configuration
                                        <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                    </a>
                                    <ul class="treeview-menu">
                                        <li {!! Request::is('interval_time','device_config','overtime_config','weekly_holiday_config','weekly_holiday_config_create', 'weekend_config') ? ' class="active treeview"' : ' class="treeview"' !!} >
                                            <a href="#"><i class="fa fa-random"></i>Base Configuration
                                                <span class="pull-right-container">
                                                <i class="fa fa-angle-left pull-right"></i>
                                            </span>
                                            </a>
                                            <ul class="treeview-menu">
                                                <li {!! Request::is('interval_time') ? '            class="active"' : null !!}>
                                                    <a href="{{URL::to('interval_time')}}"> <i
                                                            class="fa fa-circle-o"></i>Interval Time</a></li>

                                                <li {!! Request::is('device_config') ? '            class="active"' : null !!}>
                                                    <a href="{{URL::to('device_config')}}"> <i
                                                            class="fa fa-circle-o"></i>Device Config</a></li>
                                                <li {!! Request::is('overtime_config') ? '          class="active"' : null !!}>
                                                    <a href="{{URL::to('overtime_config')}}"> <i
                                                            class="fa fa-circle-o"></i>Overtime Config</a></li>
                                                <li {!! Request::is('weekly_holiday_config','weekly_holiday_config_create') ? '            class="active"' : null !!}>
                                                    <a href="{{URL::to('weekly_holiday_config')}}"> <i
                                                            class="fa fa-circle-o"></i>Weekly Holiday Config</a></li>
                                                <li {!! Request::is('weekend_config') ? '           class="active"' : null !!}>
                                                    <a href="{{URL::to('weekend_config')}}"> <i
                                                            class="fa fa-circle-o"></i>Weekend Config</a></li>
                                            </ul>
                                        </li>
                                        <li {!! Request::is('religion','religion/create') ? '            class="active"' : null !!}>
                                            <a href="{{URL::to('religion')}}"> <i
                                                    class="fa fa-circle-o"></i>Religion</a>
                                        </li>

                                        <li {!! Request::is('resignation_type','resignation_type/*') ? 'class="active"' : null !!}>
                                            <a href="{{URL::to('resignation_type')}}"> <i
                                                    class="fa fa-circle-o"></i>Resignation Type</a>
                                        </li>
                                        <li {!! Request::is('bloodgroup','bloodgroup/create') ? '        class="active"' : null !!}>
                                            <a href="{{URL::to('bloodgroup')}}"> <i class="fa fa-circle-o"></i>Blood
                                                Group</a></li>
                                        <li {!! Request::is('maritalstatus','maritalstatus/create') ? '  class="active"' : null !!}>
                                            <a href="{{URL::to('maritalstatus')}}"> <i class="fa fa-circle-o"></i>Marital
                                                Status</a></li>
                                        <li {!! Request::is('depertment','depertment/create') ? '        class="active"' : null !!}>
                                            <a href="{{URL::to('depertment')}}"> <i class="fa fa-circle-o"></i>Department</a>
                                        </li>

                                        <li {!! Request::is('section','section/create') ? '              class="active"' : null !!}>
                                            <a href="{{URL::to('section')}}"> <i class="fa fa-circle-o"></i>Sub-department</a>
                                        </li>


                                        <li {!! Request::is('designation','designation/create') ? '      class="active"' : null !!}>
                                            <a href="{{URL::to('designation')}}"> <i class="fa fa-circle-o"></i>Designation</a>
                                        </li>
                                        <li {!! Request::is('category','category/create') ? '            class="active"' : null !!}>
                                            <a href="{{URL::to('category')}}"> <i
                                                    class="fa fa-circle-o"></i>Category</a></li>
                                        @can('LocationSetup')
                                            <li {!! Request::is('location','location/create') ? '            class="active"' : null !!}>
                                                <a href="{{URL::to('location')}}"> <i class="fa fa-circle-o"></i>Location</a>
                                            </li>
                                        @endcan
                                        <li {!! Request::is('plant','plant/create') ? '                  class="active"' : null !!}>
                                            <a href="{{URL::to('plant')}}"> <i class="fa fa-circle-o"></i>Plant
                                                Setup</a></li>

                                        @can('PlantWiseSubDept')
                                            <li {!! Request::is('plantwithsection','plantwithsection/create') ? 'class="active"' : null !!}>
                                                <a href="{{URL::to('plantwithsection')}}"> <i
                                                        class="fa fa-circle-o"></i>Plant Wise Sub.Dept</a></li>
                                        @endcan
                                        <li {!! Request::is('shift','shift/create') ? '                  class="active"' : null !!}>
                                            <a href="{{URL::to('shift')}}"> <i class="fa fa-circle-o"></i>Work
                                                Shifts</a></li>
                                        <li {!! Request::is('shiftrole','shiftrole/create') ? '          class="active"' : null !!}>
                                            <a href="{{URL::to('shiftrole')}}"> <i class="fa fa-circle-o"></i>Shift Role</a>
                                        </li>
                                        <li {!! Request::is('leavetype','leavetype/create') ? '          class="active"' : null !!}>
                                            <a href="{{URL::to('leavetype')}}"> <i class="fa fa-circle-o"></i>Leave Type</a>
                                        </li>
                                        <li {!! Request::is('leaveyear','leaveyear/create') ? '          class="active"' : null !!}>
                                            <a href="{{URL::to('leaveyear')}}"> <i class="fa fa-circle-o"></i>Leave Year
                                                Setup</a></li>
                                        <li {!! Request::is('leavetypeyear','leavetypeyear/create') ? '  class="active"' : null !!}>
                                            <a href="{{URL::to('leavetypeyear')}}"> <i class="fa fa-circle-o"></i>Yearly
                                                Leave Type Setup</a></li>
                                        <li {!! Request::is('holiday','holiday/create') ? '              class="active"' : null !!}>
                                            <a href="{{URL::to('holiday')}}"> <i class="fa fa-circle-o"></i>Holiday
                                                Setup</a></li>
                                        <li {!! Request::is('salary_holdup','salary_holdup/*') ? '           class="active"' : null !!}>
                                            <a href="{{URL::to('salary_holdup')}}"> <i class="fa fa-circle-o"></i>Salary Holdup Types</a></li>
                                        <li {!! Request::is('bulkdataupload','employeebulkdataupload') ? '                        class="active"' : null !!}>
                                            <a href="{{URL::to('bulkdataupload')}}"> <i class="fa fa-circle-o"></i>Bulk Data Upload</a></li>

                                        @can('AdminSalaryConfiguration')
                                            @if (Config::get('module_config.payroll_module') == 1)
                                                <li {!! Request::is('defaultsalaryheadsetup','salarygrade','salaryheadgroup','salaryhead','salaryhead/*','salarygradesetup','salarygradesetup/*','bonus','bonus/create','pf_confiq') ? ' class="active treeview"' : ' class="treeview"' !!} >
                                                    <a href="#"><i class="fa fa-random"></i>Salary Configuration
                                                        <span class="pull-right-container">
                                                <i class="fa fa-angle-left pull-right"></i>
                                            </span>
                                                    </a>
                                                    <ul class="treeview-menu">
                                                        <li {!! Request::is('salaryheadgroup') ? '        class="active"' : null !!}>
                                                            <a href="{{URL::to('salaryheadgroup')}}"> <i
                                                                    class="fa fa-circle-o"></i>Salary Head Group</a>
                                                        </li>
                                                        <!-- <li {!! Request::is('pf_confiq') ? '          class="active"' : null !!}>   <a href="{{URL::to('providentfundconfiqu_data')}}">                    <i class="fa fa-circle-o"></i>PF Config</a></li> -->
                                                        <li {!! Request::is('salaryhead','salaryhead/*') ? '             class="active"' : null !!}>
                                                            <a href="{{URL::to('salaryhead')}}"> <i
                                                                    class="fa fa-circle-o"></i>Add Salary Head</a></li>
                                                        <li {!! Request::is('salarygrade') ? '            class="active"' : null !!}>
                                                            <a href="{{URL::to('salarygrade')}}"> <i
                                                                    class="fa fa-circle-o"></i>Salary Grade</a></li>
                                                        <li {!! Request::is('salarygradesetup','salarygradesetup/*') ? '       class="active"' : null !!}>
                                                            <a href="{{URL::to('salarygradesetup')}}"> <i
                                                                    class="fa fa-circle-o"></i>Salary Grade Setup</a>
                                                        </li>
                                                        <li {!! Request::is('defaultsalaryheadsetup') ? ' class="active"' : null !!}>
                                                            <a href="{{URL::to('defaultsalaryheadsetup')}}"> <i
                                                                    class="fa fa-circle-o"></i>Default Head Setup</a>
                                                        </li>
                                                        <li {!! Request::is('bonus','bonus/*') ? '                  class="active"' : null !!}>
                                                            <a href="{{URL::to('bonus')}}"> <i
                                                                    class="fa fa-circle-o"></i>Bonus Type Setup</a></li>
                                                    </ul>
                                                </li>
                                            @endif
                                        @endcan

                                        <li {!! Request::is('report_signatory') ? '     class="active"' : null !!}>
                                            <a href="{{URL::to('report_signatory')}}"> <i class="fa fa-circle-o"></i> Report Signatory Config</a>
                                        </li>
                                    </ul>
                                </li>
                            @endcan
                            @can('AdminEmployeeManagement')
                                <li {!! Request::is('add_new_employee','add_new_employee/*', 'employee_joining_approvals', 'employee_joining_approvals/*', 'employee','employeejoin','employeecard','employeepromotion','employeetransfer','employeeresign','employeecard/create','employee/create','salaryincrement','salaryincrementpreviousdata','employeeinactive','employeeinactive/create','reactive','employeeresign/create','resignapproved','employeeidcard','employeelongservice','employeelongservice/create','employeetransfercreate','reporting_boss_change','reporting_boss_change/create','reportingboss_change_list','employeepromotioncreate','submitemployeeidcard','service_completed_list','plantwiseemployeelist','plantwiseemployeeadd','salaryincrement/create','custom_filter', 'custom_employee_list', 'probation_employee'  ) ? ' class="active treeview"' : ' class="treeview"' !!}>
                                    <a href="#"><i class="fa fa-user-plus"></i>Employee Management
                                        <span class="pull-right-container">
                                            <i class="fa fa-angle-left pull-right"></i>
                                        </span>
                                    </a>

                                    <ul class="treeview-menu">
                                        @can('AddEmployee')
                                            <li {!! Request::is('add_new_employee','add_new_employee/*') ? '         class="active"' : null !!}>
                                                <a href="{{URL::to('add_new_employee')}}">
                                                    <i class="fa fa-circle-o"></i>Add New Employee
                                                </a>
                                            </li>
                                        @endcan

                                        @can('EmployeeJoiningApproval')
                                            <li {!! Request::is('employee_joining_approvals', 'employee_joining_approvals/*') ? '         class="active"' : null !!}>
                                                <a href="{{ url('employee_joining_approvals') }}">
                                                    <i class="fa fa-circle-o"></i>
                                                    Pending Joining Approval
                                                </a>
                                            </li>
                                        @endcan

                                        @can('EmployeeJoin')
                                            <li {!! Request::is('employeejoin') ? ' class="active"' : null !!}>
                                                <a href="{{URL::to('employeejoin')}}">
                                                    <i class="fa fa-circle-o"></i>
                                                    Active Employee List
                                                </a>
                                            </li>
                                        @endcan

                                        <li {!! Request::is('probation_employee') ? ' class="active"' : null !!}>
                                            <a href="{{URL::to('probation_employee')}}">
                                                <i class="fa fa-circle-o"></i>
                                                Probation Employee List
                                            </a>
                                        </li>

                                        @can('EmployeeCard')
                                            <li {!! Request::is('employeecard','employeecard/create') ? '     class="active"' : null !!}>
                                                <a href="{{URL::to('employeecard')}}"> <i class="fa fa-circle-o"></i>Employee
                                                    Card</a></li>
                                        @endcan
                                        @can('EmployeeQualification')
                                            <!-- <li class="treeview">
            <a href="#"><i class="fa fa-circle-o"></i> Qualifications
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li><a href="#"><i class="fa fa-circle-o"></i> Education</a></li>
              <li><a href="#"><i class="fa fa-circle-o"></i> Skills</a></li>
            </ul>
          </li> -->
                                        @endcan
                                        @can('CWEmployeeConfig')
                                            <li {!! Request::is('plantwisesalarycreate','plantwiseemployeeadd','plantwiseemployeelist') ? ' class="active treeview"' : ' class="treeview"' !!} >
                                                <a href="#"><i class="fa fa-circle-o"></i>CW Employee Config
                                                    <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
                                                </a>
                                                <ul class="treeview-menu">
                                                    <li {!! Request::is('plantwiseemployeeadd') ? 'class="active"' : null !!}>
                                                        <a href="{{URL::to('plantwiseemployeeadd')}}"> <i
                                                                class="fa fa-circle-o"></i>Available List</a></li>
                                                    <li {!! Request::is('plantwiseemployeelist') ? 'class="active"' : null !!}>
                                                        <a href="{{URL::to('plantwiseemployeelist')}}"> <i
                                                                class="fa fa-circle-o"></i>Plant Wise Emp. List</a></li>
                                                </ul>
                                            </li>
                                        @endcan
                                        @can('EmployeeTransfer')
                                            <li {!! Request::is('employeetransfer','employeetransfercreate') ? '     class="active"' : null !!}>
                                                <a href="{{URL::to('employeetransfer')}}"> <i
                                                        class="fa fa-circle-o"></i>Employee Transfer</a></li>
                                        @endcan

                                        <li {!! Request::is('reporting_boss_change','reporting_boss_change/create') ? 'class="active"' : null !!}>
                                            <a href="{{URL::to('reporting_boss_change')}}">
                                                <i class="fa fa-circle-o"></i>Reporting Boss Change
                                            </a>
                                        </li>

                                        @can('EmployeePromotion')
                                            <li {!! Request::is('employeepromotion','employeepromotioncreate') ? '    class="active"' : null !!}>
                                                <a href="{{URL::to('employeepromotion')}}"> <i
                                                        class="fa fa-circle-o"></i>Employee Promotion</a></li>
                                        @endcan
                                        @can('EmployeeIncrement')
                                            <li {!! Request::is('salaryincrement','salaryincrementpreviousdata','salaryincrement/create') ? '    class="active"' : null !!}>
                                                <a href="{{URL::to('salaryincrement')}}"> <i class="fa fa-circle-o"></i>Increment
                                                    & Promotion</a></li>
                                        @endcan
                                        @can('EmployeeResign')
                                            <li {!! Request::is('employeeresign','employeeresign/create','resignapproved') ? '     class="active"' : null !!}>
                                                <a href="{{URL::to('employeeresign')}}"> <i class="fa fa-circle-o"></i>Employee
                                                    Resign</a></li>
                                        @endcan
                                        @can('EmployeeInactive')
                                            <li {!! Request::is('employeeinactive','employeeinactive/create','reactive') ? '     class="active"' : null !!}>
                                                <a href="{{URL::to('employeeinactive')}}"> <i
                                                        class="fa fa-circle-o"></i>Employee Inactive</a></li>
                                        @endcan
                                        @can('EmployeeLongService')
                                            <li {!! Request::is('employeelongservice','employeelongservice/create','service_completed_list') ? '     class="active"' : null !!}>
                                                <a href="{{URL::to('employeelongservice')}}"> <i
                                                        class="fa fa-circle-o"></i>Employee Long Service</a></li>
                                        @endcan

                                        @can('EmployeeIdCardPrint')
                                            <li {!! Request::is('employeeidcard','submitemployeeidcard') ? '     class="active"' : null !!}>
                                                <a href="{{URL::to('employeeidcard')}}"> <i class="fa fa-circle-o"></i>Employee
                                                    Id Card Print</a></li>
                                        @endcan

                                        @can('EmployeeCustomFilter')

                                        <li {!! Request::is('custom_filter') ? '     class="active"' : null !!}>
                                            <a href="{{ url('custom_filter')}}">
                                                <i class="fa fa-circle-o"></i>
                                                Filter Config
                                            </a>
                                        </li>

                                        <li {!! Request::is('custom_employee_list') ? '     class="active"' : null !!}>
                                            <a href="{{ url('custom_employee_list')}}">
                                                <i class="fa fa-circle-o"></i>
                                                Export Employee All Info.
                                            </a>
                                        </li>

                                        @endcan


                                    </ul>
                                </li>
                            @endcan
                            @can('AdminUserManager')
                                <li {!! Request::is('users','user_role','assigned_roles','role_permission','role','role/*','permission','permission/create','users/create') ? ' class="active treeview"' : ' class="treeview"' !!}>
                                    <a href="#">
                                        <i class="fa fa-user"></i>
                                        <span>User Manager</span>
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </a>
                                    <ul class="treeview-menu">
                                        <li {!! Request::is('users','users/create') ? '               class="active"' : null !!}>
                                            <a href="{{URL::to('/users')}}"> <i class="fa fa-user-plus"></i>User List</a>
                                        </li>

                        <!--                 <li {!! Request::is('permission','permission/create') ? '     class="active"' : null !!}>
                                            <a href="{{URL::to('permission')}}"> <i class="fa fa-plus"></i>Permission
                                                List</a>
                                        </li> -->

                                        <li {!! Request::is('role','role/*') ? '                               class="active"' : null !!}>
                                            <a href="{{URL::to('/role')}}"> <i class="fa fa-users"></i>Role</a></li>
                                        <!--  <li {!! Request::is('role_permission') ? '                    class="active"' : null !!}>  <a href="{{URL::to('/role_permission')}}">             <i class="fa fa-tasks"></i>Role Permission</a></li>

          <li {!! Request::is('assigned_roles') ? '                     class="active"' : null !!}>  <a href="{{URL::to('assigned_roles')}}">        <i class="fa fa-share-alt"></i>Roles Assign </a></li>
           -->
                                    </ul>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('MainAttendanceOption')
                    <li {!! Request::is('attendancelist','absentlist','manual_attendance','manual_in','manual_out','manual_ot','manual_ot/create','manual_attendance_entry','manual_ot_all','machinedataupload','approvedholidaylist','data_process','holiday_process','hal_process','holiday_convert','holiday_process/create','holiday_convert/create','employeeleave','employeeleave/*','leaveapprove','holidayagainstleave','holidayagainstleavedaybook','holidayagainstleave/create','holidayagainstleavehistory','leaveapprovedlist','leaverejectedlist','rawdatacheck','manual_ot_process','manual_ot_view','ot_process_create','eligible_ot_emp', 'add_eligible_ot_emp','pending_ot_approval') ? ' class="active treeview"' : ' class="treeview"' !!}>

                        <a href="#">
                            <i class="fa fa-clock-o"></i>
                            <span>Attendance</span>
                            <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
      </span>
                        </a>
                        <ul class="treeview-menu">
                            @can('AttendanceDataUpload')
                                <li {!! Request::is('machinedataupload') ? '   class="active"' : null !!}><a
                                        href="{{URL::to('machinedataupload')}}"> <i class="fa fa-circle-o"></i>Data
                                        Upload</a></li>
                            @endcan
                            @can('AttendanceProcess')
                                <li {!! Request::is('data_process') ? '        class="active"' : null !!}><a
                                        href="{{URL::to('data_process')}}"> <i class="fa fa-circle-o"></i>Attandance
                                        Process</a></li>
                            @endcan
                            @can('AttendanceList')
                                <li {!! Request::is('attendancelist') ? '     class="active"' : null !!}><a
                                        href="{{URL::to('attendancelist')}}"> <i class="fa fa-circle-o"></i>Daily Attendance
                                        List</a></li>
                            @endcan
                            @can('AttendanceManualOption')


                                <li {!! Request::is('manual_attendance','manual_in','manual_out','manual_attendance_entry') ? '   class="active treeview"' : ' class="treeview"' !!} >
                                    <a href="#"><i class="fa fa-circle-o"></i>Manual Attendance
                                        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
                                    </a>
                                    <ul class="treeview-menu">
                                        @can('ManualAttendanceEntry')
                                            <li {!! Request::is('manual_attendance','manual_attendance_entry') ? ' class="active"' : null !!}>
                                                <a href="{{URL::to('manual_attendance')}}"> <i
                                                        class="fa fa-circle-o"></i>Manual Attendance Entry</a></li>
                                        @endcan
                                        @can('ManualTotalAttendanceInOut')
                                            <li {!! Request::is('manual_in') ? '         class="active"' : null !!}><a
                                                    href="{{URL::to('manual_in')}}"> <i class="fa fa-circle-o"></i>Multiple
                                                    Emp. (In-Out)</a></li>
                                        @endcan
                                        @can('ManualOutAA')
                                            <li {!! Request::is('manual_out') ? '        class="active"' : null !!}><a
                                                    href="{{URL::to('manual_out')}}"> <i class="fa fa-circle-o"></i>Manual
                                                    Attendance Out</a></li>
                                        @endcan
                                    </ul>


                                </li>
                            @endcan
                            @can('AttendanceLeaveManagementOption')
                                <li {!! Request::is('employeeleave','leaveapprove','holidayagainstleave','holidayagainstleavedaybook','holidayagainstleave/create','holidayagainstleavehistory','leaveapprovedlist','leaverejectedlist') ? ' class="active treeview"' : ' class="treeview"' !!}>
                                    <a href="#"><i class="fa fa-circle-o"></i>Leave Management
                                        <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                                    </a>
                                    <ul class="treeview-menu">
                                        @can('LeaveAplication')
                                            <li {!! Request::is('employeeleave','leaveapprovedlist','leaverejectedlist') ? '        class="active"' : null !!}>
                                                <a href="{{URL::to('employeeleave')}}"> <i class="fa fa-circle-o"></i>Leave
                                                    Application</a></li>
                                        @endcan
                                        @can('LeaveAproval')
                                            <li {!! Request::is('leaveapprove') ? '         class="active"' : null !!}>
                                                <a href="{{URL::to('leaveapprove')}}"> <i class="fa fa-circle-o"></i>Pending Leave Approval</a></li>
                                        @endcan
                                        @can('HolidayAgainstLeave')
                                            <li {!! Request::is('holidayagainstleave','holidayagainstleavehistory','holidayagainstleave/create') ? '  class="active"' : null !!}>
                                                <a href="{{URL::to('holidayagainstleave')}}"> <i
                                                        class="fa fa-circle-o"></i>Holiday Against Leave</a></li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcan


                            @can('AttendanceManualOTOption')
                                <li {!! Request::is('manual_ot','manual_ot/create','manual_ot_all','manual_ot_process','manual_ot_view','ot_process_create','eligible_ot_emp','add_eligible_ot_emp','pending_ot_approval') ? '   class="active treeview"' : ' class="treeview"' !!} >
                                    <a href="#"><i class="fa fa-circle-o"></i>Overtime (OT)
                                        <span class="pull-right-container">
                                                <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                    </a>
                                    <ul class="treeview-menu">

                                        @can('ManualOTEntry')
                                            <li {!! Request::is('manual_ot','manual_ot/create') ? '     class="active"' : null !!}>
                                                <a href="{{URL::to('manual_ot')}}"> <i class="fa fa-circle-o"></i>Manual
                                                    OT Entry</a></li>
                                        @endcan


                                        {{-- ELIGIBLE OT EMPLOYEE SET NEW DEV- JAN-2023 --}}

                                        <li {!! Request::is('eligible_ot_emp', 'add_eligible_ot_emp') ? '     class="active"' : null !!}>
                                            <a href="{{URL::to('eligible_ot_emp')}}"> <i class="fa fa-circle-o"></i>Eligible OT
                                                Employee</a></li>

                                        <li {!! Request::is('pending_ot_approval') ? '     class="active"' : null !!}><a
                                                href="{{URL::to('pending_ot_approval')}}"> <i class="fa fa-circle-o"></i>Pending Ot
                                                Approval</a></li>

                                        {{-- END ELIGIBLE OT EMPLOYEE SET NEW DEV- JAN-2023 --}}






                                        {{-- DOING Work With this for QFL --}}

                                 <!--        <li {!! Request::is('ot_adjust') ? '         class="active"' : null !!}>
                                            <a href="{{URL::to('ot_adjust')}}"> <i class="fa fa-circle-o"></i>
                                                OT Adjust</a></li> -->

                                        <li {!! Request::is('manual_ot_process','ot_process_create') ? '         class="active"' : null !!}>
                                            <a href="{{URL::to('manual_ot_process')}}"> <i class="fa fa-circle-o"></i>
                                                OT Process</a></li>

                                        <li {!! Request::is('manual_ot_view') ? '         class="active"' : null !!}><a
                                                href="{{URL::to('manual_ot_view')}}"> <i class="fa fa-circle-o"></i>Procced
                                                OT View & Edit</a></li>

                                        @can('ManualOTEntryAll')
                                        @endcan
                                        {{-- DOING Work With this for QFL end--}}

                                        @can('ManualOTEntryAll--')
                                            <li {!! Request::is('manual_ot_all') ? '         class="active"' : null !!}>
                                                <a href="{{URL::to('manual_ot_all')}}"> <i class="fa fa-circle-o"></i>Manual
                                                    OT Entry All</a></li>
                                        @endcan







                                    </ul>
                                </li>
                            @endcan





                            @can('HolidaySetupProcessOption')
                                <li {!! Request::is('holiday_process','holiday_process/create','approvedholidaylist') ? '     class="active"' : null !!}>
                                    <a href="{{URL::to('holiday_process')}}"> <i class="fa fa-circle-o"></i>Holiday
                                        Setup & Process</a></li>
                            @endcan
                            @can('HolidayConvertToWorkingOption')
                                <li {!! Request::is('holiday_convert','holiday_convert/create') ? '     class="active"' : null !!}>
                                    <a href="{{URL::to('holiday_convert')}}"> <i class="fa fa-circle-o"></i>Holiday
                                        Convert To Working</a></li>
                            @endcan
                            @can('AttendanceRawDataCheck')
                                <li {!! Request::is('rawdatacheck') ? '     class="active"' : null !!}><a
                                        href="{{URL::to('rawdatacheck')}}"> <i class="fa fa-circle-o"></i>Raw Data Check</a>
                                </li>
                            @endcan

        <!--                      <li {!! Request::is('pull_attendance') ? '     class="active"' : null !!}><a
                                    href="{{URL::to('pull_attendance')}}"> <i class="fa fa-circle-o"></i>Pull Attendnace Data</a>
                            </li>
 -->


                        </ul>
                    </li>
                @endcan

                @can('MainEmployeeShiftOption')
                    <li {!! Request::is ('wrongshiftassign','employeeshiftlist','shiftroleemployee','shiftroleassign','shiftrole_assign','shiftroleemployeelist','employeeshiftchange','pre_changeemployeeshift','previou_shiftrole_assign','employeeshiftlist_old','changeshift_multiple','change_employeeshift', 'auto_shift') ? ' class="active treeview"' : ' class="treeview"' !!}>
                        <a href="#">
                            <i class="fa fa-arrows-alt"> </i>
                            <span>Employee Shift</span>

                            <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
      </span>
                        </a>
                        <ul class="treeview-menu">
                            @can('EmployeeShiftList')
                                <li {!! Request::is('employeeshiftlist','employeeshiftlist_old','changeshift_multiple','change_employeeshift') ? '  class="active"' : null !!}>
                                    <a href="{{URL::to('employeeshiftlist')}}"> <i class="fa fa-circle-o"></i>Employee
                                        Shift List</a></li>
                            @endcan
                            @can('ShiftRoleApply')
                                <li {!! Request::is('shiftroleemployee','shiftroleassign','shiftrole_assign','shiftroleemployeelist','previou_shiftrole_assign') ? '   class="active treeview"' : ' class="treeview"' !!} >
                                    <a href="#"><i class="fa fa-circle-o"></i>Shift Role Apply
                                        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
                                    </a>
                                    <ul class="treeview-menu">
                                        @can('ShiftRoleApply')
                                            <li {!! Request::is('shiftroleemployee') ? '     class="active"' : null !!}>
                                                <a href="{{URL::to('shiftroleemployee')}}"> <i
                                                        class="fa fa-circle-o"></i>Available Employee</a></li>
                                            <!-- <li {!! Request::is('shiftroleemployeelist') ? '     class="active"' : null !!}>  <a href="{{URL::to('shiftroleemployeelist')}}">  <i class="fa fa-circle-o"></i>Role Wise Employee List</a></li> -->
                                        @endcan
                                        @can('ShiftRoleAssign')
                                            <li {!! Request::is('shiftroleassign','shiftrole_assign','previou_shiftrole_assign') ? '     class="active"' : null !!}>
                                                <a href="{{URL::to('shiftroleassign')}}"> <i class="fa fa-circle-o"></i>Shift
                                                    Role Assign</a></li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcan
                            @can('PreviosShiftChange')
                                <li {!! Request::is('employeeshiftchange','pre_changeemployeeshift') ? '  class="active"' : null !!}>
                                    <a href="{{URL::to('employeeshiftchange')}}"> <i class="fa fa-circle-o"></i>Previous
                                        Shift Change</a></li>
                            @endcan
                            @can('PreviosShiftChange')
                                <li {!! Request::is('wrongshiftassign') ? '  class="active"' : null !!}><a
                                        href="{{URL::to('wrongshiftassign')}}"> <i class="fa fa-circle-o"></i>Wrong
                                        Shift Assign List</a></li>
                            @endcan
                            @can('AutoShift')
                                <li {!! Request::is('auto_shift') ? ' class="active"' : null !!}>
                                    <a href="{{URL::to('auto_shift')}}"> <i class="fa fa-circle-o"></i>Auto Shift</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan



                @can('MainPayrollOption')
                    <li {!! Request::is('salarycreate','employeesalary','salaryprocess','payregister','payregister/details','salarygenerate','salaryextrafeatures','salaryextrafeatures/create','loanapplication','loanapplication/create','loantype','loantype/create','loanapprovedlist','loanledger','paidloanledger','otherfacility','otherfacilityprocess','otherfacilityprocess/*','otherfacilitygenerate','ofpayregister','cwsalaryprocess', 'cwsalaryprocess/*', 'cwpayregister','cwsalarygenerate','bonusgenerate','employeebonus','bonusprocess','plant_setup','plantwisesalarycreate','otherfacilityconfiq','otherfacilityconfiq/create','salaryprocess/create','cwemployeeinfo','bonusprocess/create','bonusprocesscw','salary_deduction_index','salary_deduction_create','salaryextra/*', 'holdup_application', 'holdup_application/*', 'holdup_realise','holdup-paid-list','holdup-unpaid-list') ? ' class="active treeview"' : ' class="treeview"' !!}>
                        <a href="#"><i class="fa fa-money"></i>
                            <span>Payroll</span>
                            <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
      </span>
                        </a>
                        <ul class="treeview-menu">

                            @can('PayrollSalaryOption')
                                <li {!! Request::is('salarycreate','employeesalary','salaryprocess','payregister','payregister/details','salarygenerate','salaryextrafeatures','salaryextrafeatures/create','salaryprocess/create','salary_deduction_index','salary_deduction_create','salaryextra/*') ? ' class="active treeview"' : ' class="treeview"' !!}>
                                    <a href="#"><i class="fa fa-circle-o"></i>Salary
                                        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
                                    </a>
                                    <ul class="treeview-menu">
                                        @can('EmployeeSalary')
                                            <li {!! Request::is('employeesalary') ? '   class="active"' : null !!}><a
                                                    href="{{URL::to('employeesalary')}}"> <i class="fa fa-circle-o"></i>Employee
                                                    Salary</a></li>
                                        @endcan
                                        @can('SalaryProcess')
                                            <li {!! Request::is('salaryprocess','salaryprocess/create') ? '    class="active"' : null !!}>
                                                <a href="{{URL::to('salaryprocess')}}"> <i class="fa fa-circle-o"></i>Salary
                                                    Process</a></li>
                                        @endcan
                                        @can('PayRegister')
                                            <li {!! Request::is('payregister') ? '      class="active"' : null !!}><a
                                                    href="{{URL::to('payregister')}}"> <i class="fa fa-circle-o"></i>Pay
                                                    Register</a></li>
                                        @endcan

                                        @can('PayRegister')
                                        @endcan
                                            <li {!! Request::is('payregister/details') ? '      class="active"' : null !!}><a
                                                    href="{{URL::to('payregister/details')}}"> <i class="fa fa-circle-o"></i>Pay
                                                    Register Details</a></li>
                                        @can('SalaryGenerate')
                                            <li {!! Request::is('salarygenerate') ? '   class="active"' : null !!}><a
                                                    href="{{URL::to('salarygenerate')}}"> <i class="fa fa-circle-o"></i>Salary
                                                    Generate (Lock)</a></li>
                                        @endcan
                                        @can('SalaryExtraFeatures')

                                            <li {!! Request::is('salary_deduction_index','salary_deduction_create') ? '   class="active"' : null !!}>
                                                <a href="{{URL::to('salary_deduction_index')}}"> <i
                                                        class="fa fa-circle-o"></i>Advance Adjust Single</a>
                                            </li>

                                            <li {!! Request::is('salaryextrafeatures','salaryextrafeatures/create','salaryextra/*') ? '   class="active"' : null !!}>
                                                <a href="{{URL::to('salaryextrafeatures')}}"> <i
                                                        class="fa fa-circle-o"></i>Advance Adjust Multiple</a></li>

                                        @endcan
                                    </ul>
                                </li>
                            @endcan
                            @can('PayrollFringeBenefitsOption')
                                <li {!! Request::is('otherfacility','otherfacilityprocess','otherfacilityprocess/*','otherfacilitygenerate','ofpayregister','otherfacilityconfiq','otherfacilityconfiq/create') ? ' class="active treeview"' : ' class="treeview"' !!}>
                                    <a href="#"><i class="fa fa-circle-o"></i>Fringe Benefits
                                        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
                                    </a>
                                    <ul class="treeview-menu">

                                        @can('OtherFacilityConfig')
                                            <li {!! Request::is('otherfacilityconfiq','otherfacilityconfiq/create') ?' class="active"' : null !!}>
                                                <a href="{{URL::to('otherfacilityconfiq')}}"> <i
                                                        class="fa fa-circle-o"></i>Fringe Benefits Config</a></li>
                                        @endcan
                                        @can('OtherFacility')
                                            <li {!! Request::is('otherfacility') ? '   class="active"' : null !!}><a
                                                    href="{{URL::to('otherfacility')}}"> <i class="fa fa-circle-o"></i>Employee
                                                    Fringe Benefits</a></li>
                                        @endcan
                                        @can('OtherFacilityProcess')
                                            <li {!! Request::is('otherfacilityprocess','otherfacilityprocess/*') ? '    class="active"' : null !!}>
                                                <a href="{{URL::to('otherfacilityprocess')}}"> <i
                                                        class="fa fa-circle-o"></i>Fringe Benefits Process</a></li>
                                        @endcan
                                        @can('OtherFacilityPayRegister')
                                            <li {!! Request::is('ofpayregister') ? '      class="active"' : null !!}><a
                                                    href="{{URL::to('ofpayregister')}}"> <i class="fa fa-circle-o"></i>FB
                                                    Pay Register</a></li>
                                        @endcan
                                        @can('OtherFacilityGenerate')
                                            <li {!! Request::is('otherfacilitygenerate') ? '   class="active"' : null !!}>
                                                <a href="{{URL::to('otherfacilitygenerate')}}"> <i
                                                        class="fa fa-circle-o"></i>Fringe Benefits Generate</a></li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcan
                            @can('PayrollCWSalaryOption')
                                <li {!! Request::is('cwsalaryprocess', 'cwsalaryprocess/*', 'cwpayregister','cwsalarygenerate','plant_setup','plantwisesalarycreate','cwemployeeinfo') ? ' class="active treeview"' : ' class="treeview"' !!}>
                                    <a href="#"><i class="fa fa-circle-o"></i>CW Salary
                                        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
                                    </a>
                                    <ul class="treeview-menu">

                                        <li {!! Request::is('plant_setup','plantwisesalarycreate') ? '        class="active"' : null !!}>
                                            <a href="{{URL::to('plant_setup')}}"> <i class="fa fa-circle-o"></i>Plant-Salary
                                                Setup</a></li>
                                        <li {!! Request::is('cwemployeeinfo') ? '     class="active"' : null !!}><a
                                                href="{{URL::to('cwemployeeinfo')}}"> <i class="fa fa-circle-o"></i>CW
                                                Employee Information</a></li>
                                        @can('CWSalaryProcess')
                                            <li {!! Request::is('cwsalaryprocess', 'cwsalaryprocess/*') ? '    class="active"' : null !!}>
                                                <a href="{{URL::to('cwsalaryprocess')}}"> <i class="fa fa-circle-o"></i>CW
                                                    Salary Process</a></li>
                                        @endcan
                                        @can('CWPayRegister')
                                            <li {!! Request::is('cwpayregister') ? '      class="active"' : null !!}><a
                                                    href="{{URL::to('cwpayregister')}}"> <i class="fa fa-circle-o"></i>CW
                                                    Pay Register</a></li>
                                        @endcan
                                        @can('CWSalaryGenerate')
                                            <li {!! Request::is('cwsalarygenerate') ? '   class="active"' : null !!}><a
                                                    href="{{URL::to('cwsalarygenerate')}}"> <i
                                                        class="fa fa-circle-o"></i>CW Salary Generate</a></li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcan

                            @can('PayrollBonusOption')
                                <li {!! Request::is('bonusgenerate','employeebonus','bonusprocess','bonusprocess/create','bonusprocesscw') ? ' class="active treeview"' : ' class="treeview"' !!}>
                                    <a href="#"><i class="fa fa-circle-o"></i>Bonus
                                        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
                                    </a>
                                    <ul class="treeview-menu">
                                        @can('BonusProcess')
                                            <li {!! Request::is('bonusprocess','bonusprocess/create','bonusprocesscw') ? '    class="active"' : null !!}>
                                                <a href="{{URL::to('bonusprocess')}}"> <i class="fa fa-circle-o"></i>Bonus
                                                    Process</a></li>
                                        @endcan
                                        @can('EmployeeBonus')
                                            <li {!! Request::is('employeebonus') ? '   class="active"' : null !!}><a
                                                    href="{{URL::to('employeebonus')}}"> <i class="fa fa-circle-o"></i>Employee
                                                    Bonus</a></li>
                                        @endcan
                                        @can('BonusGenerate')
                                            <li {!! Request::is('bonusgenerate') ? '   class="active"' : null !!}><a
                                                    href="{{URL::to('bonusgenerate')}}"> <i class="fa fa-circle-o"></i>Final
                                                    Generate</a></li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcan

                            @can('PayrollLoanManagementOption')
                                <li {!! Request::is('loanapplication','loanapplication/create','loantype','loantype/create','loanapprovedlist','loanledger','paidloanledger') ? ' class="active treeview"' : ' class="treeview"' !!}>
                                    <a href="#"><i class="fa fa-circle-o"></i>Loan Management
                                        <span class="pull-right-container">
                                            <i class="fa fa-angle-left pull-right"></i>
                                        </span>
                                    </a>
                                    <ul class="treeview-menu">
                                        <li {!! Request::is('loantype','loantype/create') ? '                                         class="active"' : null !!}>
                                            <a href="{{URL::to('loantype')}}"> <i class="fa fa-circle-o"></i>Loan
                                                Type</a></li>
                                        <li {!! Request::is('loanapplication','loanapplication/create','loanapprovedlist') ? '        class="active"' : null !!}>
                                            <a href="{{URL::to('loanapplication')}}"> <i class="fa fa-circle-o"></i>Loan
                                                Application</a></li>
                                        <li {!! Request::is('loanledger','paidloanledger') ? '                                                         class="active"' : null !!}>
                                            <a href="{{URL::to('loanledger')}}"> <i class="fa fa-circle-o"></i>Loan
                                                Ledger</a></li>
                                    </ul>
                                </li>
                            @endcan

                            <li {!! Request::is('holdup_application', 'holdup_application/*', 'holdup_realise', 'holdup_realise/*', 'holdup-paid-list', 'holdup-unpaid-list') ? ' class="active treeview"' : ' class="treeview"' !!}>
                                <a href="#"><i class="fa fa-circle-o"></i>Salary Holdup
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu">
                                    <li {!! Request::is('holdup_application', 'holdup_application/create') ? '   class="active"' : null !!}>
                                        <a href="{{URL::to('holdup_application')}}"> <i class="fa fa-circle-o"></i>Holdup Application</a>
                                    </li>
                                </ul>
                                <ul class="treeview-menu">
                                    <li {!! Request::is('holdup-unpaid-list') ? '   class="active"' : null !!}>
                                        <a href="{{URL::to('holdup-unpaid-list')}}"> <i class="fa fa-circle-o"></i>Holdup Salary Unpaid List</a>
                                    </li>
                                </ul>
                                <ul class="treeview-menu">
                                    <li {!! Request::is('holdup-paid-list') ? '   class="active"' : null !!}>
                                        <a href="{{URL::to('holdup-paid-list')}}"> <i class="fa fa-circle-o"></i>Holdup Salary paid List</a>
                                    </li>
                                </ul>
                            </li>

                        </ul>
                    </li>
                @endcan
                @can('MainReportOption')
                    <li {!! Request::is('reports') ? ' class="active treeview"' : null !!}>
                        <a href="{{URL::to('/reports')}}">
                            <i class="fa fa-book"></i>
                            <span>Reports</span>
                        </a></li>
                @endcan


                @can('MainDocumentArchiveOption')
                    <li {!! Request::is('document_archive') ? ' class="active"' : null !!}>
                        <a href="{{URL::to('/document_archive')}}">
                            <i class="fa fa-history"></i>
                            <span>Document Archive</span>
                        </a></li>
                @endcan
                @can('MainKPIOption')
                    <li {!! Request::is('task_department', 'kpi_config', 'kpi_config/*', 'task_department/create','kpi_assessment_date_year','kpi_date_setup','kpi_task','kpi_mark','kpi_task_details','kpi_task_details/create','kpi_employee','kpi_employee/create','kpi_increment_range','kpi_deptheadassign','kpi_deptheadassign/create','kpi_markdeduct','kpi_markdeduct/create','kpi_employee_list','kpi_employee_final_list','kpi_report') ?  ' class="active treeview"' : ' class="treeview"' !!}>
                        <a href="#"><i class="fa fa-check"></i>
                            <span>KPI</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>

                        <ul class="treeview-menu">

                            @can('KPIAdminUser')
                                <li {!! Request::is('task_department','task_department/create', 'kpi_config', 'kpi_config/*', 'kpi_assessment_date_year','kpi_date_setup','kpi_task','kpi_mark','kpi_task_details','kpi_task_details/create','kpi_increment_range','kpi_deptheadassign','kpi_deptheadassign/create','kpi_markdeduct','kpi_markdeduct/create','kpi_markdeduct','kpi_markdeduct/create') ? ' class="active treeview"' : ' class="treeview"' !!} >

                                    <a href="#"><i class="fa fa-random"></i> Configuration
                                        <span class="pull-right-container">
                                            <i class="fa fa-angle-left pull-right"></i>
                                        </span>
                                    </a>
                                    <ul class="treeview-menu">
                                        <li {!! Request::is('kpi_assessment_date_year') ? '                   class="active"' : null !!}>
                                            <a href="{{URL::to('kpi_assessment_date_year')}}"> <i
                                                    class="fa fa-circle-o"></i>KPI Year Setup</a></li>
                                        <li {!! Request::is('kpi_date_setup') ? '                             class="active"' : null !!}>
                                            <a href="{{URL::to('kpi_date_setup')}}"> <i class="fa fa-circle-o"></i>KPI
                                                Date Range Setup</a></li>
                                        {{-- <li {!! Request::is('kpi_mark') ? '                                   class="active"' : null !!}>
                                            <a href="{{URL::to('kpi_mark')}}"> <i class="fa fa-circle-o"></i>KPI
                                                Mark</a></li> --}}
                                        <li {!! Request::is('kpi_increment_range') ? '                        class="active"' : null !!}>
                                            <a href="{{URL::to('kpi_increment_range')}}"> <i class="fa fa-circle-o"></i>KPI
                                                Increment Range</a></li>
                                        {{-- <li {!! Request::is('task_department','task_department/create') ? '   class="active"' : null !!}>
                                            <a href="{{URL::to('task_department')}}"> <i class="fa fa-circle-o"></i>Task
                                                Department</a></li> --}}
                                        <li {!! Request::is('kpi_task') ? '                                   class="active"' : null !!}>
                                            <a href="{{URL::to('kpi_task')}}"> <i class="fa fa-circle-o"></i>KPI
                                                Task</a></li>
                                        {{-- <li {!! Request::is('kpi_task_details','kpi_task_details/create') ? ' class="active"' : null !!}>
                                            <a href="{{URL::to('kpi_task_details')}}"> <i class="fa fa-circle-o"></i>KPI
                                                Task Details</a></li> --}}
                                        <li {!! Request::is('kpi_markdeduct','kpi_markdeduct/create') ? '     class="active"' : null !!}>
                                            <a href="{{URL::to('kpi_markdeduct')}}"> <i class="fa fa-circle-o"></i>Mark
                                                Deduct</a></li>

                                        <li {!! Request::is('kpi_config', 'kpi_config/*') ? ' class="active"' : null !!}>
                                            <a href="{{ URL::to('kpi_config') }}">
                                                <i class="fa fa-circle-o"></i>
                                                KPI Set Config
                                            </a>
                                        </li>

                                        <li {!! Request::is('kpi_deptheadassign','kpi_deptheadassign/create') ? '   class="active"' : null !!}>
                                            <a href="{{URL::to('kpi_deptheadassign')}}"> <i class="fa fa-circle-o"></i>KPI
                                                Dept.Head Assign</a></li>
                                    </ul>
                                </li>
                            @endcan

                            @can('KPIUserOnly')
                                <li {!! Request::is('kpi_employee','kpi_employee/create','kpi_employee_list') ? ' class="active treeview"' : ' class="treeview"' !!} >
                                    <a href="#"><i class="fa fa-random"></i> Employee KPI
                                        <span class="pull-right-container">
                                            <i class="fa fa-angle-left pull-right"></i>
                                        </span>
                                    </a>
                                    <ul class="treeview-menu">
                                        <li {!! Request::is('kpi_employee','kpi_employee/create') ? '                   class="active"' : null !!}>
                                            <a href="{{URL::to('kpi_employee')}}"> <i class="fa fa-circle-o"></i>New
                                                Employee KPI</a></li>
                                        <li {!! Request::is('kpi_employee_list') ? '                                    class="active"' : null !!}>
                                            <a href="{{URL::to('kpi_employee_list')}}"> <i class="fa fa-circle-o"></i>Employee
                                                KPI List</a></li>
                                    </ul>
                                </li>
                            @endcan
                            @can('KPIAdminUser')
                                <li {!! Request::is('kpi_employee_final_list') ? '                              class="active"' : null !!}>
                                    <a href="{{URL::to('kpi_employee_final_list')}}"> <i class="fa fa-check-square"></i>Employee
                                        KPI Final List</a></li>
                                <li {!! Request::is('kpi_report') ? '                                                   class="active"' : null !!}>
                                    <a href="{{URL::to('kpi_report')}}"> <i class="fa fa-book"></i>KPI Report</a></li>
                                @endcan

                                </li>
                        </ul>
                @endcan
                @can('MobileAppsAttendance')
                    <li {!! Request::is ('current_month_app_user','app_user_log','app_user_details_log') ? ' class="active treeview"' : ' class="treeview"' !!}>
                        <a href="#">
                            <i class="fa fa-mobile"> </i>
                            <span>Mobile Apps Attendance</span>

                            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
                        </a>
                        <ul class="treeview-menu">
                            <li {!! Request::is('current_month_app_user') ? '  class="active"' : null !!}><a
                                    href="{{URL::to('current_month_app_user')}}"> <i class="fa fa-circle-o"></i>Current
                                    Month User</a></li>

                            <li {!! Request::is('app_user_details_log') ? '  class="active"' : null !!}><a
                                    href="{{URL::to('app_user_details_log')}}"> <i class="fa fa-circle-o"></i>App User
                                    Details Log</a></li>

                            <li {!! Request::is('app_user_log') ? '  class="active"' : null !!}><a
                                    href="{{URL::to('app_user_log')}}"> <i class="fa fa-circle-o"></i>App User Log</a>
                            </li>

                        </ul>
                    </li>
                @endcan
                @can('MainAssetManagement')
                    <li {!! Request::is ('asset', 'assetstore', 'assetassign', 'assetreturn','assetstore/create') ? ' class="active treeview"' : ' class="treeview"' !!}>
                        <a href="#">
                            <i class="fa fa-laptop"> </i>
                            <span>Asset Management</span>

                            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
                        </a>
                        <ul class="treeview-menu">
                            <li {!! Request::is('asset') ? '  class="active"' : null !!}>
                                <a href="{{ route('asset.index')}}">
                                    <i class="fa fa-circle-o"></i>New Asset Entry
                                </a>
                            </li>
                            <li {!! Request::is('assetstore','assetstore/create') ? '  class="active"' : null !!}>
                                <a href="{{ route('assetstore.index')}}">
                                    <i class="fa fa-circle-o"></i>Asset Store
                                </a>
                            </li>
                            <li {!! Request::is('assetassign') ? '  class="active"' : null !!}>
                                <a href="{{ route('assetassign.index')}}">
                                    <i class="fa fa-circle-o"></i>Asset Assign
                                </a>
                            </li>
                            <li {!! Request::is('assetreturn') ? '  class="active"' : null !!}>
                                <a href="{{ route('assetreturn.index')}}">
                                    <i class="fa fa-circle-o"></i>Asset Return
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan
                @can('MainSkillManagement')
                    <li {!! Request::is ('skill', 'skillemployeewise') ? ' class="active treeview"' : ' class="treeview"' !!}>
                        <a href="#">
                            <i class="fa fa-mobile"> </i>
                            <span>Skill Management</span>

                            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
                        </a>
                        <ul class="treeview-menu">
                            <li {!! Request::is('skill') ? '  class="active"' : null !!}>
                                <a href="{{ route('skill.index')}}">
                                    <i class="fa fa-circle-o"></i>New Skill Entry
                                </a>
                            </li>
                            <li {!! Request::is('skillemployeewise') ? '  class="active"' : null !!}>
                                <a href="{{ route('skillemployeewise.index')}}">
                                    <i class="fa fa-circle-o"></i>Assign Skill Employee
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan

                @can('MainCVBankManagement')
                    <li {!! Request::is (['cv-job-descriptions', 'cv-job-descriptions/*', 'cv-job-requsitions', 'cv-job-requsitions/*', 'cv-drop-list']) ? ' class="active treeview"' : ' class="treeview"' !!}>
                        <a href="#">
                            <i class="fa fa-university"></i>
                            <span>CV Bank Managment</span>

                            <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                        </a>
                        <ul class="treeview-menu">
                            <li {!! Request::is(['cv-job-descriptions', 'cv-job-descriptions/*']) ? '  class="active"' : null !!}>
                                <a href="{{ route('cv-job-descriptions.index')}}">
                                    <i class="fa fa-circle-o"></i>
                                    CV Job Description Setup
                                </a>
                            </li>
                            <li {!! Request::is(['cv-job-requsitions', 'cv-job-requsitions/*']) ? '  class="active"' : null !!}>
                                <a href="{{ route('cv-job-requsitions.index')}}">
                                    <i class="fa fa-circle-o"></i>
                                    Job Requsition
                                </a>
                            </li>

                            <li {!! Request::is(['cv-drop-list']) ? '  class="active"' : null !!}>
                                <a href="{{ route('cv-drop-list')}}">
                                    <i class="fa fa-circle-o"></i>
                                    CV Drop list
                                </a>
                            </li>
                            <li {!! Request::is(['career-opportunities']) ? '  class="active"' : null !!}>
                                <a href="{{ route('career-page')}}" target="_blank">
                                    <i class="fa fa-circle-o"></i>
                                    Career Live Page
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan






                @can('MainTaskManagement')
                    <li {!! Request::is (['employee_task_management', 'employee_task_management/*', 'employee_task_type', 'employee_task_type/*', 'completed_task_list', 'pending_task_list', 'daily_complete_task_list']) ? ' class="active treeview"' : ' class="treeview"' !!}>
                        <a href="#">
                            <i class="fa fa-tasks"></i>
                            <span>Task Managment</span>

                            <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                        </a>
                        <ul class="treeview-menu">
                            <li {!! Request::is(['employee_task_type', 'employee_task_type/*']) ? '  class="active"' : null !!}>
                                <a href="{{ url('/employee_task_type') }}">
                                    <i class="fa fa-circle-o"></i>
                                    Task Type
                                </a>
                            </li>

                            <li {!! Request::is(['employee_task_management', 'employee_task_management/*']) ? '  class="active"' : null !!}>
                                <a href="{{ route('employee_task_management.index')}}">
                                    <i class="fa fa-circle-o"></i>
                                    Task Create
                                </a>
                            </li>

                            <li {!! Request::is(['pending_task_list']) ? '  class="active"' : null !!}>
                                <a href="{{ url('pending_task_list')}}">
                                    <i class="fa fa-circle-o"></i>
                                    Pending Work List
                                </a>
                            </li>

                            <li {!! Request::is(['completed_task_list']) ? '  class="active"' : null !!}>
                                <a href="{{ url('completed_task_list')}}">
                                    <i class="fa fa-circle-o"></i>
                                    Completed Tasks
                                </a>
                            </li>

                            <li {!! Request::is(['daily_complete_task_list']) ? '  class="active"' : null !!}>
                                <a href="{{ url('daily_complete_task_list')}}">
                                    <i class="fa fa-circle-o"></i>
                                    Daily Completed Tasks
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan

                @can('MainAccountsIntegration')
                    <li {!! Request::routeIs (['account_head_title.*', 'account_journal_type.*', 'account_head_integration.*', 'account_head_wise_employee_setup.*', 'change_head.index','acc_processed_data.index']) ? ' class="active treeview"' : ' class="treeview"' !!}>
                        <a href="#">
                            <i class="fa fa-money"></i>
                            <span>Accounts Integration</span>

                            <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                </span>
                        </a>
                        <ul class="treeview-menu">
                            <li {!! Request::routeIs(['acc_processed_data.index']) ? '  class="active"' : null !!}>
                                <a href="{{ route('acc_processed_data.index') }}">
                                    <i class="fa fa-circle-o"></i>
                                    Acc Processed Data
                                </a>
                            </li>

                            <li {!! Request::routeIs(['account_journal_type.*']) ? '  class="active"' : null !!}>
                                <a href="{{ route('account_journal_type.index') }}">
                                    <i class="fa fa-circle-o"></i>
                                    Account Journal Type
                                </a>
                            </li>

                            <li {!! Request::routeIs(['account_head_title.*']) ? '  class="active"' : null !!}>
                                <a href="{{ route('account_head_title.index') }}">
                                    <i class="fa fa-circle-o"></i>
                                    Account Head Title
                                </a>
                            </li>

                            <li {!! Request::routeIs(['account_head_wise_employee_setup.*']) ? '  class="active"' : null !!}>
                                <a href="{{ route('account_head_wise_employee_setup.index') }}">
                                    <i class="fa fa-circle-o"></i>
                                    A/C Wise Employee Setup
                                </a>
                            </li>


                            <li {!! Request::routeIs(['account_head_integration.*']) ? '  class="active"' : null !!}>
                                <a href="{{ route('account_head_integration.index') }}">
                                    <i class="fa fa-circle-o"></i>
                                    Account Head Integration
                                </a>
                            </li>


                            <!--                 <li {!! Request::routeIs(['change_head.index']) ? '  class="active"' : null !!}>
                    <a href="{{ route('change_head.index') }}">
                        <i class="fa fa-circle-o"></i>
                        Exchange Employee Head
                    </a>
                </li>
 -->

                        </ul>
                    </li>
                @endcan

            @can('DatabaseBackup')
                <li {!! Request::is (['database/backup']) ?  'class="active"' : '' !!}>
                    <a href="{{ route('database.backup') }}">
                        <i class="fa fa-database"></i>
                        <span>Database Backup</span>
                    </a>
                </li>
            @endcan

            @can('EmployeeOwnOnly')

{{--             <li {!! Request::is('employeeinfo/*') ? ' class="active"' : null !!}>
                <a href="{{ url('/employeeinfo/'.auth()->user()->hrm_employee_id) }}">
                    <i class="fa fa-user"></i>
                    <span>My Profile</span>
                </a>
            </li> --}}

            <li {!! Request::is(['my_leaves', 'my_leaves/create', 'my_leaveapprovedlist', 'my_leaverejectedlist']) ? ' class="active"' : null !!}>
                <a href="{{URL::to('/my_leaves')}}">
                    <i class="fa fa-dashboard"></i>
                    <span>Leave Management</span>
                </a>
            </li>

            <li {!! Request::is('my_attendance', 'my_attendance_details/*') ? ' class="active"' : null !!}>
                <a href="{{URL::to('/my_attendance')}}">
                    <i class="fa fa-dashboard"></i>
                    <span>My Attendance </span>
                </a>
            </li>
            @endcan
        </section>
        <!-- /.sidebar -->
    </aside>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <div class="frmMsg"></div>

        <section class="content" style="padding:5px;margin:0 auto 30px;padding-left:5px;padding-right:5px;">
            <div class="flash-message">
                @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                    @if(Session::has('alert-' . $msg))
                        <h1 style="font-size: 16px" class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close"
                                                                                                  data-dismiss="alert"
                                                                                                  aria-label="close">&times;</a>
                        </h1>
                    @endif
                @endforeach
            </div>

            @yield('content')
        </section>
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer text-center">
        <strong>Copyright &copy; 2017-{{ now()->year }} <a href="http://i-infotechsolution.com" target="_blank">i-infotech
                Business Solution</a> <span>v-8.00</span> </strong>
    </footer>


    <div class="modal fade" id="showDetailModal" data-backdrop="static">
        <div id="modalSize" class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header dont-print hidden-print">
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                    <h3 class="modal-title" id="showDetailModalTile"></h3>
                </div>

                <div class="modal-body" id="showDetailModalBody"></div>

                <div class="modal-footer hidden-print" id="modal-footer">
                    <a data-dismiss="modal" class="btn btn-default" href="#">Close</a>
                </div>
            </div>
        </div>
    </div>


</div>

<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>

<script src="{{asset('bootstrap/js/bootstrap.min.js')}}"></script>

<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>

<script src="{{asset('dist/js/adminlte.min.js')}}"></script>
<script src="{{ asset('plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
<script src="{{ asset('toaster/bootoast.js') }}"></script>
<script src="{{ asset('utility.js') }}"></script>

@yield('script')
</body>
</html>
