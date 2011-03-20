/**
* Podcast Manager for Joomla!
*
* @version		$Id$
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
*/

Joomla.submitbutton = function(pressbutton) {
	if (pressbutton == 'podcasts.unpublish') {
		if (confirm('Unpublishing files may disrupt the feed. Are you sure you wish to continue unpublishing? (Files will not be removed.)')) {
			Joomla.submitform(pressbutton);
		}
	} else {
		Joomla.submitform(pressbutton);
	}
}
