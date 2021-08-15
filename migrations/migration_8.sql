CREATE TABLE `favorites` (
  `email` varchar(255) NOT NULL,
  `locationid` int(11) NOT NULL,
  PRIMARY KEY (`email`,`locationid`)
);

ALTER DATABASE br CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

ALTER TABLE report CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE report MODIFY text TEXT CHARSET utf8mb4;
