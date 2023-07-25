<?php

// define('BASE_URL', "https://".$_SERVER['HTTP_HOST']."/");
define('BASE_URL', "/199009242020121006/");
// Get Server Path
define("APP_PATH", realpath(".") );
// define Profile
define('APP_NAME', "199009242020121006");
define('SATKER', "Pengadilan Negeri Bitung");
// define Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', '');
define('DB_PASS', '');
define('DB_NAME', '');

$db_con = new mysqli( DB_HOST, DB_USER, DB_PASS ) or die(0);
// $db_custom = new mysqli( DB_HOST, DB_USER, DB_PASS ) or die(0);

$db_selected = mysqli_select_db( $db_con, DB_NAME );

date_default_timezone_set("Asia/Makassar");
setlocale(LC_TIME, 'INDONESIAN');
// setlocale(LC_TIME, 'id_ID');

?>