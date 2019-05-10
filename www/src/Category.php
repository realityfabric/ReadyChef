<?php
include_once("../config/config.php");
include_once("../src/DBConnect.php");

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
		$dbconn = connectToDatabase();

		$query = pg_prepare($dbconn, "selectCategory", "SELECT * FROM category WHERE id = $1");
		$result = pg_execute($dbconn, "selectCategory", array($id));
		$row = pg_fetch_assoc($result);

		$category = new Category($row['id'], $row['name']);

		pg_close($dbconn);
		return $category;
	}
}
?>
