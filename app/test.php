<?php
$json=file_get_contents("https://storage.googleapis.com/thp/stuff.json");
$a=json_decode($json,TRUE);
print_r($a);
