<?php
include_once("../config/config.php");
include_once("../src/DBConnect.php");
include_once("../src/Category.php");
include_once("../src/Ingredient.php");

class Recipe
{
	private $id; // int
	private $name; // string
	private $instructions; // string
	private $ingredients; // array of tuples (Ingredient objects, quantity)
	private $categories; // array of Category objects

	/* __construct
	 * @id - the recipe id
	 * @name - the recipe name
	 * @instructions - the recipe instructions
	 * @ingredients - an array of Ingredients associated with the recipe
	 * @categories - an array of Categories associated with the recipe
	 */
	public function __construct ($id, $name, $instructions, $ingredients, $categories) {
		// TODO: input validation
		$this->id = $id;
		$this->name = $name;
		$this->instructions = $instructions;
		$this->ingredients = $ingredients;
		$this->categories = $categories;
	}

	/* getName
	 * Returns the name of the Recipe
	 * @return The name of the Recipe instance
	 */
	public function getName () {
		return $this->name;
	}

	/* getId
	 * Returns the id of the Recipe
	 * @return The ID of the Recipe instance
	 */
	public function getId () {
		return $this->id;
	}

	/* getCategories
	 * Returns an array of categories which the Recipe is associated with
	 * @return The array of Category objects associated with the Recipe instance
	 */
	public function getCategories () {
		return $this->categories;
	}

	/* getIngredients
	 * Returns an array of tuples which consist of ingredients and their quantities, as required by the recipe
	 * @return The array of tuples of Ingredient objects and Quantities.
	 */
	public function getIngredients () {
		return $this->ingredients;
	}

	/* getInstructions
	 * Returns the recipe's instructions
	 * @return The instructions for the Recipe instance
	 */
	public function getInstructions () {
		return $this->instructions;
	}

	/* hasCategory
	 * Checks to see if the Recipe is associated with a specific category
	 * @category the category which is being searched for
	 * @flag a boolean value: true if category is associated with the Recipe, otherwise false
	 */
	public function hasCategory ($category) {
		// TODO: input validation
		foreach ($this->categories as $c) {
			if ($c == $category)
				return true;
			}
		return false;
	}

	/* loadRecipe
	 * searches the database to find the recipe with the specified id
	 * @id - the id of the recipe being searched for
	 * @return - an instance of Recipe
	 */
	public static function loadRecipe ($id) {
		$dbconn = connectToDatabase();

		$query =
			pg_prepare($dbconn,
			"selectRecipeById",
			"SELECT recipe.id, recipe.name, recipe.instructions FROM recipe WHERE id = $1"
		);
		$result = pg_execute($dbconn, "selectRecipeById", array($id));
		$row = pg_fetch_assoc($result);
		$recipeId = $row['id'];
		$recipeName = $row['name'];
		$recipeInstructions = $row['instructions'];

		$ingredients = Recipe::loadRecipeIngredients($recipeId);
		$categories = Recipe::loadRecipeCategories($recipeId);

		$recipe = new Recipe($recipeId, $recipeName, $recipeInstructions, $ingredients, $categories);
		return $recipe;
	}

