<?php
// Simple query display
require_once("../thpclasses/classes/thp_classes.php");
$page=new Page;
$page->icon("download","/export","Download as excel");
$page->start("Query");
if(!$admin) Die("Not authorized.");
echo("<form><textarea name=query rows=3 cols=80>".$_GET["query"]."</textarea><input type=submit></form>\n");
if( isset($_GET["query"]) ){
	$query=$_GET["query"];
	$start=substr($query,0,4);
	if(in_array($start,array("show","sele","expl"))){
		$grid=new Table;
		$grid->start($db);
		$grid->query($query);
		$grid->show();
	}else{
		$db->exec($query);
	}
}
$page->end();
?>
