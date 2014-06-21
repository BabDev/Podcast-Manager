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
		if (version_compare(JVERSION, '3.0', 'lt'))
		{
			JFactory::getDocument()->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js');

			// Ensure jQuery.noConflict() is set, just in case ;-)
			JHtml::_('script', 'mediaelements/jquery-noconflict.js', false, true);
		}
		else
		{
			JHtml::_('jquery.framework');
		}

		// Add the component media
		JHtml::_('stylesheet', 'podcastmanager/template.css', false, true, false);
		JHtml::_('script', 'podcastmanager/podcast.js', false, true);

		$this->addToolbar();
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
		$input = JFactory::getApplication('administrator')->input;
		$input->set('hidemainmenu', true);

		$user = JFactory::getUser();
		$userId = $user->get('id');
		$isNew = ($this->item->id == 0);
		$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		$canDo = PodcastManagerHelper::getActions($this->state->get('filter.feedname'), $this->item->id);

		JToolBarHelper::title(JText::_('COM_PODCASTMANAGER_VIEW_PODCAST_' . ($isNew ? 'ADD_PODCAST' : 'EDIT_PODCAST')), 'podcastmanager.png');

		// Set the actions for new and existing records.
		if ($isNew)
		{
			// For new records, check the create permission.
			if ($canDo->get('core.create') || (count(PodcastManagerHelper::getAuthorisedFeeds('core.create')) > 0))
			{
				JToolBarHelper::apply('podcast.apply');
				JToolBarHelper::save('podcast.save');
				JToolBarHelper::save2new('podcast.save2new');
			}

			JToolBarHelper::cancel('podcast.cancel');
		}
		else
		{
			// Since it's an existing record, check the edit permission.
			if (!$checkedOut
				&& ($canDo->get('core.edit') || (count(PodcastManagerHelper::getAuthorisedFeeds('core.edit')) > 0)
				|| ($canDo->get('core.edit.own')
				|| (count(PodcastManagerHelper::getAuthorisedFeeds('core.edit.own')) > 0) && $this->item->created_by == $userId)))
			{
				JToolBarHelper::apply('podcast.apply');
				JToolBarHelper::save('podcast.save');

				// We can save this record, but check the create permission to see if we can return to make a new one.
				if ($canDo->get('core.create') || (count(PodcastManagerHelper::getAuthorisedFeeds('core.create')) > 0))
				{
					JToolBarHelper::save2new('podcast.save2new');
				}
			}

			// If an existing item, can save as a copy
			if ($canDo->get('core.create') || (count(PodcastManagerHelper::getAuthorisedFeeds('core.create')) > 0))
			{
				JToolBarHelper::save2copy('podcast.save2copy');
			}

			// Add versions toolbar for CMS 3.2+
			if (version_compare(JVERSION, '3.2', 'ge') && $this->state->params->get('save_history', 0) && $canDo->get('core.edit'))
			{
				JToolbarHelper::versions('com_podcastmanager.podcast', $this->item->id);
			}

			JToolBarHelper::cancel('podcast.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
