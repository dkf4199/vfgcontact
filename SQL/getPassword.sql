SELECT a.rep_id, a.vfgrepid, b.password
FROM reps a INNER JOIN rep_login_id b
ON a.rep_id = b.rep_id
WHERE a.vfgrepid = '29CRM';