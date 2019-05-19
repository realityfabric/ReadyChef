<nav>
<a href="index.php">Home</a>
<a href="login.php">Log In</a>
<a href="logout.php">Log Out</a>
<a href="register.php">Register</a>
<?php
if (isset($_SESSION['user'])) {
?>
<a href="profile.php">My Pantry</a>
<a href="search.php">Recipe Search</a>
<?php
}
?>
</nav>