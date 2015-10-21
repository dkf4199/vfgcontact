$(document).ready(function() {
	$("#menu").menu();	//jquery-ui menu	
});	
//*******************************************************************************************
//******************** END DOCUMENT READY (VIEW REP) ACTION LINK *****************
//*******************************************************************************************

//Open function callback
function open(dialog) {
	
}
function resultShow(dialog) {
	//*************************************************************
	// CLOSE LINK - Phone Modal from Results/Summary table list
	//*************************************************************
	$('#contact-container a.resultlistcloselink').click(function (e) {
		e.preventDefault();
		
		//get link href - it holds the contacts id
		var linkValue = this.href;
		var lastslash = linkValue.lastIndexOf('/');
		var cid = linkValue.slice(lastslash+1);
		//alert(templatecat);
		
		//Close Phone Modal
		$.modal.close();
		
		
	});		//close $('#matrix-container .matrixcloselink, #contact-container a.texttemplatecloselink').click(function (e)

}
function show(dialog) {
	//***********************************************************
	// EMAIL ICON next to rep's email address
	//
	// DISPLAY EMAIL MODAL
	//***********************************************************
	$('#contact-container a.rep_email').click(function (e) {
		e.preventDefault();
		
		//get link href - it holds the contacts id
		var linkValue = this.href;
		var lastslash = linkValue.lastIndexOf('/');
		var rid = linkValue.slice(lastslash+1);
		alert(rid);
		
		
	});		//close $('#contact-container .contact-mailer').click(function (e)
	
	//********************************************************
	// SEND MAIL LINK on the emailer modal div
	//
	// SEND MAIL TO CONTACT, THEN RE-OPEN EDIT MODAL
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
	// THE CELLPHONE ICON NEXT TO THE phone input field
	// DISPLAY PHONE SCRIPTS MODAL
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
		
		// 09-01-2013 comment out the notes modal - display the phone scripts modal here
		$.get("ajax_resultlist_phone_modal.php", { currentcontactid: cid, currentfirstname: curr_fname, currentlastname: curr_lname }, function(data){
			// create a modal dialog with the data
			$(data).modal({
				//closeHTML: "<a href='#' title='Close' class='modal-close'>close X</a>",
				position: ["3%",], //top position
				opacity: 80,
				overlayId: 'contact-overlay',
				containerId: 'contact-container',	//css for container is in modaldivs.css
				minHeight: 700,
				minWidth: 950,
				maxHeight: 700,
				maxWidth: 950,
				onShow: show
			});
		});
		/*
		$.get("ajax_matrix_phone_modal.php", { currentid: curr_id, currentfirstname: curr_fname, currentlastname: curr_lname, currentemail: curr_email }, function(data){
			// create a modal dialog with the data
			$(data).modal({
				//closeHTML: "<a href='#' title='Close' class='modal-close'>close X</a>",
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
		*/
		
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
	// THE TEXT CONVERSATION ICON next to phone input field
	// DISPLAY TEXT MODAL
	//***********************************************************
	$('#contact-container a.matrix-text').click(function (e) {
		e.preventDefault();
		
		//Get values off the edit modal and pass to texter modal
		var curr_fname = $('#contact-container #first_name').val();	//contacts firstname
		var curr_lname = $('#contact-container #last_name').val();	//contacts lastname
		var curr_email = $('#contact-container #email').val();		//contacts email (don't really need)
		var curr_id = $('#contact-container #cid').val();
		//alert(curr_fname+' '+curr_lname+' '+curr_email);
		
		
		//get link href - it holds the contacts id
		var linkValue = this.href;
		var lastslash = linkValue.lastIndexOf('/');
		var cid = linkValue.slice(lastslash+1);
		
		
		//close current edit modal
		$.modal.close();
		
		/* 08/23/13 dkf test out new text template link */
		/* 08/30/13 dkf change text templates modal to retrieve
					    personal text templates for rep         
						change ajax_matrix_text_templates_modal.php
						in the .get call to ajax_texting_modal.php */
						
		$.get("ajax_texting_modal.php", { currentid: curr_id, currentfirstname: curr_fname, 
											  currentlastname: curr_lname, currentemail: curr_email }, function(data){
			// create a modal dialog with the data
			$(data).modal({
				/*closeHTML: "<a href='#' title='Close' class='modal-close'>close X</a>",*/
				position: ["5%",], //top position
				opacity: 70,
				overlayId: 'contact-overlay',
				containerId: 'contact-container',	//css for container is in modaldivs.css
				minHeight: 600,
				minWidth: 700,
				maxHeight: 600,
				maxWidth: 700,
				onShow: show
			});
		});	
	});		//close $('#contact-container .matrix-text').click(function (e)
	
	//********************************************************
	// CLOSE LINK ON ALL NOTE/TEXT MODALS, THEN OPEN EDIT MODAL
	//********************************************************
	$('#matrix-container a.matrixcloselink, #contact-container a.texttemplatecloselink').click(function (e) {
		e.preventDefault();
		
		//get link href - it holds the contacts id
		var linkValue = this.href;
		var lastslash = linkValue.lastIndexOf('/');
		var cid = linkValue.slice(lastslash+1);
		//alert(templatecat);
		
		//Close Text Modal, Reopen EDIT CONTACT modal
		closeReopen(cid);	
	});		//close $('#matrix-container .matrixcloselink, #contact-container a.texttemplatecloselink').click(function (e)
	
}
//**********************************	
// CLOSE FUNCTION show
//**********************************

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
			//alert(result);
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
function getTextTemplate(tid){
	if (tid != ''){
		
		//Get the currcid from the hidden field on the modal
		var cid = $("#contact_id").val();
		
		$.ajax({
				url:"ajax_display_text_template.php",
				type: "GET",
				data: {tid: tid, cid: cid},
				dataType: "html",		
				success:function(result){
					$("#ajax_display_textmessage").html(result);
				}	//end success:function
		}); //end $.ajax
	}
}
function getPhoneScript(script_id){
	
	if (script_id != ''){
		
		//Get values off the phone scripts modal and pass to each script
		var contact_fname = $('#contact-container #contact_firstname').val();
		var contact_lname = $('#contact-container #contact_lastname').val();
		var contact_id = $('#contact-container #contact_id').val();
		var rep_fname = $('#contact-container #reps_firstname').val();
		var rep_lname = $('#contact-container #reps_lastname').val();
		var rep_phone = $('#contact-container #reps_phone').val();
		//alert(curr_fname+' '+curr_lname+' '+curr_email);
			
		switch (script_id) {
			case '1':
				//$("#script_section").load("initialcontact.html");
				$.ajax({
					url:"initialcontactscript.php",
					type: "GET",
					data: {contact_firstname: contact_fname,
						   contact_lastname: contact_lname,
						   contact_id: contact_id,
						   reps_firstname: rep_fname,
						   reps_lastname: rep_lname,
						   reps_phone: rep_phone},
					dataType: "html",		
					success:function(result){
						$("#script_section").html(result);
					}	//end success:function
				}); //end $.ajax
				break;
			case '2':
				//$("#script_section").load("callaftervideo.html");
				$.ajax({
					url:"callaftervideo.php",
					type: "GET",
					data: {contact_firstname: contact_fname,
						   contact_lastname: contact_lname,
						   contact_id: contact_id,
						   reps_firstname: rep_fname,
						   reps_lastname: rep_lname,
						   reps_phone: rep_phone},
					dataType: "html",		
					success:function(result){
						$("#script_section").html(result);
					}	//end success:function
				}); //end $.ajax
				break;
				break;
			case '3':
				//$("#script_section").load("phoneattempt2.html");
				$.ajax({
					url:"phoneattempt2.php",
					type: "GET",
					data: {contact_firstname: contact_fname,
						   contact_lastname: contact_lname,
						   contact_id: contact_id,
						   reps_firstname: rep_fname,
						   reps_lastname: rep_lname,
						   reps_phone: rep_phone},
					dataType: "html",		
					success:function(result){
						$("#script_section").html(result);
					}	//end success:function
				}); //end $.ajax
				break;
		}
		
	}
}

