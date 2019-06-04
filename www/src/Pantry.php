<?php
include_once("../config/config.php");
include_once("../src/DBConnect.php");
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

		$ingredientInfos = array();
		while (($row = pg_fetch_assoc($result)) != false) {
			$ingredientInfos[] = array (
				"id" => $row['ingredient_id'],
				"date_purchased" => $row['date_purchased']
			);
		}
		pg_close($dbconn);

		foreach($ingredientInfos as $info) {
			$ingredient = Ingredient::loadIngredient($info['id']);

			$this->ingredients[$ingredient->getName()] = array($ingredient, $info['date_purchased']);
		}

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
		// check to make sure the ingredient isn't already in the pantry
		if (!$this->hasIngredient($ingredient)) {
			$dbconn = connectToDatabase();
			$query = pg_prepare($dbconn, "addIngredientToPantry", "INSERT INTO account_has_ingredient VALUES ($1, $2)");
			$result = pg_execute($dbconn, "addIngredientToPantry", array($userId, $ingredient->getId()));
			if ($result) {
				// if the insertion was successful, add it to the pantry object
				$this->ingredients[$ingredient->getName()] = array($ingredient, date("Y-m-d"));
			}
			pg_close($dbconn);
			return $result;
		} else { // ingredient already exists
			return false;
		}
	}

	/* removeIngredient
	 * Removes an ingredient from the User's pantry
	 * @userId - The ID of the user account for the pantry
	 * @ingredient The ingredient being removed.
	 * @return - the pg_execute result if successful, otherwise false (either pg_execute failed, or the ingredient wasn't in the db)
	 */
	public function removeIngredient ($userId, $ingredient) {
		// check to make sure the ingredient exists in the pantry
		if ($this->hasIngredient($ingredient)) {
			$dbconn = connectToDatabase();
			$query = pg_prepare($dbconn, "removeIngredientFromPantry", "DELETE FROM account_has_ingredient WHERE account_id = $1 AND ingredient_id = $2");
			$result = pg_execute($dbconn, "removeIngredientFromPantry", array($userId, $ingredient->getId()));
			if ($result) {
				// if the deletion was successful, remove it from the Pantry object instance
				unset($this->ingredients[$ingredient->getName()]); // does not re-index, but preserves keys
			}
			pg_close($dbconn);
			return $result;
		} else { // ingredient isn't in the pantry
			return false;
		}
	}

	public function getIngredients () {
		return $this->ingredients;
	}
}
