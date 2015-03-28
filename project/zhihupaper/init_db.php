<?php

$topstories_table_name    = 'top_stories'      ;
$stories_table_name       = 'stories'          ;
$beforestories_table_name = 'before_stories'   ;

$last_update_time         = 'lut'              ;

$init_topstories = 'CREATE TABLE IF NOT EXISTS '.$topstories_table_name.'(
	id            INT UNSIGNED,
	date          INT UNSIGNED,
	display_date  VARCHAR(80 ),
	title         VARCHAR(420),
	image_url     VARCHAR(300),
	image         VARCHAR(300),
	thumbnail_url VARCHAR(300),
	thumbnail     VARCHAR(300),
	ga_prefix     INT UNSIGNED,
	type          TINYINT     
	) DEFAULT     CHARSET=UTF8;';

$init_stories    = 'CREATE TABLE IF NOT EXISTS '.$stories_table_name.'(
	id            INT UNSIGNED,
	date          INT UNSIGNED,
	display_date  VARCHAR(80 ),
	title         VARCHAR(420),
	image_url     VARCHAR(300),
	image         VARCHAR(300),
	thumbnail_url VARCHAR(300),
	thumbnail     VARCHAR(300),
	ga_prefix     INT UNSIGNED,
	type          TINYINT     
	) DEFAULT     CHARSET=UTF8;';

$init_before_stories  = 'CREATE TABLE IF NOT EXISTS '.$beforestories_table_name.'(
	id            INT UNSIGNED,
	date          INT UNSIGNED,
	display_date  VARCHAR(80 ),
	title         VARCHAR(420),
	image_url     VARCHAR(300),
	image         VARCHAR(300),
	thumbnail_url VARCHAR(300),
	thumbnail     VARCHAR(300),
	ga_prefix     INT UNSIGNED,
	type          TINYINT     
	) DEFAULT     CHARSET=UTF8;';

$init_lut = 'CREATE TABLE IF NOT EXISTS '.$lut.'(
	time          INT UNSIGNED,
	date          INT UNSIGNED
	);';

$link = new mysqli('localhost', 'root' , 'caine', 'zhihupaper');

$link->query($init_topstories)    ;
$link->query($init_stories)       ;
$link->query($init_before_stories);

echo 'done';


?>