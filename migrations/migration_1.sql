ALTER TABLE `location` ADD COLUMN `latitude` DECIMAL(10, 8);
ALTER TABLE `location` ADD COLUMN `longitude` DECIMAL(11, 8);

UPDATE `location` SET `latitude`=20.798, `longitude`=-156.332 WHERE `locname` = 'Peahi/Jaws';
