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
 * Control Panel model class.
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 * @since       2.2
 */
class PodcastManagerModelCpanel extends JModelLegacy
{
	/**
	 * Checks for migration errors in Joomla! 3 installations
	 *
	 * @return  array
	 *
	 * @since   2.2
	 */
	public function getMigrationErrors()
	{
		$db                  = $this->getDbo();
		$contentTypesPresent = true;
		$errors              = [];

		$tableList = $db->getTableList();

		if (!in_array($db->replacePrefix('#__content_types'), $tableList))
		{
			$errors['noTypes']   = JText::_('COM_PODCASTMANAGER_MIGRATION_ERROR_CONTENT_TYPES_TABLE');
			$contentTypesPresent = false;
		}

		// Now check for rows if we have content types
		if ($contentTypesPresent)
		{
			$feedTypeId = $db->setQuery(
				$db->getQuery(true)
					->select($db->quoteName('type_id'))
					->from($db->quoteName('#__content_types'))
					->where($db->quoteName('type_alias') . ' = ' . $db->quote('com_podcastmanager.feed'))
			)->loadResult();

			$podcastTypeId = $db->setQuery(
				$db->getQuery(true)
					->select($db->quoteName('type_id'))
					->from($db->quoteName('#__content_types'))
					->where($db->quoteName('type_alias') . ' = ' . $db->quote('com_podcastmanager.podcast'))
			)->loadResult();

			if (!$feedTypeId)
			{
				$errors['noFeedType'] = JText::_('COM_PODCASTMANAGER_MIGRATION_ERROR_NO_FEED_TYPE');
			}

			if (!$podcastTypeId)
			{
				$errors['noPodcastType'] = JText::_('COM_PODCASTMANAGER_MIGRATION_ERROR_NO_PODCAST_TYPE');
			}
		}

		return $errors;
	}
}
