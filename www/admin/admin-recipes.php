<?php include_once("../src/Recipe.php"); ?>
<?php include_once("../src/Ingredient.php"); ?>
<!doctype html>
<html>
<head>
	<link rel="stylesheet" href="css/warnings.css" />
	<title>Admin Tools | Recipes</title>
</head>
<body>
<header>
	<h1>Administration Tools</h1>
	<?php include("../includes/admin-tools-nav.php"); ?>
	<h2>Recipes</h2>
</header>
<?php // TODO: edit recipes ?>
<?php // TODO: remove recipes ?>
<section name='create-recipe'>
	<h1>Add A New Recipe</h1>
	<?php
		if (isset($_POST['submit-create-recipe'])) {
			$error_count = 0;
			if ($_POST['recipe-name'] == "") {
				echo "<p class='error-msg'>Please include a recipe name.</p>";
				$error_count++;
			}
			if ($_POST['recipe-instructions'] == "") {
				echo "<p class='error-msg'>Please include recipe instructions.</p>";
				$error_count++;
			}
			if (!isset($_POST['recipe-ingredients'])) {
				echo "<p class='error-msg'>Please include ingredients for this recipe.</p>";
				$error_count++;
			}

			// TODO: check to see if recipe exists already

			if ($error_count == 0) {
				// TODO: input validation
				$name = $_POST['recipe-name'];
				$instructions = $_POST['recipe-instructions'];
				$recipeIngredients = array();
				if (isset($_POST['recipe-ingredients'])) { // this should be set (checked previously) but checking again for good measure
					foreach($_POST['recipe-ingredients'] as $recipeIngredient) {
						if (isset($recipeIngredient['ingredient-id'])) {
							$ingredient = Ingredient::load($recipeIngredient['ingredient-id']);
							$recipeIngredients[] = new RecipeIngredient($ingredient, $recipeIngredient['quantity']);
						}
					}
				}
				$categories = array();
				if (isset($_POST['categories'])) {
					foreach ($_POST['categories'] as $id) {
						$categories[] = Category::load($id);
					}
				}

				// should return false on failure and a new Recipe object on success
				$recipe = Recipe::createRecipe($name, $instructions, $recipeIngredients, $categories);

				if($recipe) {
					?>
					<p>Recipe Created!</p>
					<p>Recipe Name: <?php echo "{$recipe->getNameHTMLSafe()}"; ?></p>
					<p>Recipe Instructions:</p>
					<?php echo "{$recipe->getInstructionsHTMLSafe()}"; ?>
					<p>Ingredients:</p>
					<ul>
					<?php
					foreach($recipe->getRecipeIngredients() as $recipeIngredient) {
						echo "<li>{$recipeIngredient->getIngredient()->getNameHTMLSafe()}<br/>";
						echo "{$recipeIngredient->getQuantityHTMLSafe()}</li>";

					}
					?>
					</ul>
					<p>Categories:</p>
					<ul>
					<?php
					foreach($recipe->getCategories() as $category) {
						echo "<li>{$category->getNameHTMLSafe()}</li>";
					}
					?>
					</ul>
					<?php
				} else {
					// TODO: be more specific with reasons for failure
					echo "<p class='error-msg'>Recipe Creation Failed</p>";
					$error_count++;
				}
			}
		} else {
			?>
			<?php // form has an id because it's necessary to make the textarea element work ?>
			<form name="form-create-recipe" id="form-create-recipe" action="admin-recipes.php" method="post">
			<p>Recipe Name: <input type="text" name="recipe-name" placeholder="Recipe Name" /></p>
			<p>Recipe Instructions:</p>
			<textarea form="form-create-recipe" name="recipe-instructions" placeholder="Put Recipe Instructions Here"></textarea>
			<p>Select Recipe Ingredients:</p>
			<ul id="ingredient-selection">
			<?php
				$ingredients = Ingredient::loadAll();
				$i = 0;
				foreach($ingredients as $ingredient) {
					?>
					<li>
						<input type="checkbox" name="recipe-ingredients[<?php echo "{$i}"; ?>][ingredient-id]" value="<?php echo "{$ingredient->getId()}"; ?>" />
						<label for="recipe-ingredients[<?php echo "{$i}"; ?>][ingredient-id]"><?php echo "{$ingredient->getNameHTMLSafe()}"; ?></label>
						<input type="text" name="recipe-ingredients[<?php echo "{$i}"; ?>][quantity]" placeholder="Quantity" />
					</li>
					<?php
					$i++;
				}
			?>
			</ul>
			<p>Select Recipe Categories:</p>
			<ul id="category-selection">
				<?php
					$categories = Category::loadAll();
					foreach($categories as $category) {
						?>
						<li>
							<input type="checkbox" name="categories[]" value="<?php echo "{$category->getId()}"; ?>" />
							<label for="<?php echo "{$category->getId()}"; ?>"><?php echo "{$category->getNameHTMLSafe()}"; ?></label>
						</li>
						<?php
					}
				?>
			</ul> <!-- end of category selection -->
			<p><input type="submit" name="submit-create-recipe" value="Create Recipe" /></p>
			</form> <!-- end of form 'form-create-ingredient' -->
			</section> <!-- end section 'create-ingredient' -->
			<?php
		}
	?>


</section> <!-- end section 'create-recipe' -->
</body>
</html>