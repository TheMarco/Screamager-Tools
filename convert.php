<?php
function frameToJSON($image) {

	$colorcodes = Array(
		"0000" => 0,
		"2552552550" => 0,
		"255000" => 10,
		"25519800" => 11,
		"24025500" => 12,
		"6025500" => 4,
		"0542550" => 16,
		"16802550" => 8,
		"25502220" => 1,
		"2032032030" => 14,
		"166000" => 3,
		"16612800" => 2,
		"15616600" => 13,
		"3916600" => 15,
		"0351660" => 7,
		10801660 => 5,
		16601450 => 9,
		1321321320 => 6);

	$im = imagecreatefromgif($image);
	$out = "[\n";
	for($i=0;$i<18;$i++) {
		$out = $out.'[';
		for($j=0;$j<34;$j++) {
			$rgb = imagecolorat($im, $j, $i);
			$colors = imagecolorsforindex($im, $rgb);
			$out = $out.$colorcodes[implode('',$colors)];

			if($j !== 33) {
				$out = $out.",";
			}
			else {
				if($i !== 17) {
					$out = $out.'],'."\n";
				}
				else {
					$out = $out.']';
				}
			}
		}
	}
	$out = $out."\n]";
	return $out;
}

function convertImageToJSON($image) {

	$framesdir = 'blerk'.time().rand(1, 9999);
	mkdir('/tmp/'.$framesdir);
	$framesdir = '/tmp/'.$framesdir.'/';
	exec('convert '.$image.' -coalesce '.$framesdir.'anim_%d.gif');
	$handle = opendir($framesdir);
	function cmp($a, $b) {
		$ac = str_replace('anim_', '', $a);
		$bc = str_replace('anim_', '', $b);
		$ac = str_replace('.gif', '', $ac);
		$bc = str_replace('.gif', '', $bc);
		return(intval($ac) < intval($bc)) ? -1 : 1;
	}
	$fileNames = Array();
	while (false !== ($file = readdir($handle))) {
		if ($file != "." && $file != "..") {
			array_push($fileNames, $file);
		}
	}
	usort($fileNames, 'cmp');
	$out = '{"animation" : ['."\n";
	for($i=0;$i<sizeof($fileNames);$i++) {
		$out = $out . frameToJSON($framesdir.$fileNames[$i]);
		if($i === (sizeof($fileNames) - 1)) {
			$out = $out . "\n".']}';
		}
		else {
			$out = $out . ','."\n";
		}
	}
	for($i=0;$i<sizeof($fileNames);$i++) {
		unlink($framesdir.$fileNames[$i]); 
	}
	rmdir($framesdir);
	return $out;
}
?>
