$(document).ready(function() {
	
	//********************************************************************************************
	//				FULLCALENDAR FUNCTIONALITY
	//********************************************************************************************
	var date = new Date();
	var d = date.getDate();
	var m = date.getMonth();
	var y = date.getFullYear();

	var calendar = $('#calendar').fullCalendar({
		
		// determines if events can be dragged/resized
		editable: true,
		
		// defines buttons and title at top of calendar
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay'
		},
		
		// url the calendar will use to fetch events from single source
		//events: "fc_events.php",
		eventSources: [

			// your event source
			{
				url: 'fc_events.php' // use the `url` property
			},
			{
				url: 'fc_admin_events.php', // use the `url` property
				color: 'yellow',    // an option!
				textColor: 'black'  // an option!
			}

			// any other sources...

		],
		
		// Convert the allDay from string to boolean
		eventRender: function(event, element, view) {
			if (event.allDay === 'true') {
			  event.allDay = true;
			} else {
			  event.allDay = false;
			}
			 //TOOLTIP
			var start = $.fullCalendar.formatDate(event.start, "MM-dd-yyyy h:mm tt ");
			var end = $.fullCalendar.formatDate(event.end, "MM-dd-yyyy h:mm tt");
			
			element.attr('title',
							'START: '+start+"\n"+
							'    END: '+end+"\n"+
							'       '+"\n"+
							'EVENT: '+event.title+"\n"+
							'  DESC: '+event.description);
		},
		timeFormat: {
			month: 'h:mm{ - h:mm} ',      // month view
			week: 'h:mm{ - h:mm} ',       // basicWeek & agendaWeek views
			day: 'h:mm{ - h:mm} ',        // basicDay & agendaDay views

			agenda: 'h:mm{ - h:mm} ',    // agendaDay & agendaWeek views
			agendaDay: 'h:mm{ - h:mm} ',  // agendaDay view
			agendaWeek: 'h:mm{ - h:mm} ', // agendaWeek view

			basic: 'h:mm{ - h:mm} ',     // basicWeek & basicDay views
			basicWeek: 'h:mm{ - h:mm} ', // basicWeek view
			basicDay: 'h:mm{ - h:mm} ',  // basicDay view

			'': 'h(:mm)t '         // (an empty string) when no other properties match
		},
			
		//allows user to highlight multiple days or timeslots by clicking and dragging
		selectable: true,
		
		// Whether to draw a "placeholder" event while the user is dragging
		// ONLY APPLIES TO AGENDA VIEWS
		selectHelper: true,
		
		// A method for programmatically selecting a period of time.
		// THIS FIRES WHEN THE DAY IS CLICKED
		//
		select: function(start, end, allDay) {
			/*var title = prompt('Event Title:');
			var url = prompt('Type Event url, if exits:');
			if (title) {
				var start = $.fullCalendar.formatDate(start, "yyyy-MM-dd HH:mm:ss");
				var end = $.fullCalendar.formatDate(end, "yyyy-MM-dd HH:mm:ss");
				$.ajax({
					url: 'http://localhost/fullcalendar/add_event.php',
					data: 'title='+ title+'&start='+ start +'&end='+ end +'&url='+ url ,
					type: "POST",
					success: function(json) {
						alert('Added Successfully');
					}
				});
				calendar.fullCalendar('renderEvent',{
					title: title,
					start: start,
					end: end,
					allDay: allDay
				},
				true // make the event "stick"
				);
			}
			calendar.fullCalendar('unselect');
			*/
		},	//end select

		// Determines whether the events on the calendar can be modified
		editable: true,
		
		// Triggered when dragging stops and the event has moved to a different day/time
		eventDrop: function(event, delta) {
			var start = $.fullCalendar.formatDate(event.start, "yyyy-MM-dd HH:mm:ss");
			var end = $.fullCalendar.formatDate(event.end, "yyyy-MM-dd HH:mm:ss");
			$.ajax({
				url: 'fc_update_event.php',
				data: 'title='+ event.title+'&start='+ start +'&end='+ end +'&id='+ event.id ,
				type: "POST",
				success: function(json) {
					alert("Updated Successfully");
				}
			});
		
		},	//end eventDrop	
		
		// Triggered when resizing stops and the event has changed in duration
		eventResize: function(event) {
			var start = $.fullCalendar.formatDate(event.start, "yyyy-MM-dd HH:mm:ss");
			var end = $.fullCalendar.formatDate(event.end, "yyyy-MM-dd HH:mm:ss");
			$.ajax({
				url: 'fc_update_event.php',
				data: 'title='+ event.title+'&start='+ start +'&end='+ end +'&id='+ event.id ,
				type: "POST",
				success: function(json) {
					alert("Updated Successfully");
				}
			});

		}, //end eventResize:
		
		// Triggered when the user clicks on an event on calendar
		//
		eventClick: function(calEvent, jsEvent, view) {
			
			/*alert('Event: ' + calEvent.title+"\n"+
				  'Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY+"\n"+
				  'View: ' + view.name);
			*/
			var title = calEvent.title;
			var desc = calEvent.description;
			var evt_id = calEvent.id;
			var rep_id = calEvent.rep_id;
			var evt_start_hh = $.fullCalendar.formatDate( calEvent.start, "HH");
			var evt_start_mm = $.fullCalendar.formatDate( calEvent.start, "mm");
			var evt_end_hh = $.fullCalendar.formatDate( calEvent.end, "HH");
			var evt_end_mm = $.fullCalendar.formatDate( calEvent.end, "mm");
			var evt_start_dt = $.fullCalendar.formatDate( calEvent.start, "yyyy-MM-dd");
			var evt_end_dt = $.fullCalendar.formatDate( calEvent.end, "yyyy-MM-dd");
			var t_date = $.fullCalendar.formatDate( date, "yyyy-MM-dd");
			var t_mm = $.fullCalendar.formatDate( calEvent.start, "MM");
			var t_dd = $.fullCalendar.formatDate( calEvent.start, "dd");
			var t_yy = $.fullCalendar.formatDate( calEvent.start, "yyyy");
			
			if (rep_id != 'admin'){
				//alert(rep_id);
				// DKF 03/06/2014
				// Change this to ajax_fc_repevents_eventclick_modal.php 
				// from ajax_fc_editdelete_event_modal.php
				$.get("ajax_fc_repevents_eventclick_modal.php", { event_id: evt_id, 
															  title: title,
															  description: desc,
															  start_hh: evt_start_hh,
															  start_mm: evt_start_mm,
															  end_hh: evt_end_hh,
															  end_mm: evt_end_mm,
															  event_start: evt_start_dt, 
															  event_end: evt_end_dt,
															  month: t_mm,
															  day: t_dd,
															  year: t_yy}, function(data){
					// create a modal dialog with the data
					$(data).modal({
						closeHTML: "<a href='#' title='Close' class='modal-close'>close X</a>",
						position: ["5%",], //top position
						opacity: 70,
						overlayId: 'modal-overlay',
						containerId: 'modal-container',
						minHeight: 550,
						minWidth: 950,
						maxHeight: 550,
						maxWidth: 950,
						zIndex: 3000,
						/*onOpen: open,*/
						onShow: fc_show,
						onClose: fc_close
					});
					
				});
			
			} //end if
			
			
	
			// change the border color just for fun
			//$(this).css('border-color', 'red');
		
		}, //end eventClick
		
		// Triggered when the user clicks on a day.
		//
		dayClick: function(date, allDay, jsEvent, view) {

			var view = 'Current view: ' + view.name;
			var slot = 'Clicked on the slot: ' + date;
			var t_date = $.fullCalendar.formatDate( date, "yyyy-MM-dd");
			var t_mm = $.fullCalendar.formatDate( date, "MM");
			var t_dd = $.fullCalendar.formatDate( date, "dd");
			var t_yy = $.fullCalendar.formatDate( date, "yyyy");
			
			// DKF 3/4/2014 change php pgm call from ajax_fc_addevent_modal.php to ajax_fc_repevents_modal.php
			//
			$.get("ajax_fc_repevents_modal.php", 
				{ month: t_mm, day: t_dd, year: t_yy }, function(data){
				// create a modal dialog with the data
				$(data).modal({
					closeHTML: "<a href='#' title='Close' class='modal-close'>close X</a>",
					position: ["5%",], //top position
					opacity: 70,
					overlayId: 'modal-overlay',
					containerId: 'modal-container',
					minHeight: 550,
					minWidth: 950,
					maxHeight: 550,
					maxWidth: 950,
					zIndex: 3000,
					onShow: fc_show,
					onClose: fc_close
				});
				// dkf 08-14-2013 have to register datepickers on modal content load
				$("#recurring_event_end_dp").datepicker({ dateFormat: "mm-dd-yy", minDate: 0 });
				
			});
			
		} //end dayClick:
		

	});
	//*****************************************************************
	//				END FULLCALENDAR FUNCTIONALITY 					//
	//*****************************************************************
	
});		//END DOCUMENT READY
//*******************************************************************************************

