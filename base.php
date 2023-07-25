<?php
if (!isset($_SESSION)){
	session_start();
}

error_reporting(E_ALL);
// error_reporting(0);

$uri = $_GET['uri'];
$query = '';
// cek jumlah fragment uri dengan format "controller/action/query"
while ( substr($uri,-1)=='/' || substr($uri,-1)==' ' ){ $uri=substr($uri, 0, -1); }
if( $cek = substr_count($uri, "/") ){
	$controller = explode('/', $uri)[0];
	if ( $cek > 1 ){
        $action = explode('/', substr($uri, strpos($uri,"/")+1) );
	} else {
		$action = array( substr($uri, strpos($uri,"/")+1) );
	}
} else if ($uri) {
	$controller = $uri;
	$action = array("");
} else {
	$controller = '';
	$action = array("");
}

include 'base-db_con.php';
include 'base-function.php';

// $date = strftime( "%A, %d %B %Y %H:%M:%S", time() );
// $date = strftime( "%Y-%m-%d %H:%I:%S <br/> %a, %d %b %Y", time() );
// print strftime("%A, %d %B %Y", strtotime('2023-06-24') );

if ( $controller == "logout" ) {
	session_unset();
	print '<script> window.location = "'.BASE_URL.'login" </script>';
	die;
}

// LANDING PAGE / DASHBOARD / HOME 
if ( file_exists( "secretPhrase___".$controller.".php" ) ){
	include "secretPhrase___".$controller.".php";
} else {
	include "secretPhrase___home.php";
}
?>