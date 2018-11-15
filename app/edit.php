<?php // Edit one record from old budget tables - called from list1
require_once("../classes/thp_classes.php");
$table=$_SESSION["table"];
$id=$_SESSION["id"];
if($id=='') $id=0;
$prefix=($id>0 ? "Edit Record $id" : "Create new record");
$page=new Page;
$page->start("$prefix in $table");
$form=new Form;
$form->start($db,"/update");
$form->record($table,$id);
$form->end("Save data");
$page->end();
?>
