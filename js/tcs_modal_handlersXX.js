$(document).ready(function() {
	
	//DATEPICKER INITIALIZATIONS:
	$("#datepicker").datepicker({ dateFormat: "mm-dd-yy", minDate: 0 });		//datepicker on rep_edit_contact.php
	//$("#datepickermeeting").datepicker({ dateFormat: "mm-dd-yy", minDate: 0 });	//datepicker for tier meeting on rep_edit_contact.php
	
	$("#datepickerstatic").datepicker({ dateFormat: "mm-dd-yy" });
	
	$("#dp_startdate").datepicker({ dateFormat: "mm-dd-yy" });
	$("#dp_tier3_date").datepicker({ dateFormat: "mm-dd-yy" });
	$("#dp_crm_date").datepicker({ dateFormat: "mm-dd-yy" });
	$("#dp_tier4_date").datepicker({ dateFormat: "mm-dd-yy" });
	$("#dp_plandate").datepicker({ dateFormat: "mm-dd-yy" });
	$("#dp_recruitsdate").datepicker({ dateFormat: "mm-dd-yy" });
	
	var message = null;
	var theID = null;
	
	//Start Date change event - 7 and 30 day data
	//*********************************************
	$( "#dp_startdate" ).change(function() {
		//alert( "Handler for start_date.change() called." );
		var datestring7 = '';
		var datestring30 = '';
		$("#seven_day_date").val('');
		$("#thirty_day_date").val('');
		var pickedDate = $("#dp_startdate").val();
		
		if (pickedDate != ''){
			var sevenDayDate = new Date(pickedDate);
			var thirtyDayDate = new Date(pickedDate);
			
			// 7-Day
			var dayOfMonth = sevenDayDate.getDate();
			sevenDayDate.setDate(dayOfMonth + 7);
			
			var year = sevenDayDate.getFullYear();
			var day = sevenDayDate.getDate();
			var month = sevenDayDate.getMonth() + 1;
			
			datestring7 = ( (month < 10) ? "0" + month : month ) + "-";
			datestring7 +=  ( (day < 10) ? "0" + day : day ) + "-";
			datestring7 += year;

			$("#seven_day_date").val(datestring7);
			$("#hidden_seven_day").val(datestring7);
			
			// 30-Day
			var dayOfMonth30 = thirtyDayDate.getDate();
			thirtyDayDate.setDate(dayOfMonth30 + 30);
			
			var year30 = thirtyDayDate.getFullYear();
			var day30 = thirtyDayDate.getDate();
			var month30 = thirtyDayDate.getMonth() + 1;
			
			datestring30 = ( (month30 < 10) ? "0" + month30 : month30 ) + "-";
			datestring30 +=  ( (day30 < 10) ? "0" + day30 : day30 ) + "-";
			datestring30 += year30;

			$("#thirty_day_date").val(datestring30);
			$("#hidden_thirty_day").val(datestring30);
		}

	});
	//*********************************************
	
	//******************************
	// DUMP MODAL LINK
	//******************************
	$('#dumpcontact-form input.dumpcontact, #dumpcontact-form a.bluelink').click(function (e) {
		e.preventDefault();

		// .get(url, data, success)....in that order
		var linkValue = this.href;
		var lastslash = linkValue.lastIndexOf('/');
		var cid = linkValue.slice(lastslash+1);
		//alert(cid);
		
		$.get("ajax_dumprestoreflush_info_modal.php", { currentcontactid: cid, action: 'dump' }, function(data){
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
				zIndex: 3000,
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
	$('#restorecontact-form input.restorecontact, #restorecontact-form a.bluelink').click(function (e) {
		e.preventDefault();

		// .get(url, data, success)....in that order
		var linkValue = this.href;
		var lastslash = linkValue.lastIndexOf('/');
		var cid = linkValue.slice(lastslash+1);
		//var cid = linkValue.slice(-7);
		//alert(cid);
		
		$.get("ajax_dumprestoreflush_info_modal.php", { currentcontactid: cid, action: 'restore' }, function(data){
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
				zIndex: 3000,
				onShow: show,
				onClose: close
			});
		});
	});		//END RESTORE MODAL LINK
	
	//*********************************
	// FLUSH MODAL LINK
	//*********************************
	$('#flushcontact-form input.flushcontact, #flushcontact-form a.bluelink').click(function (e) {
		e.preventDefault();

		// .get(url, data, success)....in that order
		var linkValue = this.href;
		var lastslash = linkValue.lastIndexOf('/');
		var cid = linkValue.slice(lastslash+1);
		//alert(cid);
		
		$.get("ajax_dumprestoreflush_info_modal.php", { currentcontactid: cid, action: 'flush' }, function(data){
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
				zIndex: 3000,
				onShow: show,
				onClose: close
			});
			// dkf 08-14-2013 have to register datepicker on content
			//$("#datepicker").datepicker({ dateFormat: "mm-dd-yy", minDate: 0 });
		});
	});
	
	/* ***************************************************************************** */
	/* ROTARY PHONE HEADSET MODAL img in the summary list */
	/* 09/01/2013 comment out for now.  This is going to be for express calls */
	/* ***************************************************************************** */
	$('a.phonelist_modal').click(function (e) {
		e.preventDefault();
		
		//get link href - it holds the contacts id
		var linkValue = this.href;
		var lastslash = linkValue.lastIndexOf('/');
		var cid = linkValue.slice(lastslash+1);
		
	});		//close $('a.phonelist_modal').click(function (e)
	
	//***********************************************************
	// THE EMAIL ICON NEXT TO THE email address input field
	// DISPLAY THE EMAIL MODAL
	//***********************************************************
	$('.editcontactform a.email_modal_main').click(function (e) {
		e.preventDefault();
		
		//Get values off the edit modal and pass to emailer modal
		var curr_fname = $('.editcontactform #first_name').val();
		var curr_lname = $('.editcontactform #last_name').val();
		var curr_email = $('.editcontactform #email').val();
		var curr_id = $('.editcontactform #cid').val();
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
			$.get("ajax_emailer_modal.php",	
					{ currentid: curr_id, 
					  currentfirstname: curr_fname, 
					  currentlastname: curr_lname, 
					  currentemail: curr_email }, function(data){
				// create a modal dialog with the data
				$(data).modal({
					/*closeHTML: "<a href='#' title='Close' class='modal-close'>close X</a>",*/
					position: ["5%",], //top position
					opacity: 70,
					overlayId: 'contact-overlay',
					containerId: 'emailer-container',
					minHeight: 500,
					minWidth: 1050,
					maxHeight: 500,
					maxWidth: 1050,
					zIndex: 3000,
					onShow: show
				});
			});
		}
		
	});		//close $('.editcontactform a.email_modal_main').click(function (e)
	
	//***********************************************************
	// THE CELLPHONE ICON NEXT TO THE phone input field
	// DISPLAY PHONE SCRIPTS MODAL
	//***********************************************************
	$('.editcontactform a.phone_modal_main').click(function (e) {
		e.preventDefault();
		
		//Get values off the edit modal and pass to emailer modal
		var curr_fname = $('.editcontactform #first_name').val();
		var curr_lname = $('.editcontactform #last_name').val();
		var curr_email = $('.editcontactform #email').val();
		var contactPhone = $('.editcontactform #phone').val();
		var cTzone = $('.editcontactform #timezone :selected').val();
		var curr_id = $('.editcontactform #cid').val();
		//alert(curr_fname+' '+curr_lname+' '+curr_email);
		
		
		//get link href - it holds the contacts id
		var linkValue = this.href;
		var lastslash = linkValue.lastIndexOf('/');
		var cid = linkValue.slice(lastslash+1);
		
		
		// 09-01-2013 comment out the notes modal - display the phone scripts modal here
		// 01/17/2014 call ajax_phonescript_modal.php, not ajax_resultlist_phone_modal.php
		//
		$.get("ajax_phonescript_modal.php", 
				{ currentcontactid: cid,
				  currentfirstname: curr_fname, 
				  currentlastname: curr_lname,
				  contactphone: contactPhone,
				  contacttimezone: cTzone }, function(data){
			// create a modal dialog with the data
			$(data).modal({
				closeHTML: "<a href='#' title='Close' class='modal-close'>close X</a>",
				position: ["2%",], //top position
				opacity: 80,
				overlayId: 'contact-overlay',
				containerId: 'contact-container',	//css for container is in modaldivs.css
				minHeight: 900,
				minWidth: 950,
				maxHeight: 1100,
				//maxWidth: 950,
				//height: 600,
				//width: 950,
				//height: 1000,
				autoresize: true,
				zIndex: 3000,
				onShow: show
			});
		});
		
	});		//close $('.editcontactform a.phone_modal_main').click(function (e)
	
	//***********************************************************
	// THE TEXT CONVERSATION ICON next to phone input field
	// DISPLAY TEXT MODAL
	//***********************************************************
	$('.editcontactform a.text_modal_main').click(function (e) {
		e.preventDefault();
		
		var curr_phone = '';
		
		//Get values off the edit screen and pass to text modal
		var curr_fname = $('.editcontactform #first_name').val();	//contacts firstname
		var curr_lname = $('.editcontactform #last_name').val();	//contacts lastname
		curr_phone = $('.editcontactform #phone').val();			//contacts phone
		var curr_email = $('.editcontactform #email').val();		//contacts email (don't really need)
		var curr_id = $('.editcontactform #cid').val();
		//alert(curr_fname+' '+curr_lname+' '+curr_email);
		
		
		//get link href - it holds the contacts id
		var linkValue = this.href;
		var lastslash = linkValue.lastIndexOf('/');
		var cid = linkValue.slice(lastslash+1);
		
		
		//close current edit modal
		//$.modal.close();
		
		// DISPLAY MODAL ONLY IF PHONE NUMBER IS SET
		if (curr_phone != ''){
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
					zIndex: 3000,
					onShow: show
				});
			});
		} else {
			$("#update_messages").html("<p>Update contact's record with their phone number to use the texting modal.</p>");
		}
	});		//close $('.editcontactform a.text_modal_main').click(function (e)
	
	//***********************************************************
	// THE PEOPLE ICON next to DISPOSITION dropdown list
	//
	// DISPOSITION PERCENTAGES MODAL
	//***********************************************************
	$('.editcontactform a.disposition_percentages_modal').click(function (e) {
		e.preventDefault();
		
		// Need the rep's wfgid here
		//get link href - it holds the reps id
		var linkValue = this.href;
		var lastslash = linkValue.lastIndexOf('/');
		var repid = linkValue.slice(lastslash+1);
		
		// MODAL AJAX
		$.get("ajax_disposition_percentages_modal.php", { }, function(data){
			// create a modal dialog with the data
			$(data).modal({
				closeHTML: "<a href='#' title='Close' class='modal-close'>close X</a>",
				position: ["5%",], //top position
				opacity: 70,
				overlayId: 'contact-overlay',
				containerId: 'disposition-container',	//css for container is in modaldivs.css
				minHeight: 500,
				minWidth: 500,
				maxHeight: 500,
				maxWidth: 500,
				zIndex: 3000,
				onShow: show
			});
		});	
	});		//close $('.editcontactform a.disposition_percentages_modal').click(function (e)	
	
	//***********************************************************
	//    LOOKUP CONSULTANT MODAL
	//***********************************************************
	$('.editcontactform a.lookup_consultant_modal').click(function (e) {
		e.preventDefault();
		
		//Get values off the edit screen and pass to text modal
		var c_fname = $('.editcontactform #first_name').val();				//contacts firstname
		var c_lname = $('.editcontactform #last_name').val();				//contacts lastname
		var c_state = $('.editcontactform #state :selected').val();			//contacts state
		var c_state_text = $('.editcontactform #state :selected').text();	//contacts state
		var c_id = $('.editcontactform #cid').val();
		
		// DKF 03/03/2014 change ajax_lookup_consultant_modal.php to ajax_lookup_fc_consultant_modal.php
		if (c_state != ''){
			$.get("ajax_fc_lookup_consultant_modal.php", 
					{ state: c_state, 
					  statetext: c_state_text, 
					  c_first: c_fname, 
					  c_last: c_lname,
					  c_id: c_id}, function(data){
						// create a modal dialog with the data
						$(data).modal({
							closeHTML: "<a href='#' title='Close' class='modal-close'>close X</a>",
							position: ["2%",], //top position
							opacity: 70,
							overlayId: 'modal-overlay',
							containerId: 'modal-container',	//css for container is in modaldivs.css
							minHeight: 800,
							minWidth: 1000,
							maxHeight: 800,
							maxWidth: 1000,
							zIndex: 3000,
							onShow: show
						});
						// Have to register datepickers on content
						$("#consultantdatepicker").datepicker({ dateFormat: "mm-dd-yy", minDate: 0 });
						$("#consultant_addevent_startdate_dp").datepicker({ dateFormat: "mm-dd-yy", minDate: 0 });
					});
		} else {
			$("#update_messages").html("<p>No State on contact's record.</p>");
		}
		
	});		//close $('.editcontactform a.lookup_consultant_modal').click(function (e)
	
	//***********************************************************
	//    ASSIGNED MANAGER CALENDAR MODAL
	//***********************************************************
	$('.editcontactform a.lookup_manager_calendar_modal').click(function (e) {
		e.preventDefault();
		
		//Get values off the edit screen and pass to text modal
		var c_fname = $('.editcontactform #first_name').val();				//contacts firstname
		var c_lname = $('.editcontactform #last_name').val();				//contacts lastname
		var c_state = $('.editcontactform #state :selected').val();			//contacts state
		var c_state_text = $('.editcontactform #state :selected').text();	//contacts state text
		var c_id = $('.editcontactform #cid').val();
		
		var mgr_vfgid = $('.editcontactform #assigned_manager').val();		//assigned manager's vfgid
		
		
		// DKF 03/03/2014 change ajax_lookup_consultant_modal.php to ajax_lookup_fc_consultant_modal.php
		if (c_state != ''){
			$.get("ajax_fc_assigned_managers_calendar_modal.php", 
					{ mgr_vfgid: mgr_vfgid, 
					  c_first: c_fname, 
					  c_last: c_lname,
					  c_id: c_id}, function(data){
						// create a modal dialog with the data
						$(data).modal({
							closeHTML: "<a href='#' title='Close' class='modal-close'>close X</a>",
							position: ["2%",], //top position
							opacity: 70,
							overlayId: 'modal-overlay',
							containerId: 'modal-container',	//css for container is in modaldivs.css
							minHeight: 800,
							minWidth: 1000,
							maxHeight: 800,
							maxWidth: 1000,
							zIndex: 3000,
							onShow: show
						});
						// Have to register datepickers on content
						$("#managerdatepicker").datepicker({ dateFormat: "mm-dd-yy", minDate: 0 });
						$("#manager_addevent_startdate_dp").datepicker({ dateFormat: "mm-dd-yy", minDate: 0 });
					});
		} else {
			$("#update_messages").html("<p>No State on contact's record.</p>");
		}
		
	});		//close $('.editcontactform a.lookup_consultant_modal').click(function (e)
	
	//***********************************************************
	// MISSED MEETING Mail Icon NEXT TO THE STATUS DROPDOWN BOX
	// Sends emails to Inviter and Manager of MISSED MEETING
	//***********************************************************
	$('.editcontactform a.missed_meeting_email').click(function (e) {
		e.preventDefault();
		
		//Get values off the edit modal and pass to emailer modal
		var inviter = $('.editcontactform #inviter').val();				//inviter's rep_id
		var manager = $('.editcontactform #assigned_manager').val();	//manager's vfgrepid
		var tierset = $('.editcontactform #set_tier').val();
		//var status = $("#tierstep :selected").val();
		var status = $('.editcontactform #set_status').val();
		var contact_firstname = $('.editcontactform #first_name').val();
		var contact_lastname = $('.editcontactform #last_name').val();
		var tierstatus = tierset+status;
		
		if (tierstatus == '3F' || tierstatus == '4F'){
			//alert('Tier '+tierstatus+' meeting missed.');
			
			//Call ajax to send mails...
			$.ajax({
				url:"ajax_missed_meeting_mail.php",
				type: "POST",
				data: { inviter: inviter, 
						manager: manager, 
						tierstatus: tierstatus,
						cfirst: contact_firstname,
						clast: contact_lastname},
				dataType: "html",		
				success:function(result){
					//alert(result);
					$("#update_messages").html(result);
				},	//end success:function
				error:function(jqXHR, textStatus, errorThrown){
					$("#update_messages").html(errorThrown);
				}
			}); //end $.ajax
			//*********************************************
		}
		
	});		//close $('.editcontactform a.missed_meeting_email').click(function (e)
	
	//***********************************************************
	// View or View/Edit Policies link
	// Open up POLICY MODAL
	//***********************************************************
	$('.editcontactform a.policy_modal').click(function (e) {
		e.preventDefault();
		
		//Get any values needed
		var curr_cid = $('.editcontactform #cid').val();				//contact's id
		var curr_fname = $('.editcontactform #first_name').val();		//contact's first name
		var curr_lname = $('.editcontactform #last_name').val();				//contact's last name
		//alert(cid);
		
		//AJAX
		$.get("ajax_policy_modal.php", { currentid: curr_cid, 
										 currentfirstname: curr_fname,
									     currentlastname: curr_lname }, function(data){
			// create a modal dialog with the data
			$(data).modal({
				closeHTML: "<a href='#' title='Close' class='modal-close'>close X</a>",
				position: ["2%",], //top position
				opacity: 70,
				overlayId: 'contact-overlay',
				containerId: 'modal-container',	//css for container is in modaldivs.css
				minHeight: 775,
				minWidth: 1000,
				maxHeight: 775,
				maxWidth: 1000,
				zIndex: 3000,
				onShow: show
			});
		});	
		
	});		//close $('.editcontactdiv a.policy_modal').click(function (e)
	
	//*************************************************
	// SAVE POLICY 1-4 LINKS ON rep_edit_policies.php
	//*************************************************
	//POLICY 1
	$('.webform a#save_policy_1').click(function (e) {
		e.preventDefault();
		//Get any values needed
		var p_cid = $('#policy_cid').val();					//contact's id
		var p_inviter = $('#p_inviter').val();				//inviter
		var p_manager = $('#p_manager').val();				//manager
		var p_consultant = $('#p_consultant').val();		//consultant
		var p_fname = $('#p1_first_name').val();			//policy 1 first name
		var p_lname = $('#p1_last_name').val();				//policy 1 last name
		var p_hstatus = $('#p1_hstatus :selected').val();	//policy 1 household status
		var p_type = $('#p1_ptype :selected').val();		//policy 1 policy type
		var p_carrier = $('#p1_carrier :selected').val();	//policy 1 carrier
		var p_tpremium = $('#p1_target_premium').val();		//policy 1 target premium
				
		//set msgs and clear out feedback div
		var msgs = '';
		$('#policy1_msgs').html("");
		//Check values before running update
		if (p_fname == ''){
			msgs += 'First name blank.<br />';
		}
		if (p_lname == ''){
			msgs += 'Last name blank.<br />';
		}
		if (p_hstatus == ''){
			msgs += 'Select household status.<br />';
		}
		if (p_type == ''){
			msgs += 'Select policy type.<br />';
		}
		if (p_carrier == ''){
			msgs += 'Select carrier.<br />';
		}
		if (p_tpremium/p_tpremium != 1){
			msgs += 'Premium not a number.';
		}
		
		if (msgs != ''){
			$('#policy1_msgs').html(msgs);
		} else {
			//RUN AJAX
			//Call ajax to send mails...
			$('#policy1_msgs').html("");
			$.ajax({
				url:"ajax_update_policy.php",
				type: "POST",
				data: { cid: p_cid,
						inviter: p_inviter,
						manager: p_manager,
						consultant: p_consultant,
						policy_num: '1',
						firstname: p_fname, 
						lastname: p_lname,
						household_status: p_hstatus,
						policy_type: p_type,
						carrier: p_carrier,
						target_premium: p_tpremium },
				dataType: "html",		
				success:function(result){
					//alert(result);
					$("#policy1_msgs").html(result);
				},	//end success:function
				error:function(jqXHR, textStatus, errorThrown){
					$("#policy1_msgs").html(errorThrown);
				}
			}); //end $.ajax		
		}
		
	});	 //close $('.webform a.save_policy_1').click(function (e)
	
	//POLICY 2
	$('.webform a#save_policy_2').click(function (e) {
		e.preventDefault();
		//Get any values needed
		var p_cid = $('#policy_cid').val();					//contact's id
		var p_inviter = $('#p_inviter').val();				//inviter
		var p_manager = $('#p_manager').val();				//manager
		var p_consultant = $('#p_consultant').val();		//consultant
		var p_fname = $('#p2_first_name').val();			//policy 2 first name
		var p_lname = $('#p2_last_name').val();				//policy 2 last name
		var p_hstatus = $('#p2_hstatus :selected').val();	//policy 2 household status
		var p_type = $('#p2_ptype :selected').val();		//policy 2 policy type
		var p_carrier = $('#p2_carrier :selected').val();	//policy 2 carrier
		var p_tpremium = $('#p2_target_premium').val();		//policy 2 target premium
		
		//set msgs and clear out feedback div
		var msgs = '';
		$('#policy2_msgs').html("");
		//Check values before running update
		if (p_fname == ''){
			msgs += 'First name blank.<br />';
		}
		if (p_lname == ''){
			msgs += 'Last name blank.<br />';
		}
		if (p_hstatus == ''){
			msgs += 'Select household status.<br />';
		}
		if (p_type == ''){
			msgs += 'Select policy type.<br />';
		}
		if (p_carrier == ''){
			msgs += 'Select carrier.<br />';
		}
		if (p_tpremium/p_tpremium != 1){
			msgs += 'Premium not a number.';
		}
		
		if (msgs != ''){
			$('#policy2_msgs').html(msgs);
		} else {
			//RUN AJAX
			//Call ajax to send mails...
			$('#policy2_msgs').html("");
			$.ajax({
				url:"ajax_update_policy.php",
				type: "POST",
				data: { cid: p_cid,
						inviter: p_inviter,
						manager: p_manager,
						consultant: p_consultant,
						policy_num: '2',
						firstname: p_fname, 
						lastname: p_lname,
						household_status: p_hstatus,
						policy_type: p_type,
						carrier: p_carrier,
						target_premium: p_tpremium },
				dataType: "html",		
				success:function(result){
					//alert(result);
					$("#policy2_msgs").html(result);
				},	//end success:function
				error:function(jqXHR, textStatus, errorThrown){
					$("#policy2_msgs").html(errorThrown);
				}
			}); //end $.ajax	
		}
	});	 //close $('.webform a.save_policy_2').click(function (e)
	
	//POLICY 3
	$('.webform a#save_policy_3').click(function (e) {
		e.preventDefault();
		//Get any values needed
		var p_cid = $('#policy_cid').val();					//contact's id
		var p_inviter = $('#p_inviter').val();				//inviter
		var p_manager = $('#p_manager').val();				//manager
		var p_consultant = $('#p_consultant').val();		//consultant
		var p_fname = $('#p3_first_name').val();			//policy 3 first name
		var p_lname = $('#p3_last_name').val();				//policy 3 last name
		var p_hstatus = $('#p3_hstatus :selected').val();	//policy 3 household status
		var p_type = $('#p3_ptype :selected').val();		//policy 3 policy type
		var p_carrier = $('#p3_carrier :selected').val();	//policy 3 carrier
		var p_tpremium = $('#p3_target_premium').val();		//policy 3 target premium
		
		//set msgs and clear out feedback div
		var msgs = '';
		$('#policy3_msgs').html("");
		//Check values before running update
		if (p_fname == ''){
			msgs += 'First name blank.<br />';
		}
		if (p_lname == ''){
			msgs += 'Last name blank.<br />';
		}
		if (p_hstatus == ''){
			msgs += 'Select household status.<br />';
		}
		if (p_type == ''){
			msgs += 'Select policy type.<br />';
		}
		if (p_carrier == ''){
			msgs += 'Select carrier.<br />';
		}
		if (p_tpremium/p_tpremium != 1){
			msgs += 'Premium not a number.';
		}
		
		if (msgs != ''){
			$('#policy3_msgs').html(msgs);
		} else {
			//RUN AJAX
			//Call ajax to send mails...
			$('#policy3_msgs').html("");
			$.ajax({
				url:"ajax_update_policy.php",
				type: "POST",
				data: { cid: p_cid,
						inviter: p_inviter,
						manager: p_manager,
						consultant: p_consultant,
						policy_num: '3',
						firstname: p_fname, 
						lastname: p_lname,
						household_status: p_hstatus,
						policy_type: p_type,
						carrier: p_carrier,
						target_premium: p_tpremium },
				dataType: "html",		
				success:function(result){
					//alert(result);
					$("#policy3_msgs").html(result);
				},	//end success:function
				error:function(jqXHR, textStatus, errorThrown){
					$("#policy3_msgs").html(errorThrown);
				}
			}); //end $.ajax	
		}
	});	 //close $('.webform a.save_policy_3').click(function (e)
	
	//POLICY 4
	$('.webform a#save_policy_4').click(function (e) {
		e.preventDefault();
		//Get any values needed
		var p_cid = $('#policy_cid').val();					//contact's id
		var p_inviter = $('#p_inviter').val();				//inviter
		var p_manager = $('#p_manager').val();				//manager
		var p_consultant = $('#p_consultant').val();		//consultant
		var p_fname = $('#p4_first_name').val();			//policy 4 first name
		var p_lname = $('#p4_last_name').val();				//policy 4 last name
		var p_hstatus = $('#p4_hstatus :selected').val();	//policy 4 household status
		var p_type = $('#p4_ptype :selected').val();		//policy 4 policy type
		var p_carrier = $('#p4_carrier :selected').val();	//policy 4 carrier
		var p_tpremium = $('#p4_target_premium').val();		//policy 4 target premium
		
		//set msgs and clear out feedback div
		var msgs = '';
		$('#policy4_msgs').html("");
		//Check values before running update
		if (p_fname == ''){
			msgs += 'First name blank.<br />';
		}
		if (p_lname == ''){
			msgs += 'Last name blank.<br />';
		}
		if (p_hstatus == ''){
			msgs += 'Select household status.<br />';
		}
		if (p_type == ''){
			msgs += 'Select policy type.<br />';
		}
		if (p_carrier == ''){
			msgs += 'Select carrier.<br />';
		}
		if (p_tpremium/p_tpremium != 1){
			msgs += 'Premium not a number.';
		}
		
		if (msgs != ''){
			$('#policy4_msgs').html(msgs);
		} else {
			//RUN AJAX
			//Call ajax to send mails...
			$('#policy4_msgs').html("");
			$.ajax({
				url:"ajax_update_policy.php",
				type: "POST",
				data: { cid: p_cid,
						inviter: p_inviter,
						manager: p_manager,
						consultant: p_consultant,
						policy_num: '4',
						firstname: p_fname, 
						lastname: p_lname,
						household_status: p_hstatus,
						policy_type: p_type,
						carrier: p_carrier,
						target_premium: p_tpremium },
				dataType: "html",		
				success:function(result){
					//alert(result);
					$("#policy4_msgs").html(result);
				},	//end success:function
				error:function(jqXHR, textStatus, errorThrown){
					$("#policy4_msgs").html(errorThrown);
				}
			}); //end $.ajax	
		}
	});	 //close $('.webform a.save_policy_4').click(function (e)
	
	//*************************************
	// ADD NOTE - PEN IMAGE ICON click
	//*************************************
	$('img#add_note').click(function (e) {
		e.preventDefault();
		
		$("#add_note_msgs").html("");
		$("#notes_history").html("");
		
		var notes = $("#notes").val();
		var cid = $("#cid").val();
		
		
		
		if (notes != ''){
			//alert( "Handler for add note pen.png .click() called. "+cid );
			$.ajax({
				url:"ajax_add_note.php",
				type: "POST",
				data: {cid: cid, note: notes},
				dataType: "html",		
				success:function(result){
					$("#add_note_msgs").html(result);
				}	//end success:function
			}); //end $.ajax
			
			//RELOAD NOTES
			$.ajax({
				url:"ajax_reload_comm_notes.php",
				type: "GET",
				data: {cid: cid},
				dataType: "html",		
				success:function(result){
					$("#notes_history").html(result);
				}	//end success:function
			}); //end $.ajax
			//RELOADS PAGE
			//window.location.href=window.location.href;
			
		} else {
			$("#add_note_msgs").html("Add note first...");
		}
		
	});		//close $('img#add_note').click(function (e)
	
	//*************************************
	// ERASE NOTE - ERASER IMAGE ICON click
	//*************************************
	$('img#erase_note').click(function (e) {
		e.preventDefault();
		
		$("#add_note_msgs").html("");
		$("#notes").val('');
		
	});		//close $('img#add_note').click(function (e)
	
});		//END DOCUMENT READY
//********************************************************************//

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
		
		
	});		//close $('#contact-container a.resultlistcloselink').click(function (e)

}

