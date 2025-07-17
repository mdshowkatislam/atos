Project setup rules: 
1) C:\wamp64\bin\php\php8.2.26\ext 
      check if there the below two .dll file are existed.
        i) pdo_odbc.dll
        ii) php_pdo_odbc.dll 
2) Need php.ini file extensions not commented : 
    
         i)  extension=php_pdo_odbc
         ii) extension=pdo_odbc
         iii) extension=odbc
3) check API_ENDPOINT in .env file 

 