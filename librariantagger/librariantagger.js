var timers = new Array();
timers['call_number'] = 0;
var fields = new Array();
var num_ids = 0;

var good_color = "#0c0";
var warn_color = "#c50";
var error_color = "#c00";

/**
 * Function stub, reduces length of element references.
 */
var doc = function(ele) { return document.getElementById(ele); }
var multiHTML;

/**
 * function SetTimer()
 * Called when text changes in the input box.
 * Resets any information regarding success/failure
 * and sets/resets timeout before sending ajax request.
 */
function SetTimer(id)
{	
	if(timers[id] != 0) { clearTimeout(timers[id]); }

	doc('get_tags_button').setAttribute('disabled','disabled');

	doc(id).style.color = "#000";	
	doc(id + '_error').innerHTML = '';
	doc(id + '_tag').innerHTML = '';
	
	timers[id] = setTimeout("CheckNum('" + id + "')", 1000);
}

/**
 * function CheckNum()
 * When input timer expires, this function is called
 * to initiate the ajax request and handle the response
 * for the text field where a single LC num is entered.
 */
function CheckNum(id)
{	
	id_elem = doc(id);
	
	$.get("api/call_numbers/"+escape(id_elem.value)+".json", 
		function(ret) {
			CheckNumResponse(ret, id_elem);
		}, "json");
}

function FillOutParseResult(parse_result,ret){
	var header = '<tr><th>Class</th>';
	if(ret.parsed_call_number.date1 != ""){
		header += '<th>1st dt</th>'
	}
	if(ret.parsed_call_number.cutter1 != ""){
		header += '<th>1st cut</th>';
	}
	if(ret.parsed_call_number.date2 != ""){
		header += '<th>2nd dt</th>';
	}
	if(ret.parsed_call_number.cutter2 != ""){
		header += '<th>2nd cut</th>';
	}
	if(ret.parsed_call_number.element8meaning == 'year'){
		header += '<th>3rd dt</th>';
	}
	if((ret.parsed_call_number.element8 != "" && ret.parsed_call_number.element8meaning != 'year') ||
	   ret.parsed_call_number.element9 != "" || ret.parsed_call_number.element10 != ""){
		header += '<th>Unused</th>';
	}
	header += '</tr>';
	parse_result.innerHTML = header;

	var iH = '<tr>'
	+ '<td>' + ret.parsed_call_number.alphabetic + ret.parsed_call_number.wholeClass ;
	if(ret.parsed_call_number.decClass != ""){
 		iH = iH + '.' + ret.parsed_call_number.decClass ;
	}
	if(ret.parsed_call_number.date1 != ""){
		iH += '<td>' + ret.parsed_call_number.date1 + '</td>'
	}
	if(ret.parsed_call_number.cutter1 != ""){
		iH += "<td>" + ret.parsed_call_number.cutter1 + "</td>";
	}
	if(ret.parsed_call_number.date2 != ""){
		iH += "<td>" + ret.parsed_call_number.date2 + "</td>";
	}
	if(ret.parsed_call_number.cutter2 != ""){
		iH += "<td>" + ret.parsed_call_number.cutter2 + "</td>";
	}
	if(ret.parsed_call_number.element8meaning == 'year'){
		iH += "<td>" + ret.parsed_call_number.element8 + "</td>";
	}
	if((ret.parsed_call_number.element8 != "" && ret.parsed_call_number.element8meaning != 'year') ||
	   ret.parsed_call_number.element9 != "" || ret.parsed_call_number.element10 != ""){
		iH += "<td>";
		if(ret.parsed_call_number.element8meaning != 'year'){
			iH += ret.parsed_call_number.element8;
		}
		iH += (ret.parsed_call_number.element9 + ret.parsed_call_number.element10 + "</td>" + "</tr>");
	}
	parse_result.innerHTML += iH;
}

