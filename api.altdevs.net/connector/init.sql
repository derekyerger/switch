CREATE USER connectoruser IDENTIFIED BY 'FerpyDerpenstein';

CREATE DATABASE IF NOT EXISTS connector;

GRANT ALL PRIVILEGES ON connector.* TO connectoruser;

USE connector;

DROP TABLE IF EXISTS `requests`;

CREATE TABLE `requests` (
  `ID` int(5) NOT NULL AUTO_INCREMENT,
  `time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `IP` varchar(32) DEFAULT NULL,
  `nonce` varchar(96) DEFAULT NULL,
  `internalIP` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`ID`)
);

