DELIMITER $$

CREATE  PROCEDURE `get_register_filter`(
    IN vYear_id INT,
    IN vMonth_id INT,
    IN location_con VARCHAR(255)
)
BEGIN
    DROP TEMPORARY TABLE IF EXISTS tmpRegister;

    CREATE TEMPORARY TABLE IF NOT EXISTS tmpRegister AS (
        SELECT     concat(e.salary_head, '|', f.generate_type) AS salary_head,
                b.actual_amount,
                b.pay_register_id,
                aa.hrm_employee_job_info_id,
                a.hrm_depertment_id,
                a.hrm_employee_id,
                a.hrm_designation_id,
                a.hrm_location_id,
                a.employee_code,
                e.apply_for,
                e.hrm_salary_head_group_id,
                f.generate_type,
                a.hrm_section_id,
                a.hrm_category_id,
                aa.year_id,
                aa.hrm_month_id,
                aa.day_of_month,
                aa.total_present,
                aa.accounts_code,
                aa.payment_mode,
                aa.account_no,
                aa.hrm_bank_id,
                aa.amount,
                aa.hrm_employee_salary_id
        FROM pay_register aa
        JOIN hrm_employee_job_info a ON aa.hrm_employee_job_info_id = a.id

        AND aa.salary_genarate_type <> 0
        AND aa.apply_for = 1
        AND aa.year_id = vYear_id
        AND aa.hrm_month_id = vMonth_id
        JOIN pay_register_details b ON aa.id = b.pay_register_id
        JOIN hrm_salary_head e ON b.hrm_salary_head_id = e.id
        JOIN hrm_salary_head_group f ON e.hrm_salary_head_group_id = f.id
        ORDER BY f.generate_type
    );

    SET @sql = NULL;

    SELECT GROUP_CONCAT(DISTINCT
    CONCAT(
        'IFNULL(SUM(CASE WHEN salary_head = ''',
            salary_head,
        ''' THEN actual_amount END), 0) AS `',
        salary_head, '`'
    ) ORDER BY generate_type, hrm_salary_head_group_id) INTO @sql
    FROM tmpRegister;

    SET @sql = CONCAT('SELECT c.employee_name AS employee, b.depertment_name AS department,g.section_name,h.category_name, d.alis AS designation,
    e.location_name AS location, a.employee_code AS accounts_code, c.tin, f.joining_date, f.confirmation_date,
    CASE
        WHEN a.payment_mode = 1 THEN "Cash"
        ELSE (SELECT short_name from hrm_bank Where id = a.hrm_bank_id)
    END as payment_mode,

    a.account_no, ', @sql, '
        FROM tmpRegister a
        JOIN hrm_depertment b ON a.hrm_depertment_id = b.id
        JOIN hrm_employee c ON a.hrm_employee_id = c.id
        JOIN hrm_designation d ON a.hrm_designation_id = d.id
        JOIN hrm_location e ON a.hrm_location_id = e.id
        JOIN hrm_employee_joining f ON a.hrm_employee_id = f.hrm_employee_id
        JOIN hrm_section g ON a.hrm_section_id = g.id
        JOIN hrm_category h ON a.hrm_category_id = h.id

        ');

    -- Add dynamic conditions from the single variable
     IF location_con IS NOT NULL AND location_con <> '' THEN
         SET @sql = CONCAT(@sql, ' ', location_con);
     END IF;



    SET @sql = CONCAT(@sql, ' GROUP BY a.pay_register_id');

    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

END$$

DELIMITER ;
