<?php

use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\TransferLogController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'admin',
    'as' => 'admin.',
    'middleware' => ['auth', 'checkBanned'],
], function () {

    Route::post('balance-up', [HomeController::class, 'balanceUp'])->name('balanceUp');

    Route::get('logs/{id}', [HomeController::class, 'logs'])->name('logs');

    // to do
    Route::get('/changePassword/{user}', [HomeController::class, 'changePassword'])->name('changePassword');
    Route::post('/updatePassword/{user}', [HomeController::class, 'updatePassword'])->name('updatePassword');

    
    
    
    // agent start
    Route::controller(AgentController::class)
        ->prefix('agent')
        ->name('agent.')
        ->group(function () {
            Route::get('/', 'index')->middleware('permission:agent_index')->name('index');
            Route::get('/create', 'create')->middleware('permission:agent_create')->name('create');
            Route::post('/', 'store')->middleware('permission:agent_create')->name('store');
            Route::get('/{agent}/edit', 'edit')->middleware('permission:agent_edit')->name('edit');
            Route::put('/{agent}', 'update')->middleware('permission:agent_edit')->name('update');

            Route::put('/{agent}/ban', 'banAgent')->middleware('permission:agent_edit')->name('ban');

            Route::get('/{agent}/change-password', 'getChangePassword')
                ->middleware('permission:agent_change_password_access')
                ->name('getChangePassword');
            Route::post('/{agent}/change-password', 'makeChangePassword')
                ->middleware('permission:agent_change_password_access')
                ->name('makeChangePassword');

            Route::get('/{agent}/cash-in', 'getCashIn')
                ->middleware('permission:make_transfer')
                ->name('getCashIn');
            Route::post('/{agent}/cash-in', 'makeCashIn')
                ->middleware('permission:make_transfer')
                ->name('makeCashIn');
            Route::get('/{agent}/cash-out', 'getCashOut')
                ->middleware('permission:make_transfer')
                ->name('getCashOut');
            Route::post('/{agent}/cash-out', 'makeCashOut')
                ->middleware('permission:make_transfer')
                ->name('makeCashOut');

            Route::get('/{agent}/report', 'agentReportIndex')
                ->middleware('permission:transfer_log')
                ->name('report');
            Route::get('/{agent}/player-report', 'getPlayerReports')
                ->middleware('permission:transfer_log')
                ->name('getPlayerReports');
            Route::get('/{agent}/profile', 'agentProfile')
                ->middleware('permission:agent_access')
                ->name('profile');
        });
    // agent end

    
    
    // master, agent sub-agent end
    Route::get('/transfer-logs', [TransferLogController::class, 'index'])->name('transfer-logs.index');

    
});
