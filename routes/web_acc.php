<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountsIntegration\AccountHeadTitleController;
use App\Http\Controllers\AccountsIntegration\AccountJournalTypeController;
use App\Http\Controllers\AccountsIntegration\AccountHeadIntegrationController;
use App\Http\Controllers\AccountsIntegration\AccountHeadEmployeeSetupController;
use App\Http\Controllers\AccountsIntegration\AccountHeadEmployeeExchangeController;
use App\Http\Controllers\AccountsIntegration\AccProcessedDataController;

Route::middleware('auth')->group(function() {
    Route::get('account_head_title_dropdown', [AccountHeadTitleController::class, 'dropdown'])->name('account_head_title.dropdown');
    Route::get('account_head_title_dropdown_parent', [AccountHeadTitleController::class, 'dropdownParent'])->name('account_head_title.dropdown.parent');
    Route::get('account_journal_type_dropdown', [AccountJournalTypeController::class, 'dropdown'])->name('account_journal_type.dropdown');
    Route::get('getAccountsLedgerHeadId', [AccountHeadIntegrationController::class, 'getAccountsLedgerHeadId'])->name('getAccountsLedgerHeadId');
    Route::get('accCostCenter', [AccountHeadIntegrationController::class, 'accCostCenter'])->name('accCostCenter');


    // Account Head Title
    Route::resource('account_head_title', AccountHeadTitleController::class);

    // Account Journal Type
    Route::resource('account_journal_type', AccountJournalTypeController::class);

    // Account Head Integration
    Route::resource('account_head_integration', AccountHeadIntegrationController::class);

    // Account Head Wise Employee Setup
    Route::resource('account_head_wise_employee_setup', AccountHeadEmployeeSetupController::class);

    // Account Head Employee Exchange
    Route::get('account_head_employee_change_head_list', [AccountHeadEmployeeExchangeController::class, 'index'])->name('change_head.index');
    Route::get('account_head_employee_exchanged_list', [AccountHeadEmployeeExchangeController::class, 'exchangedList'])->name('change_head.exchanged');
    Route::post('account_head_employee_change_head', [AccountHeadEmployeeExchangeController::class, 'employeeChangeHead'])->name('employee_change_head');
    Route::get('exchanged_emp/{id}/delete', [AccountHeadEmployeeExchangeController::class, 'exchangedEmpDelete']);

    Route::resource('acc_processed_data', AccProcessedDataController::class);



});

