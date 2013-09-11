<?php

function close($code, $message, $dump = '') {
	header($_SERVER['SERVER_PROTOCOL'].' '.$code.' '.$message, true, $code);

	exit($dump);
}

// get URI
$URI = substr($_SERVER['DOCUMENT_ROOT'].(
	$_SERVER['QUERY_STRING'] ? substr($_SERVER['REQUEST_URI'], 0, - 1 - strlen($_SERVER['QUERY_STRING'])) : $_SERVER['REQUEST_URI']
), strlen(dirname(__FILE__)) + 1);

// set default URI density
$URI_DENSITY = 1;

// get client hints (with fallbacks)
parse_str(str_replace(',', '&', 'do=0&dpr=1&dw=1024&dh=768&'.@$_SERVER['HTTP_CH']), $HINT);

// get image info
$INFO = pathinfo($URI);

// exit if image info is empty
if (empty($INFO['basename'])) close(204, 'No Content');

// check for differing density images
for ($TEMP_DENSITY = 1, $TEMP_URI; $TEMP_DENSITY <= 10; $TEMP_DENSITY = $TEMP_DENSITY + .5) {
	$TEMP_URI = $INFO['dirname'].'/'.$INFO['filename'].'.'.$TEMP_DENSITY.'x.'.$INFO['extension'];

	// if a density image is found
	if (file_exists($TEMP_URI)) {
		// update the URI
		$URI = (string) $TEMP_URI;
		$URI_DENSITY = (string) $TEMP_DENSITY;

		break;
	}
}

// exit if URI is not a file
if (!file_exists($URI)) close(404, 'Not Found');

// get modified headers
$HEAD_LAST_MODIFIED = @$_SERVER['HTTP_IF_MODIFIED_SINCE'];
$FILE_LAST_MODIFIED = gmdate('D, d M Y H:i:s T', filemtime($URI));

// set cache headers
header('Cache-Control: public');
header('Last-Modified: '.$FILE_LAST_MODIFIED);

// exit if cache is up to date
if ($FILE_LAST_MODIFIED == $HEAD_LAST_MODIFIED) {
	close(304, 'Not Modified');
}

// set content type as image/jpeg
header('Content-Type: image/jpeg');

// get image data
list($URI_WIDTH, $URI_HEIGHT) = getimagesize($URI);

// get image ratio
$IMAGE_DENSITY = (string) min($HINT['dpr'] / $URI_DENSITY, 1);

// exit as original image if image density is 1
if ($IMAGE_DENSITY === '1') close(200, 'OK', file_get_contents($URI));

// set new image size
$IMAGE_WIDTH  = $URI_WIDTH  * $IMAGE_DENSITY;
$IMAGE_HEIGHT = $URI_HEIGHT * $IMAGE_DENSITY;

// set new image
$IMAGE = imagecreatetruecolor($IMAGE_WIDTH, $IMAGE_HEIGHT);

// copy original image
imagecopyresampled($IMAGE, imagecreatefromjpeg($URI), 0, 0, 0, 0, $IMAGE_WIDTH, $IMAGE_HEIGHT, $URI_WIDTH, $URI_HEIGHT);

// set new image as progressive
imageinterlace($IMAGE, true);

// output new image
imagejpeg($IMAGE, null, 85);