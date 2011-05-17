<?php
/**
* Podcast Manager for Joomla!
*
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
* Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
* Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
*/

// Restricted access
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view');

class PodcastManagerViewPodcasts extends JView
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 *
	 * @return	void
	 */
	public function display($tpl = null)
	{
		// Initialise variables
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// We don't need the toolbar or external media in the modal window.
		if ($this->getLayout() !== 'modal') {
			// Add the component media
			JHTML::stylesheet('administrator/components/com_podcastmanager/media/css/template.css', false, false, false);
			JHTML::script('administrator/components/com_podcastmanager/media/js/podcasts.js', false, false);

			$this->addToolbar();
		}

		require_once JPATH_COMPONENT .'/models/fields/feedfilter.php';
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		$canDo	= PodcastManagerHelper::getActions();

		JToolBarHelper::title(JText::_('COM_PODCASTMANAGER_VIEW_PODCASTS_TITLE'), 'podcastmanager.png');

		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('podcast.add','JTOOLBAR_NEW');
		}
		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList('podcast.edit','JTOOLBAR_EDIT');
		}
		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
			JToolBarHelper::custom('podcasts.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			JToolBarHelper::custom('podcasts.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::divider();
			JToolBarHelper::custom('podcasts.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
			JToolBarHelper::divider();
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'podcasts.delete','JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		}
		else if ($canDo->get('core.edit.state')) {
			JToolBarHelper::trash('podcasts.trash','JTOOLBAR_TRASH');
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_podcastmanager');
		}
	}
}
