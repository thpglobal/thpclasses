<?php 
require $_SERVER["DOCUMENT_ROOT"].'/vendor/autoload.php';
use Google\Cloud\Storage\StorageClient; 
Class CloudStorage extends StorageClient {
	function __construct() {
		parent::__construct();
		parent::registerStreamWrapper();
	}
	public function show($fullpath){
	}
	public function upload($source,$bucketName,$objectName){
		$n=filesize($source);
		Die(LOG_INFO,$source." size $n");
	    $file = fopen($source, 'r');
    	$bucket = parent::bucket($bucketName);
	    $object = $bucket->upload($file,['name' => $objectName]);
	}
	public function download($fullpath){
		$ext=strtolower(substr($fullpath,-4));
		$ct='binary/octet-stream'; // default for unknown type (downloads)
		$mode='attachment';
		$name=basename($fullpath);
		// Figure out the content type, as uploads set this incorrectly
		$cts=array(".pdf"=>"application/pdf","jpeg"=>"image/jpeg",".jpg"=>"image/jpg");
		$cts["xlsx"]="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
		$cts["docx"]="application/vnd.openxmlformats-officedocument.wordprocessingml.document";
		$cts["pptx"]="application/vnd.openxmlformats-officedocument.presentationml.presentation";
		$cts[".doc"]="application/msword";
		if(array_key_exists($ext,$cts)){ $ct=$cts[$ext]; $mode='inline'; }
		header("Content-Type:".$ct);
		header("Content-Disposition:$mode; filename=$name");
		echo readfile($fullpath);
	}
}
