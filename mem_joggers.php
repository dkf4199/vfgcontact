<?php
session_start();
// If no agent session value is present, redirect the user:
if ( !isset($_SESSION['staff_agent']) OR ($_SESSION['staff_agent'] != md5($_SERVER['HTTP_USER_AGENT'])) ) {
	require_once ('./includes/phpfunctions.php');
	$url = absolute_url('rep_login.php');
	//javascript redirect using window.location
	echo '<script language="Javascript">';
	echo 'window.location="' . $url . '"';
	echo '</script>';
	exit();	
}
include ('includes/config.inc.php');
include ('includes/phpfunctions.php');
include ('includes/selectlists.php');

?>
<!DOCTYPE html>
<html>
<head>
<meta  http-equiv="Content-Type" content="text/html; charset=utf-8" />
<META NAME="copyright" CONTENT="&copy; 2013 Deron Frederickson">
<META NAME="robots" CONTENT="index,follow"> 
<META HTTP-EQUIV="cache-control" CONTENT="no-cache">
<META HTTP-EQUIV="pragma" CONTENT="no-cache">
<meta name="revisit-after" content="30 days" />
<title>VFG Contact - Memory Jogger Prospect Utility</title>
<!-- CSS -->
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact_forms.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact_tables.css" />
<link rel="stylesheet" href="./css/start/jquery-ui-1.10.3.custom.css">
<link rel="stylesheet" type="text/css" media="screen" href="./css/modaldivs.css" />
<!-- JS -->
<script src="./js/jquery-1.10.2.js"></script>
<script src="./js/jquery-ui-1.10.3.custom.js"></script>
<script type='text/javascript' src="./js/jquery.simplemodal.js"></script>
<!-- Handlers for the image clicks to actions and modals -->
<script type='text/javascript' src='./js/memory_jogger_handlers.js'></script>
<!-- REMOTE DIALER library-->
<script type='text/javascript' src='./js/gettemp_contact.js'></script>
</head>

<body>
<div id="wrapper">
	<!-- Header -->
	<?php 
		//include('includes/html/vfg_logohead_and_menu_color.html');
		include('includes/html/newheader_log.html');
	?>

	<div class="maincontent">
		<div class="welcomespan">Welcome <?php echo $_SESSION['rep_firstname'].' '.$_SESSION['rep_lastname'].'.'; ?></div>
		
			<?php
				$repsid = $_SESSION['rep_id'];
				//DB Connection
				require_once (MYSQL);
				
				//get total aggregate leads for rep
				$allleads = 0;
				//count(*) of leads
				$t = "SELECT count(rep_id) FROM lead_list
				   WHERE rep_id='$repsid'";
				$r = @mysqli_query($dbc, $t);

				$row = mysqli_fetch_array($r, MYSQLI_NUM);
				$allleads = $row[0];
				mysqli_free_result($r);
				
				$lead_counts = array();
				$other_count = 0;
				//get counts from lead_list
				$c = "SELECT category, count(*) as total_leads 
						FROM lead_list
					  WHERE rep_id = '$repsid'
					GROUP BY category ";
				$rc = mysqli_query ($dbc, $c);
				if ($rc){
					while ($row = mysqli_fetch_array($rc, MYSQLI_ASSOC)) {
						if ($row['category'] != 'Other'){
							$lead_counts[] = $row['category'].':'.$row['total_leads'];
						}
						if ($row['category'] == 'Other'){
							$other_count = (int) $row['total_leads'];
						}
					}
					mysqli_free_result($rc);
				}
				
				
				//Get distinct categories
				$q = "SELECT DISTINCT category
					  FROM memory_joggers
					  WHERE category <> 'Other'
					  ORDER BY category ";
				$r = mysqli_query ($dbc, $q); // Run the query.
				
				if (mysqli_num_rows($r) > 0){
					echo '<div id="ajax_leadlist">
							<table id="csstable">
							   <tr>
								<th colspan=8 align="center"><h3>Memory Jogger Categories</h3>('.$allleads.' Leads Total)</th>
							   </tr>';
					
					//COLUMN and RECORDS switches for 2x3 table
					$cols=8;
					$recs=1;

					echo '<tr>';
					//Build the table rows
					while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
						//$options .= "<option value=\"".$row['category']."\">".$row['category']."</option>\n";
						$recs++;
						//$havecount = false;
						$lead_count = 0;
						foreach($lead_counts as $ele){	
							$ele_split = explode(":", $ele);
							if ($row['category'] == $ele_split[0]){
								$lead_count = $ele_split[1];
							}
						}
						echo '<td><a href="leads_by_category.php?leads_cat='.$row['category'].'">'.$row['category'].'</a><br />'.$lead_count.'</td>';
						
						if ($recs > $cols) {
							echo '</tr><tr>';
							$recs = 1;
						}
					}
					mysqli_free_result($r);
					
					//ADD 'Other' Category as last category
					echo '<td><a href="leads_by_category.php?leads_cat=Other">Other</a><br />'.$other_count.'</td>';
						
					echo '</tr></table>
						  </div>';
				}
				
				/*foreach($lead_counts as $ele){	
					$ele_split = explode(":", $ele);
					echo $ele_split[0].' --> '.$ele_split[1].'<br />';
				}*/
			?>
				
			
			

	</div> <!-- close main content -->
</div>	<!-- close wrapper -->
<?php include('includes/html/footer.html'); ?>
</body>
</html>