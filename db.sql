DROP DATABASE IF EXISTS karenbot;
CREATE DATABASE karenbot;
USE karenbot;

CREATE TABLE level (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(30) NOT NULL
);

-- Insert values into the level table
INSERT INTO level (name) VALUES ('Technicien niveau 0-1');
INSERT INTO level (name) VALUES ('Technicien niveau 2');

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    firstname VARCHAR(80) NOT NULL,
    lastname VARCHAR(80) NOT NULL,
    mail VARCHAR(255) UNIQUE NOT NULL,
    pswd VARCHAR(255) NOT NULL,
    level_id INT NOT NULL,
    is_admin TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (level_id) REFERENCES level(id) ON DELETE CASCADE
);