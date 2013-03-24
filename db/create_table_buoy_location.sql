CREATE TABLE IF NOT EXISTS `buoy_location` (
  `buoyid` varchar(50) NOT NULL,
  `locationid` int(11) NOT NULL,
  `created` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`buoyid`,`locationid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
