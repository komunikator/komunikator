<?php
/**
 * javascript.php
 * This file is part of the FreeSentral Project http://freesentral.com
 *
 * FreeSentral - is a Web Graphical User Interface for easy configuration of the Yate PBX software
 * Copyright (C) 2008-2009 Null Team
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301, USA.
 */
?>
<script type="text/javascript" language="JavaScript1.2">

function getInternetExplorerVersion()
// Returns the version of Internet Explorer or a -1
// (indicating the use of another browser).
{
  var rv = -1; // Return value assumes failure.
  if (navigator.appName == 'Microsoft Internet Explorer')
  {
    var ua = navigator.userAgent;
    var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
    if (re.exec(ua) != null)
      rv = parseFloat( RegExp.$1 );
  }
  return rv;
}

function copacClick(lin,col,role)
{
//	alert(lin+" "+col+" "+role);
	var li = document.getElementById("copac_li_"+lin+"_"+col);
	var ul = document.getElementById("copac_ul_"+lin+"_"+col);
	if (!ul)
		return true;
	if (ul.style.display == "none") {
		ul.style.display = "block";
		li.setAttribute("type","circle");
		return true;
	}
	li.setAttribute("type","disc");
	for (c=col; ; c++) {
		ul = document.getElementById("copac_ul_"+lin+"_"+c);
		if (!ul)
			break;
		ul.style.display = "none";
	}
	return false;
}

function copacValoare(lin,col)
{
	var li = document.getElementById("copac_li_"+lin+"_"+col);
	if (!li)
		return null;
	var a = li.firstChild;
	if (!a)
		return null;
	return a.innerHTML;
}

function copacParinte(lin,col)
{
	if (col <= 0)
		return null;
	col--;
	for (; lin >= 0; lin--) {
		if (document.getElementById("copac_li_"+lin+"_"+col))
			return copacValoare(lin,col);
	}
	return null;
}

function copacComuta(nume)
{
	for (lin = 0; ; lin++) {
		var none = true;
		for (col = 0; col < 100; col++) {
			var li = document.getElementById("copac_li_"+lin+"_"+col);
			if (li) {
				none = false;
				if (nume == copacValoare(lin,col)) {
					copacClick(lin,col,"");
					return true;
				}
			}
		}
		if (none)
			return false;
	}
}

function open_invoice(url)
{
    win = window.open(url.href,url.target,"width=1000");
    if (win && window.focus)
	win.focus();
}

function show_hide(element)
{
	/*var div = document.getElementById("div_rates");
	var div_show = document.getElementById("show_r");
	var div_hide = document.getElementById("hide_r");
	if (div.style.display == "none") {
		div_show.style.display = "none";
		div_hide.style.display = "block";
		div.style.display = "block";
	}else{
		div_show.style.display = "block";
		div_hide.style.display = "none";
		div.style.display = "none";
	}*/
//alert(element);
	var div = document.getElementById(element + "_selected");
	if (div.style.display == "none") {
		div.style.display = "block";
	}else{
		div.style.display = "none";
	}
}

function form_for_gateway(gwtype)
{
	//var sprot = document["forms"]["outbound"][gwtype+"protocol"];
	var sprot = document.getElementById(gwtype+"protocol");
	var sprotocol = sprot.options[sprot.selectedIndex].value || sprot.options[sprot.selectedIndex].text;
	var protocols = new Array("sip", "h323", "iax", "pstn", "BRI", "PRI");
	var i;
	var currentdiv;
	var othergw;

	if(gwtype == "reg")
		othergw = "noreg";
	else
		othergw = "reg";

	for(var i=0; i<protocols.length; i++) 
	{
		currentdiv = document.getElementById("div_"+gwtype+"_"+protocols[i]);
		if(currentdiv == null)
			continue;
		if(currentdiv.style.display == "block")
			currentdiv.style.display = "none";
	}
	for(var i=0; i<protocols.length; i++) 
	{
		currentdiv = document.getElementById("div_"+othergw+"_"+protocols[i]);
		if(currentdiv == null)
			continue;
		if(currentdiv.style.display == "block")
			currentdiv.style.display = "none";
	}
	currentdiv = document.getElementById("div_"+gwtype+"_"+sprotocol);
	if(currentdiv == null)
		return false;
	if(currentdiv.style.display == "none")
		currentdiv.style.display = "block";
}

