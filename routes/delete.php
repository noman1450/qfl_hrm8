
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::redirect('/', 'home');

Route::group(['middleware' => 'auth'], function () {
	Route::get('/home', 'HomeController');
});
Route::get('cacheclear', function(){
    Artisan::call('optimize:clear');
    return back();
});

Route::get('attendance_summary', 'API\Dashboard@attendance_summary');

// CV Bankn Controllers ====================>
Route::group(['middleware' => 'auth'], function () {
	//UsersController
	Route::resource('users',				'UsersController');
	Route::post('userslocationlist',		'UsersController@userslocationlist');
	Route::get('reset',	            		'UsersController@reset');
	Route::post('users_list',	            'UsersController@users_list');
	Route::get('users/{id}/cancel/',		'UsersController@cancel');
	Route::get('users/{id}/reactive/',		'UsersController@reactive');
	Route::get('users/{id}/edit/',	    	'UsersController@edit');
	Route::get('users/{id}/reset/',	    	'UsersController@reset');
	Route::get('users/{id}/usersLog/',	    'UsersController@usersLog')->name('users.log');
	Route::get('resetmypassword',	    	'UsersController@resetmypassword');
	Route::post('password_reset',	        'UsersController@password_reset');


	Route::get('role',	            		'UsersController@role');
	Route::post('role',	            		'UsersController@roleStore');
	Route::get('role/{id}/edit',       		'UsersController@roleEdit');
	Route::get('role/create',       		'UsersController@roleCreate');
	Route::patch('role/{id}/edit',       	'UsersController@roleUpdate');
	Route::get('role/{id}/delete',       	'UsersController@roleDelete');
	Route::get('role/{id}/show',       	    'UsersController@roleShow');
	Route::post('role_list',	            'UsersController@role_list');
	Route::post('role_store',	            'UsersController@role_store');

	// Permission Controller

	Route::get('permission/{id}/delete',	'PermissionsController@delete')->name('permission.delete');
	Route::resource('permission',			'PermissionsController');
	Route::get('getpermissionlist',			'PermissionsController@getpermissionlist');

	Route::get('role_permission',			'PermissionsController@role_permission_display');
	Route::post('submit_role_permission',	'PermissionsController@submit_role_permission');

	Route::get('assigned_roles',			'PermissionsController@user_role_display');
	Route::get('permission/{id}/user_role',	'PermissionsController@submit_user_role');
	Route::post('add_user_role',			'PermissionsController@add_user_role');

	// Route::get('permissionlist',			'PermissionController@permissionlistDisplay');
	// Route::post('permissionlist',	        'PermissionController@permissionlist');
	// Route::get('permission/permissionlist',	'PermissionController@permissionlistDisplay');
	// Route::get('role_permission',	        'PermissionController@role_permission');
	// Route::post('submit_role_permission',	'PermissionController@submit_role_permission');


	Route::get('depertment_list_data',			'MasterDataController@depertmentlist');
	Route::get('designation_list_data',			'MasterDataController@designationlist');
	Route::get('religion_list_data',			'MasterDataController@religionlistdata');
	Route::get('resignation_list_data',			 'MasterDataController@resignationListData');
	Route::get('maritalstatus_list_data',		'MasterDataController@maritalstatuslistdata');
	Route::get('blood_group_list_data',			'MasterDataController@bloodgrouplistdata');
	Route::get('category_list_data',			'MasterDataController@categorylist');
	Route::get('shift_list_data',				'MasterDataController@shiftlist');
	Route::get('employee_list_data',			'MasterDataController@employeelist');
	Route::get('location_wise_employeelist',			'MasterDataController@location_wise_employeelist');
	Route::get('join_employee_list',			'MasterDataController@joinemployeelist');
	Route::get('joinemployeelist_accounts',		'MasterDataController@joinemployeelist_accounts');
	Route::get('employeestatus_list_data',		'MasterDataController@employeestatuslist');
	Route::get('location_list_data',			'MasterDataController@locationlist');
	Route::get('location_list_data_all',		'MasterDataController@locationlistdataall');
	Route::get('section_list_data',				'MasterDataController@sectionlist');
	Route::get('day_list',				        'MasterDataController@daylist');
	Route::get('employee_leave_type',			'MasterDataController@leavetypelist');
	Route::get('monthlist',			            'MasterDataController@monthlist');
	Route::get('userlist',			            'MasterDataController@userlist');
	Route::get('getemployeejobinfo_details',    'MasterDataController@getemployeejobinfo_details');
	Route::get('holiday_list_data',	            'MasterDataController@holiday_list_data');
	Route::get('salaryheadgrouplist',	        'MasterDataController@salaryheadgrouplist');
	Route::get('salarygrade_list',	            'MasterDataController@salarygrade_list');
	Route::get('salary_head_list',	            'MasterDataController@salary_head_list');
	Route::get('salary_head_loan_list',	        'MasterDataController@salary_head_loan_list');
	Route::get('shiftrole_list_data',	        'MasterDataController@shiftrole_list_data');
	Route::get('file_type_list',	            'MasterDataController@file_type_list');
	Route::get('bonus_name_list',	            'MasterDataController@bonus_name_list');
	Route::get('process_bonus_list',	        'MasterDataController@process_bonus_list');
	Route::get('process_bonus_date_list',	    'MasterDataController@process_bonus_date_list');
	Route::get('plantname_list_data',	        'MasterDataController@plantname_list_data');
	Route::get('education_list_data',	        'MasterDataController@education_list_data');
	Route::get('loantypes_list',	   	        'MasterDataController@loantypes_list');
	Route::get('slap_name_list_data',	   	    'MasterDataController@slap_name_list_data');
	Route::get('partial_salary_list_data',	   	'MasterDataController@partial_salary_list_data');
	Route::get('salary_master_listdata',	   	'MasterDataController@salary_master_listdata');
	Route::get('kpi_daterange_listdata',	   	'MasterDataController@kpi_daterange_listdata');
	Route::get('kpi_task_department_listdata',	'MasterDataController@kpi_task_department_listdata');
	Route::get('get_kpi_mark_list',	         	'MasterDataController@get_kpi_mark_list');
	Route::get('kpi_assesment_date_list_data',	'MasterDataController@kpi_assesment_date_list_data');
	Route::get('get_bank_list',	             	'MasterDataController@get_bank_list');
	Route::get('plantwithsection_list_data',	'MasterDataController@plantwithsection_list_data');
	Route::get('increment_range_list_data',  	'MasterDataController@increment_range_list_data');
	Route::get('get-all-job-description-group', 'CVJobDescriptionGroupSetupController@jobDescriptionGroups')->name('get-all-job-description-group');
	Route::get('job_desctiption_list_data', 	'MasterDataController@job_desctiption_list_data');
	Route::get('cv_job_requsition_list_data', 	'MasterDataController@cv_job_requsition_list_data');
	Route::get('compensation_drop_list', 		'MasterDataController@compensationDropList');
	Route::get('getDesignationByEmployeeName',	'MasterDataController@getDesignationByEmployeeName');

	Route::get('salary_holdup_types_list', 		'MasterDataController@salary_holdup_types_list');
    // EndSelect2


	// DashboardController
	Route::post('provision_employeelist',	        'DashboardController@provision_employeelist');
	Route::get('probation/{id}',				    'DashboardController@probation');
	Route::post('probation_confirm',			    'DashboardController@probation_confirm');
	Route::get('common_dashboard/{id}',	            'DashboardController@common_dashboard')->name('common_dashboard');
	Route::get('cost_to_the_company',			    'DashboardController@cost_to_the_company');
	Route::get('cost_to_the_company_location',	    'DashboardController@cost_to_the_company_location');
	Route::get('cost_to_the_company_category',		'DashboardController@cost_to_the_company_category');
	Route::get('cost_to_the_companysummary',		'DashboardController@cost_to_the_companysummary');

	Route::post('cost_to_the_company_data',		    'DashboardController@cost_to_the_company_data');
	Route::post('cost_to_the_company_location_data','DashboardController@cost_to_the_company_location_data');
	Route::post('cost_to_the_company_category_data', 'DashboardController@cost_to_the_company_category_data');
	Route::post('cost_to_the_company_summary_data',  'DashboardController@cost_to_the_company_summary_data');
	Route::post('attendance_summary_by_location',  'DashboardController@attendanceSummaryByLocation');
	Route::get('attendance_summary_data_location',  'DashboardController@attendanceSummaryDataLocation');
	Route::get('attendance_summary_data_department',  'DashboardController@attendanceSummaryDataDepartment');



	// Designation Controller
	Route::resource('designation',				'DesignationController');
	Route::post('designation_list',				'DesignationController@designationlist');
	Route::get('designation/{id}/cancel/',		'DesignationController@cancel');


	// Depertment Controller
	Route::resource('depertment',				'DepertmentController');
	Route::post('depertment_list',				'DepertmentController@depertmentlist');
	Route::get('depertment/{id}/cancel/',		'DepertmentController@cancel');


	// Category Controller
	Route::resource('category',					'CategoryController');
	Route::post('category_list',				'CategoryController@categorylist');
	Route::get('category/{id}/cancel/',			'CategoryController@cancel');


	// Shift Controller
	Route::resource('shift',					'ShiftController');
	Route::post('shift_list',					'ShiftController@shiftlist');
	Route::get('shift/{id}/cancel/',			'ShiftController@cancel');


	// Blood group Controller
	Route::resource('bloodgroup',				'BloodgroupController');
	Route::post('bloodgroup_list',				'BloodgroupController@bloodgrouplist');
	Route::get('bloodgroup/{id}/cancel/',		'BloodgroupController@cancel');


	// Religion Controller
	Route::resource('religion',					'ReligionController');
	Route::get('religion_list',				    'ReligionController@religionlist');
	Route::get('religion/{id}/cancel/',			'ReligionController@cancel');


    // Religion Controller
	Route::resource('resignation_type',			'ResignationTypeController');
	Route::get('resignation_type_list',			'ResignationTypeController@resignationTypeList');
	// Route::get('religion/{id}/cancel/',		'ResignationTypeController@cancel');

	// Route::get('getpermissionlist',			'PermissionsController@getpermissionlist');


	// Marital Status Controller
	Route::resource('maritalstatus',			'MaritalStatusController');
	Route::post('marital_status_list',			'MaritalStatusController@maritalstatuslist');
	Route::get('maritalstatus/{id}/cancel/',	'MaritalStatusController@cancel');


	//Leave Type Controller
	Route::resource('leavetype',			    'LeaveTypeController');
	Route::post('leavetype_list',				'LeaveTypeController@leavetypelist');
	Route::get('leavetype/{id}/cancel/',		'LeaveTypeController@cancel');



	//Loan Type Controller
	Route::resource('loantype',			    	'LoanTypeController');
	Route::post('loantype_list',				'LoanTypeController@loantypelist');
	Route::get('loantype/{id}/cancel/',			'LoanTypeController@cancel');




	//Leave Type Controller
	Route::resource('leaveyear',			    'LeaveYearController');
	Route::post('leaveyear_list',				'LeaveYearController@leaveyearlist');
	Route::get('leaveyear/{id}/cancel/',		'LeaveYearController@cancel');


	//Leave Type Year Controller
	Route::resource('leavetypeyear',			'LeaveTypeYearController');
	Route::post('leavetypeyear_list',			'LeaveTypeYearController@leavetypeyearlist');
	Route::get('leavetypeyear/{id}/cancel/',	'LeaveTypeYearController@cancel');


	// Employee Controller
	Route::resource('employee',					'EmployeeController');
	Route::post('employee_list',				'EmployeeController@employeelist');
	Route::get('employee/{id}/cancel/',			'EmployeeController@cancel');
	Route::get('employeeinfo/{id}',		       	'EmployeeController@employeeinfo');
	Route::get('employeeinfo/{id}/edit',		'EmployeeController@edit_employeeinfo');
	Route::get('employeeinfo/{id}/print',		'EmployeeController@print_employeeinfo');
	Route::post('modify_employeeinfo',			'EmployeeController@modify_employeeinfo');
    Route::post('employeehistory_list',			'EmployeeController@employeehistory_list');


    Route::post('draft_employee_list',			'AddNewEmployeeController@draftEmployeeList');
    Route::get('add_new_employee/{id}/cancel',	'AddNewEmployeeController@cancel');
    Route::resource('add_new_employee',			'AddNewEmployeeController');
    Route::get('check_isEmployeeJoiningApproval', 'AddNewEmployeeController@checkIsEmployeeJoiningApproval');

    // Employee joining approval
    Route::get('employee_joining_approvals', 'EmployeeJoiningApprovalController@index');
    Route::get('employee_joining_approvals/{id}/get_form', 'EmployeeJoiningApprovalController@getForm');
    Route::post('employee_joining_approvals', 'EmployeeJoiningApprovalController@action');

	// Employee Join Controller
	Route::resource('employeejoin',					'EmployeeJoinController');
	Route::post('current_employeelist',				'EmployeeJoinController@current_employeelist');
	Route::get('employeejoin/{id}/requestredflag',	'EmployeeJoinController@requestredflag');

	// Probation Employee Controller
	Route::get('probation_employee',	'ProbationEmployeeController@probationEmployee');
	Route::post('probation_employee_list',				'ProbationEmployeeController@probationEmployeeList');

	Route::get('employeeidcard',					'EmployeeJoinController@employeeidcard');
	Route::post('employeeidcardlistdata',			'EmployeeJoinController@employeeidcardlistdata');
	Route::post('submitemployeeidcard',				'EmployeeJoinController@submitemployeeidcard');

	// Religion Controller
	Route::resource('favourite_link',			    'FavouriteLinkController');
	Route::post('favourite_link_list',				'FavouriteLinkController@favourite_link_list');
	Route::get('favourite_link/{id}/cancel/',		'FavouriteLinkController@cancel');
	Route::post('mynote_list',						'FavouriteLinkController@mynote_list');
	Route::post('mynote_store',						'FavouriteLinkController@mynote_store');
	Route::get('my_note/{id}/cancel/',				'FavouriteLinkController@mynote_cancel');



	Route::get('employeetransfer',		    	'TransferAndPromotionController@employeetransfer');
	Route::post('employee_transfer',			'TransferAndPromotionController@employee_transfer');
	Route::get('employeepromotion',		    	'TransferAndPromotionController@employeepromotion');
	Route::post('employee_promotion',			'TransferAndPromotionController@employee_promotion');
	Route::get('employeepromotioncreate',		'TransferAndPromotionController@employeepromotioncreate');
	Route::post('promotionandtransfer_list',	'TransferAndPromotionController@promotionandtransfer_list');
	Route::get('employeetransfercreate',		'TransferAndPromotionController@employeetransfercreate');
	Route::get('last_promotion_create',		    'TransferAndPromotionController@last_promotion_create');
	Route::post('last_promotion_list_data',	    'TransferAndPromotionController@last_promotion_list_data');
	Route::post('last_promotion_data_submit',	'TransferAndPromotionController@last_promotion_data_submit');


    Route::resource('reporting_boss_change',		'ReportingBossChangeController');
	Route::post('reportingboss_employeelist',		'ReportingBossChangeController@reportingEmployeeList');
	Route::get('reportingboss_change_list',		 'ReportingBossChangeController@reporting_boss_change_list');
    Route::post('remporting_boss_changes_emp_list',	 'ReportingBossChangeController@employeeList');



	// Employee Card Controller
	Route::resource('employeecard',				'EmployeeCardController');
	Route::post('employeecard_list',			'EmployeeCardController@employeecardlist');
	Route::get('employeecard/{id}/cancel/',		'EmployeeCardController@cancel');


	// Employee Leave Controller
	Route::resource('employeeleave',			'EmployeeLeaveController');
	Route::post('employeeleave_list',			'EmployeeLeaveController@employeeleavelist');
	Route::get('employeeleave/{id}/cancel/',	'EmployeeLeaveController@cancel');
	Route::get('employeeleave/{id}/delete/',	'EmployeeLeaveController@approve_delete');
	Route::get('leaveapprovedlist',	            'EmployeeLeaveController@approvedlist');
	Route::post('leaveapproved_list',        	'EmployeeLeaveController@leaveapprovedlist');
	Route::get('leaverejectedlist',	            'EmployeeLeaveController@leaverejectedlist');
	Route::post('leaverejected_list',        	'EmployeeLeaveController@leaverejected_list');
	Route::get('leaverejectedlist/{id}/reactive/', 'EmployeeLeaveController@leaverejected_reactive');
	Route::post('multiple-approve', 'EmployeeLeaveController@multipleApprove');

    //=================== EmployeeProfile ==================//
    Route::get('my_leaves', 'EmployeeProfileController@my_leaves');
    Route::get('my_leaves/create', 'EmployeeProfileController@my_leaves_create');
    Route::post('my_leaves', 'EmployeeProfileController@my_leaves_store')->name('my_leaves_store');
    Route::get('my_leaveapprovedlist', 'EmployeeProfileController@my_leaveapprovedlist');
    Route::get('my_leaverejectedlist', 'EmployeeProfileController@my_leaverejectedlist');

    Route::get('my_attendance', 'EmployeeProfileController@myAttendance');
    Route::get('my_attendance_details/{id}', 'EmployeeProfileController@my_attendance_details');
    //=================== EmployeeProfile ==================//


	// Employee Resign Controller
	Route::resource('employeeresign',			 'EmployeeResignController');
	Route::post('employeeresign_list',			 'EmployeeResignController@employeeresignlist');
	Route::get('employeeresign/{id}/cancel/',	 'EmployeeResignController@cancel');
	Route::post('resignapprovesubmit',		     'EmployeeResignController@resignapprovesubmit');
	Route::get('resignapproved',		         'EmployeeResignController@resignapproved')->name('resignapproved');
	Route::post('resignapprovedlist',		     'EmployeeResignController@resignapprovedlist');
	Route::get('employeeresign/{id}/rejoin/',	 'EmployeeResignController@resign_rejoin');
	Route::post('shiftdatechange_single',		 'EmployeeResignController@shiftdatechange_single');
    Route::get('resignapproved_export',		     'EmployeeResignController@resignApprovedExport')->name('resignapproved_export');

	Route::get('employee_rejoin/{id}',	        'EmployeeResignController@employee_rejoin');
	Route::post('employee_rejoin_submit',			    'EmployeeResignController@employeeRejoinSubmit')->name('employee_rejoin_submit');


	// Employee Inactive Controller
	Route::resource('employeeinactive',			   'EmployeeInactiveController');
	Route::post('employeeinactive_list',		   'EmployeeInactiveController@employeeinactivelist');
	Route::get('employeeinactive/{id}/cancel/',	   'EmployeeInactiveController@cancel');
	Route::get('reactive',		           		   'EmployeeInactiveController@reactive');
	Route::post('employeereactive_list',		   'EmployeeInactiveController@employeereactive_list');
	Route::get('employeeinactive/{id}/reactive/',  'EmployeeInactiveController@employee_reactive');
	Route::post('reactive_employee',		       'EmployeeInactiveController@reactive_employee');


	Route::resource('employeelongservice',		    'EmployeeLongServiceController');
	Route::post('employeelongservice_list',		    'EmployeeLongServiceController@employeelongservice_list');
	Route::get('employeelongservice/{id}/cancel/',  'EmployeeLongServiceController@cancel');
	Route::get('employeelongservice/{id}/createnew','EmployeeLongServiceController@createnew');
	Route::get('service_completed_list',		    'EmployeeLongServiceController@service_completed_list');
	Route::post('service_completed_list_data',		'EmployeeLongServiceController@service_completed_list_data');





	//Leave Approve Controller
	Route::resource('leaveapprove',		    	'LeaveApproveController');
	Route::post('waitingleave_approve_list',	'LeaveApproveController@leaveapprove_waitinglist');
	Route::get('print_leave_form/{id}/',	    'LeaveApproveController@print_leave_form');




	// Location Controller
	Route::resource('location',					'LocationController');
	Route::post('location_list',				'LocationController@locationlist');
	Route::get('location/{id}/cancel/',			'LocationController@cancel');



	// Shift Role Controller
	Route::resource('shiftrole',				'ShiftRoleController');
	Route::post('shiftrole_list',				'ShiftRoleController@shiftrolelist');
	Route::get('shiftrole/{id}/cancel/',		'ShiftRoleController@cancel');


	// Section Controller
	Route::resource('section',					'SectionController');
	Route::post('section_list',					'SectionController@sectionlist');
	Route::get('section/{id}/cancel/',			'SectionController@cancel');


	// GovtHoliday Controller
	Route::resource('holiday',					'HolidayController');
	Route::post('holidaylist',					'HolidayController@holidaylist');
	Route::get('holiday/{id}/cancel/',			'HolidayController@cancel');

    // Salary Holdup Types Controller
	Route::resource('salary_holdup',			'SalaryHoldupTypesController');
	Route::post('salary_holdup_list',			'SalaryHoldupTypesController@salaryHoldupList');
	Route::get('salary_holdup/{id}/cancel/',    'SalaryHoldupTypesController@cancel');

    // Salary Holdup Application Controller
	Route::resource('holdup_application',			'SalaryHoldupApplicationController');
	Route::post('holdup_application_list',			'SalaryHoldupApplicationController@holdup_application_list');
	Route::get('holdup_application/{id}/cancel/',   'SalaryHoldupApplicationController@cancel');
    Route::post('holdup_application_get_data',        'SalaryHoldupApplicationController@holdup_application_get_data');
    Route::post('holdup_application_action',        'SalaryHoldupApplicationController@holdup_application_action');

   // Salary Unpaid & Paid Controller
    Route::get('holdup-unpaid-list', 'SalaryHoudup_realiseController@unpaidListView');
    Route::get('holdup-paid-list', 'SalaryHoudup_realiseController@paidListView');
    Route::get('holdup_application/{id}/cancel', 'SalaryHoudup_realiseController@cancel');
    Route::post('holdup_application_action', 'SalaryHoudup_realiseController@holdup_application_action');
    Route::post('holdup-application-lists', 'SalaryHoudup_realiseController@holdup_application_lists');
    Route::post('holdup_application_individual', 'SalaryHoudup_realiseController@holdup_application_individual');
    // Route::get('holdup-application-list', 'SalaryHoldupRealiseController@holdup_unpaid_list');

    Route::post('holdup_paid_action', 'SalaryHoudup_realiseController@holdup_paid_action');
    Route::post('get-holdup-paid-list-data', 'SalaryHoudup_realiseController@holdup_paid_list');









	//Attendance Controller
	Route::resource('attendancelist',   	    'AttendanceController');
	Route::get('machinedataupload',			    'AttendanceController@machinedataupload');
	Route::post('attendancedataupload',			'AttendanceController@attendancedataupload');
	Route::post('attendancelistdata',	        'AttendanceController@attendancelistdata');
	Route::get('refresh_log/{id}',	        		'AttendanceController@refresh_log');


	Route::get('rawdatacheck',					'AttendanceController@rawdatacheck');
	Route::post('rawdatalist',					'AttendanceController@rawdatalist');
	Route::get('delete_duplicate_data/{id}',    'AttendanceController@delete_duplicate_data');

	if(config('module_config.attendance_api_data') == 1){
	    Route::get('pull_attendance',			    'AttendanceController@pull_attendance');
		Route::post('data_sync',			        'AttendanceController@data_sync')->name('data_sync');
	}
	//Manual Attendance Controller
	Route::post('manual_attendancelistdata',	'ManualAttendanceController@manual_attendancelistdata');
	Route::get('manual_attendance',	            'ManualAttendanceController@manual_attendance');
	Route::get('manual_attendance_entry',	    'ManualAttendanceController@manual_attendance_entry');
	Route::post('manual_attendance_submit',	    'ManualAttendanceController@manual_attendance_submit');
	Route::get('manual_attendance/{id}/cancel/','ManualAttendanceController@manual_attendance_delete');
	Route::get('manual_in',	                    'ManualAttendanceController@manual_in');
	Route::get('manual_out',	                'ManualAttendanceController@manual_out');
	Route::post('manual_in_out_listdata',	    'ManualAttendanceController@manual_inoutlistdata');
	Route::post('submitmanual_inout',	        'ManualAttendanceController@submitmanual_inout');


	Route::resource('manual_ot',				        'ManualOTController');
	Route::post('manual_otlistdata',	                'ManualOTController@manual_otlistdata');
	Route::get('manual_ot/{id}/cancel/',		        'ManualOTController@cancel');
	Route::get('manual_ot_all',					        'ManualOTController@manual_ot_all');
	Route::post('submitmanual_otall',			        'ManualOTController@submitmanual_otall');
	Route::post('manual_ot_listdata',			        'ManualOTController@manual_ot_listdata');
	Route::get('manual_ot_process',				        'ManualOTController@manual_ot_process');
	Route::post('manual_ot_process_submit',	            'ManualOTController@manual_ot_process_submit');
	Route::get('manual_ot_view',				        'ManualOTController@manual_ot_view');
	Route::post('manual_ot_view_data',			        'ManualOTController@manual_ot_view_data');
	Route::get('ot_adjust',				        		'ManualOTController@ot_adjust');
	Route::post('submit_manual_process_ot_edit',        'ManualOTController@submit_manual_process_ot_edit');
	Route::post('submit_manual_process_ot_edit_single', 'ManualOTController@submit_manual_process_ot_edit_single');
	Route::post('otprocess_list',		                'ManualOTController@otprocess_list');
	Route::get('deleteotprocess/{id}/cancel/',          'ManualOTController@deleteotprocess');
	Route::get('ot_process_create',                     'ManualOTController@ot_process_create');
	Route::get('ot_process_generate',			        'ManualOTController@ot_process_generate');
	Route::post('manual_ot_generate_submit',	        'ManualOTController@manual_ot_generate_submit');

	// Casual Worker OT Adjustment Panel
	Route::get('ot_adjust_create',			            'ManualOTController@ot_adjust_create');
	Route::post('ot_adjust_submit',	                    'ManualOTController@ot_adjust_submit');
	// END Casual Worker OT Adjustment Panel





	Route::get('add_eligible_ot_emp',        	            'ManualOTController@add_eligible_ot_emp');
	Route::get('eligible_ot_emp',             	            'ManualOTController@eligible_ot_emp');
	Route::get('eligible_ot_view_data',	                    'ManualOTController@eligible_ot_view_data');
	Route::get('date_wise_eligible_ot_list',	            'ManualOTController@date_wise_eligible_ot_list');
	Route::get('update_date_wise_eligible_ot_list/{id}',	'ManualOTController@update_date_wise_eligible_ot');
	Route::get('update_date_wise_eligible_ot_pending/{id}',	'ManualOTController@update_date_wise_eligible_ot_pending');
	Route::get('pending_ot_approval',	                    'ManualOTController@pending_ot_approval');
	Route::get('update_pending_ot_approvals/{id}',	        'ManualOTController@update_pending_ot_approvals');
	Route::POST('submit_eligible_ot_emp',	                'ManualOTController@submit_eligible_ot_emp');



    Route::resource('report_signatory',				 'ReportSignatoryController');
	Route::get('report_signatory_list_data',	         'ReportSignatoryController@report_signatory_list_data');



	//Employee Shift Change
	Route::get('employeeshiftlist',			    	'EmployeeShiftChangeController@employeeshiftlist');
	Route::get('employeeshiftlist_old',				'EmployeeShiftChangeController@employeeshiftlist_old');
	Route::post('employeecurrentshiftlist',	    	'EmployeeShiftChangeController@employee_current_shiftlist');
	Route::post('employeeoldshiftlist',	        	'EmployeeShiftChangeController@employee_old_shiftlist');
	Route::get('change_employeeshift',				'EmployeeShiftChangeController@change_employeeshift');
	Route::get('changeshift_multiple',				'EmployeeShiftChangeController@changeshift_multiple');
	Route::post('changeemployeeshift',	        	'EmployeeShiftChangeController@changeemployeeshift');
	Route::post('multipleemployee_shiftdata',		'EmployeeShiftChangeController@multipleemployee_shiftdata');
	Route::post('submitmultiple_shiftchange',		'EmployeeShiftChangeController@submitmultiple_shiftchange');
	Route::post('multipleshift',					'EmployeeShiftChangeController@multipleshiftchange');
	Route::get('wrongshiftassign',			    	'EmployeeShiftChangeController@wrongshiftassign');
	Route::post('wrongshiftassign_data',	    	'EmployeeShiftChangeController@wrongshiftassign_data');
	Route::post('wrong_to_correct_shift',	       	'EmployeeShiftChangeController@wrong_to_correct_shift');
	Route::post('wrong_to_correct_shift_permanent',	'EmployeeShiftChangeController@wrong_to_correct_shift_permanent');


    // Employee Auto Shift

    Route::resource('auto_shift', 'AutoShiftController');
    Route::post('auto_shift_list', 'AutoShiftController@getAutoShiftList');
    Route::get('change_auto_shift\{id}', 'AutoShiftController@changeShift')->name('autoShift.change');

	// ---
	Route::get('employeeshiftchange',		    'EmployeeShiftChangeController@employeeshiftchange');
	Route::post('submitprechangeemployeeshift',	'EmployeeShiftChangeController@submitprechangeemployeeshift');
	Route::get('pre_changeemployeeshift',		'EmployeeShiftChangeController@pre_changeemployeeshift');
	Route::post('pre_changeemployeeshiftlist',	'EmployeeShiftChangeController@pre_changeemployeeshiftlist');
	Route::get('pre_changeemployeeshift/{id}/cancel/','EmployeeShiftChangeController@cancel');


	//Employee Shift Role
	Route::resource('shiftroleemployee',   	     'ShiftRoleEmployeeController');
	Route::post('remaining_shiftrole_data',	     'ShiftRoleEmployeeController@remaining_shiftrole_data');
	Route::get('shiftroleemployeelist',			 'ShiftRoleEmployeeController@shiftroleemployeelist');
	Route::post('shiftrolewise_employeelist',    'ShiftRoleEmployeeController@shiftrolewise_employeelist');
	Route::get('shiftroleassign',			     'ShiftRoleEmployeeController@shiftroleassign');
	Route::get('shiftrole_assign',			     'ShiftRoleEmployeeController@shiftrole_assign');
	Route::post('shiftroleassigntoemployee',     'ShiftRoleEmployeeController@shiftroleassigntoemployee');
	Route::get('shiftroleemployee/{id}/cancel/', 'ShiftRoleEmployeeController@cancel');
	Route::post('delete_all',					 'ShiftRoleEmployeeController@delete_all');
	Route::post('getcurrentshift',				 'ShiftRoleEmployeeController@getcurrentshift');
	Route::post('assignrolelist',	             'ShiftRoleEmployeeController@shiftrole_currentshift');
	Route::get('previou_shiftrole_assign',	     'ShiftRoleEmployeeController@previou_shiftrole_assign');
	Route::post('previous_shiftrole_assign_list','ShiftRoleEmployeeController@previous_shiftrole_assign_list');


	// Route::get('url_to_delete_rows',           'ShiftRoleEmployeeController@url_to_delete_rows');
	// Plant Controller

	Route::resource('plant',					 'PlantController');
	Route::post('plant_list',					 'PlantController@plant_list');
	Route::get('plant/{id}/edit/',				 'PlantController@edit');
	Route::get('plant/{id}/plantnamecancel/',    'PlantController@plantnamecancel');


	// PlantSetupController

	Route::get('plant_setup',					 'PlantSetupController@plant_setup');
	Route::get('plant_setup/{id}/edit/',		 'PlantSetupController@plant_setup_edit');
	Route::get('plantwisesalarycreate',			 'PlantSetupController@plantwisesalarycreate');
	Route::post('plantwisesalary',	    		 'PlantSetupController@plantwisesalary');
	Route::post('plantwisesalary_list',			 'PlantSetupController@plantwisesalary_list');
	Route::post('update_plantwise_salary',	     'PlantSetupController@update_plantwise_salary');



	// PlantWithEmployeeController


	Route::get('plantwiseemployeeadd',			 'PlantWithEmployeeController@plantwiseemployeeadd');
	Route::post('remaining_plantrole_data',		 'PlantWithEmployeeController@remaining_plantrole_data');
	Route::post('plantroleemployeeadd',		     'PlantWithEmployeeController@plantroleemployeeadd');
	Route::get('plantwiseemployeelist',			 'PlantWithEmployeeController@plantwiseemployeelist');
	Route::post('plantwise_employeelist',		 'PlantWithEmployeeController@plantwise_employeelist');
	Route::get('plantroleemployee/{id}/cancel/', 'PlantWithEmployeeController@cancel');
	Route::post('delete_all_plantwise_employee', 'PlantWithEmployeeController@delete_all_plantwise_employee');



	// Plant With Section Controller

	Route::resource('plantwithsection',	     		'PlantWithSectionController');
	Route::post('plantwithsection_list',		    'PlantWithSectionController@plantwithsection_list');
	Route::get('plantwithsection/{id}/cancel/',     'PlantWithSectionController@cancel');
	Route::get('plantwithsection/{id}/reactive/',   'PlantWithSectionController@reactive');



	// Route::post('plant_list',					'PlantController@plant_list');
	// Route::get('plant/{id}/edit/',				'PlantController@edit');
	// Route::get('plant_setup',					'PlantController@plant_setup');
	// Route::get('plant_setup/{id}/edit/',		'PlantController@plant_setup_edit');
	// Route::get('plantwisesalarycreate',			'PlantController@plantwisesalarycreate');
	// Route::post('plantwisesalary',	    		'PlantController@plantwisesalary');
	// Route::post('plantwisesalary_list',			'PlantController@plantwisesalary_list');
	// Route::get('plantwiseemployeeadd',			'PlantController@plantwiseemployeeadd');
	// Route::post('remaining_plantrole_data',		'PlantController@remaining_plantrole_data');
	// Route::post('plantroleemployeeadd',		    'PlantController@plantroleemployeeadd');
	// Route::get('plantwiseemployeelist',			'PlantController@plantwiseemployeelist');
	// Route::post('plantwise_employeelist',		'PlantController@plantwise_employeelist');
	// Route::get('plantroleemployee/{id}/cancel/','PlantController@cancel');
	// Route::post('delete_all_plantwise_employee','PlantController@delete_all_plantwise_employee');
	// Route::post('update_plantwise_salary',	    'PlantController@update_plantwise_salary');
	// Route::get('plant/{id}/plantnamecancel/',   'PlantController@plantnamecancel');






	//EmployeeHolidayProcessController
	Route::resource('holiday_process',			'HolidayProcessController');
	Route::post('employeeholiday_list',	        'HolidayProcessController@employeeholiday_list');
	Route::get('holiday_process/{id}/edit',		'HolidayProcessController@edit');
	Route::get('holiday_process/{id}/action',	'HolidayProcessController@holiday_process');
	Route::get('approvedholidaylist',	        'HolidayProcessController@approvedholidaylist');
	Route::post('holiday_processlist',	        'HolidayProcessController@holiday_processlist');
	Route::post('holidayfor_selectedemployee',	'HolidayProcessController@holidayfor_selectedemployee');
	Route::get('holiday_process/{id}/delete',	'HolidayProcessController@holiday_process_delete');
	Route::get('holiday_process/{id}/deletepending',	'HolidayProcessController@deletepending');


	// Route::post('submit_holiday_process',	    'HolidayProcessController@submit_holiday_process');

	Route::resource('holiday_convert',			  'HolidayConvertToWorkingController');
	Route::post('holidayconvert_to_working_list', 'HolidayConvertToWorkingController@holidayconvert_to_working_list');
	Route::get('holiday_convert/{id}/delete',	  'HolidayConvertToWorkingController@destroy');






	//Attendance Data Process Controller
	Route::resource('data_process', 			'AttendanceDataProcessController');
	// Route::resource('data_process', 			'AttendanceDataProcessControllerNew');





	//Loan Application Controller
	Route::resource('loanapplication',			'LoanApplicationController');
	Route::post('loanapplication_list',			'LoanApplicationController@loanapplicationlist');
	Route::get('loanapplication/{id}/cancel/',	'LoanApplicationController@cancel');
	Route::post('loanapprove',			        'LoanApplicationController@loanapprove');
	Route::post('rescheduleloan',			    'LoanApplicationController@rescheduleloan');
	Route::get('loanapprovedlist',			    'LoanApplicationController@loanapprovedlist');
	Route::get('loanledger',			    	'LoanApplicationController@loanledger');
	Route::post('loanledgersummary_list',		'LoanApplicationController@loanledgersummary_list');
	Route::post('loanledgerdetails_list',		'LoanApplicationController@loanledgerdetails_list');
	Route::post('loanpayment',			    	'LoanApplicationController@loanpayment');
	Route::get('loanledgerdetails/{id}',		'LoanApplicationController@loanledgerdetails');
	Route::get('paidloanledger',			    'LoanApplicationController@paidloanledger');
	Route::post('paidloanledgersummary_list',	'LoanApplicationController@paidloanledgersummary_list');
	Route::get('loanledger_cancel/{id}',	    'LoanApplicationController@loanledgercancel');
	Route::get('employee-wise-loan',			    'LoanApplicationController@employeeWiseLoan');


	//Holiday Against Leave Controller
	Route::resource('holidayagainstleave',			  'HolidayAgainstLeaveController');
	Route::post('holidayagainstleavelist',			  'HolidayAgainstLeaveController@holidayagainstleavelist');
	Route::get('holidayagainstleave/{id}/cancel',     'HolidayAgainstLeaveController@cancel');
	Route::post('employeewiseholidaylist',		      'HolidayAgainstLeaveController@employee_wise_WorkOnHoliday');
	Route::get('holidayagainstleavehistory',		  'HolidayAgainstLeaveController@holidayagainstleavehistory');
	Route::post('holidayagainstleavehistorylist',     'HolidayAgainstLeaveController@holidayagainstleavehistorylist');


	//Employee Document Archive
	Route::resource('document_archive',			      'EmployeeFileController');
	Route::post('employeewisefile_list',		      'EmployeeFileController@employeewisefile_list');
	Route::post('filetypecreate',		              'EmployeeFileController@filetypecreate');
	Route::get('document_archive/{id}/view/',		  'EmployeeFileController@view');
	Route::get('document_archive/{id}/download/',	  'EmployeeFileController@download');
	Route::get('document_archive/{id}/cancel/',		  'EmployeeFileController@cancel');
	Route::post('summary_employeewisefile_list',	  'EmployeeFileController@summary_employeewisefile_list');
	Route::get('document_archive/{id}/view_details/', 'EmployeeFileController@view_details');
	Route::get('document_archive/create/{id}',        'EmployeeFileController@create');
	Route::post('edit_document_archive',		      'EmployeeFileController@edit_document_archive');
	Route::get('document_download/{id}',		      'EmployeeFileController@document_download');
	// Route::get('document_download/{id}',		      'EmployeeFileController@working_multiple_download');





	// Reports Controller
	Route::resource('reports',   	               'ReportsController');
	Route::post('attendance_report',   	           'ReportsController@attendance_report');
	// Route::post('manpower_report',   	           'ReportsController@manpower_report');
	Route::post('monthly_attendancesummary_report','ReportsController@monthly_attendancesummary_report');
	Route::post('departmentwisesummary',           'ReportsController@departmentwisesummary');
	Route::post('missingoutreport',                'ReportsController@missingoutreport');
	Route::post('monthly_overtime_report',         'ReportsController@monthly_overtime_report');
	Route::post('salaryreport',                    'ReportsController@salaryreport');
	Route::post('fringebenefitreport',             'ReportsController@fringebenefitreport');
	Route::post('monthly_attendancedetails_report','ReportsController@monthly_attendancedetails_report');
	Route::post('monthly_attendancedetails_with_inout','ReportsController@monthly_attendancedetails_with_inout');


	Route::post('monthly_jobcard_report',          'ReportsController@monthly_jobcard_report');
	Route::post('manpower_report',   	           'ReportsController@manpower_report');
	Route::post('categorywise_manpower_report',    'ReportsController@categorywise_manpower_report');
	Route::post('bonusreport',                     'ReportsController@bonusreport');
	Route::post('pfreport',                        'ReportsController@pfreport');
	Route::post('banksalaryreport',                'ReportsController@banksalaryreport');
	Route::post('compare_salary_report',           'ReportsController@compare_salary_report');
	Route::post('compare_OT_report',               'ReportsController@compare_OT_report');
	Route::post('monthly_ot_sheet',                'ReportsController@monthly_ot_sheet');
	Route::post('bloodgroup_report',               'ReportsController@bloodgroup_report');
	Route::post('hrm_cw_salary_report',            'ReportsController@hrm_cw_salary_report');
	Route::post('individual_leave_ledger_report',  'ReportsController@individual_leave_ledger_report');
	Route::post('employee_leave_summary_report',   'ReportsController@employee_leave_summary_report');
	Route::post('outofpocket_report',  			   'ReportsController@outofpocket_report');
	Route::post('hrm_fringbenefit_locationwise',   'ReportsController@hrm_fringbenefit_locationwise');
	Route::post('fringbenefit_designationwise',    'ReportsController@fringbenefit_designationwise');
	Route::post('fringbenefit_employeewise',  	   'ReportsController@fringbenefit_employeewise');
	Route::post('fring_benefit_topsheet',  		   'ReportsController@fring_benefit_topsheet');




	Route::post('monthly_carallowance',  		   'ReportsController@monthly_carallowance');
	Route::post('employeelog_report',  		       'ReportsController@employeelog_report');
	Route::post('monthly_topSheet',  		       'ReportsController@monthly_topSheet');
	Route::post('monthly_topSheet_amg',  		   'ReportsController@monthly_topSheet_amg');
	Route::post('bonus_topSheet',  		           'ReportsController@bonus_topSheet');
	Route::post('salary_headwise_report',  		   'ReportsController@salary_headwise_report');
	Route::post('salary_review',  		           'ReportsController@salary_review');





	//Bulk Data Upload
	Route::resource('bulkdataupload',	         'BulkDataUploadController');
	Route::get('employeebulkdataupload',		 'BulkDataUploadController@employeebulkdataupload');
	Route::get('fingercarddataupload',		     'BulkDataUploadController@fingercarddataupload');
	Route::post('importExcel',					 'BulkDataUploadController@importExcel');
	Route::post('importExcel2',					 'BulkDataUploadController@importExcel2');
	Route::get('downloadExcel/{type}',			 'BulkDataUploadController@downloadExcel');
	Route::get('downloadExcel2/{type}',			 'BulkDataUploadController@downloadExcel2');
	Route::get('downloadExcel_fingercard',		 'BulkDataUploadController@downloadExcel_fingercard');
	Route::post('importExcel_fingercard',		 'BulkDataUploadController@importExcel_fingercard');
	Route::get('employeesalaryinsert',		     'BulkDataUploadController@employeesalaryinsert');
	Route::get('jobinfotoemployeecard',		     'BulkDataUploadController@jobinfotoemployeecard');
	Route::get('deleteattendancedata_dateWise',  'BulkDataUploadController@deleteattendancedata_dateWise');



	// Salary Related Controller

	//Salary Grade Controller
	Route::resource('salarygrade',   	        'SalaryGradeController');
	Route::post('salarygrade_list',				'SalaryGradeController@salarygrade_list');
	Route::get('salarygrade/{id}/cancel/',      'SalaryGradeController@cancel');
	Route::post('salarygradeupdate',			'SalaryGradeController@salarygradeupdate');


	//Salary Head Group Controller
	Route::resource('salaryheadgroup',   	    'SalaryHeadGroupController');
	Route::post('salaryheadgroup_list',			'SalaryHeadGroupController@salaryheadgroup_list');
	Route::get('salaryheadgroup/{id}/cancel/',  'SalaryHeadGroupController@cancel');
	Route::post('salaryheadgroupupdate',		'SalaryHeadGroupController@salaryheadgroupupdate');

	//Salary Head Controller

	Route::resource('salaryhead',   	         'SalaryHeadController');
	Route::post('salaryhead_list',			     'SalaryHeadController@salaryhead_list');
	Route::get('salaryhead/{id}/cancel/',        'SalaryHeadController@cancel');
	Route::get('salaryhead/{id}/edit',	         'SalaryHeadController@edit');
	Route::get('defaultsalaryheadsetup',         'SalaryHeadController@defaultsalaryheadsetup');
	Route::post('setdefaultsalaryhead',			 'SalaryHeadController@setdefaultsalaryhead');
	Route::post('providentfundconfiquration',    'SalaryHeadController@providentfundconfiquration');
	Route::post('providentfundconfiqu_data',      'SalaryHeadController@providentfundconfiqu_data');






	// Salary grade Setup Controller
	Route::resource('salarygradesetup',   	      'SalaryGradeSetupController');
	Route::get('salarygradesetup/{id}/cancel/',   'SalaryGradeSetupController@cancel');
	Route::get('salarygradesetup/{id}/edit',	  'SalaryGradeSetupController@edit');
	Route::post('salarygradesetuplistdata',		  'SalaryGradeSetupController@salarygradesetuplistdata');




	// Bonus Controller
	Route::resource('bonus',					 'BonusController');
	Route::post('bonus_list',					 'BonusController@bonuslist');
	Route::get('bonus/{id}/cancel/',			 'BonusController@cancel');


	//Employee Bonus Controller
	Route::resource('bonusprocess',				'EmployeeBonusController');
	Route::get('employeebonus',		            'EmployeeBonusController@employeebonus');
	Route::get('add_employee_to_bonus',		    'EmployeeBonusController@add_employee_to_bonus');
	Route::post('add_employee_to_bonus',		'EmployeeBonusController@add_employee_to_bonus_submit');
	Route::post('bonuslistdata',				'EmployeeBonusController@bonuslistdata');
	Route::post('deletebonusprocess',		    'EmployeeBonusController@deletebonusprocess');
	Route::get('employeebonus/{id}/edit',	    'EmployeeBonusController@edit');
	Route::get('employeebonus/{id}/cancel',	    'EmployeeBonusController@cancel');
	Route::get('bonusgenerate',		            'EmployeeBonusController@bonusgenerate');
	Route::post('bonusgeneratesubmit',		    'EmployeeBonusController@bonusgeneratesubmit');
	Route::post('bonusprocess_list',            'EmployeeBonusController@bonusprocess_list');

	Route::get('bonusprocesscw',		        'EmployeeBonusController@bonusprocesscw');
	Route::post('bonusprocessforcw',		    'EmployeeBonusController@bonusprocessforcw');



	//Employee Salary Controller
	Route::get('employeesalary/dynamicTable/',     'EmployeeSalaryController@dynamicTable');
	Route::resource('employeesalary',   	      'EmployeeSalaryController');
	Route::get('employeesalary/{id}/cancel/',     'EmployeeSalaryController@cancel');
	Route::get('employeesalary/{id}/edit',	      'EmployeeSalaryController@edit');
	Route::post('employeesalarylistdata',		  'EmployeeSalaryController@employeesalarylistdata');
	Route::post('bankname_create',		          'EmployeeSalaryController@bankname_create');


	// Route::resource('otherfacility',   	          'OtherFacilityController');
	Route::resource('otherfacility',   	          'OtherFacilityController'); //->middleware('permission:OtherFacility');
	Route::get('otherfacility/{id}/cancel/',      'OtherFacilityController@cancel');
	Route::get('otherfacility/{id}/edit',	      'OtherFacilityController@edit');
	Route::post('otherfacilitylistdata',		  'OtherFacilityController@otherfacilitylistdata');

	// Route::get('otherfacilityconfiq',      		  'OtherFacilityController@otherfacilityconfiq');
	Route::get('otherfacilityconfiq',      		  'OtherFacilityController@otherfacilityconfiq'); // ->middleware('permission:OtherFacilityProcess');



	Route::get('otherfacilityconfiq/create',      'OtherFacilityController@otherfacilityconfiqcreate');
	Route::post('otherfacilityconfiqsubmit',      'OtherFacilityController@otherfacilityconfiqsubmit');
	Route::post('otherfacilityconfiglistdata',	  'OtherFacilityController@otherfacilityconfiglistdata');
	Route::get('otherfacilityconfiq/{id}/edit',	  'OtherFacilityController@otherfacilityconfiqedit');
	Route::post('otherfacilityconfiqeditsubmit',  'OtherFacilityController@otherfacilityconfiqeditsubmit');
	Route::post('otherfacilityconfiqstore',       'OtherFacilityController@otherfacilityconfiqstore');


	//Other Facility Process Controller
	Route::resource('otherfacilityprocess',                'OtherFacilityProcessController');
	Route::get('deleteotherfacilityprocess/{id}/cancel/',  'OtherFacilityProcessController@deleteotherfacilityprocess');
	Route::post('otherfacilityprocess_list',               'OtherFacilityProcessController@otherfacilityprocess_list');



	// Route::filter('otherfacility', function()
	// {
	//     // check the current user
	//     if (!Entrust::can('OtherFacility')) {
	//         return Redirect::to('/');
	//     }


	// });

	//Employee Salary Controller
	// if(!Entrust::can('OtherFacility')) {
	//     Redirect::to('/');
	// }else{

	// }




	//Salary Process Controller
	Route::resource('salaryprocess',   	           'SalaryProcessController');
	Route::post('salaryprocess_list',		       'SalaryProcessController@salaryprocess_list');
	Route::get('deletesalaryprocess/{id}/cancel/', 'SalaryProcessController@deletesalaryprocess');



	//Partial Salary Process Controller
	Route::resource('partialsalaryprocess',   	  'PartialSalaryProcessController');
	Route::post('deletepartialsalaryprocess',     'PartialSalaryProcessController@deletepartialsalaryprocess');
	Route::post('createsalaryslap',     		  'PartialSalaryProcessController@createsalaryslap');





	//Pay Register Controller
    Route::get('payregister/details',		  'PayRegisterController@payregisterDetails');
    Route::get('payregister/searchDetails',		  'PayRegisterController@searchPayregisterDetails');
	Route::resource('payregister',   	          'PayRegisterController');
    Route::get('employee_wise_salary_process',    'PayRegisterController@employee_wise_salary_process')->name('employee_wise_salary_process');
    Route::post('employee_wise_search',           'PayRegisterController@employee_wise_search')->name('employee_wise_search');
    Route::get('employee_salary_form/{id}',        'PayRegisterController@employee_salary_form')->name('employee_salary_form');
    Route::post('pay_register/salary_create',      'PayRegisterController@salary_create')->name('employee_salary.create');
	// Route::get('payregister/{id}/edit',	          'PayRegisterController@edit');
	Route::post('payregisterlistdata',		      'PayRegisterController@payregisterlistdata');
	Route::get('payregister/{id}/print',		  'PayRegisterController@show');
	Route::post('additional_deduction_add',		  'PayRegisterController@additional_deduction_add');
	Route::resource('ofpayregister',   	          'OtherFacilityPayRegisterController');
	Route::get('ofpayregister/{id}/edit',	      'OtherFacilityPayRegisterController@edit');
	Route::post('ofpayregisterlistdata',		  'OtherFacilityPayRegisterController@payregisterlistdata');
	Route::get('ofpayregister/{id}/print',		  'OtherFacilityPayRegisterController@show');
	Route::post('ofadditional_deduction_add',     'OtherFacilityPayRegisterController@additional_deduction_add');


	Route::get('salary_deduction_index',   	       'SalaryExtraFeaturesController@salary_deduction_index');
	Route::get('salary_deduction_create',   	   'SalaryExtraFeaturesController@salary_deduction_create');
	Route::get('salary_deduction/{id}/edit',   	   'SalaryExtraFeaturesController@salary_deduction_edit');
	Route::get('salary_deduction/{id}/delete',     'SalaryExtraFeaturesController@salary_deduction_delete');
	Route::post('salary_deduction_post',   	       'SalaryExtraFeaturesController@salary_deduction_post');
	Route::patch('salary_deduction_update/{id}',   'SalaryExtraFeaturesController@salary_deduction_update');
	Route::post('salary_deduction_list_data',      'SalaryExtraFeaturesController@salary_deduction_list_data');

	Route::resource('salaryextrafeatures',   	   'SalaryExtraFeaturesController');
	Route::post('salary_extra_listdata',		   'SalaryExtraFeaturesController@salary_extra_listdata');
	Route::post('role_salaryextra_listdata',	   'SalaryExtraFeaturesController@role_salaryextra_listdata');
	Route::get('salaryextra/{id}/cancel/',         'SalaryExtraFeaturesController@destroy');
	Route::get('salaryextra/{id}/edit',	           'SalaryExtraFeaturesController@edit');
	Route::get('salaryextradetails/{id}/delete',   'SalaryExtraFeaturesController@delete');
	Route::post('salaryextrafeaturesupdate',	   'SalaryExtraFeaturesController@salaryextrafeaturesupdate');
	Route::get('extradetails/update/{id}/{amount}','SalaryExtraFeaturesController@updatedetails');


	Route::resource('salaryincrement',   	                 'SalaryIncrementController');
	Route::post('salary_increment_listdata',		         'SalaryIncrementController@salary_increment_listdata');
	Route::post('role_salaryincrement_listdata',	         'SalaryIncrementController@role_salaryincrement_listdata');
	Route::post('past_salaryincrement_listdata',	         'SalaryIncrementController@past_salaryincrement_listdata');

	Route::get('salaryincrement/{id}/cancel/',               'SalaryIncrementController@destroy');
	Route::get('salaryincrement/{id}/edit',	                 'SalaryIncrementController@edit');
	Route::get('salaryincrementdetails/{id}/delete',         'SalaryIncrementController@delete');
	Route::post('salaryincrementupdate',	                 'SalaryIncrementController@salaryincrementupdate');
	Route::get('salaryincrementdetails/update/{id}/{amount}','SalaryIncrementController@incrementupdatedetails');
	Route::get('salaryincrementpreviousdata',		         'SalaryIncrementController@salaryincrementpreviousdata');


	Route::get('incrementApproved/{id}/{location_id}',    'SalaryIncrementController@incrementApproved');







	//Salary Generate Controller
	Route::resource('salarygenerate',   	      'SalaryGenerateController');


	//Other Facility Generate Controller
	Route::resource('otherfacilitygenerate',   	   'OtherFacilityGenerateController');


	Route::resource('cwsalaryprocess',   	         'CWSalaryProcessController');
	//Route::post('deletecwsalaryprocess',		     'CWSalaryProcessController@deletecwsalaryprocess');
	Route::post('cw_salary_process_list',		     'CWSalaryProcessController@cw_salary_process_list');
	Route::get('cwsalaryprocessdelete/{id}/cancel/', 'CWSalaryProcessController@cwsalaryprocessdelete');




	Route::resource('cwemployeeinfo',   	          'CWEmployeeInfoController');
	Route::post('cw_employeesalarylistdata',		  'CWEmployeeInfoController@cw_employeesalarylistdata');
	Route::get('cw_employeesalarylistdata/{id}/edit', 'CWEmployeeInfoController@edit');






	Route::resource('cwpayregister',   	          'CWPayRegisterController');
	Route::post('cwpayregisterlistdata',		  'CWPayRegisterController@cwpayregisterlistdata');
	Route::get('cwpayregister/{id}/edit',	      'CWPayRegisterController@edit');
	Route::get('cwpayregister/{id}/delete',	      'CWPayRegisterController@delete');


	Route::resource('cwsalarygenerate',   	      'CWSalaryGenerateController');


	Route::resource('task_department',   	      'KPITaskDepartmentController');
	Route::post('task_departmentlistdata',		  'KPITaskDepartmentController@task_departmentlistdata');
	Route::get('task_department/{id}/edit',	      'KPITaskDepartmentController@edit');

	Route::resource('kpi_deptheadassign',   	  'KPIDeptHeadAssignController');
	Route::post('kpi_deptheadassignlistdata',	  'KPIDeptHeadAssignController@kpi_deptheadassignlistdata');
	Route::get('kpi_deptheadassign/{id}/edit',	  'KPIDeptHeadAssignController@edit');
	Route::get('kpi_deptheadassign/{id}/cancel',  'KPIDeptHeadAssignController@cancel');



	Route::resource('kpi_assessment_date_year',	  'KPIAssessmentDateYearController');
	Route::post('kpi_year_list',	              'KPIAssessmentDateYearController@kpi_year_list');
	Route::get('kpi_year/{id}/cancel/',           'KPIAssessmentDateYearController@cancel');
	Route::get('kpi_date_setup',                  'KPIAssessmentDateYearController@kpi_date_setup');
	Route::post('kpi_assessment_date_store',	  'KPIAssessmentDateYearController@kpi_assessment_date_store');
	Route::post('kpi_date_list',                  'KPIAssessmentDateYearController@kpi_date_list');
	Route::get('kpi_date_setup/{id}/cancel/',     'KPIAssessmentDateYearController@kpi_date_setupcancel');

	Route::resource('kpi_markdeduct',	          'KPIMarkDeductionController');
	Route::post('kpi_markdeduct_list',            'KPIMarkDeductionController@kpi_markdeduct_list');
	Route::get('kpi_markdeduct/{id}/cancel/',     'KPIMarkDeductionController@cancel');


	Route::resource('kpi_task',   	      'KPITaskController');
	Route::post('kpi_task_list',		  'KPITaskController@kpi_task_list');
	Route::get('kpi_task/{id}/cancel',	  'KPITaskController@cancel');
	Route::post('kpi_task_type',		  'KPITaskController@kpi_task_type');




	Route::resource('kpi_mark',   	      'KPIMarkController');
	Route::post('kpi_marks_list',		  'KPIMarkController@kpi_marks_list');
	Route::get('kpi_mark/{id}/cancel',	  'KPIMarkController@cancel');


	Route::resource('kpi_increment_range',   	  'KPIIncrementRangeController');
	Route::post('kpi_increment_range_list',		  'KPIIncrementRangeController@kpi_increment_range_list');
	Route::get('kpi_increment_range/{id}/cancel', 'KPIIncrementRangeController@cancel');




	Route::resource('kpi_task_details',            'KPITaskDetailsController');
	Route::post('kpi_task_details_list',           'KPITaskDetailsController@kpi_task_details_list');
	Route::post('kpi_task_deptwise_list',          'KPITaskDetailsController@kpi_task_deptwise_list');



	Route::resource('kpi_employee',                  'KPIEmployeeController');
	Route::get('kpi_employee/{date_id}/{id}/create', 'KPIEmployeeController@createnew');
	Route::post('kpi_task_list_by_department',       'KPIEmployeeController@kpi_task_list_by_department');
	Route::get('kpi_employee_list',                  'KPIEmployeeController@kpi_employee_list');
	Route::post('kpi_employee_list_data',            'KPIEmployeeController@kpi_employee_list_data');
	Route::post('kpi_employee_list_user_wise',       'KPIEmployeeController@kpi_employee_list_user_wise');
	Route::post('kpi_employee_final_submit',         'KPIEmployeeController@kpi_employee_final_submit');
	Route::get('kpi_employee_final_list',            'KPIEmployeeController@kpi_employee_final_list');
	Route::post('kpi_employee_final_list_data',      'KPIEmployeeController@kpi_employee_final_list_data');
	Route::get('kpi_employee/{id}/edit',	         'KPIEmployeeController@edit');
	Route::get('kpi_task_list_by_emp_jobid',        'KPIEmployeeController@kpi_task_list_by_emp_jobid');
	Route::get('kpi_employee/{id}/cancel',	         'KPIEmployeeController@cancel');
	Route::get('kpi_employee_final/{id}/cancel',	 'KPIEmployeeController@final_cancel');
	Route::post('final_data_cancel',                 'KPIEmployeeController@final_data_cancel');
	Route::get('kpi_employee/{id}/addnote',	         'KPIEmployeeController@addnote');
	Route::post('kpi_employee_final_addnote_submit', 'KPIEmployeeController@kpi_employee_final_addnote_submit');




	Route::resource('kpi_report',                     'KPIReportController');
	Route::get('kpi_employee/{id}/print',	          'KPIReportController@show');
	Route::post('kpi_summary_report',                 'KPIReportController@kpi_summary_report');


    // KPI Config
    Route::get('hrm_kpi_dynamic_table_list', 'KPIConfigController@dynamicTableList');

    Route::get('hrm_kpi_task_store', 'KPIConfigController@kpiTaskStore')->name('tasks.store');
    Route::get('hrm_kpi_task_remove', 'KPIConfigController@kpiTaskRemove')->name('tasks.remove');

    Route::get('hrm_kpi_categories_store', 'KPIConfigController@kpiCategoriesStore')->name('categories.store');
    Route::get('hrm_kpi_categories_remove', 'KPIConfigController@kpiCategoriesRemove')->name('categories.remove');

    Route::get('hrm_departments_store', 'KPIConfigController@departmentsStore')->name('departments.store');
    Route::get('hrm_departments_set', 'KPIConfigController@departmentsSet')->name('departments.set');
    Route::get('hrm_departments_remove', 'KPIConfigController@departmentsRemove')->name('departments.remove');
    Route::get('hrm_departments_remove_not_na', 'KPIConfigController@departmentsRemoveNotNa')->name('departments.remove.not_na');
    Route::get('hrm_departments_detail', 'KPIConfigController@departmentsDetail')->name('departments.detail');

    Route::post('config-final-submit', 'KPIConfigController@configFinalSubmit')->name('config-final-submit');

    Route::resource('kpi_config', 'KPIConfigController');



	Route::get('interval_time',	                      'BaseConfiqController@interval_time');
	Route::post('interval_time_store',	              'BaseConfiqController@interval_time_store');
	Route::post('interval_time_list',	              'BaseConfiqController@interval_time_list');
	Route::get('interval_time/{id}/cancel',	          'BaseConfiqController@interval_time_cancel');

	Route::get('device_config',	                      'BaseConfiqController@device_config');
	Route::post('device_config_store',	              'BaseConfiqController@device_config_store');
	Route::post('device_config_list',	              'BaseConfiqController@device_config_list');
	Route::get('device_config/{id}/cancel',	          'BaseConfiqController@device_config_cancel');

	Route::get('overtime_config',	                  'BaseConfiqController@overtime_config');
	Route::post('overtime_config_store',	          'BaseConfiqController@overtime_config_store');
	Route::post('overtime_config_list',	              'BaseConfiqController@overtime_config_list');
	Route::get('overtime_config/{id}/cancel',	      'BaseConfiqController@overtime_config_cancel');

	// Route::get('weekly_holiday_config',	              'BaseConfiqController@weekly_holiday_config');

    // Weekend

    Route::resource('weekend_config', 'WeekendController');
    Route::post('weekend_list', 'WeekendController@getEmployeeWeekend');
    Route::get('employee_weekend_details', 'WeekendController@getWeekendDetails');
    Route::get('weekend_change/{id}', 'WeekendController@changeWeekend')->name('weekend_change');

	// Holidays related routes start
	Route::get('weekly_holiday_config',	'BaseConfiqController@weekly_holiday_config');
	Route::get('weekly_holiday_config_create', 'BaseConfiqController@weekly_holiday_config_create');
	Route::post('weekly_holiday_config_store', 'BaseConfiqController@weekly_holiday_config_store');
	Route::post('weekly_holiday_config_change_location', 'BaseConfiqController@weekly_holiday_config_change_location');
	Route::post('weekly_holiday_config_data_list', 'BaseConfiqController@weekly_holiday_config_data_list');

	Route::get('employee_holiday_config_list', 'EmployeeController@employee_holiday_config_list');
	Route::get('employee_holiday_config_create', 'EmployeeController@employee_holiday_config_create');
	Route::post('employee_holiday_config_store', 'EmployeeController@employee_holiday_config_store');
	Route::post('employee_holiday_config_data_list', 'EmployeeController@employee_holiday_config_data_list');
	Route::get('employee_holiday_config_edit/{id}', 'EmployeeController@employee_holiday_config_edit')->name('employee_holiday_config_edit');
	Route::post('employee_holiday_config_update/{id}', 'EmployeeController@employee_holiday_config_update');
	// Holidays related routes end



	Route::resource('current_month_app_user',         'MobileAppAttendanceController');
	Route::post('get_current_month_app_user',	      'MobileAppAttendanceController@get_current_month_app_user');
	Route::post('get_current_month_app_user_list',    'MobileAppAttendanceController@get_current_month_app_user_list');
	Route::post('get_app_user_details_log_list',      'MobileAppAttendanceController@get_app_user_details_log_list');
	Route::get('app_user_log',	      				  'MobileAppAttendanceController@app_user_log');
	Route::get('app_user_details_log',	      		  'MobileAppAttendanceController@app_user_details_log');
	Route::get('app_user_details_log',	      		  'MobileAppAttendanceController@app_user_details_log');
	Route::get('app_user_details_log',	      		  'MobileAppAttendanceController@app_user_details_log');
	Route::post('attendancelistdata_forappuser',	  'MobileAppAttendanceController@attendancelistdata_forappuser');
	Route::get('getuserwisedata/{id}/{date}/getdata', 'MobileAppAttendanceController@getuserwisedata');
	Route::get('leaflet_map/{id}/',                   'MobileAppAttendanceController@leaflet_map');

	// Asset

	Route::post('asset_list',						'AssetInformationController@asset_list');
	Route::get('asset_list_data',					'AssetInformationController@asset_list_data');
	Route::resource('asset',						'AssetInformationController');

	Route::resource('asset_type', 					'AssetTypeController');
	Route::post('asset_type_list', 					'AssetTypeController@asset_type_list');
	Route::get('asset_type_list_data', 				'AssetTypeController@asset_type_list_data');

	Route::resource('brand', 						'AssetBrandController');
	Route::post('brand_list', 						'AssetBrandController@brand_list');
	Route::get('brand_list_data', 					'AssetBrandController@brand_list_data');

	// assetstore
	Route::get('assetstore/{id}/lock',				'AssetStoreMasterController@lock');
	Route::resource('assetstore',					'AssetStoreMasterController');
	Route::post('asset_store_list',					'AssetStoreMasterController@asset_store_list');

	Route::get('check_unique_serial_no',            'AssetStoreMasterController@check_unique_serial_no');

	// assetassign
	Route::get('assetassign/{id}/lock',				'AssetAssignMasterController@lock');
	Route::resource('assetassign',					'AssetAssignMasterController');
	Route::post('asset_assign_list',				'AssetAssignMasterController@asset_assign_list');
	Route::post('asset_assign_list_inactive',		'AssetAssignMasterController@asset_assign_list_inactive');
	Route::get('asset_assign_list_get',				'AssetAssignMasterController@asset_assign_list_get');
	Route::get('asset_data',						'AssetAssignMasterController@asset_data');
	Route::get('asset_data_old',					'AssetAssignMasterController@asset_data_old');
	Route::get('asset_assign_list_select',			'AssetAssignMasterController@asset_assign_list_select');
	Route::post('asset_assign_list_post',			'AssetAssignMasterController@asset_assign_list_get');

	Route::get('assigned_list_employee',			'AssetAssignMasterController@assigned_list_employee');
	Route::get('assigned_list_department',			'AssetAssignMasterController@assigned_list_department');

	//asset_return_type
	Route::resource('assetreturntype',				'AssetReturnTypeController');
	Route::post('return_type_list',					'AssetReturnTypeController@return_type_list');
	Route::get('return_type_list_data',				'AssetReturnTypeController@return_type_list_data');

	// asset return
	Route::get('assetreturn/{id}/lock',				'AssetReturnMasterController@lock');
	Route::resource('assetreturn',					'AssetReturnMasterController');
	Route::post('asset_return_list',				'AssetReturnMasterController@asset_return_list');

	// skill
	Route::resource('skill',						'SkillController');
	Route::post('skill_list', 						'SkillController@skill_list');
	Route::get('skill_list_select', 				'SkillController@skill_list_select');

	Route::resource('skillemployeewise',			'SkillEmployeewiseController');
	Route::post('skill_employeewise_list',			'SkillEmployeewiseController@skill_employeewise_list');
	Route::get('skill_employeewise_list_get',		'SkillEmployeewiseController@skill_employeewise_list_get');
	Route::post('skill_employeewise_list_post',		'SkillEmployeewiseController@skill_employeewise_list_get');

	Route::post('compensation-store', 'CVJobDescriptionGroupSetupController@compensationStore')->name('compensationStore');
	Route::post('job-compensation-list-data', 'CVJobDescriptionGroupSetupController@compensationListData')->name('job-compensation-list-data');

	Route::post('job-decription-group-list', 'CVJobDescriptionGroupSetupController@jobDecriptionList')->name('job-decription-group-list-data');
	Route::resource('cv-job-descriptions', 'CVJobDescriptionGroupSetupController');

	Route::delete('job-description-delete/{id}', 'CVJobDescriptionController@softDelete');
	Route::post('job-decription-list', 'CVJobDescriptionController@loadList')->name('job-decription-list-data');
	Route::resource('job-descriptions', 'CVJobDescriptionController');

	Route::post('dept_wise_job_description', 'CVJobRequsitionController@deptWiseJobDescription');
	Route::get('cv-job-requsitions.publish-unpublish/{id}', 'CVJobRequsitionController@publishUnpublish')->name('cv-job-requsitions.publish-unpublish');
	Route::post('approved-reject', 'CVJobRequsitionController@approvedReject')->name('approved-reject');
	Route::post('cv-job-requsitions-datatable-list', 'CVJobRequsitionController@datatableList')->name('job-requsitions.datatableList');
	Route::resource('cv-job-requsitions', 'CVJobRequsitionController');

	Route::get('show-applicant-cv/{id}', 'CVDropMasterController@show')->name('applicant.cv');
	Route::get('cvdestroy/{id}', 'CVDropMasterController@destroy')->name('applicant.cvdestroy');
	Route::get('cvinterview/{id}', 'CVDropMasterController@cvinterview')->name('applicant.cvinterview');
	Route::get('cvreserve/{id}', 'CVDropMasterController@cvreserve')->name('applicant.cvreserve');
	Route::get('cv-drop-list', 'CVDropMasterController@cvDropList')->name('cv-drop-list');
    Route::post('cv-drop-datatable-list', 'CVDropMasterController@cvDatatableList')->name('cv-drop-list.datatableList');

    //Employee Task Management
    Route::post('get-employee_task_management-data', 'EmployeeTaskManagementController@getTableData');
    Route::get('employee_task_management/{id}/delete', 'EmployeeTaskManagementController@delete');
    Route::post('employee_task_management_assign_task/{id}', 'EmployeeTaskManagementController@assign')->name('employee_task_management.assign_task');
    Route::resource('employee_task_management', 'EmployeeTaskManagementController');

    Route::get('completed_task_list', 'EmployeeTaskManagementListController@completeTask');
    Route::post('get-completed_task_list-data', 'EmployeeTaskManagementListController@completeTaskTableData');

    Route::get('pending_task_list', 'EmployeeTaskManagementListController@pendingTask');
    Route::post('get-pending_task_list-data', 'EmployeeTaskManagementListController@pendingTaskTableData');

    Route::get('daily_complete_task_list', 'EmployeeTaskManagementListController@dailyCompleteTask');
    Route::get('daily_complete_task_print', 'EmployeeTaskManagementListController@dailyCompleteTaskPrint');
    Route::post('get-daily_complete_task_list-data', 'EmployeeTaskManagementListController@dailyCompleteTaskTableData');

    Route::get('employee_task_type', 'EmployeeTaskTypeController@index');
    Route::post('employee_task_type', 'EmployeeTaskTypeController@store');
    Route::get('employee_task_type/{id}/delete', 'EmployeeTaskTypeController@delete');
    Route::post('get-employee_task_type-data', 'EmployeeTaskTypeController@getLoadData');
    // End Task Management

    // Database Backup
    Route::get('database/backup', 'DatabaseBackupController@index')->name('database.backup');
	Route::get('database/backup/create', 'DatabaseBackupController@manualBackup')->name('database.backup.create');

	Route::get('database/download/{file}', 'DatabaseBackupController@download')->name('database.download');
	Route::get('database/delete/{file}', 'DatabaseBackupController@delete')->name('database.delete');
    // END Database Backup


    // Custom
    Route::get('custom_filter', 'Filters\CustomFilterController@index');
    Route::get('custom_filter/create', 'Filters\CustomFilterController@create');
    Route::post('custom_filter', 'Filters\CustomFilterController@store');
    Route::get('custom_filter/{id}/edit', 'Filters\CustomFilterController@edit');
    Route::get('custom_filter/{id}/delete', 'Filters\CustomFilterController@delete');
    Route::post('custom_filter/{id}/update', 'Filters\CustomFilterController@update')->name('custom_filter.update');
    Route::get('custom_filter/get-table-data', 'Filters\CustomFilterController@getTableData');

    Route::get('table_name_list', 'Filters\CustomFilterController@tableNameList');
    Route::get('filter_name_list', 'Filters\CustomFilterController@filterNameList');

    Route::get('custom_employee_list', 'Filters\FilteringController@employeeFilter');
    Route::post('custom_employee_list_data', 'Filters\FilteringController@getEmployeeFilter');
    Route::get('custom_employee_list/export', 'Filters\FilteringController@employeeExport')->name('custom_employee_list.export');

    Route::post('this_month_resign_employee', 'HomeController@this_month_resign_employee');
    Route::get('this_month_resign_employee_for_graph', 'HomeController@this_month_resign_employee_for_graph');
    Route::get('this_month_resign_join_employee_for_graph', 'HomeController@this_month_resign_join_employee_for_graph');
    Route::get('this_month_join_employee_for_graph', 'HomeController@this_month_join_employee_for_graph');
    Route::post('this_month_join_employee', 'HomeController@this_month_join_employee');
    Route::get('last_fifteen_ontime_graph', 'HomeController@last_fifteen_ontime_graph');
    Route::get('last_fifteen_absent_graph', 'HomeController@last_fifteen_absent_graph');
    Route::get('last_fifteen_leave_graph', 'HomeController@last_fifteen_leave_graph');
    Route::get('attendence_pie_chart', 'HomeController@attendence_pie_chart');

});

Route::get('career-opportunities', 'CVDropMasterController@index')->name('career-page');
Route::get('apply-for-job/{id}', 'CVDropMasterController@applyForm')->name('apply-for-job');
Route::post('submit-application', 'CVDropMasterController@submitApplication')->name('submit-application');
