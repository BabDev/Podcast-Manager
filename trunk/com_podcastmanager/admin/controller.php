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

jimport('joomla.application.component.controller');

/**
 * Podcast Manager Controller
 */
class PodcastManagerController extends JController
{
	/**
	 * @var		string	The default view.
	 * @since	1.6
	 */
	protected $default_view = 'podcasts';

	/**
	 * Method to display a view.
	 */
	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT.'/helpers/podcastmanager.php';

		// Load the submenu.
		PodcastManagerHelper::addSubmenu(JRequest::getWord('view', 'podcasts'));

		$view		= JRequest::getWord('view', 'podcasts');
		$layout 	= JRequest::getWord('layout', 'podcasts');
		$id			= JRequest::getInt('id');

		parent::display();

		return $this;
	}
}