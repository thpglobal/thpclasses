# Coding Rules
## Basics
* Tested to date with PHP 5.5 and 7.2
* PDO Class access to databases (used with MySql, Cloud SQL or other databases
## How to use these classes
* **Sub-module:** We include these classes as a [Git sub-module](https://gist.github.com/gitaarik/8735255). Of course, you are free to download / clone / adapt these as you wish, but we prefer you simply extend them.
* **Globals:** The only global objects accessed inside these classes are $_SESSION, $_SERVER, $_GET, $_POST. Some of the special $_SESSION objects are:
  * **menu** - a PHP associative array eg `array("/"=>"Home","/about"=>"About Us","/options"=>array("/1"=>"First","/2"=>"Second"))
  * **debug** - if true, dump something
* **Security:** You should create a file `includes/thpsecurity.php` to contain all the authentication rules, access level, and open the $db PDO database object. I generally define booleans $admin, $can_edit and other access levels for use inside scripts.
## Philosophy
* **HTTPS** - strongly recommend you force this to protect an authenticated $_SESSION object
* **HTML5** - no javascript except for special occasions
* **Styling** via PureCSS and FontAwesome
* **Keep it simple and standard** - always use obvious and standard names and minimize the number of arguments required in functions
* **DRY** - “Don’t repeat yourself” - let objects and generic pages do the work whenever possible (migrating from include files)
HEAVY use of $_SESSION for ¨sticky” settings across pages - no cookies
* **Case matters** -- generally we use lowercase for everything
# Database naming conventions
* Like many frameworks, we recommend standard naming conventions to make things easier. This isn't required, but it helps
* **id** Every table starts with “id” int as a primary key auto_increment
* **name** Most tables then have a unique name varchar field, particularly if you want to use dropdowns 
* **foreign keys** Many-to-one relations use key eg: reporters.country_id=countries.id
* **sticky** Dropdowns usually match a table name to a dropdown filter setting, eg: `where country_id=$_SESSION["countries"]`
* **constraints** We recommend unique index constraints but not foreign key constraints
* **Many-to-many** relations use an intermediary table, eg:
```outputs.id=output_village.outputs_iD, village.id=output_village.Village_ID```





