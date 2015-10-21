function setList(val){
	//alert(val);
	var optionstring = "<option value=\"\">Select</option>\n";
	switch(val) {
		case '1':
		  for (var i in tier1){
			optionstring += "<option value=\"" + tier1[i].substr(0,1) + "\">" + tier1[i].substr(1) + "</option>" + "\n"; 
		  }
		  break;
		case '2':
		  for (var i in tier2){
			optionstring += "<option value=\"" + tier2[i].substr(0,1) + "\">" + tier2[i].substr(1) + "</option>" + "\n"; 
		  }
		  break;
		case '3':
		  for (var i in tier3){
			optionstring += "<option value=\"" + tier3[i].substr(0,1) + "\">" + tier3[i].substr(1) + "</option>" + "\n"; 
		  }
		  break;
		case '4':
		  for (var i in tier4){
			optionstring += "<option value=\"" + tier4[i].substr(0,1) + "\">" + tier4[i].substr(1) + "</option>" + "\n"; 
		  }
		  break;
		default:
		  optionstring = "<option value=\"no list\">No List</option>" + "\n";
	}
	$("select[name='tierstep']").find('option').remove().end().append($(optionstring));
	
	// Update the Tier setting used to determine if missed meeting email should be sent
	$('.editcontactform #set_tier').val(val);
}