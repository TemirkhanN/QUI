# Quintessence framework.


## Requirements
 at least PHP 5.4 and higher
 
 
## Directory structure
1. backend
2. cache
3. config
4. frontend
5. views

## Installation
1. Clone repository
2. Configure database connection and import database_dump.sql from repository root
3. Configure your application's(web-site) document_root to /frontend/


## Basic information
  * Configuration file lay at /config/main.php
  * Configuration file contains base routes that will be loaded automatically(routing system somehow commented)
  * Login page at site.com/login/
  * Admin-panel at site.com/praefect/  (it is empty — only layout for future. I don't know how to architect it correctly)
  * Admin's login and password are equal to "admin" without quotes
  
  
## Used-in third-party
  *  [Bootstrap 3.3.5](https://github.com/twbs/bootstrap)
  *  [Bootpag](https://github.com/botmonster/jquery-bootpag)
  *  [Password_compat](https://github.com/ircmaxell/password_compat)
  *  [Sb-admin2](https://github.com/IronSummitMedia/startbootstrap-sb-admin-2)




As I already said It will be great if you report issues and give advices.
