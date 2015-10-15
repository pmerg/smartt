# ************************************************************
# Sequel Pro SQL dump
# Version 4499
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 83.212.116.159 (MySQL 5.5.44-0ubuntu0.14.04.1)
# Database: smartt
# Generation Time: 2015-10-15 13:21:18 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table bus_lines
# ------------------------------------------------------------

DROP TABLE IF EXISTS `bus_lines`;

CREATE TABLE `bus_lines` (
  `_id` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `line_name_el` text COLLATE utf8_unicode_ci NOT NULL,
  `line_name_en` text COLLATE utf8_unicode_ci NOT NULL,
  `is_circular` tinyint(1) NOT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table bus_stops
# ------------------------------------------------------------

DROP TABLE IF EXISTS `bus_stops`;

CREATE TABLE `bus_stops` (
  `s_id` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `name_el` text COLLATE utf8_unicode_ci NOT NULL,
  `name_en` text COLLATE utf8_unicode_ci NOT NULL,
  `street_el` text COLLATE utf8_unicode_ci NOT NULL,
  `street_en` text COLLATE utf8_unicode_ci NOT NULL,
  `m_id` int(11) NOT NULL,
  `lat` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lon` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `waypoint` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`s_id`),
  KEY `mid` (`m_id`),
  CONSTRAINT `fk_m_id` FOREIGN KEY (`m_id`) REFERENCES `municipalities` (`_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table bus_times
# ------------------------------------------------------------

DROP TABLE IF EXISTS `bus_times`;

CREATE TABLE `bus_times` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `line_id` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `direction` int(11) NOT NULL,
  `day` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `minute` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_line_idx` (`line_id`),
  CONSTRAINT `fk_line` FOREIGN KEY (`line_id`) REFERENCES `bus_lines` (`_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table line_stops
# ------------------------------------------------------------

DROP TABLE IF EXISTS `line_stops`;

CREATE TABLE `line_stops` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `line_id` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `stop_id` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `direction_flag` int(11) DEFAULT NULL,
  `line_waypoint` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `lineid` (`line_id`),
  KEY `stopid` (`stop_id`),
  CONSTRAINT `fk_line_id` FOREIGN KEY (`line_id`) REFERENCES `bus_lines` (`_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_stop_id` FOREIGN KEY (`stop_id`) REFERENCES `bus_stops` (`s_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table municipalities
# ------------------------------------------------------------

DROP TABLE IF EXISTS `municipalities`;

CREATE TABLE `municipalities` (
  `_id` int(11) NOT NULL,
  `municipality_name_el` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `municipality_name_en` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`_id`),
  UNIQUE KEY `municipality_name_en_index` (`municipality_name_en`),
  UNIQUE KEY `municipality_name_el_index` (`municipality_name_el`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table route_waypoints
# ------------------------------------------------------------

DROP TABLE IF EXISTS `route_waypoints`;

CREATE TABLE `route_waypoints` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `route_id` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `direction` int(11) NOT NULL,
  `waypoints_json` longtext COLLATE utf8_unicode_ci NOT NULL,
  `lat` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lon` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `route_id` (`route_id`),
  CONSTRAINT `fk_route_id` FOREIGN KEY (`route_id`) REFERENCES `bus_lines` (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table user_favs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_favs`;

CREATE TABLE `user_favs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `route_id` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `dir` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `route_id` (`route_id`),
  KEY `user_id_2` (`user_id`),
  CONSTRAINT `user_favs_ibfk_2` FOREIGN KEY (`route_id`) REFERENCES `bus_lines` (`_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `user_favs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table user_locations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_locations`;

CREATE TABLE `user_locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lat` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `lon` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userid` int(11) NOT NULL,
  `routeid` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `direction` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `routeid` (`routeid`),
  CONSTRAINT `fk_routeid` FOREIGN KEY (`routeid`) REFERENCES `bus_lines` (`_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_userid` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table user_ratings
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_ratings`;

CREATE TABLE `user_ratings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `route_id` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `direction` tinyint(1) NOT NULL,
  `arrival_time` int(1) NOT NULL DEFAULT '0',
  `comfort` int(1) NOT NULL DEFAULT '0',
  `route_duration` int(1) NOT NULL DEFAULT '0',
  `driver_rating` int(1) NOT NULL DEFAULT '0',
  `comment` text COLLATE utf8_unicode_ci,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_user_id_idx` (`user_id`),
  CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `device_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `counter` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