function show_hide_comment(id)
{
	var fontvr = document.getElementById("comment_"+id);
	if(fontvr == null)
		return;
	if (fontvr.style.display == "none")
		fontvr.style.display = "block";
	else
		if(fontvr.style.display == "block")
			fontvr.style.display = "none";
}

function form_for_dialplan(objname)
{
	var protocols = new Array("sip", "h323", "iax", "wp", "zap", "for_gateway");
	var sprotocol = document.outbound.protocol.value;
	var sgateway = document.outbound.gateway.value;
	var currentdiv;

	for(var i=0; i<protocols.length; i++)
	{
		currentdiv = document.getElementById(protocols[i]);
		if(currentdiv == null)
			continue;

		if(currentdiv.style.display == "block")
			currentdiv.style.display = "none"; 
	}	
	if(objname == "for_gateway") {
		document.outbound.protocol.selectedIndex = 0;
		currentdiv = document.getElementById("for_gateway");
	}else{
		document.outbound.gateway.selectedIndex = 0;
		currentdiv = document.getElementById(sprotocol);
	}
	if(currentdiv == null)
		return false;
	if(currentdiv.style.display != "block")
		currentdiv.style.display = "block";
}

function gateway_type()
{
	var divname, div;

/*	var elems = document.outbound.elements;
	for(i=0; i<elems.length;i++)
	{
		alert(elems[i].name);
		if (i>2)
			break;
	}
	return;*/

	var radio = document['forms']['outbound']['gateway_with_registration'];
	var sel_gateway;
	for(var i=0; i<radio.length; i++)
	{
		divname = radio[i].value;
		div = document.getElementById('div_'+divname);
		if (div == null)
			continue;
		if (radio[i].checked == true) {
			div.style.display = "block";
			sel_gateway = (divname == "Yes") ? "reg" : "noreg";
		} else
			div.style.display = "none";
	}

	var gwtype = "reg";
	var othergw = "noreg";
	var protocols = new Array("sip", "h323", "iax", "zap", "wp");

	for(i=0; i<protocols.length; i++) 
	{
		currentdiv = document.getElementById("div_"+gwtype+"_"+protocols[i]);
		if(currentdiv == null)
			continue;
		if(currentdiv.style.display == "block")
			currentdiv.style.display = "none";
	}
	for(i=0; i<protocols.length; i++) 
	{
		currentdiv = document.getElementById("div_"+othergw+"_"+protocols[i]);
		if(currentdiv == null)
			continue;
		if(currentdiv.style.display == "block")
			currentdiv.style.display = "none";
	}
	if (sel_gateway != null)
		form_for_gateway(sel_gateway);
}

function advanced(identifier)
{
	var elems = document.outbound.elements;
	var elem_name;
	var elem;

	var ie = getInternetExplorerVersion();

	for(var i=0;i<elems.length;i++)
	{
		elem_name = elems[i].name;
		if(identifier.length < elem_name.length && elem_name.substr(0,identifier.length) != identifier)
			continue;
		var elem = document.getElementById("tr_"+elem_name); 
		if(elem == null)
			continue;
		if(elem.style.display == null || elem.style.display == "")
			continue;
		if(elem.style.display == "none")
			elem.style.display = (ie > 1 && ie < 8) ? "block" : "table-row";
		else
			// specify the display property (the elements that are not advanced will have display="")
			if(elem.style.display == "block" || elem.style.display == "table-row")
				elem.style.display = "none";
	}

	var img = document.getElementById(identifier+"advanced");
	var imgsrc= img.src;
	var imgarray = imgsrc.split("/");
	if(imgarray[imgarray.length - 1] == "advanced.jpg"){
		imgarray[imgarray.length - 1] = "basic.jpg";
		img.title = "Hide advanced fields";
	}else{
		imgarray[imgarray.length - 1] = "advanced.jpg";
		img.title = "Show advanced fields";
	}

	img.src = imgarray.join("/");
}

