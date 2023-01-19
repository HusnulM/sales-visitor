<?php
use Illuminate\Support\Facades\Route;

Route::get('/coba', function () {
    echo "Coba";
});

Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => '/master/item'], function () {
        Route::get('/',             'Master\ItemMasterController@index')->middleware('checkAuth:master/item');
        Route::get('/create',       'Master\ItemMasterController@create')->middleware('checkAuth:master/item');
        Route::get('/edit/{p1}',    'Master\ItemMasterController@edit')->middleware('checkAuth:master/item');
        Route::post('/save',        'Master\ItemMasterController@save')->middleware('checkAuth:master/item');
        Route::post('/update',      'Master\ItemMasterController@update')->middleware('checkAuth:master/item');
        Route::get('/delete/{id}',  'Master\ItemMasterController@delete')->middleware('checkAuth:master/item');  
        Route::get('/itemlist',     'Master\ItemMasterController@itemLists')->middleware('checkAuth:master/item');  
        Route::get('/itemcatlist',  'Master\ItemMasterController@itemCategoryLists')->middleware('checkAuth:master/item');  
        Route::get('/uomlists',     'Master\ItemMasterController@uomLists')->middleware('checkAuth:master/item');  
       
        Route::post('/saveitemcategory', 'Master\ItemMasterController@saveitemcategory')->middleware('checkAuth:master/item');
        Route::post('/saveuom',          'Master\ItemMasterController@saveuom')->middleware('checkAuth:master/item');

        Route::post('/findpartnumber', 'Master\ItemMasterController@findPartnumber');
    });

    

    Route::group(['prefix' => '/master/department'], function () {
        Route::get('/',             'Master\DepartmentMasterController@index')->middleware('checkAuth:master/department');
        Route::get('/create',       'Master\DepartmentMasterController@create')->middleware('checkAuth:master/department');
        Route::post('/save',        'Master\DepartmentMasterController@save')->middleware('checkAuth:master/department');
        Route::post('/update',      'Master\DepartmentMasterController@update')->middleware('checkAuth:master/department');
        Route::get('/delete/{id}',  'Master\DepartmentMasterController@delete')->middleware('checkAuth:master/department');  
        Route::get('/deptlists',    'Master\DepartmentMasterController@departmentLists')->middleware('checkAuth:master/department');  
        
    });

    Route::group(['prefix' => '/master/jabatan'], function () {
        Route::get('/',             'Master\JabatanMasterController@index')->middleware('checkAuth:master/jabatan');
        Route::get('/create',       'Master\JabatanMasterController@create')->middleware('checkAuth:master/jabatan');
        Route::post('/save',        'Master\JabatanMasterController@save')->middleware('checkAuth:master/jabatan');
        Route::post('/update',      'Master\JabatanMasterController@update')->middleware('checkAuth:master/jabatan');
        Route::get('/delete/{id}',  'Master\JabatanMasterController@delete')->middleware('checkAuth:master/jabatan');  
        Route::get('/jabatanlist',  'Master\JabatanMasterController@jabatanLists')->middleware('checkAuth:master/jabatan');  
        
    });

    Route::group(['prefix' => '/master/pegawai'], function () {
        Route::get('/',             'Master\PegawaiController@index')->middleware('checkAuth:master/pegawai');
        Route::get('/create',       'Master\PegawaiController@create')->middleware('checkAuth:master/pegawai');
        Route::post('/save',        'Master\PegawaiController@save')->middleware('checkAuth:master/pegawai');
        Route::post('/update',      'Master\PegawaiController@update')->middleware('checkAuth:master/pegawai');
        Route::get('/delete/{id}',  'Master\PegawaiController@delete')->middleware('checkAuth:master/pegawai');  
        Route::get('/listpegawai',  'Master\PegawaiController@listPegawai')->middleware('checkAuth:master/pegawai');  
        
    });

    Route::group(['prefix' => '/master/toko'], function () {
        Route::get('/',             'Master\TokoController@index')->middleware('checkAuth:master/toko');
        Route::get('/create',       'Master\TokoController@create')->middleware('checkAuth:master/toko');
        Route::get('/edit/{id}',    'Master\TokoController@edit')->middleware('checkAuth:master/toko');
        Route::post('/save',        'Master\TokoController@save')->middleware('checkAuth:master/toko');
        Route::post('/update',      'Master\TokoController@update')->middleware('checkAuth:master/toko');
        Route::get('/delete/{id}',  'Master\TokoController@delete')->middleware('checkAuth:master/toko');  
        Route::get('/listtoko',     'Master\TokoController@listToko')->middleware('checkAuth:master/toko');  
        Route::post('/findtoko',    'Master\TokoController@findToko');
    });
});