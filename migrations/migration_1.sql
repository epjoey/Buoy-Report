ALTER TABLE `location` ADD COLUMN `latitude` DECIMAL(10, 3);
ALTER TABLE `location` ADD COLUMN `longitude` DECIMAL(11, 3);

UPDATE `location` SET `latitude`=20.798, `longitude`=-156.332 WHERE `locname` = 'Peahi/Jaws';

ALTER TABLE `location` DROP COLUMN `creator`;
ALTER TABLE `location` ADD COLUMN `email` varchar(255);
ALTER TABLE `location` CHANGE COLUMN `locname` `name` varchar(255) NOT NULL;
