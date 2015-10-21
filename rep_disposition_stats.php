<?php
session_start();
// VFGCONTACT
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

?>
<!DOCTYPE html>
<html>
<head>
<meta  http-equiv="Content-Type" content="text/html; charset=utf-8" />
<META NAME="copyright" CONTENT="&copy; 2013 Deron Frederickson">
<META NAME="robots" CONTENT="index,follow"> 
<META HTTP-EQUIV="cache-control" CONTENT="no-cache">
<META HTTP-EQUIV="pragma" CONTENT="no-cache">
<title>VFG Contacts - Disposition Stats</title>
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact_forms.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact_tables.css" />
<link rel="stylesheet" href="./css/start/jquery-ui-1.10.3.custom.css">
<script src="./js/jquery-1.10.2.js"></script>
<script src="./js/jquery-ui-1.10.3.custom.js"></script>

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
		
			include ('includes/config.inc.php');
			include ('includes/phpfunctions.php');
			include ('includes/selectlists.php');
			
			//DB Connection
			require_once (MYSQL);
			
			
			//***************************************************************
			//		REP STATS
			//***************************************************************
			
			// REP DISPOSITION STATS 
			//COUNT() CONTACTS in contacts table for rep
			$total_contacts = 0;
			$t = "SELECT count(rep_id) FROM contacts
			   WHERE rep_id='$repsid'";
			$r = mysqli_query($dbc, $t);
			$row = mysqli_fetch_array($r, MYSQLI_NUM);
			$total_contacts = $row[0];
			mysqli_free_result($r);
			// If total_contacts = 0, we can't use it denominator.  Illegal operation....
			// SO, set the denominator to be 1 so it doesn't error out.
			$total_contacts_denominator = 1;
			if ($total_contacts != 0){
				$total_contacts_denominator = $total_contacts;
			}
	
			//TOTAL Prospects in contacts table for rep - BY DISPOSITION
			$n_tot = $j_tot = $b_tot = $br_tot = $jb_tot = $rs_tot = 0;
			$c = "SELECT disposition, count(contact_id) as category_total
					FROM contacts
				   WHERE rep_id = '$repsid'
				GROUP BY disposition";
			$r = @mysqli_query($dbc, $c);
			if (mysqli_num_rows($r) > 0){
				while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
					switch(trim($row['disposition'])){
						case 'J':
							$j_tot = (int) $row['category_total'];
							break;
						case 'B':
							$b_tot = (int) $row['category_total'];
							break;
						case 'BR':
							$br_tot = (int) $row['category_total'];
							break;
						case 'JB':
							$jb_tot = (int) $row['category_total'];
							break;
						case 'RS':
							$rs_tot = (int) $row['category_total'];
							break;
						case 'N':
							$n_tot = (int) $row['category_total'];
							break;
					}
				}
				mysqli_free_result($r);
			}
			
			//COUNT() DUMPED CONTACTS for rep
			$total_dumped_contacts = 0;
			$t = "SELECT count(rep_id) FROM dumped_contacts
			   WHERE rep_id='$repsid'";
			$r = mysqli_query($dbc, $t);
			$row = mysqli_fetch_array($r, MYSQLI_NUM);
			$total_dumped_contacts = $row[0];
			mysqli_free_result($r);
			
			
			//FLUSHED CONTACTS for rep
			$total_flushed_contacts = 0;
			$t = "SELECT flushed_contacts FROM reps
			   WHERE rep_id='$repsid'";
			$r = mysqli_query($dbc, $t);
			$row = mysqli_fetch_array($r, MYSQLI_NUM);
			$total_flushed_contacts = $row[0];
			mysqli_free_result($r);
			
			//Personal LEADS - BY PRIORITY
			//COUNT() leads in lead_list
			$total_leads = 0;
			$t = "SELECT count(rep_id) FROM lead_list
			   WHERE rep_id='$repsid'
			     AND is_prospect = 'N'";
			$r = mysqli_query($dbc, $t);
			$row = mysqli_fetch_array($r, MYSQLI_NUM);
			$total_leads = $row[0];
			mysqli_free_result($r);
			
			//TOTAL leads in lead_list for rep - BY PRIORITY
			$pri_1 = $pri_2 = $pri_3 = $pri_4 = $pri_5 = 0;
			$c = "SELECT priority_number, count(l_id) as priority_total
					FROM lead_list
				   WHERE rep_id = '$repsid'
				     AND is_prospect = 'N'
				GROUP BY priority_number";
			$r = @mysqli_query($dbc, $c);
			if (mysqli_num_rows($r) > 0){
				while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
					switch($row['priority_number']){
						case '1':
							$pri_1 = (int) $row['priority_total'];
							break;
						case '2':
							$pri_2 = (int) $row['priority_total'];
							break;
						case '3':
							$pri_3 = (int) $row['priority_total'];
							break;
						case '4':
							$pri_4 = (int) $row['priority_total'];
							break;
						case '5':
							$pri_5 = (int) $row['priority_total'];
							break;
					}
				}
				mysqli_free_result($r);
			}
			//*********** END REP INDIVIDUAL STATS **************************
			
			
			//***********************************************
			// 				TEAM STATS
			//***********************************************
			$team_stats_id = '';
			$show_team_stats = false;
			
			if (isset($_SESSION['team_stats_id'])){
				
				$team_stats_id = $_SESSION['team_stats_id'];
				$show_team_stats = true;
				
				//COUNT() CONTACTS in contacts table for team
				$total_team_contacts = 0;
				$t = "SELECT count(a.contact_id) FROM contacts a
					  INNER JOIN reps b ON a.rep_id = b.rep_id
					  WHERE b.team_stats_id = '$team_stats_id'";
				$r = mysqli_query($dbc, $t);
				$row = mysqli_fetch_array($r, MYSQLI_NUM);
				$total_team_contacts = $row[0];
				mysqli_free_result($r);
				// If total_team_contacts = 0, we can't use it denominator.  Illegal operation....
				// SO, set the denominator to be 1 so it doesn't error out.
				$total_team_contacts_denominator = 1;
				if ($total_team_contacts != 0){
					$total_team_contacts_denominator = $total_team_contacts;
				}
			
				//TOTAL Prospects in contacts table for team - BY DISPOSITION
				$team_n_tot = $team_j_tot = $team_b_tot = $team_br_tot = $team_jb_tot = $team_rs_tot = 0;
				$c = "SELECT a.disposition, count(a.contact_id) as category_total
						FROM contacts a INNER JOIN reps b ON a.rep_id = b.rep_id
					   WHERE b.team_stats_id = '$team_stats_id'
					GROUP BY a.disposition";
				$r = @mysqli_query($dbc, $c);
				if (mysqli_num_rows($r) > 0){
					while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
						switch(trim($row['disposition'])){
							case 'J':
								$team_j_tot = (int) $row['category_total'];
								break;
							case 'B':
								$team_b_tot = (int) $row['category_total'];
								break;
							case 'BR':
								$team_br_tot = (int) $row['category_total'];
								break;
							case 'JB':
								$team_jb_tot = (int) $row['category_total'];
								break;
							case 'RS':
								$team_rs_tot = (int) $row['category_total'];
								break;
							case 'N':
								$team_n_tot = (int) $row['category_total'];
								break;
						}
					}
					mysqli_free_result($r);
				}
				
				//DUMPED CONTACTS for TEAM
				$team_total_dumped_contacts = 0;
				$t = "SELECT count(a.contact_id) FROM dumped_contacts a
					  INNER JOIN reps b ON a.rep_id = b.rep_id
					  WHERE b.team_stats_id = '$team_stats_id'";
				$r = mysqli_query($dbc, $t);
				$row = mysqli_fetch_array($r, MYSQLI_NUM);
				$team_total_dumped_contacts = $row[0];
				mysqli_free_result($r);
				
				//FLUSHED CONTACTS for TEAM
				$team_total_flushed_contacts = 0;
				$t = "SELECT flushed_contacts FROM reps
				   WHERE team_stats_id = '$team_stats_id'";
				$r = mysqli_query($dbc, $t);
				if ($r){
					if (mysqli_num_rows($r) > 0){
						while ($row = mysqli_fetch_array($r, MYSQLI_NUM)) {
							$team_total_flushed_contacts = $team_total_flushed_contacts + (int) $row[0];
						}
					}
					mysqli_free_result($r);
				}
				
				//TEAM LEADS TOTAL
				$team_total_leads = 0;
				$t = "SELECT count(a.rep_id) FROM lead_list a
					  INNER JOIN reps b ON a.rep_id = b.rep_id
					  WHERE b.team_stats_id = '$team_stats_id' 
					  AND a.is_prospect = 'N'";
				$r = mysqli_query($dbc, $t);
				$row = mysqli_fetch_array($r, MYSQLI_NUM);
				$team_total_leads = $row[0];
				mysqli_free_result($r);
				
				//TEAM LEADS - BY PRIORITY
				$t_pri_1 = $t_pri_2 = $t_pri_3 = $t_pri_4 = $t_pri_5 = 0;
				$c = "SELECT a.priority_number, count(a.l_id) as priority_total
						FROM lead_list a
						INNER JOIN reps b ON a.rep_id = b.rep_id
						WHERE b.team_stats_id = '$team_stats_id' 
						AND a.is_prospect = 'N'
					GROUP BY a.priority_number";
				$r = @mysqli_query($dbc, $c);
				if (mysqli_num_rows($r) > 0){
					while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
						switch($row['priority_number']){
							case '1':
								$t_pri_1 = (int) $row['priority_total'];
								break;
							case '2':
								$t_pri_2 = (int) $row['priority_total'];
								break;
							case '3':
								$t_pri_3 = (int) $row['priority_total'];
								break;
							case '4':
								$t_pri_4 = (int) $row['priority_total'];
								break;
							case '5':
								$t_pri_5 = (int) $row['priority_total'];
								break;
						}
					}
					mysqli_free_result($r);
				}
			
			}
			//*********** END TEAM STATS *************************************
			
			
			//****************************************************************
			//				COMPANY STATS
			//****************************************************************
			
			//TOTAL PROSPECTS in contacts table for company
			$total_company_contacts = 0;
			$c = "SELECT count(contact_id) FROM contacts";
			$rs = mysqli_query($dbc, $c);
			$row = mysqli_fetch_array($rs, MYSQLI_NUM);
			$total_company_contacts = $row[0];
			mysqli_free_result($rs);
			// If total_contacts = 0, we can't use it denominator.  Illegal operation....
			// SO, set the denominator to be 1 so it doesn't error out.
			$total_company_contacts_denominator = 1;
			if ($total_company_contacts != 0){
				$total_company_contacts_denominator = $total_company_contacts;
			}
			
			//TOTAL PROSPECTS in contacts table BY DISPOSITION
			$co_n_tot = $co_j_tot = $co_b_tot = $co_br_tot = $co_jb_tot = $co_rs_tot = $co_n_tot = 0;
			$ct = "SELECT disposition, count(contact_id) as category_total
					FROM contacts
				GROUP BY disposition";
			$r = mysqli_query($dbc, $ct);
			if (mysqli_num_rows($r) > 0){
				while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
					switch($row['disposition']){
						case 'J':
							$co_j_tot = (int) $row['category_total'];
							break;
						case 'B':
							$co_b_tot = (int) $row['category_total'];
							break;
						case 'BR':
							$co_br_tot = (int) $row['category_total'];
							break;
						case 'JB':
							$co_jb_tot = (int) $row['category_total'];
							break;
						case 'RS':
							$co_rs_tot = (int) $row['category_total'];
							break;
						case 'N':
							$co_n_tot = (int) $row['category_total'];
							break;
					}
				}
				mysqli_free_result($r);
			}
			
			//COMPANY DUMPED CONTACTS 
			$company_total_dumped_contacts = 0;
			$t = "SELECT count(contact_id) FROM dumped_contacts ";
			$r = mysqli_query($dbc, $t);
			$row = mysqli_fetch_array($r, MYSQLI_NUM);
			$company_total_dumped_contacts = $row[0];
			mysqli_free_result($r);
			
			//COMPANY FLUSHED CONTACTS contacts_archive
			$company_total_flushed_contacts = 0;
			$t = "SELECT count(contact_id) FROM contacts_archive ";
			$r = mysqli_query($dbc, $t);
			$row = mysqli_fetch_array($r, MYSQLI_NUM);
			$company_total_flushed_contacts = $row[0];
			mysqli_free_result($r);
		
			//COMPANY LEADS TOTAL
			$company_total_leads = 0;
			$t = "SELECT count(rep_id) FROM lead_list
					WHERE is_prospect = 'N'";
			$r = mysqli_query($dbc, $t);
			$row = mysqli_fetch_array($r, MYSQLI_NUM);
			$company_total_leads = $row[0];
			mysqli_free_result($r);
			
			//COMPANY LEADS - BY PRIORITY
			$c_pri_1 = $c_pri_2 = $c_pri_3 = $c_pri_4 = $c_pri_5 = 0;
			$c = "SELECT priority_number, count(l_id) as priority_total
					FROM lead_list
				   WHERE is_prospect = 'N'
				GROUP BY priority_number";
			$r = @mysqli_query($dbc, $c);
			if (mysqli_num_rows($r) > 0){
				while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
					switch($row['priority_number']){
						case '1':
							$c_pri_1 = (int) $row['priority_total'];
							break;
						case '2':
							$c_pri_2 = (int) $row['priority_total'];
							break;
						case '3':
							$c_pri_3 = (int) $row['priority_total'];
							break;
						case '4':
							$c_pri_4 = (int) $row['priority_total'];
							break;
						case '5':
							$c_pri_5 = (int) $row['priority_total'];
							break;
					}
				}
				mysqli_free_result($r);
			}
			//********END COMPANY WIDE STATS *****************************************
			
			mysqli_close($dbc);
		?>
		
		<!-- Agent Data Edit Form -->
		<div class="statsbox roundcorners opacity80">
			<h2>Agent Disposition Stats</h2>
			
			<div class="repstatsbox roundcorners opacity80">
				<!-- PERSONAL REP STATS -->
				<h4>Personal Disposition Stats(<?php echo $total_contacts.' total';?>)</h4>
				<ul>
					<li>
						<label>Joined (<?php echo $j_tot;?>):</label>
						<label class="labelright"><?php echo sprintf("%.2f%%", ($j_tot/$total_contacts_denominator) * 100); ?></label>
					</li>
					<li>
						<label>Bought (<?php echo $b_tot;?>):</label>
						<label class="labelright"><?php echo sprintf("%.2f%%", ($b_tot/$total_contacts_denominator) * 100); ?></label>
					</li>
					<li>
						<label>Bought & Referral Source (<?php echo $br_tot;?>):</label>
						<label class="labelright"><?php echo sprintf("%.2f%%", ($br_tot/$total_contacts_denominator) * 100); ?></label>
					</li>
					<li>
						<label>Joined & Bought (<?php echo $jb_tot;?>):</label>
						<label class="labelright"><?php echo sprintf("%.2f%%", ($jb_tot/$total_contacts_denominator) * 100); ?></label>
					</li>
					<li>
						<label>Referal Source (<?php echo $rs_tot;?>):</label>
						<label class="labelright"><?php echo sprintf("%.2f%%", ($rs_tot/$total_contacts_denominator) * 100); ?></label>
					</li>
					<li>
						<label>Nothing (<?php echo $n_tot;?>):</label>
						<label class="labelright"><?php echo sprintf("%.2f%%", ($n_tot/$total_contacts_denominator) * 100); ?></label>
					</li>
				</ul>
				<ul>
					<li>
						<label>Dumped Contacts:</label>
						<label class="labelright"><?php echo $total_dumped_contacts; ?></label>
					</li>
				</ul>
				<ul>
					<li>
						<label>Flushed Contacts:</label>
						<label class="labelright"><?php echo $total_flushed_contacts; ?></label>
					</li>
				</ul>
				<h4>Personal Leads Stats (<?php echo $total_leads.' total';?>)</h4>
				<ul>
					<li>
						<label>Priority 1 Leads:</label>
						<label class="labelright"><?php echo $pri_1; ?></label>
					</li>
					<li>
						<label>Priority 2 Leads:</label>
						<label class="labelright"><?php echo $pri_2; ?></label>
					</li>
					<li>
						<label>Priority 3 Leads:</label>
						<label class="labelright"><?php echo $pri_3; ?></label>
					</li>
					<li>
						<label>Priority 4 Leads:</label>
						<label class="labelright"><?php echo $pri_4; ?></label>
					</li>
					<li>
						<label>Priority 5 Leads:</label>
						<label class="labelright"><?php echo $pri_5; ?></label>
					</li>
				</ul>
				
			</div>
			
			<div class="companystatsbox roundcorners opacity80">
				<!-- COMPANY-WIDE STATS -->
				<h4>Company Disposition Stats (<?php echo $total_company_contacts.' total';?>)</h4>
				<ul>
					<li>
						<label>Joined (<?php echo $co_j_tot;?>):</label>
						<label class="labelright"><?php echo sprintf("%.2f%%", ($co_j_tot/$total_company_contacts_denominator) * 100); ?></label>
					</li>
					<li>
						<label>Bought (<?php echo $co_b_tot;?>):</label>
						<label class="labelright"><?php echo sprintf("%.2f%%", ($co_b_tot/$total_company_contacts_denominator) * 100); ?></label>
					</li>
					<li>
						<label>Bought & Referral Source (<?php echo $co_br_tot;?>):</label>
						<label class="labelright"><?php echo sprintf("%.2f%%", ($co_br_tot/$total_company_contacts_denominator) * 100); ?></label>
					</li>
					<li>
						<label>Joined & Bought (<?php echo $co_jb_tot;?>):</label>
						<label class="labelright"><?php echo sprintf("%.2f%%", ($co_jb_tot/$total_company_contacts_denominator) * 100); ?></label>
					</li>
					<li>
						<label>Referal Source (<?php echo $co_rs_tot;?>):</label>
						<label class="labelright"><?php echo sprintf("%.2f%%", ($co_rs_tot/$total_company_contacts_denominator) * 100); ?></label>
					</li>
					<li>
						<label>Nothing (<?php echo $co_n_tot;?>):</label>
						<label class="labelright"><?php echo sprintf("%.2f%%", ($co_n_tot/$total_company_contacts_denominator) * 100); ?></label>
					</li>
				</ul>
				<ul>
					<li>
						<label>Company Dumped Contacts:</label>
						<label class="labelright"><?php echo $company_total_dumped_contacts; ?></label>
					</li>
				</ul>
				<ul>
					<li>
						<label>Company Flushed Contacts:</label>
						<label class="labelright"><?php echo $company_total_flushed_contacts; ?></label>
					</li>
				</ul>
				<h4>Company Leads Stats (<?php echo $company_total_leads.' total';?>)</h4>
				<ul>
					<li>
						<label>Priority 1 Leads:</label>
						<label class="labelright"><?php echo $c_pri_1; ?></label>
					</li>
					<li>
						<label>Priority 2 Leads:</label>
						<label class="labelright"><?php echo $c_pri_2; ?></label>
					</li>
					<li>
						<label>Priority 3 Leads:</label>
						<label class="labelright"><?php echo $c_pri_3; ?></label>
					</li>
					<li>
						<label>Priority 4 Leads:</label>
						<label class="labelright"><?php echo $c_pri_4; ?></label>
					</li>
					<li>
						<label>Priority 5 Leads:</label>
						<label class="labelright"><?php echo $c_pri_5; ?></label>
					</li>
				</ul>
				
				<!-- TEAM STATS -->
				<?php
					if ($show_team_stats){
						
						$teamstr = '<h4>Team '.$team_stats_id.' Disposition Stats ('.$total_team_contacts.' total)</h4>
							<ul>
								<li>
									<label>Joined ('.$team_j_tot.'):</label>
									<label class="labelright">'.sprintf("%.2f%%", ($team_j_tot/$total_team_contacts_denominator) * 100).'</label>
								</li>
								<li>
									<label>Bought ('.$team_b_tot.'):</label>
									<label class="labelright">'.sprintf("%.2f%%", ($team_b_tot/$total_team_contacts_denominator) * 100).'</label>
								</li>
								<li>
									<label>Bought & Referral Source ('.$team_br_tot.'):</label>
									<label class="labelright">'.sprintf("%.2f%%", ($team_br_tot/$total_team_contacts_denominator) * 100).'</label>
								</li>
								<li>
									<label>Joined & Bought ('.$team_jb_tot.'):</label>
									<label class="labelright">'.sprintf("%.2f%%", ($team_jb_tot/$total_team_contacts_denominator) * 100).'</label>
								</li>
								<li>
									<label>Referal Source ('.$team_rs_tot.'):</label>
									<label class="labelright">'.sprintf("%.2f%%", ($team_rs_tot/$total_team_contacts_denominator) * 100).'</label>
								</li>
								<li>
									<label>Nothing ('.$team_n_tot.'):</label>
									<label class="labelright">'.sprintf("%.2f%%", ($team_n_tot/$total_team_contacts_denominator) * 100).'</label>
								</li>
							</ul>
							<ul>
								<li>
									<label>Team Dumped Contacts:</label>
									<label class="labelright">'.$team_total_dumped_contacts.'</label>
								</li>
								<li>
									<label>Team Flushed Contacts:</label>
									<label class="labelright">'.$team_total_flushed_contacts.'</label>
								</li>
							</ul>
							<h4> Team '.$team_stats_id.' Leads Stats ('.$team_total_leads.' total)</h4>
							<ul>
								<li>
									<label>Priority 1 Leads:</label>
									<label class="labelright">'.$t_pri_1.'</label>
								</li>
								<li>
									<label>Priority 2 Leads:</label>
									<label class="labelright">'.$t_pri_2.'</label>
								</li>
								<li>
									<label>Priority 3 Leads:</label>
									<label class="labelright">'.$t_pri_3.'</label>
								</li>
								<li>
									<label>Priority 4 Leads:</label>
									<label class="labelright">'.$t_pri_4.'</label>
								</li>
								<li>
									<label>Priority 5 Leads:</label>
									<label class="labelright">'.$t_pri_5.'</label>
								</li>
							</ul>';
						echo $teamstr;
					}
				?>
			</div>
			
			<div class="cleardiv"></div>
		</div>
		
	</div> <!-- close main content -->
</div>	<!-- close wrapper -->
<?php include('includes/html/footer.html'); ?>
</body>
</html>