<?php

date_default_timezone_set('PRC');

class db{

	var $copyright = [ 'Author' => 'Mailman_Recat'         ,
					   'Date'   => '2015'                  ,
					   'Email'  => 'MailmanRecat@gmail.com',
					   'Blog'   => 'http://zhihupaper.com' 
				     ];

	protected $db_host      ;
	protected $db_user      ;
	protected $db_passcode  ;
	protected $db_name      ;

	protected $db_link      ;

	var       $last_affected_rows; 

	private   $has_connected = false;			     

    var $tables    = [ 'top_stories' => 'top_stories'   ,
    				       'stories' => 'stories'       ,
    				'before_stories' => 'before_stories',
    				           'lut' => 'lut'           ,
    				          'fuck' => 'fuck'    
                     ];

    public function __construct( $db_host, $db_user, $db_passcode, $db_name ){

    	$this->db_host     = $db_host;
    	$this->db_user     = $db_user;
    	$this->db_passcode = $db_passcode;
    	$this->db_name     = $db_name;

    	$this->db_connect();
    }

    public function check_connection(){
    	if( mysqli_ping( $this->db_link ) ):
    		return true;
    	else:
    		return false;
    	endif;
    }

    public function db_connect(){
    	$db_link = mysqli_connect( $this->db_host, $this->db_user, $this->db_passcode, $this->db_name );
    	if( mysqli_errno( $db_link ) ):
    	else: 
    		$this->db_link       = $db_link;
    		$this->has_connected = true;
    		$this->db_link->set_charset("utf8");
    	endif;
    }

    public function do_query( $res = null ){
    	if( $this->check_connection() ):

    		if( is_null( $res ) ):
    			       $this->db_link->query( $this->query );
    		elseif( $res == true ):
    			return $this->db_link->query( $this->query );
    		endif;

    	endif;		
    }

    public function set_lut( $time ){
    		$this->query   = 'TRUNCATE TABLE '.$this->tables['lut'].';';
    		$this->do_query();
    		$this->query   = 'INSERT INTO '.$this->tables['lut'].' VALUES('.$time.' '.$this->get_time().');';
    		$this->do_query();
    }
    public function get_lut(){
    		$this->query   = 'SELECT * FROM '.$this->tables['lut'].';';
    		$res = $this->do_query( true );
    		if( $res->num_rows == 0 ):
    			       $this->set_lut( time() );
    		    return $this->get_lut(        );
    		else:
    			$time = $res->fetch_array();
    			return $time;
    		endif; 	
    }

    public function get_time(){
		$tomorrow           = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
		return                date('Ymd', $tomorrow);
	}

    public function get_last_affected_rows(){
    	return $this->last_affected_rows;
    }

    public function get_rows( $table ){
    		$this->query   = 'SELECT * FROM '.$this->tables[$table].';';
    		$res  = $this->do_query( true );

    		$rows = $res->num_rows;
    		return $rows;
    }

    public function top_stories_rows(){
    	return $this->get_rows( 'top_stories' );
    }

    public function stories_rows(){
    	return $this->get_rows( 'stories' );
    }

    public function before_stories_rows(){
    	return $this->get_rows( 'before_stories' );
    }

    public function truncate_top_stories(){
    		$this->query = 'TRUNCATE TABLE '.$this->tables['top_stories'].';';
    		$this->do_query();
    }

    public function truncate_stories(){
    		$this->query = 'TRUNCATE TABLE '.$this->tables['stories'].';';
    		$this->do_query();
    }

    public function update( $table, $values ){
    	if( $this->check_connection() ):
    		$str = '';
    		foreach( $values as $key => $value ) {
    			$str .= "{$key}='{$value}',";
    		}

    		$str = rtrim( $str, ',' );
    		$this->query = 'INSERT INTO '.$table.' SET '.$str.';';
    		$this->db_link->query( $this->query );

    		$this->last_affected_rows = $this->db_link->affected_rows;
    	endif;	
    }

