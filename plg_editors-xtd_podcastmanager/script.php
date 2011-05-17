<?php
/**
* Podcast Manager for Joomla!
*
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
* Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
* Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
*/

// NOTE: This class does not currently process due to a bug in the installer
// See http://joomlacode.org/gf/project/joomla/tracker/?action=TrackerItemEdit&tracker_id=8103&tracker_item_id=25462

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
