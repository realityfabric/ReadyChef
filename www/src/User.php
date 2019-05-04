<?php
include_once("../config/config.php");
include_once("../src/Pantry.php");
include_once("../src/DBConnect.php");
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
		return $this->pantry;
	}

	/* login
	 * Generates an instance of a User object for a user
	 * @username the username of the user
	 * @password the password of the user
	 * @return the instance of the User class, or false if failed
	 */
	public static function login ($username, $password) {
		$dbconn = connectToDatabase();

		$sanitize_username = pg_escape_string($username);
		$sanitize_password = pg_escape_string($password);

		$query = pg_prepare($dbconn, "selectUser", "SELECT * FROM account WHERE username = $1");
		$result = pg_execute($dbconn, "selectUser", array($sanitize_username));
		$account = pg_fetch_assoc($result);

		// TODO: implement login logging
		if (password_verify($password, $account['hash'])) {
			$id = $account['id'];
			$pantry = new Pantry($id);
			$user = new User($id, $sanitize_username, $pantry);

			return $user;
		} else {
			return false;
		}
	}

	/* register
	 * Register a new user
	 * @username - the desired username
	 * @password - the desired password
	 * @return - true if successfully registered, false if unsuccessfully registered
	 */
	public static function register ($username, $password) {
		$dbconn = connectToDatabase();

		$query = pg_prepare($dbconn, "checkAccounts", "SELECT * FROM account WHERE username = $1");
		$result = pg_execute($dbconn, "checkAccounts", array($username));

		if (pg_num_rows($result) > 0) {
			return false;
		}

		$sanitize_username = pg_escape_string($username);
		$sanitize_password = pg_escape_string($password);
		$hash = password_hash($sanitize_password, PASSWORD_DEFAULT);

		$query = pg_prepare($dbconn, "newUser", "INSERT INTO account (username, hash) VALUES ($1, $2)");
		$result = pg_execute($dbconn, "newUser", array($sanitize_username, $hash));

		return $result; // false on failure
	}
}
