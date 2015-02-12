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

defined('_JEXEC') or die;

/**
 * Podcast Manager button plugin.
 *
 * @package     PodcastManager
 * @subpackage  plg_podcastmedia_user
 * @since       2.0
 */
class PlgPodcastMediaUser extends JPlugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 * @since  3.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * Appends the user name to the default path
	 *
	 * @return  string  The user name
	 *
	 * @since   2.0
	 */
	public function onPathFind()
	{
		return JFactory::getUser()->username;
	}
}
