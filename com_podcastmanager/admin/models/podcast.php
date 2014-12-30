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

jimport('joomla.application.component.modeladmin');

/**
 * Podcast edit model class.
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 * @since       1.6
 */
class PodcastManagerModelPodcast extends JModelAdmin
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_PODCASTMANAGER';

	/**
	 * Model context string.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $context = 'com_podcastmanager.podcast';

	/**
	 * Method to perform batch operations on an item or a set of items.
	 *
	 * @param   array  $commands  An array of commands to perform.
	 * @param   array  $pks       An array of item ids.
	 * @param   array  $contexts  An array of item contexts.
	 *
	 * @return  boolean  Returns true on success, false on failure.
	 *
	 * @since   1.8
	 */
	public function batch($commands, $pks, $contexts)
	{
		// Sanitize user ids.
		$pks = array_unique($pks);
		JArrayHelper::toInteger($pks);

		// Remove any values of zero.
		if (array_search(0, $pks, true))
		{
			unset($pks[array_search(0, $pks, true)]);
		}

		if (empty($pks))
		{
			$this->setError(JText::_('JGLOBAL_NO_ITEM_SELECTED'));

			return false;
		}

		$done = false;

		if (!empty($commands))
		{
			$cmd = JArrayHelper::getValue($commands, 'move_copy', 'c');

			if ($cmd == 'c')
			{
				$result = $this->batchCopy($commands['feed_id'], $pks, $contexts);

				if (is_array($result))
				{
					$pks = $result;
				}
				else
				{
					return false;
				}
			}
			elseif ($cmd == 'm' && !$this->batchMove($commands['feed_id'], $pks, $contexts))
			{
				return false;
			}

			$done = true;
		}

		if (!empty($commands['language_id']))
		{
			if (!$this->batchLanguage($commands['language_id'], $pks, $contexts))
			{
				return false;
			}

			$done = true;
		}

		if (!empty($commands['tag']))
		{
			if (!$this->batchTag($commands['tag'], $pks, $contexts))
			{
				return false;
			}

			$done = true;
		}

		if (!$done)
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'));

			return false;
		}

		// Clear the cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Batch copy podcasts to a new feed or current.
	 *
	 * @param   integer  $value     The new feed.
	 * @param   array    $pks       An array of row IDs.
	 * @param   array    $contexts  An array of item contexts.
	 *
	 * @return  mixed  An array of new IDs on success, boolean false on failure.
	 *
	 * @since   1.8
	 */
	protected function batchCopy($value, $pks, $contexts)
	{
		$feedId = (int) $value;

		$table = $this->getTable();
		$i = 0;

		// Check that the feed exists
		if ($feedId != '0')
		{
			$feedTable = $this->getTable();

			if (!$feedTable->load($feedId))
			{
				if ($error = $feedTable->getError())
				{
					// Fatal error
					$this->setError($error);

					return false;
				}
				else
				{
					$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_MOVE_CATEGORY_NOT_FOUND'));

					return false;
				}
			}
		}

		if (is_null($feedId))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_MOVE_CATEGORY_NOT_FOUND'));

			return false;
		}

		// Check that the user has create permission for the component
		$user = JFactory::getUser();

		if (!$user->authorise('core.create', 'com_podcastmanager'))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_CREATE'));

			return false;
		}

		// Parent exists so we let's proceed
		while (!empty($pks))
		{
			// Pop the first ID off the stack
			$pk = array_shift($pks);

			$table->reset();

			// Check that the row actually exists
			if (!$table->load($pk))
			{
				if ($error = $table->getError())
				{
					// Fatal error
					$this->setError($error);

					return false;
				}
				else
				{
					// Not fatal error
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					continue;
				}
			}

			// Alter the title & alias
			$data = $this->generateNewTitle($feedId, $table->alias, $table->title);
			$table->title = 'Copy of ' . $table->title;
			$table->alias = $data['1'];

			// Reset the ID because we are making a copy
			$table->id = 0;

			// New feed ID
			$table->feedname = $feedId;

			// Check the row.
			if (!$table->check())
			{
				$this->setError($table->getError());

				return false;
			}

			// Store the row.
			if (!$table->store())
			{
				$this->setError($table->getError());

				return false;
			}

			// Get the new item ID
			$newId = $table->get('id');

			// Add the new ID to the array
			$newIds[$i]	= $newId;
			$i++;
		}

		// Clean the cache
		$this->cleanCache();

		return $newIds;
	}

	/**
	 * Batch move podcasts to a new feed
	 *
	 * @param   integer  $value     The new feed ID.
	 * @param   array    $pks       An array of row IDs.
	 * @param   array    $contexts  An array of item contexts.
	 *
	 * @return  boolean  True if successful, false otherwise and internal error is set.
	 *
	 * @since   1.8
	 */
	protected function batchMove($value, $pks, $contexts)
	{
		$feedId = (int) $value;

		$table = $this->getTable();

		// Check that the feed exists
		if ($feedId != '0')
		{
			$feedTable = $this->getTable();

			if (!$feedTable->load($feedId))
			{
				if ($error = $feedTable->getError())
				{
					// Fatal error
					$this->setError($error);

					return false;
				}
				else
				{
					$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_MOVE_CATEGORY_NOT_FOUND'));

					return false;
				}
			}
		}

		if (is_null($feedId))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_MOVE_CATEGORY_NOT_FOUND'));

			return false;
		}

		// Check that user has create and edit permission for the component
		$user = JFactory::getUser();

		if (!$user->authorise('core.create', 'com_podcastmanager'))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_CREATE'));

			return false;
		}

		if (!$user->authorise('core.edit', 'com_podcastmanager'))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));

			return false;
		}

		// Parent exists so we let's proceed
		foreach ($pks as $pk)
		{
			// Check that the row actually exists
			if (!$table->load($pk))
			{
				if ($error = $table->getError())
				{
					// Fatal error
					$this->setError($error);

					return false;
				}
				else
				{
					// Not fatal error
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					continue;
				}
			}

			// Set the new feed ID
			$table->feedname = $feedId;

			// Check the row.
			if (!$table->check())
			{
				$this->setError($table->getError());

				return false;
			}

			// Store the row.
			if (!$table->store())
			{
				$this->setError($table->getError());

				return false;
			}
		}

		// Clean the cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission for the component.
	 *
	 * @since   2.0
	 */
	protected function canDelete($record)
	{
		if (!empty($record->id))
		{
			if ($record->published == -2)
			{
				$user = JFactory::getUser();

				return $user->authorise('core.delete', 'com_podcastmanager.podcast.' . (int) $record->id);
			}
		}
	}

	/**
	 * Method to test whether a record's state can be modified.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission for the component.
	 *
	 * @since   2.0
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		// Check for existing podcast.
		if (!empty($record->id))
		{
			return $user->authorise('core.edit.state', 'com_podcastmanager.podcast.' . (int) $record->id);
		}

		// Default to component settings if no feed to check.
		else
		{
			return $user->authorise('core.edit.state', 'com_podcastmanager');
		}
	}

	/**
	 * Custom clean cache method
	 *
	 * @param   string   $group      The component name
	 * @param   integer  $client_id  The client ID
	 *
	 * @return  void
	 *
	 * @since   1.7
	 */
	protected function cleanCache($group = 'com_podcastmanager', $client_id = 1)
	{
		parent::cleanCache($group, $client_id);
	}

	/**
	 * Method to change the title & alias.
	 *
	 * @param   integer  $category_id  The id of the feed.
	 * @param   string   $alias        The alias.
	 * @param   string   $title        The title.
	 *
	 * @return	array  Contains the modified title and alias.
	 *
	 * @since	2.1
	 */
	protected function generateNewTitle($category_id, $alias, $title)
	{
		// Alter the title & alias
		$table = $this->getTable();

		while ($table->load(array('alias' => $alias, 'feedname' => $category_id)))
		{
			$title = JString::increment($title);
			$alias = JString::increment($alias, 'dash');
		}

		return array($title, $alias);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_podcastmanager.podcast', 'podcast', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		// Modify the form based on access controls.
		if (!$this->canEditState((object) $data))
		{
			// Disable fields for display.
			$form->setFieldAttribute('publish_up', 'disabled', 'true');
			$form->setFieldAttribute('published', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('publish_up', 'filter', 'unset');
			$form->setFieldAttribute('published', 'filter', 'unset');
		}

		return $form;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 *
	 * @since   2.1
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk))
		{
			// Convert the metadata field to an array.
			$registry = new JRegistry;
			$registry->loadString($item->metadata);
			$item->metadata = $registry->toArray();

			// Tags support in CMS 3.1+
			if (version_compare(JVERSION, '3.1', 'ge'))
			{
				if (!empty($item->id))
				{
					$item->tags = new JHelperTags;
					$item->tags->getTagIds($item->id, 'com_podcastmanager.podcast');
					$item->metadata['tags'] = $item->tags;
				}
			}
		}

		return $item;
	}

	/**
	 * Returns a JTable object, always creating it
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A database object
	 *
	 * @since   1.6
	 */
	public function getTable($type = 'Podcast', $prefix = 'PodcastManagerTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_podcastmanager.edit.podcast.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param   JTable  &$table  A JTable object.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function prepareTable(&$table)
	{
		// Set the publish date to now
		if ($table->published == 1 && intval($table->publish_up) == 0)
		{
			$table->publish_up = JFactory::getDate()->toSql();
		}
	}

	/**
	 * Method to allow derived classes to preprocess the form.
	 *
	 * @param   JForm   $form   A JForm object.
	 * @param   mixed   $data   The data expected for the form.
	 * @param   string  $group  The name of the plugin group to import (defaults to "content").
	 *
	 * @return  void
	 *
	 * @see     JFormField
	 * @since   2.1
	 * @throws  Exception if there is an error in the form event.
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'content')
	{
		// Add tags for CMS 3.1 and later
		if (version_compare(JVERSION, '3.1', 'ge'))
		{
			$form->setField(
				new SimpleXMLElement(
					'<fields name="metadata"><fieldset name="jmetadata" label="JGLOBAL_FIELDSET_METADATA_OPTIONS">'
					. '<field name="tags" type="tag" label="JTAG" description="JTAG_DESC" class="inputbox" multiple="true" /></fieldset></fields>'
				)
			);
		}

		// Add version note for CMS 3.2 and later
		if (version_compare(JVERSION, '3.2', 'ge'))
		{
			$form->setField(
				new SimpleXMLElement(
					'<field name="version_note" type="text" label="JGLOBAL_FIELD_VERSION_NOTE_LABEL" description="JGLOBAL_FIELD_VERSION_NOTE_DESC"'
					. ' class="inputbox" size="45" labelclass="control-label" />'
				)
			);
		}

		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * Method to validate the form data.
	 *
	 * @param   JForm   $form   The form to validate against.
	 * @param   array   $data   The data to validate.
	 * @param   string  $group  The name of the field group to validate.
	 *
	 * @return  mixed  Array of filtered data if valid, false otherwise.
	 *
	 * @see     JFormRule
	 * @see     JFilterInput
	 * @since   2.1
	 */
	public function validate($form, $data, $group = null)
	{
		$data = parent::validate($form, $data, $group);

		// Tags B/C break at 3.1.2
		if (version_compare(JVERSION, '3.1.2', 'ge'))
		{
			if (isset($data['metadata']['tags']))
			{
				$data['tags'] = $data['metadata']['tags'];
			}
		}

		// If there's a space in the filename, notify the user but allow saving
		if (strpos($table->filename, ' ') !== false)
		{
			JFactory::getApplication()->enqueueMessage(
				JText::_('COM_PODCASTMANAGER_SPACE_IN_FILENAME'),
				'warning'
			);
		}

		return $data;
	}
}
