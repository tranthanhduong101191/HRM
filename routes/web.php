<?php

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

Route::get('login', 'UserController@getLogin')->name('login');
Route::post('login', 'UserController@postLogin');
Route::group(['middleware'=>'auth'], function(){

    Route::get('/', function () {
        return v('pages.index');
    });
    Route::get('/user/logout', 'UserController@getLogout');

    Route::group(['middleware'=>'permission'], function(){
        Route::group(['prefix'=>'config'], function(){

            Route::get('system', 'SystemController@getSystem');
            Route::post('system', 'SystemController@postSystem');

            Route::get('groups', 'GroupController@getList');
            Route::get('groups/data', 'GroupController@dataList');
            Route::get('groups/create', 'GroupController@getCreate');
            Route::post('groups/create', 'GroupController@postCreate');
            Route::get('group/del', 'GroupController@getDelete');
            Route::get('group/edit', 'GroupController@getEdit');
            Route::post('group/edit', 'GroupController@postEdit');

            Route::get('users', 'UserController@getList');
            Route::get('users/data', 'UserController@dataList');
            Route::get('deleted-users', 'UserController@getDeletedList');
            Route::get('users/deleted_data', 'UserController@dataDeletedList');
            Route::get('users/create', 'UserController@getCreate');
            Route::post('users/create', 'UserController@postCreate');
            Route::get('user/del', 'UserController@getDelete');
            Route::get('user/recast', 'UserController@getReCast');
            Route::get('user/edit', 'UserController@getEdit');
            Route::post('user/edit', 'UserController@postEdit');
            Route::get('user/changepassword', 'UserController@getChangepassword');
            Route::post('user/changepassword', 'UserController@postChangepassword');

            Route::get('noiquy', 'NoiquyController@getList')->name('noiquy');
            Route::get('noiquy/data', 'NoiquyController@dataList');
            Route::get('noiquy/create', 'NoiquyController@getCreate');
            Route::post('noiquy/create', 'NoiquyController@postCreate');
            Route::get('noiquy/del', 'NoiquyController@getDelete');
            Route::get('noiquy/edit', 'NoiquyController@getEdit');
            Route::post('noiquy/edit', 'NoiquyController@postEdit');

            Route::get('branches', 'BranchController@getList');
            Route::get('branches/data', 'BranchController@dataList');
            Route::get('branches/create', 'BranchController@getCreate');
            Route::post('branches/create', 'BranchController@postCreate');
            Route::get('branch/del', 'BranchController@getDelete');
            Route::get('branch/edit', 'BranchController@getEdit');
            Route::post('branch/edit', 'BranchController@postEdit');

            Route::get('permissions', 'PermissionController@getList');
            Route::get('permissions/data', 'PermissionController@getData');
            Route::get('permissions/create', 'PermissionController@getCreate');
            Route::post('permissions/create', 'PermissionController@postCreate');
            Route::get('permission/del', 'PermissionController@getDelete');
            Route::get('permission/edit', 'PermissionController@getEdit');
            Route::post('permission/edit', 'PermissionController@postEdit');
            Route::get('permissions/roletable', 'PermissionController@getRoletable');
            Route::post('permissions/add-group-permission', 'PermissionController@postAddGroupPermission');

            Route::get('plugins', 'PluginController@getList');
            Route::get('plugins/create', 'PluginController@getCreate');
            Route::post('plugins/create', 'PluginController@postCreate');
            Route::get('plugin/{plugin}/install', 'PluginController@getInstallPlugin');
            Route::get('plugin/{plugin}/uninstall', 'PluginController@getUninstallPlugin');

            Route::get('widget', 'WidgetController@getIndex');
            Route::post('widget/add', 'WidgetController@postAdd');
            Route::post('widget/edit', 'WidgetController@postEdit');
            Route::get('widget/delete', 'WidgetController@getDelete');
        });

        Route::group(['prefix'=>'ajax'],function(){
            Route::get('routes-list', 'AjaxController@getRoutelist');
        });

        Route::get('sap-lich/{id}', 'SapLichController@saplich')->name('saplich');
        Route::post('sap-lich/{id}', 'SapLichController@save')->name('savesaplich');
        Route::get('cai-dat-sap-lich', 'SapLichController@saplichSetting')->name('saplichSetting');
        Route::post('cai-dat-sap-lich', 'SapLichController@saveSetting')->name('saveSetting');
        Route::post('save-shift', 'SapLichController@saveshift')->name('saveshift');

        Route::get('sap-lich-thang/{thang}/{nam}', 'SapLichController@saplichthang')->name('saplichthang');
        Route::post('up-lich-thang', 'SapLichController@uplichExcel')->name('uplichExcel');

        Route::post('giai-trinh/approve', ['as'=>'explaint.approve', 'uses'=>'GiaitrinhController@approve']);
        Route::get('giai-trinh/del', ['as'=>'explaint.del', 'uses'=>'GiaitrinhController@delete']);


        Route::post('tong-cham-lai-theo-ngay', 'TonghopcongController@tinhlaitheongay')->name('tinhlaitheongay');
        Route::post('tong-hop-cong/edit', 'TonghopcongController@updateCong')->name('updateCong');
        Route::get('cham-lai', ['as'=>'tinhlai', 'uses'=>'TonghopcongController@tinhlai']);
        Route::post('cham-lai', ['as'=>'tinhlai', 'uses'=>'TonghopcongController@tinhlai']);
        Route::get('chi-tiet-cong', ['as'=>'getCongData', 'uses'=>'TonghopcongController@chitietcong']);
    });

    Route::get('lich-su-cham-cong', 'CheckinoutController@index')->name('checkinHistory');
    Route::get('lich-su-cham-cong/data', 'CheckinoutController@checkinoutData')->name('checkinHistoryData');

    Route::get('tong-hop-cong', 'TonghopcongController@index')->name('tonghopcong');

    Route::get('tong-ket-luong', 'TongketluongController@index')->name('tongketluong');
    Route::get('tong-ket-luong/dataList', 'TongketluongController@dataList')->name('tongketluongData');
    Route::get('tong-ket-luong/tinhlailuong', 'TongketluongController@tinhlailuong')->name('tinhluong');
    Route::post('tong-ket-luong/tinhlailuongtheothang', 'TongketluongController@tinhlaitheothang')->name('tinhlailuongtheothang');

    Route::get('thuong-phat', 'ThuongphatController@index')->name('bonus');
    Route::get('thuong-phat/data', 'ThuongphatController@data')->name('bonusData');
    Route::post('thuong-phat/add', ['as'=>'addBonus', 'uses'=>'ThuongphatController@store']);
    Route::get('thuong-phat/del', ['as'=>'delBonus', 'uses'=>'ThuongphatController@delete']);

    Route::get('xem-lich-thang/{thang?}/{nam?}', 'SapLichController@xemlichthang')->name('xemlichthang');

    Route::get('giai-trinh', 'GiaitrinhController@index')->name('explaint.index');
    Route::get('giai-trinh/data', 'GiaitrinhController@data')->name('explaint.data');
    Route::post('giai-trinh/add', ['as'=>'explaint.add', 'uses'=>'GiaitrinhController@store']);
    Route::get('giai-trinh/ajax', ['as'=>'explaint.ajax', 'uses'=>'GiaitrinhController@ajax']);
});

Route::get('t', function(){
	//auth()->loginUsingId(1);
	return redirect('/');
});
Route::get('lam',function(){
    return view('bang');
});