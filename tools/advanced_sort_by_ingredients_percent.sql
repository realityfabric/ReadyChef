-- pulled from a former iteration of this project that never went anywhere
-- tested 2019-05-04 and it seems to work
SELECT
	recipe.name AS name,
	COUNT(*) / (
		SELECT COUNT(*)
		FROM recipe_has_ingredient
		WHERE recipe_id = r.recipe_id
	) AS ratio
FROM account_has_ingredient
	JOIN recipe_has_ingredient r
		ON account_has_ingredient.ingredient_id = r.ingredient_id
	JOIN recipe
		ON recipe.id = r.recipe_id
GROUP BY recipe.name, r.recipe_id
HAVING (COUNT(*) / (SELECT COUNT(*) FROM recipe_has_ingredient WHERE recipe_id = r.recipe_id)) = 1
ORDER BY ratio DESC;
