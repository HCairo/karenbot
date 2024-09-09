DROP DATABASE IF EXISTS karenbot;
CREATE DATABASE karenbot;
USE karenbot;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    firstname VARCHAR(80) NOT NULL,
    lastname VARCHAR(80) NOT NULL,
    mail VARCHAR(255) UNIQUE NOT NULL,
    pswd VARCHAR(255) NOT NULL,
    level_id INT NOT NULL,
    ip_address VARCHAR(40),
    token VARCHAR(255),
    token_creation_time DATETIME
);
