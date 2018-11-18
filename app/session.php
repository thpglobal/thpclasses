<?php
require_once(__DIR__."/../classes/thp_classes.php");
$page=new Page;
$page->start("Session");
echo("<pre>");
print_r($_SESSION);
echo("</pre>");
$page->end();
?>
