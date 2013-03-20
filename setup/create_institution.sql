delimiter $$

CREATE TABLE `institution` (
  `inst_id` varchar(20) NOT NULL,
  `name` varchar(140) NOT NULL,
  `admin_contact` varchar(45) NOT NULL,
  `inst_type` int(6) NOT NULL,
  `inst_size` int(6) NOT NULL,
  `is_activated` binary(1) NOT NULL,
  `exp_date` datetime NOT NULL,
  `num_api_calls` int(11) NOT NULL,
  `inst_num` int(11) NOT NULL AUTO_INCREMENT,
  `alt_contact` varchar(500) NOT NULL,
  `inst_url` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`inst_num`),
  UNIQUE KEY `inst_id_UNIQUE` (`inst_id`),
  KEY `inst_id_idx` (`inst_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8$$