function show_fields(nr)
{
	var elems = document.wizard.elements;
	var tr_elem, tr_elem_id, elem_name;
	var ie = getInternetExplorerVersion();

	elem_name = "add"+(nr-1);
	tr_elem_id = "tr_"+"add"+(nr-1);
	tr_elem = document.getElementById(tr_elem_id);
		
	if(tr_elem == null) {
		return;
	}
	if(tr_elem_id.substr(elem_name.length+2, tr_elem_id.length) != (nr-1)){
		return;
	}
	tr_elem.style.display = "none";
	for(var i=0; i<elems.length; i++)
	{
		elem_name = elems[i].name;
		tr_elem_id = "tr_"+elem_name;
		tr_elem = document.getElementById(tr_elem_id);
		
		if(tr_elem == null)
			continue;

		if(tr_elem_id.substr(elem_name.length+2, tr_elem_id.length) != nr)
			continue;

	/*	if(tr_elem.style.display == "table-row")
			tr_elem.style.display = "none";
		else*/
		tr_elem.style.display = (ie > 1 && ie < 8) ? "block" : "table-row";
	}

	elem_name = "add"+nr;
	tr_elem_id = "tr_"+"add"+nr;
	tr_elem = document.getElementById(tr_elem_id);
		
	if(tr_elem == null) {
		return;
	}
	if(tr_elem_id.substr(elem_name.length+2, tr_elem_id.length) != nr){
		return;
	}
	tr_elem.style.display = (ie > 1 && ie < 8) ? "block" : "table-row";
}

function comute_destination(elem_type)
{
	var elem = document.getElementById(elem_type);
	var other_elem = (elem_type == "group") ? "number" : "group";
	var unselect_elem = document.getElementById(other_elem);

	if(other_elem == "number")
		unselect_elem.value = '';
	else
		unselect_elem.selectedIndex = 0;
}

function dependant_fields()
{
	var prot = document.getElementById("protocol");
	var sel_prot = prot.options[prot.selectedIndex].value || prot.options[prot.selectedIndex].text;
	var field, textf;
	var fields = new Array("ip_address", "netmask", "gateway");
	var ie = getInternetExplorerVersion();

	for(var i=0; i<fields.length; i++)
	{
		field = document.getElementById("div_"+fields[i]);
		textf = document.getElementById("text_"+fields[i]);
		if(sel_prot == "static") {
			field.style.display = (ie>1 && ie<8) ? "block" : "table-cell";
			textf.style.display = "none";
		}else if(sel_prot == "dhcp" || sel_prot == "none" || sel_prot=="") {
			field.style.display = "none";
			textf.style.display = (ie>1 && ie<8) ? "block" : "table-cell";
		}
	}
}

function check_selected_destination()
{
	var dest = document.getElementById("destination");
	var sel = dest.options[dest.selectedIndex].value || dest.options[dest.selectedIndex].text;
	var ie = getInternetExplorerVersion();
	var insert_destination = document.getElementById("tr_insert_destination");
	var default_destination = document.getElementById("tr_default_destination");

	if (sel == "custom") {
		insert_destination.style.display = (ie>1 && ie<8) ? "block" : "table-row";
		default_destination.style.display = "none";
	} else if (sel == "external/nodata/auto_attendant.php") {
		default_destination.style.display = (ie>1 && ie<8) ? "block" : "table-row";
		insert_destination.style.display = "none";
	} else {
		insert_destination.style.display = "none";
		default_destination.style.display = "none";
	}
}

function change_background(color)
{
//alert(color);
this.bgColor =  "red";
/*	if(color == "gray")
	this.bgColor =  "#888888";
	else
	this.bgColor = "#eeeeee";*/
//alert("exit");
}

function error(error)
{
	alert(error);
}

