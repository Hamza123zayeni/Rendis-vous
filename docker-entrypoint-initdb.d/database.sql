CREATE TABLE IF NOT EXISTS `webuser` (
    `email` VARCHAR(255) PRIMARY KEY,
    `usertype` VARCHAR(10) NOT NULL
);

CREATE TABLE IF NOT EXISTS `patient` (
    `pid` INT AUTO_INCREMENT PRIMARY KEY,
    `pemail` VARCHAR(255) NOT NULL UNIQUE,
    `pname` VARCHAR(255) NOT NULL,
    `ppassword` VARCHAR(255) NOT NULL,
    `paddress` VARCHAR(255),
    `pnic` VARCHAR(20),
    `pdob` DATE,
    `ptel` VARCHAR(20),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`pemail`) REFERENCES `webuser`(`email`)
);

CREATE TABLE IF NOT EXISTS `doctor` (
    `did` INT AUTO_INCREMENT PRIMARY KEY,
    `demail` VARCHAR(255) NOT NULL UNIQUE,
    `dname` VARCHAR(255) NOT NULL,
    `dpassword` VARCHAR(255) NOT NULL,
    `dspec` VARCHAR(255),
    `dtel` VARCHAR(20),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`demail`) REFERENCES `webuser`(`email`)
);