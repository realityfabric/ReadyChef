<?php
include_once("../config/config.php");
include_once("../src/DBConnect.php");
include_once("../src/User.php");
session_start();
?>
<!doctype html>
<html>
<head>
	<title>Register</title>
</head>
<?php include("../includes/header.php"); ?>
<?php
if(isset($_POST['submit'])) {
	$username = $_POST['username'];
	$password = $_POST['password'];
	$confirm = $_POST['confirm'];

	$error_count = 0;
	$is_registered = false;

	if ($username == "") {
		$error_count++;
		echo "<p>Username field cannot be blank.</p>";
	}

	if ($password == "") {
		$error_count++;
		echo "<p>Password field cannot be blank.</p>";
	}

	if ($password != $confirm) {
		$error_count++;
		echo "<p>Passwords don't match.</p>";
	}

	if ($error_count == 0) {
		if (!(User::register($username, $password))) {
			$error_count++;
			echo "<p>Registration failed. Try a different username.</p>"; // TODO: better failure reporting
		} else {
			echo "<p>Thank you for registering with ReadyChef, {$username}.</p>";
			$user = User::login($username, $password);
			$_SESSION['user'] = $user;

			$is_registered = true;
		}
	}
} else if(isset($_SESSION['user'])) {
	echo "<p>You are logged in as {$_SESSION['user']->getUsername()}. If this is not you, please <a href='logout.php'>log out</a>.</p>";

	$is_registered = true;
}

if (!$is_registered) {
?>
	<h1>Register with ReadyChef!</h1>
	<!-- TODO: implement SSL -->
	<h1>WARNING: THIS PAGE IS NOT SECURED WITH SSL</h1>
	<h1>WARNING: THIS IS FOR TESTING PURPOSES ONLY</h1>

	<form action='register.php' method='post'>
		<p>username: <input type='text' name='username' /></p>
		<p>password: <input type='password' name='password' /></p>
		<p>confirm password: <input type='password' name='confirm' /></p>
		<p><input type='submit' name='submit' value='login' /></p>
	</form>
<?php
}
?>
<?php include("../includes/footer.php"); ?>
</html>