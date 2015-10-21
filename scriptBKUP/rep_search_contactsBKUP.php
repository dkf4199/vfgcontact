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
<title>EAPCRM Leads Management - It's Easy As Pie</title>
<link rel="stylesheet" type="text/css" media="screen" href="./css/eapcrm.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/eap_navmenu.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/eap_forms.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/eap_tables.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/modaldivs.css" />
<link href="./css/smoothness/jquery-ui-1.10.3.custom.css" rel="stylesheet" />
<!-- Load JavaScript files -->
<script type='text/javascript' src="./js/jquery.js"></script>
<script type='text/javascript' src="./js/jquery.simplemodal.js"></script>
<script src="./js/jquery-ui-1.10.3.custom.js"></script>
<script src="./js/tier_list.js"></script>
<script src="./js/dynamic_tier_list.js"></script>
<!-- contact_handlers.js contains the jquery that runs the "edit" "dump" "restore" "flush" modal divs -->
<script type='text/javascript' src='./js/contact_handlers_restore_flush.js'></script>
<script>
$(document).ready(function() {
	var message = null;
	var theID = null;
	
	//EDIT MODAL OPEN LINK
	$('#contact-form input.contact, #contact-form a.contact').click(function (e) {
		e.preventDefault();
		// load the contact info form using ajax
		
		// .get(url, data, success)....in that order
		var linkValue = this.href;
		var lastslash = linkValue.lastIndexOf('/');
		var cid = linkValue.slice(lastslash+1);

		//alert(cid);
		
		$.get("ajax_getcontact_info_modal.php", { currentcontactid: cid }, function(data){
			// create a modal dialog with the data
			$(data).modal({
				closeHTML: "<a href='#' title='Close' class='modal-close'>close X</a>",
				position: ["5%",], //top position
				overlayId: 'contact-overlay',
				containerId: 'contact-container',
				onOpen: open,
				onShow: show,
				onClose: close
			});
			// dkf 08-14-2013 have to register datepicker on content
			$("#datepicker").datepicker({ dateFormat: "mm-dd-yy", minDate: 0 });
		});
	});		//END EDIT MODAL LINK CLICK
	
	//********************************************************************************
	// DUMP CONTACT LINK
	//********************************************************************************
	$('#dumpcontact-form input.dumpcontact, #dumpcontact-form a.dumpcontact').click(function (e) {
		e.preventDefault();

		// .get(url, data, success)....in that order
		var linkValue = this.href;
		var lastslash = linkValue.lastIndexOf('/');
		var cid = linkValue.slice(lastslash+1);
		//alert(cid);
		
		$.get("ajax_getdumpcontact_info_modal.php", { currentcontactid: cid }, function(data){
			// create a modal dialog with the data
			$(data).modal({
				closeHTML: "<a href='#' title='Close' class='modal-close'>close X</a>",
				position: ["5%",], //top position
				overlayId: 'contact-overlay',
				containerId: 'contact-container',
				onShow: show,
				onClose: close
			});
			// dkf 08-14-2013 have to register datepicker on content
			//$("#datepicker").datepicker({ dateFormat: "mm-dd-yy", minDate: 0 });
		});
	});		//END DUMP LINK
	
	
});	//end ready

//Open function callback
function open(dialog) {
	
}

