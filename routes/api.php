<?php

use Illuminate\Support\Facades\Route;

Route::post('app_version', 'API\UserController@appVersion');
Route::post('login', 'API\UserController@login');

Route::middleware('auth:api')->group(function () {
    Route::post('/common_list_data', 'API\DropDownController@common_list_data');
    Route::post('/common_store',     'API\DropDownController@common_store');
    Route::post('appShiftStore',     'API\DropDownController@appShiftStore');




    Route::post('logout', 'API\UserController@logout');
    Route::post('employee_attendance_status', 'API\Attendance@attendance_status');
    Route::post('employee_attendance', 'API\Attendance@store');
    Route::post('employee_attendance_details', 'API\Attendance@attendance_details');

    // handle all leave management requests are handled by this route
    Route::get('leave_type_drop_data', 'API\LeaveController@leaveTypeDropData');
    Route::get('employee_apply_to', 'API\LeaveController@applyTo');
    Route::post('employee_leave_list', 'API\LeaveController@leaveApplicationList');
    Route::get('employee_leave_delete/{id}', 'API\LeaveController@delete');
    Route::apiResource('employee_leave', 'API\LeaveController');
    Route::get('pendingLeaveList', 'API\LeaveController@pendingLeaveList');
    Route::post('leaveApproveReject', 'API\LeaveController@leaveApproveReject');




    // Start Employee info controller
    Route::post('employee_info_update', 'API\EmployeeInfoController@update'); // complete_employee_tasks
    Route::post('employee_info', 'API\EmployeeInfoController@show'); // complete_employee_tasks
    Route::post('employee_info_edit', 'API\EmployeeInfoController@edit'); // complete_employee_tasks
    Route::post('security_settings', 'API\EmployeeInfoController@securitySettings'); // password and image
    Route::post('newEmployeeCreate', 'API\EmployeeInfoController@newEmployeeCreate'); // password and image
    Route::get('bossWiseEmployeeList', 'API\EmployeeInfoController@bossWiseEmployeeList');

    // End Employee info controller

    // Start Employee task controller
    Route::post('complete_task_store', 'API\EmployeeTaskController@complete_task_store');
    Route::post('complete_employee_tasks', 'API\EmployeeTaskController@complete_employee_tasks');
    Route::get('pendingEmployeeTask/{id}', 'API\EmployeeTaskController@pendingEmployeeTask');
    Route::apiResource('employee_tasks', 'API\EmployeeTaskController');



    // Route::apiResource('employee_tasks', 'API\EmployeeTaskController');
    // End Employee task controller

    Route::post('document_archive_create', 'API\EmployeeFileController@store');
    Route::post('document_archive_list', 'API\EmployeeFileController@list');
    Route::post('document_archive_edit', 'API\EmployeeFileController@edit');
    Route::post('document_archive_update', 'API\EmployeeFileController@update');
    Route::post('document_archive_delete', 'API\EmployeeFileController@delete');

    //Dashboard Relevant API
    Route::post('attendance_summary', 'API\Dashboard@attendance_summary');

});
