<?php
// Default router under php 7.2 on GAE
$path=$_SERVER['REQUEST_URI'];
$path=parse_url($url, PHP_URL_PATH);
if($path<>'/') {
	include('app'.$path.'.php');
}else{
	include('app/index.php');
}
