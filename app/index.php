<?php
require(__DIR__."/../classes/page.php");
$page=new Page;
$page->start("THP Classes Test Platform!");
$driver=$db->getAttribute(PDO::ATTR_DRIVER_NAME);
echo("<p>You are connected to a $driver database.</p>\n");
$page->end();
