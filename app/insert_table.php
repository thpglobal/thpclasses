<?php
// Move data from the contents array into a table - table name passed as cookie
require_once($_SERVER["DOCUMENT_ROOT"]."/includes/thpsecurity.php");
$table=$_COOKIE["table"];
if($table=='') $table="m";
foreach($_SESSION["contents"] as $row) {
	$query="insert into $table values(";
	foreach($row as $val) {
		$v=(is_numeric($val) ? floor($val) : $val); // only accept integer numbers
		$query .= "'$v',";
	}
	$query=substr($query,0,-1).")";
	echo("<p>$query</p>\n");
	$db->exec($query);
}
echo("<p>Done.</p>\n");
