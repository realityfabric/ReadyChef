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

	/* getNameHTMLSafe
	 * Returns the name of the ingredient, converted to prevent script injection
	 * @return - The name of the ingredient with special characters converted
	 */
	public function getNameHTMLSafe () {
		return  htmlspecialchars($this->name);
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

	/* loadIngredient - DEPRECATED
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

	/* load
	 * Searches the database for an ingredient and returns an instance of it
	 * @id - The ID of the ingredient in the DB
	 * @return - An instance of Ingredient matching the ID
	 */
	public static function load ($id) {
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

	/* loadIngredientByName - DEPRECATED
	 * @name - the name of the ingredient to be loaded
	 * @return - an instance of Ingredient matching the given name
	 */
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
		$result = pg_query($dbconn, "SELECT * FROM ingredient_has_category WHERE ingredient_id = $ingredientId");

		while (($row = pg_fetch_assoc($result)) != false) {
			$categoryId = $row['category_id'];
			$categories[] = Category::loadCategory($categoryId);
		}

		$ingredient = new Ingredient ($ingredientId, $ingredientName, $categories);

		pg_close($dbconn);
		return $ingredient;
	}

	/* loadByName
	 * @name - the name of the ingredient to be loaded
	 * @return - an instance of Ingredient matching the given name
	 */
	public static function loadByName ($name) {
		$dbconn = connectToDatabase();

		$query = pg_prepare($dbconn, "selectIngredient", "SELECT * FROM ingredient WHERE name = $1");
		$result = pg_execute($dbconn, "selectIngredient", array($name));
		$row = pg_fetch_assoc($result);

		// TODO: load categories for the loaded ingredient

		$ingredientId = $row['id'];
		$ingredientName = $row['name'];

		// TODO: input validation / sanitization
		$result = pg_query($dbconn, "SELECT * FROM ingredient_has_category WHERE ingredient_id = $ingredientId");

		$categoryIds = array();
		while (($row = pg_fetch_assoc($result)) != false) {
			$categoryIds[] = $row['category_id'];
		}
		pg_close($dbconn);
		$categories = array();
		foreach ($categoryIds as $categoryId) {
			$categories[] = Category::loadCategory($categoryId);
		}
		$ingredient = new Ingredient ($ingredientId, $ingredientName, $categories);

		return $ingredient;
	}

	/* createIngredient
	 * Creates a new Ingredient record in the database, along with associated records for ingredient_has_category
	 * @name - The name of the new ingredient
	 * @categories - An array of categories associated with the ingredient
	 * @return - An instance of the new ingredient on success, or false on failure.
	 */
	public static function createIngredient ($name, $categories = array()) {
		$dbconn = connectToDatabase();

		$query = pg_prepare($dbconn, "checkIngredient", "SELECT * FROM ingredient WHERE name =  $1");
		$result = pg_execute($dbconn, "checkIngredient", array($name));

		if (pg_num_rows($result) > 0) {
			return false;
		}

		$query = pg_prepare($dbconn, "insertIngredient", "INSERT INTO ingredient (name) VALUES ($1)");
		$insertResult = pg_execute($dbconn, "insertIngredient", array($name));

		pg_close($dbconn);

		if ($insertResult) {
			$ingredient = Ingredient::loadByName($name);
			foreach($categories as $category) {
				Ingredient::addCategory($ingredient->getId(), $category->getId());
			}
			$ingredient = Ingredient::loadByName($name); // reload with newly added categories
			return $ingredient;
		} else {
			return $insertResult;
		}
	}

	/* addCategory
	 * Adds a record in the ingredient_has_category table of the database
	 * @ingredientId - The id of the ingredient which has the category associated with it
	 * @categoryId - The id of the category which is associated with the ingredient
	 * @return - A database resource on success, false on failure.
	 */
	public static function addCategory ($ingredientId, $categoryId) {
		$dbconn = connectToDatabase();

		$query = pg_prepare($dbconn, "addCategoryToIngredient", "INSERT INTO ingredient_has_category VALUES ($1, $2)");
		$result = pg_execute($dbconn, "addCategoryToIngredient", array($ingredientId, $categoryId));

		pg_close($dbconn);
		return $result;
	}

	/* getAll - DEPRECATED
	 * Loads all of the ingredients in the database. DEPRECATED
	 * @return - an array of Ingredient objects
	 */
	public static function getAll () {
		$dbconn = connectToDatabase();

		$ingredientsResult = pg_query($dbconn, "SELECT id FROM ingredient");
		$ingredients = array();
		$ingredientIds = array();
		while (($ingredientsRow = pg_fetch_assoc($ingredientsResult)) != false) {
			$ingredientIds[] = $ingredientsRow['id'];
		}
		pg_close($dbconn);

		foreach ($ingredientIds as $id) {
			$ingredients[] = Ingredient::loadIngredient($id);
		}

		return $ingredients;
	}

	/* loadAll
	 * Loads all of the ingredients from the database.
	 * @return - An array of Ingredient objects.
	 */
	public static function loadAll () {
		$dbconn = connectToDatabase();

		$ingredientsResult = pg_query($dbconn, "SELECT id FROM ingredient ORDER BY ingredient.name");
		$ingredients = array();
		$ingredientIds = array();
		while (($ingredientsRow = pg_fetch_assoc($ingredientsResult)) != false) {
			$ingredientIds[] = $ingredientsRow['id'];
		}
		pg_close($dbconn);

		foreach ($ingredientIds as $id) {
			$ingredients[] = Ingredient::loadIngredient($id);
		}

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
