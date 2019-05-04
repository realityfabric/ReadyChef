<?php
include_once("../config/config.php");

/* connectToDatabase
 * @return an instance as returned by pg_connect, or false if failed
 */
function connectToDatabase () {
	global $db;

	$dbhost = $db['host'];
	$dbuser = $db['user'];
	$dbpassword = $db['password'];

	$dbconn = pg_connect("host='$dbhost' user='$dbuser' password='$dbpassword'");

	return $dbconn;
}
?>
