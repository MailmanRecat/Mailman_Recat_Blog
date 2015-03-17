<?php
require_once('zhihupaper.class.php');

$day   = getPost('day');
if( $day > 0 ) exit();

$zhihu = new paper;
$news  = $zhihu->getBeforeNewsWithDay( $day );
$time  = $zhihu->getTime( $day );
$day  -= 1;
$arr   = array( 'day' => $day, 'time' => $time, 'news' => $news, 'err-msg' => '' );
echo json_encode( $arr ); 
?>