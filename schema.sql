CREATE DATABASE yeticave
	DEFAULT CHARACTER SET utf8
	DEFAULT COLLATE utf8_general_ci;

USE yeticave;


CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name CHAR(64),
  symbol_code CHAR(32)
);

CREATE TABLE lots (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date_create TIMESTAMP,
  name CHAR(64),
  description CHAR(128),
  url_image CHAR(128),
  start_price DECIMAL,
  date_end TIMESTAMP,
  step_price DECIMAL,
  author INT,
  winner INT,
  category INT
);

CREATE TABLE bets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date_create TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  price DECIMAL NOT NULL,
  user INT,
  lot INT
);

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date_registered TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  email VARCHAR(128) NOT NULL UNIQUE,
  name CHAR(64) NOT NULL UNIQUE,
  password CHAR(64),
  created_lot INT,
  bet INT
);

CREATE INDEX name_category ON categories(name);
CREATE INDEX date_create ON lots(date_create);
CREATE INDEX name_lot ON lots(name);
CREATE INDEX date_end ON lots(date_end);
CREATE INDEX password ON users(password);