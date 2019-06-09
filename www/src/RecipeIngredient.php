<?php
include_once("../src/DBConnect.php");
include_once("../src/Ingredient.php");

class RecipeIngredient {
	private $ingredient; // Ingredient object
	private $quantity; // String

	public function __construct ($ingredient, $quantity) {
		// TODO: input validation
		$this->ingredient = $ingredient;
		$this->quantity = $quantity;
	}

	public function getIngredient () {
		return $this->ingredient;
	}

	public function getQuantity () {
		return $this->quantity;
	}

	public function getQuantityHTMLSafe () {
		return htmlspecialchars($this->quantity);
	}
}