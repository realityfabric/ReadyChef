<?php
/*
 * This class stores user data, including a pointer to an instance of the Pantry class specific to the user.
 * User data is pulled from the user table.
 */
class User
{
	private $id; // int
	private $username; // string
	private $pantry // Pantry (custom class)
	
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
	 * @user the instance of the User class
	 */
	public function login ($username, $password) {
		// TODO
	}
}