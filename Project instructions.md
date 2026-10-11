==========================================================================
🎯  ATOS → ATTENDANCE SYSTEM : SETUP GUIDE
==========================================================================

📌 Flow of data:
   1) Windows Task Scheduler (client PC) uploads the .mdb data to ATOS every minute or dalily as per setup.
   2) A queue job pushes the checkinout table from ATOS to the attendance system.
   3) A cPanel cron job keeps that queue processor running.


==========================================================================
🖥️  PART 1 : SERVER / DEVELOPER PC SETUP
==========================================================================

📂 1.1  Check the PHP ODBC extension files
   Folder: C:\wamp64\bin\php\php8.2.26\ext
   Confirm both files exist:
      ✅ pdo_odbc.dll
      ✅ php_pdo_odbc.dll

⚙️ 1.2  Configure php.ini
   ⚠️ Be very careful: enable ONLY the first two lines. Do NOT enable all the ODBC entries.

      ✅ extension=odbc
      ✅ extension=pdo_odbc
      ❌ ;extension=php_odbc.dll
      ❌ ;extension=php_pdo_odbc.dll

   (First two = active, no semicolon.  Last two = stay commented, keep the semicolon.)

🔑 1.3  Check the .env file
   Make sure API_ENDPOINT is set correctly.


==========================================================================
⏱️  PART 2 : WINDOWS TASK SCHEDULER  (MDB upload to ATOS)
==========================================================================

📝 2.1  Create the task
   ▸ Create a new task, then give it a name and a description.

📅 2.2  Triggers tab
   ▸ Click "New".
   ▸ Begin the task: On a schedule  →  Daily.
   ▸ Advanced settings: tick "Repeat task every" → 1 minute.
     (Don't worry, this is only for the .mdb file upload.)
   ▸ For a duration of: Indefinitely.
   ▸ Tick "Enabled", then click OK.

🚀 2.3  Actions tab
   ▸ Click "New". Action = "Start a program".
   ▸ Program/script   : php   (or browse to your PHP location)
   ▸ Add arguments    : C:\zk-sync\sync_access.php   (script location on the local PC)
   ▸ Start in         : C:\wamp64\www\atosql         (application location)

🔋 2.4  Conditions tab  →  Power
   ▸ Tick the first two options:
      ☑ Start the task only if the computer is on AC power
      ☑ Stop if the computer switches to battery power

🛠️ 2.5  Settings tab
   ▸ ☑ Allow task to be run on demand
   ▸ ☑ If the running task does not end when requested, force it to stop
   ▸ Click OK.

▶️ 2.6  Run the task
   ▸ In Task Scheduler, open the right panel → "Selected Item".
   ▸ Enable the task (if needed).
   ▸ Click Run.

   ✅ Data will now sync automatically, but ONLY to ATOS.


==========================================================================
🔄  PART 3 : SYNC checkinout TABLE TO THE ATTENDANCE SYSTEM
==========================================================================

📌 This sync uses a QUEUE JOB, so a cron job must be set up in cPanel.
   Go to: cPanel → Cron Jobs → Add new cron job.
   Use ONE of the two options below.

🔁 Option A : Run every minute

   * * * * * curl -s http://jmagc-atos.bidyapith.com/api-push-queue-processor >> /dev/null 2>&1

🕘 Option B : Run at 5 fixed times a day  (server only)

   ⏰ 9:15 AM  (09:15)
   15 9  * * * curl -s http://jmagc-atos.bidyapith.com/api-push-queue-processor >> /dev/null 2>&1

   ⏰ 2:00 PM  (14:00)
   0 14 * * * curl -s http://jmagc-atos.bidyapith.com/api-push-queue-processor >> /dev/null 2>&1

   ⏰ 3:30 PM  (15:30)
   30 15 * * * curl -s http://jmagc-atos.bidyapith.com/api-push-queue-processor >> /dev/null 2>&1

   ⏰ 4:00 PM  (16:00)
   0 16 * * * curl -s http://jmagc-atos.bidyapith.com/api-push-queue-processor >> /dev/null 2>&1

   ⏰ 5:00 PM  (17:00)
   0 17 * * * curl -s http://jmagc-atos.bidyapith.com/api-push-queue-processor >> /dev/null 2>&1


==========================================================================
👤  PART 4 : ONE-TIME USER INFO SYNC  (also a queue job)
==========================================================================

   1️⃣  Open the backend site.
   2️⃣  Go to: Settings → table_management.
   3️⃣  Find "userinfo" → View Data → click "Sync Data". This creates a job.
   4️⃣  Run this in the terminal:

          php artisan queue:work --tries=1

   5️⃣  The users will now sync one time. ✅


==========================================================================
💻  PART 5 : CLIENT PC SETUP  (ZKTeco MDB file upload)
==========================================================================

   1️⃣  Find the "zk-sync" folder in this project:
          database/php_file_for_upload_mdb_as_sql/zk-sync

   2️⃣  Copy the "zk-sync" folder to the C: drive of the client PC:
          C:\zk-sync

   3️⃣  Confirm PHP is installed on the client PC and its location is added
       to the PATH environment variable, so the script can run.


==========================================================================
🩺  PART 6 : CLIENT PC SETUP   (Windows ODBC 64bit installation and checkup)
==========================================================================
1️⃣  Open cmd in client pc , then run C:\Windows\System32\odbcad32.exe , this command .
     after openning the odbc installed window then click on drive , check if there have adjectly 
     Microsoft Access Driver (*.mdb, *.accdb) , in not that means odbc 64 bit driver not installed.

2️⃣ install odbc : from (https://www.microsoft.com/en-us/download/details.aspx?id=54920) this site but 64 bit
     Before installation remove any previous install ms office 2007 or older because they carry odbc 32 bit.
3️⃣ after finishing odbc 64 bit installation check again the Number-1 instruction , if the Microsoft Access Driver (*.mdb, *.accdb) is exist in driver.
==========================================================================
🩺  PART 7 : DEBUGGING ROUTES  (add to routes/web.php)
==========================================================================

🔍 7.1  Diagnostics

   Route::get('/test-disabled-functions', [\App\Http\Controllers\Backend\AccessUploadController::class, 'testDisabledFunctions'])->name('test.disabled');
   Route::get('/test-artisan-command', [\App\Http\Controllers\Backend\AccessUploadController::class, 'testArtisanCommand'])->name('test.artisan');
   Route::get('/test-curl-status', [\App\Http\Controllers\Backend\AccessUploadController::class, 'testCurlStatus'])->name('test.curl');
   Route::get('/api-push-queue-processor', [\App\Http\Controllers\Backend\AccessUploadController::class, 'processApiQueue'])->name('api.queue.process');

🧰 7.2  Run Artisan commands on the server

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
       Log::debug('This is an optimize test');
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

🚨 SECURITY WARNING
   These routes run migrations and other commands WITHOUT authentication.
   Anyone who knows the URL can call them. Protect them with auth middleware
   or an IP restriction, or remove them once debugging is finished.

==========================================================================
🏁  END OF GUIDE
==========================================================================
