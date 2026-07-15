<?php



use App\Http\Controllers\HomeController;

use Carbon\Carbon;

use App\Http\Controllers\SuperAdmin\SoftwareVersion\VersionController;

use App\Http\Controllers\SuperAdmin\AdminProfileController;

use App\Http\Controllers\Reseller\RSController;

use App\Http\Controllers\SuperAdmin\Role\RoleController;

use App\Http\Controllers\Company\CompController;

use App\Http\Controllers\Transport\Driver\DriverController;

use App\Http\Controllers\TranslateController;

use App\Http\Controllers\Reseller\User\UserController;

use App\Http\Controllers\SuperAdmin\user\UserViewController;

use App\Http\Controllers\Reseller\LeadBy\LeadByController;

use App\Http\Controllers\SuperAdmin\WhiteLabel\WhiteLabelController;

use App\Http\Controllers\SuperAdmin\language\LanguageController;

use App\Http\Controllers\Transport\Settings\Organization\DriverActivityController;

use App\Http\Controllers\WhiteLabel\Company\CompanyController;

use App\Http\Controllers\WhiteLabel\Reseller\ResellerController;

use App\Http\Controllers\SuperAdmin\Hardware\Device\DeviceAssignController;

use App\Http\Controllers\SuperAdmin\Hardware\Device\DeviceAdminController;

use App\Http\Controllers\Transport\Map\MapController;

use App\Http\Controllers\Company\Transport\TransportController;

use App\Http\Controllers\driver\ActivityDriverController;

use App\Http\Controllers\Transport\ComplianceController;

use App\Http\Controllers\driver\DriverDashboardController;

use App\Http\Controllers\Transport\Device\DeviceController;

use App\Http\Controllers\SuperAdmin\Currency\CurrenciesController;

use App\Http\Controllers\Transport\Settings\Organization\UserRoleController;

use App\Http\Controllers\SuperAdmin\module\ModulesController;

use App\Http\Controllers\Transport\SSEController;

use App\Http\Controllers\driver\DriverDocumentController;

use App\Http\Controllers\Transport\Assets\VehiclesController;

use App\Http\Controllers\SuperAdmin\package\PackageAssignController;

use App\Http\Controllers\Transport\Assets\AddressesController;

use App\Http\Controllers\Transport\DashboardController;

use App\Http\Controllers\SuperAdmin\package\PackagesController;

use App\Http\Controllers\SuperAdmin\permission\PermissionsController;

use App\Http\Controllers\SuperAdmin\Hardware\HardwareController;

use App\Http\Controllers\SuperAdmin\Role\RolesController;

use App\Http\Controllers\Transport\Document\DocumentController;

use App\Http\Controllers\Transport\Settings\Organization\GeneralController;

use App\Http\Controllers\WhiteLabel\WhiteProfileController;

use Illuminate\Support\Facades\Route;



/*

|--------------------------------------------------------------------------

| Web Routes

|--------------------------------------------------------------------------

|

| Here is where you can register web routes for your application. These

| routes are loaded by the RouteServiceProvider and all of them will

| be assigned to the "web" middleware group. Make something great!

|

 */



Route::get('check/eld-output-file/{type}/{token}', [AdminProfileController::class, 'eld_output_file']);



Route::get('/greet/{name}', function ($name) {

    echo json_encode(check_eld_rules(79, 1, '2024-07-22', '2024-07-22'));

    // echo json_encode(formatTime('2024-07-03 10:07:30'));

    // echo json_encode(conTimezone('America/Bahia', Carbon::now()));

    exit();

});

Route::get('white-label/dashboard', function () {

    return view('white-label.dashboard');

})->middleware(['auth', 'verified', 'WC'])->name('white-label.dashboard');



Route::get('homepage', [HomeController::class, 'index']);



