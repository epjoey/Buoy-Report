ALTER TABLE `buoy_location` ADD COLUMN `sort_order` INT(11);
ALTER TABLE `buoy_location` ADD UNIQUE (`buoyid`,`locationid`,`sort_order`);