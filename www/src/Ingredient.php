<?php
include_once("../config/config.php");
include_once("../src/Category.php");

class Ingredient
{
	private $id; // int
	private $name; // string
	private $categories; // array of categories

	/*
	 * __construct
	 * The constructor for the Ingredient class
	 * @id The id of the Ingredient instance
	 * @name The name of the Ingredient instance
	 * @categories An array of categories associated with the Ingredient instance
	 */
	function __construct ($id, $name, $categories) {
		// TODO: input validation
		$this->id = $id;
		$this->name = $name;
		$this->categories = $categories;
	}

	/* getName
	 * Returns the name of the ingredient
	 * @return the name of the ingredient
	 */
	public function getName () {
		return $this->name;
	}

	/* getId
	 * Returns the id of the ingredient
	 * @return the id of the Ingredient instance
	 */
	public function getId () {
		return $this->id;
	}

	/* getCategories
	 * Returns an array of categories which the ingredient is associated with
	 * @return An array of Category instances which are associated with the Ingredient instance
	 */
	public function getCategories () {
		return $this->categories;
	}

	/* hasCategory
	 * Checks to see if the ingredient is associated with a specific category
	 * @category the category which is being searched for
	 * @return a boolean value: true if category is associated with the ingredient, otherwise false
	 */
	public function hasCategory ($category) {
		// TODO: input validation
		foreach ($this->categories as $c) {
			if ($c == $category)
				return true;
			}
		return false;
	}

	/* loadIngredient
	 * Searches the database for an ingredient and returns an instance of it
	 * @id - The ID of the ingredient in the DB
	 * @return - An instance of Ingredient matching the ID
	 */
	public static function loadIngredient ($id) {
		global $db;

		$dbhost = $db['host'];
		$dbuser = $db['user'];
		$dbpassword = $db['password'];

		$dbconn = pg_connect("host='$dbhost' user='$dbuser' password='$dbpassword'");

		// TODO: input validation / sanitization
		$result = pg_query($dbconn, "SELECT * FROM ingredient WHERE id = $id");
		$row = pg_fetch_assoc($result);

		// TODO: load categories for the loaded ingredient

		$ingredientId = $row['id'];
		$ingredientName = $row['name'];

		$ingredient = new Ingredient ($ingredientId, $ingredientName, array());

		return $ingredient;
	}
}
