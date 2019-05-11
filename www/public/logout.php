<?php
// https://www.php.net/manual/en/function.session-destroy.php
$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();
?>
<!doctype html>
<html>
<head>
	<title>Logout</title>
</head>
<body>
<?php include("../includes/header.php"); ?>

<p>You have been logged out.</p>

<?php include("../includes/footer.php"); ?>
</body>
</html>