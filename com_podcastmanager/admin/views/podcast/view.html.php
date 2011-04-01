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

jimport('joomla.application.component.view');
jimport('joomla.filesystem.file');

class PodcastManagerViewPodcast extends JView
{
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// Initialiase variables.
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Add the component media
		JHTML::stylesheet('administrator/components/com_podcastmanager/media/css/template.css', false, false, false);
		JHTML::script('administrator/components/com_podcastmanager/media/js/podcast.js', false, false);

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
		JRequest::setVar('hidemainmenu', true);

		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$isNew		= ($this->item->id == 0);
		$canDo		= PodcastManagerHelper::getActions();

		JToolBarHelper::title(JText::_('COM_PODCASTMANAGER_VIEW_PODCAST_'.($isNew ? 'ADD_PODCAST' : 'EDIT_PODCAST')), 'podcastmanager.png');

		// Set the actions for new and existing records.
		if ($isNew)  {
			// For new records, check the create permission.
			if ($canDo->get('core.create')) {
				JToolBarHelper::apply('podcast.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('podcast.save', 'JTOOLBAR_SAVE');
			}

			JToolBarHelper::cancel('podcast.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			// Since it's an existing record, check the edit permission.
			if ($canDo->get('core.edit')) {
				JToolBarHelper::apply('podcast.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('podcast.save', 'JTOOLBAR_SAVE');

				// We can save this record, but check the create permission to see if we can return to make a new one.
				if ($canDo->get('core.create')) {
					JToolBarHelper::custom('podcast.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
				}
			}
			// If an existing item, can save as a copy
			if ($canDo->get('core.create')) {
				JToolBarHelper::custom('podcast.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
			}

			JToolBarHelper::cancel('podcast.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
