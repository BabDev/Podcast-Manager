<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 *
 * @copyright   Copyright (C) 2011-2015 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

/**
 * Podcast edit view class.
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 * @since       1.6
 */
class PodcastManagerViewPodcast extends JViewLegacy
{
	/**
	 * The form object
	 *
	 * @var    JForm
	 * @since  1.6
	 */
	protected $form;

	/**
	 * The item record
	 *
	 * @var    JObject
	 * @since  1.6
	 */
	protected $item;

	/**
	 * The state information
	 *
	 * @var    JObject
	 * @since  1.6
	 */
	protected $state;

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
		// Initialise variables.
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		// Ensure jQuery is loaded for the metadata parser
		JHtml::_('jquery.framework');

		// Add the component media
		JHtml::_('stylesheet', 'podcastmanager/template.css', false, true, false);
		JHtml::_('script', 'podcastmanager/podcast.js', false, true);

		$this->addToolbar();

		return parent::display($tpl);
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
		JFactory::getApplication('administrator')->input->set('hidemainmenu', true);

		$userId = JFactory::getUser()->get('id');
		$isNew = ($this->item->id == 0);
		$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		$canDo = PodcastManagerHelper::getActions($this->state->get('filter.feedname'), $this->item->id);

		JToolbarHelper::title(JText::_('COM_PODCASTMANAGER_VIEW_PODCAST_' . ($isNew ? 'ADD_PODCAST' : 'EDIT_PODCAST')), 'podcastmanager.png');

		// Set the actions for new and existing records.
		if ($isNew)
		{
			// For new records, check the create permission.
			if ($canDo->get('core.create') || (count(PodcastManagerHelper::getAuthorisedFeeds('core.create')) > 0))
			{
				JToolbarHelper::apply('podcast.apply');
				JToolbarHelper::save('podcast.save');
				JToolbarHelper::save2new('podcast.save2new');
			}

			JToolbarHelper::cancel('podcast.cancel');
		}
		else
		{
			// Since it's an existing record, check the edit permission.
			if (!$checkedOut
				&& ($canDo->get('core.edit') || (count(PodcastManagerHelper::getAuthorisedFeeds('core.edit')) > 0)
				|| ($canDo->get('core.edit.own')
				|| (count(PodcastManagerHelper::getAuthorisedFeeds('core.edit.own')) > 0) && $this->item->created_by == $userId)))
			{
				JToolbarHelper::apply('podcast.apply');
				JToolbarHelper::save('podcast.save');

				// We can save this record, but check the create permission to see if we can return to make a new one.
				if ($canDo->get('core.create') || (count(PodcastManagerHelper::getAuthorisedFeeds('core.create')) > 0))
				{
					JToolbarHelper::save2new('podcast.save2new');
				}
			}

			// If an existing item, can save as a copy
			if ($canDo->get('core.create') || (count(PodcastManagerHelper::getAuthorisedFeeds('core.create')) > 0))
			{
				JToolbarHelper::save2copy('podcast.save2copy');
			}

			// Add versions toolbar
			if ($this->state->params->get('save_history', 0) && $canDo->get('core.edit'))
			{
				JToolbarHelper::versions('com_podcastmanager.podcast', $this->item->id);
			}

			JToolbarHelper::cancel('podcast.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
