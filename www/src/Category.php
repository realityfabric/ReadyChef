<?php
class Category
{
	private $id; // int
	private $name; // string

	/*
	 * Constructor for the Category class.
	 * @id the ID of the Category instance
	 * @name the name of the Category instance
	 */
	function __construct ($id, $name) {
		// TODO: input validation
		$this->id = $id;
		$this->name = $name;
	}
	
	/* getName
	 * Returns the name of the category
	 * @return the name of the Category instance
	 */
	public function getName () {
		return $this->name;
	}

	/* getId
	 * Returns the id of the category
	 * @return the id of the Category instance
	 */
	public function getId () {
		return $this->id;
	}
}
