<?php



use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mobile\API\AppConfigAPIController;
use App\Http\Controllers\Mobile\API\HOSMobileAPIController;
use App\Http\Controllers\Mobile\API\UserMobileAPIController;
use App\Http\Controllers\Mobile\API\LoginMobileApiController;
use App\Http\Controllers\Mobile\API\GroupMobileApiController;
use App\Http\Controllers\Mobile\API\SafetyMobileAPIController;
use App\Http\Controllers\Mobile\API\SettingMobileAPIController;
use App\Http\Controllers\Mobile\API\ApprovalMobileAPIController;
use App\Http\Controllers\Mobile\API\BluetoothAPIController;
use App\Http\Controllers\Mobile\API\DashboardMobieAPIController;
use App\Http\Controllers\Mobile\API\DocumentMobileAPIController;
use App\Http\Controllers\Mobile\API\ActivityLogMobileAPIController;
use App\Http\Controllers\Mobile\API\GroupAssignMobileApiController;
use App\Http\Controllers\Mobile\API\NotificationMobileAPIController;
use App\Http\Controllers\Mobile\API\DOTInspectionMobileAPIController;
use App\Http\Controllers\Mobile\API\HOSUnsignedLogMobileAPIController;
use App\Http\Controllers\Mobile\API\InspectionReportMobileAPIController;
use App\Http\Controllers\Mobile\API\UserDeviceAPIController;


// Public routes
Route::post('user/mobile/login', [LoginMobileAPIController::class, 'mobile_login']);

Route::post('forgot/mobile/password/{email}', [UserMobileAPIController::class, 'index']);

Route::post('reset/mobile/password/{email}', [UserMobileAPIController::class, 'store']);

// Protected routes (requires mobileAPI guard)
Route::middleware(['APILogCheck', 'auth:mobileAPI', 'DrCheckMobile', 'mobileAPI'])->group(function () {

    Route::get('config/data', [AppConfigAPIController::class, 'app_config_data']);

    Route::post("/user/device/notify", [UserDeviceAPIController::class, "store"]);

    Route::get('change/mobile/duty/status/{id}/{lat}/{long}/{text}', [HOSMobileAPIController::class, 'change_mobile_duty_status']);

    Route::get('dashboard/mobile/data', [DashboardMobieAPIController::class, 'dashboard_data_index'])->name('dashboard.mobile.data');

    Route::get('activity/mobile/log/data/{count?}/{page?}', [ActivityLogMobileAPIController::class, 'index']);

    Route::post('hos/form/edit/activity', [ActivityLogMobileAPIController::class, 'store']);

    Route::get('hos/mobile/data/{start}/{end}', [HOSMobileAPIController::class, 'hos_mobile_data']);

    Route::get('hos/mobile/data/test/{start}/{end}', [HOSMobileAPIController::class, 'hos_mobile_test_data'])->name('hos.data.mobile.test');

    Route::get('hos/mobile/graph/data/{date}', [HOSMobileAPIController::class, 'graph_hos_chart_data']);

    Route::resource('document/mobile/data', DocumentMobileAPIController::class);

    Route::resource('driver/mobile/inspection', InspectionReportMobileAPIController::class);

    Route::post('document/mobile/data/update/{id}', [DocumentMobileAPIController::class, 'docs_update']);

    Route::get('safety/dashboard/page', [SafetyMobileAPIController::class, 'safety_dashboard_page']);

    Route::get('safety/data/{event}/{start?}/{end?}', [SafetyMobileAPIController::class, 'safety_event_data']);

    Route::get('DOT/mobile/inspection/data', [DOTInspectionMobileAPIController::class, 'index']);

    Route::get('send/mail/{email}', [DOTInspectionMobileAPIController::class, 'send_mail']);

    Route::get('setting/account/mobile/data', [SettingMobileAPIController::class, 'account_data']);

    Route::post('setting/mobile/account/edit', [SettingMobileAPIController::class, 'account_data_edit']);

    Route::resource('hos/log/unsigned', HOSUnsignedLogMobileAPIController::class);

    Route::post('hos/log/unsigned/certify/{date}', [HOSUnsignedLogMobileAPIController::class, 'hos_edit_certify']);

    Route::get('approval/mobile/request', [ApprovalMobileAPIController::class, 'index']);

    Route::post('approval/mobile/request/{type}/{accept}', [ApprovalMobileAPIController::class, 'approval_func']);

    Route::post('insert/bluetooth-data', [BluetoothAPIController::class, "create"]);

    Route::post('setting/mobile/change/password', [SettingMobileAPIController::class, 'setting_change_password']);

    Route::get('setting/mobile/carrier/data', [SettingMobileAPIController::class, 'setting_carrier_data']);

    Route::post('setting/mobile/carrier/data/update', [SettingMobileAPIController::class, 'setting_carrier_update']);

    Route::get('setting/mobile/cycle/rule/data', [SettingMobileAPIController::class, 'setting_cycle_rule_data']);

    Route::post('setting/mobile/cycle/rule/data/update', [SettingMobileAPIController::class, 'setting_cycle_rule_update']);

    Route::resource('user/data/notification', NotificationMobileAPIController::class);

    Route::resource('group/mobile/data', GroupMobileApiController::class)
        ->names([
            'index' => 'group.mobile.data.index',
            'create' => 'group.mobile.data.create',
            'store' => 'group.mobile.data.store',
            'show' => 'group.mobile.data.show',
            'edit' => 'group.mobile.data.edit',
            'update' => 'group.mobile.data.update',
            'destroy' => 'group.mobile.data.destroy',
        ]);

    Route::resource('group/assign/mobile/data', GroupAssignMobileApiController::class)
        ->names([
            'index' => 'group.assign.mobile.data.index',
            'create' => 'group.assign.mobile.data.create',
            'store' => 'group.assign.mobile.data.store',
            'show' => 'group.assign.mobile.data.show',
            'edit' => 'group.assign.mobile.data.edit',
            'update' => 'group.assign.mobile.data.update',
            'destroy' => 'group.assign.mobile.data.destroy',
        ]);

    Route::post('group/message/mobile/update/{id}', [GroupMobileApiController::class, 'post_update']);

    Route::post('group/assign/mobile/data/update/{id}', [GroupAssignMobileApiController::class, 'post_update']);

    Route::get('user/mobile/logout/{id}', [LoginMobileApiController::class, 'logout']);

});

