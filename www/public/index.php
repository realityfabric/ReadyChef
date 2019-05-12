<?php
include_once("../config/config.php");
include_once("../src/User.php");
session_start();
?>
<!doctype html>
<html>
<head>
	<title>Home</title>
</head>
<?php include("../includes/header.php"); ?>
<p>Welcome to ReadyChef<?php if(isset($_SESSION['user'])) echo ", " . $_SESSION['user']->getUsername(); ?>.</p>
<?php include("../includes/footer.php"); ?>
</html>