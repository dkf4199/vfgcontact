/*
 * SimpleModal Contact Form
 * http://simplemodal.com
 *
 * Copyright (c) 2013 Eric Martin - http://ericmmartin.com
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * DOCUMENTATION
 
 OPTIONS
		The following is a list of current options. Default values are indicated with: [Type:Value]

		* appendTo [String:'body']
		The jQuery selector to append the elements to. For ASP.NET, use 'form'.

		* focus [Boolean:true] (Changed in 1.4)
		Focus in the first visible, enabled element?

		* opacity [Number:50]
		The opacity value for the overlay div, from 0 - 100

		* overlayId [String:'simplemodal-overlay']
		The DOM element id for the overlay div

		* overlayCss [Object:{}]
		The CSS styling for the overlay div

		* containerId [String:'simplemodal-container']
		The DOM element id for the container div

		* containerCss [Object:{}]
		The CSS styling for the container div

		* dataId [String:'simplemodal-data']
		The DOM element id for the data div

		* dataCss [Object:{}]
		The CSS styling for the data div

		* minHeight [Number:null]
		The minimum height for the container

		* minWidth [Number:null]
		The minimum width for the container

		* maxHeight [Number:null]
		The maximum height for the container. If not specified, the window height is used.

		* maxWidth [Number:null]
		The maximum width for the container. If not specified, the window width is used.

		* autoResize [Boolean:false] (Changed in 1.4)
		Resize the container if it exceeds the browser window dimensions?

		* autoPosition [Boolean:true] (Changed in 1.4)
		Automatically position the container upon creation and on window resize?

		* zIndex [Number: 1000]
		Starting z-index value

		* close [Boolean:true]
		If true, closeHTML, escClose and overlayClose will be used if set. If false, none of them will be used.

		* closeHTML [String:'']
		The HTML for the default close link. SimpleModal will automatically add the closeClass to this element.

		* closeClass [String:'simplemodal-close']
		The CSS class used to bind to the close event

		* escClose [Boolean:true]
		Allow Esc keypress to close the dialog?

		* overlayClose [Boolean:false]
		Allow click on overlay to close the dialog?

		* position [Array:null]
		Position of container [top, left]. Can be number of pixels or percentage

		* persist [Boolean:false]
		Persist the data across modal calls? Only used for existing DOM elements. 
		If true, the data will be maintained across modal calls.
		If false, the data will be reverted to its original state.

		* modal [Boolean:true] (Added in 1.3.4. Name changed from transient in 1.3.5))
		User will be unable to interact with the page below the modal or 
		tab away from the dialog. If false, the overlay, iframe, and certain 
		events will be disabled allowing the user to interact 
		with the page below the dialog.

		* onOpen [Function:null]
		The callback function used in place of SimpleModal's open

		* onShow [Function:null]
		The callback function used after the modal dialog has opened

		* onClose [Function:null]
		The callback function used in place of SimpleModal's close
 */

