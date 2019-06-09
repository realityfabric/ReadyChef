<?php
include_once("../config/config.php");
include_once("../src/Recipe.php");
include_once("../src/User.php");
session_start();
?>
<!doctype html>
<html>
<head>
	<?php include("../includes/layout.php"); ?>
	<title>Search</title>
</head>
<?php include("../includes/header.php"); ?>
<body>
<section>
<form name="search" action="search.php" method="post">
	<h1>Search for Recipes.</h1>
	<p>You can use SQL Pattern Matching for more advanced searches. For example, use % as a wildcard.</p>
	<p>
		<input type="text" name="search-string" placeholder="Search for Recipes" />
	</p>
	<p>Search by:</p>
	<ul id="search-options" class="options">
		<li>
			<input type="checkbox" name="search-recipe-name" <?php if(isset($_POST["search-recipe-name"])) echo "checked=1"; ?> />
			<label for="search-recipe-name">recipe name</label>
		</li>
		<li>
			<input type="checkbox" name="search-recipe-instructions" <?php if(isset($_POST["search-recipe-instructions"])) echo "checked=1"; ?> />
			<label for="search-recipe-instructions">recipe instructions</label>
		</li>
		<li>
			<input type="checkbox" name="search-ingredient-name" <?php if(isset($_POST["search-ingredient-name"])) echo "checked=1"; ?> />
			<label for="search-ingredient-name">ingredient</label>
		</li>
		<li>
			<input type="checkbox" name="search-category-name" <?php if(isset($_POST["search-category-name"])) echo "checked=1"; ?> />
			<label for="search-category-name">category</label>
		</li>
	</ul>
	<p>
		<input type="submit" name="submit" value="Search for Recipes" />
		<input type="checkbox" name="search-by-pantry" <?php if(isset($_POST["search-by-pantry"])) echo "checked=1"; ?> />
		<label for="search-by-pantry">Only show recipes if all ingredients are available</label>
	</p>
</form>
<?php
$recipeLoaded = false;
if (isset($_GET['id'])) {
	$recipeLoaded = Recipe::loadRecipe($_GET['id']);
	if ($recipeLoaded) {
		?>
		</section>
		<section>
		<h1><?php echo $recipeLoaded->getNameHTMLSafe(); ?></h1>
		<h3>Ingredients:</h3>
		<ul>
		<?php
		foreach ($recipeLoaded->getRecipeIngredients() as $recipeIngredient) {
			echo "<li>{$recipeIngredient->getIngredient()->getNameHTMLSafe()}
				<br />{$recipeIngredient->getQuantityHTMLSafe()}</li>";
		}
		?>
		</ul>
		<h3>Instructions:</h3>
		<p><?php echo $recipeLoaded->getInstructionsHTMLSafe(); ?></p>

		<h3>Categories</h3>
		<p>
			<?php
			$categoriesArray = array();
			foreach ($recipeLoaded->getCategories() as $category) {
				$categoriesArray[] = $category->getNameHTMLSafe();
			}
			$categories = implode(",", $categoriesArray);
			if ($categories != "") {
				echo $categories;
			} else { // no categories returned
				echo "No categories listed for this recipe.";
			}
			?>
		</p>
		</section>
		<?php
	}
}

if (!$recipeLoaded) {
	?>
	<table>
		<tr>
			<th>Recipe Name</th>
			<th>Categories</th>
			<th></th>
		</tr>
		<?php
			$recipes = array();
			$options = array();

			if (isset($_POST["search-recipe-name"]))
				$options["name"] = true;

			if (isset($_POST["search-recipe-instructions"]))
				$options["instructions"] = true;

			if (isset($_POST["search-ingredient-name"]))
				$options["ingredients"] = true;

			if (isset($_POST["search-category-name"]))
				$options["categories"] = true;

			if (isset($_POST["search-by-pantry"]))
				$options["in_pantry"] = true;

			if (isset($_POST["search-string"]))
				$recipes = Recipe::searchRecipesPatternMatching($_POST["search-string"], $options);

			foreach ($recipes as $recipe) {
				$recipeName = $recipe->getNameHTMLSafe();
				$recipeCategories = $recipe->getCategories();
				$categoriesArray = array();
				foreach ($recipeCategories as $category) {
					$categoriesArray[] = $category->getNameHTMLSafe();
				}
				?>
				<tr>
						<td>
							<?php echo $recipeName; ?>
						</td>
						<td>
							<?php echo implode(",", $categoriesArray); ?>
						</td>
						<td>
							<a href="search.php?id=<?php echo $recipe->getId(); ?>">View Recipe</a>
						</td>
				</tr>
				<?php
			}
		?>
	</table>
	<?php
}
?>
</section>
</body>
<?php include("../includes/footer.php"); ?>
</html>