//Open function callback
function open(dialog) {
	
}

//**********************************************************************************
//		BEGIN FULLCALENDAR show() close() MODAL DIALOG FUNCTIONS
//**********************************************************************************
function fc_show(dialog) {

	/* ****************************************************************** */
	/* REP ADDING EVENTS TO THEIR OWN CALENDAR							  */
	/* REP - ADD EVENT BUTTON on the Rep FullCalendar Modal
	/* ****************************************************************** */
	$('#modal-container #rep_addevent_button').click(function (e) {
		//e.preventDefault();
		
		// Consultants gmail credentials are in $_SESSION vars
				
		//var thisConsultantId = $('#selected_consultant :selected').val();
		//alert(thisConsultantId);
		var msgs = '';
		//Event Title
		var eventTitle = $('.addevent #event_title').val();
		//Event Desc
		var eventDesc = $('.addevent #event_desc').val();
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
		
		var starttime = eventStarthh+eventStartmm;
		var endtime = eventEndhh+eventEndmm;
	
		// RECURRING EVENTS section
		var recurring_until = $("#recurring_event_end_dp").val();			// datepicker MM-DD-YYYY
		var repeat_event = '0';
		var repeat_interval = 'none';
		//Check box for repeating event - it's a 0 or 1 value
		if ( $('#repeats_checkbox').prop("checked") ) {
			repeat_event = '1';
		}
		//alert(repeat_event);
		
		if (repeat_event == '1'){
			repeat_interval = $("input:radio[name=repeat_freq]:checked").val();
			//alert(repeat_interval);
		}
		
		// VALIDATION
		if (eventTitle == ''){
			msgs = "Event title is blank.<br />";
		}
		if (eventDesc == ''){
			msgs += "Event description is blank.<br />";
		}
		if (starttime >= endtime){
			msgs += "End Time must be after start time.";
		}
		if (repeat_event == '1' && repeat_interval == ''){
			msgs += "Select a repeating interval for event.";
		}
		if (repeat_event == '1' && recurring_until == ''){
			msgs += "Select the end date range for recurring event.";
		}
		//alert(eventTitle+' : '+eventDesc+' '+cMonth+' '+cDay+' '+cYear);
		
		//AJAX POST
		if (msgs == ''){
			$.ajax({
				url:"ajax_fc_rep_addevent.php",
				type: "POST",
				data: { event_title: eventTitle, 
						event_desc: eventDesc,
						event_start_hh: eventStarthh,
						event_start_mm: eventStartmm,
						event_end_hh: eventEndhh,
						event_end_mm: eventEndmm,
						month: cMonth,
						day: cDay,
						year: cYear,
						repeat: repeat_event,
						repeat_freq: repeat_interval,
						recurring_until: recurring_until },
				dataType: "html",		
				success:function(result){
					$("#ajax_add_consultant_event").html(result);
				}	//end success:function
			}); //end $.ajax
			
			//AJAX - Reload the consultantevents div
			$.ajax({
				url:"ajax_reload_repevents.php",
				type: "GET",
				data: {month: cMonth, day: cDay, year: cYear},
				dataType: "html",		
				success:function(result){
					$(".consultantevents").html(result);
				},	//end success:function
				error:function(jqXHR, textStatus, errorThrown){
					$(".consultantevents").html(errorThrown);
				}
			}); //end $.ajax
			
		} else {
			$("#ajax_add_consultant_event").html(msgs);
		}
		
	});
	
}

