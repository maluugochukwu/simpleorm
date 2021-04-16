![SimpleORM](https://e7.pngegg.com/pngimages/916/639/png-clipart-node-js-object-relational-mapping-javascript-postgresql-npm-server-blue-angle-thumbnail.png)

**Simple-orm** is an Object Relational Mapper for _php >=7.0.0_.
It gives you the flexibility to write complex SQL queries in one line of code

##Installation
> composer require ugo/simple-orm

##Usage
```php
<?php
require_once __DIR__."/../vendor/autoload.php";
use DbObject\DbObject;

$dbcnx = [YOUR DATABASE CONNECTION STRING];
$object = new DbObject($dbcnx);

// to select 2 records from the table userdata 
$output = $object->doSelect('userdata')->limit('2')->run();
var_dump($output);


// get all records but just the username and password fields from table userdata 
$output = $object->doSelect('userdata',['username','password'])->run();
var_dump($output);
```