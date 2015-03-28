<?php

define( 'IMAGE_PATH', './image/' );

function get_image( $url, $filename, $path = null ){
	if( is_null($path) ):
		$path = IMAGE_PATH;
	endif;

	$data = file_get_contents( $url );
	$fp   = fopen( IMAGE_PATH.$filename, 'w' );
	if( $fp ):
		fwrite($fp, $data);
		fclose($fp);
	endif;

	return $path.$filename;
}

// $path = get_image( 'http://pic1.zhimg.com/6588234fd83d7b2ae117d9afc815e918.jpg', '0001.jpg' );
?>