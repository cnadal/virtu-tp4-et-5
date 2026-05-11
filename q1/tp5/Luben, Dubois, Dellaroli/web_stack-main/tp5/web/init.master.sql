CREATE USER IF NOT EXISTS 'replicator'@'%' IDENTIFIED BY 'replicator_password';
GRANT REPLICATION SLAVE ON *.* TO 'replicator'@'%';
FLUSH PRIVILEGES;

CREATE TABLE IF NOT EXISTS users (
	id INT AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(80) NOT NULL UNIQUE,
	full_name VARCHAR(120) NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS cars (
	id INT AUTO_INCREMENT PRIMARY KEY,
	user_id INT NOT NULL,
	brand VARCHAR(80) NOT NULL,
	model VARCHAR(80) NOT NULL,
	registration VARCHAR(20) NOT NULL UNIQUE,
	year SMALLINT,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	CONSTRAINT fk_cars_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT IGNORE INTO users (id, username, full_name) VALUES
	(1, 'alice', 'Alice Martin'),
	(2, 'bob', 'Bob Dupont'),
	(3, 'charlie', 'Charlie Laurent');

INSERT IGNORE INTO cars (user_id, brand, model, registration, year) VALUES
	(1, 'Peugeot', '208', 'AA-101-AA', 2021),
	(1, 'Renault', 'Clio', 'BB-202-BB', 2019),
	(2, 'Tesla', 'Model 3', 'CC-303-CC', 2022),
	(3, 'Volkswagen', 'Golf', 'DD-404-DD', 2018);