// SAVE TEXT TO COMMUNICATION HISTORY FUNCTION
// Have to do it here.....ajax loaded content is NOT
// instantiated when the modal is first loaded.
function saveTextToHistory(cid){
	
	// need the following off modal screen: txt_template
	var template_name = $('#contact-container #txt_template').val();
	var blank_txt_body = '';
	
	//If blank - we are gonna insert the text body into the history record
	if (template_name == 'blank'){
		blank_txt_body = $('#contact-container #notes').val();
		//alert("Saving Blank Text Message to History : " + cid + "\n" + "Text sent : " + blank_txt_body);
	} else {
		//alert("Saving Text Template " + template_name + " to History.");
	}
	
	//STEP 1 - call ajax to insert text history record	
	$.ajax({
		url:"ajax_matrix_insert_text_history.php",
		type: "POST",
		data: { template_name: template_name, text_body: blank_txt_body, cid: cid },
		dataType: "html",		
		success:function(result){
			//alert(result);
			//$("#email_response").html(result);
		}	//end success:function
	}); //end $.ajax
	//*********************************************
	
	//STEP 2 - close modal and re-open edit modal
	closeReopen(cid);
					
	return false;
}

function saveScriptToHistory(cid, script, calltype){
	//alert(cid+' : '+script+' : '+calltype);
	
	//STEP 1 - call ajax to insert text history record	
	$.ajax({
		url:"ajax_matrix_insert_phonecall_history.php",
		type: "POST",
		data: { cid: cid, script: script, calltype: calltype },
		dataType: "html",		
		success:function(result){
			//alert(result);
			//$("#email_response").html(result);
		}	//end success:function
	}); //end $.ajax
	//*********************************************
	
	//STEP 2 - close modal and re-open edit modal
	closeReopen(cid);
					
	return false;
	
}