========================================================
============   🎯 Project setup rules: ================
========================================================
1) C:\wamp64\bin\php\php8.2.26\ext 
      check if there the below two .dll file are existed.
        i) pdo_odbc.dll
        ii) php_pdo_odbc.dll 
2) Need php.ini file extensions not commented : 
    
         i)  extension=php_pdo_odbc
         ii) extension=pdo_odbc
         iii) extension=odbc
3) check API_ENDPOINT in .env file 

4) Setup the windows Task Scheduler as below :
        i) create a new task: give and and description.
       ii) Then trigers Tab->Begin the task =On a Schedule -> from button "New"-> then setup settings->daily
      iii) Then Advance settings-> Repeat task every day -> 1 minutes ( don't worry this is for just mdb file upload)->for a duration of= Indefinitely
       iv) Then Enable -> then ok .
        v) Then go Action tab->new-> Action = "start a program".
       vi) From Setting ->Program/script:->php ( you can brows for you php location)
      vii) Add arguments( optional):-> C:\zk-sync\sync_access.php (this php script location ,   which will be in local pc).
     viii)  Start in ( optional) ;-> C:\wamp64\www\atosql (( this is program app location )).
       ix) Conditions-> Power -> select 1st 2( start task .. on Ac power & stop if the .. battery power)
        x) Settings-> Allow task to be run on demand && if the runnig task does not 
        end when requested, force it to stop ->ok 
        ... .... ....

6) Then form right on task scheduler -> Selected Item 
         i) Enable ( if need)
        ii) run

5) Then Automatic data will sync in the user given time  ( but it will sync to atos only)

6) But for sync checkinout table to sync in attendance system it will use queue jot so for that you should set a corn job first 
   time in cpanel. below is the process.

7) At the end just have to add Corn job for sync checkinout table atos to attendance system:
          
        Then set up the cron job in cPanel:
         Go to cPanel → Cron Jobs
         Add new cron: * * * * * curl -s http://jmagc-atos.bidyapith.com/api-push-queue-processor >> /dev/null 2>&1    


8) But for one time User Info should need to be sync ( and this a queue job, so follow the below steps ):
        i) open its backend site .
       ii) Goto the settings-> table_management.
      iii) Then userinfo -> view data -> then click sync data 
       iv) Then it will create a job
        v) Run from the terminal "php artisan queue:work --tries-1"
       vi) Then it will sync the users one time.

🪜🪜  For User/Client Pc setup : ( How to setup user pc for zktech mdb file upload)
    i) In this system database/php_file_for_upload_mdb_as_sql\zk-sync , folder (zk-sync) is the file
    ii) just copy and pest this "zk-sync" file in "C" drive.
    iii) and Confirm the Php installed and Php location is setup in Environment variable in client pc
          to read this file.

===================================================================================
=================== 🩺 Imporant Debuging Web Routs Form Projects 🩺  =======================
===================================================================================

Route::get('/test-disabled-functions', [\App\Http\Controllers\Backend\AccessUploadController::class, 'testDisabledFunctions'])->name('test.disabled');
Route::get('/test-artisan-command', [\App\Http\Controllers\Backend\AccessUploadController::class, 'testArtisanCommand'])->name('test.artisan');
Route::get('/test-curl-status', [\App\Http\Controllers\Backend\AccessUploadController::class, 'testCurlStatus'])->name('test.curl');
Route::get('/api-push-queue-processor', [\App\Http\Controllers\Backend\AccessUploadController::class, 'processApiQueue'])->name('api.queue.process');


// For Artisan Command Run in Server
Route::get('/log', function () {
    Log::debug('This is a debug log test');
    return 'Log written!';
});
Route::get('/artisan_migrate', function () {
    Artisan::call('migrate', ['--force' => true]);
    return 'php artisan migrate command executed successfully.';
});
Route::get('/artisan_optimize', function () {
    Artisan::call('optimize:clear');
    Artisan::call('optimize');
    Log::debug('This is a optimize test');
    return 'php artisan optimize command executed successfully.';
});
Route::get('/artisan_storage_link', function () {
    Artisan::call('storage:link');
    Log::info(Artisan::output());
    return 'php artisan storage:link command executed successfully.';
});
Route::get('/artisan_sync', function () {
    Artisan::call('access:sync');
    Log::info(Artisan::output());
    return 'php artisan access:sync command executed successfully.';
});

Route::get('/queue_work', function () {
    Artisan::call('queue:work', [
        '--once' => true,
        '--tries' => 1,
    ]);

    return 'Queue worker ran once';
});


===================================================================================
===================         ⏰  All 5 Corn Jobs  ⏰           ====================
===================================================================================

9:15 AM
2:00 PM (14:00)
3:30 PM (15:30)
4:00 PM (16:00) 
5:00 PM (17:00) 


//// commands are ( for server only ) :
15 9  * * *   curl -s http://jmagc-atos.bidyapith.com/api-push-queue-processor >> /dev/null 2>&1
0  14 * * *   curl -s http://jmagc-atos.bidyapith.com/api-push-queue-processor >> /dev/null 2>&1
30 15 * * *   curl -s http://jmagc-atos.bidyapith.com/api-push-queue-processor >> /dev/null 2>&1
0  16 * * *   curl -s http://jmagc-atos.bidyapith.com/api-push-queue-processor >> /dev/null 2>&1
0  17 * * *   curl -s http://jmagc-atos.bidyapith.com/api-push-queue-processor >> /dev/null 2>&1

 