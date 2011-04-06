<?php
/**
* Podcast Manager for Joomla!
*
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
*/

// No direct access
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.error.log');
/**
 * File Media Controller
 *
 * @package		Podcast Manager
 * @subpackage	com_podcastmedia
 * @since		1.6
 */
class PodcastMediaControllerFile extends JController
{
	/**
	 * Upload a file
	 *
	 * @since 1.6
	 */
	function upload()
	{
		// Check for request forgeries
		if (!JRequest::checkToken('request')) {
			$response = array(
				'status' => '0',
				'error' => JText::_('JINVALID_TOKEN')
			);
			echo json_encode($response);
			return;
		}

		// Get the user
		$user		= JFactory::getUser();
		$log		= JLog::getInstance('upload.error.php');

		// Get some data from the request
		$file		= JRequest::getVar('Filedata', '', 'files', 'array');
		$folder		= JRequest::getVar('folder', '', '', 'path');
		$return		= JRequest::getVar('return-url', null, 'post', 'base64');

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		// Make the filename safe
		$file['name']	= JFile::makeSafe($file['name']);

		if (isset($file['name']))
		{
			// The request is valid
			$err = null;

			// Remove spaces from the file name for RSS validation
			$filename	= str_replace(' ', '_', $file[name]);
			
			$filepath = JPath::clean(COM_PODCASTMEDIA_BASE.DS.$folder.DS.strtolower($filename));
			
			if (!PodcastMediaHelper::canUpload($file, $err))
			{
				$log->addEntry(array('comment' => 'Invalid: '.$filepath.': '.$err));
				$response = array(
					'status' => '0',
					'error' => JText::_($err)
				);
				echo json_encode($response);
				return;
			}

			// Trigger the onContentBeforeSave event.
			JPluginHelper::importPlugin('content');
			$dispatcher	= JDispatcher::getInstance();
			$object_file = new JObject($file);
			$object_file->filepath = $filepath;
			$result = $dispatcher->trigger('onContentBeforeSave', array('com_podcastmedia.file', &$object_file));
			if (in_array(false, $result, true)) {
				// There are some errors in the plugins
				$log->addEntry(array('comment' => 'Errors before save: '.$filepath.' : '.implode(', ', $object_file->getErrors())));
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
				$log->addEntry(array('comment' => 'File exists: '.$filepath.' by user_id '.$user->id));
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
				$log->addEntry(array('comment' => 'Create not permitted: '.$filepath.' by user_id '.$user->id));
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
				$log->addEntry(array('comment' => 'Error on upload: '.$filepath));
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
				$log->addEntry(array('comment' => $folder));
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
