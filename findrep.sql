SELECT a.rep_id, a.vfgrepid, b.password
FROM `reps` a INNER JOIN rep_login_id b
ON a.vfgrepid = b.vfgid 
WHERE a.lastname='miller'
AND a.firstname = 'rick';