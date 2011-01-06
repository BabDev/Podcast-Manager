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
	protected $default_view = 'files';

	/**
	 * Method to display a view.
	 */
	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT.'/helpers/podcastmanager.php';

		// Load the submenu.
		PodcastManagerHelper::addSubmenu(JRequest::getWord('view', 'files'));

		$view		= JRequest::getWord('view', 'files');
		$layout 	= JRequest::getWord('layout', 'files');
		$id			= JRequest::getInt('id');

		parent::display();

		return $this;
	}
}