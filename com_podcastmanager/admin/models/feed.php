<?php
/**
* Podcast Manager for Joomla!
*
* @package     PodcastManager
* @subpackage  com_podcastmanager
*
* @copyright   Copyright (C) 2011 Michael Babker. All rights reserved.
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
			if ($record->published != -2)
			{
				return;
			}
			$user = JFactory::getUser();

			return $user->authorise('core.delete', 'com_podcastmanager.feed.'.(int) $record->id);
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
			return $user->authorise('core.edit.state', 'com_podcastmanager.feed.'.(int) $record->id);
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
	 * @param   string  $group      The component name
	 * @param   int     $client_id  The client ID
	 *
	 * @return  void
	 *
	 * @since   1.7
	 */
	function cleanCache($group = 'com_podcastmanager', $client_id = 1)
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
}
