<?php
/**
* Podcast Manager for Joomla!
*
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
*/

class plgEditorsXtdPodcastManagerInstallerScript {

	function install($parent) {
		echo '<p>Activate Podcast Manager button plugin</p>';
	}
	
	function postflight($type, $parent) {
		$db = JFactory::getDBO();
		$query	= 'UPDATE `#__extensions` SET `enabled` = 1 WHERE `name` = "plg_editors-xtd_podcastmanager"';
		$db->setQuery($query);
		$db->query();
	}
}
