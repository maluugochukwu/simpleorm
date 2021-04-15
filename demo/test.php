<?php
require_once __DIR__."/../vendor/autoload.php";
require_once 'dbcnx.inc.php';
$dbcnx = new dbcnx();

use DbObject\DbObject;

$nn = new DbObject($dbcnx->connect());

$output = $nn->doSelect('userdata')->limit('2')->run();
var_dump($output);