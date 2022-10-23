# thpclasses
PHP classes designed for monitoring and evaluation data by The Hunger Project but is made public for anyone to easily and quickly establish database apps, on any platform but especially on Google App Engine. It's big advantage is the clever way it uses $_SESSSION variables to make dropdown filter selections sticky.

## Latest refactoring - May 2020
Except for the passing of query results ($contents), all use of $_SESSION has been replaced by $_COOKIE. This is to eliminate premature time outs. It complicates things, as all changes to $_COOKIE must happen with two calls BEFORE the Page::Start: $_COOKIE[$key)=$val and setcookie($key,val);

## Running with just default scripts

To create the *simplest possible* GAE PHP 7.2 app that uses this package as a sub-module, create these folders and files which will simply run the family of build in pages for basic database CRUD functionality.

### /includes/thpsecurity

```php
// Any code that is special to your app, like connecting to a database or setting variables used throughout
$today=date("Y-m-d");
$user=$_SESSION["user"]; // set in the built in login scripts
if($user doesn't meet YOUR logical condition) Die("Not authorized, sorry"); 
$admin=if($user meets YOUR logical condition);
$can_edit=if($user meets YOUR logical condition for write permission);
$db = new PDO("mysql:unix_socket=/cloudsql/YOUR_PROJ:YOUR_REGION:YOUR_INSTANCE","YOUR_USER","YOUR_PWD")
```

### /includes/menu.php
```php
include("../thpclasses/includes/menu.php"); // copy the demo menu from the classes
```

### /app.yaml
```
runtime: php73
entrypoint: thpclasses/app/index.php
handlers:

- url: /static
  static_dir: static

- url: /favicon.ico
  static_files: static/tst.png
  upload: static/tst.png

- url: .*
  script: auto
  secure: always
```

## To automatically create dropdown menus linking to your scripts

* change includes/menu.php to include("../thpclasses/includes/auto.php");
* create folders like /app/apples /app/berries
* create scripts like /app/apples/bartlet.php, /app/apples/green.php, /app/berries/blue.php, /app/berries/goose.php

When deployed, you'll have a runnable app that looks like this:

