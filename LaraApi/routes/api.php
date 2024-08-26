<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MenuController;

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

header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Authorization, Accept,charset,boundary,Content-Length');
header('Access-Control-Allow-Origin: *');

Route::middleware(['json.response'])->group(function () {
    Route::post('Users/GetLoginInfo', [UserController::class, 'GetLoginInfo']);
    Route::put('Users/UpdateLoginHistory', [UserController::class, 'UpdateLoginHistory']);
    Route::get('Users/GeneralSettings', [UserController::class, 'GeneralSettings']);
});


Route::middleware(['auth:sanctum','json.response'])->group(function () {
    Route::post('Users/CreateLoginHistory', [UserController::class, 'CreateLoginHistory'])->middleware('api.common');

    //Chart
    Route::get('Users/GetLogInSummaryByDate/{userId}', [UserController::class, 'GetLogInSummaryByDate'])->middleware('api.common');
    Route::get('Users/GetLogInSummaryByMonth/{userId}', [UserController::class, 'GetLogInSummaryByMonth'])->middleware('api.common');
    Route::get('Users/GetLogInSummaryByYear/{userId}', [UserController::class, 'GetLogInSummaryByYear'])->middleware('api.common');
    Route::get('Users/GetRoleWiseUser', [UserController::class, 'GetRoleWiseUser'])->middleware('api.common');
    Route::get('Users/GetSummaryByBrowser/{userId}', [UserController::class, 'GetSummaryByBrowser'])->middleware('api.common');

    //User Role
    Route::get('Users/GetUserRoleList', [UserController::class, 'GetUserRoleList'])->middleware('api.superAdmin');
    Route::get('Users/GetSingleRole/{roleId}', [UserController::class, 'GetSingleRole'])->middleware('api.superAdmin');
    Route::delete('Users/DeleteSingleRole/{roleId}', [UserController::class, 'DeleteSingleRole'])->middleware('api.superAdmin');
    Route::post('Users/CreateUserRole', [UserController::class, 'CreateUserRole'])->middleware('api.superAdmin');
    Route::put('Users/UpdateUserRole', [UserController::class, 'UpdateUserRole'])->middleware('api.superAdmin');

    //User
    Route::get('Users/GetUserList', [UserController::class, 'GetUserList'])->middleware('api.superAdmin');
    Route::get('Users/GetSingleUser/{userId}', [UserController::class, 'GetSingleUser'])->middleware('api.common');
    Route::delete('Users/DeleteSingleUser/{userId}', [UserController::class, 'DeleteSingleUser'])->middleware('api.superAdmin');
    Route::post('Users/CreateUser', [UserController::class, 'CreateUser'])->middleware('api.superAdmin');
    Route::put('Users/UpdateUser', [UserController::class, 'UpdateUser'])->middleware('api.superAdmin');
    Route::put('Users/UpdateUserProfile', [UserController::class, 'UpdateUserProfile'])->middleware('api.common');
    Route::post('Users/Unlock', [UserController::class, 'Unlock'])->middleware('api.common');
    Route::put('Users/ChangeUserPassword', [UserController::class, 'ChangeUserPassword'])->middleware('api.common');
    Route::get('Users/UserStatus', [UserController::class, 'UserStatus'])->middleware('api.common');
    Route::post('Users/Upload', [UserController::class, 'Upload'])->middleware('api.common');
    Route::get('Users/GetBrowseList', [UserController::class, 'GetBrowseList'])->middleware('api.common');
    Route::get('Users/GetNotificationList', [UserController::class, 'GetNotificationList'])->middleware('api.common');

    //settings
    Route::put('Users/UpdateSiteTitle', [UserController::class, 'UpdateSiteTitle'])->middleware('api.superAdmin');
    Route::put('Users/UpdateDescription', [UserController::class, 'UpdateDescription'])->middleware('api.superAdmin');
    Route::put('Users/UpdateFooter', [UserController::class, 'UpdateFooter'])->middleware('api.superAdmin');

    //Menu
    Route::get('Menu/GetMenuList', [MenuController::class, 'GetMenuList'])->middleware('api.superAdmin');
    Route::get('Menu/GetParentMenuList', [MenuController::class, 'GetParentMenuList'])->middleware('api.superAdmin');
    Route::get('Menu/GetSingleMenu/{menuId}', [MenuController::class, 'GetSingleMenu'])->middleware('api.superAdmin');
    Route::delete('Menu/DeleteSingleMenu/{menuId}', [MenuController::class, 'DeleteSingleMenu'])->middleware('api.superAdmin');
    Route::post('Menu/CreateMenu', [MenuController::class, 'CreateMenu'])->middleware('api.superAdmin');
    Route::put('Menu/UpdateMenu', [MenuController::class, 'UpdateMenu'])->middleware('api.superAdmin');
    Route::get('Menu/GetSidebarMenu/{roleId}', [MenuController::class, 'GetSidebarMenu'])->middleware('api.common');
    Route::get('Menu/GetAllMenu/{menuGrpId}', [MenuController::class, 'GetAllMenu'])->middleware('api.superAdmin');
    Route::post('Menu/MenuAssign', [MenuController::class, 'MenuAssign'])->middleware('api.superAdmin');

    //Menu Group
    Route::get('Menu/GetMenuGroupList', [MenuController::class, 'GetMenuGroupList'])->middleware('api.superAdmin');
    Route::get('Menu/GetSingleMenuGroup/{menuGrpId}', [MenuController::class, 'GetSingleMenuGroup'])->middleware('api.superAdmin');
    Route::delete('Menu/DeleteSingleMenuGroup/{menuGrpId}', [MenuController::class, 'DeleteSingleMenuGroup'])->middleware('api.superAdmin');
    Route::post('Menu/CreateMenuGroup', [MenuController::class, 'CreateMenuGroup'])->middleware('api.superAdmin');
    Route::put('Menu/UpdateMenuGroup', [MenuController::class, 'UpdateMenuGroup'])->middleware('api.superAdmin');

    //Menu Mapping
    Route::get('Menu/GetMenuGroupWiseMenuMappingList', [MenuController::class, 'GetMenuGroupWiseMenuMappingList'])->middleware('api.superAdmin');
});

