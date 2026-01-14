Project setup rules: 
=======================
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
        vii) Add arguments( optional):-> C:\zk-sync\sync_access.php (this php script location , which will be in local pc).
        viii)  Start in ( optional) ;-> C:\wamp64\www\atosql (( this is program app location )).
        ix) Conditions-> Power -> select 1st 2( start task .. on Ac power & stop if the .. battery power)
        x) Settings-> Allow task to be run on demand && if the runnig task does not 
        end when requested, force it to stop ->ok 
        ... .... ....

6) then form right on task scheduler -> Selected Item 
    i) Enable ( if need)
    ii) run 
5) Then Automatic data will sync in the user given time

6) But for one time User Inof should need to be sync ( and this a queue job, so follow the below steps ):
    i) open its backend site .
    ii) Goto the settings-> table_management.
    iii) Then userinfo -> view data -> then click sync data 
     iv) Then it will create a job
     v) Run from the terminal "php artisan queue:work --tries-1"
     vi) Then it will sync the users one time.

### For User/Client Pc setup : ( How to setup user pc for zktech mdb file upload)
    i) In this system database/php_file_for_upload_mdb_as_sql\zk-sync , folder (zk-sync) is the file
    ii) just copy and pest this "zk-sync" file in "C" drive.
    iii) and Confirm the Php installed and Php location is setup in Environment variable in client pc
          to read this file.
 