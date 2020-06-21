<?php // Edit one record from old budget tables
// 2020-06 allow the ability to pass one hidden identifier name to be preset by cookie value
require(__DIR__."/../classes/thp_classes.php"); // Load the classes
$table=$_COOKIE["table"];
$id=$_COOKIE["id"];
$hide=$_COOKIE["hidden"]; // Allow one hidden variable to be passed in the url
if($hide) {
	// is it the name of a dropdown link?
	$n=strpos($hide,"_ID"); 
	$cookie_name= strtolower( ($n ? substr($hide,0,$n-1) : $hide));
	$hidden=array($hide=>$_COOKIE[$cookie_name]);
	if($_COOKIE["debug"]) echo("<p>Hide $hide $hidden</p>\n");
}													
if($id=='') $id=0;
$prefix=($id>0 ? "Edit Record $id" : "Create new record");
$page=new Page;
$page->start("$prefix in $table");
if($table=='') Die("No table set.");
$form=new Form;
$form->start($db,"/update");
if($hide) $form->hidden=$hidden;
$form->record($table,$id);
$form->end("Save data");
$page->end();
