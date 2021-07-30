ALTER TABLE `location` ADD COLUMN `stormsurfingurl` varchar(255);
ALTER TABLE `report` CHANGE COLUMN `obsdate` `obsdate` BIGINT;
ALTER TABLE `buoydata` CHANGE COLUMN `gmttime` `gmttime` BIGINT;