function is_numeric(val)
{  
     return !isNaN(val);  
} 

function on_submit(function_name)
{
	if(typeof function_name == "string" && eval('typeof ' + function_name) == 'function')
		var res = eval(function_name + '()');
	else
		res = false;//true;
//alert(res);

	if(res == null)
		res = false;
	return res;
	/*if(res == true) 
		document.wizard.submit();
	else
		return false;*/
}

function verify_password()
{
	var new_password = document.wizard.new_password.value;
	var retype_password = document.wizard.retype_new_password.value;
	if(new_password == '' || retype_password == '') {
		error("Please set all required fields.");
		return false;
	}
	if(new_password.length < 5) {
		error("Password must be at least 5 digits long.");
		return false;
	}
	if(new_password != retype_password) {
		error("The passwords don't match.");
		return false;
	}
	return true;
}

function verify_extensions()
{
	var from = document.wizard.from.value;
	var to = document.wizard.to.value;

	if(from == '' || to == '') {
		error("Please set all the required fields.");
		return false;
	}
	if(from.length != to.length) {
		error("The 'From' and 'To' fields must have the same length.");
		return false;
	}
	if(from > to) {
		error("'From' field must be smaller than 'To'.");
		return false;
	}
	if(!is_numeric(from) || !is_numeric(to))
	{
		error("Fields must be numeric.");
		return false;
	}
	if(from.length < 3) {
		error("Fields must be at least 3 digits long.");
		return false;
	}
	return true;
}

function verify_groups()
{
//	var fields = new Array("group", "extension", "members", "from", "to");
	var total = 4;
	var nr;
	var group, extension, members, from, to;

	for(var i=1; i<=total; i++)
	{
		nr = (i==1) ? '' : i;
		group = eval("document.wizard.group"+nr+".value");
		extension = eval("document.wizard.extension"+nr+".value");
		members = eval("document.wizard.members"+nr+".value");
		from = eval("document.wizard.from"+nr+".value");
		to = eval("document.wizard.to"+nr+".value");
		if(group == '' || extension == '')
			if(i == 1) {
				error("Please set all the required fields.");
				return false;
			}else{
				for(j=i; j<=total;j++) {
					eval("document.wizard.group"+j+".value='';");
					eval("document.wizard.extension"+j+".value='';");
					eval("document.wizard.members"+j+".value='';");
					eval("document.wizard.from"+j+".value=''");
					eval("document.wizard.to"+j+".value='';")
				}
				break;
			}
		if(members == '' && (to == '' || from == '')) {
			error("You must either complete the Members"+nr+" fields or the To"+nr+" and From"+nr+" fields.");
			return false;
		}
		if(extension.length != 2) {
			error("Field 'Extension"+nr+"' must be 2 digits long.");
			return false;
		}
		if(to != '' && from != '') {
			if(from.length != to.length) {
				error("The 'From"+nr+"' and 'To"+nr+"' fields must have the same length.");
				return false;
			}
			if(from > to) {
				error("'From"+nr+"' field must be smaller than 'To"+nr+"'.");
				return false;
			}
			if((typeof parseInt(from)) != "number" || (typeof parseInt(to)) != "number")
			{
				error("Fields must be numeric.");
				return false;
			}
			if(from.length < 3) {
				error("Fields must be at least 3 digits long.");
				return false;
			}
		}
	}
	return true;
}

function verify_gateway()
{
	var protocol = document.wizard.protocol.value;
	var username = document.wizard.username.value;
	var password = document.wizard.password.value;
	var server = document.wizard.server.value;

	if(protocol == '' || server == '') {
		error("Please set all the required fields.");
		return false;
	}
	if((username == '' && password != '') || (username != '' && password == '')) {
		error("Please set both username and password.");
		return false;
	}
	return true;
}

function verify_voicemail()
{
	var number = document.wizard.number.value;
	if(number == '') {
		error("Please set the 'Number' field.");
		return false;
	}
	if(!is_numeric(number)) {
		error("Field 'Number' must be numeric.");
		return false;
	}
	return true;
}

