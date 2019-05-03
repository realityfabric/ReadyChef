<?php
include_once("../config/config.php");
include_once("../src/Pantry.php");
/*
 * This class stores user data, including a pointer to an instance of the Pantry class specific to the user.
 * User data is pulled from the user table.
 */
class User
{
	private $id; // int
	private $username; // string
	private $pantry; // Pantry (custom class)
	
	/* getPantry
	 * Generates a Pantry object and assigns it to the User instance
	 * @pantry Returns the Pantry object
	 */
	public function getPantry () {
		// TODO
	}

	/* login
	 * Generates an instance of a User object for a user
	 * @username the username of the user
	 * @password the password of the user
	 * @return the instance of the User class, or false if failed
	 */
	public function login ($username, $password) {
		global $db;

		$dbhost = $db['host'];
		$dbuser = $db['user'];
		$dbpassword = $db['password'];

		$dbconn = pg_connect("host='localhost' user='readychef' password='ReadyChefDev'");

		$sanitize_username = pg_escape_string($username);
		$sanitize_password = pg_escape_string($password);

		$result = pg_query($dbconn, "SELECT * FROM account WHERE username = '$sanitize_username'");
		$account = pg_fetch_assoc($result);

		if (password_verify($password, $account['hash'])) {
			// TODO: create instance of Pantry associated with User
			// TODO: create instance of User
			return true; // TODO: return instance of User
		} else {
			return false;
		}
	}
}
