<?php
include_once("../config/config.php");
include_once("../src/Category.php");
include_once("../src/DBConnect.php");

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
		$dbconn = connectToDatabase();

		$query = pg_prepare($dbconn, "selectIngredient", "SELECT * FROM ingredient WHERE id = $1");
		$result = pg_execute($dbconn, "selectIngredient", array($id));
		$row = pg_fetch_assoc($result);

		// TODO: load categories for the loaded ingredient

		$ingredientId = $row['id'];
		$ingredientName = $row['name'];
		$categories = array();

		// TODO: input validation / sanitization
		$result = pg_query($dbconn, "SELECT * FROM ingredient_has_category WHERE ingredient_id = $id");

		$categoryIds = array();
		while (($row = pg_fetch_assoc($result)) != false) {
			$categoryIds[] = $row['category_id'];
		}
		pg_close($dbconn);

		foreach($categoryIds as $id) {
			$categories[] = Category::loadCategory($id);
		}

		$ingredient = new Ingredient ($ingredientId, $ingredientName, $categories);


		return $ingredient;
	}

	public static function loadIngredientByName ($name) {
		$dbconn = connectToDatabase();

		$query = pg_prepare($dbconn, "selectIngredient", "SELECT * FROM ingredient WHERE name = $1");
		$result = pg_execute($dbconn, "selectIngredient", array($name));
		$row = pg_fetch_assoc($result);

		// TODO: load categories for the loaded ingredient

		$ingredientId = $row['id'];
		$ingredientName = $row['name'];
		$categories = array();

		// TODO: input validation / sanitization
		$result = pg_query($dbconn, "SELECT * FROM ingredient_has_category WHERE ingredient_id = $id");

		while (($row = pg_fetch_assoc($result)) != false) {
			$categoryId = $row['category_id'];
			$categories[] = Category::loadCategory($categoryId);
		}

		$ingredient = new Ingredient ($ingredientId, $ingredientName, $categories);

		pg_close($dbconn);
		return $ingredient;
	}

	public static function createIngredient ($name, $categories = array()) {
		$dbconn = connectToDatabase();

		$query = pg_prepare($dbconn, "checkIngredient", "SELECT * FROM ingredient WHERE name =  $1");
		$result = pg_execute($dbconn, "checkIngredient", array($name));

		if (pg_num_rows($result) > 0) {
			return false;
		}

		// TODO: input validation / sanitization
		$insertResult = pg_insert($dbconn, "ingredient", array("name" => $name));

		// TODO: insert categories
		return $insertResult;
	}

	public static function getAll () {
		$dbconn = connectToDatabase();

		$ingredientsResult = pg_query($dbconn, "SELECT * FROM ingredient");

		$ingredients = array();

		while (($ingredientsRow = pg_fetch_assoc($ingredientsResult)) != false) {
			$ingredientId = $ingredientsRow['id'];
			$ingredientName = $ingredientsRow['name'];
			$categories = array();

			// TODO: input validation / sanitization
			$dbconn2 = connectToDatabase();
			$categoriesResult = pg_query($dbconn2, "SELECT * FROM ingredient_has_category WHERE ingredient_id = $ingredientId");

			while (($categoriesRow = pg_fetch_assoc($categoriesResult)) != false) {
				$categoryId = $categoriesRow['category_id'];
				$categories[] = Category::loadCategory($categoryId);
			}

			$ingredient = new Ingredient($ingredientsRow['id'], $ingredientsRow['name'], $categories);

			$ingredients[] = $ingredient;
		}

		pg_close($dbconn2);
		pg_close($dbconn);
		return $ingredients;
	}

	/* toJSON
	 * Creates a JSON copy of the object
	 */
	public function toJSON () {
		$categoriesJSON = "[";
		foreach ($this->categories as $category) {
			$categoriesJSON .= $category->getJSON() . ",";
		}
		$categoriesJSON = rtrim($categoriesJSON, ",");
		$categoriesJSON .= "]";

		$json = "{'id': " . $this->id . ", 'name': \"" . $this->name . "\", 'categories': " . $categoriesJSON . "}";

 		return $json;
	}

	/* toPHPArray
	 * Creates a copy of the object as an assciated array
	 */
	public function toPHPArray () {
		$arr = array ();
		$categoriesArr = array();
		foreach($categories as $category) {
			$categoriesArr[] = $category->toPHPArray();
		}

		$arr['id'] = $this->id;
		$arr['name'] = $this->name;
		$arr['categories'] = $categoriesArr;

		return $arr;
	}
}
?>