function CheckNumResponse(ret, id)
{	
	var parse_result = doc(id.id + '_presult');
	FillOutParseResult(parse_result,ret);

	var tag = doc(id.id + '_tag');
	tag.innerHTML = "";

	if(ret.result.search("SUCCESS") != -1) {
		id.style.color = good_color;
		tag.innerHTML += ret.book_tag;
	} else {
		id.style.color = error_color; 
	}
	//Want this to run no matter what, so it disables if needed
	EnableGetTagsButton();
	
	if(ret.parser_feedback.length != 0) {
	  if(ret.result.search("SUCCESS") != -1){id.style.color = warn_color;}
		doc(id.id + '_error').innerHTML = DisplayWarnings(ret.parser_feedback,ret.call_number);
	}
}

/**
 * function CheckNums()
 * When input timer expires, this function is called
 * to initiate the ajax request and handle the response
 * for the textarea where multiple LC nums are entered.
 */
function CheckNums()
{
	var nums = doc('LCnums').value;
	nums = nums.split('\n');
        CheckNumsResponse(nums);
}

function CheckNumsResponse(nums)
{
	multiHTML = doc('multi').innerHTML;
	doc('multi').innerHTML = "";
	
	num_ids = nums.length;

	var i;
	for(i = 0; i < nums.length; i++)
	{
	  var url_to_get = "api/call_numbers/"+escape(nums[i])+".json";
	  FetchLCField(url_to_get, i);
	}

    $("#BackButton").show();
}

function RemoveLCField(id){
	doc('multi').removeChild(doc(id+'_div'));
	EnableGetTagsButton();
}

function FetchLCField(url_to_get, id){
  $.get(url_to_get, 
	function(ret) {		
	  GenerateLCField(ret, id);
	}, "json");
}

function GenerateLCField(ret, id)
{
	
	id = "num" + id;
	var presult_id = id + "_presult";

	var div = document.createElement('div');
	div.setAttribute('id',id+'_div');
	div.style.cssFloat = 'left'; //left?
	div.style.width = '300px';
	div.style.padding = '3px';
	div.style.border = 'solid 1px #dddddd';
	
	var input = document.createElement('input');
	input.setAttribute('type', 'text');
	input.setAttribute('value', ret.call_number);
	input.setAttribute('id', id);
	input.setAttribute('size','30');
	input.setAttribute('onkeyup', 'SetTimer("' + id + '")');
	
	var remove = document.createElement('a');
	remove.setAttribute('onclick','RemoveLCField("' + id + '")');
	remove.style.color = error_color;
	remove.style.cssFloat = 'right';
	remove.innerHTML = '-remove-';

	var parse_result = document.createElement('table');
	parse_result.setAttribute('id',presult_id);
	parse_result.className = 'parsed_call' ;
	FillOutParseResult(parse_result,ret);

	var errors = document.createElement('p');
	errors.setAttribute('id', id + '_error');
	
	/* Tag is a hidden field, used for storing base64 reps of tags */
	var tag = document.createElement('p');
	tag.setAttribute('id', id + '_tag');
	tag.style.display = 'none';
	tag.innerHTML = "";

	if(ret.parser_feedback.length != 0){
		errors.innerHTML += DisplayWarnings(ret.parser_feedback,ret.call_number);
	}
	
	if(ret.result.search("SUCCESS") != -1) { 
		input.style.color = good_color;
		tag.innerHTML += ret.book_tag;
	} else { 
		input.style.color = error_color; 
	}
	
	div.appendChild(input);
	div.appendChild(remove);
	div.appendChild(parse_result);
	div.appendChild(document.createElement('br'));
	div.appendChild(errors);
	div.appendChild(tag);

	doc('multi').appendChild(div);

	//Want this to run no matter what, in case we need to disable
	//Must check AFTER the tag has been added!
	EnableGetTagsButton();

}

/**
 * function DisplayImage(data)
 * Stub that will eventually fetch a generated
 * image tag based on parsed LC data.
 * @param data The parsed LC number returned
 * from the ajax request to the parser.
 */
function DisplayImage(num)
{	
	var tag;

	$.get("api/call_numbers/"+escape(num)+".json",
		function(ret) {
			tag = ret.base64;
		}, "json");
	
	return '<img src=\"tagmaker/' + tag + '.png\" />';
}

