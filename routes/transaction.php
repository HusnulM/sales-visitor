<?php
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => '/transaction/budgeting'], function () {
        Route::get('/',             'Transaksi\BudgetingController@index')->middleware('checkAuth:transaction/budgeting');
        Route::post('/save',        'Transaksi\BudgetingController@save')->middleware('checkAuth:transaction/budgeting');
        Route::get('/list',         'Transaksi\BudgetingController@list')->middleware('checkAuth:transaction/budgeting');  
        Route::get('/budgetlist',   'Transaksi\BudgetingController@budgetLists')->middleware('checkAuth:transaction/budgeting');  
        
    });

    Route::group(['prefix' => '/transaksi/salesvisit'], function () {
        Route::get('/',             'Transaksi\SalesVisitController@index')->middleware('checkAuth:transaksi/salesvisit');
        Route::get('/new',          'Transaksi\SalesVisitController@newVisit')->middleware('checkAuth:transaksi/salesvisit');
        Route::post('/save',        'Transaksi\SalesVisitController@save')->middleware('checkAuth:transaksi/salesvisit');
        Route::post('/savenew',     'Transaksi\SalesVisitController@saveNew')->middleware('checkAuth:transaksi/salesvisit');
    });
    // transaksi/salesvisit

    Route::post('/saleschecklog',   'Transaksi\SalesVisitController@saveCheckLog');

});