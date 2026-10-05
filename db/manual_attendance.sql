CREATE TABLE IF NOT EXISTS `qfl_hrm`.`hrm_manual_attendance_data` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `device_no` VARCHAR(45) NULL,
  `employee_code` VARCHAR(55) NULL,
  `punch_date` DATE NULL,
  `punch_time` TIME NULL,
  `hrm_location_id` INT NOT NULL,
  `is_new` INT NOT NULL,
  `hrm_employee_id` INT NOT NULL,
  `data_from` INT NULL COMMENT '1_for_device\n2_for_manual',
  `valid` INT NOT NULL DEFAULT 1,
  `hrm_attendance_raw_data_id` INT NULL,
  `comment` VARCHAR(200) NULL,
  `entry_status` TINYTEXT NULL,
  `users_id` INT NOT NULL,
  `created_at` DATETIME NOT NULL,
  `approved_at` DATETIME NULL,
  `approved_by` INT NULL,
  `osd_time_status` TINYINT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_hrm_attendance_raw_data_hrm_location1_idx` (`hrm_location_id` ASC),
  INDEX `fk_hrm_attendance_raw_data_hrm_employee1_idx` (`hrm_employee_id` ASC),
  INDEX `punch_date` (`punch_date` ASC),
  INDEX `fk_hrm_manual_attendance_data_hrm_attendance_raw_data1_idx` (`hrm_attendance_raw_data_id` ASC),
  INDEX `fk_hrm_manual_attendance_data_users1_idx` (`users_id` ASC),
  INDEX `fk_hrm_manual_attendance_data_users2_idx` (`approved_by` ASC),
  CONSTRAINT `fk_hrm_attendance_raw_data_hrm_location10`
    FOREIGN KEY (`hrm_location_id`)
    REFERENCES `qfl_hrm`.`hrm_location` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_hrm_attendance_raw_data_hrm_employee10`
    FOREIGN KEY (`hrm_employee_id`)
    REFERENCES `qfl_hrm`.`hrm_employee` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_hrm_manual_attendance_data_hrm_attendance_raw_data1`
    FOREIGN KEY (`hrm_attendance_raw_data_id`)
    REFERENCES `qfl_hrm`.`hrm_attendance_raw_data` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_hrm_manual_attendance_data_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `qfl_hrm`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_hrm_manual_attendance_data_users2`
    FOREIGN KEY (`approved_by`)
    REFERENCES `qfl_hrm`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


CREATE TABLE `jobs` (
  `id` bigint(19) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) DEFAULT NULL,
  `payload` longtext DEFAULT NULL,
  `attempts` int(11) DEFAULT NULL,
  `reserved_at` datetime DEFAULT NULL,
  `available_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


CREATE TABLE `failed_jobs` (
  `id` bigint(19) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) DEFAULT NULL,
  `connection` text DEFAULT NULL,
  `queue` text DEFAULT NULL,
  `payload` longtext DEFAULT NULL,
  `exception` longtext DEFAULT NULL,
  `failed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;



INSERT INTO hrm_manual_attendance_data
    (device_no, employee_code, punch_date, punch_time, hrm_location_id,
     is_new, hrm_employee_id, data_from, valid, hrm_attendance_raw_data_id,
     comment, entry_status, users_id, created_at, osd_time_status,approved_by,approved_at)
SELECT
    r.device_no,
    r.employee_code,
    r.punch_date,
    r.punch_time,
    r.hrm_location_id,
    r.is_new,
    r.hrm_employee_id,
    r.data_from,
    r.valid,
    r.id,
    c.comment,
    c.entry_status,
    c.users_id,
    c.created_at,
    c.osd_time_status,
    c.users_id,
    c.created_at
FROM hrm_attendance_raw_data r
 JOIN hrm_attendance_comment c ON c.hrm_attendance_raw_data_id = r.id where c.users_id is not null
AND r.data_from = 2 and r.valid = 1;

alter table company_information
add column manual_attendance_auto_approved boolean default(1);

