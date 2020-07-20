CREATE DATABASE yeticave
	DEFAULT CHARACTER SET utf8
	DEFAULT COLLATE utf8_general_ci;

USE yeticave;


CREATE TABLE categories (
  id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
  name CHAR(64) NOT NULL,
  symbol_code CHAR(32) NOT NULL
);

INSERT INTO categories (name, symbol_code) VALUES ('Доски и лыжи', 'boards');
INSERT INTO categories (name, symbol_code) VALUES ('Крепления', 'attachment');
INSERT INTO categories (name, symbol_code) VALUES ('Ботинки', 'boots');
INSERT INTO categories (name, symbol_code) VALUES ('Одежда', 'clothing');
INSERT INTO categories (name, symbol_code) VALUES ('Инструменты', 'tools');
INSERT INTO categories (name, symbol_code) VALUES ('Разное', 'other');


CREATE TABLE lots (
  id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
  date_create TIMESTAMP NOT NULL,
  name CHAR(64) NOT NULL,
  description TEXT NOT NULL,
  url_image CHAR(128) NOT NULL,
  start_price INT NOT NULL,
  date_end TIMESTAMP NOT NULL,
  step_price INT NOT NULL,
  author INT NOT NULL,
  winner INT,  
  category INT NOT NULL
);

CREATE TABLE bets (
  id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
  date_create TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  price INT NOT NULL,
  user INT NOT NULL,
  lot INT NOT NULL
);

CREATE TABLE users (
  id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
  date_registered TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  email VARCHAR(254) NOT NULL UNIQUE,
  name CHAR(64) NOT NULL,
  password TEXT NOT NULL,
  contact TEXT NOT NULL
);


CREATE INDEX name_category ON categories(name);
CREATE INDEX date_create ON lots(date_create);
CREATE INDEX name_lot ON lots(name);
CREATE INDEX date_end ON lots(date_end);
CREATE INDEX email_index ON users(email);

ALTER TABLE lots ADD FOREIGN KEY (author)  REFERENCES users (id);
ALTER TABLE lots ADD FOREIGN KEY (winner)  REFERENCES users (id);
ALTER TABLE lots ADD FOREIGN KEY (category)  REFERENCES users (id);
ALTER TABLE bets ADD FOREIGN KEY (user)  REFERENCES users (id);
ALTER TABLE bets ADD FOREIGN KEY (lot)  REFERENCES lots (id);

