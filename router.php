<?php
$path="";
if(isset($_SERVER['PATH_INFO'])) {
	include('app'.$_SERVER['PATH_INFO'].'.php');
}else{
	include('app/hello.php');
}
?>
