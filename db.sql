CREATE TABLE IF NOT EXISTS `smartt`.`users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '',
  `email` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT '',
  `device_id` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT '',
  `password` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT '',
  `counter` INT(11) NOT NULL DEFAULT '0' COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '')
ENGINE = InnoDB
AUTO_INCREMENT = 12
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci


CREATE TABLE IF NOT EXISTS `smartt`.`routes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '',
  `short_name` VARCHAR(20) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT '',
  `long_name` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '')
  ENGINE = InnoDB
  AUTO_INCREMENT = 19
  DEFAULT CHARACTER SET = utf8
  COLLATE = utf8_unicode_ci


CREATE TABLE IF NOT EXISTS `smartt`.`bus_lines` (
  `_id` VARCHAR(30) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT '',
  `line_name_el` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT '',
  `line_name_en` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT '',
  `is_circular` TINYINT(1) NOT NULL COMMENT '',
  PRIMARY KEY (`_id`)  COMMENT '')
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8
  COLLATE = utf8_unicode_ci


CREATE TABLE IF NOT EXISTS `smartt`.`user_locations` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '',
  `lat` VARCHAR(20) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT '',
  `lon` VARCHAR(20) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT '',
  `time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '',
  `userid` INT(11) NOT NULL COMMENT '',
  `routeid` VARCHAR(30) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT '',
  `direction` TINYINT(1) NULL DEFAULT NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '',
  INDEX `userid` (`userid` ASC)  COMMENT '',
  INDEX `routeid` (`routeid` ASC)  COMMENT '',
  CONSTRAINT `fk_routeid`
  FOREIGN KEY (`routeid`)
  REFERENCES `smartt`.`bus_lines` (`_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_userid`
  FOREIGN KEY (`userid`)
  REFERENCES `smartt`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8
  COLLATE = utf8_unicode_ci