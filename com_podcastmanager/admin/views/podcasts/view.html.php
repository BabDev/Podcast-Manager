<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 *
 * @copyright   Copyright (C) 2011-2012 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

/**
 * Podcast management view class.
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 * @since       1.6
 */
class PodcastManagerViewPodcasts extends JViewLegacy
{
	/**
	 * The items to display
	 *
	 * @var    array
	 * @since  1.6
	 */
	protected $items;

	/**
	 * The pagination object
	 *
	 * @var    JPagination
	 * @since  1.6
	 */
	protected $pagination;

	/**
	 * The HTML markup for the sidebar
	 *
	 * @var    string
	 * @since  2.1
	 */
	protected $sidebar;

	/**
	 * The state information
	 *
	 * @var    JObject
	 * @since  1.6
	 */
	protected $state;

	/**
	 * The allowed item states for list filtering
	 *
	 * @var    array
	 * @since  2.0
	 */
	protected $states = array('published' => true, 'unpublished' => true, 'archived' => false, 'trashed' => true, 'all' => true);

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @since   1.6
	 */
	public function display($tpl = null)
	{
		// Load the submenu.
		if ($this->getLayout() != 'modal')
		{
			PodcastManagerHelper::addSubmenu('podcasts');
		}

		// Initialise variables
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state      = $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		// We don't need the toolbar or external media in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			// Add the component media
			JHtml::_('stylesheet', 'podcastmanager/template.css', false, true, false);

			// Make text JS available
			JText::script('COM_PODCASTMANAGER_CONFIRM_PODCAST_UNPUBLISH');

			$this->addToolbar();

			// Add the sidebar for J! 3.0
			if (version_compare(JVERSION, '3.0', 'ge'))
			{
				$this->sidebar = JHtmlSidebar::render();
			}
		}

		// Add the HTML Helper
		JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/html');

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		$canDo = PodcastManagerHelper::getActions($this->state->get('filter.feedname'));

		JToolBarHelper::title(JText::_('COM_PODCASTMANAGER_VIEW_PODCASTS_TITLE'), 'podcastmanager.png');

		if ($canDo->get('core.create') || (count(PodcastManagerHelper::getAuthorisedFeeds('core.create')) > 0))
		{
			JToolBarHelper::addNew('podcast.add');
		}

		if ($canDo->get('core.edit') || (count(PodcastManagerHelper::getAuthorisedFeeds('core.edit')) > 0)
			|| $canDo->get('core.edit.own') || (count(PodcastManagerHelper::getAuthorisedFeeds('core.edit.own')) > 0))
		{
			JToolBarHelper::editList('podcast.edit');
		}

		if ($canDo->get('core.edit.state') || (count(PodcastManagerHelper::getAuthorisedFeeds('core.edit.state')) > 0))
		{
			JToolBarHelper::divider();
			JToolBarHelper::publish('podcasts.publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('podcasts.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::divider();
			JToolBarHelper::checkin('podcasts.checkin');
			JToolBarHelper::divider();
		}

		if ($this->state->get('filter.published') == -2 && ($canDo->get('core.delete')
			|| (count(PodcastManagerHelper::getAuthorisedFeeds('core.delete')) > 0)))
		{
			JToolBarHelper::deleteList('', 'podcasts.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		}
		elseif ($canDo->get('core.edit.state') || (count(PodcastManagerHelper::getAuthorisedFeeds('core.edit.state')) > 0))
		{
			JToolBarHelper::trash('podcasts.trash');
			JToolBarHelper::divider();
		}

		// Add a batch button in J! 3.0
		if (version_compare(JVERSION, '3.0', 'ge') && $canDo->get('core.edit'))
		{
			$bar = JToolBar::getInstance('toolbar');
			$title = JText::_('JTOOLBAR_BATCH');
			$dhtml = '<button data-toggle="modal" data-target="#collapseModal" class="btn btn-small">'
					. '<i class="icon-checkbox-partial" title="' . $title . '"></i>'
					. $title . '</button>';
			$bar->appendButton('Custom', $dhtml, 'batch');
		}

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_podcastmanager');
		}

		// Section below for 3.0 compatibility
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/html');

			JHtmlSidebar::setAction('index.php?option=com_podcastmanager&view=feeds');

			JHtmlSidebar::addFilter(
				JText::_('JOPTION_SELECT_PUBLISHED'),
				'filter_published',
				JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', $this->states), 'value', 'text', $this->state->get('filter.published'), true)
			);

			JHtmlSidebar::addFilter(
				JText::_('COM_PODCASTMANAGER_SELECT_FEEDNAME'),
				'filter_feedname',
				JHtml::_('select.options', JHtml::_('podcast.feeds'), 'value', 'text', $this->state->get('filter.feedname'))
			);

			JHtmlSidebar::addFilter(
				JText::_('JOPTION_SELECT_LANGUAGE'),
				'filter_language',
				JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'))
			);
		}
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'a.published' => JText::_('JSTATUS'),
			'a.title' => JText::_('JGLOBAL_TITLE'),
			'a.feedname' => JText::_('COM_PODCASTMANAGER_HEADING_FEEDNAME'),
			'a.created' => JText::_('JDATE'),
			'a.language' => JText::_('JGRID_HEADING_LANGUAGE'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
