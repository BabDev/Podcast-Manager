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

jimport( 'joomla.application.component.modeladmin' );

class PodcastManagerModelPodcast extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_PODCASTMANAGER';

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_podcastmanager.podcast', 'podcast', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		// Modify the form based on access controls.
		if (!$this->canEditState((object) $data)) {
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
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param	JTable	A JTable object.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function prepareTable(&$table)
	{
		// Set the publish date to now
		if($table->published == 1 && intval($table->publish_up) == 0) {
			$table->publish_up = JFactory::getDate()->toMySQL();
		}
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 *
	 * @return	JTable	A database object
	 */
	public function getTable($type = 'Podcast', $prefix = 'PodcastManagerTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_podcastmanager.edit.podcast.data', array());

		if (empty($data)) {
			$data = $this->getItem();
			// If changing the selected file, process the new data through getID3
			if (isset($_COOKIE[podManFile])) {
				$data = $this->fillMetaData($data);
			}
		}
		return $data;
	}

	/**
	 * Method to process the file through the getID3 library to extract key data
	 *
	 * @param	mixed	The data object for the form
	 *
	 * @return	mixed	The processed data for the form.
	 * @since	1.6
	 */
	protected function fillMetaData($data)
	{
		jimport('getid3.getid3');
		define('GETID3_HELPERAPPSDIR', JPATH_LIBRARIES.DS.'getid3');

		$filename	= JPATH_ROOT.'/'.$_COOKIE[podManFile];
		$getID3		= new getID3($filename);
		$fileInfo	= $getID3->analyze($filename);

		if (isset($fileInfo['tags_html'])) {
			$t = $fileInfo['tags_html'];
			$tags = isset($t['id3v2']) ? $t['id3v2'] : (isset($t['id3v1']) ? $t['id3v1'] : (isset($t['quicktime']) ? $t['quicktime'] : null));
			if ($tags) {
				if (isset($tags['title'])) {
					$data->title = $tags['title'][0];
				}
				if (isset($tags['album'])) {
					$data->itSubtitle = $tags['album'][0];
				}
				if (isset($tags['artist'])) {
					$data->itAuthor = $tags['artist'][0];
				}
				if (isset($tags['genre'])) {
					$data->itCategory = $tags['genre'][0];
				}
			}
		}

		if (isset($fileInfo['playtime_string'])) {
			$data->itDuration = $fileInfo['playtime_string'];
		}
		return $data;
	}
}
