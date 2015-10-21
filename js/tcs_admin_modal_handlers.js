$(document).ready(function() {
	
	//******************************
	// DUMP MODAL LINK
	//******************************
	$('#dumprep_div a.bluelink').click(function (e) {
		e.preventDefault();

		// .get(url, data, success)....in that order
		var linkValue = this.href;
		var lastslash = linkValue.lastIndexOf('/');
		var rid = linkValue.slice(lastslash+1);
		//alert(cid);
		
		$.get("ajax_dumprestoreflush_repinfo_modal.php", { rid: rid, action: 'dump' }, function(data){
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
	$('#restorerep_div a.bluelink').click(function (e) {
		e.preventDefault();

		// .get(url, data, success)....in that order
		var linkValue = this.href;
		var lastslash = linkValue.lastIndexOf('/');
		var rid = linkValue.slice(lastslash+1);
		//var cid = linkValue.slice(-7);
		//alert(cid);
		
		$.get("ajax_dumprestoreflush_repinfo_modal.php", { rid: rid, action: 'restore' }, function(data){
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
	$('#flushrep_div a.bluelink').click(function (e) {
		e.preventDefault();

		// .get(url, data, success)....in that order
		var linkValue = this.href;
		var lastslash = linkValue.lastIndexOf('/');
		var rid = linkValue.slice(lastslash+1);
		//alert(cid);
		
		$.get("ajax_dumprestoreflush_repinfo_modal.php", { rid: rid, action: 'flush' }, function(data){
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
	
	
	//***********************************************************
	// Rep LICENSING Lookup - Search Glass Icon
	// Display the States Model
	//***********************************************************
	$('.editcontactform a.rep_licensing_modal').click(function (e) {
		e.preventDefault();
		
		//Get needed values off the edit screen and pass to text modal
		var curr_rid = $('.editcontactform #rid').val();	//reps id
		var rep_firstname = $('.editcontactform #firstname').val();	//reps first name
		var rep_lastname = $('.editcontactform #lastname').val();	//reps last name
		
		//alert(rep_firstname+' '+rep_lastname);
		
		//get link href - it holds the contacts id
		var linkValue = this.href;
		var lastslash = linkValue.lastIndexOf('/');
		var rid = linkValue.slice(lastslash+1);
		
		//close current edit modal
		//$.modal.close();
		
		$.get("ajax_rep_licensing_states.php", { currentrid: curr_rid, repfirst: rep_firstname, replast: rep_lastname }, function(data){
			// create a modal dialog with the data
			$(data).modal({
				closeHTML: "<a href='#' title='Close' class='modal-close'>close X</a>",
				position: ["5%",], //top position
				opacity: 70,
				overlayId: 'contact-overlay',
				containerId: 'contact-container',	//css for container is in modaldivs.css
				minHeight: 800,
				minWidth: 1000,
				maxHeight: 800,
				maxWidth: 1000,
				zIndex: 3000,
				onShow: show
			});
		});
	});		//close $('.editcontactform a.rep_licensing_modal').click(function (e)
	
});		//END DOCUMENT READY
//*******************************************************************************************
//******************** END DOCUMENT READY (EDIT, DUMP, FLUSH, RESTORE LINKS *****************
//*******************************************************************************************

//Open function callback
function open(dialog) {
	
}
function resultShow(dialog) {
	
}
//***************************************************************************************************
//          LINKS ON THE DISPLAYED MODALS
//***************************************************************************************************
function show(dialog) {
	
}
//**********************************	
// CLOSE FUNCTION show
//**********************************

function close(dialog) {
	$.modal.close();
}

function closeReopen(cid){
	
	
}
function error(xhr) {
	alert(xhr.statusText);
}

function updateRep(){

	var dataChanged = false;
	//var ev_in = 'N';
	//var ev_mg = 'N';
	//var ev_cn = 'N';
	//var ev_sv = 'N';
	//var firstName = $("#first_name").val();
	//var ogFirstName = $("#og_firstname").val();
	//var lastName = $("#last_name").val();
	//var ogLastName = $("#og_lastname").val();
	//var gmail = $("#gmail_acct").val();
	//var ogGmail = $("#og_gmail").val();
	//var phone = $("#phone").val();
	//var ogPhone = $("#og_phone").val();
	//var recruiter_vfgid = $("#recruiter_vfgid").val();
	//var ogRecruiterVfgid = $("#og_recruiter_vfgid").val();
	//var timeZone = $("#timezone :selected").val();
	//var ogTimeZone = $("#og_timezone").val();
	var repLicensed = $("#rep_licensed :selected").val();
	var repPurchasedPolicy = $("#purchased_policy :selected").val();
	var repManager = $("#rep_manager :selected").val();
	var repConsultant = $("#rep_consultant :selected").val();
	//var ogEvIn = $("#og_ev_in").val();
	//var ogEvMg = $("#og_ev_mg").val();
	//var ogEvCn = $("#og_ev_cn").val();
	//var ogEvSv = $("#og_ev_sv").val();
	var ogRl = $("#og_rl").val();
	var ogPp = $("#og_pp").val();
	var ogRm = $("#og_rl_man").val();
	var ogRc = $("#og_rl_con").val();
	
	/*if ( $('#eventlevel_inviter').prop("checked") ) {
		ev_in = 'Y';
	}
	if ( $('#eventlevel_manager').prop("checked") ) {
		ev_mg = 'Y';
	}
	if ( $('#eventlevel_consultant').prop("checked") ) {
		ev_cn = 'Y';
	}
	if ( $('#eventlevel_svp').prop("checked") ) {
		ev_sv = 'Y';
	}
	*/
	
	//alert('dataChanged before comparisons : '+dataChanged.toString());
	//Serialize the form
	//compare original vals from php program 
	//to form vars on update click
	//NOTE: CONVERT to lowercase - JS is case sensitive!
	/*if (firstName.toLowerCase() != ogFirstName.toLowerCase()) {dataChanged = true;}
	if (lastName.toLowerCase() != ogLastName.toLowerCase()) {dataChanged = true;}
	if (gmail != ogGmail) {dataChanged = true;}
	if (phone != ogPhone) {dataChanged = true;}
	if (recruiter_vfgid != ogRecruiterVfgid) {dataChanged = true;}
	if (timeZone != ogTimeZone) {dataChanged = true;}
	if (ev_in != ogEvIn) {dataChanged = true;}
	if (ev_mg != ogEvMg) {dataChanged = true;}
	if (ev_cn != ogEvCn) {dataChanged = true;}
	if (ev_sv != ogEvSv) {dataChanged = true;}*/
	if (repLicensed != ogRl) {dataChanged = true;}
	if (repPurchasedPolicy != ogPp) {dataChanged = true;}
	if (repManager != ogRm) {dataChanged = true;}
	if (repConsultant != ogRc) {dataChanged = true;}
	
	
	/*alert('Inviter : '+ev_in+' '+ogEvIn+"\n"+
		  'Manager : '+ev_mg+' '+ogEvMg+"\n"+
		  'Consultant : '+ev_cn+' '+ogEvCn+"\n"+
		  'SVP : '+ev_sv+' '+ogEvSv+"\n");*/
	//alert('dataChanged after comparisons : '+dataChanged.toString());
	if (dataChanged) {
		//set hidden form var data_changed to true
		$("#data_changed").val('true');
	}
	
	
	var formdata = $('#edit_rep_record').serialize();
	
	//alert(tier+' '+tierStep);
	//call ajax_updatelead.php
	//the ajax_verify_update div is contained in the output
	//from the call to ajaxgetlead_info.php
	//
	$.ajax({
		url:"ajax_updaterep.php",
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
	/*$("#og_firstname").val(firstName);
	$("#og_lastname").val(lastName);
	$("#og_gmail").val(gmail);
	$("#og_phone").val(phone);
	$("#og_timezone").val(timeZone);
	$("#og_recruiter_vfgid").val(recruiter_vfgid);
	$("#og_ev_in").val(ev_in);
	$("#og_ev_mg").val(ev_mg);
	$("#og_ev_cn").val(ev_cn);
	$("#og_ev_sv").val(ev_sv);*/
	$("#og_rl").val(repLicensed);
	$("#og_pp").val(repPurchasedPolicy);
	$("og_rl_man").val(repManager);
	$("og_rl_con").val(repConsultant);
		
	//reset the data_changed hidden flag
	$("#data_changed").val('false');

	//alert("Update Form Button clicked.\n"+"First Name: "+firstName+"\n"+"Last Name: "+lastName+"\n");
	//alert("Update Form Button clicked.\n"+"Data Changed Flag: "+dataChanged);
	return false;
}
//function dumpContact
function dumpRep() {

	var formdata = $('#dumprep_form').serialize();
	//move the contact to the dump table
	//and out of the contacts table
	$.ajax({
			url:"ajax_dump_rep.php",
			type: "POST",
			data: formdata,
			dataType: "html",		
			success:function(result){
				$("#ajax_verify_repdump").html(result);
			}	//end success:function
	}); //end $.ajax
	return false;
}

function restoreRep() {
	var formdata = $('#restorerep_form').serialize();
	//move the contact to the dump table
	//and out of the contacts table
	$.ajax({
			url:"ajax_restore_rep.php",
			type: "POST",
			data: formdata,
			dataType: "html",		
			success:function(result){
				$("#ajax_verify_represtore").html(result);
			}	//end success:function
	}); //end $.ajax
	return false;
}
function flushRep() {
	var formdata = $('#flushrep_form').serialize();
	//move the contact to the dump table
	//and out of the contacts table
	$.ajax({
			url:"ajax_flush_rep.php",
			type: "POST",
			data: formdata,
			dataType: "html",		
			success:function(result){
				$("#ajax_verify_repflush").html(result);
			}	//end success:function
	}); //end $.ajax
	return false;
}
function updateRepStates(){

	//alert("updateRepStates");
	var formdata = $('#edit_rep_states').serialize();
	//alert(formdata);
	//call ajax_update_rep_states.php
	//the ajax_verify_update div is contained in the output
	//
	$.ajax({
		url:"ajax_update_rep_states.php",
		type: "POST",
		data: formdata,
		dataType: "html",		
		success:function(result){
			//alert(result);
			$("#ajax_verify_state_update").html(result);
		}	//end success:function
	}); //end $.ajax
	
	return false;
}


