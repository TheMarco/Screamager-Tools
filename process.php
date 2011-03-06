<?php
error_reporting(E_ALL);
include_once('convert.php');
define ("MAX_SIZE","300"); 
function getExtension($str) {
	$i = strrpos($str,".");
	if (!$i) { return ""; }
	$l = strlen($str) - $i;
	$ext = substr($str,$i+1,$l);
	return $ext;
}
$errors=0;
//checks if the form has been submitted
if(isset($_POST['Submit'])) {

	$image = $_FILES['image']['name'];
	if($image) {
		$filename = stripslashes($_FILES['image']['name']);
		$extension = getExtension($filename);
		$extension = strtolower($extension);
		if($extension != 'gif') {
			die('Image is not a GIF file.');
		}
		$size=filesize($_FILES['image']['tmp_name']);
		if($size > MAX_SIZE*1024) {
			die('Image size exceeds the allowed limit of 300kb.');
		}
		if(exif_imagetype($_FILES['image']['tmp_name']) != 1) {
			die('Image is not a GIF file.');
		}
		$size = getImageSize($_FILES['image']['tmp_name']);
		if(!(($size[0] == 34) && ($size[1] == 18))) {
			die('image is not 34x18 pixels. In fact, it\'s '.$size[0].'x'.$size[1].'.');
		}
		$jsondata = convertImageToJSON($_FILES['image']['tmp_name']);
		echo '<h1>Conversion succesful!</h1>';
		echo '<p>Copy/paste the data below into a file. Host it anywhere you like and enter the URL to the file in the \'<strong>Display remote animation or image</strong>\' section of the Screamager application to play it.</p>';
		echo '<textarea rows="20" cols="60">';
		echo $jsondata;
		echo '</textarea>';
	}
}