<?php
/**
* Podcast Manager for Joomla!
*
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
*/

class Com_PodcastMediaInstallerScript {

	function install($parent) {
		echo '<p>Removing Podcast Media - File Manager menu item</p>';
	}
	
	function postflight($type, $parent) {
		$db = JFactory::getDBO();
		$query	= 'DELETE FROM `#__menu` WHERE `title` = "com_podcastmedia"';
		$db->setQuery($query);
		$db->query();
	}
}
