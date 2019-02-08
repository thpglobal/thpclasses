<?php
$json=file_get_contents("gs://thp/stuff.json");
$a=json_decode($json,TRUE);
print_r($a);
