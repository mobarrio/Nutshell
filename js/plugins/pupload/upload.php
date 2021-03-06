<?php
/**
 * upload.php
 *
 * Copyright 2013, Moxiecode Systems AB
 * Released under GPL License.
 *
 * License: http://www.plupload.com/license
 * Contributing: http://www.plupload.com/contributing
 */

if (session_id() == ''){ session_start(); }


// Make sure file is not cached (as it happens for example on iOS devices)
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


// 5 minutes execution time
@set_time_limit(5 * 60);

// Uncomment this one to fake upload time
// usleep(5000);

$id_galeria       = isset($_REQUEST["id_galeria"]) ? $_REQUEST["id_galeria"] : "generica";
$targetDir        = "../../../gallery/${id_galeria}";
$targetThumbs     = $targetDir . "/thumbs/";
$cleanupTargetDir = true; // Remove old files
$maxFileAge       = 5 * 3600; // Temp file age in seconds

// Create target dir
if (!file_exists($targetDir)) {
	@mkdir($targetDir);
}
if (!file_exists($targetThumbs)) {
	@mkdir($targetThumbs,0777);
}

$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : uniqid("file_");
$filePath = $targetDir . "/" . $fileName;

$chunking = isset($_REQUEST["offset"]) && isset($_REQUEST["total"]);


// Remove old temp files	
if ($cleanupTargetDir) {
	if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
	}

	while (($file = readdir($dir)) !== false) {
		$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

		// If temp file is current file proceed to the next
		if ($tmpfilePath == "{$filePath}.part") {
			continue;
		}

		// Remove temp file if it is older than the max age and is not the current file
		if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
			@unlink($tmpfilePath);
		}
	}
	closedir($dir);
}	


// Open temp file
if (!$out = @fopen("{$filePath}.part", $chunking ? "cb" : "wb")) {
	die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
}

if (!empty($_FILES)) {
	if ($_FILES['file']['error'] || !is_uploaded_file($_FILES['file']['tmp_name'])) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
	}

	// Read binary input stream and append it to temp file
	if (!$in = @fopen($_FILES['file']['tmp_name'], "rb")) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
	}
} else {	
	if (!$in = @fopen("php://input", "rb")) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
	}
}

if ($chunking) {
	fseek($out, $_REQUEST["offset"]); // write at a specific offset
}

while ($buff = fread($in, 4096)) {
	fwrite($out, $buff);
}


@fclose($out);
@fclose($in);

// Check if file has been uploaded
if (!$chunking || filesize("{$filePath}.part") >= $_REQUEST["total"]) {
	// Strip the temp .part suffix off 
	rename("{$filePath}.part", $filePath);
}

createThumb( $fileName, $filePath, $targetThumbs, 100 );
// Return Success JSON-RPC response
die('{"jsonrpc" : "2.0", "result" : null, "id" : "id", "fileInfo": {"targetDir":"'.$targetDir.'", "fileName": "'.$fileName.'", "targetThumbs":"'.$targetThumbs.'", "filePath":"'.$filePath.'"}}');


function createThumb( $fileName, $pathToImage, $pathToThumbs, $thumbWidth ) 
{
  $img = imagecreatefromjpeg( $pathToImage );
  $width = imagesx( $img );
  $height = imagesy( $img );
  $new_width = $thumbWidth;
  $new_height = floor( $height * ( $thumbWidth / $width ) );
  $tmp_img = imagecreatetruecolor( $new_width, $new_height );
  imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
  imagejpeg( $tmp_img, "${pathToThumbs}${fileName}" );
}

function createThumbs( $pathToImages, $pathToThumbs, $thumbWidth ) 
{
  if (!is_dir($pathToThumbs) && !mkdir($pathToThumbs, 0777)) { die('Fallo al crear carpetas...'); } 	
  $dir = opendir( $pathToImages );
  while (false !== ($fname = readdir( $dir ))) {
    $info = pathinfo($pathToImages . $fname);
	
	if(empty($info['extension'])) continue;
    if ( strtolower($info['extension']) == 'jpg' ) 
    {
      $img = imagecreatefromjpeg( "{$pathToImages}{$fname}" );
      $width = imagesx( $img );
      $height = imagesy( $img );
      $new_width = $thumbWidth;
      $new_height = floor( $height * ( $thumbWidth / $width ) );
      $tmp_img = imagecreatetruecolor( $new_width, $new_height );
      imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
      imagejpeg( $tmp_img, "{$pathToThumbs}{$fname}" );
    }
  }
  closedir( $dir );
}
