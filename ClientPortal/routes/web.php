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

Route::get('/', function () {
    if (!session('logged_in')) {
        return redirect('login');
    } else {
        return redirect('report');
    }
});

// route to show the login form
Route::get('login', array('uses' => 'AuthController@getLogin'));

Route::get('logout', array('uses' => 'AuthController@getLogOut'));

// route to process the form
Route::post('login', array('uses' => 'AuthController@postLogin'));


Route::get('report', array('uses' => 'ReportController@index'))->middleware('crmauth');

Route::post('activities', array('uses' => 'ReportController@activities'))->middleware('crmauth');

Route::post('chart-drill-down', array('uses' => 'ReportController@chartDrillDown'))->middleware('crmauth');

Route::post('report-delivery', array('uses' => 'ReportController@reportDelivery'))->middleware('crmauth');

Route::post('edit-appointment', array('uses' => 'ReportController@editAppointment'))->middleware('crmauth');

Route::post('salesreps', array('uses' => 'ReportController@salesreps'))->middleware('crmauth');

Route::post('positive-appointments', array('uses' => 'ReportController@positiveAppointments'))->middleware('crmauth');

Route::post('download-appointments', array('uses' => 'ReportController@downloadExcel'))->middleware('crmauth');


Route::get('account', array('uses' => 'AccountController@index'))->middleware('crmauth');

Route::post('target-lists', array('uses' => 'AccountController@fetchLists'))->middleware('crmauth');

Route::post('target-lists-dt', array('uses' => 'AccountController@getList'))->middleware('crmauth');

Route::post('remove-accounts', array('uses' => 'AccountController@removeAccounts'))->middleware('crmauth');

Route::post('get-account-lists-dt', array('uses' => 'AccountController@getAccountList'))->middleware('crmauth');

Route::post('create-new-list', array('uses' => 'AccountController@createNewList'))->middleware('crmauth');

Route::post('lock-list', array('uses' => 'AccountController@lockList'))->middleware('crmauth');

Route::post('add-to-list', array('uses' => 'AccountController@addToList'))->middleware('crmauth');

Route::post('delete-list', array('uses' => 'AccountController@deleteList'))->middleware('crmauth');


Route::get('feedback', array('uses' => 'FeedbackController@index'));

Route::post('feedback', array('uses' => 'FeedbackController@submitFeedback'));