<?php
include_once("../config/config.php");
include_once("../src/DBConnect.php");
include_once("../src/User.php");

session_start();

$logged_in = false;
$login_failed = false;

if (isset($_POST['submit'])) {
	// TODO: input validation
	$username = $_POST['username'];
	$password = $_POST['password'];
	$user = User::login($username, $password);

	if (!$user) {
		$login_failed = true;
	} else {
		$_SESSION['user'] = $user;
		$logged_in = true;
	}
}
?>
<!doctype html>
<html>
<head>
<?php include("../includes/layout.php"); ?>
<title>Login Page</title>
</head>
<body>
<?php include("../includes/header.php"); ?>
<?php
	if(isset($_SESSION['user'])) {
		echo "<p>Welcome, " . $_SESSION['user']->getUsernameHTMLSafe() . "</p>";
	} else {
		if($login_failed) {
			echo "<p>Username or password is incorrect. Please try again.</p>";
		}

		?>
			<form action='login.php' method='post'>
				<p>username: <input type='text' name='username' /></p>
				<p>password: <input type='password' name='password' /></p>
				<p><input type='submit' name='submit' value='login' /></p>
			</form>
		<?php
	}
?>
<?php include("../includes/footer.php"); ?>
</body>
</html>