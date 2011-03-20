<?php
/**
* Podcast Manager for Joomla!
*
* @version		$Id: podcastmedia.php 93 2011-03-13 11:46:46Z mbabker $
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
*/

class PodcastMediaInstallerScript {

	function postflight($type, $parent) {
		$db = JFactory::getDBO();
		$query	= 'DELETE FROM `#__menu` WHERE `title` = "com_podcastmedia"';
		$db->setQuery($query);
		$db->query();
	}
}
