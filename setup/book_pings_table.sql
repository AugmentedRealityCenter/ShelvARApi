CREATE TABLE IF NOT EXISTS `book_pings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `book_tag` varchar(40) NOT NULL,
  `book_call` varchar(240) NOT NULL,
  `neighbor1_tag` varchar(40) DEFAULT NULL,
  `neighbor1_call` varchar(240) DEFAULT NULL,
  `neighbor2_tag` varchar(40) DEFAULT NULL,
  `neighbor2_call` varchar(240) DEFAULT NULL,
  `ping_time` datetime NOT NULL,
  `user_id` varchar(40) DEFAULT NULL,
  `inst_id` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `user_id_bp` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `inst_id_bp` FOREIGN KEY (`inst_id`) REFERENCES `institutions` (`inst_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
);
