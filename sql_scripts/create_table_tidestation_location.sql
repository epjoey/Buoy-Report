CREATE TABLE IF NOT EXISTS `tidestation_location` (
  `tidestationid` varchar(50) NOT NULL,
  `locationid` int(11) NOT NULL,
  `created` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tidestationid`,`locationid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