var notified = false;
/*
function verify_auto_attendant()
{
	var number = document.wizard.number.value;
	var extension = document.wizard.extension.value;
	var online_prompt = document.wizard.online_prompt.value;
	var offline_prompt = document.wizard.offline_prompt.value;
	var start, end, not;
alert("enter");
	// i suppose he did't want to set the auto attendant
	if(number == '')
		return true;
	if(number == '' || extension == '' || online_prompt == '' || offline_prompt == '')
	{
		error("Please set all the required fields.");
		return false;
	}
alert("i reach here");

	var days = new Array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
}*/


function verify_auto_attendant()
{
	var number = document.wizard.number.value;
	var extension = document.wizard.extension.value;
	var online_prompt = (document.wizard.online_prompt.value != '') ? document.wizard.online_prompt.value : document.wizard.fake_online_prompt.value;
	var offline_prompt = (document.wizard.offline_prompt.value != '') ? document.wizard.offline_prompt.value : document.wizard.fake_offline_prompt.value;
	var start, end, not;

	if(number == '' || extension == '' || online_prompt == '' || offline_prompt == '')
	{
		error("Please set all the required fields. number="+number+" extension="+extension+" online_prompt="+online_prompt+"offline_prompt"+offline_prompt);
		return false;
	}

	var days = new Array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
	var scheduled = false;
	for(var i=0; i<days.length; i++) {
		start = eval("document.wizard.start_"+days[i]+".value");
		end = eval("document.wizard.end_"+days[i]+".value");
		if(start != "Not selected" && end != "Not selected") {
			scheduled = true;
			break;
		}
	}

	if(scheduled == false) {
		not = confirm("You didn't scheduled the online AutoAttendant. Are you sure you want to continue?");
		if(not == false)
			return false;
	}

	var total = 5;
	var aut_type, key, number, group, keys_defined;

	keys_defined = false;
	for(i=1; i<=total; i++) {
		key = eval("document.wizard.key"+i+".value");
		aut_type = eval("document.wizard.type"+i+".value");
		number = eval("document.wizard.number"+i+".value");
		group = eval("document.wizard.group"+i+".value");

		if(aut_type != '' && key != '') {
			keys_defined = true;
		}else
			continue;
		if((number == '' && group == '') || (number != '' && group != '')) {
			error("You must set one of the two fields Number"+i+" or Group"+i+".");
			return false;
		}
	}
	if(keys_defined == false) {
		not = confirm("You didn't defined any keys for your Auto Attendant. Are you sure you want to continue?");
		if(not == false)
			return false;
	}

	return true;
}

function verify_moh()
{
	var max_files =  5;
	var setted = false;
	for(var i=1; i<=5; i++)
	{
		if(eval("document.wizard.file"+i+".value") != "")
			setted = true;
	}
	if(setted == false){
		not = confirm("You didn't upload any files for Music on hold. Are you sure you want to continue?");
		if(not == false)
			return false;
	}

	return true;
}

var e1_sig_chan = "16";
var e1_voice_chan = "1.15-17-31";

var t1_sig_chan = "24";
var t1_voice_chan = "1-23";

function set_timeslots()
{
	var select_type = document.getElementById("connection_type");
	var interface_type = select_type.options[select_type.selectedIndex].value || select_type.options[select_type.selectedIndex].text;

	var sig_channels = document.getElementById("sig_channels");
	var voice_channels = document.getElementById("voice_channels");

	var front_end_line_coding = document.getElementById("front_end_line_coding");
	var front_end_framing = document.getElementById("front_end_framing");

	// configuring the channels differently depending whether the connection type is E1 or T1
	if (interface_type != "T1") {
		sig_channels.value = e1_sig_chan;
		voice_channels.value = e1_voice_chan;
		front_end_framing.options.length = 0;
		front_end_framing.options[0] = new Option("CRC4","CRC4",true,false);
		front_end_framing.options[1] = new Option("NCRC4","NCRC4",false,false);

		front_end_line_coding.options.length = 0;
		front_end_line_coding.options[0] = new Option("B8ZS","B8ZS",true,false);
		front_end_line_coding.options[1] = new Option("AMI","AMI",true,false);
	}else{
		sig_channels.value = t1_sig_chan;
		voice_channels.value = t1_voice_chan;
		front_end_framing.options.length = 0;
		front_end_framing.options[0] = new Option("ESF","ESF",true,false);
		front_end_framing.options[1] = new Option("D4","D4",false,false);

		front_end_line_coding.options.length = 0;
		front_end_line_coding.options[0] = new Option("HDB3","HDB3",true,false);
		front_end_line_coding.options[1] = new Option("AMI","AMI",true,false);
	}
}

