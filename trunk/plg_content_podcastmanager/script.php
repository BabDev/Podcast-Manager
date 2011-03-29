<?php
/**
* Podcast Manager for Joomla!
*
* @version		$Id$
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
*/

class plgContentPodcastManagerInstallerScript {

	function install($parent) {
		echo '<p>Activate Podcast Manager content plugin</p>';
	}
	
	function postflight($type, $parent) {
		$db = JFactory::getDBO();
		$query	= 'UPDATE `#__extensions` SET `enabled` = 1 WHERE `name` = "plg_content_podcastmanager"';
		$db->setQuery($query);
		$db->query();
	}
}
