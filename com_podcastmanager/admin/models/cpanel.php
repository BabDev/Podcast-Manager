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
		$errors              = array();

		// First, make sure the #__content_types table is actually present (3.1+)
		if (version_compare(JVERSION, '3.1', 'ge'))
		{
			$tableList = $db->getTableList();

			if (!in_array($db->replacePrefix('#__content_types'), $tableList))
			{
				$errors['noTypes']   = JText::_('COM_PODCASTMANAGER_MIGRATION_ERROR_CONTENT_TYPES_TABLE');
				$contentTypesPresent = false;
			}
		}

		// Now check for rows if we have content types
		if (version_compare(JVERSION, '3.1', 'ge') && $contentTypesPresent)
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

		// Check to ensure the Isis overrides are installed
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			// This check is done in two steps; first query the database for the record
			try
			{
				/** @var JTableUpdate $table */
				$table = $this->getTable('Update', 'JTable');
				$extensionId = $table->find(array('element' => 'podcastmanager_strapped', 'type' => 'file'));
			}
			catch (RuntimeException $e)
			{
				// The check failed, just set a false flag and move to step two
				$extensionId = false;
			}

			// Step 2 - Check to make sure the cpanel override exists
			$overrideExists = file_exists(JPATH_ADMINISTRATOR . '/templates/isis/html/com_podcastmanager/cpanel/default.php');

			if (!$extensionId && !$overrideExists)
			{
				$errors['noLayouts'] = JText::_('COM_PODCASTMANAGER_MIGRATION_ERROR_NO_LAYOUTS');
			}
		}

		return $errors;
	}
}
