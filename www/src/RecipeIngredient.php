<?php
include_once("../src/DBConnect.php");
include_once("../src/Ingredient.php");

class RecipeIngredient {
	private $ingredient; // Ingredient object
	private $quantity; // String

	/* __construct
	 * The constructor for the RecipeIngredient class
	 */
	public function __construct ($ingredient, $quantity) {
		// TODO: input validation
		$this->ingredient = $ingredient;
		$this->quantity = $quantity;
	}

	/* getIngredient
	 * @return - an ingredient object
	 */
	public function getIngredient () {
		return $this->ingredient;
	}

	/* getQuantity
	 * @return - a string representing the quantity of ingredient
	 */
	public function getQuantity () {
		return $this->quantity;
	}

	/* getQuantityHTMLSafe
	 * @return - A string representing the quantity of ingredient, with special characters converted to prevent script injection
	 */
	public function getQuantityHTMLSafe () {
		return htmlspecialchars($this->quantity);
	}
}