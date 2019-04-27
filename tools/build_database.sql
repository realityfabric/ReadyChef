-- BUILD THE DATABASE

CREATE DATABASE readychef;

USE readychef;

-- USER AUTHENTICATION
CREATE TABLE account (
	id SERIAL PRIMARY KEY,
	username VARCHAR(255) NOT NULL,
	hash VARCHAR(64),
	salt VARCHAR(5),
	password_set DATE DEFAULT NOW()
);

CREATE TABLE login_history (
	user_id INTEGER REFERENCES account (id),
	success BOOL NOT NULL DEFAULT (FALSE),
	login_time TIMESTAMP DEFAULT NOW()
);

-- COOKBOOK
CREATE TABLE ingredient (
	id SERIAL PRIMARY KEY,
	name VARCHAR(100) NOT NULL
);

CREATE TABLE recipe (
	id SERIAL PRIMARY KEY,
	name VARCHAR(100) NOT NULL,
	instructions TEXT NOT NULL
);

CREATE TABLE category (
	id SERIAL PRIMARY KEY,
	name VARCHAR(100) NOT NULL
);

CREATE TABLE recipe_has_ingredient (
	recipe_id INT REFERENCES recipe(id),
	ingredient_id INT REFERENCES ingredient(id),
	quantity VARCHAR(100)
);

CREATE TABLE ingredient_has_category (
	ingredient_id INT REFERENCES ingredient(id),
	category_id INT REFERENCES category(id)
);

CREATE TABLE recipe_has_category (
	recipe_id INT REFERENCES recipe(id),
	category_id INT REFERENCES category(id)
);

-- PANTRY
CREATE TABLE account_has_ingredient (
	account_id INT REFERENCES account(id),
	ingredient_id INT REFERENCES ingredient(id),
	date_purchased DATE DEFAULT NOW()
);
