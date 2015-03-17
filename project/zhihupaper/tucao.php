<?php
require_once('zhihupaper.class.php');

$day = getPost('day');
if( $day && $day > 0): exit(); endif;

$zhihu       = new paper;

$content = $zhihu->getContent( $day );
if($day): $day -= 1; else: $day = -1; endif;
$arr     = array( 'day' => $day, 'content' => $content );
echo json_encode( $arr ); 

?>
