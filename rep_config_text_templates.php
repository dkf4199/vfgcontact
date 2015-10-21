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
<title>VFG Contact - Edit Personal Text Templates</title>
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
				url:"ajax_get_text_template_settings.php",
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
			
	//alert('Preview Template link for template '+tid+' clicked');
	return false;
}

function updateTextTemplateSettings(){
	
	var dataChanged = false;
	
	var name = $("#tt_name").val();
	var ogName = $("#original_name").val();
	var body = $("#tt_body").val();
	var ogBody = $("#original_body").val();
	//compare original vals from php program 
	//to form vars on update click
	//NOTE: CONVERT to lowercase - JS is case sensitive!
	if ( name.toLowerCase() != ogName.toLowerCase() ) {dataChanged = true;}
	if ( body.toLowerCase() != ogBody.toLowerCase() ) {dataChanged = true;}
			
	if (dataChanged) {
		//set hidden form var data_changed to true
		$("#data_changed").val('true');
	}
	
	//Serialize form for submission
	var formdata = $('#text_config_form').serialize();
	
	//AJAX CALL
	$.ajax({
		url:"ajax_update_textsettings.php",
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
	$("#original_body").val(body);
		
	//reset the data_changed hidden flag
	$("#data_changed").val('false');
	
	var optionslist = '';
	//Run AJAX call to update the optionstring in the select list
	//on this page.
	$.ajax({
		url:"ajax_refresh_text_template_list.php",
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
				//Get distinct templates from vfg_rep_text_templates
				$q = "SELECT tt_id, text_template_name
					  FROM vfg_rep_text_templates
					  WHERE rep_id = '$repsid'";
				$r = mysqli_query ($dbc, $q); // Run the query.
				if (mysqli_num_rows($r) > 0){
					//Build the options string with the template ids
					while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
						$options .= "<option value=\"".$row['tt_id']."\">".$row['text_template_name']."</option>\n";
					}
					mysqli_free_result($r);
				}
			?>
			<span class="template_select">
				<label>Text Template:</label>
				<select name="template_id" id="template_id">
					<div id="optionlist"><?php echo $options; ?></div>
				</select>
				<input type="button" id="get_settings_button" class="generalbutton" value="View Text" />
				<input type="hidden" id="rep_id" value="<?php echo $_SESSION['rep_id']; ?>" />
				<span id="selecterror" class="errorspan"></span>
			</span>
			<div id="ajax_emailtemplate_settings"></div>
		
		
		
	</div> <!-- close main content -->
</div>	<!-- close wrapper -->
<?php include('includes/html/footer.html'); ?>
</body>
</html>