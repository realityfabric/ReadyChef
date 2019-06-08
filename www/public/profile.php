<?php
include_once("../config/config.php");
include_once("../src/Ingredient.php");
include_once("../src/User.php");
session_start();
?>
<!doctype html>
<html>
<head>
	<?php include("../includes/layout.php"); ?>
	<title>Profile</title>
</head>
<?php include("../includes/header.php"); ?>
<body>
<?php
if (isset($_POST["submitRemove"])) {
	$ingredient = Ingredient::loadIngredient($_POST["ingredientId"]);
	$result = $_SESSION['user']->removeIngredientFromPantry($ingredient);

	if ($result) {
		echo "<p>Ingredient ({$ingredient->getName()}) was removed successfully.</p>";
	}
	else { // TODO: check to see if ingredients are already in the pantry
		echo "<p>Failed to remove the ingredient ({$ingredient->getName()}) from your pantry.</p>";
	}
}

if (isset($_POST["submitAdd"])) {
	$ingredient = Ingredient::loadIngredient($_POST["ingredientId"]);
	$result = $_SESSION['user']->addIngredientToPantry($ingredient);

	if ($result) {
		echo "<p>Ingredient ({$ingredient->getName()}) was added successfully.</p>";
	}
	else { // TODO: check to see if ingredients are already in the pantry
		echo "<p>Failed to add the ingredient ({$ingredient->getName()}) to your pantry.</p>";
	}
}
?>
	<section>
		<h1>Ingredients in your pantry</h1>
			<table>
				<tr>
					<th>Ingredient</th>
					<th>Date Added</th>
					<th>Categories</th>
					<th></th> <!-- this is intentionally blank because there are 4 columns -->
				</tr>
			<?php
			$ingredients = $_SESSION['user']->getIngredientsFromPantry();

			foreach ($ingredients as $ingredient) {
				?>
				<form name="removeIngredient" action="profile.php" method="post">
					<tr>
						<td>
							<?php
							echo $ingredient[0]->getName();
							?>
						</td>
						<td>
							<?php
							echo $ingredient[1];
							?>
						</td>
						<td>
							<?php
							$categories = $ingredient[0]->getCategories();
							$arr = array();
							foreach ($categories as $category) {
								$arr[] = $category->getName();
							}
							echo implode(",", $arr);
							?>
						</td>
						<td>
						<input type="hidden" name="ingredientId" value="<?php echo $ingredient[0]->getId(); ?>" />
						<input type="submit" name="submitRemove" value="<?php echo "Remove {$ingredient[0]->getName()}"; ?>" />
						</td>
					</tr>
				</form>
				<?php
			}
			?>
		</table>
	</section>
	<section>
	<h1>All Ingredients</h1>
	<table>
		<tr>
			<th>Ingredient</th>
			<th>Categories</th>
			<th></th> <!-- this is intentionally blank because there are 3 columns -->
		</tr>
	<?php
	$ingredients = Ingredient::getAll();

	foreach ($ingredients as $ingredient) {
		?>
		<tr>
			<form name="addIngredient" action="profile.php" method="post">
				<td>
					<?php
					echo $ingredient->getName();
					?>
				</td>
				<td>
					<?php
					$categories = $ingredient->getCategories();
					$arr = array();
					foreach ($categories as $category) {
						$arr[] = $category->getName();
					}
					echo implode(",", $arr);
					?>
				</td>
				<td>
					<input type="hidden" name="ingredientId" value="<?php echo $ingredient->getId(); ?>" />
					<input type="submit" name="submitAdd" value="<?php echo "Add {$ingredient->getName()}"; ?>" />
				</td>
			</form>
		</tr>
		<?php
	}
	echo "</table>";
	?>
	</section>
</body>
<?php include("../includes/footer.php"); ?>
</html>