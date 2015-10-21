<?php
session_start();
	$repsid = $_SESSION['rep_id'];
	
	//Set the timezone to the rep's timezone
	date_default_timezone_set($_SESSION['rep_tz']);
	
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	//DB Connection
	//require_once (MYSQL);
	
	//$_POST VARS
	$evt_title = $_POST['event_title'];
	$evt_desc = $_POST['event_desc'];
	$evt_start_hh = $_POST['event_start_hh'];
	$evt_start_mm = $_POST['event_start_mm'];
	$evt_end_hh = $_POST['event_end_hh'];
	$evt_end_mm = $_POST['event_end_mm'];
	$m = $_POST['month'];
	$d = $_POST['day'];
	$y = $_POST['year'];
	$repeat_event = $_POST['repeat'];				// '0' or '1'
	$recurring_until = $_POST['recurring_until']; 	// mm-dd-yyyy
	
	$todays_date = $y.'-'.$m.'-'.$d;
	
	//Break the recurring date (mm-dd-yyyy) into parts
	if ($recurring_until != ''){
		list($rd_mm,$rd_dd,$rd_yy) = explode('-',$recurring_until);
		$recur_end_date = $rd_yy.'-'.$rd_mm.'-'.$rd_dd;
	}
	
	// For fc_events_parent table:
	$start_time = $evt_start_hh.":".$evt_start_mm.":00";
	$end_time = $evt_end_hh.":".$evt_end_mm.":00";
	$weekday = date('N', strtotime($todays_date));
	
	// Formatting for SQL datetime (if this is edited, it will NOT work.)
	$event_start_fulldatetime = $y."-".$m."-".$d." ".$evt_start_hh.":".$evt_start_mm.":00";
	
	// Formatting for SQL datetime (if this is edited, it will NOT work.)
	$event_end_fulldatetime = $y."-".$m."-".$d." ".$evt_end_hh.":".$evt_end_mm.":00";

	/* comment out the mysqli block, PDO section is below this.....
	$insEvent_sql = "INSERT INTO fc_events (rep_id, title, description, start, end) 
					VALUES('$repsid', '$evt_title', '$evt_desc', '$event_start', '$event_end') ";
			
	$r = mysqli_query($dbc, $insEvent_sql);    // or die(mysqli_error($dbc));
	
	if (mysqli_affected_rows($dbc) == 1){
		echo 'Event Successfully added.';	
	} else {
		echo 'Event Insert Problem.';
	}
	*/
	
	// connection to the database
	try {
		$dbh = new PDO(PDO_HOSTSTRING, PDO_USER, PDO_PASS);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch(Exception $e) {
		exit('Unable to connect to database.');
	}

	// IF NOT REPEATING EVENT (0)
	if ($repeat_event == '0'){
		$repeats = 0;
		$repeat_freq = 0;
		try{
            $dbh->beginTransaction();
            $stmt = $dbh->prepare("INSERT INTO fc_events_parent 
                (rep_id,title,start_date, start_time, end_time, weekday, repeats, repeat_freq)
                VALUES (:rep_id, :title, :start_date, :start_time, :end_time, :weekday, :repeats, :repeat_freq)");
			
			$stmt->bindParam(':rep_id', $repsid );
            $stmt->bindParam(':title', $evt_title );
            $stmt->bindParam(':start_date', $todays_date);
            $stmt->bindParam(':start_time', $start_time);
            $stmt->bindParam(':end_time', $end_time);
            $stmt->bindParam(':weekday', $weekday);
            $stmt->bindParam(':repeats', $repeats);
            $stmt->bindParam(':repeat_freq', $repeat_freq);
            $stmt->execute();
            $last_id = $dbh->lastInsertId();

			// CHILD EVENT
            $stmt = $dbh->prepare("INSERT INTO fc_events 
                (parent_id, rep_id, title, description, start, end)
                VALUES (:parent_id, :rep_id, :title, :description, :start, :end)");

			$stmt->bindParam(':parent_id', $last_id);
			$stmt->bindParam(':rep_id', $repsid);
            $stmt->bindParam(':title', $evt_title );
			$stmt->bindParam(':description', $evt_desc);
            $stmt->bindParam(':start', $event_start_fulldatetime);
            $stmt->bindParam(':end', $event_end_fulldatetime);
            
            $stmt->execute();

            $dbh->commit();
			echo 'Event Added.';
        }
        catch(Exception $e){
            $dbh->rollback();
			echo 'Add Failed.';
        }
		
		
	} else {	// REPEATING EVENT
		$repeats = 1;
        $repeat_freq = (int) $_POST['repeat_freq'];
        $until = (365/$repeat_freq);		// recur these out 1 year for now
        if ($repeat_freq == 1){
            $weekday = 0;
        }
		
		$dbh->beginTransaction();
        try{
            $stmt = $dbh->prepare("INSERT INTO fc_events_parent 
                (rep_id, title, start_date, start_time, end_time, recur_ending_date, weekday, repeats, repeat_freq)
                VALUES (:rep_id, :title, :start_date, :start_time, :end_time,:recur_ending_date, :weekday, :repeats, :repeat_freq)");

			$stmt->bindParam(':rep_id', $repsid );
            $stmt->bindParam(':title', $evt_title );
            $stmt->bindParam(':start_date', $todays_date);
            $stmt->bindParam(':start_time', $start_time);
            $stmt->bindParam(':end_time', $end_time);
			$stmt->bindParam(':recur_ending_date', $recur_end_date);
            $stmt->bindParam(':weekday', $weekday);
            $stmt->bindParam(':repeats', $repeats);
            $stmt->bindParam(':repeat_freq', $repeat_freq);
            $stmt->execute();
            $last_id = $dbh->lastInsertId();

			//initialize begin_date range
			$begin_date = strtotime($event_start_fulldatetime.'+0DAYS');
			$current_date = date("Y-m-d", $begin_date);
			
            for($x = 0; $x <$until; $x++){
                $stmt = $dbh->prepare("INSERT INTO fc_events 
                    (parent_id, rep_id, title, description, start, end)
                    VALUES (:parent_id, :rep_id, :title, :description, :start, :end )");
				$stmt->bindParam(':parent_id', $last_id);
				$stmt->bindParam(':rep_id', $repsid);
                $stmt->bindParam(':title', $evt_title );
				$stmt->bindParam(':description', $evt_desc);
                $stmt->bindParam(':start', $event_start_fulldatetime);
                $stmt->bindParam(':end', $event_end_fulldatetime);
                
				
				if ( $current_date <= $recur_end_date ){
					$stmt->execute();
				}
                $start_date = strtotime($event_start_fulldatetime . '+' . $repeat_freq . 'DAYS');
                $end_date = strtotime($event_end_fulldatetime . '+' . $repeat_freq . 'DAYS');
                $event_start_fulldatetime = date("Y-m-d H:i", $start_date);
                $event_end_fulldatetime = date("Y-m-d H:i", $end_date);
				$current_date = date("Y-m-d", $start_date);
            }
            $dbh->commit();
			echo 'Event Added.';
			
		} catch(Exception $e){
            $dbh->rollback();
			echo 'Add Failed.';
        }
		
	
	}
	
	
?>