CREATE TABLE IF NOT EXISTS `book_pings` (
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
);

CREATE TABLE IF NOT EXISTS `institutions` (
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

CREATE TABLE IF NOT EXISTS `users` (
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