//***************************************************************************************************
//          LINKS ON THE DISPLAYED MODALS
//***************************************************************************************************
function show(dialog) {
	//********************************************************
	// SEND MAIL LINK on the emailer modal div
	//
	// SEND MAIL TEMPLATE TO CONTACT
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
				url:"ajax_send_gmail.php",
				type: "POST",
				data: { currentid: cid,
						templatecat: templatecat,
						templateid: templateid,
						to_first: to_first,
						to_last: to_last,
						to_email: to_email,
						from_email: from_email },
				dataType: "html",		
				success:function(result){
					//alert(result);
					$("#email_response").html(result);
				},	//end success:function
				error:function(jqXHR, textStatus, errorThrown){
					$("#email_response").html(errorThrown);
				}
			}); //end $.ajax
			//*********************************************
			
			//STEP 2 - close modal and re-open edit modal
			//closeReopen(cid);
			
		}
		//otherwise, don't do anything.
		
	});		//close $('#emailer-container a.sendlink').click(function (e)
	
	//********************************************************
	// SEND BLANK EMAIL on the emailer modal div
	//
	// SEND BLANK MAIL TEMPLATE TO CONTACT
	//********************************************************
	$('#emailer-container a.sendlink_blank').click(function (e) {
		e.preventDefault();
		
		var errors = '';
		// need the following off modal screen:
		// subject, body, mailtofirstname, mailtolastname, mailtoemail, mailtofromemail
		var blank_subject = $("#blank_subject").val();
		var blank_body = $("#blank_body").val();
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
		
		if (blank_subject == '' || blank_body == '') {
			$("#email_response").html('Please fill out subject and/or body before sending.');
			return;
		}
		
		
		if (blank_subject != '' && blank_body != '') {	
			//$("#email_response").html('Send the blank email.');
			//STEP 1 - call ajax email
			$.ajax({
				url:"ajax_send_gmail_blank.php",
				type: "POST",
				data: { currentid: cid,
						subject: blank_subject,
						body: blank_body,
						to_first: to_first,
						to_last: to_last,
						to_email: to_email,
						from_email: from_email },
				dataType: "html",		
				success:function(result){
					//alert(result);
					$("#email_response").html(result);
				},	//end success:function
				error:function(jqXHR, textStatus, errorThrown){
					$("#email_response").html(errorThrown);
				}
			}); //end $.ajax
			//*********************************************
			
			//STEP 2 - close modal and re-open edit modal
			//closeReopen(cid);
			
		}
		//otherwise, don't do anything.
		
	});		//close $('#emailer-container .sendlink_blank').click(function (e)
	
	//********************************************************
	// CLOSE EMAIL MODAL
	//********************************************************
	$('#emailer-container a.closeemailer').click(function (e) {
		e.preventDefault();
		
		//get link href - it holds the contacts id
		var linkValue = this.href;
		var lastslash = linkValue.lastIndexOf('/');
		var cid = linkValue.slice(lastslash+1);
		//alert(templatecat);
		
		//Close Text Modal
		//closeReopen(cid);
		
		$.modal.close();
		
	});		//close $('#emailer-container .closeemailer').click(function (e)
	
	
	
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
			//closeReopen(cid);
			$.modal.close();
			
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
				zIndex: 3000,
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
		
		//Close Text Modal
		//closeReopen(cid);	
		$.modal.close();
	});		
	
	//**************************************************************
	//  SET CONSULTANT BUTTON on ajax_lookup_consultant_modal.php
	//
	//  When consultant is selected.....show datepicker to see
	//  events for that consultant and allow rep to add an event
	//  to their calendar.
	//**************************************************************
	$('#modal-container a#set_consultant_button').click(function (e) {
		e.preventDefault();
		
		//Get the selected consultant's vfgid
		var thisConsultant = $('#selected_consultant :selected').val();
		var consultantName = $('#selected_consultant :selected').text();
		//alert(thisConsultant);
		//$('#ajax_add_consultant_event').html("");
		
		if (thisConsultant != ""){
			//display consultant selected
			$('#set_consultant_response').html('Selected Consultant:  '+consultantName);
			
			//Add appropriate text to the description box
			//$('#repadd_event_shortdesc').val('Appt Added By '+consultantName);
			
			//Set the assigned consultant textbox on the editcontact page
			$('.editcontactform #assigned_consultant').val(thisConsultant);
			
			//Set the assigned consultant span to consultant's name
			$('.editcontactform #assigned_con').html(consultantName);
		}
		
		
		if (thisConsultant != ""){
			//Clear the calendar events div, and the datepicker
			$('#consultants_schedule').html("");
			$('#consultantdatepicker').val("");
			
			//display consultant selected
			$('#set_consultant_response').html('Selected Consultant:  '+consultantName);
			
			//Set the label text for the datepicker
			$('#day_label').html('Pick A Day From '+consultantName+'\'s calendar:');
			
			//Set the assigned consultant textbox on the editcontact page
			$('.editcontactform #assigned_consultant').val(thisConsultant);
			
			//Set the assigned consultant span to consultant's name
			$('.editcontactform #assigned_con').html(consultantName);
			
			//AJAX to set session vars needed to lookup and add calendar events
			//AJAX POST
			$.ajax({
				url:"ajax_set_consultant_credentials.php",
				type: "POST",
				data: { repvfgid: thisConsultant },
				dataType: "html",		
				success:function(result){
					//display consultants timezone
					$("#set_consultant_timezone").html(consultantName+"'s Timezone:  "+"<b>"+result+"</b>");
				}	//end success:function
			}); //end $.ajax
			
			
		}
		
	});
	
	/* ****************************************************************** */
	/* CONSULTANT - ADD EVENT BUTTON on the Consultant Calendar Modal
	/* ****************************************************************** */
	$('#modal-container #consultant_addevent_button').click(function (e) {
		//e.preventDefault();
		
		// Consultants gmail credentials are in $_SESSION vars
				
		//var thisConsultantId = $('#selected_consultant :selected').val();
		//alert(thisConsultantId);
		
		//Event Title
		var eventTitle = $('.addevent #event_title').val();
		//Event Desc
		var eventDesc = $('.addevent #event_shortdesc').val();
		//Event Start HH
		var eventStarthh = $('.addevent #event_start_hh :selected').val();	
		//Event Start MM
		var eventStartmm = $('.addevent #event_start_mm :selected').val();
		//Event End HH
		var eventEndhh = $('.addevent #event_end_hh :selected').val();
		//Event End MM
		var eventEndmm = $('.addevent #event_end_mm :selected').val();
		
		//month, day, year
		var cMonth = $('#evt_m').val();
		var cDay = $('#evt_d').val();
		var cYear = $('#evt_y').val();
		
		
		//alert(eventTitle+' : '+eventDesc+' '+cMonth+' '+cDay+' '+cYear);
		
		//AJAX POST
		$.ajax({
			url:"ajax_consultant_calendar_addevent.php",
			type: "POST",
			data: { event_title: eventTitle, 
					event_desc: eventDesc,
					event_start_hh: eventStarthh,
					event_start_mm: eventStartmm,
					event_end_hh: eventEndhh,
					event_end_mm: eventEndmm,
					month: cMonth,
					day: cDay,
					year: cYear },
			dataType: "html",		
			success:function(result){
				$("#ajax_add_consultant_event").html(result);
			}	//end success:function
		}); //end $.ajax
		
		//AJAX - Reload the consultantevents div
		$.ajax({
			url:"ajax_rep_reload_consultantevents.php",
			type: "GET",
			data: {month: cMonth, day: cDay, year: cYear},
			dataType: "html",		
			success:function(result){
				$(".consultantevents").html(result);
			}	//end success:function
		}); //end $.ajax
		
	});
	
	/* ************************************************************************************** */
	/* REP - ADD EVENT TO MANAGERS'S CALENDAR:  BUTTON on the Assigned Manager Calendar Modal
	/* ************************************************************************************** */
	$('#modal-container #rep_add_event_to_managers_calendar').click(function (e) {
		//e.preventDefault();
		
		// Get the prospect's CID from the DOM tree
		var cid = $('#repadd_cid').val();
				
		var msgs = '';
		//Event Title
		var eventTitle = $('.addevent #repadd_event_title').val();
		if (eventTitle == ''){
			msgs = "Event title is blank.<br />";
		}
		//Event Desc
		var eventDesc = $('.addevent #repadd_event_shortdesc').val();
		if (eventDesc == ''){
			msgs += "Event description is blank.<br />";
		}
		//Event Start HH
		var eventStarthh = $('.addevent #repadd_event_start_hh :selected').val();
		if (eventStarthh == ''){
			msgs += "Select hours for start time.<br />";
		}	
		//Event Start MM
		var eventStartmm = $('.addevent #repadd_event_start_mm :selected').val();
		if (eventStartmm == ''){
			msgs += "Select minutes for start time.<br />";
		}
		//Event End HH
		var eventEndhh = $('.addevent #repadd_event_end_hh :selected').val();
		if (eventEndhh == ''){
			msgs += "Select hours for end time.<br />";
		}
		//Event End MM
		var eventEndmm = $('.addevent #repadd_event_end_mm :selected').val();
		if (eventEndmm == ''){
			msgs += "Select minutes for end time.<br />";
		}
		
		// Time Compare Vars
		var starttime = eventStarthh+eventStartmm;
		var endtime = eventEndhh+eventEndmm;
		
		if (starttime >= endtime){
			msgs += "End Time must be after start time.<br />";
		}
		
		//month, day, year
		var cMonth = $('#repadd_m').val();
		var cDay = $('#repadd_d').val();
		var cYear = $('#repadd_y').val();
		
		
		//alert(eventTitle+' : '+eventDesc+' '+cMonth+' '+cDay+' '+cYear);
		if (msgs == ''){
			
			// DKF 03/18/2014 change ajax program:
			// old: ajax_repadd_consultant_calendar_event.php
			// new: ajax_rep_add_appt_to_consultant_calendar.php
			//**************************************************
			// AJAX POST
			$.ajax({
				url:"ajax_rep_add_appt_to_managers_calendar.php",
				type: "POST",
				data: {	cid: cid,
				        event_title: eventTitle, 
						event_desc: eventDesc,
						event_start_hh: eventStarthh,
						event_start_mm: eventStartmm,
						event_end_hh: eventEndhh,
						event_end_mm: eventEndmm,
						month: cMonth,
						day: cDay,
						year: cYear },
				dataType: "html",		
				success:function(result){
					$("#ajax_rep_add_manager_event").html(result);
				},	//end success:function
				error:function(jqXHR, textStatus, errorThrown){
					$("#ajax_rep_add_manager_event").html(errorThrown);
				}
			}); //end $.ajax
			
			//AJAX - Reload the consultantevents div
			$.ajax({
				url:"ajax_rep_reload_managers_events.php",
				type: "GET",
				data: {month: cMonth, day: cDay, year: cYear},
				dataType: "html",		
				success:function(result){
					$("#consultants_schedule").html(result);
				}	//end success:function
			}); //end $.ajax
		
		} else {
			$("#ajax_rep_add_manager_event").html(msgs);
		}
		return false;
		
					
	});	//end
	
	
	/* ******************************************************************************** */
	/* REP - ADD EVENT TO CONSULTANT'S CALENDAR:  BUTTON on the Lookup Consultant Modal
	/* ******************************************************************************** */
	$('#modal-container #rep_add_event_to_consultant_calendar').click(function (e) {
		//e.preventDefault();
		
		// Get the prospect's CID from the DOM tree
		var cid = $('#repadd_cid').val();
				
		//var thisConsultantId = $('#selected_consultant :selected').val();
		//alert(thisConsultantId);
		var msgs = '';
		//Event Title
		var eventTitle = $('.addevent #repadd_event_title').val();
		if (eventTitle == ''){
			msgs = "Event title is blank.<br />";
		}
		//Event Desc
		var eventDesc = $('.addevent #repadd_event_shortdesc').val();
		if (eventDesc == ''){
			msgs += "Event description is blank.<br />";
		}
		//Event Start HH
		var eventStarthh = $('.addevent #repadd_event_start_hh :selected').val();
		if (eventStarthh == ''){
			msgs += "Select hours for start time.<br />";
		}	
		//Event Start MM
		var eventStartmm = $('.addevent #repadd_event_start_mm :selected').val();
		if (eventStartmm == ''){
			msgs += "Select minutes for start time.<br />";
		}
		//Event End HH
		var eventEndhh = $('.addevent #repadd_event_end_hh :selected').val();
		if (eventEndhh == ''){
			msgs += "Select hours for end time.<br />";
		}
		//Event End MM
		var eventEndmm = $('.addevent #repadd_event_end_mm :selected').val();
		if (eventEndmm == ''){
			msgs += "Select minutes for end time.<br />";
		}
		
		// Time Compare Vars
		var starttime = eventStarthh+eventStartmm;
		var endtime = eventEndhh+eventEndmm;
		
		if (starttime >= endtime){
			msgs += "End Time must be after start time.<br />";
		}
		
		//month, day, year
		var cMonth = $('#repadd_m').val();
		var cDay = $('#repadd_d').val();
		var cYear = $('#repadd_y').val();
		
		
		//alert(eventTitle+' : '+eventDesc+' '+cMonth+' '+cDay+' '+cYear);
		if (msgs == ''){
			
			// DKF 03/18/2014 change ajax program:
			// old: ajax_repadd_consultant_calendar_event.php
			// new: ajax_rep_add_appt_to_consultant_calendar.php
			//**************************************************
			// AJAX POST
			$.ajax({
				url:"ajax_rep_add_appt_to_consultant_calendar.php",
				type: "POST",
				data: {	cid: cid,
				        event_title: eventTitle, 
						event_desc: eventDesc,
						event_start_hh: eventStarthh,
						event_start_mm: eventStartmm,
						event_end_hh: eventEndhh,
						event_end_mm: eventEndmm,
						month: cMonth,
						day: cDay,
						year: cYear },
				dataType: "html",		
				success:function(result){
					$("#ajax_rep_add_consultant_event").html(result);
				},	//end success:function
				error:function(jqXHR, textStatus, errorThrown){
					$("#ajax_rep_add_consultant_event").html(errorThrown);
				}
			}); //end $.ajax
			
			//AJAX - Reload the consultantevents div
			$.ajax({
				url:"ajax_rep_reload_consultantevents.php",
				type: "GET",
				data: {month: cMonth, day: cDay, year: cYear},
				dataType: "html",		
				success:function(result){
					$("#consultants_schedule").html(result);
				}	//end success:function
			}); //end $.ajax
		
		} else {
			$("#ajax_rep_add_consultant_event").html(msgs);
		}
		return false;
		
					
	});	//end
	
}
//**************************************************************************
// *****************  CLOSE SHOW(DIALOG) FUNCTION ************************
//**************************************************************************


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
			zIndex: 3000,
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

	//reset the feedback div
	$("#update_messages").html("");
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
	var porc = $("#porc :selected").val();
	var ogPorc = $("#original_porc").val();
	var contactSource = $("#prospect_source :selected").val();
	var ogContactSource = $("#og_prospectsource").val();
	//var notes = $("#notes").val();
	//var ogNotes = $("#original_notes").val();
	var nextActionDate = $("#datepicker").val();
	var ogNextActionDate = $("#original_nextactiondate").val();
	var assignedMgr = $("#assigned_manager").val();
	var ogAssignedMgr = $("#original_assignedmanager").val();
	var assignedConsult = $("#assigned_consultant").val();
	var ogAssignedConsult = $("#original_assignedconsultant").val();
	//var ogTier2Call = $("#original_tier2call").val();
	//var ogInviterOnCall = $("#original_inviteroncall").val();
	var additionalRep = $("#additional_rep").val();
	var ogAdditionalRep = $("#original_additionalrep").val();
	//var tier2call = ogTier2Call;
	//var inviterOnCall = ogInviterOnCall;
	var disposition = $("#disposition :selected").val();
	var ogDisposition = $("#og_disposition").val();
	
	//7 & 30 Day Data
	var startDate = $("#dp_startdate").val();
	var ogStartDate = $("#og_start_date").val();
	var sevenDayDate = $("#seven_day_date").val();
	var ogSevenDayDate = $("#og_seven_day_date").val();
	var tier3Date = $("#dp_tier3_date").val();
	var ogTier3Date = $("#og_tier3_date").val();
	var crmDate = $("#dp_crm_date").val();
	var ogCrmDate = $("#og_crm_date").val();
	var tier4Date = $("#dp_tier4_date").val();
	var ogTier4Date = $("#og_tier4_date").val();
	var plan = $("#plan").val();
	var ogPlan = $("#og_plan").val();
	var persRef = $("#pers_ref").val();
	var ogPersRef = $("#og_pers_ref").val();
	var planPoints = $("#plan_points").val();
	var ogPlanPoints = $("#og_plan_points").val();
	var planDate = $("#dp_plandate").val();
	var ogPlanDate = $("#og_plan_date").val();
	var planPercent25 = $("#plan_percent25").val();
	var ogPlanPercent25 = $("#og_plan_percent25").val();
	var promoTo = $("#promo_to").val();
	var ogPromoTo = $("#og_promo_to").val();
	var thirtyDayDate = $("#thirty_day_date").val();
	var ogThirtyDayDate = $("#og_thirty_day_date").val();
	var recruit1 = $("#recruit1").val();
	var ogRecruit1 = $("#og_recruit1").val();
	var recruit1Points = $("#recruit1_points").val();
	var ogRecruit1Points = $("#og_recruit1_points").val();
	var recruit2 = $("#recruit2").val();
	var ogRecruit2 = $("#og_recruit2").val();
	var recruit2Points = $("#recruit2_points").val();
	var ogRecruit2Points = $("#og_recruit2_points").val();
	var recruitsPercent25 = $("#recruits_percent25").val();
	var ogRecruitsPercent25 = $("#og_recruits_percent25").val();
	var recruitsPromoTo = $("#recruits_promo_to").val();
	var ogRecruitsPromoTo = $("#og_recruits_promo_to").val();
	
	var ogPCphone = $("#og_pcphone").val();
	var ogPCtext = $("#og_pctext").val();
	var ogPCemail = $("#og_pcemail").val();
	
	// CHECK Preferred Contact CHECKBOXES
	var pc_phone = 'N';
	var pc_text = 'N';
	var pc_email = 'N';
	//Check box state
	if ( $('#pc_phone').prop("checked") ) {
		pc_phone = 'Y';
	}
	if ( $('#pc_text').prop("checked") ) {
		pc_text = 'Y';
	}
	if ( $('#pc_email').prop("checked") ) {
		pc_email = 'Y';
	}
	
	//CHECK TO SEE IF CHECKBOXES ARE THERE for manager
	/*if ( $('#tier2_call').prop("checked") ) {
		tier2call = 'Y';
	}
	if ( $('#inviter_on_call').prop("checked") ) {
		inviterOnCall = 'Y';
	}*/
	
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
	if (porc != ogPorc) {dataChanged = true;}
	//if (notes != ogNotes) {dataChanged = true;}
	if (nextActionDate != ogNextActionDate) {dataChanged = true;}
	if (assignedMgr.toLowerCase() != ogAssignedMgr.toLowerCase()) {dataChanged = true;}
	if (assignedConsult.toLowerCase() != ogAssignedConsult.toLowerCase()) {dataChanged = true;}
	//if (tier2call != ogTier2Call) {dataChanged = true;}
	//if (inviterOnCall != ogInviterOnCall) {dataChanged = true;}
	if (additionalRep != ogAdditionalRep) {dataChanged = true;}
	if (disposition != ogDisposition) {dataChanged = true;}
	if (contactSource != ogContactSource) {dataChanged = true;}
	
	if (startDate != ogStartDate) {dataChanged = true;}
	if (tier3Date != ogTier3Date) {dataChanged = true;}
	if (crmDate != ogCrmDate) {dataChanged = true;}
	if (tier4Date != ogTier4Date) {dataChanged = true;}
	if (plan != ogPlan) {dataChanged = true;}
	if (persRef != ogPersRef) {dataChanged = true;}
	if (planPoints != ogPlanPoints) {dataChanged = true;}
	if (planDate != ogPlanDate) {dataChanged = true;}
	if (planPercent25 != ogPlanPercent25) {dataChanged = true;}
	if (promoTo != ogPromoTo) {dataChanged = true;}
	if (recruit1 != ogRecruit1) {dataChanged = true;}
	if (recruit1Points != ogRecruit1Points) {dataChanged = true;}
	if (recruit2 != ogRecruit2) {dataChanged = true;}
	if (recruit2Points != ogRecruit2Points) {dataChanged = true;}
	if (recruitsPercent25 != ogRecruitsPercent25) {dataChanged = true;}
	if (recruitsPromoTo != ogRecruitsPromoTo) {dataChanged = true;}
	
	if (pc_phone != ogPCphone) {dataChanged = true;}
	if (pc_text != ogPCtext) {dataChanged = true;}
	if (pc_email != ogPCemail) {dataChanged = true;}
	//alert(dataChanged.toString());
	if (dataChanged) {
		//set hidden form var data_changed to true
		$("#data_changed").val('true');
	}
		
	var formdata = $('#edit_contact_record').serialize();
	
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
			$("#update_messages").html(result);
			$("#update_button").removeClass();
			$("#update_button").addClass("generalbuttongreen");
		},//end success:function
			error:function(jqXHR, textStatus, errorThrown){
				$("#update_messages").html(errorThrown);
		}
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
	$("#original_porc").val(porc);
	//$("#original_notes").val(notes);
	$("#original_nextactiondate").val(nextActionDate);
	$("#original_assignedmanager").val(assignedMgr);
	$("#original_assignedconsultant").val(assignedConsult);
	$("#original_additionalrep").val(additionalRep);
	$("#inviter").val(assignedConsult);
	$("#og_disposition").val(disposition);
	$("#og_prospectsource").val(contactSource);
	$("#og_pcphone").val(pc_phone);
	$("#og_pctext").val(pc_text);
	$("#og_pcemail").val(pc_email);
	
	$("#og_start_date").val(startDate);
	$("#og_seven_day_date").val(sevenDayDate);
	$("#og_tier3_date").val(tier3Date);
	$("#og_crm_date").val(crmDate);
	$("#og_tier4_date").val(tier4Date);
	$("#og_plan").val(plan);
	$("#og_pers_ref").val(persRef);
	$("#og_plan_points").val(planPoints);
	$("#og_plan_date").val(planDate);
	$("#og_plan_percent25").val(planPercent25);
	$("#og_promo_to").val(promoTo);
	$("#og_thirty_day_date").val(thirtyDayDate);
	$("#og_recruit1").val(recruit1);
	$("#og_recruit1_points").val(recruit1Points);
	$("#og_recruit2").val(recruit2);
	$("#og_recruit2_points").val(recruit2Points);
	$("#og_recruits_date").val(recruitsDate);
	$("#og_recruits_percent25").val(recruitsPercent25);
	$("#og_recruits_promo_to").val(recruitsPromoTo);
	
		
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
	var globaltemplates = ["1VFG Agent Intro",
							"2VFG Incomplete Intro",
							"3VFG RE Intro 1",
							"4VFG RE Intro 2",
							"5VFG Thanks for Your Interest"];
	
	var customoptionstring = "<option value=\"\">Select</option>\n<option value=\"blank\">Blank Email</option>";
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

