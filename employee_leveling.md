CREATE TABLE IF NOT EXISTS `DB_HRM`.`hrm_employee_leveling_master` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `Level_name` VARCHAR(45) NULL,
  `created_at` DATETIME NULL,
  `valid` TINYINT(1) NULL DEFAULT 1,
  `users_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `fk_hrm_employee_leveling_master_users1_idx` (`users_id` ASC),
  CONSTRAINT `fk_hrm_employee_leveling_master_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `DB_HRM`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `DB_HRM`.`hrm_employee_leveling_details` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `hrm_employee_leveling_master_id` INT UNSIGNED NOT NULL,
  `hrm_salary_grade_id` INT NOT NULL,
  `valid` TINYINT(1) NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `fk_hrm_employee_leveling_details_hrm_employee_leveling_mast_idx` (`hrm_employee_leveling_master_id` ASC),
  INDEX `fk_hrm_employee_leveling_details_hrm_salary_grade1_idx` (`hrm_salary_grade_id` ASC),
  CONSTRAINT `fk_hrm_employee_leveling_details_hrm_employee_leveling_master1`
    FOREIGN KEY (`hrm_employee_leveling_master_id`)
    REFERENCES `DB_HRM`.`hrm_employee_leveling_master` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_hrm_employee_leveling_details_hrm_salary_grade1`
    FOREIGN KEY (`hrm_salary_grade_id`)
    REFERENCES `DB_HRM`.`hrm_salary_grade` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


================================================================================
EMPLOYEE LEVELING - SUMMARY (changes done)
================================================================================

NEW FILES (create kora hoyeche):
--------------------------------
1. app/Models/HrmEmployeeLevelingMaster.php
2. app/Models/HrmEmployeeLevelingDetails.php
3. app/Http/Controllers/EmployeeLevelingController.php
4. resources/views/employee_leveling/employee_leveling_list.blade.php
5. resources/views/employee_leveling/create_employee_leveling.blade.php
6. resources/views/employee_leveling/edit_employee_leveling.blade.php

MODIFIED FILES (update kora hoyeche):
-------------------------------------
1. routes/web.php
   -> resource('employeeleveling') + employeeleveling_list + employeeleveling/{id}/cancel routes add
2. resources/views/layouts/main.blade.php
   -> Admin > Configuration er sese "Employee Leveling" menu add
   -> active-state (Request::is) 3 jaygay update

FEATURES (ki kora hoyeche):
---------------------------
- create/edit/delete (valid=0 soft delete) system
- salary grade ekbar setup hobar por ar dekhabe na:
    create form e sudhu sei salary grades dekhabe jara onno level e valid=1 assignment kora nai
    edit form e current assigned grade gulo thakbe, baki excluded thakbe
- list e level name + salary grades grouped + status + edit + delete button
- master (hrm_employee_leveling_master) + details (hrm_employee_leveling_details) structure
- users_id = current logged in user

UI:
---
- create/edit te salary grades TABLE e checkbox diye ashe (NOT select2 multiple)
- check/uncheck kore create/update hoy
- onno leveling e je grade ache seta ei table e asbe na


================================================================================
COST TO THE COMPANY - REPORT (routes + file mapping)
================================================================================

ROUTES (routes/web.php):
------------------------
Route::get('cost-to-the-company', 'ReportsController@cost_to_the_company');      // page load
Route::post('cost-to-the-company', 'ReportsController@get_cost_to_the_company'); // DataTable AJAX data
Route::post('cost-to-the-company/excel-export', 'ReportsController@exportExcelCostToTheCompany');
Route::post('cost-to-the-company/pdf-export', 'ReportsController@exportPdfCostToTheCompany');

FILE MAPPING PER ROUTE:
-----------------------
1. GET cost-to-the-company  -> ReportsController@cost_to_the_company
   - resources/views/reports/cost-to-the-company.blade.php
     (report page: month-from/to + location dropdown + DataTable 10 column
      subtotal/location/TOTAL row + Excel/PDF export button)

2. POST cost-to-the-company -> ReportsController@get_cost_to_the_company
   - ReportsController: buildCostParams() -> getCostToTheCompanyGroupedData()
   - returns JSON data (row_type: subtotal / location, TOTAL row) for DataTable

3. POST cost-to-the-company/excel-export -> exportExcelCostToTheCompany
   - ReportsController: buildCostParams() -> getCostToTheCompanyData() (raw rows)
   - app/Exports/CostToTheCompanyExport.php  -> cost_to_the_company.xlsx
     (startCell 'A1' -> data row 4 theke shuru -> Sl.No./level rows bold + light-blue,
      location rows normal, TOTAL bold yellow)

4. POST cost-to-the-company/pdf-export -> exportPdfCostToTheCompany
   - ReportsController: buildCostParams() -> getCostToTheCompanyData()
   - resources/views/reports/cost-to-the-company-pdf.blade.php (A4 landscape)
     (company header only on page 1, table column header repeats via <thead>,
      FIRST page: Al-Mostafa Group header + title; subsequent pages: only column headers)

CHANGES / FIXES (ki kora hoyeche):
----------------------------------
1. 10TH COLUMN: 'PF Com. Contribution' add (Two Festival Bonus er pore) page + excel + pdf e.
   -> reports blade columns config + tfoot, CostToTheCompanyExport, pdf blade

2. DYNAMIC MONTH RANGE + LOCATION FILTER:
   -> buildCostParams(): month_from/month_to (M-yyyy) -> 'Ym' + 'Y-m-d' duration
   -> SQL er 6 ta hardcoded 'BETWEEN 202601 AND 202609' -> '$monthFrom' AND '$monthTo'
   -> hrm_location_id filtered (is_numeric && != '999'), inline e form j SQL er UNION od stop.

3. LEVEL ORDER (page + excel + pdf):
   -> ORDER BY level_name (alphabetical) replace
   -> CASE: 'Permanent Worker' = 1, 'Casual Worker' = 2, 'Contructual Worker' = 3, else 0
      result: Permanent Worker casual/contractual er AAGE ashe
   (level list: Above Assistant Direction, Above Assistant Manager, Above Management Trainee,
    Below Management Trainee, Permanent Worker, Casual Worker, Contructual Worker)

4. EXCEL BOLD FIX (Sl.No. rows bold hocchilo na):
   -> root cause: startCell 'A3' + 3 heading rows => data actually row 6 theke shuru,
      kintu bold loop row 4 theke cholt. Fix: startCell 'A3' -> 'A1'
   -> ekhon Sl.No. (level) rows bold + EEF2FB, location rows normal, TOTAL FFF2CC

5. PDF HEADER (first page only) + TABLE HEADER REPEAT:
   -> company header block (Al-Mostafa Group, title, From/To) page 1 e thake
   -> table column headers + page-top spacer thead er moddhe -> every page e repeat

6. PDF FOOTER MARGIN (~0.5 inch):
   -> @page bottom 0.5in + .page bottom padding 0.4in
      (dompdf 0.8.x @page margin unreliable: middle page ~0.67in, page1 ~0.46in)

CONTROLLER PRIVATE HELPERS (app/Http/Controllers/ReportsController.php):
----------------------------------------------------------------------
- buildCostParams(Request)                 ~line 4502
    month_from/month_to (M-yyyy) -> 'Ym' + 'Y-m-d' (duration), + location condition
- getCostToTheCompanyData(...)             ~line 4517
    main SQL: UNION branches (Contractual Worker / Regular / etc.) grouped by level_name,
    month range (dynamic BETWEEN) + optional hrm_location_id filter,
    computes gross, festival bonus (TIMESTAMPDIFF months), pf, houseRent, fixed, transport, existing_cost,
    final ORDER BY: CASE (Permanent=1, Casual=2, Contractual=3, else 0) then level_name
- getCostToTheCompanyGroupedData(...)      ~line 4720
    groups raw rows by level_name, builds subtotal + location rows + TOTAL, adds row_type/sl_no

DB TABLES USED (in SQL):
------------------------
pay_register, pay_register_details, pay_register_cw,
hrm_employee, hrm_employee_job_info, hrm_employee_salary,
hrm_employee_leveling_master, hrm_employee_leveling_details,
hrm_salary_grade_master, hrm_location, hrm_location (user_location),
hrm_salary_head, hrm_salary_generate_master

NOTE:
- '999' / empty / undefined hrm_location_id => ALL locations filter off
  (condition sudhu is_numeric && != '999' holei lage)
