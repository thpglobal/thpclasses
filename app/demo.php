<?php
require("../classes/thp_classes.php"); // Load the classes
$page=new Page;
$page->icon("upload","upload","Import and excel spreadsheet");
$page->icon("download","export","Export data as an excel spreadsheet");
$page->start("Demo thp_classes");
$grid=new Table:
if(isset($_SESSION["contents"])) { $grid->contents=$_SESSION["contents"];}
else{
}
$page->end();
?>
