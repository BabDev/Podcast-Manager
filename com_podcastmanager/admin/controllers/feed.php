<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 *
 * @copyright   Copyright (C) 2011-2013 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Feed edit controller class.
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 * @since       1.7
 */
class PodcastManagerControllerFeed extends JControllerForm
{
	/**
	 * Method to check if you can edit an existing record.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key; default is id.
	 *
	 * @return  boolean  True if allowed
	 *
	 * @since   2.0
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Initialise variables.
		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;
		$user = JFactory::getUser();
		$userId = $user->get('id');

		// Check general edit permission first.
		if ($user->authorise('core.edit', 'com_podcastmanager'))
		{
			return true;
		}

		// Check specific edit permission.
		if ($user->authorise('core.edit', 'com_podcastmanager.feed.' . $recordId))
		{
			return true;
		}

		// Fallback on edit.own.
		// First test if the permission is available.
		if ($user->authorise('core.edit.own', 'com_podcastmanager.feed.' . $recordId) || $user->authorise('core.edit.own', 'com_podcastmanager'))
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
			if ($ownerId == $userId)
			{
				return true;
			}
		}

		return false;
	}


	/**
	 * Function that allows child controller access to model data after the data has been saved.
	 *
	 * @param   JModelLegacy  $model      The data model object.
	 * @param   array         $validData  The validated data.
	 *
	 * @return  void
	 *
	 * @since   2.1
	 */
	protected function postSaveHook(JModelLegacy $model, $validData = array())
	{
		// Only supports CMS 3.1+ for now
		if (version_compare(JVERSION, '3.1', 'ge'))
		{
			$item = $model->getItem();

			$id = $item->id;

			if (empty($validData['tags']) && !empty($item->tags))
			{
				$oldTags = new JTags;
				$oldTags->unTagItem($id, 'com_podcastmanager.feed');

				return;
			}

			$tags = $validData['tags'];

			// Store the tag data if the feed was saved.
			if ($tags[0] != '')
			{
				$isNew = $item->id == 0 ? true : false;

				$tagsHelper = new JTags;
				$tagsHelper->tagItem($id, 'com_podcastmanager.feed', $isNew, $item, $tags, null);
			}
		}

		return;
	}
}
