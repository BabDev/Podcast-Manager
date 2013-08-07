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

jimport('joomla.application.component.modeladmin');

/**
 * Feed edit model class.
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 * @since       1.7
 */
class PodcastManagerModelFeed extends JModelAdmin
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.7
	 */
	protected $text_prefix = 'COM_PODCASTMANAGER';

	/**
	 * Model context string.
	 *
	 * @var    string
	 * @since  1.7
	 */
	protected $context = 'com_podcastmanager.feed';

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

				return $user->authorise('core.delete', 'com_podcastmanager.feed.' . (int) $record->id);
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

		// Check for existing feed.
		if (!empty($record->id))
		{
			return $user->authorise('core.edit.state', 'com_podcastmanager.feed.' . (int) $record->id);
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
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm  A JForm object on success, false on failure
	 *
	 * @since   1.7
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_podcastmanager.feed', 'feed', array('control' => 'jform', 'load_data' => $loadData));

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
					$item->tags->getTagIds($item->id, 'com_podcastmanager.feed');
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
	 * @since   1.7
	 */
	public function getTable($type = 'Feed', $prefix = 'PodcastManagerTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.7
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_podcastmanager.edit.feed.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
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

		return $data;
	}
}
