<?php
// Simple query display
require(__DIR__."/../classes/thp_classes.php"); // Load the classes
$page=new Page;
$page->icon("download","/export","Download as excel");
$page->start("Query");
if(!$admin) Die("Not authorized.");
echo("<form><textarea name=query rows=3 cols=80>".$_GET["query"]."</textarea><input type=submit></form>\n");
if( isset($_GET["query"]) ){
	$query=$_GET["query"];
	$start=substr($query,0,4);
	if(in_array($start,array("show","sele","expl"))){
		if(!strpos($query,'limit')) {
			$query .= " limit 1000");
			echo("<p>Note: Query limited to 1,000 rows</p>\n");
		}
		$grid=new Table;
		$grid->start($db);
		$grid->query($query);
		$grid->show();
	}else{
		$affected=$db->exec($query);
		echo("<p>$affected rows affected</p>\n");
	}
}
$page->end();
?>
