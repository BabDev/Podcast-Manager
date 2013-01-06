<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  plg_finder_podcastmanager_podcasts
 *
 * @copyright   Copyright (C) 2011-2013 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

/**
 * Installation class to perform additional changes during install/uninstall/update
 *
 * @package     PodcastManager
 * @subpackage  plg_finder_podcastmanager_podcasts
 * @since       2.0
 */
class PlgFinderPodcastManager_PodcastsInstallerScript
{
	/**
	 * Function to act prior to installation process begins
	 *
	 * @param   string            $type    The action being performed
	 * @param   JInstallerPlugin  $parent  The function calling this method
	 *
	 * @return  boolean  True on success, false on error
	 *
	 * @since   2.0
	 */
	public function preflight($type, $parent)
	{
		// Make sure we aren't uninstalling first
		if ($type != 'uninstall')
		{
			// Check if Podcast Manager is installed
			if (!is_dir(JPATH_BASE . '/components/com_podcastmanager'))
			{
				JError::raiseNotice(null, JText::_('PLG_FINDER_PODCASTMANAGER_PODCASTS_ERROR_COMPONENT'));

				return false;
			}
		}

		return true;
	}
}
