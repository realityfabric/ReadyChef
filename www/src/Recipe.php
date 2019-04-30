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
}
