CREATE DATABASE yeticave
	DEFAULT CHARACTER SET utf8
	DEFAULT COLLATE utf8_general_ci;

USE yeticave;

CREATE TABLE categories (
  id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
  name VARCHAR (64) NOT NULL,
  symbol_code VARCHAR (32) NOT NULL
);

CREATE TABLE lots (
  id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
  date_create DATETIME NOT NULL,
  name VARCHAR (64) NOT NULL,
  description TEXT NOT NULL,
  url_image VARCHAR (128) NOT NULL,
  start_price INT NOT NULL,
  date_end DATETIME NOT NULL,
  step_price INT NOT NULL,
  author_id INT NOT NULL,
  winner_id INT,
  category_id INT NOT NULL
);

CREATE TABLE bets (
  id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
  date_create DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  price INT NOT NULL,
  user_id INT NOT NULL,
  lot_id INT NOT NULL
);

CREATE TABLE users (
  id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
  date_registered DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  email VARCHAR (254) NOT NULL UNIQUE,
  name VARCHAR (64) NOT NULL,
  password CHAR (60) NOT NULL,
  contact TEXT NOT NULL
);

CREATE INDEX name_category ON categories(name);
CREATE INDEX date_create ON lots(date_create);
CREATE INDEX name_lot ON lots(name);
CREATE INDEX date_end ON lots(date_end);
CREATE INDEX email_index ON users(email);

CREATE FULLTEXT INDEX yeticave_ft_search_lot ON lots(name, description);

ALTER TABLE lots ADD FOREIGN KEY (author_id)  REFERENCES users (id);
ALTER TABLE lots ADD FOREIGN KEY (winner_id)  REFERENCES users (id);
ALTER TABLE lots ADD FOREIGN KEY (category_id)  REFERENCES categories (id);
ALTER TABLE bets ADD FOREIGN KEY (user_id)  REFERENCES users (id);
ALTER TABLE bets ADD FOREIGN KEY (lot_id)  REFERENCES lots (id);