function DisplayWarnings(warn,originalLC)
{
	var i;
	var html = "";
	
	for(i = 0; i < warn.length; i++)
	{
		if(warn[i].isWarning) {
			html += "<span style=\"color:";
			html += warn_color;
			html += ";\">";
		} else {
			html += "<span style=\"color:";
			html += error_color;
			html += ";\">";
		}
		html += warn[i].msg + "</span>";
		html += "<br />Context: ";
		var j;
		for(j=0; j<originalLC.length; j++){
			if(j == warn[i].conflictStart){
				html += "<span style=\"color:" + warn_color + ";\">&gt;";
			}
			html += originalLC[j];
			if(j == warn[i].conflictEnd){
				html += "&lt;</span>";
			}
		}
 		html += "<br /><br />";
	}
	
	return html;
}

function StepBack() {
	doc("multi").innerHTML = multiHTML;
	doc("num_tags_counter").innerHTML = "N/A";
	$("#BackButton").hide();
}

function EnableGetTagsButton(){
	var alldone = true;
	var counter = 0;
	doc('tag_list').innerHTML = "{";
	for(i=0; i < num_ids; i++){
		var tag = doc('num'+i+'_tag');
		if(tag != null && tag.innerHTML == ""){
			alldone = false;
		}
		if(tag != null){
			counter++;
			doc('tag_list').innerHTML += ("\"" + tag.innerHTML + "\",<br />");
		}
	}
	doc('tag_list').innerHTML += "}<br />";
	if(alldone){
		doc('get_tags_button').removeAttribute('disabled');
	} else {
		doc('get_tags_button').setAttribute('disabled','disabled');
	}
	doc('num_tags_counter').innerHTML = counter;
	/*if(counter > 30){
		doc('num_tags_counter').style.color = error_color;
	} else {
		doc('num_tags_counter').style.color = "#000";
		}*/
	//doc('gtb_debug').innerHTML = alldone;
}

function GetTags(){
	//Using the direct URL instead of shelvar.com to avoid the frames/wrapper
	var URL = "tagmaker/5160.pdf?";
	var counter=0;
	for(i=0; i < num_ids; i++){
		var tag = doc('num'+i+'_tag');
		if(tag != null && tag.innerHTML != ""){
			URL += 'tag' + counter + "=" + tag.innerHTML + '&';
			counter++;
		}
	}
	window.open(URL);
	/*var tag_array = new Array();
	for(i=0; i < num_ids; i++){
		var tag = doc('num'+i+'_tag');
		if(tag != null && tag.innerHTML != ""){
			tag_array[i] = tag.innerHTML;
		}
	}

	var pdfString;
	
	$.post("http://easlnx01.eas.muohio.edu/~shelvar/release/tagmaker/5160PDF.php", 
		{ "TagList": JSON.stringify(tag_array) }, 
		function(ret) {
			pdfString = ret.output;
			window.open("data:application/pdf," + escape(pdfString));
		}, "json");
	*/
}
function GetTags(){
	var tag_array = new Array();
	for(i=0; i < num_ids; i++){
		var tag = doc('num'+i+'_tag');
		if(tag != null && tag.innerHTML != ""){
			tag_array[i] = tag.innerHTML;
		}
	}

	var pdfString;
	var pdfForm = document.createElement("form");
	pdfForm.target = "5160";
	pdfForm.method = "POST";
	pdfForm.action = "tagmaker/5160.pdf";
	pdfForm.style.display = "none";
	
	pdfInput = document.createElement("input");
	pdfInput.type = "text";
	pdfInput.name = "TagList";
	pdfInput.value = tag_array;
	pdfForm.appendChild(pdfInput);
	
	document.body.appendChild(pdfForm);
	
	pdf = window.open("", "5160", "status=0,title=0,height=600,width=800,scrollbars=1");
	
	if (pdf){
		pdfForm.submit();
	} else {
		alert('You must allow popups for this pdf to work.');
	}
	

}
