<?php
/**
* Podcast Manager for Joomla!
*
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
	protected $default_view = 'feeds';

	/**
	 * Method to display a view.
	 */
	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT.'/helpers/podcastmanager.php';

		// Load the submenu.
		PodcastManagerHelper::addSubmenu(JRequest::getWord('view', 'feeds'));

		$view		= JRequest::getWord('view', 'feeds');
		$layout 	= JRequest::getWord('layout', 'feeds');
		$id			= JRequest::getInt('id');

		// Check for edit form.
		if ($view == 'feed' && $layout == 'edit' && !$this->checkEditId('com_podcastmanager.edit.feed', $id)) {

			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_podcastmanager&view=feeds', false));

			return false;
		}
		else if ($view == 'podcast' && $layout == 'edit' && !$this->checkEditId('com_podcastmanager.edit.podcast', $id)) {

			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_podcastmanager&view=podcasts', false));

			return false;
		}

		parent::display();

		return $this;
	}
}
