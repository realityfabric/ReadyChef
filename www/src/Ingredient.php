<?php
class Ingredient
{
	private $id; // int
	private $name; // string
	private $categories; // array of categories
	
	/*
	 * __construct
	 * The constructor for the Ingredient class
	 * @id The id of the Ingredient instance
	 * @name The name of the Ingredient instance
	 * @categories An array of categories associated with the Ingredient instance
	 */
	function __construct ($id, $name, $categories) {
		// TODO: input validation
		$this->id = $id;
		$this->name = $name;
		$this->categories = $categories;
	}

	/* getName
	 * Returns the name of the ingredient
	 * @return the name of the ingredient
	 */
	public function getName () {
		// TODO: return name
	}

	/* getId
	 * Returns the id of the ingredient
	 * @return the id of the Ingredient instance
	 */
	public function getId () {
		// TODO return id
	}

	/* getCategories
	 * Returns an array of categories which the ingredient is associated with
	 * @return An array of Category instances which are associated with the Ingredient instance
	 */
	public function getCategories () {
		// TODO: return array of categories
	}

	/* hasCategory
	 * Checks to see if the ingredient is associated with a specific category
	 * @category the category which is being searched for
	 * @return a boolean value: true if category is associated with the ingredient, otherwise false
	 */
	public function hasCategory ($category) {
		// TODO: check for category
	}
}
