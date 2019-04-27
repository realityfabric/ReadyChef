<?php
class Ingredient
{
	private $id; // int
	private $name; // string
	private $categories // array of categories

	/* getName
	 * Returns the name of the ingredient
	 * @name the name of the ingredient
	 */
	public function getName () {
		// TODO: return name
	}

	/* getId
	 * Returns the id of the ingredient
	 */
	public function getId () {
		// TODO return id
	}

	/* getCategories
	 * Returns an array of categories which the ingredient is associated with
	 * @categories the list of categories which is returned
	 */
	public function getCategories () {
		// TODO: return array of categories
	}

	/* hasCategory
	 * Checks to see if the ingredient is associated with a specific category
	 * @category the category which is being searched for
	 * @flag a boolean value: true if category is associated with the ingredient, otherwise false
	 */
	public function hasCategory ($category) {
		// TODO: check for category
	}
}
