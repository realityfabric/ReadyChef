<html>
<head>
<title>ReadyChef Tests</title>
</head>
<body>
<h1>ReadyChef Tests</h1>
<?php
/*
	TESTING
*/
include_once("../www/config/config.php");
include_once("../www/src/DBConnect.php");
include_once("../www/src/Category.php");
include_once("../www/src/Ingredient.php");
include_once("../www/src/Pantry.php");
include_once("../www/src/Recipe.php");
include_once("../www/src/User.php");

$TEST_ACCOUNT_USERNAME = "test_account";
$TEST_ACCOUNT_PASSWORD = "password";

$tests_failed = 0;

// TEST DATABASE CONNECTION
$dbconn = connectToDatabase();
if ($dbconn) {
	echo "<p>Database Connection: Pass</p>";
} else {
	echo "<p>Database Connection: Fail</p>";
	$tests_failed += 1;
}

// TEST USER REGISTRATION
$registration = User::register($TEST_ACCOUNT_USERNAME, $TEST_ACCOUNT_PASSWORD);
if ($registration) {
	echo "<p>Registration: Pass</p>";
} else {
	echo "<p>Registration: Fail</p>";
	$tests_failed += 1;
}

// TEST USER REGISTRATION REJECTION (previous test creates user)
unset($registration);
$registration = User::register($TEST_ACCOUNT_USERNAME, $TEST_ACCOUNT_PASSWORD);
if (!$registration) {
	echo "<p>Registration Rejection: Pass</p>";
} else {
	echo "<p>Registration Rejection: Fail</p>";
	$tests_failed += 1;
}

// TEST USER LOGIN
$user = User::login($TEST_ACCOUNT_USERNAME, $TEST_ACCOUNT_PASSWORD);
if ($user) {
	echo "<p>User Login: Pass</p>";
} else {
	echo "<p>User Login: Fail</p>";
	$tests_failed += 1;
}

// TEST USER VALUES
if ($user->getUsername() == $TEST_ACCOUNT_USERNAME) {
	echo "<p>User Username: Pass</p>";
} else {
	echo "<p>User Username: Fail</p>";
	$tests_failed += 1;
}

// TEST Pantry::addIngredient()
$ingredient = Ingredient::loadIngredient(1);
$pantry = $user->getPantry();
$pantry->removeIngredient($user->getId(), $ingredient);
$pantry->addIngredient($user->getId(), $ingredient);
if ($pantry->hasIngredient($ingredient)) {
	echo "<p>Pantry::addIngredient(\$id): Pass</p>";
}  else {
	echo "<p>Pantry::addIngredient(\$id): Fail</p>";
	$tests_failed += 1;
}
$pantry->removeIngredient($user->getId(), $ingredient);

// TEST Pantry::removeIngredient()
$pantry->removeIngredient($user->getId(), $ingredient);
if (!$pantry->hasIngredient($ingredient)) {
	echo "<p>Pantry::removeIngredient(\$id): Pass</p>";
} else {
	echo "<p>Pantry::removeIngredient(\$id): Fail</p>";
	$tests_failed += 1;
}

// clear variables from previous test
unset($ingredient);
unset($pantry);

// TEST Ingredient
$categories = array(
	new Category(5000,"test_category_1"),
	new Category(10000, "test_category_2")
);
$ingredient = new Ingredient(5000, "test_ingredient", $categories);

// TEST Ingredient::getId()
if ($ingredient->getId() == 5000) {
	echo "<p>Ingredient::getId() - Pass</p>";
} else {
	echo "<p>Ingredient::getId() - Fail</p>";
	$tests_failed += 1;
}

// TEST Ingredient::getName()
if ($ingredient->getName() == "test_ingredient") {
	echo "<p>Ingredient::getName() - Pass</p>";
} else {
	echo "<p>Ingredient::getName() - Fail</p>";
	$tests_failed += 1;
}

