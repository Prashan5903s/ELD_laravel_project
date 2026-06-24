<?php



use App\Models\User;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\UserApiController;

use App\Http\Controllers\API\Transport\DocumentAPIController;

use App\Http\Controllers\API\Transport\FleetUserAPIController;

use App\Http\Controllers\API\Driver\HOS\HOSDetailAPIController;

use App\Http\Controllers\API\Driver\Document\DocumentDriverAPIController;

use App\Http\Controllers\API\Transport\ComplianceAPIController;

use App\Http\Controllers\API\Transport\Safety\SafetyAPIController;

use App\Http\Controllers\API\Driver\Inspection\InspectionReportDriverAPIController;

use App\Http\Controllers\API\Driver\SafetyDriverAPIController;

use App\Http\Controllers\API\Transport\Driver\DriverAPIController;

use App\Http\Controllers\API\Transport\Settings\Organization\UnidentifiedDrivingAPIController;

use App\Http\Controllers\API\Transport\Report\ReportAPIController;

use App\Http\Controllers\API\Transport\Assets\VehicleAPIController;

use App\Http\Controllers\API\Transport\CoDriverAPIController;

use App\Http\Controllers\API\Transport\Mileage\MileageAPIController;

use App\Http\Controllers\API\Transport\Assets\LocationAPIController;

use App\Http\Controllers\Mobile\API\DOTInspectionMobileAPIController;

use App\Http\Controllers\API\Transport\Assets\EnviornmentAPIController;

use App\Http\Controllers\API\Transport\Assets\CoverageMapAPIController;

use App\Http\Controllers\API\Transport\Report\IdlingReportAPIController;

use App\Http\Controllers\API\Transport\Report\SafetyReportAPIController;

use App\Http\Controllers\API\Transport\Settings\device\DeviceAPIController;

use App\Http\Controllers\API\Transport\Report\FuelPerformanceAPIController;

use App\Http\Controllers\API\Transport\ReAssignCoDriverAPIController;

use App\Http\Controllers\API\Transport\Alert\AlertAPIController;

use App\Http\Controllers\API\Driver\Settings\AccountAPIController;

use App\Http\Controllers\API\Transport\Report\InspectionReportAPIController;

use App\Http\Controllers\API\Transport\Report\HoursWorkedReportAPIController;

use App\Http\Controllers\API\Driver\DriverActivity\DriverActivityInfoAPIController;

use App\Http\Controllers\API\Transport\Settings\Organization\UserRoleAPIController;

use App\Http\Controllers\API\Driver\DOTInspection\DOTInspectionAPIController;

use App\Http\Controllers\API\Transport\Settings\Organization\VehicleAssignAPIController;

use App\Http\Controllers\API\Transport\Settings\Organization\DriverActivityAPIController;

use App\Http\Controllers\API\Driver\Settings\ChangePasswordAPIController;

use App\Http\Controllers\API\Driver\Settings\GeneralAPIController;

use App\Http\Controllers\API\Driver\Settings\CarrerAPIController;

use App\Http\Controllers\API\Driver\Settings\CycleRuleAPIController;



/*

|--------------------------------------------------------------------------

| API Routes

|--------------------------------------------------------------------------

|

| Here is where you can register API routes for your application. These

| routes are loaded by the RouteServiceProvider within a group which

| is assigned the "api" middleware group. Enjoy building your API!

|

 */



Route::get('check/token/email/{token}', [UserApiController::class, 'check_email_token']);



Route::get('dot/inspection/data/{token}', [DOTInspectionMobileAPIController::class, 'dot_data']);



Route::get('forgot/password/mail/{email}', [UserApiController::class, 'forgot_password']);



Route::get('check/forgot/password/token/{token}', [UserApiController::class, 'check_token_forgot_password']);



Route::post('reset/change/password/{token}', [UserApiController::class, 'check_reset_password']);



