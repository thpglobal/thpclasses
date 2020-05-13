<?php
$next_path=$_COOKIE["path"];
if(!$next_path) $next_path="/";
$checkendpoint = "https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=";
$checkurl= $checkendpoint.$_POST['idtoken'];
$v=file_get_contents($checkurl);
$vp=json_decode($v,true);
setcookie("name",$vp["name"]);
setcookie("user",$vp["email"]);
header("Location:".$next_path);
