<?php
header("Content-Type:text/html;charset=utf-8");
date_default_timezone_set('PRC');

function getPost($name){ return isset($_POST[$name])? $_POST[$name] : null; }
function get    ($name){ return isset($_GET[$name])? $_GET[$name] : null  ; }

class paper{
	var $url = 'http://news.at.zhihu.com/api/1.2/news/latest';
	var $BS_url_before = 'http://news.at.zhihu.com/api/4/news/before/';
	var $BS_url_content = 'http://news-at.zhihu.com/api/4/news/';

	var $date;
	var $display_date;
	var $is_today;

	var $title;
	var $content_url;

	var $before;
	var $before_url;

	function __construct(){
		$res                = json_decode( file_get_contents( $this->url ) );

		$this->display_date = $res->display_date;

		$newsNumber         = count( $res->news ) - 1;

		$this->title        = $res->news[ $newsNumber ]->title;
		$this->content_url  = $res->news[ $newsNumber ]->url;
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
}

?>