-- MySQL dump 10.13  Distrib 5.1.62, for debian-linux-gnu (i486)
--
-- Host: localhost    Database: shelvar
-- ------------------------------------------------------
-- Server version	5.1.62-0ubuntu0.10.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `book_pings`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `book_pings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `book_tag` varchar(40) NOT NULL,
  `book_call` varchar(240) NOT NULL,
  `neighbor1_tag` varchar(40) DEFAULT NULL,
  `neighbor1_call` varchar(240) DEFAULT NULL,
  `neighbor2_tag` varchar(40) DEFAULT NULL,
  `neighbor2_call` varchar(240) DEFAULT NULL,
  `ping_time` datetime NOT NULL,
  `institution` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=108082 DEFAULT CHARSET=latin1;

CREATE TABLE `institutions` (
  `inst_id` varchar(20) NOT NULL,
  `inst_num` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(140) NOT NULL,
  `admin_contact` varchar(45) NOT NULL,
  `alt_contact` varchar(500) NOT NULL,
  `inst_type` int(6) NOT NULL,
  `inst_url` varchar(100) DEFAULT NULL,
  `is_activated` binary(1) NOT NULL,
  `exp_date` datetime NOT NULL,
  `num_api_calls` int(11) NOT NULL,
  PRIMARY KEY (`inst_id`),
  KEY (`inst_num`)
);

CREATE TABLE `users` (
  `user_id` varchar(45) NOT NULL UNIQUE,
  `inst_id` varchar(20) NOT NULL,
  `user_num` int(11) NOT NULL AUTO_INCREMENT,
  `password` char(64) NOT NULL,
  `salt` char(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(45) NOT NULL,
  `email_verified` varchar(16) NOT NULL,
  `is_admin` binary(1) NOT NULL,
  `can_submit_data` binary(1) NOT NULL,
  `can_read_data` binary(1) NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY (`user_num`),
  FOREIGN KEY (inst_id) REFERENCES institutions(inst_id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-06-06 10:21:42
