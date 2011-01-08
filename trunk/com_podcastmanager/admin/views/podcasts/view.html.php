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

jimport( 'joomla.application.component.view');

class PodcastManagerViewPodcasts extends JView {
	/** display function from 1.5
	 *  public function display($tpl = null) {
		global $option;
		$app	= JFactory::getApplication();

		$params =& JComponentHelper::getParams($option);

		$filter_published = $app->getUserStateFromRequest($option . 'filter_published', 'filter_published', '*', 'word');
		$filter_metadata = $app->getUserStateFromRequest($option . 'filter_metadata', 'filter_metadata', '*', 'word');

		$filter = array();
		$filter['published'] = self::filter($filter_published, JText::_('Published'), JText::_('Unpublished'), JText::_('Published'), 'filter_published');
		$filter['metadata'] = self::filter($filter_metadata, JText::_('Has Metadata'), JText::_('No Metadata'), JText::_('Metadata'), 'filter_metadata');

		$data =& $this->get('data');
		$folder = $this->get('folder');
		$pagination =& $this->get('pagination');
		$hasSpaces = $this->get('hasSpaces');
		
		$this->assignRef('params', $params);
		$this->assignRef('filter', $filter);
		$this->assignRef('data', $data);
		$this->assignRef('folder', $folder);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('hasSpaces', $hasSpaces);

		parent::display($tpl);
	} */
	
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
		$this->folder		= $this->get('folder');
		$this->data			= $this->get('data');
		$this->hasSpaces	= $this->get('hasSpaces');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$params = JComponentHelper::getParams('com_podcastmanager');
		
		$this->addToolbar();
		
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

		JToolBarHelper::title(JText::_('COM_PODCASTMANAGER_VIEW_PODCASTS_TITLE'), '');

		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('podcast.add','JTOOLBAR_NEW');
		}

		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList('podcast.edit','JTOOLBAR_EDIT');
		}
		if ($canDo->get('core.admin')) {
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_podcastmanager');
		}
	}
	
	// based on JHTMLGrid::state
	private static function filter($filter_state = '*', $state1, $state2, $desc, $requestVar = 'filter_state') {
		$state[] = JHTML::_('select.option', '*', '- ' . $desc . ' -');
		$state[] = JHTML::_('select.option', 'on', $state1);
		$state[] = JHTML::_('select.option', 'off', $state2);
		
		return JHTML::_('select.genericlist', $state, $requestVar, 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_state);
	}
}