function show(dialog) {
	//***********************************************************
	//THE EMAIL ICON NEXT TO THE email address input field
	//
	// DISPLAY THE EMAIL MODAL
	//***********************************************************
	$('#contact-container a.contact-mailer').click(function (e) {
		e.preventDefault();
		
		//Get values off the edit modal and pass to emailer modal
		var curr_fname = $('#contact-container #first_name').val();
		var curr_lname = $('#contact-container #last_name').val();
		var curr_email = $('#contact-container #email').val();
		var curr_id = $('#contact-container #cid').val();
		//alert(curr_fname+' '+curr_lname+' '+curr_email);
		
		
		//get link href - it holds the contacts id
		var linkValue = this.href;
		var lastslash = linkValue.lastIndexOf('/');
		var cid = linkValue.slice(lastslash+1);
		//alert(curr_id);
		
		if (curr_email != ''){
			//close current edit modal
			$.modal.close();
			//window.setTimeout(contact.showSecondModal,500);
			$.get("ajax_emailer_modal.php",	{ currentid: curr_id, currentfirstname: curr_fname, currentlastname: curr_lname, currentemail: curr_email }, function(data){
				// create a modal dialog with the data
				$(data).modal({
					/*closeHTML: "<a href='#' title='Close' class='modal-close'>close X</a>",*/
					position: ["5%",], //top position
					overlayId: 'contact-overlay',
					containerId: 'emailer-container',
					minHeight: 600,
					minWidth: 800,
					maxHeight: 600,
					maxWidth: 800,
					onShow: show
				});
			});
		}
		//This is firing before modal is closed!
		//alert('Still in here?');
	});		//close $('#contact-container .contact-mailer').click(function (e)
	
	//********************************************************
	// SEND MAIL LINK on the emailer modal div
	//
	// CALL PHP TO SEND MAIL TO CONTACT, THEN OPEN EDIT MODAL
	//********************************************************
	$('#emailer-container a.sendlink').click(function (e) {
		e.preventDefault();
		
		var errors = '';
		// need the following off modal screen:
		// templateid, mailtofirstname, mailtolastname, mailtoemail, mailtofromemail
		var templatecat = $("input[name='template_cat']:checked").val()
		var templateid = $("#template_list :selected").val();
		var to_first = $("#mailto_firstname").val();
		var to_last = $("#mailto_lastname").val();
		var to_email = $("#mailto_email").val();
		var from_email = $("#mailto_from").val();
		//alert(templateid+"\n"+to_first+"\n"+to_last+"\n"+to_email+"\n"+from_email+"\n");
		
		
		//get link href - it holds the contacts id
		var linkValue = this.href;
		var lastslash = linkValue.lastIndexOf('/');
		var cid = linkValue.slice(lastslash+1);
		//alert(templatecat);
		
		if (templateid != '') {
			
			//STEP 1 - call ajax email
			$.ajax({
				url:"ajax_send_mail.php",
				type: "POST",
				data: { currentid: cid,
						templatecat: templatecat,
						templateid: templateid,
						to_first: to_first,
						to_last: to_last,
						to_email: to_email,
						from_email: from_email },
				dataType: "text",		
				success:function(result){
					//alert(result);
					//$("#email_response").html(result);
				}	//end success:function
			}); //end $.ajax
			//*********************************************
			
			//STEP 2 - close modal and re-open edit modal
			closeReopen(cid);
			
		}
		//otherwise, don't do anything.
		
	});		//close $('#emailer-container .sendlink').click(function (e)
	
	//********************************************************
	// CLOSE EMAILER MODAL, THEN OPEN EDIT MODAL
	//********************************************************
	$('#emailer-container a.closeemailer').click(function (e) {
		e.preventDefault();
		
		//get link href - it holds the contacts id
		var linkValue = this.href;
		var lastslash = linkValue.lastIndexOf('/');
		var cid = linkValue.slice(lastslash+1);
		//alert(templatecat);
		
		//Close Text Modal, Reopen EDIT CONTACT modal
		closeReopen(cid);
		
		
	});		//close $('#emailer-container .closeemailer').click(function (e)
	
	//***********************************************************
	// THE EMAIL ICON NEXT TO THE phone input field
	// DISPLAY THE MATRIX MAIL MODAL
	//***********************************************************
	$('#contact-container a.matrix-mail').click(function (e) {
		e.preventDefault();
		
		//Get values off the edit modal and pass to emailer modal
		var curr_fname = $('#contact-container #first_name').val();
		var curr_lname = $('#contact-container #last_name').val();
		var curr_email = $('#contact-container #email').val();
		var curr_id = $('#contact-container #cid').val();
		//alert(curr_fname+' '+curr_lname+' '+curr_email);
		
		
		//get link href - it holds the contacts id
		var linkValue = this.href;
		var lastslash = linkValue.lastIndexOf('/');
		var cid = linkValue.slice(lastslash+1);
		
		
		//close current edit modal
		$.modal.close();
		//window.setTimeout(contact.showSecondModal,500);
		$.get("ajax_matrix_mail_modal.php",	{ currentid: curr_id, currentfirstname: curr_fname, currentlastname: curr_lname, currentemail: curr_email }, function(data){
			// create a modal dialog with the data
			$(data).modal({
				/*closeHTML: "<a href='#' title='Close' class='modal-close'>close X</a>",*/
				position: ["5%",], //top position
				overlayId: 'contact-overlay',
				containerId: 'matrix-container',	//css for container is in modaldivs.css
				minHeight: 300,
				minWidth: 700,
				maxHeight: 300,
				maxWidth: 700,
				onShow: show
			});
		});
		
		
	});		//close $('#contact-container .matrix-mail').click(function (e)
	
	//********************************************************
	// SAVE MAIL NOTE LINK on the emailer modal div
	//
	// CALL PHP TO SAVE EMAIL NOTE TO DB, THEN OPEN EDIT MODAL
	//********************************************************
	$('#matrix-container a.matrixmaillink').click(function (e) {
		e.preventDefault();
		
		var errors = '';
		// need the following off modal screen:
		// templateid, mailtofirstname, mailtolastname, mailtoemail, mailtofromemail
		var mailnote = $("#mail_note").val()
		
		var to_first = $("#mailto_firstname").val();
		var to_last = $("#mailto_lastname").val();
		var to_email = $("#mailto_email").val();
		var from_email = $("#mailto_from").val();
		//alert(templateid+"\n"+to_first+"\n"+to_last+"\n"+to_email+"\n"+from_email+"\n");
		
		
		//get link href - it holds the contacts id
		var linkValue = this.href;
		var lastslash = linkValue.lastIndexOf('/');
		var cid = linkValue.slice(lastslash+1);
		//alert(templatecat);
		
		if (mailnote != '') {
			
			//STEP 1 - call ajax email
			$.ajax({
				url:"ajax_matrix_insert_mail_note.php",
				type: "POST",
				data: { mailnote: mailnote,	cid: cid },
				dataType: "html",		
				success:function(result){
					//alert(result);
					//$("#email_response").html(result);
				}	//end success:function
			}); //end $.ajax
			//*********************************************
			
			//STEP 2 - close modal and re-open edit modal
			closeReopen(cid);
			
		}	//end if(mailnote != '')
		
		
	});		//close $('#matrix-container .matrixmaillink').click(function (e)
	
	//***********************************************************
	// THE PHONE ICON NEXT TO THE phone input field
	// DISPLAY THE MATRIX PHONE MODAL
	//***********************************************************
	$('#contact-container a.matrix-phone').click(function (e) {
		e.preventDefault();
		
		//Get values off the edit modal and pass to emailer modal
		var curr_fname = $('#contact-container #first_name').val();
		var curr_lname = $('#contact-container #last_name').val();
		var curr_email = $('#contact-container #email').val();
		var curr_id = $('#contact-container #cid').val();
		//alert(curr_fname+' '+curr_lname+' '+curr_email);
		
		
		//get link href - it holds the contacts id
		var linkValue = this.href;
		var lastslash = linkValue.lastIndexOf('/');
		var cid = linkValue.slice(lastslash+1);
		
		
		//close current edit modal
		$.modal.close();
		//window.setTimeout(contact.showSecondModal,500);
		$.get("ajax_matrix_phone_modal.php",	{ currentid: curr_id, currentfirstname: curr_fname, currentlastname: curr_lname, currentemail: curr_email }, function(data){
			// create a modal dialog with the data
			$(data).modal({
				/*closeHTML: "<a href='#' title='Close' class='modal-close'>close X</a>",*/
				position: ["5%",], //top position
				overlayId: 'contact-overlay',
				containerId: 'matrix-container',	//css for container is in modaldivs.css
				minHeight: 300,
				minWidth: 700,
				maxHeight: 300,
				maxWidth: 700,
				onShow: show
			});
		});
		
		
	});		//close $('#contact-container .matrix-phone').click(function (e)
	//********************************************************
	// SAVE PHONE NOTE LINK on the emailer modal div
	//
	// CALL PHP TO SAVE PHONE NOTE TO DB, THEN OPEN EDIT MODAL
	//********************************************************
	$('#matrix-container a.matrixphonelink').click(function (e) {
		e.preventDefault();
		
		var errors = '';
		// need the following off modal screen:
		// templateid, mailtofirstname, mailtolastname, mailtoemail, mailtofromemail
		var phonenote = $("#phone_note").val()
		
		var to_first = $("#mailto_firstname").val();
		var to_last = $("#mailto_lastname").val();
		var to_email = $("#mailto_email").val();
		var from_email = $("#mailto_from").val();
		//alert(templateid+"\n"+to_first+"\n"+to_last+"\n"+to_email+"\n"+from_email+"\n");
		
		
		//get link href - it holds the contacts id
		var linkValue = this.href;
		var lastslash = linkValue.lastIndexOf('/');
		var cid = linkValue.slice(lastslash+1);
		//alert(templatecat);
		
		if (phonenote != '') {
			
			//STEP 1 - call ajax email
			$.ajax({
				url:"ajax_matrix_insert_phone_note.php",
				type: "POST",
				data: { phonenote: phonenote,	cid: cid },
				dataType: "html",		
				success:function(result){
					//alert(result);
					//$("#email_response").html(result);
				}	//end success:function
			}); //end $.ajax
			//*********************************************
			
			//STEP 2 - close modal and re-open edit modal
			closeReopen(cid);
						
		}	//end if(mailnote != '')
		
		
	});		//close $('#matrix-container .matrixphonelink').click(function (e)
	
	//***********************************************************
	// THE EMAIL ICON NEXT TO THE phone input field
	// DISPLAY THE MATRIX MAIL MODAL
	//***********************************************************
	$('#contact-container a.matrix-text').click(function (e) {
		e.preventDefault();
		
		//Get values off the edit modal and pass to emailer modal
		var curr_fname = $('#contact-container #first_name').val();
		var curr_lname = $('#contact-container #last_name').val();
		var curr_email = $('#contact-container #email').val();
		var curr_id = $('#contact-container #cid').val();
		//alert(curr_fname+' '+curr_lname+' '+curr_email);
		
		
		//get link href - it holds the contacts id
		var linkValue = this.href;
		var lastslash = linkValue.lastIndexOf('/');
		var cid = linkValue.slice(lastslash+1);
		
		
		//close current edit modal
		$.modal.close();
		//window.setTimeout(contact.showSecondModal,500);
		$.get("ajax_matrix_text_modal.php",	{ currentid: curr_id, currentfirstname: curr_fname, currentlastname: curr_lname, currentemail: curr_email }, function(data){
			// create a modal dialog with the data
			$(data).modal({
				/*closeHTML: "<a href='#' title='Close' class='modal-close'>close X</a>",*/
				position: ["5%",], //top position
				overlayId: 'contact-overlay',
				containerId: 'matrix-container',	//css for container is in modaldivs.css
				minHeight: 300,
				minWidth: 700,
				maxHeight: 300,
				maxWidth: 700,
				onShow: show
			});
		});
		
		
	});		//close $('#contact-container .matrix-text').click(function (e)
	//********************************************************
	// SAVE TEXT NOTE LINK on the emailer modal div
	//
	// CALL PHP TO SAVE TEXT NOTE TO DB, THEN OPEN EDIT MODAL
	//********************************************************
	$('#matrix-container a.matrixtextlink').click(function (e) {
		e.preventDefault();
		
		var errors = '';
		// need the following off modal screen:
		// templateid, mailtofirstname, mailtolastname, mailtoemail, mailtofromemail
		var textnote = $("#text_note").val()
		
		var to_first = $("#mailto_firstname").val();
		var to_last = $("#mailto_lastname").val();
		var to_email = $("#mailto_email").val();
		var from_email = $("#mailto_from").val();
		//alert(templateid+"\n"+to_first+"\n"+to_last+"\n"+to_email+"\n"+from_email+"\n");
		
		
		//get link href - it holds the contacts id
		var linkValue = this.href;
		var lastslash = linkValue.lastIndexOf('/');
		var cid = linkValue.slice(lastslash+1);
		//alert(templatecat);
		
		if (textnote != '') {
			
			//STEP 1 - call ajax email
			$.ajax({
				url:"ajax_matrix_insert_text_note.php",
				type: "POST",
				data: { textnote: textnote,	cid: cid },
				dataType: "html",		
				success:function(result){
					//alert(result);
					//$("#email_response").html(result);
				}	//end success:function
			}); //end $.ajax
			//*********************************************
			
			//STEP 2 - close modal and re-open edit modal
			closeReopen(cid);
			
			
		}	//end if(mailnote != '')
		
		
	});		//close $('#matrix-container a.matrixtextlink').click(function (e)
	
	//********************************************************
	// CLOSE LINK ON ALL NOTE MODALS, THEN OPEN EDIT MODAL
	//********************************************************
	$('#matrix-container a.matrixcloselink').click(function (e) {
		e.preventDefault();
		
		//get link href - it holds the contacts id
		var linkValue = this.href;
		var lastslash = linkValue.lastIndexOf('/');
		var cid = linkValue.slice(lastslash+1);
		//alert(templatecat);
		
		//Close Text Modal, Reopen EDIT CONTACT modal
		closeReopen(cid);
		
		
	});		//close $('#matrix-container .matrixcloselink').click(function (e)
	
	
	
}	// CLOSE show

