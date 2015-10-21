<?php
session_start();
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
<title>EAPCRM Leads Management - It's Easy As Pie</title>
<link rel="stylesheet" type="text/css" media="screen" href="./css/eapcrm.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/eap_navmenu.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/eap_forms.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/eap_modal.css" />
<script type="text/javascript" src="./js/autotab.js"></script>
<script src="./js/jquery1-9-1.js"></script>
<script src="./js/jquery_migrate1-1-0.js"></script>
<script src="./js/jquery.simplemodal-1.4.4.js"></script>
<script src="./js/tier_list.js"></script>
<script src="./js/dynamic_tier_list.js"></script>
<script>
/*$( document ).ready(function() {
    $("#modallink").click(function() {
		//alert( "You clicked a link!" );
		$("#openModal").modal();
	});
	
	
});*/
function addContact(){

	var dataChanged = false;
		
	
	var formdata = $('#add_contact_form').serialize();
	
	//call ajax_add_contact.php
	//the ajax_verify_update div is contained in the output
	//from the call to ajaxgetlead_info.php
	//
	$.ajax({
		url:"ajax_add_contact.php",
		type: "POST",
		data: formdata,
		dataType: "html",		
		success:function(result){
			$("#ajax_verify_update").html(result);
		}	//end success:function
	}); //end $.ajax
	
	//Have to reset the original values equal to what
	//was just submitted to start fresh for any
	//subsequent update!
	//

	//alert("Update Form Button clicked.\n"+"First Name: "+firstName+"\n"+"Last Name: "+lastName+"\n");
	//alert("Update Form Button clicked.\n"+"Data Changed Flag: "+dataChanged);
	return false;
}
</script>
</head>

