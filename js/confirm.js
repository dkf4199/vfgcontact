/*
 * SimpleModal Confirm Modal Dialog
 * http://simplemodal.com
 *
 * Copyright (c) 2013 Eric Martin - http://ericmmartin.com
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 */

jQuery(function ($) {
	$('#confirm-dialog input.confirm, #confirm-dialog a.confirm').click(function (e) {
		e.preventDefault();
		
		$("#confirm_message").html("");
		// example of calling the confirm function
		// you must use a callback function to perform the "yes" action
		//confirm(dialog, callback)....
		confirm("Do you want to dump this contact?", function () {
			
			//this is where the dumpcontact ajax would go....
			//window.location.href = 'http://simplemodal.com';
			$("#confirm_message").html("Contact Dumped.");
		});
	});
});

function confirm(message, callback) {
	$('#confirm').modal({
		closeHTML: "<a href='#' title='Close' class='modal-close'>X</a>",
		position: ["20%",],
		overlayId: 'confirm-overlay',
		containerId: 'confirm-container', 
		onShow: function (dialog) {
			var modal = this;

			$('.message', dialog.data[0]).append(message);

			// if the user clicks "yes"
			$('.yes', dialog.data[0]).click(function () {
				// call the callback
				if ($.isFunction(callback)) {
					callback.apply();
					//$("#confirm_message").html("Confirmed.");
				}
				// close the dialog
				modal.close(); // or $.modal.close();
			});
		}
	});
}