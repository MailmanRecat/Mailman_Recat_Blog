<?php
header("Content-Type:text/html;charset=utf-8");
date_default_timezone_set('PRC');

require_once('zhihupaper-db.class.php');

define( 'IMAGE_PATH', './image/'     );
define( 'UPDATE_INTERVAL_TIME', 3600 );
define( 'ON'        , true           );
define( 'OFF'       , false          );

function getPost($name){ return isset($_POST[$name])? $_POST[$name] : null; }
function get    ($name){ return isset($_GET[$name])? $_GET[$name] : null  ; }

class paper{

	var $url            = 'http://news.at.zhihu.com/api/1.2/news/latest';
	var $BS_url_before  = 'http://news.at.zhihu.com/api/4/news/before/';
	var $BS_url_content = 'http://news-at.zhihu.com/api/4/news/';

	var $db;

	var $last_update_time;
	var $last_update_date;
	var $top_stories_rows;
	var $stories_rows;

	var $update_lock = ON;

	var $date;
	var $display_date;
	var $is_today;

	var $title;
	var $content_url;

	var $before;
	var $before_url;

	function __construct(){

		$this->db           = new db('localhost', 'root', 'caine', 'zhihupaper');

		if( $this->db ):

			$this->top_stories_rows       = $this->db->top_stories_rows();
			$this->stories_rows           = $this->db->stories_rows();

			$time                         = $this->db->get_lut();
			$this->last_update_time       = $time['time'];
			$this->last_update_date       = $time['date']; 

			if( $this->top_stories_rows == 0 || $this->stories_rows == 0):
				$this->update_lock = OFF;
				$this->update();
			else:
				$this->check_update();	
			endif;
				
		endif;
	}

	function demo(){
		$this->db->get_fuck();
		echo $this->db->top_stories_rows();
	}

	function check_update(){
		if( $this->last_update_date != $this->get_time() ):
			$this->update_lock = OFF;
			$this->archive();
			$this->update();
			return false;
		endif;

		if( $this->last_update_time + 10 > time() ):
			$this->update_lock = OFF;
			$this->update();
		endif;
	}

	function archive(){
		echo 'archive';
	}

	function update(){
		if( $this->update_lock == OFF ):
			if( $this->top_stories_rows > 0 ):
				$old_top_stories = $this->db->get_top_stories();
			endif;
			if( $this->stories_rows > 0 ):
				$old_stories     = $this->db->get_stories();
			endif;

			$res                = json_decode( file_get_contents( $this->url ) );
			$date               = $res->date;
			$this->display_date = $res->display_date;
			$top_Lenght         = count( $res->top_stories );
			$news_Lenght     = count( $res->news     );
			for( $t = 0; $t < $top_Lenght; $t++ ){
				$image_name = $date.$t.'top.jpg';

				$id        = $res->top_stories[$t]->id;

				if( $proof = $old_top_stories[$t]['id'] ):
					if( in_array($id, $proof) ):
						continue;
					endif;
				endif;	

				$title         = $res->top_stories[$t]->title;
				$image         = $res->top_stories[$t]->image;
				$image_url     = $this->download_image( $image, $image_name );
				$thumbnail     = 'unkown';
				$thumbnail_url = 'unkown';
				$ga_prefix     = $res->top_stories[$t]->ga_prefix;
				$type          = '0';
				$this->db->update_top_stories([
					'id'            => $id,
					'date'          => $date,
					'display_date'  => $this->display_date,
					'title'         => $title,
					'image_url'     => $image_url,
					'image'         => $image,
					'thumbnail_url' => $thumbnail_url,
					'thumbnail'     => $thumbnail,
					'ga_prefix'     => $ga_prefix,
					'type'          => $type
					                          ]);
			}

			for( $n = 0; $n < $news_Lenght; $n++ ){
				$image_name = $date.$n.'top.jpg';

				$id            = $res->news[$n]->id;

				if( $proof = $old_stories[$n]['id'] ):
					if( in_array($id, $proof) ):
						continue;
					endif;
				endif;	

				$title         = $res->news[$n]->title;
				$image         = $res->news[$n]->image;
				$image_url     = $this->download_image( $image, $image_name );
				$thumbnail     = 'unkown';
				$thumbnail_url = 'unkown';
				$ga_prefix     = $res->news[$n]->ga_prefix;
				$type          = '0';
				$this->db->update_stories([
					'id'            => $id,
					'date'          => $date,
					'display_date'  => $this->display_date,
					'title'         => $title,
					'image_url'     => $image_url,
					'image'         => $image,
					'thumbnail_url' => $thumbnail_url,
					'thumbnail'     => $thumbnail,
					'ga_prefix'     => $ga_prefix,
					'type'          => $type
					                          ]);
			}

			$this->update_lock = ON;

		endif;		
	}

