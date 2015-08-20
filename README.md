# Tuksi4
## Installation
Clone repository
```sh
$ git clone git@github.com:dwarfhq/tuksi4.git
```
Create writable folders
```sh
$ mkdir media uploads download templates/compiled shellscripts/newsletter/spool shellscripts/newsletter/spool/single
$ chmod -R 777 media uploads download templates/compiled shellscripts/newsletter/spool shellscripts/newsletter/spool/single
```
Setup database
```sh
$ mysql YOURDB < sql.tuksi4.sql
```
## Site setup
* Edit configuration/tuksi_db.ini with your favorite editor and change the credentials
* Edit the database table 'cmssitesetup' and setup you URL to allow logon til TUKSI (/tuksi)
* Setup apache conf to folder and with alias  "preview." + URL
* View site at http://preview.URL
* Login to the admin interface at http://URL/tuksi with these credentials: admin / ChangeMe


We apologize that a lot of documentation and content is in Danish.