function sendBlankEmail(cid){

	// HAS TO BE CALLED outside of show() when modal loads
	// after other templates are chosen because it isn't
	// registered after content on modal changes.
	
	var errors = '';
	// need the following off modal screen:
	// subject, body, mailtofirstname, mailtolastname, mailtoemail, mailtofromemail
	var blank_subject = $("#blank_subject").val();
	var blank_body = $("#blank_body").val();
	var to_first = $("#mailto_firstname").val();
	var to_last = $("#mailto_lastname").val();
	var to_email = $("#mailto_email").val();
	var from_email = $("#mailto_from").val();
	//alert(templateid+"\n"+to_first+"\n"+to_last+"\n"+to_email+"\n"+from_email+"\n");
	
	if (blank_subject == '' || blank_body == '') {
		$("#email_response").html('Please fill out subject and/or body before sending.');
		return;
	}
	
	
	if (blank_subject != '' && blank_body != '') {	
		//$("#email_response").html('Send the blank email.');
		//STEP 1 - call ajax email
		$.ajax({
			url:"ajax_send_gmail_blank.php",
			type: "POST",
			data: { currentid: cid,
					subject: blank_subject,
					body: blank_body,
					to_first: to_first,
					to_last: to_last,
					to_email: to_email,
					from_email: from_email },
			dataType: "html",		
			success:function(result){
				//alert(result);
				$("#email_response").html(result);
			},	//end success:function
			error:function(jqXHR, textStatus, errorThrown){
				$("#email_response").html(errorThrown);
			}
		}); //end $.ajax
		//*********************************************
		
		//STEP 2 - close modal and re-open edit modal
		//closeReopen(cid);
		
	}
	//otherwise, don't do anything.
	
	return false;

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
		
		// 01/17/2014 renaming the scripts
		// old name - new name
		// 1 Intro Script		-	Intro Call #1
		// 2 Call After Video	-	Intro Call #2
		// 3 2nd Call Attempt	-	Final Intro Call
		//
		switch (script_id) {
			case '1':
				// 01-17-2014 change initialcontactscript.php to introcall1.php
				$.ajax({
					url:"introcall1.php",
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
				// 01-17-2014 change callaftervideo.php to introcall2.php
				$.ajax({
					url:"introcall2.php",
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
			case '3':
				// 01-17-2014 change phoneattempt2.php to finalintrocall.php
				$.ajax({
					url:"finalintrocall.php",
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
			case '4':
				// 01-17-2014 leave noscript.php as is....
				$.ajax({
					url:"noscript.php",
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
	
	var c_phone = $('.editcontactform #phone').val();	//contact's phone
	
	$("#update_messages").html("");
	// need the following off modal screen: txt_template
	var template_name = $('#contact-container #txt_template').val();
	var txt_body = $('#contact-container #notes').val();
	
	if (txt_body != ''){
		//STEP 1 - call ajax to insert text history record and send text to dialer	
		$.ajax({
			url:"ajax_matrix_insert_text_history.php",
			type: "POST",
			data: { template_name: template_name, text_body: txt_body, cid: cid, phone: c_phone},
			dataType: "html",		
			success:function(result){
				//alert(result);
				$("#update_messages").html(result);
			}	//end success:function
		}); //end $.ajax
		//*********************************************
		
		//STEP 2 - close modal 
		$.modal.close();
	}
					
	return false;
}

function saveScriptToHistory(cid, script, calltype){
	//alert(cid+' : '+script+' : '+calltype);
	var contact_em = $(".editcontactform #email").val();
	var contact_firstname = $('.editcontactform #first_name').val();
	var contact_lastname = $('.editcontactform #last_name').val();
	var contact_phone = $('.editcontactform #phone').val();
	
	// 01-17-2014 intro, aftervideo, phoneattempt2 CHANGE TO intro1,intro2,finalcall
	//
	//Find out if the email or text trifecta boxes are checked....
	//do a switch on the script (intro1,intro2,finalcall) then calltype(vm, cv)
	var sendmail = 'N';
	var sendtext = 'N';
	switch(script) {
	case 'intro1':
		if (calltype == 'vm'){
			if ( $('#ic1_vm_trifecta_email').prop("checked") ) {
				sendmail = 'Y';
			}
			if ( $('#ic1_vm_trifecta_text').prop("checked") ) {
				sendtext = 'Y';
			}
		}
		if (calltype == 'cv'){
			if ( $('#ic1_cv_trifecta_email').prop("checked") ) {
				sendmail = 'Y';
			}
			if ( $('#ic1_cv_trifecta_text').prop("checked") ) {
				sendtext = 'Y';
			}
		} 
		break;
	case 'intro2':
	  if (calltype == 'vm'){
			if ( $('#ic2_vm_trifecta_email').prop("checked") ) {
				sendmail = 'Y';
			}
			if ( $('#ic2_vm_trifecta_text').prop("checked") ) {
				sendtext = 'Y';
			}
		}
		if (calltype == 'cv'){
			if ( $('#ic2_cv_trifecta_email').prop("checked") ) {
				sendmail = 'Y';
			}
			if ( $('#ic2_cv_trifecta_text').prop("checked") ) {
				sendtext = 'Y';
			}
		}
	  break;
	case 'finalcall':
		if (calltype == 'vm'){
			if ( $('#ic3_vm_trifecta_email').prop("checked") ) {
				sendmail = 'Y';
			}
			if ( $('#ic3_vm_trifecta_text').prop("checked") ) {
				sendtext = 'Y';
			}
		}
		if (calltype == 'cv'){
			if ( $('#ic3_cv_trifecta_email').prop("checked") ) {
				sendmail = 'Y';
			}
			if ( $('#ic3_cv_trifecta_text').prop("checked") ) {
				sendtext = 'Y';
			}
		}
		break;
	default:
	  sendmail = 'N';
	  sendtext = 'N';
	}
	
	//DEBUG
	//alert('Script :'+script+'\n'+'Sendmail flag: '+sendmail+'\n'+'Sendtext flag '+sendtext);
	
	
	//STEP 1 ajax call to insert VM history and send email/text
	//
	// 01-17-2014 change ajax_phone_script_trifecta.php to ajax_phonescript_trifecta_autoresponder.php
	$.ajax({
		url:"ajax_phonescript_trifecta_autoresponder.php",
		type: "POST",
		data: { cid: cid, 
				script: script, 
				calltype: calltype, 
				sendmail: sendmail, 
				sendtext: sendtext, 
				contact_em: contact_em,
				contact_fname: contact_firstname,
				contact_lname: contact_lastname,
				contact_phone: contact_phone },
		dataType: "html",		
		success:function(result){
			//alert(result);
			$("#update_messages").html(result);
		},	//end success:function
		error:function(jqXHR, textStatus, errorThrown){
			$("#update_messages").html(errorThrown);
		}
	}); //end $.ajax
	//*********************************************
	
	//STEP 2 - close modal 
	$.modal.close();
				
	return false;
	
}

function saveNoScriptPhoneNote(cid){
	var phoneNote = $('#noscript_form #phone_note').val();
	
	if (phoneNote != ''){
		$.ajax({
			url:"ajax_matrix_insert_phone_note.php",
			type: "POST",
			data: { cid: cid, phonenote: phoneNote },
			dataType: "html",		
			success:function(result){
				//alert(result);
				$("#ajax_save_note").html(result);
			}	//end success:function
		}); //end $.ajax
		//*********************************************
	} else {
		$("#ajax_save_note").html('Please provide notes before saving.');
	}
	
	return false;
}

//***************************************************
// SCHEDULE TIER 2 CALL LINK on phone script modals
//***************************************************
function goToTierCallScheduler(cid){
	
	$("#update_messages").html("");
	
	var from_phone = 'Y';
	var cfirst = $('.editcontactform #first_name').val();
	var clast = $('.editcontactform #last_name').val();
	var cTzone = $('.editcontactform #timezone :selected').val();
	var tier = '2';
	
	$('#tiercall_meeting').html('');
	
	//Close the current phone scripts modal
	$.modal.close();
	
	if (cTzone != ''){
		$.get("ajax_tiercall_modal.php", 
			{tier: tier, cid: cid, cfirst: cfirst, clast: clast, ctzone: cTzone, fromphone: from_phone }, function(data){
				// create a modal dialog with the data
				$(data).modal({
					closeHTML: "<a href='#' title='Close' class='modal-close'>close X</a>",
					position: ["5%",], //top position
					opacity: 70,
					overlayId: 'modal-overlay',
					containerId: 'modal-container',	//css for container is in modaldivs.css
					minHeight: 600,
					minWidth: 500,
					maxHeight: 600,
					maxWidth: 500,
					zIndex: 3000,
					onShow: show
				});
				// Have to register datepickers on content
				$("#datepickermeeting").datepicker({ dateFormat: "mm-dd-yy", minDate: 0 });
			});
	} else {
		$("#update_messages").html("Prospect's timezone not set. Please set it to schedule a tiercall meeting and set up a meeting notification");
	}
	
	return false;
}


function previewEmailTemplate(){
	var templateid = $("#template_list :selected").val();
	var cid = $("#mailer_currentid").val();
	var fname = $("#mailto_firstname").val();
	if ( $('#custom').prop("checked") ) {
		//alert('Preview Email Template. '+templateid);
		$.ajax({
			url:"ajax_preview_text_template.php",
			type: "GET",
			data: {tid: templateid, cid: cid, fname: fname },
			dataType: "html",		
			success:function(result){
				$(".contentright").html(result);
			}	//end success:function
		}); //end $.ajax
	}
	
}

function assignedLookup(repid, who){
	//alert(repid+' '+who);
	
	$.ajax({
		url:"ajax_lookup_manager_consultant.php",
		type: "POST",
		data: { repid: repid },
		dataType: "html",		
		success:function(result){
			//alert(result);
			if (who == 'mgr'){
				$("#assigned_mgr").html(result);
			}
			if (who == 'con'){
				$("#assigned_con").html(result);
			}
			if (who == 'adr'){
				$("#addl_rep").html(result);
			}	
		}	//end success:function
	}); //end $.ajax
	
}
function setStatus(val){
	$("#update_messages").html("");
	
	$('.editcontactform #set_status').val(val);
	var cfirst = $('.editcontactform #first_name').val();
	var clast = $('.editcontactform #last_name').val();
	var cTzone = $('.editcontactform #timezone').val();
	var tier =	$('.editcontactform #set_tier').val();
	var status = $('.editcontactform #set_status').val();
	//Contact's Data
	var cid =	$('.editcontactform #cid').val();
	var tierstatus = tier+status;
	
	$('#tiercall_meeting').html('');
	
	switch(tierstatus) {
		case '2A':
		case '3A':
		case '4A':
			if (cTzone != ''){
				$.get("ajax_tiercall_modal.php", 
					{tier: tier, cid: cid, cfirst: cfirst, clast: clast, ctzone: cTzone }, function(data){
						// create a modal dialog with the data
						$(data).modal({
							closeHTML: "<a href='#' title='Close' class='modal-close'>close X</a>",
							position: ["5%",], //top position
							opacity: 70,
							overlayId: 'modal-overlay',
							containerId: 'modal-container',	//css for container is in modaldivs.css
							minHeight: 800,
							minWidth: 500,
							maxHeight: 800,
							maxWidth: 500,
							zIndex: 3000,
							onShow: show
						});
						// Have to register datepickers on content
						$("#datepickermeeting").datepicker({ dateFormat: "mm-dd-yy", minDate: 0 });
					});
			} else {
				$("#update_messages").html("Prospect's timezone not set. Please set it to schedule a tiercall meeting and set up a meeting notification");
			}
			break;
	}
	
}
function addTierMeeting() {

	//Contact's Data
	var cid = $('.editcontactform #cid').val();
	var cFirst = $('.editcontactform #first_name').val();
	var cLast =	$('.editcontactform #last_name').val();
	var cEmail = $('.editcontactform #email').val();
	var cPhone = $('.editcontactform #phone').val();
	var cTzone = $('.editcontactform #timezone :selected').val();
	var tier = $('.editcontactform #set_tier').val();
	var fromphone = $('#from_phonescript').val();
	
	//dkf 07/01/2014
	//Inviter - Manager - Consultant ID's
	//Inviter is the rep_id : Man and Con are VFGID
	var inviter = $('.editcontactform #inviter').val();
	var manager = $('.editcontactform #assigned_manager').val();
	//var consultant = $('#selected_consultant :selected').val();
	var consultant = $('.editcontactform #assigned_consultant').val();
	//Meeting info
	var mDate =	$('#datepickermeeting').val();
	var mHour =	$('#meeting_time_hh').val();
	var mMinute = $('#meeting_time_mm').val();
	var interval = $('#notification_interval').val();
	var sendEmail = $("#meeting_send_email :selected").val();
	var sendText = $("#meeting_send_text :selected").val();
	
	//alert('Add Tier '+tier+' Meeting\n'+cid+"\n"+cFirst+"\n"+cLast+"\n"+cEmail+"\n"+cPhone+"\n"+mDate);
	
	//AJAX - add the tier meeting
	$.ajax({
		url:"ajax_insertupdate_tiercall_meeting.php",
		type: "POST",
		data: { cid: cid,
				cfirst: cFirst,
				clast: cLast,
				cemail: cEmail, 
				cphone: cPhone,
				ctimezone: cTzone,
				tier: tier,
				inviter: inviter,
				manager: manager,
				consultant: consultant,
				mdate: mDate,
				mhour: mHour,
				mminute: mMinute,
				interval: interval,
				sendemail: sendEmail,
				sendtext: sendText,
				fromphone: fromphone },
		dataType: "html",		
		success:function(result){
			//alert(result);
			$("#tier_messages").html(result);
			$("#datepicker").val(mDate);
		}	//end success:function
	}); //end $.ajax
	//*********************************************
		
	return false;
}
function deleteTierMeeting(cid) {

	//AJAX - add the tier meeting
	$.ajax({
		url:"ajax_delete_tiercall_meeting.php",
		type: "POST",
		data: { cid: cid },
		dataType: "html",		
		success:function(result){
			$("#tier_messages").html(result);
		}	//end success:function
	}); //end $.ajax
	//*********************************************
		
	return false;
}

function getManagersEvents(dt){

	//$('#ajax_add_consultant_event').html("");
	// I need the consultant's vfgid and date to get the
	// calendar events for them
	var mgr_vfgid = $('.editcontactform #assigned_manager').val();		//assigned manager's vfgid
	
	// Split apart the date (mm-dd-yyyy) and 
	// assign to the hidden fields for the add event routine
	var splitDate = dt.split("-");	
	$("#repadd_m").val(splitDate[0]);
	$("#repadd_d").val(splitDate[1]);
	$("#repadd_y").val(splitDate[2]);
	
	//AJAX POST
	$.ajax({
		url:"ajax_managers_events.php",
		type: "POST",
		data: { mgr_vfgid: mgr_vfgid, eventdate: dt },
		dataType: "html",		
		success:function(result){
			$("#consultants_schedule").html(result);
		}	//end success:function
	}); //end $.ajax
	
	return false;
}

function getConsultantEvents(dt){

	$('#ajax_add_consultant_event').html("");
	// I need the consultant's vfgid and date to get the
	// calendar events for them
	
	var thisConsultantId = $('#selected_consultant :selected').val();
	//alert(dt+'   '+thisConsultantId);
	
	// Split apart the date (mm-dd-yyyy) and 
	// assign to the hidden fields for the add event routine
	var splitDate = dt.split("-");	
	$("#repadd_m").val(splitDate[0]);
	$("#repadd_d").val(splitDate[1]);
	$("#repadd_y").val(splitDate[2]);
	
	//AJAX POST
	$.ajax({
		url:"ajax_consultants_events.php",
		type: "POST",
		data: { repvfgid: thisConsultantId, eventdate: dt },
		dataType: "html",		
		success:function(result){
			$("#consultants_schedule").html(result);
		}	//end success:function
	}); //end $.ajax
	
	return false;
}
