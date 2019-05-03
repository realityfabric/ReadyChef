<?php
include_once("../config/config.php");

class Category
{
	private $id; // int
	private $name; // string

	/*
	 * Constructor for the Category class.
	 * @id the ID of the Category instance
	 * @name the name of the Category instance
	 */
	function __construct ($id, $name) {
		// TODO: input validation
		$this->id = $id;
		$this->name = $name;
	}

	/* getName
	 * Returns the name of the category
	 * @return the name of the Category instance
	 */
	public function getName () {
		return $this->name;
	}

	/* getId
	 * Returns the id of the category
	 * @return the id of the Category instance
	 */
	public function getId () {
		return $this->id;
	}

	/* loadCategory
	 * Searches the database and returns the category associated with the ID
	 * @id - The ID of the category in the DB
	 * @return - An instance of the Category, or false
	 */
	public static function loadCategory ($id) {
		global $db;

		$dbhost = $db['host'];
		$dbuser = $db['user'];
		$dbpassword = $db['password'];

		$dbconn = pg_connect("host='$dbhost' user='$dbuser' password='$dbpassword'");

		// TODO: input validation / sanitization
		$result = pg_query($dbconn, "SELECT * FROM category WHERE id = $id");
		$row = pg_fetch_assoc($result);

		$category = new Category($row['id'], $row['name']);

		return $category;
	}
}
