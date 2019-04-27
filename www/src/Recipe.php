<?php
class Recipe
{
	private $id; // int
	private $name; // string
	private $instructions; // string
	private $ingredients; // array of tuples (Ingredient objects, quantity)
	private $categories // array of Category objects

	/* getName
	 * Returns the name of the Recipe
	 */
	public function getName () {
		// TODO: return name
	}

	/* getId
	 * Returns the id of the Recipe
	 */
	public function getId () {
		// TODO return id
	}

	/* getCategories
	 * Returns an array of categories which the Recipe is associated with
	 */
	public function getCategories () {
		// TODO: return array of categories
	}

	/* getIngredients
	 * Returns an array of tuples which consist of ingredients and their quantities, as required by the recipe
	 */
	public function getIngredients () {
		// TODO: get ingredients and quantities
	}

	/* getInstructions
	 * Returns the recipe's instructions
	 */
	public function getInstructions () {
		// TODO: get instructions
	}

	/* hasCategory
	 * Checks to see if the Recipe is associated with a specific category
	 * @category the category which is being searched for
	 * @flag a boolean value: true if category is associated with the Recipe, otherwise false
	 */
	public function hasCategory ($category) {
		// TODO: check for category
	}
}
