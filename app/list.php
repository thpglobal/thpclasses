<?php
// SMART GENERIC LIST - OOP Version - Show the individual records from any report
require_once('../classes/thp_classes.php');
$table=$_SESSION["table"];
$_SESSION["back"]="/list?table=$table";
$page=new Page;
if($can_edit) $page->icon("plus-circle","/edit?table=$table&id=0","Add new record");
$page->start($table);
$grid=new Table;
$grid->start($db);
$grid->smartquery($table,$_SESSION["where"]);
$grid->show("/edit?table={$table}&id=");
$page->end();
?>