Route::middleware(['api-acess', 'throttle:300,1'])->group(function () {



    Route::post('user/post/login', [UserApiController::class, 'login']);



    Route::middleware(['APILogCheck', 'auth:api'])->group(function () {



        Route::get('/user', function (Request $request) {

            return $request->user();
        });



        Route::get('states/{id}', [UserApiController::class, 'getStates']);



        Route::get('cities/{id}', [UserApiController::class, 'getCities']);



        Route::post('user/logout/{log}', [UserApiController::class, 'logout']);
    });



    Route::middleware(['APILogCheck', 'auth:api', 'ECAPI', 'throttle:300,1'])->group(function () {



        Route::get('user/company/add', [UserApiController::class, 'addShow']);



        Route::post('user/add/post', [UserApiController::class, 'postAdd']);



        Route::get('user/edit/{id}', [UserApiController::class, 'editShow']);



        Route::get('company/user/index', [UserApiController::class, 'companyIndex']);



        Route::get('user/{id}', [UserApiController::class, 'getUser']);
    });



    Route::middleware(['APILogCheck', 'auth:api', 'TRAPI', 'throttle:300,1'])->group(function () {



        Route::resource('driver', DriverAPIController::class);

        Route::get('driver/detail/{id}', [DriverAPIController::class, 'driver_detail']);

        Route::get('driver/hos/detail/{id}', [DriverAPIController::class, 'driver_hos_detail']);

        Route::get('driver/date/log/{id}/{start_date}/{end_date}', [DriverAPIController::class, 'driver_date_hos_data']);

        Route::get('driver/location/data/{id}', [DriverAPIController::class, 'driver_location_data']);

        Route::get('step2', [DriverAPIController::class, 'step2']);

        Route::get('step3', [DriverAPIController::class, 'step3']);



        Route::get('driver/edit/check/{id}', [DriverAPIController::class, 'check_driver_api']);



        Route::get('transport/latest/change/{userId}/{id}', [DriverActivityAPIController::class, 'transport_latest_log_change']);



        Route::get('idling/report/data/{start?}/{end?}/{driver?}/{vehicle?}', [IdlingReportAPIController::class, 'index']);



        Route::get('graph/chart/data/{id}/{date}', [DriverAPIController::class, 'graph_chart_date_data']);



        Route::prefix('setting/organization')->group(function () {

            Route::resource('user-roles', UserRoleAPIController::class);
        });



        Route::get('hours/worked/data/{start?}/{end?}/{driver?}', [HoursWorkedReportAPIController::class, 'index']);



        Route::get('data/report/inspection/{start?}/{end?}/{driver?}/{vehicle?}', [InspectionReportAPIController::class, 'index']);



        Route::get('fuel/performance/data/{start?}/{end?}/{driver?}/{vehicle?}', [FuelPerformanceAPIController::class, 'index']);



        Route::get('mileage/filter', [MileageAPIController::class, 'mileage_filter']);



        Route::get('mileage/data/{start?}/{end?}/{vehicle?}/{jurisdiction?}/{fuelType?}', [MileageAPIController::class, 'index']);



        Route::get('safety/report/data/{start?}/{end?}', [SafetyAPIController::class, 'index']);



        Route::get('safety/score/factor/{start?}/{end?}', [SafetyAPIController::class, 'create']);



        Route::get('safety/score/trend', [SafetyAPIController::class, 'safety_score_trend']);



        Route::resource('reassign/api/{driverId}/{date}/codriver', ReAssignCoDriverAPIController::class);



        Route::get('safety/driver/score/{start?}/{end?}', [SafetyAPIController::class, 'safety_score_per_driver']);



        Route::get('event/data/set', [SafetyAPIController::class, 'event_data_set']);



        Route::resource('alert/user/info', AlertAPIController::class);



        Route::resource('transport/unidentified/driving/data', UnidentifiedDrivingAPIController::class)->names('unidentified.driving.data');



        Route::get('safety/driver/report/{start?}/{end?}/{driver?}/{event?}', [SafetyReportAPIController::class, 'index']);



        Route::get('event/data/filter/{start?}/{end?}/{driver?}/{vehicle?}/{behaviour?}', [SafetyAPIController::class, 'event_data_filter']);



        Route::get('report/data/{start?}/{end?}/{id?}', [ReportAPIController::class, 'index']);



        Route::get('event/detail/{id}', [SafetyAPIController::class, 'event_detail_data']);



        Route::get('speed/detail/{id}', [SafetyAPIController::class, 'safety_detail_data']);



        Route::resource('driver/work/activity', DriverActivityAPIController::class);

        Route::get('driver/work/activity/{logType}/{type}/{page}/{itemNo}', [DriverActivityAPIController::class, 'log_data']);

        Route::resource('setting/driver/devices', DeviceAPIController::class);



        Route::get('check/email/{email}/{id}', [DriverAPIController::class, 'check_unique_email']);



        Route::get('check/username/{username}/{id}', [DriverAPIController::class, 'check_unique_username']);



        Route::get('check/serial/{serial}/{id}', [DeviceAPIController::class, 'check_unique_serial']);



        Route::get('check/vin/{vin}/{id}', [VehicleAPIController::class, 'check_unique_vin']);



        Route::get('generate/username', [DriverAPIController::class, 'generate_username']);



        Route::get('/check/roles/{id}', [UserRoleAPIController::class, 'check_role']);



        Route::resource('transport/vehicle', VehicleAPIController::class);



        Route::resource('settings/vehicle/assign', VehicleAssignAPIController::class);



        Route::resource('asset/location', LocationAPIController::class);



        Route::post('/dashboard/document/post/{id}', [DocumentAPIController::class, 'document_post']);



        Route::resource('dashboard/documents', DocumentAPIController::class);



        Route::resource('fleet-user', FleetUserAPIController::class);



        Route::get('graph/chart/data/{id}', [DriverAPIController::class, 'graph_chart_data']);



        Route::resource('assets/enviornment', EnviornmentAPIController::class);



        Route::get('assets/overview/coverage-map/{dateStart}/{dateEnd}', [CoverageMapAPIController::class, 'index']);



        Route::get('compliance/{type}/{start}/{end}', [ComplianceAPIController::class, 'index']);



        Route::get('user/unnotify/vehicle/{id}', [VehicleAPIController::class, 'vehicle_notify_id']);



        Route::get('user/unnotify/vehicle', [VehicleAPIController::class, 'vehicle_unnotify']);



        Route::get('vehicle/assign/data/{id}', [VehicleAPIController::class, 'assign_vehicle']);



        Route::get('device/vehicle/assign/{vid}/{id}', [DeviceAPIController::class, 'unique_vehicle_assign']);



        Route::get('vehicle/assign/driver/unique/{did}/{vid}/{id}', [VehicleAssignAPIController::class, 'vehicle_driver_unique_assign']);



        Route::get('check/time/{id}/{start}/{endTime}', [DriverActivityAPIController::class, 'check_time_driver_shift']);



        Route::get('check/edit/driver/activity/{id}', [DriverActivityAPIController::class, 'Check_edit_driver_activity']);
    });



    Route::middleware(['APILogCheck', 'auth:api'])->group(function () {



        Route::get('transport/permission', [DriverAPIController::class, 'trans_perms']);



        Route::get('profile/user/data', [UserApiController::class, 'profile_user_data']);



        Route::get('user/notify/vehicle', [VehicleAPIController::class, 'vehicle_notify']);



        Route::get('codriver/list/{id}', [CoDriverAPIController::class, 'driver_list']);



        // Use specific routes for different operations instead of Route::resource

        Route::get('data/{date}/{id}/codriver', [CoDriverAPIController::class, 'index']);

        Route::post('data/{date}/{id}/codriver', [CoDriverAPIController::class, 'store']);

        Route::get('data/{date}/{id}/codriver/show', [CoDriverAPIController::class, 'show']);

        Route::put('data/{date}/{id}/codriver', [CoDriverAPIController::class, 'update']);

        // Route::delete('data/{date}/{id}/codriver', [CoDriverAPIController::class, 'destroy']);



    });



    Route::middleware(['APILogCheck', 'DrCheck', 'auth:api', 'throttle:300,1'])->group(function () {

        Route::get('driver/info/driver-activity/data/{pageNo}/{itemNo}', [DriverActivityInfoAPIController::class, 'paginate']);

        Route::resource('driver/info/driver-activity', DriverActivityInfoAPIController::class);

        Route::get('driver/hos/details/{startTime}/{endTime}', [HOSDetailAPIController::class, 'driver_date_hos_data']);

        Route::get('driver/detail/hos/page', [HOSDetailAPIController::class, 'hos_detail_page']);

        Route::get('driver/change/duty/status/{id}', [DriverActivityInfoAPIController::class, 'change_driver_duty_status']);

        Route::get("chart/graph/data/{date}", [HOSDetailAPIController::class, 'graph_data']);

        Route::get('check/driver/time/{id}/{start}/{endTime}', [DriverActivityInfoAPIController::class, 'check_time_driver_shift']);

        Route::resource('document/data/driver', DocumentDriverAPIController::class)->names('document-driver');

        Route::post('document/data/driver/post/{id}', [DocumentDriverAPIController::class, 'docs_update']);

        Route::get('driver/data/safety', [SafetyDriverAPIController::class, 'index']);

        Route::get('driver/safety/event/detail/{event}', [SafetyDriverAPIController::class, 'safety_event_data']);

        Route::get('driver/safety/event/specific/{id}', [SafetyDriverAPIController::class, 'event_safety_details']);

        Route::resource('dvir/driver/data', InspectionReportDriverAPIController::class)->names('dvir-data');

        Route::post('data/driver/imspection/{id}', [InspectionReportDriverAPIController::class, 'update_data']);



        Route::get('driver/mail/dot/inspection/{email}', [DOTInspectionAPIController::class, 'send_mail'])->name('driver-mail-dot-inspection');



        Route::get('driver/mail/dot/inspections/document', [DOTInspectionAPIController::class, 'index'])->name('driver-mail-dot-inspection-document');



        Route::resource('driver/setting/account/data', AccountAPIController::class)->names('driver.setting.account.data');



        Route::resource('driver/change/password/data/update', ChangePasswordAPIController::class)->names('driver.setting.change.password.update');



        Route::resource('driver/general/data/index', GeneralAPIController::class)->names('driver.general.data.index');



        Route::resource('driver/carrer/data', CarrerAPIController::class)->names('driver.carrer.data');



        Route::resource('driver/cycle/rule/data', CycleRuleAPIController::class)->names('driver.cycle.rule.data');
    });
});
