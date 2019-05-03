<?php
include_once("../config/config.php");
include_once("../src/Ingredient.php");

class Pantry
{
	private $ingredients; // Array of tuples (Ingredient, date)

	public function __construct ($userId) {
		global $db;

		$dbhost = $db['host'];
		$dbuser = $db['user'];
		$dbpassword = $db['password'];

		$dbconn = pg_connect("host='$dbhost' user='$dbuser' password='$dbpassword'");

		$this->ingredients = array();

		// TODO: input validation / sanitization
		// This isn't particularly secure...
		$result = pg_query($dbconn, "SELECT * FROM account_has_ingredient WHERE account_id = $userId");

		while (($row = pg_fetch_assoc($result)) != false) {
			$ingredientId = $row['ingredient_id'];
			$ingredient = Ingredient::loadIngredient($ingredientId);

			$this->ingredients[$ingredient->getName()] = array($ingredient, $row['date_purchased']);
		}
	}

	/* hasIngredient
	 * Checks if the pantry has a specific ingredient.
	 * @ingredient The ingredient in question
	 */
	public function hasIngredient ($ingredient) {
		// TODO: check for ingredient
	}

	/* addIngredient
	 * Adds an ingredient to the User's pantry
	 * @ingredient The ingredient being added
	 */
	public function addIngredient ($ingredient) {
		// TODO: add ingredient
	}

	/* removeIngredient
	 * Removes an ingredient from the User's pantry
	 * @ingredient The ingredient being removed.
	 */
	public function removeIngredient ($ingredient) {
		// TODO: remove ingredient
	}
}
