<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/thpclasses/classes/thp_classes.php");
$page=new Page;
$page->start("Cookies");
echo("<pre>".print_r($_COOKIE,TRUE)."</pre>");
$page->end();
