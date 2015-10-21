<?php
	//cron job - notifies contact of their scheduled
	//           tier call.
	//
	//			 Notification times are based on contact's
	//           local time!
	//
	// tiercall_meetings table :
	//
	// tc_id, tier, contact_id, contact_firstname, contact_lastname,
	// contact_email, contact_phone, contact_timezone, rep_id, rep_vfgid,
	// rep_unique_id, scheduled_meeting, notification_interval, notification_time,
	// send_email, send_text
	//******************************************************************************
	include ('./includes/config.inc.php');

	//Zend Mail for gmail stuff
	require_once ('Zend/Mail.php');
	require_once ('Zend/Mail/Transport/Sendmail.php');

	//DB Connection
	require_once (MYSQL);

	$thisday = date("m-d-Y");	//Todays date
	$rightnow = date("Y-m-d H:i:s");	//For dialer timestamp

	$msgs = array();
	
	//TEST hour past right now
	//$timespan = 'PT60M';
	//$m_date = new DateTime($rightnow); 
	//$n_int = new DateInterval($timespan); 
	//$n_int->invert = 1; //Make it negative, timespan in the past! 
	//$m_date->add($n_int);
	//$hour_past = $m_date->format('H:i');
	//echo 'Now: '.$rightnow.'<br />';
	//echo 'Hour From Now:'.$hour_past;
	//END TEST
	
	
	//Initialize vars needed to process each record out here
	//Contact
	$c_tier = $c_first = $c_last = $c_email = $c_phone = '';
	//Rep
	$r_vfgid = $r_unique_id = $r_gmail = $r_gpass = '';
	//Flags
	$send_email = $send_text = '';
			
	//*************************************
	// Need to go through each record
	// in tiercall_meetings - Find ones for
	// today.
	//*************************************
	
	$q = "SELECT  a.tc_id, 
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
				b.firstname as 'inviterfirst',
	            b.lastname as 'inviterlast',
                b.phone as 'inviterphone',
				c.firstname as 'managerfirst',
				c.lastname as 'managerlast',
				c.phone as 'managerphone',
				d.firstname as 'consultantfirst',
				d.lastname as 'consultantlast',
				d.phone	as 'consultantphone'
			FROM tiercall_meetings a 
			INNER JOIN reps b ON a.rep_id = b.rep_id
			INNER JOIN reps c ON a.manager = c.vfgrepid
			INNER JOIN reps d ON a.consultant = d.vfgrepid
			WHERE date_format(a.scheduled_meeting, '%m-%d-%Y') = '$thisday' ";
	$r = mysqli_query($dbc, $q);

	if ($r) { 
		// Ran OK
		// Fetch data:
		while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			
			//THIS WON'T WORK IF CONTACT'S TIMEZONE IS NOT SET!!!!!!!!!!
			if ($row['contact_timezone'] != ''){
			
				//Initialize vars needed to process each record
				//Contact
				$c_tcid = $c_first = $c_last = $c_email = $c_phone = '';
				//Rep
				$r_vfgid = $r_unique_id = $r_gmail = $r_gpass = '';
				//Inviter Manager Consultant
				$invname = $invphone = $manname = $manphone = $conname = $conphone = '';
				//Flags
				$send_email = $send_text = '';
				
				$tzdesc = '';
				//Get timezone description
				switch($row['contact_timezone']){
					case 'America/New_York':
						$tzdesc = 'Eastern';
						break;
					case 'America/Chicago':
						$tzdesc = 'Central';
						break;
					case 'America/Boise':
						$tzdesc = 'Mountain';
						break;
					case 'America/Phoenix':
						$tzdesc = 'Arizona Mountain';
						break;
					case 'America/Los_Angeles':
						$tzdesc = 'Pacific';
						break;
					case 'America/Juneau':
						$tzdesc = 'Alaska';
						break;
					case 'Pacific/Honolulu':
						$tzdesc = 'Hawaii';
						break;
				}
				
				//Get contact's local time
				date_default_timezone_set($row['contact_timezone']);
				$contact_current_time = date("H:i");
				/*echo '<br />Contact Id:  '.$row['contact_id']."\n";
				echo '<br />Meeting Date :  '.$row['scheduled_meeting']."\n";
				echo '<br />Contacts Current Time :  '.$contact_current_time."\n";
				echo '<br />Notification Time :  '.$row['notification_time']."\n";
				echo "<br />************************************<br />\n";
				*/
				$c_tcid = $row['tc_id'];
				$c_tier = $row['tier'];
				$c_first = $row['contact_firstname'];
				$c_last = $row['contact_lastname'];
				$c_phone = $row['contact_phone'];
				$c_email = $row['contact_email'];
				
				$r_vfgid = $row['rep_vfgid'];
				$r_unique_id = $row['rep_unique_id'];
				$r_gmail = $row['rep_gmail'];
				$r_gpass = $row['rep_gpass'];
				
				$send_email = $row['send_email'];
				$send_text = $row['send_text'];
				
				$invname = $row['inviterfirst'].' '.$row['inviterlast'];
				$invphone = $row['inviterphone'];
				$manname = $row['managerfirst'].' '.$row['managerlast'];
				$manphone = $row['managerphone'];
				$conname = $row['consultantfirst'].' '.$row['consultantlast'];
				$conphone = $row['consultantphone'];
				
				//See if notification time (hour-minute) == contacts current time
				// Find the DateTime using interval
				$n_time = new DateTime($row['notification_time']);
				//$date->add(new DateInterval($timespan));
				$notification_time = $n_time->format('H:i');
				
				//Get the hour/minute of the scheduled time
				$s_time = new DateTime($row['scheduled_meeting']);
				$scheduled_time = new DateTime($row['scheduled_meeting']);

 				
				//echo '<br /><br />Converted Notification Time :  '.$notification_time."\n";
				
				// Check notification_time in contact's timezone.
				// If it's time to notify:
				// 1.) Send Email to contact
				// 2.) Send Text to contact

				if ($contact_current_time == $notification_time){
					
					if ($send_email == 'Y'){
						if ($r_gmail != '' && $r_gpass != ''){
							//RUN EMAIL FUNCTION
							$tr = new Zend_Mail_Transport_Sendmail('smtp.gmail.com', array(
								'auth' => 'login',
								'username' => $r_gmail,
								'password' => $r_gpass,
								'ssl' => 'ssl',
								'port' => 465)
							);
							Zend_Mail::setDefaultTransport($tr);
							
							$subject = 'Tiercall Meeting Reminder.';
							$emailbody = '<p>This is a courtesy reminder of your scheduled Tier '.$c_tier.' phone meeting at '.$scheduled_time->format('h:i a').' '.$tzdesc.' time today.  Thank you.</p>
										  <p>The Client is '.$c_first.' '.$c_last.' at '.$c_phone.'</p>
										  <p>The Manager on record is '.$manname.' at '.$manphone.'</p>
										  <p>The Consultant on record is '.$conname.' at '.$conphone.'</p>';
									
							// 11/12/2013 add the addBcc( string $email) parameter
							$mail = new Zend_Mail();
							$mail->setBodyHtml($emailbody);
							$mail->addTo($c_email);
							$mail->addBcc($r_gmail);
							
							$mail->setSubject($subject);
							$mail->setFrom($r_gmail);
							//
							try {
								$mail->send();
								//$mailsent = true;
								//echo "Custom Template sent to ".$tofirst." ".$tolast."<br />";
							} catch (Exception $ex) {
								//echo "Failed to send gmail mail! " . $ex->getMessage() . "<br />";
							}
						
						}	//end if ($r_gmail != '' && $r_gpass != '')
							
					}	//end $send_email == 'Y'
					//**************************************************************
					
					if ($send_text == 'Y') {
						//RUN TEXT FUNCTION
						
						if($r_unique_id != ''){
						
							//initialize status
							$status = "0";
							
							//Create key field for temp_contacts table field "id_temp_contact" 
							$idexists = true;
							do {
								$randnum = mt_rand(1,99999);
								$strnum = strval($randnum);

								switch (strlen($strnum)) {
								case 1:
									$finalnum = '0000'.$strnum;
									break;
								case 2:
									$finalnum = '000'.$strnum;
									break;
								case 3:
									$finalnum = '00'.$strnum;
									break;
								case 4:
									$finalnum = '0'.$strnum;
									break;
									case 5:
									$finalnum = $strnum;
									break;
								}
								$tempcontactid = $finalnum;
								
								// Is value already in id_temp_contact in temp_contacts table?
								$tcquery = "SELECT id_temp_contact 
											FROM temp_contacts 
											WHERE id_temp_contact = '$tempcontactid'";
								$tcq = @mysqli_query ($dbc, $tcquery);
								if (mysqli_num_rows($tcq) != 1) {
									  //id is unique
									  $idexists = false;
								}
								mysqli_free_result($tcq);
								
							} while ($idexists);
						
							$text_msg = 'This is a courtesy reminder of your Tier '.$c_tier.' phone meeting at '.$scheduled_time->format('h:i a').' '.$tzdesc.' Time today.  Thank you.';
							//NOTE: Change this next routine....send the phone # into this program via ajax call.
							//      No need to go to db to retrieve this.
							if ($c_phone != ''){
								$insquery = "INSERT INTO temp_contacts (id_temp_contact, unique_id, phone, comm_note, timestamp, status) 
											VALUES ('$tempcontactid', '$r_unique_id', '$c_phone', '$text_msg', '$rightnow', '$status')";
								$result = mysqli_query ($dbc, $insquery);
								if (mysqli_affected_rows($dbc) == 1){
									$msgs[] = 'Text sent via Dialer.<br />';
								} else {
									$msgs[] = 'Dialer problem with text message.';
									//echo '<p>'.mysqli_error($dbc).'</p>';
								}
							}
						
						}	// end if ($r_unique_id != '')
						
					}	//end $send_text == 'Y'
					
					
				} //end $contact_current_time == $notification_time
			
				//EMAIL REP 1 HOUR AFTER MEETING TO FOLLOW UP, THEN DELETE ENTRY FROM tiercall_meetings
				//
				//have $contact_current_time......find 1 hour past scheduled meeting time ($s_time)
				
				$timespan = 'PT60M';	// +60 minute interval
				
				$n_int = new DateInterval($timespan);	// create interval
				
				//$n_int->invert = 1;   //Make it negative, timespan in the past! 
				$s_time->add($n_int);	//add interval to $s_time
				$hour_past_meeting = $s_time->format('H:i');
				
				//echo 'Hour Past Meeting:  '.$hour_past_meeting.'  Current Time:  '.$contact_current_time.'<br />';
				
				if ($hour_past_meeting == $contact_current_time){
				
					if ($r_gmail != '' && $r_gpass != ''){
						
						//SEND MAIL TO REP TO FOLLOW UP
						$tr = new Zend_Mail_Transport_Sendmail('smtp.gmail.com', array(
							'auth' => 'login',
							'username' => $r_gmail,
							'password' => $r_gpass,
							'ssl' => 'ssl',
							'port' => 465)
						);
						Zend_Mail::setDefaultTransport($tr);
						
						$subject = 'Tiercall Meeting Follow-Up.';
						$emailbody = '<p>Remember to follow up with '.$c_first.' '.$c_last.' at '.$c_phone.' regarding their Tier '.
									  $c_tier.' phone meeting at '.$scheduled_time->format('h:i a').' '.$tzdesc.' '.' today.  Thank you.</p>';
								
						// 11/12/2013 add the addBcc( string $email) parameter
						$mail = new Zend_Mail();
						$mail->setBodyHtml($emailbody);
						$mail->addTo($r_gmail);
								
						$mail->setSubject($subject);
						$mail->setFrom($r_gmail);
						//
						try {
							$mail->send();
							//$mailsent = true;
						} catch (Exception $ex) {
							$msgs[] = "Failed to send gmail mail! " . $ex->getMessage() . "<br />";
						}
					
					}	//end if ($r_gmail != '' && $r_gpass != '')
					
				}	//end if ($hour_past_meeting == $contact_current_time)
				
				
			}  //end if ($row['contact_timezone'] != '')
			
		}	//end while $row fetch
		
	} 

	mysqli_close($dbc);

?>