Route::middleware(['auth', 'SA'])->group(function () {



    Route::post('wc/check-email', [DashboardController::class, 'checkMail']);



    Route::get('admin/dashboard', [AdminProfileController::class, 'dashboard'])->name('admin.dashboard');



    Route::resource('hardware/device', DeviceAdminController::class)->names('device.admin.data');



    Route::resource('device/assign', DeviceAssignController::class)->names('device.assign');



    Route::resource('user-management/roles', RolesController::class);

    Route::resource('user-management/package/assign', PackageAssignController::class)->names('package.assign');

    Route::resource('user-management/permissions/permissions', PermissionsController::class);

    Route::resource('user-management/permissions/modules', ModulesController::class);



    Route::get('user-list/{ut}', [AdminProfileController::class, 'view_total'])->name('admin.view.total');



    Route::get('user/view', [UserViewController::class, 'index'])->name('user.view');

    // Package

    Route::resource('packages', PackagesController::class);



    Route::resource('language', LanguageController::class);



    Route::get('hardware/devices/{device}', [HardwareController::class, 'device_index'])->name('hardware.device.name');



    // Currency

    Route::resource('currency', CurrenciesController::class);



    Route::get('user/shadow/login/{ut}/{id}', [AdminProfileController::class, 'changeUser'])->name('admin.user.change');



    Route::get('white-label', [WhiteLabelController::class, 'index'])->name('white-label.index');

    Route::get('white-label/add', [WhiteLabelController::class, 'add'])->name('white-label.edit');

    Route::get('white-label/edit/{id}', [WhiteLabelController::class, 'edit']);

    Route::post('white-label/post', [WhiteLabelController::class, 'addForm']);

    Route::post('white-label/postEdit/{id}', [WhiteLabelController::class, 'postEdit']);

    Route::get('white-label/ajax/state', [WhiteLabelController::class, 'getState'])->name('get.states');

    Route::get('white-label/ajax/cities', [WhiteLabelController::class, 'getCities'])->name('get.cities');

    Route::get('white-label/users/search', [WhiteLabelController::class, 'searchUsers']);



    Route::get('admin/profile', [AdminProfileController::class, 'index'])->name('admin.profile.index');

    Route::get('admin/profile/edit', [AdminProfileController::class, 'edit'])->name('admin.profile.edit');

    Route::post('admin/profile/post/{id}', [AdminProfileController::class, 'post']);

    Route::resource('admin/software/version', VersionController::class)->names('admin.software.version');

});



// Routes for White Label Admin (WC)

Route::middleware(['auth', 'WC'])->group(function () {



    Route::post('rs/check-email', [DashboardController::class, 'checkMail']);



    Route::get('wc/shadow/login/{ut}/{id}', [WhiteProfileController::class, 'changeUser'])->name('wc.user.change');



    Route::get('reseller', [ResellerController::class, 'index'])->name('reseller.index');

    Route::get('reseller/add', [ResellerController::class, 'add'])->name('reseller.add');

    Route::post('reseller/addForm', [ResellerController::class, 'addForm'])->name('reseller.addForm');

    Route::get('reseller/edit/{id}', [ResellerController::class, 'edit'])->name('reseller.edit');

    Route::post('reseller/post/{id}', [ResellerController::class, 'post'])->name('reseller.post.edit');



    Route::prefix('company')->group(function () {

        Route::get('/', [CompanyController::class, 'index'])->name('company.index');

        Route::get('/add', [CompanyController::class, 'add'])->name('company.add');

        Route::post('/addForm', [CompanyController::class, 'addForm'])->name('company.addForm');

        Route::get('/edit/{id}', [CompanyController::class, 'edit'])->name('company.edit');

        Route::post('/post/{id}', [CompanyController::class, 'post'])->name('company.post.edit');

    });



    Route::get('white-label/profile', [WhiteProfileController::class, 'index'])->name('white-label.profile.index');

    Route::get('white-label/profile/edit', [WhiteProfileController::class, 'edit'])->name('white-label.profile.edit');

    Route::post('white-label/profile/post/{id}', [WhiteProfileController::class, 'post'])->name('white-label.profile.post');

});



Route::middleware(['auth', 'EC'])->group(function () {



    Route::post('ec/check-email', [DashboardController::class, 'checkMail']);



    Route::get('company/dashboard', [CompController::class, 'index'])->name('company.dashboard');



    Route::get('company/user/change', [CompController::class, 'change_user'])->name('company.user.change')->middleware('ecCheck');



    Route::get('ec/change/{id}', [CompController::class, 'chUsers'])->name('change.ec.user');



    Route::get('company/dashboard/change/{id}', [CompController::class, 'change_dashboard'])->name('company.dashboard.change');



    Route::get('ec/shadow/login/{ut}/{id}', [CompController::class, 'changeUser'])->name('ec.user.change');



    Route::prefix('transport')->group(function () {

        Route::get('/', [TransportController::class, 'index'])->name('transport.index');

        Route::get('/add', [TransportController::class, 'add'])->name('transport.add');

        Route::post('/addForm', [TransportController::class, 'addForm'])->name('transport.addForm');

        Route::get('/edit/{id}', [TransportController::class, 'edit'])->name('transport.edit');

        Route::post('/post/{id}', [TransportController::class, 'post'])->name('transport.post.edit');

    });

});



