<?php
session_start();
	$displayform = '';
	
	$admins_id = $_SESSION['admin_vfgrepid'];
			
	$curr_rid = $_GET['currentrid'];
	$rep_first = $_GET['repfirst'];
	$rep_last = $_GET['replast'];
	
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	//DB Connection
	require_once (MYSQL);
	
	//Set the checked vars:
	$al_checked = $ak_checked = $az_checked = $ar_checked = $ca_checked = $co_checked = $ct_checked = '';
	$de_checked = $dc_checked = $fl_checked = $ga_checked = $hi_checked = $id_checked = $il_checked = '';
	$in_checked = $ia_checked = $ks_checked = $ky_checked = $la_checked = $me_checked = $md_checked = '';
	$ma_checked = $mi_checked = $mn_checked = $ms_checked = $mo_checked = $mt_checked = $ne_checked = '';
	$nv_checked = $nh_checked = $nj_checked = $nm_checked = $ny_checked = $nc_checked = $nd_checked = '';
	$oh_checked = $ok_checked = $or_checked = $pa_checked = $ri_checked = $sc_checked = $sd_checked = '';
	$tn_checked = $tx_checked = $ut_checked = $vt_checked = $va_checked = $wa_checked = $wv_checked = '';
	$wi_checked = $wy_checked = '';
	//Get reps states that they are licensed from 
	$q = "SELECT lic_al, lic_ak, lic_az, lic_ar, lic_ca, lic_co, lic_ct,
				 lic_de, lic_dc, lic_fl, lic_ga, lic_hi, lic_id, lic_il,
				 lic_in, lic_ia, lic_ks, lic_ky, lic_la, lic_me, lic_md,
				 lic_ma, lic_mi, lic_mn, lic_ms, lic_mo, lic_mt, lic_ne,
				 lic_nv, lic_nh, lic_nj, lic_nm, lic_ny, lic_nc, lic_nd,
				 lic_oh, lic_ok, lic_or, lic_pa, lic_ri, lic_sc, lic_sd,
				 lic_tn, lic_tx, lic_ut, lic_vt, lic_va, lic_wa, lic_wv,
				 lic_wi, lic_wy
		  FROM rep_license
		  WHERE rep_id = '$curr_rid' LIMIT 1";
	$r = mysqli_query ($dbc, $q); // Run the query.
	if ($r) { // If it ran OK, display the record.
		if (mysqli_num_rows($r) == 1){
			// Fetch and print all the records:
			while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
				//Get checked state for each of the states
				if ($row['lic_al'] == 'Y') {	
					$al_checked = ' checked="checked" ';
				}
				if ($row['lic_ak'] == 'Y') {
					$ak_checked = ' checked="checked" ';
				}
				if ($row['lic_az'] == 'Y') {
					$az_checked = ' checked="checked" ';
				}
				if ($row['lic_ar'] == 'Y') {
					$ar_checked = ' checked="checked" ';
				}
				if ($row['lic_ca'] == 'Y') {
					$ca_checked = ' checked="checked" ';
				}
				if ($row['lic_co'] == 'Y') {	
					$co_checked = ' checked="checked" ';
				}
				if ($row['lic_ct'] == 'Y') {
					$ct_checked = ' checked="checked" ';
				}
				if ($row['lic_de'] == 'Y') {
					$de_checked = ' checked="checked" ';
				}
				if ($row['lic_dc'] == 'Y') {
					$dc_checked = ' checked="checked" ';
				}
				if ($row['lic_fl'] == 'Y') {
					$fl_checked = ' checked="checked" ';
				}
				if ($row['lic_ga'] == 'Y') {	
					$ga_checked = ' checked="checked" ';
				}
				if ($row['lic_hi'] == 'Y') {	
					$hi_checked = ' checked="checked" ';
				}
				if ($row['lic_id'] == 'Y') {
					$id_checked = ' checked="checked" ';
				}
				if ($row['lic_il'] == 'Y') {
					$il_checked = ' checked="checked" ';
				}
				if ($row['lic_in'] == 'Y') {
					$in_checked = ' checked="checked" ';
				}
				if ($row['lic_ia'] == 'Y') {
					$ia_checked = ' checked="checked" ';
				}
				if ($row['lic_ks'] == 'Y') {	
					$ks_checked = ' checked="checked" ';
				}
				if ($row['lic_ky'] == 'Y') {
					$ky_checked = ' checked="checked" ';
				}
				if ($row['lic_la'] == 'Y') {
					$la_checked = ' checked="checked" ';
				}
				if ($row['lic_me'] == 'Y') {
					$me_checked = ' checked="checked" ';
				}
				if ($row['lic_md'] == 'Y') {
					$md_checked = ' checked="checked" ';
				}
				if ($row['lic_ma'] == 'Y') {	
					$ma_checked = ' checked="checked" ';
				}
				if ($row['lic_mi'] == 'Y') {	
					$mi_checked = ' checked="checked" ';
				}
				if ($row['lic_mn'] == 'Y') {
					$mn_checked = ' checked="checked" ';
				}
				if ($row['lic_ms'] == 'Y') {
					$ms_checked = ' checked="checked" ';
				}
				if ($row['lic_mo'] == 'Y') {
					$mo_checked = ' checked="checked" ';
				}
				if ($row['lic_mt'] == 'Y') {
					$mt_checked = ' checked="checked" ';
				}
				if ($row['lic_ne'] == 'Y') {	
					$ne_checked = ' checked="checked" ';
				}
				if ($row['lic_nv'] == 'Y') {
					$nv_checked = ' checked="checked" ';
				}
				if ($row['lic_nh'] == 'Y') {
					$nh_checked = ' checked="checked" ';
				}
				if ($row['lic_nj'] == 'Y') {
					$nj_checked = ' checked="checked" ';
				}
				if ($row['lic_nm'] == 'Y') {
					$nm_checked = ' checked="checked" ';
				}
				if ($row['lic_ny'] == 'Y') {	
					$ny_checked = ' checked="checked" ';
				}
				if ($row['lic_nc'] == 'Y') {	
					$nc_checked = ' checked="checked" ';
				}
				if ($row['lic_nd'] == 'Y') {
					$nd_checked = ' checked="checked" ';
				}
				if ($row['lic_oh'] == 'Y') {
					$oh_checked = ' checked="checked" ';
				}
				if ($row['lic_ok'] == 'Y') {
					$ok_checked = ' checked="checked" ';
				}
				if ($row['lic_or'] == 'Y') {
					$or_checked = ' checked="checked" ';
				}
				if ($row['lic_pa'] == 'Y') {	
					$pa_checked = ' checked="checked" ';
				}
				if ($row['lic_ri'] == 'Y') {
					$ri_checked = ' checked="checked" ';
				}
				if ($row['lic_sc'] == 'Y') {
					$sc_checked = ' checked="checked" ';
				}
				if ($row['lic_sd'] == 'Y') {
					$sd_checked = ' checked="checked" ';
				}
				if ($row['lic_tn'] == 'Y') {
					$tn_checked = ' checked="checked" ';
				}
				if ($row['lic_tx'] == 'Y') {	
					$tx_checked = ' checked="checked" ';
				}
				if ($row['lic_ut'] == 'Y') {	
					$ut_checked = ' checked="checked" ';
				}
				if ($row['lic_vt'] == 'Y') {
					$vt_checked = ' checked="checked" ';
				}
				if ($row['lic_va'] == 'Y') {
					$va_checked = ' checked="checked" ';
				}
				if ($row['lic_wa'] == 'Y') {
					$wa_checked = ' checked="checked" ';
				}
				if ($row['lic_wv'] == 'Y') {
					$wv_checked = ' checked="checked" ';
				}
				if ($row['lic_wi'] == 'Y') {	
					$wi_checked = ' checked="checked" ';
				}
				if ($row['lic_wy'] == 'Y') {
					$wy_checked = ' checked="checked" ';
				}
				
				
				$displayform = 
					'<div style="display:none">
						<div class="contact-content">
							<h1 class="contact-title">Licensing States for '.$rep_first.' '.$rep_last.'</h1>
							<div class="repstatesbox roundcorners opacity80">
								<div class="editcontactform">
									<form name="edit_rep_states" id="edit_rep_states" onSubmit="return updateRepStates();" >
									<table align="center" border="0" width="95%">
										<th colspan=5>
											Agent Licensing States
										</th>
										<tr>
											<td><input type="checkbox" name="lic_al" id="lic_al" value="Y" '.$al_checked.' >Alabama</td>
											<td><input type="checkbox" name="lic_ak" id="lic_ak" value="Y" '.$ak_checked.' >Alaska</td>
											<td><input type="checkbox" name="lic_az" id="lic_az" value="Y" '.$az_checked.' >Arizona</td>
											<td><input type="checkbox" name="lic_ar" id="lic_ar" value="Y" '.$ar_checked.' >Arkansas</td>
											<td><input type="checkbox" name="lic_ca" id="lic_ca" value="Y" '.$ca_checked.' >California</td>
										</tr>
										<tr>
											<td><input type="checkbox" name="lic_co" id="lic_co" value="Y" '.$co_checked.' >Colorado</td>
											<td><input type="checkbox" name="lic_ct" id="lic_ct" value="Y" '.$ct_checked.' >Connecticut</td>
											<td><input type="checkbox" name="lic_de" id="lic_de" value="Y" '.$de_checked.' >Delaware</td>
											<td><input type="checkbox" name="lic_dc" id="lic_dc" value="Y" '.$dc_checked.' >District of Columbia</td>
											<td><input type="checkbox" name="lic_fl" id="lic_fl" value="Y" '.$fl_checked.' >Florida</td>
										</tr>
										<tr>
											<td><input type="checkbox" name="lic_ga" id="lic_ga" value="Y" '.$ga_checked.' >Georgia</td>
											<td><input type="checkbox" name="lic_hi" id="lic_hi" value="Y" '.$hi_checked.' >Hawaii</td>
											<td><input type="checkbox" name="lic_id" id="lic_id" value="Y" '.$id_checked.' >Idaho</td>
											<td><input type="checkbox" name="lic_il" id="lic_il" value="Y" '.$il_checked.' >Illinois</td>
											<td><input type="checkbox" name="lic_in" id="lic_in" value="Y" '.$in_checked.' >Indiana</td>
										</tr>
										<tr>
											<td><input type="checkbox" name="lic_ia" id="lic_ia" value="Y" '.$ia_checked.' >Iowa</td>
											<td><input type="checkbox" name="lic_ks" id="lic_ks" value="Y" '.$ks_checked.' >Kansas</td>
											<td><input type="checkbox" name="lic_ky" id="lic_ky" value="Y" '.$ky_checked.' >Kentucky</td>
											<td><input type="checkbox" name="lic_la" id="lic_la" value="Y" '.$la_checked.' >Louisiana</td>
											<td><input type="checkbox" name="lic_me" id="lic_me" value="Y" '.$me_checked.' >Maine</td>
										</tr>
										<tr>
											<td><input type="checkbox" name="lic_md" id="lic_md" value="Y" '.$md_checked.' >Maryland</td>
											<td><input type="checkbox" name="lic_ma" id="lic_ma" value="Y" '.$ma_checked.' >Massachusetts</td>
											<td><input type="checkbox" name="lic_mi" id="lic_mi" value="Y" '.$mi_checked.' >Michigan</td>
											<td><input type="checkbox" name="lic_mn" id="lic_mn" value="Y" '.$mn_checked.' >Minnesota</td>
											<td><input type="checkbox" name="lic_ms" id="lic_ms" value="Y" '.$ms_checked.' >Mississippi</td>
										</tr>
										<tr>
											<td><input type="checkbox" name="lic_mo" id="lic_mo" value="Y" '.$mo_checked.' >Missouri</td>
											<td><input type="checkbox" name="lic_mt" id="lic_mt" value="Y" '.$mt_checked.' >Montana</td>
											<td><input type="checkbox" name="lic_ne" id="lic_ne" value="Y" '.$ne_checked.' >Nebraska</td>
											<td><input type="checkbox" name="lic_nv" id="lic_nv" value="Y" '.$nv_checked.' >Nevada</td>
											<td><input type="checkbox" name="lic_nh" id="lic_nh" value="Y" '.$nh_checked.' >New Hampshire</td>
										</tr>
										<tr>
											<td><input type="checkbox" name="lic_nj" id="lic_nj" value="Y" '.$nj_checked.' >New Jersey</td>
											<td><input type="checkbox" name="lic_nm" id="lic_nm" value="Y" '.$nm_checked.' >New Mexico</td>
											<td><input type="checkbox" name="lic_ny" id="lic_ny" value="Y" '.$ny_checked.' >New York</td>
											<td><input type="checkbox" name="lic_nc" id="lic_nc" value="Y" '.$nc_checked.' >North Carolina</td>
											<td><input type="checkbox" name="lic_nd" id="lic_nd" value="Y" '.$nd_checked.' >North Dakota</td>
										</tr>
										<tr>
											<td><input type="checkbox" name="lic_oh" id="lic_oh" value="Y" '.$oh_checked.' >Ohio</td>
											<td><input type="checkbox" name="lic_ok" id="lic_ok" value="Y" '.$ok_checked.' >Oklahoma</td>
											<td><input type="checkbox" name="lic_or" id="lic_or" value="Y" '.$or_checked.' >Oregon</td>
											<td><input type="checkbox" name="lic_pa" id="lic_pa" value="Y" '.$pa_checked.' >Pennsylvania</td>
											<td><input type="checkbox" name="lic_ri" id="lic_ri" value="Y" '.$ri_checked.' >Rhode Island</td>
										</tr>
										<tr>
											<td><input type="checkbox" name="lic_sc" id="lic_sc" value="Y" '.$sc_checked.' >South Carolina</td>
											<td><input type="checkbox" name="lic_sd" id="lic_sd" value="Y" '.$sd_checked.' >South Dakota</td>
											<td><input type="checkbox" name="lic_tn" id="lic_tn" value="Y" '.$tn_checked.' >Tennessee</td>
											<td><input type="checkbox" name="lic_tx" id="lic_tx" value="Y" '.$tx_checked.' >Texas</td>
											<td><input type="checkbox" name="lic_ut" id="lic_ut" value="Y" '.$ut_checked.' >Utah</td>
										</tr>
										<tr>
											<td><input type="checkbox" name="lic_vt" id="lic_vt" value="Y" '.$vt_checked.' >Vermont</td>
											<td><input type="checkbox" name="lic_va" id="lic_va" value="Y" '.$va_checked.' >Virginia</td>
											<td><input type="checkbox" name="lic_wa" id="lic_wa" value="Y" '.$wa_checked.' >Washington</td>
											<td><input type="checkbox" name="lic_wv" id="lic_wv" value="Y" '.$wv_checked.' >West Virginia</td>
											<td><input type="checkbox" name="lic_wi" id="lic_wi" value="Y" '.$wi_checked.' >Wisconsin</td>
										</tr>
										<tr>
											<td><input type="checkbox" name="lic_wy" id="lic_wy" value="Y" '.$wy_checked.' >Wyoming</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
										</tr>
									</table>
									<p align="center">
										<input type="submit" class="generalbutton" value="Update" /><br />
										<input type="hidden" name="rid" id="rid" value="'.$curr_rid.'" />
										<div id="ajax_verify_state_update"></div>
									</p>
									</form>
								</div>
							</div>
						</div>
					</div>';
			}
		
		mysqli_free_result($r);
		}
	} else {
		echo '<div style="display:none">
			<div class="modal-content">
				<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $q . '</p>
			</div>
		</div>';
	}
	echo $displayform;
	
	
	//unset($_GET);
	mysqli_close($dbc); // Close the database connection.		
?>