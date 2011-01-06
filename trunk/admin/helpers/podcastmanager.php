<?php
/**
* Podcast Manager for Joomla!
*
* @version		$Id$
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
*/

// Restricted access
defined('_JEXEC') or die();

/**
 * Podcast Manager component helper.
 */
class PodcastManagerHelper
{
	public static $extension = 'com_podcastmanager';

	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	$vName	The name of the active view.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public static function addSubmenu($vName)
	{
		JSubMenuHelper::addEntry(
			JText::_('COM_PODCASTMANAGER_SUBMENU_FILES'),
			'index.php?option=com_podcastmanager&view=files',
			$vName == 'files');
		JSubMenuHelper::addEntry(
			JText::_('COM_PODCASTMANAGER_SUBMENU_INFO'),
			'index.php?option=com_podcastmanager&view=info',
			$vName == 'info');
	}
}