	/* loadRecipeIngredients
	 * searches the database for ingredients associated with the recipe
	 * @id - the id of the recipe
	 * @dbconn - a database connection, or false if a new database connection should be established
	 * @return - an array of Ingredient objects
	 */
	public static function loadRecipeIngredients ($id, $dbconn = false) {
		if (!$dbconn)
			$dbconn = connectToDatabase();

		$ingredients = array();

		$query =
			pg_prepare($dbconn,
			"selectRecipeIngredientIds",
			"SELECT recipe_has_ingredient.ingredient_id, recipe_has_ingredient.quantity FROM recipe_has_ingredient JOIN recipe ON recipe.id = recipe_has_ingredient.recipe_id WHERE recipe.id = $1"
		);
		$resultIds = pg_execute($dbconn, "selectRecipeIngredientIds", array($id));

		// not using Ingredient::loadIngredient() because it would create and close db connections and waste the opportunity to reuse a prepared statement
		$query =
			pg_prepare($dbconn,
			"selectIngredientById",
			"SELECT ingredient.id, ingredient.name FROM ingredient WHERE ingredient.id = $1"
		);
		while (($row = pg_fetch_assoc($resultIds)) != false) {
			$resultIngredient =
				pg_execute($dbconn,
				"selectIngredientById",
				array($row['ingredient_id'])
			);
			$rowIngredient = pg_fetch_assoc($resultIngredient);
			// TODO: can I use Ingredient::loadIngredient() instead of new Ingredient?
			// tested it: it breaks things. why? if i knew that it wouldn't break them anymore.
			// it isn't closing dbconn, but it seems to break pg_execute
//			$ingredient = Ingredient::loadIngredient($rowIngredient['id']);
			$ingredient = new Ingredient ($rowIngredient['id'], $rowIngredient['name'], array()); // TODO: include categories
			$ingredients[$ingredient->getName()] = array("ingredient" => $ingredient, "quantity" => $row['quantity']);
		}

		pg_close($dbconn);
		return $ingredients;
	}

	/* loadRecipeCategories
	 * Searches the database for categories associated with the recipe
	 * @id - the id of the recipe
	 * @dbconn - a database connection, or false if a new connection should be established
	 * @return - an array of Category objects assocated with the recipe
	 */
	public static function loadRecipeCategories ($id, $dbconn = false) {
		if (!$dbconn)
			$dbconn = connectToDatabase();

		$categories = array();

		$query =
			pg_prepare($dbconn,
			"selectRecipeCategoryIds",
			"SELECT recipe_has_category.category_id FROM recipe_has_category JOIN recipe ON recipe.id = recipe_has_category.recipe_id WHERE recipe.id = $1"
		);
		$resultIds = pg_execute($dbconn, "selectRecipeCategoryIds", array($id));

		// not using Category::loadCategory() because it would create and close db connections and waste the opportunity to reuse a prepared statement
		$query =
			pg_prepare($dbconn,
			"selectCategoryById",
			"SELECT category.id, category.name FROM category WHERE category.id = $1"
		);
		while (($row = pg_fetch_assoc($resultIds)) != false) {
			$resultCategory =
				pg_execute($dbconn,
				"selectCategoryById",
				array($row['category_id'])
			);
			$rowCategory = pg_fetch_assoc($resultCategory);
			$category = new Category ($rowCategory['id'], $rowCategory['name']);
			$categories[$category->getName()] = $category;
		}

		pg_close($dbconn);
		return $categories;
	}

	/* searchByDBPantry
	 * searches the database for recipes with the given ratio of ingredients on hand to ingredients required (1: 100%, 0: 0%)
	 * @userId - the ID of the user account whose pantry is being analyzed
	 * @ratio - the ratio of ingredients on hand to required (e.g. 3 ingredients on hand and 4 required would have a ratio of 3/4, or .75)
	 *		default ratio is 0 (returning all recipes)
	 * @return - an array of Recipe instances
	 */
	public static function searchByDBPantry ($userId, $ratio = 0) {
		$recipeIds = Recipe::searchRecipeIdsByDBPantry($userId, $ratio);
		$recipes = array();

		foreach($recipeIds as $id) {
			$recipes[] = Recipe::loadRecipe($id);
		}

		return $recipes;
	}

