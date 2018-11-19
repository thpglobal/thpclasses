<?php // Edit one record from old budget tables
require(__DIR__."/../classes/thp_classes.php"); // Load the classes
$table=$_SESSION["table"];
$id=$_SESSION["id"];
if($id=='') $id=0;
$prefix=($id>0 ? "Edit Record $id" : "Create new record");
$page=new Page;
$page->start("$prefix in $table");
if($table=='') Die("No table set.");
$form=new Form;
$form->start($db,"/update");
$form->record($table,$id);
$form->end("Save data");
$page->end();
?>
