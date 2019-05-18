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
		<label for="ratio">Only search for recipes you have all of the ingredients for.</label>
	</p>
</form>
<table>
	<tr>
		<th>Recipe Name</th>
		<th>Categories</th>
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
			?>
			<tr>
				<td>
					<?php echo $recipeName; ?>
				</td>
				<td>
					<?php echo implode(",", $recipeCategories); ?>
				</td>
			</tr>
			<?php
		}
	?>
</table>
</section>
</body>
<?php include("../includes/footer.php"); ?>
</html>