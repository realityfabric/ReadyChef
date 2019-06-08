<?php
include_once("../config/config.php");
include_once("../src/Recipe.php");
include_once("../src/User.php");
session_start();
?>
<!doctype html>
<html>
<head>
	<title>Search</title>
</head>
<?php include("../includes/header.php"); ?>
<body>
<section>
<form name="search" action="search.php" method="post">
	<p>
		<input type="submit" name="submit" value="Search for Recipes" />
		<input type="checkbox" name="ratio"
			<?php
			if(isset($_POST["ratio"])) {
				echo "checked=1";
			}
			?>
		/>
		<label for="ratio">Get recipes you have all of the ingredients for.</label>
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
		<h1><?php echo $recipeLoaded->getName(); ?></h1>
		<h3>Ingredients:</h3>
		<ul>
		<?php
		foreach ($recipeLoaded->getIngredients() as $ingredient) {
			echo "<li>{$ingredient['ingredient']->getName()}
				<br />{$ingredient['quantity']}</li>";
		}
		?>
		</ul>
		<h3>Instructions:</h3>
		<p><?php echo $recipeLoaded->getInstructions(); ?></p>

		<h3>Categories</h3>
		<p>
			<?php
			$categoriesArray = array();
			foreach ($recipeLoaded->getCategories() as $category) {
				$categoriesArray[] = $category->getName();
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
			$userId = $_SESSION['user']->getId();
			$ratio = 0;
			if (isset($_POST["ratio"])) {
				$ratio = 1;
			}
			$recipes = Recipe::searchByDBPantry($userId, $ratio);

			foreach ($recipes as $recipe) {
				$recipeName = $recipe->getName();
				$recipeCategories = $recipe->getCategories();
				$categoriesArray = array();
				foreach ($recipeCategories as $category) {
					$categoriesArray[] = $category->getName();
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
							<a href="search.php?id=<?php echo $recipe->getId(); ?>">Recipe</a>
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