function set_format()
{
	var sel_port = document.getElementById("noreg_PRIport");
	var name_port = sel_port.options[sel_port.selectedIndex].value || sel_port.options[sel_port.selectedIndex].text;
	var connection_type;
	if(name_port.indexOf("(PRI-T1)") != -1)
		connection_type = "T1";
	else
		connection_type = "E1";
	var format = (connection_type == "T1") ? "mulaw" : "alaw";
	var opts = document.getElementById("noreg_PRIformat");

	var opts = document.outbound.noreg_PRIformat;
	
	for(i=0; i<opts.length; i++) {
		if((opts[i].value == format || opts[i].text == format) && opts.checked != true)
			opts[i].checked = true;
	}

//alert(opts.length);
//	opts.value = "alaw";
}

function set_voice_channels()
{
	var select_type = document.getElementById("connection_type");
	var interface_type = select_type.options[select_type.selectedIndex].value || select_type.options[select_type.selectedIndex].text;
	var num;

	var sig_chan = document.getElementById("sig_channels");
	var voice_chan = document.getElementById("voice_channels");
	var bt, tp;

	if (interface_type != "T1") {
		e1_sig_chan = sig_chan.value;
		if(e1_sig_chan == null || e1_sig_chan == "") {
			sig_chan.value = "16";
			e1_sig_chan = "16";
		}
		if (e1_sig_chan == "16")
			return true;
		num = parseInt(e1_sig_chan);
		if (num == 1) {
			voice_chan.value = "2-31";
			e1_voice_chan = "2-31";
			return true;
		}
		bt = num - 1;
		tp = num + 1;
		if(bt > 1)
			e1_voice_chan = "1."+bt;
		else
			e1_voice_chan = "1";
		if(tp < 31)
			e1_voice_chan = e1_voice_chan + "-" + tp + ".31";
		else
			e1_voice_chan = e1_voice_chan + "-" + "31";
		voice_chan.value = e1_voice_chan;
	}else{
		t1_sig_chan = sig_chan.value;
		if (t1_sig_chan == null || t1_sig_chan == "") {
			sig_chan.value = "24";
			t1_sig_chan = "24";
		}
		if (t1_sig_chan == "24")
			return true;
		num = parseInt(t1_sig_chan);
		if (num == 1) {
			voice_chan.value = "2-24";
			t1_voice_cham = "2-34";
			return true;
		}
		bt = num -1;
		tp = num +1;
		if(bt>1)
			t1_voice_chan = "1."+bt;
		else
			t1_voice_chan = "1";
		if(tp<24)
			t1_voice_chan = t1_voice_chan + "-" + tp + ".24";
		else
			t1_voice_chan = t1_voice_chan + "-" + "24";
		voice_chan.value = t1_voice_chan;
	}
	return true;
}

function check_transport()
{
	var transport = document.getElementById("noreg_sipoip_transport");
	var transport_type = transport.options[transport.selectedIndex].value || transport.options[transport.selectedIndex].text;

	if (transport_type=="TLS" && document.getElementById("noreg_sipport").value == 5060)
		document.getElementById("noreg_sipport").value = 5061;
	else if (document.getElementById("noreg_sipport").value == 5061)
		document.getElementById("noreg_sipport").value = 5060;
}

</script>
