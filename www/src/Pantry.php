<?php
include_once("../config/config.php");
include_once("../src/Ingredient.php");

class Pantry
{
	private $ingredients; // Array of tuples (Ingredient, date)

	public function __construct ($userId) {
		$dbconn = connectToDatabase();

		$this->ingredients = array();

		// This isn't particularly secure...
		$query = pg_prepare($dbconn, "selectPantry", "SELECT * FROM account_has_ingredient WHERE account_id = $1");
		$result = pg_execute($dbconn, "selectPantry", array($userId));

		while (($row = pg_fetch_assoc($result)) != false) {
			$ingredientId = $row['ingredient_id'];
			$ingredient = Ingredient::loadIngredient($ingredientId);

			$this->ingredients[$ingredient->getName()] = array($ingredient, $row['date_purchased']);
		}

		pg_close($dbconn);
	}

	/* hasIngredient
	 * Checks if the pantry has a specific ingredient.
	 * @ingredient The ingredient in question
	 * @return true if ingredient is in the pantry, else false
	 */
	public function hasIngredient ($ingredient) {
		// TODO: input validation
		return array_key_exists($ingredient->getName(), $this->ingredients);
	}

	/* addIngredient
	 * Adds an ingredient to the User's pantry
	 * @userId - The ID of the user account whose pantry is being modified
	 * @ingredient The ingredient being added
	 * @return - the pg_execute result if successful, otherwise false (either pg_execute failed, or the ingredient is already in the db)
	 */
	public function addIngredient ($userId, $ingredient) {
		$dbconn = connectToDatabase();

		if (!$this->hasIngredient($ingredient)) {
			$query = pg_prepare($dbconn, "addIngredientToPantry", "INSERT INTO account_has_ingredient VALUES ($1, $2)");
			$result = pg_execute($dbconn, "addIngredientToPantry", array($userId, $ingredient->getId()));
			if ($result) {
				// if the insertion was successful, add it to the pantry object
				$this->ingredients[$ingredient->getName()] = array($ingredient, date("Y-m-d"));
			}
			return $result;
		} else { // ingredient already exists
			return false;
		}
	}

	/* removeIngredient
	 * Removes an ingredient from the User's pantry
	 * @ingredient The ingredient being removed.
	 */
	public function removeIngredient ($ingredient) {
		// TODO: remove ingredient
	}
}
