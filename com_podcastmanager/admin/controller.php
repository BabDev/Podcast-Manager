<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 *
 * @copyright   Copyright (C) 2011-2014 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

/**
 * Podcast Manager base controller
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 * @since       1.6
 */
class PodcastManagerController extends JControllerLegacy
{
	/**
	 * The default view.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $default_view = 'cpanel';

	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types,
	 *                               for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JControllerLegacy  A JControllerLegacy object to support chaining.
	 *
	 * @since   1.6
	 */
	public function display($cachable = false, $urlparams = array())
	{
		$input = JFactory::getApplication('administrator')->input;
		include_once JPATH_COMPONENT . '/helpers/podcastmanager.php';

		$view = $input->get('view', $this->default_view, 'word');
		$layout = $input->get('layout', $this->default_view, 'word');
		$id = $input->get('id', null, 'int');

		// Check for edit form.
		if ($view == 'feed' && $layout == 'edit' && !$this->checkEditId('com_podcastmanager.edit.feed', $id))
		{
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_podcastmanager&view=feeds', false));

			return false;
		}
		elseif ($view == 'podcast' && $layout == 'edit' && !$this->checkEditId('com_podcastmanager.edit.podcast', $id))
		{
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
