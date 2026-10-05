SELECT 
    a.hrm_employee_id,
    Concat(ifnull(c.employee_name,'') ,'  |  ', ifnull(b.employee_code,'')) as employee_name,
    d.depertment_name,
    e.designation_name,
    f.confirmation_date,
    a.punche_date,
    a.in_time,
    a.out_time,
    a.early_out_time,
    a.late_time,   
     STR_TO_DATE(a.overtime_time,"%H:%i:%s") AS overtime_time,
    g.alies as attendance_status,
     CONCAT(ifnull(aa.comment,'') , ifnull(a.comment,'')) as comment ,
    absent.absent,
    present.present,
    allLeave.allLeave,
    late.late,
    Friday.Friday,
    Holiday.Holiday,
    HalfDayLeave.halfday,
    totalovertime.totalovertime,
    totallatetime.totallatetime,
    quarterday.quarterday,
    EarlyOut.EarlyOut
    
FROM
    hrm_attendance a
        JOIN
    hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id 
 AND b.id in (SELECT max(id) FROM hrm_employee_job_info Group By hrm_employee_id) 
        JOIN
    hrm_employee c ON b.hrm_employee_id = c.id
        JOIN
    hrm_depertment d ON b.hrm_depertment_id=d.id
        JOIN
    hrm_designation e ON b.hrm_designation_id=e.id
        JOIN
    hrm_employee_joining f ON b.hrm_employee_id=f.hrm_employee_id
       JOIN 
hrm_attendance_status g ON g.id=a.attendance_status
$P!{condition_parameter}

LEFT JOIN
     (SELECT Count(id) as absent,hrm_employee_id FROM hrm_attendance WHERE attendance_status in (1) $P!{subquery_parameter}  group by hrm_employee_id)  absent
     ON a.hrm_employee_id=absent.hrm_employee_id
 
LEFT JOIN
     (SELECT Count(id) as EarlyOut,hrm_employee_id FROM hrm_attendance WHERE attendance_status in (14) $P!{subquery_parameter}  group by hrm_employee_id)  EarlyOut
     ON a.hrm_employee_id=EarlyOut.hrm_employee_id    
     
LEFT JOIN
    (SELECT Count(id) as present,hrm_employee_id FROM hrm_attendance WHERE attendance_status in(2,5) $P!{subquery_parameter}  group by hrm_employee_id)  present
    ON a.hrm_employee_id=present.hrm_employee_id
LEFT JOIN
    (SELECT Count(id) as allLeave,hrm_employee_id FROM hrm_attendance WHERE attendance_status in(3,9,11) $P!{subquery_parameter}  group by hrm_employee_id) as allLeave
    ON a.hrm_employee_id=allLeave.hrm_employee_id
LEFT JOIN

    (SELECT Count(id) as late,hrm_employee_id FROM hrm_attendance WHERE attendance_status in(6) $P!{subquery_parameter}  group by hrm_employee_id)  late
    ON a.hrm_employee_id=late.hrm_employee_id
LEFT JOIN 
    (SELECT Count(id) as Friday,hrm_employee_id FROM hrm_attendance WHERE attendance_status in(7) AND in_time>'00:00:00'  $P!{subquery_parameter}  group by hrm_employee_id)  Friday
 ON a.hrm_employee_id=Friday.hrm_employee_id
LEFT JOIN
    (SELECT Count(id) as Holiday,hrm_employee_id  FROM hrm_attendance WHERE attendance_status in(8) AND in_time>'00:00:00'  $P!{subquery_parameter}  group by hrm_employee_id)  Holiday
ON  a.hrm_employee_id=Holiday.hrm_employee_id
LEFT JOIN
    (SELECT Count(id) as halfday,hrm_employee_id FROM hrm_attendance WHERE attendance_status in(4,10) $P!{subquery_parameter}  group by hrm_employee_id)  HalfDayLeave
ON  a.hrm_employee_id=HalfDayLeave.hrm_employee_id
LEFT JOIN
    (SELECT Count(id) as quarterday,hrm_employee_id FROM hrm_attendance WHERE attendance_status in(12,13) $P!{subquery_parameter}  group by hrm_employee_id)  quarterday
ON  a.hrm_employee_id=quarterday.hrm_employee_id
LEFT JOIN    
   ((SELECT TIME_FORMAT(SEC_TO_TIME( SUM(TIME_TO_SEC(overtime_time))),'%H:%i') AS totalovertime,hrm_employee_id FROM hrm_attendance WHERE hrm_employee_id  <> ''  $P!{subquery_parameter}  group by hrm_employee_id))  totalovertime
ON  a.hrm_employee_id=totalovertime.hrm_employee_id
LEFT JOIN    
   ((SELECT TIME_FORMAT(SEC_TO_TIME( SUM(TIME_TO_SEC(late_time))),'%H:%i') AS totallatetime,hrm_employee_id  FROM hrm_attendance WHERE hrm_employee_id  <> ''   $P!{subquery_parameter}  group by hrm_employee_id))  totallatetime
   ON   a.hrm_employee_id=totallatetime.hrm_employee_id
Left JOIN 
(SELECT 
  DISTINCT  a.hrm_employee_id, a.punch_date,c.comment,b.id as job_id
FROM
    hrm_attendance_raw_data a
        JOIN
    hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
        AND a.data_from = 2
$P!{condition_parameter_1} 
        JOIN
    hrm_attendance_comment c ON a.id = c.hrm_attendance_raw_data_id
    GROUP BY a.hrm_employee_id, a.punch_date) aa ON a.hrm_employee_id=aa.hrm_employee_id AND aa.punch_date=a.punche_date 
order by  a.hrm_employee_id,a.punche_date