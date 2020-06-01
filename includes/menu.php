<?php
// This is used only when the package is used standalone 

/* Sample Menu items */
$menu=array("Home"=>"/",
	"Import"=>"upload",
	"Export"=>"export",
	"Query"=>"query",
	"Cookies"=>"cookies",
			"Test"=>"Cascade Test"
	);
$_SESSION["menu"]=$menu;
