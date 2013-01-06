/**
 * Podcast Manager for Joomla!
 *
 * @package		PodcastManager
 * @subpackage	com_podcastmanager
 *
 * @copyright	Copyright (C) 2011-2013 Michael Babker. All rights reserved.
 * @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

cookie_name = "podManFile";
var FileName;

var today = new Date();
today.setTime(today.getTime());

// Create a thirty second cookie
// expires = 1000 (milliseconds) * (seconds) * (minutes) etc.
expires = 30000;
var expires_date = new Date(today.getTime() + (expires));

function makeCookie() {
	if (document.cookie != document.cookie) {
		index = document.cookie.indexOf(cookie_name);
	} else {
		index = -1;
	}

	if (index == -1) {
		FileName = escape(document.adminForm.jform_filename.value);
		document.cookie=cookie_name+"="+FileName+"; expires="+expires_date.toGMTString();
		location.reload();
	}
}
