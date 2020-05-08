<?php
// Default router under php 7.2 on GAE
$path=$_SERVER['REQUEST_URI'];
$q=strpos($path,"?"); // strip off GET string if needed
if($q) $path=substr($path,0,$q);
if($path<>'/') {
	include('app'.$path.'.php');
}else{
	include('app/index.php');
}
?>
