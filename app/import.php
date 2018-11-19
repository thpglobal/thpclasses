<?php
// Import an xlsx file into $contents based on ZipArchive
session_start();
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL & ~E_NOTICE);
$into=$_SESSION["into"];
if($into=="") $into="/dump";
$tmpname=$_FILES['userfile']['tmp_name'];
debug("Files:",$_FILES);
$name=$_FILES['userfile']['name'];
$dir=sys_get_temp_dir();
$dest=$dir.'/'.$name;
debug("Temp:",$tmpname);
debug("Dest:",$dest);
rename($tmpname,$dest); // Move the file from uploads to the regular temp area.
// extract everything to the temporary file system
$zip = new ZipArchive;
$zip->openFile($dest)
->extractTo($dir);
debug("Unzipped xl folder",scandir($dir.'/xl/'));
// Open up shared strings & the first worksheet
$strings=array();
if(file_exists($dir . '/xl/sharedStrings.xml')) {
	$sxml=file_get_contents($dir . '/xl/sharedStrings.xml');
	$strings = simplexml_load_string($sxml);
}
debug("Shared strings:",$strings);
$xml=file_get_contents($dir . '/xl/worksheets/sheet1.xml');
debug("Sheet as XML",htmlentities($xml));
$sheet   = simplexml_load_string($xml);
// Parse the rows into the $contents array
$xlrows = $sheet->sheetData->row; // this is an iterative object
foreach ($xlrows as $xlrow) {
	$line=array(); // clear out the array
    foreach ($xlrow->c as $cell) {
		$t=(string)$cell['t'];
		$v=(string)$cell->v;
		$si = $strings->si[(int) $v];
		if($t=="s") $v=(string)$si->t;
		if($t=="inlineStr") $v=(string)$cell->is->t;
		$line[]=$v;
	}
	debug("Line",$line);
	$contents[]=$line;
}
$_SESSION["contents"]=$contents;
$_SESSION["reply"]="Success importing $nrows from $name";
if(!$_SESSION["debug"]) header("Location:".$into);
echo("<p><a href=$into>Click here for $into</a></p>");
?>
