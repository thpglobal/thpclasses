<?php
// Test app will go here
require_once("../classes/thp_classes.php");
$page=new Page;
$page->start("Hello World!");
$form=new Form;
$form->start($db);
$form->record("demo",0);
$form->end();
$grid->new Table;
$grid->start($db);
$grid->query("select * from demo");
$grid->show();
$page->end();
?>
