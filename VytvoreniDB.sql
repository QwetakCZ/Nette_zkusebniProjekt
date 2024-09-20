-- --------------------------------------------------------
-- Hostitel:                     127.0.0.1
-- Verze serveru:                10.4.32-MariaDB - mariadb.org binary distribution
-- OS serveru:                   Win64
-- HeidiSQL Verze:               12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Exportování struktury databáze pro
CREATE DATABASE IF NOT EXISTS `ukazkovy_projekt` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci */;
USE `ukazkovy_projekt`;

-- Exportování struktury pro tabulka ukazkovy_projekt.dodavatele
CREATE TABLE IF NOT EXISTS `dodavatele` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(50) DEFAULT NULL,
  `ico` varchar(50) DEFAULT NULL,
  `telefon` varchar(50) DEFAULT NULL,
  `userId` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Export dat nebyl vybrán.

-- Exportování struktury pro tabulka ukazkovy_projekt.role
CREATE TABLE IF NOT EXISTS `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Export dat nebyl vybrán.

-- Exportování struktury pro tabulka ukazkovy_projekt.user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL DEFAULT '0',
  `email` varchar(100) NOT NULL DEFAULT '0',
  `password` varchar(255) NOT NULL DEFAULT '0',
  `userId` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Export dat nebyl vybrán.

-- Exportování struktury pro tabulka ukazkovy_projekt.user_x_role
CREATE TABLE IF NOT EXISTS `user_x_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `role_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `FK__user` (`user_id`),
  KEY `FK__role` (`role_id`),
  CONSTRAINT `FK__role` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK__user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Export dat nebyl vybrán.

-- Exportování struktury pro tabulka ukazkovy_projekt.vykup
CREATE TABLE IF NOT EXISTS `vykup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plomba` varchar(50) NOT NULL DEFAULT '0',
  `vaha` decimal(20,6) NOT NULL DEFAULT 0.000000,
  `dodavatel_id` int(11) NOT NULL DEFAULT 0,
  `userId` varchar(100) NOT NULL DEFAULT '0',
  `prodejniCena` decimal(20,6) NOT NULL DEFAULT 0.000000,
  PRIMARY KEY (`id`),
  KEY `FK__dodavatele` (`dodavatel_id`),
  CONSTRAINT `FK__dodavatele` FOREIGN KEY (`dodavatel_id`) REFERENCES `dodavatele` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Export dat nebyl vybrán.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
