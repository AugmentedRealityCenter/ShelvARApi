ShelvAR API
===========

Web API and documentation for ShelvAR

Installation
------------

1. Copy files to your server.
2. Create the database you will use for ShelvAR, then import setup/shelvar.sql to create the necessary tables. We use phpMyAdmin for this.
3. Edit db_info.php with the login and password for your database.
4. Modify the .htaccess file and institutions.php to reflect settings for your site. Documentation on how to do this is TODO

Git Branches
------------
The master branch runs on api.shelvar.com, and the dev branch runs on devapi.shelvar.com. 
dev (and devapi) is unstanble, and should only be used by ShelvAR developers. If you are building front-end code that uses shelvar, use master (and api).
