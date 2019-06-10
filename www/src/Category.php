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

	/* getNameHTMLSafe
	 * @return the name of the category instance, with all special characters converted to prevent script injection
	 */
	public function getNameHTMLSafe () {
		return  htmlspecialchars($this->name);
	}


	/* getId
	 * Returns the id of the category
	 * @return the id of the Category instance
	 */
	public function getId () {
		return $this->id;
	}

	/* loadCategory - DEPRECATED // left in because it's used places, but it should be removed and not used moving forward
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

	/* load
	 * @id - The id of the Category instance that is to be loaded
	 * @return - an instance of Category matching the given id
	 */
	public static function load ($id) {
		$dbconn = connectToDatabase();

		$query = pg_prepare($dbconn, "selectCategory", "SELECT * FROM category WHERE id = $1");
		$result = pg_execute($dbconn, "selectCategory", array($id));
		$row = pg_fetch_assoc($result);

		$category = new Category($row['id'], $row['name']);

		pg_close($dbconn);
		return $category;
	}

	/* loadCategoryByName
	 * @name - the name of the Category to be loaded
	 * @return - an instance of Category matching the given name
	 */
	public static function loadCategoryByName ($name) {
		$dbconn = connectToDatabase();

		$query = pg_prepare($dbconn, "selectCategory", "SELECT * FROM category WHERE name = $1");
		$result = pg_execute($dbconn, "selectCategory", array($name));
		$row = pg_fetch_assoc($result);

		$category = new Category($row['id'], $row['name']);

		pg_close($dbconn);
		return $category;
	}

	/* loadAll
	 * @return - An array of all Category records in the database
	 */
	public static function loadAll () {
		$dbconn = connectToDatabase();

		$result = pg_query($dbconn, "SELECT id FROM category ORDER BY category.name");
		$categoryIds = array();
		while (($row = pg_fetch_assoc($result)) != false) {
			$categoryIds[] = $row["id"];
		}

		$categories = array();
		foreach ($categoryIds as $id) {
			$categories[] = Category::loadCategory($id);
		}

		return $categories;
	}

	/* createCategory
	 * Creates a new Category record in the database
	 * @name - the name of the new Category
	 * @return - A database resource on success, false on failure
	 */
	public static function createCategory ($name) {
		$dbconn = connectToDatabase();

		$query = pg_prepare($dbconn, "checkCategory", "SELECT * FROM category WHERE name =  $1");
		$result = pg_execute($dbconn, "checkCategory", array($name));

		if (pg_num_rows($result) > 0) {
			return false;
		}

		// TODO: input validation / sanitization
		$insertResult = pg_insert($dbconn, "category", array("name" => $name));

		// TODO: insert categories
		return $insertResult;
	}

	/* getJSON
	 * A useless function that I immediately forgot the purpose for after writing it
	 * @return - a JSON representation of the Category instance
	 */
	public function getJSON () {
		$json = "{'id': " . $this->id . ", 'name': \"" . $this->name . "\"}";

		return $json;
	}

	/* toPHPArray
	 * A useless function that I immediately forgot the purpose for after writing it
	 * @return - a PHP Array representation of the Category instance
	 */
	public function toPHPArray () {
		$arr = array();

		$arr['id'] = $this->id;
		$arr['name'] = $this->name;

		return $arr;
	}
}
?>
