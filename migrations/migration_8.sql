CREATE TABLE `favorites` (
  `email` varchar(255) NOT NULL,
  `locationid` int(11) NOT NULL,
  PRIMARY KEY (`email`,`locationid`)
);