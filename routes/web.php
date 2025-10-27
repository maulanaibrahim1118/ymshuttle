<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/', function () {
    return redirect('/login');
});

Route::group(['middleware' => ['auth']], function () {
    Route::get('/account-setting', 'AccountController@index')->name('account.index');
    Route::post('/password-change', 'AccountController@changePassword')->name('password.change');
    
    Route::middleware(['force.password.change'])->group(function () {
        Route::get('/home', 'HomeController@index')->name('home');
        
        Route::get('/master-locations', 'LocationController@index')->name('locations.index')->middleware('permission:location-view');
        Route::get('/ajax/list-locations', 'LocationController@list')->name('locations.list')->middleware('permission:location-view');

        Route::get('/master-categories', 'CategoryController@index')->name('categories.index')->middleware('permission:category-view');
        Route::post('/master-categories/store', 'CategoryController@store')->name('categories.store')->middleware('permission:category-add');
        Route::put('/master-categories/update', 'CategoryController@update')->name('categories.update')->middleware('permission:category-edit');
        Route::delete('/master-categories/destroy/{id}', 'CategoryController@destroy')->name('categories.destroy')->middleware('permission:category-delete');
        Route::get('/ajax/check-unique-category-name', 'CategoryController@checkUniqueName')->middleware('permission:category-add|category-edit');
        Route::get('/ajax/master-categories/{id}/get-criterias', 'CategoryController@getCriterias')->middleware('permission:category-edit');

        Route::get('/shipments', 'ShipmentController@index')->name('shipments.index');
        Route::get('/shipments/create', 'ShipmentController@create')->name('shipments.create');
        Route::post('/shipments/store', 'ShipmentController@store')->name('shipments.store');
        Route::get('/shipments/details/{id}', 'ShipmentController@show')->name('shipments.show');
        Route::get('/shipments/scan', 'ShipmentController@scanPage')->name('shipments.scan');
        Route::post('/shipments/scan', 'ShipmentController@scanProcess')->name('shipments.scan.process');
        Route::post('/shipments/collect/{noShipment}', 'ShipmentController@collect')->name('shipments.collect');
        Route::get('/ajax/list-shipments', 'ShipmentController@list')->name('shipments.list');

        Route::get('/setting-users', 'UserController@index')->name('users.index')->middleware('permission:user-view');
        Route::get('/setting-users/list', 'UserController@list')->name('users.list')->middleware('permission:user-view');
        Route::post('/setting-users/store', 'UserController@store')->name('users.store')->middleware('permission:user-add');
        Route::put('/setting-users/update', 'UserController@update')->name('users.update')->middleware('permission:user-edit');
        Route::post('/setting-users/import', 'UserController@import')->name('users.import')->middleware('permission:user-import');
        Route::post('/setting-users/reset-pass/{id}', 'UserController@resetPassword')->name('users.reset')->middleware('permission:user-reset-password');
        Route::post('/setting-users/activate/{id}', 'UserController@activate')->name('users.activate')->middleware('permission:user-activate');
        Route::post('/setting-users/deactivate/{id}', 'UserController@deactivate')->name('users.deactivate')->middleware('permission:user-deactivate');
        Route::get('/ajax/check-unique-username', 'UserController@checkUniqueUsername')->middleware('permission:user-add|user-edit');
        
        Route::get('/setting-roles', 'RoleController@index')->name('roles.index')->middleware('permission:role-view');
        Route::post('/setting-roles/store', 'RoleController@store')->name('roles.store')->middleware('permission:role-add');
        Route::put('/setting-roles/update', 'RoleController@update')->name('roles.update')->middleware('permission:role-edit');
        Route::delete('/setting-roles/destroy/{id}', 'RoleController@destroy')->name('roles.destroy')->middleware('permission:role-delete');
        Route::put('/setting-roles/{encryptedId}/update-permissions', 'RoleController@updatePermissions')->name('roles.update.permission')->middleware('permission:role-edit-permission');
        Route::get('/ajax/check-unique-role-name', 'RoleController@checkUniqueName')->middleware('permission:role-add|role-edit');
        Route::get('/ajax/setting-roles/{encryptedId}/permissions', 'RoleController@getPermissions')->middleware('permission:role-permission');
        Route::get('/ajax/setting-roles/{encryptedId}/permissions-edit', 'RoleController@getEditPermissions')->middleware('permission:role-edit-permission');
        
        Route::get('/log-activities', 'LogActivityController@index')->name('logActivities.index')->middleware('permission:logActivity-view');
        Route::get('/log-activities/list', 'LogActivityController@list')->name('logActivities.list')->middleware('permission:logActivity-view');
    });
});