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

	/* __construct
	 * Create an instance of the User class
	 * @id - The ID of the user (int)
	 * @username - The username of the user (string)
	 * @pantry - An instance of the Pantry class which is associated with the user (Pantry)
	 */
	public function __construct ($id, $username, $pantry) {
		// TODO: input validiation
		$this->id = $id;
		$this->username = $username;
		$this->pantry = $pantry;
	}

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

		$dbconn = pg_connect("host='$dbhost' user='$dbuser' password='$dbpassword'");

		$sanitize_username = pg_escape_string($username);
		$sanitize_password = pg_escape_string($password);

		$query = pg_prepare($dbconn, "selectUser", "SELECT * FROM account WHERE username = $1");
		$result = pg_execute($dbconn, "selectUser", array($sanitize_username));
		$account = pg_fetch_assoc($result);

		// TODO: implement login logging
		if (password_verify($password, $account['hash'])) {
			// TODO: create instance of Pantry associated with User
			// TODO: create instance of User
			return true; // TODO: return instance of User
		} else {
			return false;
		}
	}
}
