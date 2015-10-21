<?php
session_start();
	//echo '<p>You selected '.$_GET['companyid'].'</p>';
	$repsid = $_SESSION['rep_id'];
	
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	
	//DB Connection
	require_once (MYSQL);

	//TOTAL LEADS in contacts table for rep
	$total_contacts = 0;
	$t = "SELECT count(rep_id) FROM contacts
	   WHERE rep_id = '$repsid'";
	$r = @mysqli_query($dbc, $t);
	$row = mysqli_fetch_array($r, MYSQLI_NUM);
	$total_contacts = $row[0];
	mysqli_free_result($r);
	
	// If total_contacts = 0, we can't use it to divide by.  Illegal operation....
	// SO, set the denominator to be 1 so it doesn't error out.
	$denominator = 1;
	if ($total_contacts != 0){
		$denominator = $total_contacts;
	}
	//TOTAL LEADS in contacts table for rep
	$n_tot = $j_tot = $b_tot = $jb_tot = $br_tot = $rs_tot = 0;
	$c = "SELECT disposition, count(contact_id) as category_total
			FROM contacts
		   WHERE rep_id = '$repsid'
		GROUP BY disposition";
	$r = @mysqli_query($dbc, $c);
	if (mysqli_num_rows($r) > 0){
		while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			switch($row['disposition']){
				case 'J':
					$j_tot = (int) $row['category_total'];
					break;
				case 'B':
					$b_tot = (int) $row['category_total'];
					break;
				case 'JB':
					$jb_tot = (int) $row['category_total'];
					break;
				case 'BR':
					$br_tot = (int) $row['category_total'];
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
		
	//Can use one of the following:
	//1.) number_format(($j_tot/$total_contacts)*100, 2).'%</p>
	//
	//2.) sprintf("%.2f%%", ($j_tot/$total_contacts) * 100).'</p>
	//
	//Using sprintf for now
	//Using sprintf for now
	$displayform = '<div style="display:none">
		<div class="contact-top"></div>
		<div class="contact-content">
		<h1 class="contact-title">Disposition Percentages</h1>
		<div class="contact-loading" style="display:none"></div>
		<div class="contact-message" style="display:none"></div>
		<br />
		<div class="dispositionpercentages">
			<table class="disposition_table">
			<th align="center" colspan="2">All contacts in system:  '.$total_contacts.'</th>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td class="rightalign">Joined ('.$j_tot.'):</td>
				<td>'.sprintf("%.2f%%", ($j_tot/$denominator) * 100).'</td>
			</tr>
			<tr>
				<td class="rightalign">Bought ('.$b_tot.'):</td>
				<td>'.sprintf("%.2f%%", ($b_tot/$denominator) * 100).'</td>
			</tr>
				<td class="rightalign">Joined & Bought ('.$jb_tot.'):</td>
				<td>'.sprintf("%.2f%%", ($jb_tot/$denominator) * 100).'</td>
			</tr>
			<tr>
				<td class="rightalign">Bought & Referral Source ('.$br_tot.'):</td>
				<td>'.sprintf("%.2f%%", ($br_tot/$denominator) * 100).'</td>
			</tr>
			<tr>
				<td class="rightalign">Referral Source ('.$rs_tot.'):</td>
				<td>'.sprintf("%.2f%%", ($rs_tot/$denominator) * 100).'</td>
			</tr>
			<tr>
				<td class="rightalign">Nothing ('.$n_tot.'):</td>
				<td>'.sprintf("%.2f%%", ($n_tot/$denominator) * 100).'</td>
			</tr>
			</table>
		</div>
	 </div> <!-- close class="contact-content" -->
	</div>';
	echo $displayform;
	
	//unset($_GET);
	mysqli_close($dbc); // Close the database connection.		
?>