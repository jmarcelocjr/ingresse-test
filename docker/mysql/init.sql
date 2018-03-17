use ingresse-test;

CREATE TABLE `users` (
	id INT PRIMARY KEY AUTO_INCREMENT,
	createdAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	login VARCHAR(25) NOT NULL,
	password VARCHAR(100) NOT NULL,
	name VARCHAR(100) NULL DEFAULT '',
	email VARCHAR(100) NULL  DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;