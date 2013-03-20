delimiter $$

CREATE TABLE `users` (
  `inst_id` varchar(20) NOT NULL,
  `password` char(64) NOT NULL,
  `name` varchar(50) NOT NULL,
  `user_id` varchar(45) NOT NULL,
  `is_admin` binary(1) NOT NULL,
  `email` varchar(45) NOT NULL,
  `email_verified` varchar(16) NOT NULL,
  `encrip_salt` varchar(10) NOT NULL,
  `can_submit_data` binary(1) NOT NULL,
  `can_read_data` binary(1) NOT NULL,
  `user_num` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`user_num`),
  UNIQUE KEY `user_id_UNIQUE` (`user_id`),
  KEY `inst_id_idx` (`inst_id`),
  KEY `inst_id_idx1` (`inst_id`),
  CONSTRAINT `inst_id` FOREIGN KEY (`inst_id`) REFERENCES `institution` (`inst_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8$$