function fc_close(dialog) {

	$.modal.close();
	$('#calendar').fullCalendar( 'refetchEvents' );
	
}
//****************  END FULLCALENDAR show() close() FUNCTIONS ***************************************
//***************************************************************************************************
function error(xhr) {
	alert(xhr.statusText);
}

//***************************************************************************************************
//		FULLCALENDAR FUNCTION CALLS ON MAIN EVENTS MODAL
//***************************************************************************************************
function displayRepAddEvent(){
	
	//month, day, year
	var cMonth = $('#evt_m').val();
	var cDay = $('#evt_d').val();
	var cYear = $('#evt_y').val();
	
	//Display form to add event
	//AJAX POST - Delete script
	$.ajax({
		url:"ajax_fc_add_new_repevent_form.php",
		type: "GET",
		data: {month: cMonth, day: cDay, year: cYear},
		dataType: "html",		
		success:function(result){
			$(".addevent").html(result);
			$("#newlink_recurring_event_end_dp").datepicker({ dateFormat: "mm-dd-yy", minDate: 0 });
		}	//end success:function
		
	}); //end $.ajax
	
	
	return false;
}
function displayRepEventInfo(repid, ev_id){

	//Get the data and display it in the .addevent modal
	//AJAX - Reload the consultantevents div
	$.ajax({
		url:"ajax_display_repevent_for_edit.php",
		type: "GET",
		data: {repid: repid, event_id: ev_id },
		dataType: "html",		
		success:function(result){
			$(".addevent").html(result);
		}	//end success:function
	}); //end $.ajax
	
	return false;
}

