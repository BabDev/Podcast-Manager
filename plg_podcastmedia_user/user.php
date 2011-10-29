<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  plg_podcastmedia_user
 *
 * @copyright   Copyright (C) 2011 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

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
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An array that holds the plugin configuration
	 *
	 * @since	2.0
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * Appends the user name to the default path
	 *
	 * @return  string  The user name
	 *
	 * @since   2.0
	 */
	function onPathFind()
	{
		$user = JFactory::getUser();
		$username = $user->get('username');

		return $username;
	}
}