	/* searchRecipeIdsByDBPantry
	 * searches the database for recipe id by looking up the ingredients a user has in the DB
	 * @userId - The ID of a user account
	 * @ratio - The ratio of ingredients not on hand to on hand. Default is 0, returning all recipes.
	 * @return - An array of recipe ids for recipes
	 */
	public static function searchRecipeIdsByDBPantry ($userId, $ratio = 0) {
		$dbconn = connectToDatabase();

		$query =
			pg_prepare($dbconn,
			"searchRecipeIdsByDBPantry",
			"SELECT
				recipe.id
			FROM account_has_ingredient
				JOIN recipe_has_ingredient rhi
					ON account_has_ingredient.ingredient_id = rhi.ingredient_id
				JOIN recipe
					ON recipe.id = rhi.recipe_id
			WHERE account_has_ingredient.account_id = $1
			GROUP BY recipe.id, rhi.recipe_id
			HAVING (COUNT(*) / (SELECT COUNT(*) FROM recipe_has_ingredient WHERE recipe_id = rhi.recipe_id)) >= $2");

		$result = pg_execute($dbconn,
			"searchRecipeIdsByDBPantry",
			array($userId, $ratio)
		);

		$recipeIds = array();
		while (($row = pg_fetch_assoc($result)) != false) {
			$recipeIds[] = $row['id'];
		}

		pg_close($dbconn);
		return $recipeIds;
	}

	/* loadRecipeByName
	 * Creates a Recipe object (and associated child objects) based on the record in the recipe table with the name passed as an argument
	 * @name - The name of the recipe being loaded
	 * @return - The Recipe object created
	 */
	public static function loadRecipeByName ($name) {
		$dbconn = connectToDatabase();

		$query =
			pg_prepare($dbconn,
			"selectRecipeByName",
			"SELECT recipe.id, recipe.name, recipe.instructions FROM recipe WHERE name = $1"
		);
		$result = pg_execute($dbconn, "selectRecipeByName", array($name));
		$row = pg_fetch_assoc($result);
		$recipeId = $row['id'];
		$recipeName = $row['name'];
		$recipeInstructions = $row['instructions'];

		$ingredients = Recipe::loadRecipeIngredients($recipeId);
		$categories = Recipe::loadRecipeCategories($recipeId);

		if (!$ingredients) {
			$ingredients = array();
		}

		if (!$categories) {
			$categories = array();
		}

		$recipe = new Recipe($recipeId, $recipeName, $recipeInstructions, $ingredients, $categories);
		return $recipe;
	}

	/* createRecipe
	 * Creates a recipe in the DB.
	 * @name - (String:) The name of the recipe
	 * @instructions - (String:) The instructions for preparing the recipe
	 * @ingredients - (Array of Arrays:) The ingredients necessary to make the recipe, and their quantities
	 *					in the form array ( array ( "ingredient" => Ingredient , "quantity" => String )[, ...])
	 * @categories - (Array of Category:) The categories associated with the recipe. Default is an empty array.
	 * @return - Boolean value indicating whether the creation was successful (True for Success, False for Failure)
	 */
	public static function createRecipe ($name, $instructions, $ingredients, $categories = array()) {
		$dbconn = connectToDatabase();

		$query = pg_prepare($dbconn, "checkRecipe", "SELECT * FROM recipe WHERE name =  $1");
		$result = pg_execute($dbconn, "checkRecipe", array($name));

		if (pg_num_rows($result) > 0) {
			return false;
		}

		// TODO: input validation / sanitization
		$insertResult = pg_insert($dbconn, "recipe", array("name" => $name, "instructions" => $instructions));

		pg_close($dbconn);
		$recipe = Recipe::loadRecipeByName($name);

		$dbconn = connectToDatabase();

		$query = pg_prepare($dbconn, "insertIngredient", "INSERT INTO recipe_has_ingredient (recipe_id, ingredient_id, quantity) VALUES ($1, $2, $3)");
		foreach ($ingredients as $ingredient) {
			$id = $recipe->getId();
			$ing_id = $ingredient['ingredient']->getId();
			$qty = $ingredient['quantity'];

			$insertIngredientResult = pg_execute($dbconn, "insertIngredient", array( $id, $ing_id, $qty));
		}

		$query = pg_prepare($dbconn, "insertCategory", "INSERT INTO recipe_has_category (recipe_id, category_id) VALUES ($1, $2)");
		foreach ($categories as $category) {
			$id = $recipe->getId();
			$cat_id = $category->getId();
			$insertCategoryResult = pg_execute($dbconn, "insertCategory", array($id, $cat_id));
		}

		// TODO: insert categories
		return $insertResult; // TODO: return success or failure based on more than that
	}

	/* searchRecipesPatternMatching
	 * Searches the Database for recipes using pattern matching.
	 * @pattern - (String:) The pattern to search for.
	 *				Currently supports SQL pattern matching (using LIKE)
	 * @options - (Array:) Optional arguments. By default the pattern will only match recipe names. Options should all be boolean.
	 *				Current accepted options:
	 *				- name
	 *				- instructions
	 * @return - (Array of Recipe:) An array of recipes found using the given pattern.
	 */
	public static function searchRecipesPatternMatching ($pattern, $options = array("name" => true)) {
		$dbconn = connectToDatabase();

		// will be set to either $pattern or an empty string based on whether they are set to true in $options
		$values = array ();
		// used to prevent adding a table multiple times to the FROM clause
		// more tables can be added as necessary
		$tables = array (
			"recipe" => true // this will always be true, because recipe table must be included to get recipe.id in the SELECT clause
		);

		$select = "SELECT recipe.id ";
		$from = "FROM recipe";
		$from .= " LEFT JOIN recipe_has_ingredient ON recipe.id = recipe_has_ingredient.recipe_id";
		$from .= " LEFT JOIN ingredient ON ingredient.id = recipe_has_ingredient.ingredient_id";
		$from .= " LEFT JOIN recipe_has_category ON recipe.id = recipe_has_category.recipe_id";
		$from .= " LEFT JOIN category on category.id = recipe_has_category.category_id";

		// not looping over $values to prevent bad keys being injected
		if (isset($options["name"]) && $options["name"]) {
			$values["name"] = $pattern;
			$tables["recipe"] = true;
		} else {
			$values["name"] = "";
		}
		if (isset($options["instructions"]) && $options["instructions"]) {
			$values["instructions"] = $pattern;
			$tables["recipe"] = true;
		} else {
			$values["instructions"] = "";
		}
		if (isset($options["ingredients"])) {
			$values["ingredients"] = $pattern;
			$tables["ingredient"] = true;
		} else {
			$values["ingredients"] = "";
		}
		if (isset($options["categories"])) {
			$values["categories"] = $pattern;
			$tables["category"] = true;
		} else {
			$values["categories"] = "";
		}

		// $1 will be $values["name"]
		// $2 will be $values["instructions"]
		// $3 will be $values["ingredients"]
		// $4 will be $values["categories"]
		$where = " WHERE LOWER(recipe.name) LIKE LOWER($1)";
		$where .= " OR LOWER(recipe.instructions) LIKE LOWER($2)";
		$where .= " OR LOWER(ingredient.name) LIKE LOWER($3)";
		$where .= " OR LOWER(category.name) LIKE LOWER($4)";

		$group = " GROUP BY recipe.id";
		$queryString = $select . $from . $where . $group;
		$query = pg_prepare($dbconn,
			"searchRecipesPatternMatching",
			$queryString
		);

		// each pattern must be a separate argument
		// to allow the user to *not* match against a field
		// this comment is here because i forgot why i did that
		// and i didn't want to spend another 10 minutes figuring it out later
		// when i inevitably forget why again
		$result = pg_execute(
			$dbconn,
			"searchRecipesPatternMatching",
			array (
				$values["name"],
				$values["instructions"],
				$values["ingredients"],
				$values["categories"]
			)
		);

		$recipeIds = array();
		while (($row = pg_fetch_assoc($result)) != false) {
			$recipeIds[] = $row["id"];
		}
		pg_close($dbconn);

		$recipes = array();
		foreach($recipeIds as $id) {
			$recipes[] = Recipe::loadRecipe($id);
		}

		return $recipes;
	}
}