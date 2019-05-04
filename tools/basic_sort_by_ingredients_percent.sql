-- pulled from a former iteration of this project that never went anywhere
-- tested 2019-05-04 and it seems to work
SELECT
	r.recipe_id AS rec,
	COUNT(*),
	COUNT(*) / (
		SELECT COUNT(*)
		FROM recipe_has_ingredient
		WHERE recipe_id = r.recipe_id
	) AS percent
FROM account_has_ingredient pantry
JOIN recipe_has_ingredient r
ON r.ingredient_id = pantry.ingredient_id
GROUP BY rec
ORDER BY percent DESC;
