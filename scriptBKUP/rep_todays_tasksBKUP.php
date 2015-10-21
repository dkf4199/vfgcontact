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
<title>EAPCRM Leads Management - Today's Tasks</title>
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
<!--<script type='text/javascript' src='./js/contact_handlers.js'></script>-->
<script>
$(document).ready(function() {
	
	$("#datepickerstatic").datepicker({ dateFormat: "mm-dd-yy" });
	var message = null;
	var theID = null;
	
	//EDIT MODAL OPEN LINK
	$('#contact-form input.contact, #contact-form a.contact').click(function (e) {
		e.preventDefault();
		// load the contact info form using ajax
		// 08/12/13 change contact.php to modal_add_contact.php
		//
		/* $.get( url [, data ] [, success(data, textStatus, jqXHR) ] [, dataType ] )
			IS SHORTHAND FOR:
			$.ajax({
				  url: url,
				  data: data,
				  success: success,
				  dataType: dataType
				});
		*/
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
	
	$('#dumpcontact-form input.dumpcontact, #dumpcontact-form a.dumpcontact').click(function (e) {
		e.preventDefault();

		// load the contact info form using ajax
		// 08/12/13 change contact.php to modal_add_contact.php
		//
		/* $.get( url [, data ] [, success(data, textStatus, jqXHR) ] [, dataType ] )
			IS SHORTHAND FOR:
			$.ajax({
				  url: url,
				  data: data,
				  success: success,
				  dataType: dataType
				});
		*/
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
				onOpen: open,
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
	// dynamically determine height
	var h = 550;
	if ($('#contact-subject').length) {
		h += 26;
	}
	if ($('#contact-cc').length) {
		h += 22;
	}

	var title = $('#contact-container .contact-title').html();
	$('#contact-container .contact-title').html('Loading...');
	dialog.overlay.fadeIn(200, function () {
		dialog.container.fadeIn(200, function () {
			dialog.data.fadeIn(200, function () {
				$('#contact-container .contact-content').animate({
					height: h
				}, function () {
					$('#contact-container .contact-title').html(title);
					$('#contact-container form').fadeIn(200, function () {
						$('#contact-container #contact-name').focus();

						$('#contact-container .contact-cc').click(function () {
							var cc = $('#contact-container #contact-cc');
							cc.is(':checked') ? cc.attr('checked', '') : cc.attr('checked', 'checked');
						});
					});
				});
			});
		});
	});
}

function show(dialog) {
	$('#contact-container .contact-send').click(function (e) {
		e.preventDefault();
		// validate form on click of button
		if (validate()) {
			var msg = $('#contact-container .contact-message');
			msg.fadeOut(function () {
				msg.removeClass('contact-error').empty();
			});
			$('#contact-container .contact-title').html('Sending...');
			$('#contact-container form').fadeOut(200);
			$('#contact-container .contact-content').animate({
				height: '80px'
			}, function () {
				$('#contact-container .contact-loading').fadeIn(200, function () {
					$.ajax({
						url: 'contact.php',
						data: $('#contact-container form').serialize() + '&action=send',
						type: 'post',
						cache: false,
						dataType: 'html',
						success: function (data) {
							$('#contact-container .contact-loading').fadeOut(200, function () {
								$('#contact-container .contact-title').html('Thank you!');
								msg.html(data).fadeIn(200);
							});
						},
						error: error
					});
				});
			});		
		}
		else {	//contact.validate === false
			if ($('#contact-container .contact-message:visible').length > 0) {
				var msg = $('#contact-container .contact-message div');
				msg.fadeOut(200, function () {
					msg.empty();
					showError();
					msg.fadeIn(200);
				});
			}
			else {
				$('#contact-container .contact-message').animate({
					height: '30px'
				}, showError());
			}
			
		}
	});		//close $('#contact-container .contact-send').click(function (e)

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
		
		if (curr_email != ''){
			//close current edit modal
			$.modal.close();
			//window.setTimeout(contact.showSecondModal,500);
			$.get("ajax_emailer_modal.php",	{ currentid: curr_id, currentfirstname: curr_fname, currentlastname: curr_lname, currentemail: curr_email }, function(data){
				// create a modal dialog with the data
				$(data).modal({
					closeHTML: "<a href='#' title='Close' class='modal-close'>close X</a>",
					position: ["5%",], //top position
					overlayId: 'contact-overlay',
					containerId: 'emailer-container',
					minHeight: 500,
					minWidth: 600,
					maxHeight: 500,
					maxWidth: 600,
					/*onOpen: open,*/
					onShow: show,
					onClose: close
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
				data: { templatecat: templatecat,
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
			//close current emailer modal
			$.modal.close();
			//window.setTimeout(contact.showSecondModal,500);
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
			
		}
		//otherwise, don't do anything.
		
	});		//close $('#contact-container .mailsent').click(function (e)
	
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
				/*onOpen: open,*/
				onShow: show,
				/*onClose: close*/
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
			//close current emailer modal
			$.modal.close();
			//window.setTimeout(contact.showSecondModal,500);
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
				/*onOpen: open,*/
				onShow: show,
				/*onClose: close*/
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
			//close current emailer modal
			$.modal.close();
			//window.setTimeout(contact.showSecondModal,500);
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
				/*onOpen: open*/
				onShow: show,
				/*onClose: close*/
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
			//close current emailer modal
			$.modal.close();
			//window.setTimeout(contact.showSecondModal,500);
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
		
				
		//STEP 2 - close modal and re-open edit modal
		//close current emailer modal
		$.modal.close();
		//window.setTimeout(contact.showSecondModal,500);
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
			onOpen: open,
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
//****************************************
// VALIDATE THE FORM FIELDS
//****************************************
function validate() {
	contact.message = '';
	if (!$('#contact-container #contact-name').val()) {
		contact.message += 'Name is required. ';
	}

	var email = $('#contact-container #contact-email').val();
	if (!email) {
		contact.message += 'Email is required. ';
	}
	else {
		if (!contact.validateEmail(email)) {
			contact.message += 'Email is invalid. ';
		}
	}

	if (!$('#contact-container #contact-message').val()) {
		contact.message += 'Message is required.';
	}

	if (contact.message.length > 0) {
		return false;
	}
	else {
		return true;
	}
}
function validateEmail (email) {
	var at = email.lastIndexOf("@");

	// Make sure the at (@) sybmol exists and  
	// it is not the first or last character
	if (at < 1 || (at + 1) === email.length)
		return false;

	// Make sure there aren't multiple periods together
	if (/(\.{2,})/.test(email))
		return false;

	// Break up the local and domain portions
	var local = email.substring(0, at);
	var domain = email.substring(at + 1);

	// Check lengths
	if (local.length < 1 || local.length > 64 || domain.length < 4 || domain.length > 255)
		return false;

	// Make sure local and domain don't start with or end with a period
	if (/(^\.|\.$)/.test(local) || /(^\.|\.$)/.test(domain))
		return false;

	// Check for quoted-string addresses
	// Since almost anything is allowed in a quoted-string address,
	// we're just going to let them go through
	if (!/^"(.+)"$/.test(local)) {
		// It's a dot-string address...check for valid characters
		if (!/^[-a-zA-Z0-9!#$%*\/?|^{}`~&'+=_\.]*$/.test(local))
			return false;
	}

	// Make sure domain contains only valid characters and at least one period
	if (!/^[-a-zA-Z0-9\.]*$/.test(domain) || domain.indexOf(".") === -1)
		return false;	

	return true;
}
function showError() {
	$('#contact-container .contact-message')
		.html($('<div class="contact-error"></div>').append(contact.message))
		.fadeIn(200);
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
</script>
</head>

<body>
<div class="wrapper showgoldborder">
	<!-- Header include -->
	<?php include('includes/html/header_log.html'); ?>
	
	<div class="maincontent">
		<p class="welcomespan">Welcome <?php echo $_SESSION['rep_firstname'].' '.$_SESSION['rep_lastname'].'.'; ?></p>
		
		<form action="rep_todays_tasks.php" method="GET">
			<label>Select Tasks for:</label>
			<input type="text" name="nextactiondate" id="datepickerstatic" 
					value="<?php if (isset($_GET['nextactiondate'])) echo $_GET['nextactiondate']; ?>" />
			<input type="submit" id="taskbutton" value="Get Tasks" />
		</form>
		
		<div id="ajax_viewcontacts">
			<?php
				$repsid = $_SESSION['rep_id'];
				
				//mysql date is yyyy-mm-dd
				$mysqldate = $displaydate = '';
				
				// nextactiondate is from the datepicker, in mm-dd-yyyy form
				// MySQL uses yyyy-mm-dd for date
				// we are displaying this thing as mm-dd-yyyy
				if (isset($_GET['nextactiondate']) && $_GET['nextactiondate'] != ''){
					$actiondate = $_GET['nextactiondate'];
					list($ad_mm,$ad_dd,$ad_yy) = explode('-',$actiondate);
					$displaydate = $ad_mm.'-'.$ad_dd.'-'.$ad_yy;
					$mysqldate = $ad_yy.'-'.$ad_mm.'-'.$ad_dd;
				} else {
					$mysqldate = date('Y-m-d');
				}
				
				if (isset($_GET['actiondate'])){
					$mysqldate = $_GET['actiondate'];
					list($ad_yy,$ad_mm,$ad_dd) = explode('-',$mysqldate);
					$displaydate = $ad_mm.'-'.$ad_dd.'-'.$ad_yy;
				}
				
				include ('includes/config.inc.php');
				include ('includes/phpfunctions.php');
				//DB Connection
				require_once (MYSQL);
				//*********************************************************
				//Pagination
				//*********************************************************

				//Max Display
				$display = 10;

				if (isset($_GET['p']) && is_numeric($_GET['p'])) {
					//already determined
					$pages = $_GET['p'];

				} else {
					//need to determine based on count in db
					//
					 $q = "SELECT count(rep_id) FROM contacts
						   WHERE rep_id='$repsid'
						   AND next_action_date='$mysqldate'";
					 $r = @mysqli_query($dbc, $q);

					 $row = @mysqli_fetch_array($r, MYSQLI_NUM);
					 $records = $row[0];
					 
					 if ($records > $display) {
						 //more than one page
						 $pages = ceil($records/$display);
					 } else {
						 $pages = 1;
					 }
				} //end if get 'p'

				//determine where in database to return results from
				if (isset($_GET['s']) && is_numeric($_GET['s'])){
					$start = $_GET['s'];
				} else {
					$start = 0;
				}
				//Get total recs for refresh purposes
				$q = "SELECT count(rep_id) FROM contacts
					  WHERE rep_id='$repsid'
					  AND next_action_date='$mysqldate'";
				$r = @mysqli_query($dbc, $q);
				$row = @mysqli_fetch_array($r, MYSQLI_NUM);
				$_SESSION['todays_total_recs'] = $row[0];
				mysqli_free_result($r);
				
				//ASC and DESC - TOGGLE
				$sort_order = 'DESC'; 
				if(isset($_GET['sortorder']))	{ 
					if($_GET['sortorder'] == 'ASC') { 
						$sort_order = 'DESC'; 
					} else { 
						$sort_order = 'ASC'; 
					} 
				} 
				//SORT OPTION
				$sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'entrydt';
				switch ($sort){
					case 'ln':
						case 'ln':
							$order_by = 'lastname '.$sort_order;
							break;
						case 'entrydt':
							$order_by = 'entry_date '.$sort_order;
							break;
					default:
						$order_by = 'entry_date ASC';
						break;
				}

				// Make the query:
				$q = "SELECT contact_id, firstname, lastname, email, phone, tier_status, entry_date 
					  FROM contacts
					  WHERE rep_id = '$repsid'
					  AND next_action_date = '$mysqldate'
					  ORDER BY $order_by 
					  LIMIT $start, $display";
				
				$r = @mysqli_query ($dbc, $q); // Run the query.

				if ($r) { // If it ran OK, display the records.

					echo '<table id="csstable">
						<tr>
							<th colspan=7>CONTACTS WITH TASKS ('.$_SESSION['todays_total_recs'].' total) for '.$displaydate.'</th>
						</tr>
						<tr>
							<th scope="col">Actions</th>
							<th scope="col">Lastname</th>
							<th scope="col">Firstname</th>
							<th scope="col">Email</th>
							<th scope="col">Phone</th>
							<th scope="col"><a href="rep_todays_tasks.php?sort=entrydt&sortorder='.$sort_order.'&actiondate='.$mysqldate.
												'&nextactiondate='.$displaydate.'">Entry Date</a></th>
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
						//echo '<tr class="'.$rowspec.'">
						//		<td><a href="javascript: contactEditInfo(\''.$row['contact_id'].'\');">Edit</a>
						//					&nbsp;<a href="javascript: contactDumpInfo(\''.$row['contact_id'].'\');">Dump</a></td>
						#SIMPLEMODAL mod 08/14/2013 dkf
						# href attribute holds the contacts id.  it is sliced off and used in the
						# $.get() ajax call in eapcrm_contacts.js
						echo '<tr class="'.$rowspec.'">
							<td><div id="contact-form"><a href="'.$row['contact_id'].'" class="contact">Edit</a></div>
								&nbsp;<div id="dumpcontact-form"><a href="'.$row['contact_id'].'" class="dumpcontact">Dump</a></div></td>
								<td>'.$row['lastname'].'</td>
								<td>'.$row['firstname'].'</td>
								<td>'.$row['email'].'</td>
								<td>'.$row['phone'].'</td>
								<td>'.$formatted_entrydt.'</td>
								<td>'.substr($row['tier_status'],0,1).'</td>
								</tr>';
						
					}
					
					//CREATE PAGE LINKS TO OTHER RECS
					if ($pages > 1) {
						
						echo '<tr>
								<th colspan=7 align="center">
								<div class="paginator">';
						

						$current_page = ($start/$display) + 1;

						//if not first page - make previous button
						if ($current_page != 1) {
							//echo '<a href="javascript: pageResults('.($start - $display).','.$pages.');">Previous</a>&nbsp;';
							echo '<a href="rep_todays_tasks.php?s='.($start - $display).'&p='.$pages.
									'&actiondate='.$mysqldate.'">Previous</a>&nbsp;';
						}
						//make number links to pages
						for ($i = 1; $i <= $pages; $i++){
							if ($i != $current_page){
								//echo '<a href="javascript: pageResults('.(($display * ($i - 1))).','.$pages.');">'.$i.'</a>&nbsp;';
								echo '<a href="rep_todays_tasks.php?s='.(($display * ($i - 1))).'&p='.$pages.
									'&actiondate='.$mysqldate.'">'.$i.'</a>&nbsp;';
							} else {
								//current page isn't a link
								echo $i . ' ';
							}
						} //end for loop
								
						//not last page - make a next link
						if ($current_page != $pages){
							//echo '&nbsp;<a href="javascript: pageResults('.($start + $display).','.$pages.');">Next</a>';
							echo '&nbsp;<a href="rep_todays_tasks.php?s='.($start + $display).'&p='.$pages.
									'&actiondate='.$mysqldate.'">Next</a>';
						}

						//close p
						//echo '</p>';

						//Close Paginator Div and Table
						echo '</div></th></tr></table>';
							
					} else {
						//no nav needed
						echo '</table>';
					}//end if ($pages > 1)

					//refresh button
					echo '<p align="center">
						<form action="rep_todays_tasks.php" method="GET" >
							<input type="hidden" name="s" value="'.$start.'" />
							<input type="hidden" name="actiondate" value="'.$mysqldate.'" />
							<input type="hidden" name="nextactiondate" value="'.$displaydate.'" />
							<input type="submit" name="submit" class="generalbutton" value="Refresh List" />
						</form>
						</p>';

					mysqli_free_result ($r); // Free up the resources.	

				} else { // If it did not run OK.

					// Public message:
					echo '<p>Your contacts could not be retrieved. We will fix the problem shortly.</p>';
					echo '</div>';
					
					// Debugging message:
					//echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $q . '</p>';
					
				} // End of if ($r)

				//unset($_GET);
				mysqli_close($dbc); // Close the database connection.
			?>
		
		</div> <!-- close ajax_viewcontacts -->
		
		<?php include('includes/html/footer.html'); ?>
		
	</div> <!-- close main content -->
	
	

</div>	<!-- close wrapper -->
</body>
</html>