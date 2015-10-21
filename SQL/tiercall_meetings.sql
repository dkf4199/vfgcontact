use vfgcontact;
SELECT a.tc_id, 
				  a.tier, 
				  a.contact_id, 
				  a.contact_firstname, 
				  a.contact_lastname,
				  a.contact_email, 
				  a.contact_phone, 
				  a.contact_timezone, 
				  a.rep_id, 
				  a.rep_vfgid,
				  a.rep_unique_id,
				  a.rep_gmail,
				  a.rep_gpass,
				  a.scheduled_meeting,
				  a.notification_interval, 
				  a.notification_time,
				  a.send_email, 
				  a.send_text,
				  a.inviter,
				  a.manager,
				  a.consultant,
	              b.lastname as 'inviterlast',
                  b.phone as 'inviterphone',
				  c.lastname as 'managerlast',
					c.phone as 'managerphone',
					d.lastname as 'consultantlast',
					d.phone	as 'consultantphone'
			FROM tiercall_meetings a 
			INNER JOIN reps b ON a.rep_id = b.rep_id
			INNER JOIN reps c ON a.manager = c.vfgrepid
			INNER JOIN reps d ON a.consultant = d.vfgrepid
  
		  WHERE date_format(a.scheduled_meeting, '%m-%d-%Y') = '07-02-2014';