function displayRepEventDeleteInfo(repid, ev_id){

	//Get the data and display it in the .addevent modal
	//AJAX - Reload the consultantevents div
	$.ajax({
		url:"ajax_display_repevent_for_delete.php",
		type: "GET",
		data: {repid: repid, event_id: ev_id },
		dataType: "html",		
		success:function(result){
			$(".addevent").html(result);
		}	//end success:function
	}); //end $.ajax
	
	return false;
}
function updateRepEvent() {
	var msgs = '';
	//Event Title
	var eventTitle = $('.addevent #edit_event_title').val();
	if (eventTitle == ''){
		msgs = "Event title is blank.<br />";
	}
	//Event Desc
	var eventDesc = $('.addevent #edit_event_desc').val();
	if (eventDesc == ''){
		msgs += "Event description is blank.<br />";
	}
	//Event Start HH
	var eventStarthh = $('.addevent #edit_event_start_hh :selected').val();
	if (eventStarthh == ''){
		msgs += "Select hours for start time.<br />";
	}	
	//Event Start MM
	var eventStartmm = $('.addevent #edit_event_start_mm :selected').val();
	if (eventStartmm == ''){
		msgs += "Select minutes for start time.<br />";
	}
	//Event End HH
	var eventEndhh = $('.addevent #edit_event_end_hh :selected').val();
	if (eventEndhh == ''){
		msgs += "Select hours for end time.<br />";
	}
	//Event End MM
	var eventEndmm = $('.addevent #edit_event_end_mm :selected').val();
	if (eventEndmm == ''){
		msgs += "Select minutes for end time.<br />";
	}
	//calendar entry id field
	var fcId = $('#editevent_fc_id').val();
	
	var starttime = eventStarthh+eventStartmm;
	var endtime = eventEndhh+eventEndmm;
	
	//month, day, year
	var cMonth = $('#evt_m').val();
	var cDay = $('#evt_d').val();
	var cYear = $('#evt_y').val();
	
	// VALIDATION
	if (eventTitle == ''){
		msgs = "Event title is blank.<br />";
	}
	if (eventDesc == ''){
		msgs += "Event description is blank.<br />";
	}
	if (starttime >= endtime){
		msgs += "End Time must be after start time.";
	}
	
	// AJAX CALL
	if (msgs == ''){
		//AJAX POST
		$.ajax({
			url:"ajax_fc_rep_editevent.php",
			type: "POST",
			data: { fcid: fcId,
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
				$("#ajax_edit_consultant_event").html(result);
			},	//end success:function
			error:function(jqXHR, textStatus, errorThrown){
				$("#ajax_edit_consultant_event").html(errorThrown);
			}
		}); //end $.ajax
		
		//AJAX - Reload the consultantevents div
		$.ajax({
			url:"ajax_reload_repevents.php",
			type: "GET",
			data: {month: cMonth, day: cDay, year: cYear},
			dataType: "html",		
			success:function(result){
				$(".consultantevents").html(result);
			}	//end success:function
		}); //end $.ajax
	
	} else {
		$("#ajax_edit_consultant_event").html(msgs);
	}
	
	return false;
}