Route::middleware(['auth', 'RS'])->group(function () {



    Route::post('user/check-email', [DashboardController::class, 'checkMail']);



    Route::get('rs/shadow/login/{ut}/{id}', [RSController::class, 'changeUser'])->name('rs.user.change');



    Route::get('reseller/dashboard', [RSController::class, 'index'])->name('reseller.dashboard');



    Route::prefix('user')->group(function () {

        Route::get('/', [UserController::class, 'index'])->name('user.index');

        Route::get('/add', [UserController::class, 'add'])->name('user.add');

        Route::post('/addForm', [UserController::class, 'addForm'])->name('user.addForm');

        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('user.edit');

        Route::post('/post/{id}', [UserController::class, 'post'])->name('user.post.edit');

    });



});



Route::middleware(['auth', 'TR'])->group(function () {



    Route::get('tr/shadow/login/{ut}/{id}', [DashboardController::class, 'changeUser'])->name('tr.user.change');



    Route::post('check-email', [DashboardController::class, 'checkMail']);



    Route::get('transport/dashboard', [DashboardController::class, 'default']);



    Route::get('tr/change/{id}', [DashboardController::class, 'chUsers'])->name('change.tr.user');



    Route::get('sse/send', [SSEController::class, 'index'])->name('sse.send.index');



    Route::get('get-vehicles', [DriverActivityController::class, 'get_vehicles']);



    Route::prefix('{lang?}/settings')->group(function () {



        // Additional routes with specific permissions

        Route::get('device', [DeviceController::class, 'index'])->name('setting.device.index')->middleware('permission:35');

        Route::get('device/create', [DeviceController::class, 'create'])->name('setting.device.create')->middleware('permission:33');

        Route::post('device', [DeviceController::class, 'store'])->name('setting.device.store')->middleware('permission:33');

        Route::get('device/{device}/edit', [DeviceController::class, 'edit'])->name('setting.device.edit')->middleware('permission:34');

        Route::put('device/{device}', [DeviceController::class, 'update'])->name('setting.device.update')->middleware('permission:34');

        Route::delete('device/{device}', [DeviceController::class, 'destroy'])->name('setting.device.destroy');





        // Additional routes with specific permissions

        Route::get('user_roles', [UserRoleController::class, 'index'])->name('settings.organisation.userRoles.index')->middleware('permission:26');

        Route::get('user_roles/create', [UserRoleController::class, 'create'])->name('settings.organisation.userRoles.create')->middleware('permission:24');

        Route::post('user_roles', [UserRoleController::class, 'store'])->name('settings.organisation.userRoles.store')->middleware('permission:24');

        Route::get('user_roles/{userRole}/edit', [UserRoleController::class, 'edit'])->name('settings.organisation.userRoles.edit')->middleware('permission:25');

        Route::put('user_roles/{userRole}', [UserRoleController::class, 'update'])->name('settings.organisation.userRoles.update')->middleware('permission:25');

        Route::delete('user_roles/{userRole}', [UserRoleController::class, 'destroy'])->name('settings.organisation.userRoles.destroy');





    });



    Route::get('data/date', [DriverController::class, 'data_date'])->name('data.dates.index');



    Route::post('generate-username', [DriverController::class, 'generate_username']);



    Route::post('check-username', [DriverController::class, 'check_username']);



    Route::post('editUsername', [DriverController::class, 'editUsername']);



    Route::prefix('{lang?}')->group(function () {



        Route::get('compliance', [ComplianceController::class, 'index'])->name('driver.compliance')->middleware('permission:17');



        Route::get('driver/{id}/log', [DriverController::class, 'driver_detail'])->name('driver.driver_detail');



        Route::get('transport/change/{id}', [DashboardController::class, 'changeId'])->name('transport.change');



        Route::get('transport/dashboard', [DashboardController::class, 'index'])->name('transport.dashboard');



        Route::get('report/data-log', [DriverController::class, 'data_log'])->name('driver.report.data')->middleware('permission:18');

        Route::get('report/data-bluetooth', [DriverController::class, 'bluetooth_log'])->name('driver.report.bluetooth')->middleware('permission:18');


        Route::get('report/vechile', [DriverController::class, 'report_vechile'])->name('driver.report.vechile')->middleware('permission:19');



        Route::prefix('settings/organistaion')->group(function () {



            Route::prefix('driver')->group(function () {



                Route::get('/', [DriverController::class, 'driver_organisation'])->name('setting.driver.organisation')->middleware('permission:29');



                Route::get('add', [DriverController::class, 'driver_organisation_add'])->name('setting.driver.organisation.add')->middleware('permission:27');



                Route::get('edit/{id}', [DriverController::class, 'driver_organisation_edit'])->name('setting.driver.organisation.edit')->middleware('permission:31');



                Route::post('edit_post/{id}', [DriverController::class, 'edit_post'])->name('setting.driver.organisation.edit.post')->middleware('permission:31');



                Route::post('add_post', [DriverController::class, 'add_post'])->name('setting.driver.organisation.add.post')->middleware('permission:27');



            });



            // Additional routes with specific permissions

            Route::get('driver-activity', [DriverActivityController::class, 'index'])->name('driver.activity.index')->middleware('permission:32');

            Route::get('driver-activity/create', [DriverActivityController::class, 'create'])->name('driver.activity.create')->middleware('permission:30');

            Route::post('driver-activity', [DriverActivityController::class, 'store'])->name('driver.activity.store')->middleware('permission:30');

            Route::get('driver-activity/{driverActivity}/edit', [DriverActivityController::class, 'edit'])->name('driver.activity.edit')->middleware('permission:31');

            Route::put('driver-activity/{driverActivity}', [DriverActivityController::class, 'update'])->name('driver.activity.update')->middleware('permission:31');

            Route::delete('driver-activity/{driverActivity}', [DriverActivityController::class, 'destroy'])->name('driver.activity.destroy');



        });



        Route::get('overview/enviorement', [DriverController::class, 'enviorement_data'])->name('overview.enviorement.data')->middleware('permission:15');



        Route::get('/overview/map', [MapController::class, 'show_map'])->name('view.overview.map')->middleware('permission:16');



    });



    Route::prefix('{lang?}/driver')->group(function () {

        Route::get('/', [DriverController::class, 'index'])->name('driver.auth.index')->middleware('permission:12');

        Route::get('/add', [DriverController::class, 'add'])->name('driver.auth.add')->middleware('permission:10');

        Route::post('/addForm', [DriverController::class, 'addForm'])->name('driver.auth.addForm')->middleware('permission:10');

        Route::get('/edit/{id}', [DriverController::class, 'edit'])->name('driver.auth.edit')->middleware('permission:11');

        Route::post('/post/{id}', [DriverController::class, 'post'])->name('driver.auth.post')->middleware('permission:11');

    });



    Route::prefix('{lang?}/transport/assets')->group(function () {



        Route::get('vehicles', [VehiclesController::class, 'index'])->name('vehicles.index')->middleware('permission:3');



        Route::get('vehicles/create', [VehiclesController::class, 'create'])->name('vehicles.create')->middleware('permission:1');



        Route::post('vehicles', [VehiclesController::class, 'store'])->name('vehicles.store')->middleware('permission:1');



        // Route::get('vehicles/{vehicle}', [VehiclesController::class, 'show'])->name('vehicles.show')->middleware('permission:view vehicle');



        // Edit

        Route::get('vehicles/{vehicle}/edit', [VehiclesController::class, 'edit'])->name('vehicles.edit')->middleware('permission:2');

        Route::put('vehicles/{vehicle}', [VehiclesController::class, 'update'])->name('vehicles.update')->middleware('permission:2');



        // Delete

        Route::delete('vehicles/{vehicle}', [VehiclesController::class, 'destroy'])->name('vehicles.destroy');



        // Additional routes with specific permissions

        Route::get('addresses', [AddressesController::class, 'index'])->name('addresses.index')->middleware('permission:6');

        Route::get('addresses/create', [AddressesController::class, 'create'])->name('addresses.create')->middleware('permission:4');

        Route::post('addresses', [AddressesController::class, 'store'])->name('addresses.store')->middleware('permission:4');

        Route::get('addresses/{address}/edit', [AddressesController::class, 'edit'])->name('addresses.edit')->middleware('permission:5');

        Route::put('addresses/{address}', [AddressesController::class, 'update'])->name('addresses.update')->middleware('permission:5');

        Route::delete('addresses/{address}', [AddressesController::class, 'destroy'])->name('addresses.destroy');



    });



    Route::prefix('{lang?}/transport')->group(function () {

        Route::resource('document', DocumentController::class)->middleware('permission:9');

        Route::prefix('settings/organization')->group(function () {

            Route::resource('general', GeneralController::class)->names('organization.general')->middleware('permission:23');

        });

    });



});



Route::middleware(['auth', 'DR'])->group(function () {



    Route::get('driver/dashboard', [DriverDashboardController::class, 'index'])->name('driver.dashboard');



    Route::resource('driver/driver/activity', ActivityDriverController::class)->names('driver.activity.dashboard');



    Route::resource('driver/documents', DriverDocumentController::class)->names('driver.documents');



});



require __DIR__ . '/auth.php';

