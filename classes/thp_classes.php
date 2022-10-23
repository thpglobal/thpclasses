<?php
// thp_classes contains four basic object classes for formatting pages using the PureCSS library
// Page -- Sends the headers, starts the body, sends the navbar, main title and control icons
// Filters -- Sets up dropdowns that feed into the $_SESSION object
// Table -- Sets up and outputs a 2d table - also backing it up into $_SESSION["contents"];
// Form -- Sets up an editing form with validation
require($_SERVER['DOCUMENT_ROOT']."/includes/thpsecurity.php"); // this version sets up up PDO object and global permission variables
require(__DIR__."/page.php");
require(__DIR__."/filter.php");
require(__DIR__."/form.php");
require(__DIR__."/table.php");
require(__DIR__."/chart.php");