function addNewRepEvent(){
	
	var msgs = '';
	//Event Title
	var eventTitle = $('.addevent #newlink_event_title').val();
	if (eventTitle == ''){
		msgs = "Event title is blank.<br />";
	}
	//Event Desc
	var eventDesc = $('.addevent #newlink_event_desc').val();
	if (eventDesc == ''){
		msgs += "Event description is blank.<br />";
	}
	//Event Start HH
	var eventStarthh = $('.addevent #newlink_event_start_hh :selected').val();
	if (eventStarthh == ''){
		msgs += "Select hours for start time.<br />";
	}	
	//Event Start MM
	var eventStartmm = $('.addevent #newlink_event_start_mm :selected').val();
	if (eventStartmm == ''){
		msgs += "Select minutes for start time.<br />";
	}
	//Event End HH
	var eventEndhh = $('.addevent #newlink_event_end_hh :selected').val();
	if (eventEndhh == ''){
		msgs += "Select hours for end time.<br />";
	}
	//Event End MM
	var eventEndmm = $('.addevent #newlink_event_end_mm :selected').val();
	if (eventEndmm == ''){
		msgs += "Select minutes for end time.<br />";
	}
	// RECURRING EVENTS section
	var recurring_until = $("#newlink_recurring_event_end_dp").val();			// datepicker MM-DD-YYYY
	var repeat_event = '0';
	var repeat_interval = 'none';
	//Check box for repeating event - it's a 0 or 1 value
	if ( $('#newlink_repeats_checkbox').prop("checked") ) {
		repeat_event = '1';
	}
	//alert(repeat_event);
	
	if (repeat_event == '1'){
		repeat_interval = $("input:radio[name=newlink_repeat_freq]:checked").val();
		//alert(repeat_interval);
	}
		
	var starttime = eventStarthh+eventStartmm;
	var endtime = eventEndhh+eventEndmm;
			
	//month, day, year
	var cMonth = $('#evt_m').val();
	var cDay = $('#evt_d').val();
	var cYear = $('#evt_y').val();
	
	// VALIDATION
	if (eventTitle == ''){
		msgs = "Event title is blank.<br />";
	}
	if (eventDesc == ''){
		msgs += "Event description is blank.<br />";
	}
	if (starttime >= endtime){
		msgs += "End Time must be after start time.";
	}
	if (repeat_event == '1' && repeat_interval == ''){
		msgs += "Select a repeating interval for event.";
	}
	if (repeat_event == '1' && recurring_until == ''){
		msgs += "Select the end date range for recurring event.";
	}
	
	// AJAX CALL
	if (msgs == ''){
		//AJAX POST
		$.ajax({
			url:"ajax_fc_rep_addevent.php",
			type: "POST",
			data: {	event_title: eventTitle, 
					event_desc: eventDesc,
					event_start_hh: eventStarthh,
					event_start_mm: eventStartmm,
					event_end_hh: eventEndhh,
					event_end_mm: eventEndmm,
					month: cMonth,
					day: cDay,
					year: cYear,
					repeat: repeat_event,
					repeat_freq: repeat_interval,
					recurring_until: recurring_until},
			dataType: "html",		
			success:function(result){
				$("#ajax_new_consultant_event").html(result);
			},	//end success:function
			error:function(jqXHR, textStatus, errorThrown){
				$("#ajax_new_consultant_event").html(errorThrown);
			}
		}); //end $.ajax
		
		//AJAX - Reload the consultantevents div
		$.ajax({
			url:"ajax_reload_repevents.php",
			type: "GET",
			data: {month: cMonth, day: cDay, year: cYear},
			dataType: "html",		
			success:function(result){
				$(".consultantevents").html(result);
			}	//end success:function
		}); //end $.ajax
	
	} else {
		$("#ajax_new_consultant_event").html(msgs);
	}
	return false;
}

