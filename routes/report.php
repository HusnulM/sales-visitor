<?php
use Illuminate\Support\Facades\Route;

// <!-- reports/documentlist -->
Route::group(['middleware' => 'auth'], function () {

    Route::group(['prefix' => '/report'], function () {
        Route::get('/salesvisit',               'Reports\KunjuganSalesController@index')->middleware('checkAuth:report/salesvisit');
        Route::get('/detailkunjungan',          'Reports\KunjuganSalesController@detailkunjungan')->middleware('checkAuth:report/salesvisit');
        Route::get('/salesvisitdata',           'Reports\KunjuganSalesController@datakunjuganan')->middleware('checkAuth:report/salesvisit');
        Route::get('/detaildatavisit',          'Reports\KunjuganSalesController@pemesananByKunjungan')->middleware('checkAuth:report/salesvisit');
        Route::post('/detaildatavisitbyid',     'Reports\KunjuganSalesController@detailDataKunjungan')->middleware('checkAuth:report/salesvisit');
        Route::post('/detailkunjungan/export',  'Reports\KunjuganSalesController@exportdetailDataKunjungan')->middleware('checkAuth:report/salesvisit');
        Route::post('/waktukunjungan/export',   'Reports\KunjuganSalesController@exportWaktuKunjungan')->middleware('checkAuth:report/salesvisit');

        Route::post('/findsales',               'Reports\KunjuganSalesController@findSalesman');
        // 
    });
});