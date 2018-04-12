CREATE TABLE bookmark(
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	eventID INTEGER UNSIGNED NOT NULL,
	userID INTEGER UNSIGNED NOT NULL,
	PRIMARY KEY(created_at,eventID, userID),
	FOREIGN KEY (eventID) REFERENCES events(id),
	FOREIGN KEY (userID) REFERENCES users(id)
);