jQuery(function ($) {
	//***********************************************************************
	//RESTORE CONTACT
	//***********************************************************************
	var restorecontact = {
		message: null,
		init: function () {
			//click function covers the ".contact" class for input and a elements
			//inside the id element "#contact-form" 
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
						overlayId: 'restorecontact-overlay',
						containerId: 'restorecontact-container',
						onOpen: restorecontact.open,
						onShow: restorecontact.show,
						onClose: restorecontact.close
					});
					// dkf 08-14-2013 have to register datepicker on content
					//$("#datepicker").datepicker({ dateFormat: "mm-dd-yy", minDate: 0 });
				});
			});
		},
		open: function (dialog) {
			// dynamically determine height
			var h = 550;
			if ($('#restorecontact-subject').length) {
				h += 26;
			}
			if ($('#restorecontact-cc').length) {
				h += 22;
			}

			var title = $('#restorecontact-container .restorecontact-title').html();
			$('#restorecontact-container .restorecontact-title').html('Loading...');
			dialog.overlay.fadeIn(200, function () {
				dialog.container.fadeIn(200, function () {
					dialog.data.fadeIn(200, function () {
						$('#restorecontact-container .restorecontact-content').animate({
							height: h
						}, function () {
							$('#restorecontact-container .restorecontact-title').html(title);
							$('#restorecontact-container form').fadeIn(200, function () {
								$('#restorecontact-container #restorecontact-name').focus();

								$('#restorecontact-container .restorecontact-cc').click(function () {
									var cc = $('#restorecontact-container #restorecontact-cc');
									cc.is(':checked') ? cc.attr('checked', '') : cc.attr('checked', 'checked');
								});
							});
						});
					});
				});
			});
		},
		//SEND OR UPDATE BUTTON CLICK - Turned Off, no button
		show: function (dialog) {
			$('#restorecontact-container .restorecontact-send').click(function (e) {
				e.preventDefault();
				// validate form on click of button
				if (contact.validate()) {
					var msg = $('#restorecontact-container .restorecontact-message');
					msg.fadeOut(function () {
						msg.removeClass('restorecontact-error').empty();
					});
					$('#restorecontact-container .restorecontact-title').html('Sending...');
					$('#restorecontact-container form').fadeOut(200);
					$('#restorecontact-container .restorecontact-content').animate({
						height: '80px'
					}, function () {
						$('#restorecontact-container .restorecontact-loading').fadeIn(200, function () {
							$.ajax({
								url: 'contact.php',
								data: $('#restorecontact-container form').serialize() + '&action=send',
								type: 'post',
								cache: false,
								dataType: 'html',
								success: function (data) {
									$('#restorecontact-container .restorecontact-loading').fadeOut(200, function () {
										$('#restorecontact-container .restorecontact-title').html('Thank you!');
										msg.html(data).fadeIn(200);
									});
								},
								error: dumpcontact.error
							});
						});
					});
				}
				else {
					if ($('#restorecontact-container .restorecontact-message:visible').length > 0) {
						var msg = $('#restorecontact-container .restorecontact-message div');
						msg.fadeOut(200, function () {
							msg.empty();
							restorecontact.showError();
							msg.fadeIn(200);
						});
					}
					else {
						$('#restorecontact-container .restorecontact-message').animate({
							height: '30px'
						}, restorecontact.showError);
					}
					
				}
			});
		},
		close: function (dialog) {
			$('#restorecontact-container .restorecontact-message').fadeOut();
			$('#restorecontact-container .restorecontact-title').html('Goodbye...');
			$('#restorecontact-container form').fadeOut(200);
			$('#restorecontact-container .restorecontact-content').animate({
				height: 40
			}, function () {
				dialog.data.fadeOut(200, function () {
					dialog.container.fadeOut(200, function () {
						dialog.overlay.fadeOut(200, function () {
							$.modal.close();
						});
					});
				});
			});
		},
		error: function (xhr) {
			alert(xhr.statusText);
		},
		validate: function () {
			restorecontact.message = '';
			if (!$('#restorecontact-container #restorecontact-name').val()) {
				restorecontact.message += 'Name is required. ';
			}

			var email = $('#restorecontact-container #contact-email').val();
			if (!email) {
				restorecontact.message += 'Email is required. ';
			}
			else {
				if (!restorecontact.validateEmail(email)) {
					restorecontact.message += 'Email is invalid. ';
				}
			}

			if (!$('#restorecontact-container #restorecontact-message').val()) {
				restorecontact.message += 'Message is required.';
			}

			if (restorecontact.message.length > 0) {
				return false;
			}
			else {
				return true;
			}
		},
		validateEmail: function (email) {
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
		},
		showError: function () {
			$('#restorecontact-container .restorecontact-message')
				.html($('<div class="restorecontact-error"></div>').append(restorecontact.message))
				.fadeIn(200);
		}
	};
	
	//***********************************************************************
	//FLUSH CONTACT
	//***********************************************************************
	var flushcontact = {
		message: null,
		init: function () {
			//click function covers the ".contact" class for input and a elements
			//inside the id element "#contact-form" 
			$('#flushcontact-form input.flushcontact, #flushcontact-form a.flushcontact').click(function (e) {
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
				
				$.get("ajax_getflushcontact_info_modal.php", { currentcontactid: cid }, function(data){
					// create a modal dialog with the data
					$(data).modal({
						closeHTML: "<a href='#' title='Close' class='modal-close'>close X</a>",
						position: ["5%",], //top position
						overlayId: 'flushcontact-overlay',
						containerId: 'flushcontact-container',
						onOpen: flushcontact.open,
						onShow: flushcontact.show,
						onClose: flushcontact.close
					});
					// dkf 08-14-2013 have to register datepicker on content
					//$("#datepicker").datepicker({ dateFormat: "mm-dd-yy", minDate: 0 });
				});
			});
		},
		open: function (dialog) {
			// dynamically determine height
			var h = 550;
			if ($('#flushcontact-subject').length) {
				h += 26;
			}
			if ($('#flushcontact-cc').length) {
				h += 22;
			}

			var title = $('#flushcontact-container .flushcontact-title').html();
			$('#flushcontact-container .flushcontact-title').html('Loading...');
			dialog.overlay.fadeIn(200, function () {
				dialog.container.fadeIn(200, function () {
					dialog.data.fadeIn(200, function () {
						$('#flushcontact-container .flushcontact-content').animate({
							height: h
						}, function () {
							$('#flushcontact-container .flushcontact-title').html(title);
							$('#flushcontact-container form').fadeIn(200, function () {
								$('#flushcontact-container #flushcontact-name').focus();

								$('#flushcontact-container .flushcontact-cc').click(function () {
									var cc = $('#flushcontact-container #flushcontact-cc');
									cc.is(':checked') ? cc.attr('checked', '') : cc.attr('checked', 'checked');
								});
							});
						});
					});
				});
			});
		},
		//SEND OR UPDATE BUTTON CLICK - Turned Off, no button
		show: function (dialog) {
			$('#flushcontact-container .flushcontact-send').click(function (e) {
				e.preventDefault();
				// validate form on click of button
				if (contact.validate()) {
					var msg = $('#flushcontact-container .flushcontact-message');
					msg.fadeOut(function () {
						msg.removeClass('flushcontact-error').empty();
					});
					$('#flushcontact-container .flushcontact-title').html('Sending...');
					$('#flushcontact-container form').fadeOut(200);
					$('#flushcontact-container .flushcontact-content').animate({
						height: '80px'
					}, function () {
						$('#flushcontact-container .flushcontact-loading').fadeIn(200, function () {
							$.ajax({
								url: 'contact.php',
								data: $('#flushcontact-container form').serialize() + '&action=send',
								type: 'post',
								cache: false,
								dataType: 'html',
								success: function (data) {
									$('#flushcontact-container .flushcontact-loading').fadeOut(200, function () {
										$('#flushcontact-container .flushcontact-title').html('Thank you!');
										msg.html(data).fadeIn(200);
									});
								},
								error: dumpcontact.error
							});
						});
					});
				}
				else {
					if ($('#flushcontact-container .flushcontact-message:visible').length > 0) {
						var msg = $('#flushcontact-container .flushcontact-message div');
						msg.fadeOut(200, function () {
							msg.empty();
							flushcontact.showError();
							msg.fadeIn(200);
						});
					}
					else {
						$('#flushcontact-container .flushcontact-message').animate({
							height: '30px'
						}, flushcontact.showError);
					}
					
				}
			});
		},
		close: function (dialog) {
			$('#flushcontact-container .flushcontact-message').fadeOut();
			$('#flushcontact-container .flushcontact-title').html('Goodbye...');
			$('#flushcontact-container form').fadeOut(200);
			$('#flushcontact-container .flushcontact-content').animate({
				height: 40
			}, function () {
				dialog.data.fadeOut(200, function () {
					dialog.container.fadeOut(200, function () {
						dialog.overlay.fadeOut(200, function () {
							$.modal.close();
						});
					});
				});
			});
		},
		error: function (xhr) {
			alert(xhr.statusText);
		},
		validate: function () {
			flushcontact.message = '';
			if (!$('#flushcontact-container #flushcontact-name').val()) {
				flushcontact.message += 'Name is required. ';
			}

			var email = $('#flushcontact-container #contact-email').val();
			if (!email) {
				flushcontact.message += 'Email is required. ';
			}
			else {
				if (!flushcontact.validateEmail(email)) {
					flushcontact.message += 'Email is invalid. ';
				}
			}

			if (!$('#flushcontact-container #flushcontact-message').val()) {
				flushcontact.message += 'Message is required.';
			}

			if (flushcontact.message.length > 0) {
				return false;
			}
			else {
				return true;
			}
		},
		validateEmail: function (email) {
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
		},
		showError: function () {
			$('#flushcontact-container .flushcontact-message')
				.html($('<div class="flushcontact-error"></div>').append(flushcontact.message))
				.fadeIn(200);
		}
	};

	//Initialize - Resister the prototype variables to activate them
	restorecontact.init();
	flushcontact.init();
});