// TEST Ingredient::getCategories()
if ($ingredient->getCategories() == $categories) {
	echo "<p>Ingredient::getCategories() - Pass</p>";
} else {
	echo "<p>Ingredient::getCategories() - Fail</p>";
	$tests_failed += 1;
}

// TEST Ingredient::hasCategory()
$category = $categories[0];
if ($ingredient->hasCategory($category)) {
	echo "<p>Ingredient::hasCategory() - Pass</p>";
} else {
	echo "<p>Ingredient::hasCategory() - Fail</p>";
	$tests_failed += 1;
}

// TEST failure of Ingredient::hasCategory()
unset($category);
$category = new Category(20000, "test_category_3");
if (!$ingredient->hasCategory($category)) {
	echo "<p>Ingredient::hasCategory() == false - Pass</p>";
} else {
	echo "<p>Ingredient::hasCategory() == false - Fail</p>";
	$tests_failed += 1;
}

// TODO: test Ingredient::loadIngredient($id)

// clear variables from previous test
unset($category);
unset($categories);
unset($ingredient);

// TEST Category
$category = new Category(5000, "test_category_1");

// TEST Category::getId()
if ($category->getId() == 5000) {
	echo "<p>Category::getId() - Pass</p>";
} else {
	echo "<p>Category::getId() - Pass</p>";
	$tests_failed += 1;
}

// TODO: test Category::loadCategory($id)

// clear variables from previous test
unset($category);

// TEST Recipe
$ingredients = array(
	array(
		"ingredient" => new Ingredient(5000, "test_ingredient_1"),
		"quantity" => "500mg"
	),
	array(
		"ingredient" => new Ingredient(10000, "test_ingredient_2"),
		"quantity" => "40 cups"
	)
);
$categories = array(
	new Category (5000, "test_category_1"),
	new Category (10000, "test_category_2")
);
$recipe = new Recipe (5000, "test_recipe_1", "test_instructions_1", $ingredients, $categories);

// TEST Recipe::getName()
if ($recipe->getName() == "test_recipe_1") {
	echo "<p>Recipe::getName() - Pass</p>";
} else {
	echo "<p>Recipe::getName() - Fail</p>";
	$tests_failed += 1;
}

// TEST Recipe::getId()
if ($recipe->getId() == 5000) {
	echo "<p>Recipe::getId() - Pass</p>";
} else {
	echo "<p>Recipe::getId() - Fail</p>";
	$tests_failed += 1;
}

// TEST Recipe::getCategories()
if ($recipe->getCategories() == $categories) {
	echo "<p>Recipe::getCategories() - Pass</p>";
} else {
	echo "<p>Recipe::getCategories() - Fail</p>";
	$tests_failed += 1;
}

// TEST Recipe::hasCategory()
if ($recipe->hasCategory($categories[0])) {
	echo "<p>Recipe::hasCategory() - Pass</p>";
} else {
	echo "<p>Recipe::hasCategory() - Fail</p>";
	$tests_failed += 1;
}

// TEST Recipe::hasCategory() where result is false
if (!$recipe->hasCategory(new Category (40000, "nonexistent category"))) {
	echo "<p>Recipe::hasCategory() == false - Pass</p>";
} else {
	echo "<p>Recipe::hasCategory() == false - Fail</p>";
	$tests_failed += 1;
}

// TODO: test the rest of Recipe methods which access database

// DELETE TEST USER ACCOUNT
// TODO: FIX
// currently test account must be manually deleted after testing
// pg_delete($dbconn, "account", array("username" => $TEST_ACCOUNT_USERNAME));
echo "<h1>Testing Complete. Manually delete user account \"$TEST_ACCOUNT_USERNAME\" before running another test.</h1>";
if ($tests_failed == 0) {
	echo "<p>All Tests Passed</p>";
} else {
	echo "<p>$tests_failed tests have failed.</p>";
}

pg_close($dbconn);

?>
</html>
