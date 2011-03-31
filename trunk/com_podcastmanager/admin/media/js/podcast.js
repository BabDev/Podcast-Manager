/**
* Podcast Manager for Joomla!
*
* @version		$Id$
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
*/

cookie_name = "podManFile";
var FileName;

var today = new Date();
today.setTime(today.getTime());

var expires_date = new Date(today.getTime()+'120000');

function makeCookie() {
	if (document.cookie != document.cookie) {
		index = document.cookie.indexOf(cookie_name);
	} else {
		index = -1;
	}

	if (index == -1) {
		FileName = document.adminForm.jform_filename.value;
		document.cookie=cookie_name+"="+FileName+"; expires="+expires_date.toGMTString();
		location.reload();
	}
}
