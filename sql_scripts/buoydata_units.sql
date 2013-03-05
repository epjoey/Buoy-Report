//manually set all columns to varchar 50
UPDATE `buoydata` SET `swellheight` = ROUND(`swellheight`/3.28, 2);
  
UPDATE `buoydata` SET `windspeed` = ROUND(`windspeed`/2.237, 2);

ALTER TABLE `buoydata` ADD COLUMN `watertemp` varchar(50) DEFAULT NULL;

ALTER TABLE `tidedata` ADD COLUMN `predictedTide` varchar(50) DEFAULT NULL;
ALTER TABLE `tidedata` ADD COLUMN `tideRise` TINYINT DEFAULT NULL;
UPDATE `tidedata` SET `tideRise` = `tideres`/ABS(`tideres`);
ALTER TABLE `tidedata` DROP COLUMN `tideres`;
