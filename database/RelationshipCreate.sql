CREATE TABLE CategoryRelationship(
	categoryID1 INTEGER UNSIGNED NOT NULL,
	categoryID2 INTEGER UNSIGNED NOT NULL,
	PRIMARY KEY(categoryID1, categoryID2),
	FOREIGN KEY (categoryID1) REFERENCES categories(id),
	FOREIGN KEY (categoryID2) REFERENCES categories(id)
);