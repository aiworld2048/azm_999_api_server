<?php

use App\Http\Controllers\Admin\AdsVedioController;
use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\BankController;
use App\Http\Controllers\Admin\BannerAdsController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BannerTextController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\DepositRequestController;
use App\Http\Controllers\Admin\PaymentTypeController;
use App\Http\Controllers\Admin\PlayerController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\TransferLogController;
use App\Http\Controllers\Admin\WithDrawRequestController;
use App\Http\Controllers\Admin\BuffaloGame\BuffaloReportController;
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

    Route::get('/changeplayersite/{user}', [HomeController::class, 'changePlayerSite'])->name('changeSiteName');

    Route::post('/updatePlayersite/{user}', [HomeController::class, 'updatePlayerSiteLink'])->name('updateSiteLink');

    Route::get('/player-list', [HomeController::class, 'playerList'])->name('playerList');


     // banner etc start

    Route::resource('video-upload', AdsVedioController::class)->middleware('permission:banner_view|banner_create|banner_update|banner_delete');

    Route::resource('banners', BannerController::class)->middleware('permission:banner_view|banner_create|banner_update|banner_delete');
    Route::resource('adsbanners', BannerAdsController::class)->middleware('permission:banner_view|banner_create|banner_update|banner_delete');
    Route::resource('text', BannerTextController::class)->middleware('permission:banner_text_view|banner_text_create|banner_text_update|banner_text_delete');
    Route::resource('/promotions', PromotionController::class)->middleware('permission:promotion_view|promotion_create|promotion_update|promotion_delete');
    Route::resource('contact', ContactController::class);
    Route::resource('paymentTypes', PaymentTypeController::class);
    Route::resource('bank', BankController::class)->middleware('permission:bank_view|bank_create|bank_update|bank_delete');
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

    
    
    // Player list start
    Route::middleware(['permission:player_view'])->group(function () {
        Route::get('players', [PlayerController::class, 'index'])->name('player.index');
        Route::get('players/{player}', [PlayerController::class, 'show'])->name('player.show');
    });

    Route::middleware(['permission:player_update'])->group(function () {
        Route::get('players/{player}/edit', [PlayerController::class, 'edit'])->name('player.edit');
        Route::put('players/{player}', [PlayerController::class, 'update'])->name('player.update');
    });

    // Player creation routes
    Route::get('agent/players/create', [PlayerController::class, 'create'])
        ->middleware('permission:player_create')
        ->name('agent.player.create');
    Route::post('agent/players', [PlayerController::class, 'store'])
        ->middleware('permission:player_create')
        ->name('agent.player.store');
    

    // Withdraw routes (for process_withdraw permission)
    Route::middleware(['permission:agent_wallet_withdraw'])->group(function () {
        Route::get('finicialwithdraw', [WithDrawRequestController::class, 'index'])->name('agent.withdraw');
        Route::post('finicialwithdraw/{withdraw}', [WithDrawRequestController::class, 'statusChangeIndex'])->name('agent.withdrawStatusUpdate');
        Route::post('finicialwithdraw/reject/{withdraw}', [WithDrawRequestController::class, 'statusChangeReject'])->name('agent.withdrawStatusreject');
        Route::get('finicialwithdraw/{withdraw}', [WithDrawRequestController::class, 'WithdrawShowLog'])->name('agent.withdrawLog');
    });

    // Deposit routes (for both parent agents and sub-agents)
    Route::middleware(['permission:agent_wallet_deposit'])->group(function () {
        Route::get('finicialdeposit', [DepositRequestController::class, 'index'])->name('agent.deposit');
        Route::get('finicialdeposit/{deposit}', [DepositRequestController::class, 'view'])->name('agent.depositView');
        Route::post('finicialdeposit/{deposit}', [DepositRequestController::class, 'statusChangeIndex'])->name('agent.depositStatusUpdate');
        Route::post('finicialdeposit/reject/{deposit}', [DepositRequestController::class, 'statusChangeReject'])->name('agent.depositStatusreject');
        Route::get('finicialdeposit/{deposit}/log', [DepositRequestController::class, 'DepositShowLog'])->name('agent.depositLog');
    });

    // Cash-in/cash-out routes
    Route::middleware(['permission:player_update'])->group(function () {
        Route::get('player-cash-in/{player}', [PlayerController::class, 'getCashIn'])->name('player.getCashIn');
        Route::post('player-cash-in/{player}', [PlayerController::class, 'makeCashIn'])->name('player.makeCashIn');
        Route::get('player/cash-out/{player}', [PlayerController::class, 'getCashOut'])->name('player.getCashOut');
        Route::post('player-cash-out/update/{player}', [PlayerController::class, 'makeCashOut'])->name('player.makeCashOut');
    });

    // Player ban route
    Route::middleware(['permission:player_ban'])->group(function () {
        Route::put('player/{id}/ban', [PlayerController::class, 'banUser'])->name('player.ban');
    });

    // Player change password routes
    Route::middleware(['permission:player_password_change'])->group(function () {
        Route::get('player-changepassword/{id}', [PlayerController::class, 'getChangePassword'])->name('player.getChangePassword');
        Route::post('player-changepassword/{id}', [PlayerController::class, 'makeChangePassword'])->name('player.makeChangePassword');
    });

    Route::get('/players-list', [PlayerController::class, 'player_with_agent'])
        ->middleware('permission:player_view')
        ->name('playerListForAdmin');
    
    // Player report route
    Route::get('player/{player}/report', [PlayerController::class, 'playerReportIndex'])
        ->middleware('permission:player_view')
        ->name('player.report_detail');
    
    // agent create player end
    // report log

    //  agent end
    Route::get('/transfer-logs', [TransferLogController::class, 'index'])
        ->middleware('permission:agent_wallet_deposit|agent_wallet_withdraw')
        ->name('transfer-logs.index');

   
    Route::get('playertransferlog/{id}', [TransferLogController::class, 'PlayertransferLog'])
        ->middleware('permission:player_view')
        ->name('PlayertransferLogDetail');

    

    // Buffalo Game reports
    Route::group(['prefix' => 'buffalo-game'], function () {
        Route::get('/report', [BuffaloReportController::class, 'index'])->name('buffalo-report.index');
        Route::get('/report/{id}', [BuffaloReportController::class, 'show'])->name('buffalo-report.show');
    });

    
});
