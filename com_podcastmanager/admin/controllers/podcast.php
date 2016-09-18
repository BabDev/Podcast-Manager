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

use Joomla\Utilities\ArrayHelper;

/**
 * Podcast edit controller class.
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 * @since       1.6
 */
class PodcastManagerControllerPodcast extends JControllerForm
{
	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param   array  $data  An array of input data.
	 *
	 * @return  boolean  True if allowed
	 *
	 * @since   2.0
	 */
	protected function allowAdd($data = [])
	{
		// Initialise variables.
		$feedId = ArrayHelper::getValue($data, 'feedname', $this->input->get('filter_feedname', '', 'int'), 'int');

		if ($feedId)
		{
			// If the feed has been passed in the data or URL check it.
			return JFactory::getUser()->authorise('core.create', 'com_podcastmanager.feed.' . $feedId);
		}

		// In the absence of better information, revert to the component permissions.
		return parent::allowAdd();
	}

	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key.
	 *
	 * @return  boolean  True if allowed
	 *
	 * @since   2.0
	 */
	protected function allowEdit($data = [], $key = 'id')
	{
		// Initialise variables.
		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;
		$user     = JFactory::getUser();

		// Check feed edit permission.
		if ($user->authorise('core.edit', 'com_podcastmanager.podcast.' . $recordId))
		{
			return true;
		}

		// Fallback on edit.own.
		// First test if the permission is available.
		if ($user->authorise('core.edit.own', 'com_podcastmanager.podcast.' . $recordId) || $user->authorise('core.edit.own', 'com_podcastmanager'))
		{
			// Now test the owner is the user.
			$ownerId = (int) isset($data['created_by']) ? $data['created_by'] : 0;

			if (empty($ownerId) && $recordId)
			{
				// Need to do a lookup from the model.
				$record = $this->getModel()->getItem($recordId);

				if (empty($record))
				{
					return false;
				}

				$ownerId = $record->created_by;
			}

			// If the owner matches 'me' then do the test.
			if ($ownerId == $user->id)
			{
				return true;
			}
		}

		// Check component permissions if a record is not available
		return parent::allowEdit($data, $key);
	}

	/**
	 * Method to run batch operations.
	 *
	 * @param   object  $model  The model of the component being processed.
	 *
	 * @return  boolean  True if successful, false otherwise and internal error is set.
	 *
	 * @since   1.8
	 */
	public function batch($model = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set the model
		$model = $this->getModel('Podcast', 'PodcastManagerModel', array());

		// Preset the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_podcastmanager&view=podcasts' . $this->getRedirectToListAppend(), false));

		return parent::batch($model);
	}
}