function close(dialog) {
	
	$.modal.close();
}

function closeReopen(cid){
	
	$.modal.close();
	//window.setTimeout(contact.showSecondModal,500);
	$.get("ajax_getcontact_info_modal.php", { currentcontactid: cid }, function(data){
		// create a modal dialog with the data
		$(data).modal({
			closeHTML: "<a href='#' title='Close' class='modal-close'>close X</a>",
			position: ["5%",], //top position
			overlayId: 'contact-overlay',
			containerId: 'contact-container',
			minHeight: 550,
			minWidth: 950,
			maxHeight: 550,
			maxWidth: 950,
			onShow: show,
			onClose: close
		});
		// dkf 08-14-2013 have to register datepicker on content
		$("#datepicker").datepicker({ dateFormat: "mm-dd-yy", minDate: 0 });
	});
}

function error(xhr) {
	alert(xhr.statusText);
}

function updateContact(){

	var dataChanged = false;
		
	var firstName = $("#first_name").val();
	var ogFirstName = $("#original_firstname").val();
	var lastName = $("#last_name").val();
	var ogLastName = $("#original_lastname").val();
	var email = $("#email").val();
	var ogEmail = $("#original_email").val();
	var phone = $("#phone").val();
	var ogPhone = $("#original_phone").val();
	var city = $("#city").val();
	var ogCity = $("#original_city").val();
	var state = $("#state :selected").val();
	var ogState = $("#original_state").val();
	var timeZone = $("#timezone :selected").val();
	var ogTimeZone = $("#original_timezone").val();
	var tier = $("input:radio[name=tier]:checked").val();
	var ogTier = $("#original_tier").val();
	var tierStep = $("#tierstep :selected").val();
	var ogTierStep = $("#original_tierstep").val();
	var teamMember = $("#team_member :selected").val();
	var ogTeamMember = $("#original_teammember").val();
	var contactType = $("#contact_type :selected").val();
	var ogContactType = $("#original_contacttype").val();
	var rorc = $("#rorc :selected").val();
	var ogRorc = $("#original_rorc").val();
	var notes = $("#notes").val();
	var ogNotes = $("#original_notes").val();
	var nextActionDate = $("#datepicker").val();
	var ogNextActionDate = $("#original_nextactiondate").val();
	//Serialize the form
	//compare original vals from php program 
	//to form vars on update click
	//NOTE: CONVERT to lowercase - JS is case sensitive!
	if (firstName.toLowerCase() != ogFirstName.toLowerCase()) {dataChanged = true;}
	if (lastName.toLowerCase() != ogLastName.toLowerCase()) {dataChanged = true;}
	if (email != ogEmail) {dataChanged = true;}
	if (phone != ogPhone) {dataChanged = true;}
	if (city != ogCity) {dataChanged = true;}
	if (state != ogState) {dataChanged = true;}
	if (timeZone != ogTimeZone) {dataChanged = true;}
	if (tier != ogTier) {dataChanged = true;}
	if (tierStep != ogTierStep) {dataChanged = true;}
	if (teamMember != ogTeamMember) {dataChanged = true;}
	if (contactType != ogContactType) {dataChanged = true;}
	if (rorc != ogRorc) {dataChanged = true;}
	if (notes != ogNotes) {dataChanged = true;}
	if (nextActionDate != ogNextActionDate) {dataChanged = true;}
	
	if (dataChanged) {
		//set hidden form var data_changed to true
		$("#data_changed").val('true');
	}
		
	var formdata = $('#editcontact_form').serialize();
	
	//alert(tier+' '+tierStep);
	//call ajax_updatelead.php
	//the ajax_verify_update div is contained in the output
	//from the call to ajaxgetlead_info.php
	//
	$.ajax({
		url:"ajax_updatecontact.php",
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
	//SET og hiddens to the extracted js variables from form fields
	//after ajax runs
	$("#original_firstname").val(firstName);
	$("#original_lastname").val(lastName);
	$("#original_email").val(email);
	$("#original_phone").val(phone);
	$("#original_city").val(city);
	$("#original_state").val(state);
	$("#original_timezone").val(timeZone);
	$("#original_tier").val(tier);
	$("#original_tierstep").val(tierStep);
	$("#original_teammember").val(teamMember);
	$("#original_contacttype").val(contactType);
	$("#original_rorc").val(rorc);
	$("#original_notes").val(notes);
	$("#original_nextactiondate").val(nextActionDate);
	
	
	//reset the data_changed hidden flag
	$("#data_changed").val('false');
	
	
	
	//alert("Update Form Button clicked.\n"+"First Name: "+firstName+"\n"+"Last Name: "+lastName+"\n");
	//alert("Update Form Button clicked.\n"+"Data Changed Flag: "+dataChanged);
	return false;
}

//function dumpContact
function dumpContact() {

	var formdata = $('#dumpcontact_form').serialize();
	//move the contact to the dump table
	//and out of the contacts table
	$.ajax({
			url:"ajax_dumpcontact.php",
			type: "POST",
			data: formdata,
			dataType: "html",		
			success:function(result){
				$("#ajax_verify_dump").html(result);
			}	//end success:function
	}); //end $.ajax
	return false;
}
//****************************************************
// FLUSH FUNCTIONS
//****************************************************
function flushContact() {
	var formdata = $('#flushdumpcontact_form').serialize();
	//move the contact to the dump table
	//and out of the contacts table
	$.ajax({
			url:"ajax_flushcontact.php",
			type: "POST",
			data: formdata,
			dataType: "html",		
			success:function(result){
				$("#ajax_verify_flush").html(result);
			}	//end success:function
	}); //end $.ajax
	return false;
}
function restoreContact() {
	var formdata = $('#viewdumpcontact_form').serialize();
	//move the contact to the dump table
	//and out of the contacts table
	$.ajax({
			url:"ajax_restoredumpcontact.php",
			type: "POST",
			data: formdata,
			dataType: "html",		
			success:function(result){
				$("#ajax_verify_restore").html(result);
			}	//end success:function
	}); //end $.ajax
	return false;
}
//Templates List - on emailer modal div
function setTemplateList(val){
	var globaltemplates = ["1Global Template 1",
			"2Global Template 2",
			"3Global Template 3",
			"4Global Template 4"];
	
	var customoptionstring = "<option value=\"\">Select</option>\n";
	//Build custom template list from hidden options_string
	//options_string is in this format: t_id:template_name,
	//
	
	//strip off the final , on the end of this thing
	var customtemplates = $("#options_string").val().slice(0,-1);
	
	var n=customtemplates.split(",");
	for (var i = 0; i < n.length; i++) {
		//thisstring += n[i]+"\n";
		var colonloc = n[i].lastIndexOf(':');
		customoptionstring += "<option value=\"" + n[i].slice(0,colonloc-1) + "\">" + n[i].slice(colonloc+1) + "</option>" + "\n";
	}
	//alert(customoptionstring);
	
	var optionstring = ""
	switch(val){
		case 'custom':
		  optionstring = customoptionstring;
		  break;
		case 'global':
		  optionstring = "<option value=\"\">Select</option>\n";
		  for (var i in globaltemplates){
			optionstring += "<option value=\"" + globaltemplates[i].substr(0,1) + "\">" + globaltemplates[i].substr(1) + "</option>" + "\n"; 
		  }
		  break;
		default:
		  optionstring = "<option value=\"no list\">No List</option>" + "\n";
	}
	$("select[name='template_list']").find('option').remove().end().append($(optionstring));
}
</script>
</head>

<body>
<div class="wrapper showgoldborder">
	<!-- Header include -->
	<?php include('includes/html/header_log.html'); ?>
	
	<div class="maincontent">
		<div class="welcomespan">Welcome <?php echo $_SESSION['rep_firstname'].' '.$_SESSION['rep_lastname'].'.'; ?></div>
		
		<div id="ajax_viewcontacts">
		<?php
			$repsid = $_SESSION['rep_id'];
			if (isset($_GET['srch']) && $_GET['srch'] != ''){
				$searchstr = $_GET['srch'];
			} else {
				$searchstr = '';
			}	
			
			include ('includes/config.inc.php');
			include ('includes/phpfunctions.php');
			//DB Connection
			require_once (MYSQL);
			//*********************************************************
			//Pagination
			//*********************************************************

			//UNION query
			$contactsquery = "(SELECT 'contacts' AS from_table,
							  contact_id,
							  firstname,
							  lastname,
							  email,
							  phone,
							  tier_status,
							  entry_date 
						FROM contacts 
						WHERE lastname LIKE '$searchstr%'
						AND rep_id = '$repsid'
						ORDER BY entry_date desc)
					UNION
					  (SELECT 'dumpedcontacts' AS from_table,
							   contact_id,
							   firstname,
							   lastname,
							   email,
							   phone,
							   tier_status,
							   entry_date 
						 FROM dumped_contacts
					     WHERE lastname LIKE '$searchstr%'
						 AND rep_id = '$repsid'
						 ORDER BY entry_date desc)";
			//**********************************************
			$r = @mysqli_query ($dbc, $contactsquery); // Run the query.

			if ($r) { // If it ran OK, display the records.
				
				echo '<table id="csstable">
					<tr>
						<th colspan=7>SEARCH RESULTS</th>
					</tr>
					<tr>
						<th scope="col">Actions</th>
						<th scope="col">Lastname</th>
						<th scope="col">Firstname</th>
						<th scope="col">Email</th>
						<th scope="col">Phone</th>
						<th scope="col">Entry Date</th>
						<th scope="col">Tier</th>
					</tr>';
			
				//alternate row class css spec: spec, specalt
				$rowspec = 'specalt';	
				// Fetch and print all the records:
				while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
					
					$rawdate = strtotime( $row['entry_date'] );
					$formatted_entrydt = date( 'm-d-Y h:i a', $rawdate );
					
					//switch the rowspec value
					//$rowspec = ($rowspec=='#ffffff' ? '#f5fafa' : '#ffffff');
					$rowspec = ($rowspec=='spec' ? 'specalt' : 'spec');
										
					//create each row
					
					echo '<tr class="'.$rowspec.'">';
					if ($row['from_table'] == 'contacts'){
						echo '<td><div id="contact-form"><a href="'.$row['contact_id'].'" class="contact">Edit</a></div>
								&nbsp;<div id="dumpcontact-form"><a href="'.$row['contact_id'].'" class="dumpcontact">Dump</a></div></td>';
					} elseif ($row['from_table'] == 'dumpedcontacts'){
						echo '<td><div id="restorecontact-form"><a href="'.$row['contact_id'].'" class="restorecontact">Restore</a></div>
								&nbsp;<div id="flushcontact-form"><a href="'.$row['contact_id'].'" class="flushcontact">Flush</a></div></td>';
					}
							
					echo '<td>'.$row['lastname'].'</td>
						  <td>'.$row['firstname'].'</td>
						  <td>'.$row['email'].'</td>
						  <td>'.$row['phone'].'</td>
						  <td>'.$formatted_entrydt.'</td>
						  <td>'.substr($row['tier_status'],0,1).'</td>	
						  </tr>';
					
				}
				
				echo '</table>';
				
				mysqli_free_result ($r); // Free up the resources.	

			} else { // If it did not run OK.

				// Public message:
				echo '<p>Your contacts could not be retrieved. We will fix the problem shortly.</p>';
				echo '</div>';
				
				// Debugging message:
				echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $contactquery . '</p>';
				
			} // End of if ($r)

			mysqli_close($dbc); // Close the database connection.

		?>
		</div> <!-- close ajax_viewcontacts -->
		
		<?php include('includes/html/footer.html'); ?>
				
	</div> <!-- close main content -->
	
	

</div>	<!-- close wrapper -->
</body>
</html>