    public function update_top_stories( $top_stories ){
    	if( count( $top_stories ) != 10 ): return false; endif;

    	$this->update( $this->tables['top_stories'], $top_stories );
    }

    public function update_stories( $stories ){
    	if( count( $stories ) != 10 ): return false; endif;
    	$this->update( $this->tables['stories'], $stories );
    }

    public function update_before_stories( $stories ){
    	if( count( $stories ) != 10 ): return false; endif;

    	$this->update( $this->tables['before_stories'], $stories );	
    }

    public function format_stories( $res ){
    	$stories = []; 
    		while ( $cache = $res->fetch_array() ){
    			$stories[ count( $stories ) ] =   [

    			            'id' => $cache['id'           ],
    			          'date' => $cache['date'         ],
    			  'display_date' => $cache['display_date' ],
    			         'title' => $cache['title'        ],
    			     'image_url' => $cache['image_url'    ],
    			         'image' => $cache['image'        ],
    			 'thumbnail_url' => $cache['thumbnail_url'],
    			     'thumbnail' => $cache['thumbnail'    ],
    			     'ga_prefix' => $cache['ga_prefix'    ],
    			          'type' => $cache['type'         ]
    													  ];
    		}

    	return $stories;
    }

    public function get_top_stories(){
    		$this->query = 'SELECT * FROM '.$this->tables['top_stories'].';';
    		$res = $this->do_query( true );

    		return $this->format_stories( $res );
    }

    public function get_stories(){
    		$this->query = 'SELECT * FROM '.$this->tables['stories'].';';
    		$res = $this->do_query( true );

    		return $this->format_stories( $res );
    }

    public function get_before_stories( $day ){
    		$this->query = 'SELECT * FROM '.$this->tables['stories'].';';
    		$res = $this->do_query( true );

    		return $this->format_stories( $res );
    }

    public function get_fuck(){
    	if( $this->check_connection() ):
    		$this->query = 'SELECT * FROM '.$this->tables['fuck'].';';
    		$res = $this->db_link->query( $this->query );

    		while($t = $res->fetch_array()){
    			echo $t['id'];
        	};
    	endif;
    }

	public function info(){
		$info = [ 'Author: '.$this->copyright['Author']    , 
				    'Date: '.$this->copyright['Date'  ]    ,
				   'Email: '.$this->copyright['Email' ]    ,
				    'Blog: '.$this->copyright['Blog'  ]    
			    ];
		echo implode(" | ", $info);
	}
	public function getInfo(){
		$info = [ 'Author: '.$this->copyright['Author']    , 
				    'Date: '.$this->copyright['Date'  ]    ,
				   'Email: '.$this->copyright['Email' ]    ,
				    'Blog: '.$this->copyright['Blog'  ]    
			    ];
		return implode(" | ", $info);
	}
}

$db = new db('localhost', 'root', 'caine', 'zhihupaper');

$r = $db->getInfo();

echo $r;
echo '<hr>';

$db->get_fuck();

$db->update_before_stories([
	'id'           => '4597932',
	'date'         => '20150319',
	'display_date' => '20150319',
	'title'        => '到处都在疯狂补贴，背后琢磨的是什么呢',
	'image_url'    => 'http://pic1.zhimg.com/6588234fd83d7b2ae117d9afc815e918.jpg',
	'image'        => 'http://pic1.zhimg.com/6588234fd83d7b2ae117d9afc815e918.jpg',
	'thumbnail_url'=> 'http://pic1.zhimg.com/6588234fd83d7b2ae117d9afc815e918.jpg',
	'thumbnail'    => 'http://pic1.zhimg.com/6588234fd83d7b2ae117d9afc815e918.jpg',
	'ga_prefix'    => '031909',
	'type'         => '0'
	]);


echo '<hr>';
// print_r($db->get_stories());
// $db->truncate_stories();
// $db->truncate_top_stories();

?>