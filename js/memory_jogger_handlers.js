$(document).ready(function() {
	
	//SELECT LIST dropdown on memjog.php
	//
	$( "#mem_category" ).change(function() {
		var thiscat = $("#mem_category :selected").val()
	  
		if (thiscat != ''){
			$.ajax({
				url:"ajax_retrieve_leadlist.php",
				type: "POST",
				data: { category: thiscat },
				dataType: "html",		
				success:function(result){
					//alert(result);
					$("#ajax_memjog_list").html(result);
				}	//end success:function
			}); //end $.ajax
			//*********************************************
		} else {
			$("#ajax_memjog_list").html('Please select a category.');
		}
	
	});
	
	//**************************************
	//		ADD NEW LEAD BUTTON
	//**************************************
	$('#add_new_lead').click(function (e) {
		e.preventDefault();

		var lead_cat = $("#new_lead_cat").val();
		
		$.get("ajax_addnewlead_modal.php", { lead_cat: lead_cat }, function(data){
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
	
	
});
//**************************************************************************		
//			END DOCUMENT READY
//**************************************************************************

//Open function callback
function open(dialog) {
	
}

//**************************************************************************
//          LINKS ON THE DISPLAYED MODALS
//**************************************************************************
function show(dialog) {
	
	//ADD LEAD BUTTON
	$('#add_lead').click(function (e) {
		e.preventDefault();
		
		//Get fields off modal
		var leadcat = $("#lead_category :selected").val();
		var first = $("#first_name").val();
		var last = $("#last_name").val();
		var phone = $("#phone").val();
		var email = $("#email").val();
		var priority = $("#lead_priority :selected").val();
		var leadnotes = $("#lead_notes").val();
		
		$.ajax({
			url:"ajax_add_new_lead.php",
			type: "POST",
			data: { category: leadcat,
					first_name: first,
					last_name: last,
					phone: phone,
					email: email,
					lead_priority: priority,
					lead_notes: leadnotes},
			dataType: "html",		
			success:function(result){
				//alert(result);
				$(".leadcontentright").html(result);
			},	//end success:function
			error:function(jqXHR, textStatus, errorThrown){
				$(".leadcontentright").html(errorThrown);
			}
		}); //end $.ajax
		
	});		//END ADD_LEAD BUTTON
	//*********************************************************
	
	
	//EDIT Lead BUTTON
	$('#edit_lead').click(function (e) {
		e.preventDefault();
		
		var dataChanged = false;
		
		//Get fields off modal
		var lid = $("#lid").val();
		var leadcat = $("#lead_category :selected").val();
		var first = $("#first_name").val();
		var last = $("#last_name").val();
		var phone = $("#phone").val();
		var email = $("#email").val();
		var priority = $("#lead_priority :selected").val();
		var leadnotes = $("#lead_notes").val();
				
		//Original Values
		var ogleadcat = $("#og_category").val();
		var ogfirst = $("#og_firstname").val();
		var oglast = $("#og_lastname").val();
		var ogphone = $("#og_phone").val();
		var ogemail = $("#og_email").val();
		var ogpriority = $("#og_priority").val();
		var ogleadnotes = $("#og_notes").val();
		
					  
		//Check for data changes
		if (leadcat != ogleadcat) {dataChanged = true;}
		if (first.toLowerCase() != ogfirst.toLowerCase()) {dataChanged = true;}
		if (last.toLowerCase() != oglast.toLowerCase()) {dataChanged = true;}
		if (email != ogemail) {dataChanged = true;}
		if (phone != ogphone) {dataChanged = true;}
		if (priority != ogpriority) {dataChanged = true;}
		if (leadnotes != ogleadnotes) {dataChanged = true;}
		
		if (dataChanged) {
			//set hidden form var data_changed to true
			$("#data_changed").val('true');
		}	
		
		var datachange = $("#data_changed").val();
		
		$.ajax({
			url:"ajax_update_lead.php",
			type: "POST",
			data: { lid: lid,
					category: leadcat,
					first_name: first,
					last_name: last,
					phone: phone,
					email: email,
					lead_priority: priority,
					lead_notes: leadnotes,
					ogleadcat: ogleadcat,
					ogfirst: ogfirst,
					oglast: oglast,
					ogphone: ogphone,
					ogemail: ogemail,
					ogpriority: ogpriority,
					ogleadnotes: ogleadnotes,
					data_changed: datachange},
			dataType: "html",		
			success:function(result){
				//alert(result);
				$(".leadcontentright").html(result);
			},	//end success:function
			error:function(jqXHR, textStatus, errorThrown){
				$(".leadcontentright").html(errorThrown);
			}
		}); //end $.ajax
		
		//Have to reset the original values equal to what
		//was just submitted to start fresh for any
		//subsequent update!
		//
		//SET og hiddens to the extracted js variables from form fields
		//after ajax runs
		$("#og_category").val(leadcat);
		$("#og_firstname").val(first);
		$("#og_lastname").val(last);
		$("#og_phone").val(phone);
		$("#og_email").val(email);
		$("#og_priority").val(priority);
		$("#og_notes").val(leadnotes);
		
		//reset the data_changed hidden flag
		$("#data_changed").val('false');
		
	});		//END EDIT_LEAD BUTTON
	
	//**************************************
	//		'MOVE TO PROSPECTS' BUTTON
	//**************************************
	$('#make_prospect').click(function (e) {
		e.preventDefault();
		
		$(".leadcontentright").html("");
		
		//Get the OG values to send over to contacts
		//table
		//Original Values
		var lid = $("#lid").val();
		var ogleadcat = $("#og_category").val();
		var ogfirst = $("#og_firstname").val();
		var oglast = $("#og_lastname").val();
		var ogphone = $("#og_phone").val();
		var ogemail = $("#og_email").val();
		var ogpriority = $("#og_priority").val();
		var ogleadnotes = $("#og_notes").val();
		
		$.ajax({
			url:"ajax_convert_lead_to_prospect.php",
			type: "POST",
			data: { lid: lid,
					category: ogleadcat,
					first_name: ogfirst,
					last_name: oglast,
					phone: ogphone,
					email: ogemail,
					lead_notes: ogleadnotes},
			dataType: "html",		
			success:function(result){
				//alert(result);
				$(".leadcontentright").html(result);
			},	//end success:function
			error:function(jqXHR, textStatus, errorThrown){
				$(".leadcontentright").html(errorThrown);
			}
		}); //end $.ajax
		
		
	});		//END MAKE PROSPECT BUTTON
}
//**************************************************************************
// *****************  CLOSE SHOW(DIALOG) FUNCTION ************************
//**************************************************************************


function close(dialog) {
	$.modal.close();
}


function error(xhr) {
	alert(xhr.statusText);
}
function getLeads(category){
	alert(category);
}
function editLead(leadid){
	//alert(leadid);

	$.get("ajax_editlead_modal.php", { lid: leadid }, function(data){
		// create a modal dialog with the data
		$(data).modal({
			closeHTML: "<a href='#' title='Close' class='modal-close'>close X</a>",
			position: ["5%",], //top position
			opacity: 70,
			overlayId: 'contact-overlay',
			containerId: 'contact-container',
			minHeight: 650,
			minWidth: 950,
			maxHeight: 650,
			maxWidth: 950,
			zIndex: 3000,
			/*onOpen: open,*/
			onShow: show,
			onClose: close
		});
	});


}

