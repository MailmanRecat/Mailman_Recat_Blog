<?php
require_once('zhihupaper.class.php');

$id      = getPost('id');

if( !$id ): echo json_encode( array( 'err-msg' => 'lost-id' ) ); exit(); endif;

$zhihu   = new paper;
$content = $zhihu->getContentWithID( $id );
$arr     = array( 'content' => $content, 'err-msg' => '' );
echo json_encode( $arr ); 
?>