function deleteRepEvent(){

	// calendar entry id field
	var fcId = $('#deleteevent_fc_id').val();
	// parent_id field and repeats
	var fcParentId = $('#deleteevent_fc_parent_id').val();	// 0 - no parent_id, or the parent_id
	var repeats = $('#deleteevent_fc_repeats').val();
	
	//month, day, year
	var cMonth = $('#evt_m').val();
	var cDay = $('#evt_d').val();
	var cYear = $('#evt_y').val();
	
	
	//AJAX POST - Delete script
	$.ajax({
		url:"ajax_fc_rep_deleteevent.php",
		type: "POST",
		data: { fcid: fcId,
				fcparent: fcParentId,
				month: cMonth,
				day: cDay,
				year: cYear },
		dataType: "html",		
		success:function(result){
			$("#ajax_delete_consultant_event").html(result);
		},	//end success:function
		error:function(jqXHR, textStatus, errorThrown){
			$("#ajax_delete_consultant_event").html(errorThrown);
		}
	}); //end $.ajax
	
	//AJAX - Reload the consultantevents div
	$.ajax({
		url:"ajax_reload_repevents.php",
		type: "GET",
		data: {month: cMonth, day: cDay, year: cYear},
		dataType: "html",		
		success:function(result){
			$(".consultantevents").html(result);
		}	//end success:function
	}); //end $.ajax
	

	return false;
}
function deleteRepRecurringEvents(){

	// calendar entry id field
	var fcId = $('#deleteevent_fc_id').val();
	// parent_id field and repeats
	var fcParentId = $('#deleteevent_fc_parent_id').val();	// 0 - no parent_id, or the parent_id
	var repeats = $('#deleteevent_fc_repeats').val();
	
	//month, day, year
	var cMonth = $('#evt_m').val();
	var cDay = $('#evt_d').val();
	var cYear = $('#evt_y').val();
	
	
	//AJAX POST - Delete script
	$.ajax({
		url:"ajax_fc_rep_delete_recurring_events.php",
		type: "POST",
		data: { fcid: fcId,
				fcparent: fcParentId,
				month: cMonth,
				day: cDay,
				year: cYear },
		dataType: "html",		
		success:function(result){
			$("#ajax_delete_consultant_event").html(result);
		},	//end success:function
		error:function(jqXHR, textStatus, errorThrown){
			$("#ajax_delete_consultant_event").html(errorThrown);
		}
	}); //end $.ajax
	
	//AJAX - Reload the consultantevents div
	$.ajax({
		url:"ajax_reload_repevents.php",
		type: "GET",
		data: {month: cMonth, day: cDay, year: cYear},
		dataType: "html",		
		success:function(result){
			$(".consultantevents").html(result);
		}	//end success:function
	}); //end $.ajax
	

	return false;
}
function toggleDiv(){
	//toggle given div
	$("#recurring_eventdiv").toggle();
	return true;
	
}