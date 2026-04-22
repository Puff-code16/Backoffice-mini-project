<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BackOffice\DashboardController;
use App\Http\Controllers\BackOffice\DepartmentController;
use App\Http\Controllers\BackOffice\EmployeeController;
use App\Http\Controllers\BackOffice\MenuController;
use App\Http\Controllers\BackOffice\PlaceController;
use App\Http\Controllers\BackOffice\PositionController;
use App\Http\Controllers\BackOffice\RouteController;
use App\Http\Controllers\BackOffice\RoutePlaceController;
use App\Http\Controllers\BackOffice\TripController;
use App\Http\Controllers\BackOffice\UserController;
use App\Http\Controllers\BackOffice\VehicleController;
use App\Http\Controllers\BackOffice\VehicleTypeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/post-login', [AuthController::class, 'postlogin'])->name('login.post');

Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/post-register', [AuthController::class, 'postregister'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Backoffice
|--------------------------------------------------------------------------
*/

Route::prefix('backoffice')->group(function () {

    Route::get('/', [DashboardController::class, 'dashboardPage'])
        ->middleware('position.menu:dashboard');

    // USERS
    Route::prefix('users')->middleware('position.menu:user_manage')->group(function () {
        Route::get('/', [UserController::class, 'usersPage']);
        Route::get('/data', [UserController::class, 'data']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::post('/', [UserController::class, 'store']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });

    // MENUS
    Route::prefix('menus')->middleware('position.menu:menu_manage')->group(function () {
        Route::view('/', 'backoffice.menus');
        Route::get('/data', [MenuController::class, 'data']);
        Route::get('/all', [MenuController::class, 'listAll']);
        Route::get('/{id}', [MenuController::class, 'show']);
        Route::post('/', [MenuController::class, 'store']);
        Route::put('/{id}', [MenuController::class, 'update']);
        Route::delete('/{id}', [MenuController::class, 'destroy']);
    });

    // DEPARTMENTS
    Route::prefix('departments')->middleware('position.menu:department_position_manage')->group(function () {
        Route::get('/list', [DepartmentController::class, 'list']);
        Route::get('/data', [DepartmentController::class, 'data']);
        Route::get('/{id}', [DepartmentController::class, 'show']);
        Route::post('/', [DepartmentController::class, 'store']);
        Route::put('/{id}', [DepartmentController::class, 'update']);
        Route::delete('/{id}', [DepartmentController::class, 'destroy']);
    });

    Route::view('/org', 'backoffice.org')->middleware('position.menu:department_position_manage');

    // POSITIONS
    Route::prefix('positions')->middleware('position.menu:department_position_manage')->group(function () {
        Route::get('/list', [PositionController::class, 'list']);
        Route::get('/data', [PositionController::class, 'data']);
        Route::get('/{id}', [PositionController::class, 'show']);
        Route::post('/', [PositionController::class, 'store']);
        Route::put('/{id}', [PositionController::class, 'update']);
        Route::delete('/{id}', [PositionController::class, 'destroy']);
    });

    // EMPLOYEES
    Route::prefix('employees')->middleware('position.menu:employee_manage')->group(function () {
        Route::view('/', 'backoffice.employees');
        Route::get('/data', [EmployeeController::class, 'data']);
        Route::get('/{id}', [EmployeeController::class, 'show']);
        Route::post('/', [EmployeeController::class, 'store']);
        Route::put('/{id}', [EmployeeController::class, 'update']);
        Route::delete('/{id}', [EmployeeController::class, 'destroy']);
    });

    // VEHICLE TYPES
    Route::prefix('vehicle-types')->middleware('position.menu:vehicle_vehicle_type_manage')->group(function () {
        Route::get('/list', [VehicleTypeController::class, 'list']);
        Route::get('/data', [VehicleTypeController::class, 'data']);
        Route::get('/{id}', [VehicleTypeController::class, 'show']);
        Route::post('/', [VehicleTypeController::class, 'store']);
        Route::put('/{id}', [VehicleTypeController::class, 'update']);
        Route::delete('/{id}', [VehicleTypeController::class, 'destroy']);
    });

    // VEHICLES
    Route::prefix('vehicles')->middleware('position.menu:vehicle_vehicle_type_manage')->group(function () {
        Route::view('/', 'backoffice.vehicles');
        Route::get('/data', [VehicleController::class, 'data']);
        Route::get('/{id}', [VehicleController::class, 'show']);
        Route::post('/', [VehicleController::class, 'store']);
        Route::put('/{id}', [VehicleController::class, 'update']);
        Route::delete('/{id}', [VehicleController::class, 'destroy']);
    });

    // ROUTES
    Route::prefix('routes')->middleware('position.menu:routes_places_manage')->group(function () {
        Route::get('/data', [RouteController::class, 'data']);
        Route::get('/{id}', [RouteController::class, 'show']);
        Route::post('/', [RouteController::class, 'store']);
        Route::put('/{id}', [RouteController::class, 'update']);
        Route::delete('/{id}', [RouteController::class, 'destroy']);

        Route::get('/{route}/route-places-page', function ($route) {
            return view('backoffice.route_places');
        });

        Route::get('/{route}/route-places', [RoutePlaceController::class, 'data']);
        Route::post('/{route}/route-places', [RoutePlaceController::class, 'store']);
        Route::put('/{route}/route-places/{route_place}', [RoutePlaceController::class, 'update']);
        Route::delete('/{route}/route-places/{route_place}', [RoutePlaceController::class, 'destroy']);
        Route::post('/{route}/route-places/reorder', [RoutePlaceController::class, 'reorder']);
    });

    Route::view('/routes-places', 'backoffice.routes_places')
        ->middleware('position.menu:routes_places_manage');

    // PLACES
    Route::prefix('places')->middleware('position.menu:routes_places_manage')->group(function () {
        Route::get('/list', [PlaceController::class, 'list']);
        Route::get('/data', [PlaceController::class, 'data']);
        Route::get('/{id}', [PlaceController::class, 'show']);
        Route::post('/', [PlaceController::class, 'store']);
        Route::put('/{id}', [PlaceController::class, 'update']);
        Route::delete('/{id}', [PlaceController::class, 'destroy']);
    });

    // TRIPS
    Route::prefix('trips')->middleware('position.menu:trips_manage')->group(function () {
        Route::view('/', 'backoffice.trips');
        Route::get('/data', [TripController::class, 'data']);
        Route::get('/init', [TripController::class, 'init']);
        Route::get('/{id}', [TripController::class, 'show']);
        Route::post('/', [TripController::class, 'store']);
        Route::put('/{id}', [TripController::class, 'update']);
        Route::delete('/{id}', [TripController::class, 'destroy']);
    });
});

/*
|--------------------------------------------------------------------------
| TEST OCI
|--------------------------------------------------------------------------
*/

Route::get('/test-oci', function () {
    return function_exists('oci_connect') ? 'YES' : 'NO';
});