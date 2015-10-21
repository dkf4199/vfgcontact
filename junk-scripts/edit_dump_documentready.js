$(document).ready(function() {

	$("#datepickerstatic").datepicker({ dateFormat: "mm-dd-yy" });
	var message = null;
	var theID = null;
	
	//**********************
	// EDIT MODAL OPEN LINK
	//**********************
	$('#contact-form input.contact, #contact-form a.contact').click(function (e) {
		e.preventDefault();
		// load the contact info form using ajax
		
		// .get(url, data, success)....in that order
		var linkValue = this.href;
		var lastslash = linkValue.lastIndexOf('/');
		var cid = linkValue.slice(lastslash+1);
		
		$.get("ajax_getcontact_info_modal.php", { currentcontactid: cid }, function(data){
			// create a modal dialog with the data
			$(data).modal({
				closeHTML: "<a href='#' title='Close' class='modal-close'>close X</a>",
				position: ["5%",], //top position
				opacity: 70,
				overlayId: 'contact-overlay',
				containerId: 'contact-container',
				minHeight: 550,
				minWidth: 950,
				maxHeight: 550,
				maxWidth: 950,
				/*onOpen: open,*/
				onShow: show,
				onClose: close
			});
			// dkf 08-14-2013 have to register datepicker on content
			$("#datepicker").datepicker({ dateFormat: "mm-dd-yy", minDate: 0 });
		});
	});		//END EDIT MODAL LINK CLICK
	
	//******************************
	// DUMP MODAL LINK
	//******************************
	$('#dumpcontact-form input.dumpcontact, #dumpcontact-form a.dumpcontact').click(function (e) {
		e.preventDefault();

		// .get(url, data, success)....in that order
		var linkValue = this.href;
		var lastslash = linkValue.lastIndexOf('/');
		var cid = linkValue.slice(lastslash+1);
		
		$.get("ajax_getdumpcontact_info_modal.php", { currentcontactid: cid }, function(data){
			// create a modal dialog with the data
			$(data).modal({
				closeHTML: "<a href='#' title='Close' class='modal-close'>close X</a>",
				position: ["5%",], //top position
				opacity: 70,
				overlayId: 'contact-overlay',
				containerId: 'contact-container',
				minHeight: 550,
				minWidth: 950,
				maxHeight: 550,
				maxWidth: 950,
				/*onOpen: open,*/
				onShow: show,
				onClose: close
			});
			// dkf 08-14-2013 have to register datepicker on content
			//$("#datepicker").datepicker({ dateFormat: "mm-dd-yy", minDate: 0 });
		});
	});		//END DUMP LINK
	
	//************************
	// RESTORE MODAL LINK
	//************************
	$('#restorecontact-form input.restorecontact, #restorecontact-form a.restorecontact').click(function (e) {
		e.preventDefault();

		// .get(url, data, success)....in that order
		var linkValue = this.href;
		var lastslash = linkValue.lastIndexOf('/');
		var cid = linkValue.slice(lastslash+1);
		//var cid = linkValue.slice(-7);
		//alert(cid);
		
		$.get("ajax_getrestorecontact_info_modal.php", { currentcontactid: cid }, function(data){
			// create a modal dialog with the data
			$(data).modal({
				closeHTML: "<a href='#' title='Close' class='modal-close'>close X</a>",
				position: ["5%",], //top position
				opacity: 70,
				overlayId: 'contact-overlay',
				containerId: 'contact-container',
				minHeight: 550,
				minWidth: 950,
				maxHeight: 550,
				maxWidth: 950,
				onShow: show,
				onClose: close
			});
		});
	});		//END RESTORE MODAL LINK
	
	//*********************************
	// FLUSH MODAL LINK
	//*********************************
	$('#flushcontact-form input.flushcontact, #flushcontact-form a.flushcontact').click(function (e) {
		e.preventDefault();

		// .get(url, data, success)....in that order
		var linkValue = this.href;
		var lastslash = linkValue.lastIndexOf('/');
		var cid = linkValue.slice(lastslash+1);
		//alert(cid);
		
		$.get("ajax_getflushcontact_info_modal.php", { currentcontactid: cid }, function(data){
			// create a modal dialog with the data
			$(data).modal({
				closeHTML: "<a href='#' title='Close' class='modal-close'>close X</a>",
				position: ["5%",], //top position
				opacity: 70,
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
			//$("#datepicker").datepicker({ dateFormat: "mm-dd-yy", minDate: 0 });
		});
	});
	
});	
//*******************************************************************************************
//*************************** END DOCUMENT READY ********************************************
//*******************************************************************************************

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
					opacity: 70,
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
				opacity: 70,
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
				opacity: 70,
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
				opacity: 70,
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
			opacity: 70,
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