<body>
<div class="wrapper showgoldborder">
	<div class="header">
		<span>EAPCRM.com.  Leads Management - "It's easy as pie..."</span>
	</div>
	<div class="topnav">
		<ul id="menu_top">
			<li><a href="rep_maindash.php">Home</a></li>
			<li><a href="rep_add_contact.php">Add Contact</a></li>
			<li><a href="#">Active Contacts</a>
				<ul id="static_rpt_submenu">
					<li><a href="rep_manage_contacts.php">Manage Contacts</a></li>
					<li><a href="rep_manage_tasks.php">Manage Tasks</a></li>
					<li><a href="rep_manage_team.php">Manage Team</a></li>
				</ul>
			</li>
			<li><a href="rep_dumped_contacts.php">Dumped Contacts</a></li>
			<li><a href="rep_logout.php">Logout</a></li>
		</ul>
	</div>
	
	<div class="maincontent">
		<p class="welcomespan">Welcome <?php echo 'User.'; ?></p>
		
		<?php
			include ('includes/selectlists.php');
		?>
		<a href="#openModal">Open Modal</a>

		<div id="openModal" class="modalDialog">
			<div id="modaldisplay">
				<a href="#close" title="Close" class="close">X</a>

				<div class="formdiv">
					<form name="add_contact_form" id="add_contact_form" onSubmit="return addContact()" >
						
						<h2>Enter Contact Info:</h2>
						<ul>
							<li>
								<label for="first_name">First Name:</label>
								<input type="text" id="first_name" name="first_name" class="input200 capitalwords" maxlength="30"
									value="" />
							</li>
							<li>
								<label for="last_name">Last Name:</label>
								<input type="text" id="last_name" name="last_name" class="input200 capitalwords" maxlength="45"  
									value="" />
							</li>
							<li>
								<label for="email">Email:</label>
								<input type="text" id="email" name="email" class="input200" maxlength="80"
									value="" />
							</li>
							<li>
								<label>Phone:</label>
								<input type="text" name="phone" id="phone" class="input125"
											value="" />
							</li>
							<li>
								<label for="city">City:</label>
								<input type="city" id="city" name="city" class="input125" maxlength="40"  
									value=""/>
							</li>
							<li>
							<label for="state">State</label>
								<?php 
									$selected_state = "";
									if (isset($_POST['state'])){
										$selected_state = $_POST['state'];
									} 
								?>
								<select name="state" id="state">
									<?php
										foreach($states as $id=>$name){
											if($selected_state == $id){
												$sel = 'selected="selected"';
											}
											else{
												$sel = '';
											}
											echo "<option $sel value=\"$id\">$name</option>";
										}
									?>
								</select>
							</li>
							<li>
								<label for="timezone">Timezone:</label>
								<?php 
									$selected_tz = "";
									if (isset($_POST['timezone'])){
										$selected_tz = $_POST['timezone'];
									}
								?>
								<select id="timezone" name="timezone">
									<?php
										foreach($americaTimeZones as $id=>$name){
											if($selected_tz == $id){
												$sel = 'selected="selected"';
											}
											else{
												$sel = '';
											}
											echo "<option $sel value=\"$id\">$name</option>";
										}
									?>
								</select>
							</li>
							<li>
								<?php
									if (!isset($tier)){
										$tier = '1';
									}
								?>
								<label>Tier:</label>
								<input type="radio" name="tier" id="tier1" value="1" <?php echo ($tier == '1' ? 'checked' : ''); ?> 
										 onClick="javascript: setList(this.value);" /><label for="tier1">1</label>
								<input type="radio" name="tier" id="tier2" value="2" <?php echo ($tier == '2' ? 'checked' : ''); ?>  
										 onClick="javascript: setList(this.value);" /><label for="tier2">2</label>
								<input type="radio" name="tier" id="tier3" value="3" <?php echo ($tier == '3' ? 'checked' : ''); ?>  
										 onClick="javascript: setList(this.value);" /><label for="tier3">3</label>
								<input type="radio" name="tier" id="tier4" value="4" <?php echo ($tier == '4' ? 'checked' : ''); ?>  
										 onClick="javascript: setList(this.value);" /><label for="tier4">4</label>
							</li>
							<li>
								<label for="tierstep">Status:</label>
								<?php
									//get the tierstep array based on the tier level
									$tierarray = '';
									$thistier = '';
									if (isset($_POST['tier'])){
										$thistier = $_POST['tier'];
									}
									switch ($thistier){
										case '1':
											$tierarray = $tier1steps;
											break;
										case '2':
											$tierarray = $tier2steps;
											break;
										case '3':
											$tierarray = $tier3steps;
											break;
										case '4':
											$tierarray = $tier4steps;
											break;
										default:
											$tierarray = $tier1steps;
											break;
									}
									
									$selected_tier = "";
									if (isset($_POST['tierstep'])){
										$selected_tier = $_POST['tierstep'];
									} 
								?>
								<select name="tierstep" id="tierstep">
									<?php
										foreach($tierarray as $id=>$name){
											if($selected_tier == $id){
												$sel = 'selected="selected"';
											}
											else{
												$sel = '';
											}
											echo "<option $sel value=\"$id\">$name</option>";
										}
									?>
								</select>
							</li>
							<li>
							<label for="team_member">Team Member:</label>
								<?php 
									$selected_teammember = "";
									if (isset($_POST['team_member'])){
										$selected_teammember = $_POST['team_member'];
									} 
								?>
								<select name="team_member" id="team_member">
									<?php
										foreach($yes_or_no as $id=>$name){
											if($selected_teammember == $id){
												$sel = 'selected="selected"';
											}
											else{
												$sel = '';
											}
											echo "<option $sel value=\"$id\">$name</option>";
										}
									?>
								</select>
							</li>
							<li>
								<label for="notes">Notes:</label>
								<textarea id="notes" name="notes" rows="6" cols="40"></textarea> 
										
							</li>
							<li>
								<input type="hidden" name="submitted" value="addcontact" />
								<input type="submit" class="button" value="Add Contact" />
							</li>
							<li>
								<div id="ajax_verify_update">Please fill out form.</div>
							</li>
						</ul>
					</form>
				</div>		
			</div>
			
		</div> <!-- close openmodal -->		
	
	
	</div> <!-- close main content -->
	
	<?php include('includes/html/footer.html'); ?>

</div>	<!-- close wrapper -->
</body>
</html>