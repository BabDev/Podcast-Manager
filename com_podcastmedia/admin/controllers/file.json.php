<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmedia
 *
 * @copyright   Copyright (C) 2011-2012 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Podcast Media File Controller for JSON response
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmedia
 * @since       1.6
 */
class PodcastMediaControllerFile extends JController
{
	/**
	 * Upload a file
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function upload()
	{
		static $log;

		if ($log == null)
		{
			$options['format'] = '{DATE}\t{TIME}\t{LEVEL}\t{CODE}\t{MESSAGE}';
			$options['text_file'] = 'podcastmedia_upload.error.php';
			$log = JLog::addLogger($options);
		}

		// Check for request forgeries
		if (!JSession::checkToken('request'))
		{
			$response = array(
				'status' => '0',
				'error' => JText::_('JINVALID_TOKEN')
			);
			echo json_encode($response);
			return;
		}

		// Get the user
		$user = JFactory::getUser();

		// Get some data from the request
		$input = JFactory::getApplication()->input;
		// $file = $input->files->get('Filedata', '', 'array');
		$file = JRequest::getVar('Filedata', '', 'files', 'array');
		$folder = $input->get('folder', '', 'path');

		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');

		// Make the filename safe
		$file['name'] = JFile::makeSafe($file['name']);

		if (isset($file['name']))
		{
			// The request is valid
			$err = null;

			// Remove spaces from the file name for RSS validation
			$filename = str_replace(' ', '_', $file['name']);

			$filepath = JPath::clean(COM_PODCASTMEDIA_BASE . '/' . $folder . '/' . strtolower($filename));

			if (!PodcastMediaHelper::canUpload($file, $err))
			{
				JLog::add('Invalid: ' . $filepath . ': ' . $err, JLog::ERROR);
				$response = array(
					'status' => '0',
					'error' => JText::_($err)
				);
				echo json_encode($response);
				return;
			}

			// Trigger the onContentBeforeSave event.
			JPluginHelper::importPlugin('content');
			$dispatcher = JDispatcher::getInstance();
			$object_file = new JObject($file);
			$object_file->filepath = $filepath;
			$result = $dispatcher->trigger('onContentBeforeSave', array('com_podcastmedia.file', &$object_file));
			if (in_array(false, $result, true))
			{
				// There are some errors in the plugins
				JLog::add('Errors before save: ' . $filepath . ' : ' . implode(', ', $object_file->getErrors()), JLog::ERROR);
				$response = array(
					'status' => '0',
					'error' => JText::plural('COM_PODCASTMEDIA_ERROR_BEFORE_SAVE', count($errors = $object_file->getErrors()), implode('<br />', $errors))
				);
				echo json_encode($response);
				return;
			}

			if (JFile::exists($filepath))
			{
				// File exists
				JLog::add('File exists: ' . $filepath . ' by user_id ' . $user->id, JLog::ERROR);
				$response = array(
					'status' => '0',
					'error' => JText::_('COM_PODCASTMEDIA_ERROR_FILE_EXISTS')
				);
				echo json_encode($response);
				return;
			}
			elseif (!$user->authorise('core.create', 'com_podcastmanager'))
			{
				// File does not exist and user is not authorised to create
				JLog::add('Create not permitted: ' . $filepath . ' by user_id ' . $user->id, JLog::ERROR);
				$response = array(
					'status' => '0',
					'error' => JText::_('COM_PODCASTMEDIA_ERROR_CREATE_NOT_PERMITTED')
				);
				echo json_encode($response);
				return;
			}

			$file = (array) $object_file;
			if (!JFile::upload($file['tmp_name'], $file['filepath']))
			{
				// Error in upload
				JLog::add('Error on upload: ' . $filepath, JLog::ERROR);
				$response = array(
					'status' => '0',
					'error' => JText::_('COM_PODCASTMEDIA_ERROR_UNABLE_TO_UPLOAD_FILE')
				);
				echo json_encode($response);
				return;
			}
			else
			{
				// Trigger the onContentAfterSave event.
				$dispatcher->trigger('onContentAfterSave', array('com_podcastmedia.file', &$object_file, true));
				JLog::add($folder, JLog::INFO);
				$response = array(
					'status' => '1',
					'error' => JText::sprintf('COM_PODCASTMEDIA_UPLOAD_COMPLETE', substr($file['filepath'], strlen(COM_PODCASTMEDIA_BASE)))
				);
				echo json_encode($response);
				return;
			}
		}
		else
		{
			$response = array(
				'status' => '0',
				'error' => JText::_('COM_PODCASTMEDIA_ERROR_BAD_REQUEST')
			);

			echo json_encode($response);
			return;
		}
	}
}
