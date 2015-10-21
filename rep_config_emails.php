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
<title>VFG Contact - Edit Personal Email Templates</title>
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact_forms.css" />
<link rel="stylesheet" href="./css/start/jquery-ui-1.10.3.custom.css">
<script src="./js/jquery-1.10.2.js"></script>
<script src="./js/jquery-ui-1.10.3.custom.js"></script>
<script>
$(document).ready(function() {
	
	$( "#menu" ).menu();
	
	//Company dropdown list
	$('#get_settings_button').click(function() {
		//alert("GC_BUTTON clicked");
		$("#selecterror").text("");
		var errors = '';
		
		var template = $("#template_id :selected").val();
		var repid = $("#rep_id").val();
		
		if (template == '') {errors = 'Select a template.';}
				
		if (errors != ''){
			$("#selecterror").text(errors);
		} else {
			//alert('Tier: '+current_toptier+"\n"+'Tier Step: '+current_toptierstep);
			$.ajax({
				url:"ajax_get_template_settings.php",
				type: "GET",
				data: {thistemplate: template, 
					   thisrepid: repid},
				dataType: "html",		
				success:function(result){
					$("#ajax_emailtemplate_settings").html(result);
					
			}}); //end success:function
		}
	});
			
});	//end ready
</script>
<script>
// CALLED FUNCTIONS FOR LOADED AJAX DIVS
// CALL outside the document.ready because they aren't on the page
// initially when the document is first loaded and ready.
//
// These return false to prevent the default actions from firing
//*****************************************************************
function previewTemplate(tid) {
			
	alert('Preview Template link for template '+tid+' clicked');
	return false;
}

function updateTemplateSettings(){
	
	var dataChanged = false;
	
	var name = $("#t_name").val();
	var ogName = $("#original_name").val();
	var subject = $("#t_subject").val();
	var ogSubject = $("#original_subject").val();
	var salutation = $("#t_salutation :selected").val();
	var ogSalutation = $("#original_salutation").val();
	var body = $("#t_body").val();
	var ogBody = $("#original_body").val();
	var imglink = $("#t_imagelink").val();
	var ogImglink = $("#original_imglink").val();
	var closing = $("#t_closing :selected").val();
	var ogClosing = $("#original_closing").val();
	
	//alert(body+"\n"+ogBody);
	//compare original vals from php program 
	//to form vars on update click
	//NOTE: CONVERT to lowercase - JS is case sensitive!
	if (name.toLowerCase() != ogName.toLowerCase()) {dataChanged = true;}
	if (subject.toLowerCase() != ogSubject.toLowerCase()) {dataChanged = true;}
	if (salutation != ogSalutation) {dataChanged = true;}
	if (body != ogBody) {dataChanged = true;}
	if (imglink != ogImglink) {dataChanged = true;}
	if (closing != ogClosing) {dataChanged = true;}
	
	//alert(dataChanged.toString());
	
	
	if (dataChanged) {
		//alert('Data has changed.');
		//set hidden form var data_changed to true
		$("#data_changed").val('true');
	}
	
	
	
	//Serialize form for submission
	var formdata = $('#email_config_form').serialize();
	
	//alert(tier+' '+tierStep);
	//call ajax_updatelead.php
	//the ajax_verify_update div is contained in the output
	//from the call to ajaxgetlead_info.php
	//
	$.ajax({
		url:"ajax_update_emailsettings.php",
		type: "POST",
		data: formdata,
		dataType: "html",		
		success:function(result){
			$("#ajax_emailsettings_update").html(result);
		},	//end success:
		error: function (request, status, error) {
			alert(request.responseText);
		}//end error:
	}); //end $.ajax
	
	//Have to reset the original values equal to what
	//was just submitted to start fresh for any
	//subsequent update!
	//
	//SET og hiddens to the extracted js variables from form fields
	//after ajax runs
	$("#original_name").val(name);
	$("#original_subject").val(subject);
	$("#original_salutation").val(salutation);
	$("#original_body").val(body);
	$("#original_imglink").val(imglink);
	$("#original_closing").val(closing);
		
	//reset the data_changed hidden flag
	$("#data_changed").val('false');
	
	var optionslist = '';
	//Run AJAX call to update the optionstring in the select list
	//on this page.
	$.ajax({
		url:"ajax_custom_template_options.php",
		type: "GET",
		dataType: "html",		
		success:function(result){
			//alert(result);
			$("select[name='template_id']").find('option').remove().end().append($(result));
			//$("#optionlist").html(result);
		},	//end success:
		error: function (request, status, error) {
			alert(request.responseText);
		}//end error:
	}); //end $.ajax
	
	$("#optionlist").html(optionslist);
	// Return false....this is in an onSubmit action for the form.
	// Prevents form submission to nowhere.
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
				
			<?php
				$repsid = $_SESSION['rep_id'];
				//DB Connection
				require_once (MYSQL);
				
				$options = "<option value=\"\">Select</option>\n";
				//Get distinct templates from vfg_globalemail_settings table
				$q = "SELECT t_id, template_name
					  FROM vfg_customemail_settings
					  WHERE rep_id = '$repsid'";
				$r = mysqli_query ($dbc, $q); // Run the query.
				if (mysqli_num_rows($r) > 0){
					//Build the options string with the template ids
					while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
						$options .= "<option value=\"".$row['t_id']."\">".$row['template_name']."</option>\n";
					}
					mysqli_free_result($r);
				}
			?>
			<span class="template_select">
				<label>Email Template:</label>
				<select name="template_id" id="template_id">
					<div id="optionlist"><?php echo $options; ?></div>
				</select>
				<input type="button" id="get_settings_button" class="generalbutton" value="View Settings" />
				<input type="hidden" id="rep_id" value="<?php echo $_SESSION['rep_id']; ?>" />
				<span id="selecterror" class="errorspan"></span>
			</span>
			<div id="ajax_emailtemplate_settings"></div>
		
		
		
	</div> <!-- close main content -->
</div>	<!-- close wrapper -->
<?php include('includes/html/footer.html'); ?>
</body>
</html>