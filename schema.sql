CREATE DATABASE yeticave
	DEFAULT CHARACTER SET utf8
	DEFAULT COLLATE utf8_general_ci;

USE yeticave;


CREATE TABLE categories (
  id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
  name CHAR(64) NOT NULL,
  symbol_code CHAR(32) NOT NULL
);


CREATE TABLE lots (
  id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
  date_create TIMESTAMP NOT NULL,
  name CHAR(64) NOT NULL,
  description TEXT,
  url_image CHAR(128) NOT NULL,
  start_price INT NOT NULL,
  date_end TIMESTAMP NOT NULL,
  step_price INT NOT NULL,
  author INT,
  winner INT,  
  category INT/*,
  FOREIGN KEY (author)  REFERENCES users (id),
  FOREIGN KEY (winner)  REFERENCES users (id),
  FOREIGN KEY (category)  REFERENCES categories (id)*/
);

CREATE TABLE bets (
  id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
  date_create TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  price INT NOT NULL,
  user INT,
  lot INT/*,
  FOREIGN KEY (user)  REFERENCES users (id),
  FOREIGN KEY (lot)  REFERENCES lots (id)*/
);

CREATE TABLE users (
  id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
  date_registered TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  email VARCHAR(254) NOT NULL UNIQUE,
  name CHAR(64) NOT NULL,
  password TEXT NOT NULL,
  created_lot INT,
  bet INT/*,
  FOREIGN KEY (created_lot)  REFERENCES lots (id),
  FOREIGN KEY (bet)  REFERENCES bets (id)*/
);

/*CREATE TABLE lot_author (
  id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
  date_create TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  price INT NOT NULL,
  user INT,
  lot INT/*,
  FOREIGN KEY (user)  REFERENCES users (id),
  FOREIGN KEY (lot)  REFERENCES lots (id)
);*/

/*ALTER TABLE lots ADD COLUMN author INTEGER REFERENCES users(id);*/

CREATE INDEX name_category ON categories(name);
CREATE INDEX date_create ON lots(date_create);
CREATE INDEX name_lot ON lots(name);
CREATE INDEX date_end ON lots(date_end);
/*CREATE INDEX password ON users(password);*/

ALTER TABLE lots ADD FOREIGN KEY (author)  REFERENCES users (id);
ALTER TABLE lots ADD FOREIGN KEY (winner)  REFERENCES users (id);
ALTER TABLE lots ADD FOREIGN KEY (category)  REFERENCES users (id);
ALTER TABLE bets ADD FOREIGN KEY (user)  REFERENCES users (id);
ALTER TABLE bets ADD FOREIGN KEY (lot)  REFERENCES lots (id);
ALTER TABLE users ADD FOREIGN KEY (created_lot)  REFERENCES lots (id);
ALTER TABLE users ADD FOREIGN KEY (bet)  REFERENCES bets (id);

  /*FOREIGN KEY (author)  REFERENCES users (id);
  FOREIGN KEY (winner)  REFERENCES users (id);
  FOREIGN KEY (category)  REFERENCES categories (id);

  FOREIGN KEY (user)  REFERENCES users (id);
  FOREIGN KEY (lot)  REFERENCES lots (id);

  FOREIGN KEY (created_lot)  REFERENCES lots (id);
  FOREIGN KEY (bet)  REFERENCES bets (id);*/