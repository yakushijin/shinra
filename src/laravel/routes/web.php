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

Auth::routes();

//get
Route::get('/', function () {
    return view('welcome');
});
Route::get('/', 'WorkControllers@allGet');
Route::get('/work', 'WorkControllers@allGet');
Route::get('/admin', 'AdminControllers@admin');
Route::get('/home', 'HomeController@index')->name('home');

Route::post('/databasecreate', 'SystemInitControllers@databaseCreate');
Route::post('/initdataadd', 'SystemInitControllers@initDataAdd');
Route::post('/logininit', 'SystemInitControllers@loginInit')->name('logininit');


//ワーク画面
Route::post('/taskget', 'WorkControllers@taskGet');
Route::post('/categoryadd', 'WorkControllers@categoryAdd');
Route::post('/taskadd', 'WorkControllers@taskAdd');
Route::post('/categoryupdate', 'WorkControllers@categoryUpdate');
Route::post('/taskupdate', 'WorkControllers@taskUpdate');
Route::post('/tabupdate', 'WorkControllers@tabUpdate');
Route::post('/categorydelete', 'WorkControllers@categoryDelete');
Route::post('/taskdelete', 'WorkControllers@taskDelete');
Route::post('/categorystatusupdate', 'WorkControllers@categoryStatusUpdate');
Route::post('/categorysuspended', 'WorkControllers@categorySuspended');
Route::post('/taskstatusupdate', 'WorkControllers@taskStatusUpdate');
Route::post('/tasksuspended', 'WorkControllers@taskSuspended');
Route::post('/categorydeadlineupdate', 'WorkControllers@categoryDeadlineUpdate');
Route::post('/taskdeadlineupdate', 'WorkControllers@taskDeadlineUpdate');
Route::post('/categorysort', 'WorkControllers@categorySort');
Route::post('/tasksort', 'WorkControllers@taskSort');

//モーダル_基本
Route::post('/userinfoget', 'ModalControllers@userInfoGet');
Route::post('/groupinfoget', 'ModalControllers@groupInfoGet');
Route::post('/tabinfoget', 'ModalControllers@tabInfoGet');
Route::post('/userinfoupdate', 'ModalControllers@userInfoUpdate');
Route::post('/groupinfoupdate', 'ModalControllers@groupInfoUpdate');
Route::post('/categorydetailupdate', 'ModalControllers@categoryDetailUpdate');
Route::post('/taskdetailupdate', 'ModalControllers@taskDetailUpdate');
Route::post('/tabdelete', 'ModalControllers@tabDelete');
Route::post('/groupuserget', 'ModalControllers@groupUserget');
Route::post('/groupnotuserget', 'ModalControllers@groupNotUserget');
Route::post('/tabgroupchange', 'ModalControllers@tabGroupChange');
Route::post('/categoryuserchange', 'ModalControllers@categoryUserChange');
Route::post('/grouptabchange', 'ModalControllers@groupTabChange');
Route::post('/tabadd', 'ModalControllers@tabAdd');
Route::post('/tabinfoupdate', 'ModalControllers@tabInfoUpdate');
Route::post('/categoryinfoget', 'ModalControllers@categoryInfoGet');
Route::post('/taskinfoget', 'ModalControllers@taskInfoGet');
Route::post('/usergroupget', 'ModalControllers@userGroupGet');
Route::post('/groupallget', 'ModalControllers@groupAllGet');
Route::post('/groupuserchange', 'ModalControllers@groupUserChange');
Route::post('/categorydoneArchive', 'ModalControllers@categoryDoneArchive');
Route::post('/categoryfilter', 'ModalControllers@categoryFilter');

//モーダル_レポート
Route::post('/userreport', 'ReportControllers@userReport');
Route::post('/groupreport', 'ReportControllers@groupReport');
Route::post('/tabreport', 'ReportControllers@tabReport');

//モーダル_機械学習
Route::post('/getwordtouser', 'MachinelearnControllers@getWordToUser');
Route::post('/getusertoword', 'MachinelearnControllers@getUserToWord');
Route::post('/getallnewword', 'MachinelearnControllers@getAllNewWord');
Route::post('/getfreewordtoid', 'MachinelearnControllers@getFreeWordToId');
Route::post('/getnewwordtoid', 'MachinelearnControllers@getNewWordToId');

//管理者ユーザ用画面
Route::post('/tabsearch', 'AdminControllers@tabSearch');
Route::post('/usersearch', 'AdminControllers@userSearch');
Route::post('/groupsearch', 'AdminControllers@groupSearch');
Route::post('/useradd', 'AdminControllers@userAdd');
Route::post('/groupadd', 'AdminControllers@groupAdd');
Route::post('/performancesearch', 'AdminControllers@performanceSearch');
Route::post('/companyget', 'AdminControllers@companyGet');
Route::post('/companyupdate', 'AdminControllers@companyUpdate');
// Route::post('/systemuserchange', 'AdminControllers@systemUserChange');システムユーザ変更関連API保留
// Route::post('/adminuserget', 'AdminControllers@adminUserGet');システムユーザ変更関連API保留
Route::post('/logininfoget', 'AdminControllers@loginInfoGet');
Route::post('/loginidupdate', 'AdminControllers@loginIdUpdate');
Route::post('/loginpasswordupdate', 'AdminControllers@loginPasswordUpdate');
Route::post('/systemloginnameget', 'AdminControllers@systemLoginNameGet');

//デバッグ用
Route::post('/demo', 'DebugControllers@demo');
Route::post('/machinelearning1', 'DebugControllers@machineLearning1');
Route::post('/machinelearning2', 'DebugControllers@machineLearning2');
Route::post('/machineLearningDelete', 'DebugControllers@machineLearningDelete');
Route::post('/datadelete', 'DebugControllers@datadelete');


Route::post('/register/email_check', 'Auth\RegisterController@email_check')->name('register.email_check');
Route::get('/systeminit', 'SystemInitControllers@systemInit');
Route::get('/register/{token}', 'Auth\RegisterController@preRegisterResult');
Route::get('/preregisterdone', 'Auth\RegisterController@preRegisterDone');

