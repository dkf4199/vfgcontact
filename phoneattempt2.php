<?php
session_start();
	
	$repid = $_SESSION['rep_id'];
		
	$cid = $_GET['contact_id'];
	$fname = $_GET['contact_firstname'];
	$lname = $_GET['contact_lastname'];
	$repfirst = $_GET['reps_firstname'];
	$replast = $_GET['reps_lastname'];
	$repphone = $_GET['reps_phone'];
	
	$scriptstr = '<p>
		<b>Voice Mail</b>&nbsp;&nbsp;&nbsp;
		<a href="#" class="savevm" 
			onClick="javascript: return saveScriptToHistory(\''.$cid.'\',\'phoneattempt2\',\'vm\');">Left VM. Save to History.</a>&nbsp;
		<input type="checkbox" name="pa_vm_trifecta_email" id="pa_vm_trifecta_email" value="Y" >Send Email&nbsp;
		<input type="checkbox" name="pa_vm_trifecta_text" id="pa_vm_trifecta_text" value="Y" >Send Text<br /><br />

		Hello '.$fname.'. This is '.$repfirst.' '.$replast.' with the Virtual Financial Group. Please give me a call at '.$repphone.' so that we
		can discuss the video that we spoke about.  Have you been able to watch it yet?  My website is.  Once again, that\'s '.$repfirst.' '.
		$replast.' with VFG at '.$repphone.'. I look forward to hearing from you soon.
		</p>

		<p>
		<b>Conversation</b>&nbsp;&nbsp;&nbsp;
		<a href="#" class="saveconvo" 
			onClick="javascript: return saveScriptToHistory(\''.$cid.'\',\'phoneattempt2\',\'cv\');">Had Conversation. Save to History.</a>&nbsp;
		<input type="checkbox" name="pa_cv_trifecta_email" id="pa_cv_trifecta_email" value="Y" >Send Email&nbsp;
		<input type="checkbox" name="pa_cv_trifecta_text" id="pa_cv_trifecta_text" value="Y" >Send Text<br /><br />

		Hello '.$fname.'. This is '.$repfirst.' '.$replast.' with the Virtual Financial Group.  Did you get a chance to watch the video
		that we spoke of?<br /><br />
		
		<b>NO.</b> Well, when would be a good time for you to watch it?  Ok, great.  Can we meet online at 4pm after you have had a chance to
		watch it?<br />
		<i>(Deal with the response and set an appointment to call.)</i><br /><br />

		<b>YES.</b> Great. What did you like most about it?<br />
		<i>(Listen closely to their reponse and take notes.  DO NOT interrupt them.  Deal with their responses positively and agree it is a special
		opportunity.)<br />
		(Discuss a few more points about video.  Great resources/tools, no meetings, no appointments, larget market, great compensation, etc)</i><br /><br />
		The next step in the process is for you to speak with a manager who will show you in more detail how are systems work and the great 
		resources & tools that the company makes available to you for you to succeed. You will need about 45 minutes to go 
		through everything and you will need to be in front of a computer with internet service. When would be a good time for the call?<br />
		<i>(Deal with the response accordingly and go to the Google calendar and set up the time with the manager that you are working with.)<br />
		(Let them know if you will be joining the call or not. Also explain to them that you have just completed the responsibilities required for a tier 1.)
		</i><br />
		Excuse yourself from the conversation accordingly.
		</p>
		<p>&nbsp;</p>';
	
	echo $scriptstr;
	
?>