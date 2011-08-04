<?php
/**
* Podcast Manager for Joomla!
*
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* @package		PodcastManager
* @subpackage	com_podcastmanager
*
* Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
* Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
*/

// Restricted access
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Feed management controller class.
 *
 * @package		PodcastManager
 * @subpackage	com_podcastmanager
 * @since		1.7
 */
class PodcastManagerControllerFeeds extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 *
	 * @since	1.7
	 */
	public function &getModel($name = 'Feed', $prefix = 'PodcastManagerModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}
