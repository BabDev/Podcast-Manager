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
 * Feed edit view class.
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 * @since       1.7
 */
class PodcastManagerViewFeed extends JViewLegacy
{
	/**
	 * The form object
	 *
	 * @var    JForm
	 * @since  1.7
	 */
	protected $form;

	/**
	 * The item record
	 *
	 * @var    JObject
	 * @since  1.7
	 */
	protected $item;

	/**
	 * The state information
	 *
	 * @var    JObject
	 * @since  1.7
	 */
	protected $state;

	/**
	 * Object containing allowed actions
	 *
	 * @var    JObject
	 * @since  2.0
	 */
	protected $canDo;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @since   1.8
	 */
	public function display($tpl = null)
	{
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');
		$this->canDo = PodcastManagerHelper::getActions();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		// Add the component media
		JHtml::_('stylesheet', 'podcastmanager/template.css', false, true, false);

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.7
	 */
	protected function addToolbar()
	{
		$input = JFactory::getApplication('administrator')->input;
		$input->set('hidemainmenu', true);

		$user = JFactory::getUser();
		$userId = $user->get('id');
		$isNew = ($this->item->id == 0);
		$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		$canDo = PodcastManagerHelper::getActions($this->item->id);

		JToolBarHelper::title(JText::_('COM_PODCASTMANAGER_VIEW_FEED_' . ($isNew ? 'ADD_FEED' : 'EDIT_FEED')), 'podcastmanager.png');

		// Set the actions for new and existing records.
		if ($isNew)
		{
			// For new records, check the create permission.
			if ($canDo->get('core.create') || (count(PodcastManagerHelper::getAuthorisedFeeds('core.create')) > 0))
			{
				JToolBarHelper::apply('feed.apply');
				JToolBarHelper::save('feed.save');
				JToolBarHelper::save2new('feed.save2new');
			}

			JToolBarHelper::cancel('feed.cancel');
		}
		else
		{
			// Since it's an existing record, check the edit permission.
			if (!$checkedOut
				&& ($canDo->get('core.edit') || (count(PodcastManagerHelper::getAuthorisedFeeds('core.edit')) > 0)
				|| ($canDo->get('core.edit.own')
				|| (count(PodcastManagerHelper::getAuthorisedFeeds('core.edit.own')) > 0) && $this->item->created_by == $userId)))
			{
				JToolBarHelper::apply('feed.apply');
				JToolBarHelper::save('feed.save');

				// We can save this record, but check the create permission to see if we can return to make a new one.
				if ($canDo->get('core.create') || (count(PodcastManagerHelper::getAuthorisedFeeds('core.create')) > 0))
				{
					JToolBarHelper::save2new('feed.save2new');
				}
			}

			// If an existing item, can save as a copy
			if ($canDo->get('core.create') || (count(PodcastManagerHelper::getAuthorisedFeeds('core.create')) > 0))
			{
				JToolBarHelper::save2copy('feed.save2copy');
			}

			// Add versions toolbar for CMS 3.2+
			if (version_compare(JVERSION, '3.2', 'ge') && $this->state->params->get('save_history', 0) && $canDo->get('core.edit'))
			{
				JToolbarHelper::versions('com_podcastmanager.feed', $this->item->id);
			}

			JToolBarHelper::cancel('feed.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
