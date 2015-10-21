<?php
session_start();

	include ('includes/config.inc.php');
	include ('includes/selectlists.php');
	include ('includes/phpfunctions.php');
	
	//DB Connection
	require_once (MYSQL);
	$messages = array();
	
	//echo 'Getting in here.';
	/*$postData = $_POST;
	$insert_fields = '';
	foreach ($postData as $k => $v) {
		if ($k != 'rid'){
			$insert_fields .= $k."='".$v."', ";
		}
	}*/
	
	$insert_fields = '';
	// PARSE any/all POST fields to determine which checkboxes were checked
	// must do it for each state...ugh
	if (isset($_POST['lic_al'])){
		$insert_fields .= "lic_al = 'Y', ";
	} else {
		$insert_fields .= "lic_al = 'N', ";
	}
	if (isset($_POST['lic_ak'])){
		$insert_fields .= "lic_ak = 'Y', ";
	} else {
		$insert_fields .= "lic_ak = 'N', ";
	}
	if (isset($_POST['lic_az'])){
		$insert_fields .= "lic_az = 'Y', ";
	} else {
		$insert_fields .= "lic_az = 'N', ";
	}
	if (isset($_POST['lic_ar'])){
		$insert_fields .= "lic_ar = 'Y', ";
	} else {
		$insert_fields .= "lic_ar = 'N', ";
	}
	if (isset($_POST['lic_ca'])){
		$insert_fields .= "lic_ca = 'Y', ";
	} else {
		$insert_fields .= "lic_ca = 'N', ";
	}
	if (isset($_POST['lic_co'])){
		$insert_fields .= "lic_co = 'Y', ";
	} else {
		$insert_fields .= "lic_co = 'N', ";
	}
	if (isset($_POST['lic_ct'])){
		$insert_fields .= "lic_ct = 'Y', ";
	} else {
		$insert_fields .= "lic_ct = 'N', ";
	}
	if (isset($_POST['lic_de'])){
		$insert_fields .= "lic_de = 'Y', ";
	} else {
		$insert_fields .= "lic_de = 'N', ";
	}
	if (isset($_POST['lic_dc'])){
		$insert_fields .= "lic_dc = 'Y', ";
	} else {
		$insert_fields .= "lic_dc = 'N', ";
	}
	if (isset($_POST['lic_fl'])){
		$insert_fields .= "lic_fl = 'Y', ";
	} else {
		$insert_fields .= "lic_fl = 'N', ";
	}
	if (isset($_POST['lic_ga'])){
		$insert_fields .= "lic_ga = 'Y', ";
	} else {
		$insert_fields .= "lic_ga = 'N', ";
	}
	if (isset($_POST['lic_hi'])){
		$insert_fields .= "lic_hi = 'Y', ";
	} else {
		$insert_fields .= "lic_hi = 'N', ";
	}
	if (isset($_POST['lic_id'])){
		$insert_fields .= "lic_id = 'Y', ";
	} else {
		$insert_fields .= "lic_id = 'N', ";
	}
	if (isset($_POST['lic_il'])){
		$insert_fields .= "lic_il = 'Y', ";
	} else {
		$insert_fields .= "lic_il = 'N', ";
	}
	if (isset($_POST['lic_in'])){
		$insert_fields .= "lic_in = 'Y', ";
	} else {
		$insert_fields .= "lic_in = 'N', ";
	}
	if (isset($_POST['lic_ia'])){
		$insert_fields .= "lic_ia = 'Y', ";
	} else {
		$insert_fields .= "lic_ia = 'N', ";
	}
	if (isset($_POST['lic_ks'])){
		$insert_fields .= "lic_ks = 'Y', ";
	} else {
		$insert_fields .= "lic_ks = 'N', ";
	}
	if (isset($_POST['lic_ky'])){
		$insert_fields .= "lic_ky = 'Y', ";
	} else {
		$insert_fields .= "lic_ky = 'N', ";
	}
	if (isset($_POST['lic_la'])){
		$insert_fields .= "lic_la = 'Y', ";
	} else {
		$insert_fields .= "lic_la = 'N', ";
	}
	if (isset($_POST['lic_me'])){
		$insert_fields .= "lic_me = 'Y', ";
	} else {
		$insert_fields .= "lic_me = 'N', ";
	}
	if (isset($_POST['lic_md'])){
		$insert_fields .= "lic_md = 'Y', ";
	} else {
		$insert_fields .= "lic_md = 'N', ";
	}
	if (isset($_POST['lic_ma'])){
		$insert_fields .= "lic_ma = 'Y', ";
	} else {
		$insert_fields .= "lic_ma = 'N', ";
	}
	if (isset($_POST['lic_mi'])){
		$insert_fields .= "lic_mi = 'Y', ";
	} else {
		$insert_fields .= "lic_mi = 'N', ";
	}
	if (isset($_POST['lic_mn'])){
		$insert_fields .= "lic_mn = 'Y', ";
	} else {
		$insert_fields .= "lic_mn = 'N', ";
	}
	if (isset($_POST['lic_ms'])){
		$insert_fields .= "lic_ms = 'Y', ";
	} else {
		$insert_fields .= "lic_ms = 'N', ";
	}
	if (isset($_POST['lic_mo'])){
		$insert_fields .= "lic_mo = 'Y', ";
	} else {
		$insert_fields .= "lic_mo = 'N', ";
	}
	if (isset($_POST['lic_mt'])){
		$insert_fields .= "lic_mt = 'Y', ";
	} else {
		$insert_fields .= "lic_mt = 'N', ";
	}
	if (isset($_POST['lic_ne'])){
		$insert_fields .= "lic_ne = 'Y', ";
	} else {
		$insert_fields .= "lic_ne = 'N', ";
	}
	if (isset($_POST['lic_nv'])){
		$insert_fields .= "lic_nv = 'Y', ";
	} else {
		$insert_fields .= "lic_nv = 'N', ";
	}
	if (isset($_POST['lic_nh'])){
		$insert_fields .= "lic_nh = 'Y', ";
	} else {
		$insert_fields .= "lic_nh = 'N', ";
	}
	if (isset($_POST['lic_nj'])){
		$insert_fields .= "lic_nj = 'Y', ";
	} else {
		$insert_fields .= "lic_nj = 'N', ";
	}
	if (isset($_POST['lic_nm'])){
		$insert_fields .= "lic_nm = 'Y', ";
	} else {
		$insert_fields .= "lic_nm = 'N', ";
	}
	if (isset($_POST['lic_ny'])){
		$insert_fields .= "lic_ny = 'Y', ";
	} else {
		$insert_fields .= "lic_ny = 'N', ";
	}
	if (isset($_POST['lic_nc'])){
		$insert_fields .= "lic_nc = 'Y', ";
	} else {
		$insert_fields .= "lic_nc = 'N', ";
	}
	if (isset($_POST['lic_nd'])){
		$insert_fields .= "lic_nd = 'Y', ";
	} else {
		$insert_fields .= "lic_nd = 'N', ";
	}
	if (isset($_POST['lic_oh'])){
		$insert_fields .= "lic_oh = 'Y', ";
	} else {
		$insert_fields .= "lic_oh = 'N', ";
	}
	if (isset($_POST['lic_ok'])){
		$insert_fields .= "lic_ok = 'Y', ";
	} else {
		$insert_fields .= "lic_ok = 'N', ";
	}
	if (isset($_POST['lic_or'])){
		$insert_fields .= "lic_or = 'Y', ";
	} else {
		$insert_fields .= "lic_or = 'N', ";
	}
	if (isset($_POST['lic_pa'])){
		$insert_fields .= "lic_pa = 'Y', ";
	} else {
		$insert_fields .= "lic_pa = 'N', ";
	}
	if (isset($_POST['lic_ri'])){
		$insert_fields .= "lic_ri = 'Y', ";
	} else {
		$insert_fields .= "lic_ri = 'N', ";
	}
	if (isset($_POST['lic_sc'])){
		$insert_fields .= "lic_sc = 'Y', ";
	} else {
		$insert_fields .= "lic_sc = 'N', ";
	}
	if (isset($_POST['lic_sd'])){
		$insert_fields .= "lic_sd = 'Y', ";
	} else {
		$insert_fields .= "lic_sd = 'N', ";
	}
	if (isset($_POST['lic_tn'])){
		$insert_fields .= "lic_tn = 'Y', ";
	} else {
		$insert_fields .= "lic_tn = 'N', ";
	}
	if (isset($_POST['lic_tx'])){
		$insert_fields .= "lic_tx = 'Y', ";
	} else {
		$insert_fields .= "lic_tx = 'N', ";
	}
	if (isset($_POST['lic_ut'])){
		$insert_fields .= "lic_ut = 'Y', ";
	} else {
		$insert_fields .= "lic_ut = 'N', ";
	}
	if (isset($_POST['lic_vt'])){
		$insert_fields .= "lic_vt = 'Y', ";
	} else {
		$insert_fields .= "lic_vt = 'N', ";
	}
	if (isset($_POST['lic_va'])){
		$insert_fields .= "lic_va = 'Y', ";
	} else {
		$insert_fields .= "lic_va = 'N', ";
	}
	if (isset($_POST['lic_wa'])){
		$insert_fields .= "lic_wa = 'Y', ";
	} else {
		$insert_fields .= "lic_wa = 'N', ";
	}
	if (isset($_POST['lic_wv'])){
		$insert_fields .= "lic_wv = 'Y', ";
	} else {
		$insert_fields .= "lic_wv = 'N', ";
	}
	if (isset($_POST['lic_wi'])){
		$insert_fields .= "lic_wi = 'Y', ";
	} else {
		$insert_fields .= "lic_wi = 'N', ";
	}
	if (isset($_POST['lic_wy'])){
		$insert_fields .= "lic_wy = 'Y', ";
	} else {
		$insert_fields .= "lic_wy = 'N', ";
	}
	// The substr(field, 0, -2) is stripping off the trailing 
	// comma-space from the field string created in the loop above
	$updatesql = "UPDATE rep_license SET ".substr($insert_fields, 0, -2)." WHERE rep_id = '".$_POST['rid']."'";
	
	//echo $updatesql;
	
	//RUN UPDATE QUERY
	$rs= mysqli_query($dbc, $updatesql);
	if (mysqli_affected_rows($dbc) == 1){
		echo '<p>States update successful.</p>';
						
	} else {
		echo '<p>No Update Made.</p>';
		//echo '<p>'.mysqli_error($dbc).'</p>';
	
	}	// close mysqli_affected_rows($dbc) == 1
	
	mysqli_close($dbc); // Close the database connection.
?>