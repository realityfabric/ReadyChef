<?php
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
			"SELECT recipe_has_ingredient.ingredient_id FROM recipe_has_ingredient JOIN recipe ON recipe.id = recipe_has_ingredient.recipe_id WHERE recipe.id = $1"
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
			$ingredient = new Ingredient ($rowIngredient['id'], $rowIngredient['name']);
			$ingredients[$ingredient->getName()] = $ingredient;
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
}
