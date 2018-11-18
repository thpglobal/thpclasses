<?php
$path=$_SERVER['REQUEST_URI'];
if(isset($_SERVER['PATH_INFO'])) {
	include('app'.$path.'.php');
}else{
	include('app/demo.php');
}
?>
