<?php include_once("../src/Category.php"); ?>
<?php include_once("../src/Ingredient.php"); ?>
<!doctype html>
<html>
<head>
	<link rel="stylesheet" href="css/warnings.css" />
	<title>Admin Tools | Ingredients</title>
</head>
<body>
<header>
	<h1>Administration Tools</h1>
	<?php include("../includes/admin-tools-nav.php"); ?>
	<h2>Ingredients</h2>
</header>

	<?php // TODO: Remove ingredient from DB ?>
	<?php // TODO: Edit ingredient ?>
	<section id='create-ingredient'>
	<h1>Add A New Ingredient</h1>
	<?php
		if (isset($_POST['submit-create-ingredient'])) {
			$error_count = 0;
			if ($_POST['ingredient-name'] == "") {
				echo "<p class='error-msg'>Please include an ingredient name.</p>";
				$error_count++;
			}

			// TODO: check to see if ingredient exists already

			if ($error_count == 0) {
				// TODO: input validation
				$name = $_POST['ingredient-name'];
				$categories = array();
				if (isset($_POST['categories'])) {
					foreach ($_POST['categories'] as $id) {
						$categories[] = Category::load($id);
					}
				}

				$ingredient = Ingredient::createIngredient($name, $categories);

				if($ingredient) {
					?>
					<p>Ingredient Created!</p>
					<p>Ingredient Name: <?php echo "{$ingredient->getNameHTMLSafe()}"; ?></p>
					<p>Categories:</p>
					<ul>
					<?php
					foreach($ingredient->getCategories() as $category) {
						echo "<li>{$category->getNameHTMLSafe()}</li>";
					}
					?>
					</ul>
					<?php
				} else {
					// TODO: be more specific with reasons for failure
					echo "<p class='error-msg'>Ingredient Creation Failed</p>";
					$error_count++;
				}
			}
		} else {
			?>
			<form name="form-create-ingredient" action="admin-ingredients.php" method="post">
			<p>Ingredient Name: <input type="text" name="ingredient-name" placeholder="Ingredient Name" /></p>
			<p>Select Ingredient Categories:</p>
			<ul id="category-selection">
				<?php
					$categories = Category::loadAll();

					foreach($categories as $category) {
						?>
						<li>
							<input type="checkbox" name="categories[]" value="<?php echo "{$category->getId()}"; ?>" />
							<label for="<?php echo "{$category->getId()}"; ?>"><?php echo "{$category->getNameHTMLSafe()}"; ?>
						</li>
						<?php
					}
				?>
			</ul> <!-- end of category selection -->
			<p><input type="submit" name="submit-create-ingredient" value="Create Ingredient" /></p>
			</form> <!-- end of form 'form-create-ingredient' -->
			</section> <!-- end section 'create-ingredient' -->
			<?php
		}
	?>
</body>
</html>