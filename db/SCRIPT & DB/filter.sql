CREATE TABLE IF NOT EXISTS `DB_HRM`.`hrm_custom_filter` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `description` VARCHAR(45) NULL,
  `table_name` VARCHAR(45) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB


CREATE TABLE IF NOT EXISTS `DB_HRM`.`hrm_custom_filter_master` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `filter_name` VARCHAR(100) NULL,
  `valid` TINYINT NULL,
  `hrm_custom_filter_id` INT UNSIGNED NOT NULL,
  `users_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_hrm_custom_filter_master_hrm_custom_filter1_idx` (`hrm_custom_filter_id` ASC) VISIBLE,
  INDEX `fk_hrm_custom_filter_master_users1_idx` (`users_id` ASC) VISIBLE,
  CONSTRAINT `fk_hrm_custom_filter_master_hrm_custom_filter1`
    FOREIGN KEY (`hrm_custom_filter_id`)
    REFERENCES `DB_HRM`.`hrm_custom_filter` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_hrm_custom_filter_master_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `DB_HRM`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB


CREATE TABLE IF NOT EXISTS `DB_HRM`.`hrm_custom_filter_details` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `hrm_custom_filter_master_id` INT UNSIGNED NOT NULL,
  `ref_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_hrm_custom_filter_details_hrm_custom_filter_master1_idx` (`hrm_custom_filter_master_id` ASC) VISIBLE,
  CONSTRAINT `fk_hrm_custom_filter_details_hrm_custom_filter_master1`
    FOREIGN KEY (`hrm_custom_filter_master_id`)
    REFERENCES `DB_HRM`.`hrm_custom_filter_master` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB



