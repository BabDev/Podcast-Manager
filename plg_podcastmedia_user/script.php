<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  plg_podcastmedia_user
 *
 * @copyright   Copyright (C) 2011-2015 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

/**
 * Installation class to perform additional changes during install/uninstall/update
 *
 * @package     PodcastManager
 * @subpackage  plg_podcastmedia_user
 * @since       3.0
 */
class PlgPodcastMediaUserInstallerScript extends JInstallerScript
{
	/**
	 * Extension script constructor.
	 *
	 * @since   3.0
	 */
	public function __construct()
	{
		$this->extension     = 'user';
		$this->minimumJoomla = '3.6';
		$this->minimumPhp    = '5.4';
	}
}
