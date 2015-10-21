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
<title>VFG Contact - Google Calendar Events</title>
<!-- CSS -->
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact_forms.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact_tables.css" />
<link rel="stylesheet" href="./css/start/jquery-ui-1.10.3.custom.css">
<link rel="stylesheet" type="text/css" media="screen" href="./css/google_calendar.css" />
<!-- JS -->
<script src="./js/jquery-1.10.2.js"></script>
<script src="./js/jquery-ui-1.10.3.custom.js"></script>
<script>
$(document).ready(function() {

	$("#datepickerone").datepicker({ dateFormat: "yy-mm-dd" });
	$("#datepickertwo").datepicker({ dateFormat: "yy-mm-dd" });
	
	//Company dropdown list
	$('#gc_view_button').click(function() {
		//alert("GC_BUTTON clicked");
		var errors = '';
		
		var fromdt = $("#datepickerone").val();
		var todt = $("#datepickertwo").val();
		if (fromdt == '') {errors = 'Provide From Date.';}
		//if (todt == '') {errors += ' Provide To Date.';}
		/*var encoded_fromdt = encodeURIComponent(fromdt);
		var encoded_todt = encodeURIComponent(todt);
		alert(encoded_fromdt+' '+encoded_todt);*/
		
		if (errors != ''){
			$("#gc_fielderrors").text(errors);
		} else {
			//alert('Tier: '+current_toptier+"\n"+'Tier Step: '+current_toptierstep);
			$.ajax({
				url:"ajax_get_gc_events.php",
				type: "GET",
				data: {fromdt: fromdt, 
					   todt: todt},
				dataType: "html",		
				success:function(result){
					$("#gcEventList").html(result);
					$("#gcEditEvent").html("");
			}}); //end success:function
		}
	});
		
});	//end ready
</script>
<script>
function displayEventForEdit(id) { 
	//alert("clicked link" + id); 
	$.ajax({
		url:"ajax_display_event_for_edit.php",
		type: "GET",
		data: {id: id},
		dataType: "html",		
		success:function(result){
			$("#gcEditEvent").html(result);
			//$("#ajax_updatecontact").html("");
			//need to initialize datepicker on loaded ajax content!
			$("#datepicker_edit_startdate").datepicker({ dateFormat: "mm-dd-yy" });
			$("#datepicker_edit_enddate").datepicker({ dateFormat: "mm-dd-yy" });
	}}); //end success:function
	
}
function displayEventForDelete(id) { 
	//alert("clicked link" + id); 
	$.ajax({
		url:"ajax_display_event_for_delete.php",
		type: "GET",
		data: {id: id},
		dataType: "html",		
		success:function(result){
			$("#gcEditEvent").html(result);
			//$("#ajax_updatecontact").html("");
	}}); //end success:function
	
}
function updateGCEvent(id){ 
	//alert("clicked link" + id);
	
	//serialize the form for post
	var formdata = $('#gc_editevent_form').serialize();
	$.ajax({
		url:"ajax_gc_update_event.php",
		type: "POST",
		data: formdata,
		dataType: "html",		
		success:function(result){
			$("#ajax_gc_editevent_update").html(result);
	}}); //end success:function
	return false;
}
function deleteGCEvent(id){ 
	//alert("clicked link" + id);
	
	//serialize the form for post
	var formdata = $('#gc_deleteevent_form').serialize();
	$.ajax({
		url:"ajax_gc_delete_event.php",
		type: "POST",
		data: formdata,
		dataType: "html",		
		success:function(result){
			$("#ajax_gc_editevent_update").html(result);
	}}); //end success:function
	return false;
}
</script>
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
		
		<div id="gcActions">
			<a class="googlelink" href="rep_calendar.php">Back To Calendar</a>
		</div>
		<div class="googleinputdiv">
			<span>NOTE: To view your events for one day - leave the "to date" field blank.</span><br />
			<label>From Date:</label>
			<input type="text" id="datepickerone" />
			<label>To Date:</label>
			<input type="text" id="datepickertwo" />
			<input type="button" id="gc_view_button" class="googlebutton" value="View Events" />
			<span id="gc_fielderrors" class="gc_errorspan"></span>
		</div>
				
		<!-- div for update form -->
		<div id="gcEventList" class="opacity70"></div>
		
		<div id="gcEditEvent" class="opacity70"></div>		
		<div class="cleardiv"></div>
		
	</div> <!-- close main content -->
</div>	<!-- close wrapper -->
<?php include('includes/html/footer.html'); ?>
</body>
</html>