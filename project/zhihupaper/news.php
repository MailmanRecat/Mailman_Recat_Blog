<?php
require_once('zhihupaper.class.php');

$zhihu   = new paper;

$news = $zhihu->getTodayNews();
$arr     = array( 'news' => $news );
echo json_encode( $arr ); 

?>