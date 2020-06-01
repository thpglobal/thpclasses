<?php
// Default router under php 7+
session_start();
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL & ~E_NOTICE);
$root=$_SERVER["DOCUMENT_ROOT"];
$path=$_SERVER['REQUEST_URI'];
$path=parse_url($url, PHP_URL_PATH);
die($path);
if($path<>'/') {
	include($root.'app'.$path.'.php');
}else{
	include('app/index.php');
}