	function getBefore( $day ){
		$tomorrow           = mktime(0, 0, 0, date("m")  , date("d") + $day , date("Y"));
		$t                  = date('Ymd', $tomorrow);
		$this->display_date = date('Y.m.d l', $tomorrow);
		$url                = $this->BS_url_before.$t;
		$res                = json_decode( file_get_contents( $url ) );
		$newsNumber         = count( $res->stories ) - 1;
		$id                 = $res->stories[ $newsNumber ]->id;
		return                $this->BS_url_content.$id;
	}
	function getBeforeURl( $day ){
		$tomorrow           = mktime(0, 0, 0, date("m")  , date("d") + $day , date("Y"));
		$t                  = date('Ymd', $tomorrow);
		$url                = $this->BS_url_before.$t;
		return                $url;
	}
	function getTime( $day ){
		$tomorrow           = mktime(0, 0, 0, date("m")  , date("d") + $day , date("Y"));
		return                date('Y.m.d l', $tomorrow);
	}
	function get_time(){
		$time           = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
		return            date('Ymd');
	}

	function getContent( $day = null ){
		if($day):
			$url          = $this->getBefore( $day );
			$content_json = json_decode( file_get_contents( $url ) );
		else:
			$content_json = json_decode( file_get_contents( $this->content_url ) );
		endif;

		$content = $content_json->body;
		$content = str_ireplace('<div class="main-wrap content-wrap">', '', $content);
		$content = str_ireplace('<div class="headline">',               '', $content);
		$content = str_ireplace('<div class="img-place-holder">',       '', $content);
		$content = str_ireplace('<div class="content-inner">',          '', $content);

		$c       = explode('</div>', $content, 2);
		$content = $c[1];
		$c       = explode('</div>', $content, 2);
		$content = $c[1];

		$content = '<div><div><div class="time">'.$this->display_date.'</div>'.$content.'<div class="comment-btn"><button>查看评论</button></div>';
		return $content;
	}
	function getTodayNews(){
		$news                = json_decode( file_get_contents( $this->url ) );
		return $news;
	}
	function getContentWithID( $id ){
		$url    = $this->BS_url_content.$id;
		$res    = json_decode( file_get_contents( $url ) );

		$res = $res->body;
		$res = str_ireplace('<div class="main-wrap content-wrap">', '<div>', $res);

		return $res;
	}
	function getBeforeNewsWithDay( $day ){
		$url       = $this->getBeforeURl( $day );
		$news      = json_decode( file_get_contents( $url ) );
		return       $news;
	}
	function download_image( $url, $filename, $path = null ){
		if( is_null($path) ):
			$path = IMAGE_PATH;
		endif;

		// $data = file_get_contents( $url );
		// $fp   = fopen( IMAGE_PATH.$filename, 'w' );
		// if( $fp ):
		// 	fwrite($fp, $data);
		// 	fclose($fp);
		// else:
		// 	return '/image/default.png';	
		// endif;

		return $path.$filename;
	}
}

$fuck = new paper;
echo 'done';

?>
