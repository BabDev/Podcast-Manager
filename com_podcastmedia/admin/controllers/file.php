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
 * Podcast Media File Controller
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmedia
 * @since       1.6
 */
class PodcastMediaControllerFile extends JControllerLegacy
{
	/*
	 * The folder we are uploading into
	 *
	 * @var    string
	 * @since  2.1
	 */
	protected $folder = '';

	/**
	 * Check that the user is authorized to perform this action
	 *
	 * @param   string  $action  The action being performed
	 *
	 * @return  boolean  True if authorised
	 *
	 * @since   2.1
	 */
	protected function authoriseUser($action)
	{
		if (!JFactory::getUser()->authorise('core.' . strtolower($action), 'com_podcastmanager'))
		{
			// User is not authorised
			JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_' . strtoupper($action) . '_NOT_PERMITTED'));
			return false;
		}

		return true;
	}

	/**
	 * Upload a file
	 *
	 * @return  boolean  True on success
	 *
	 * @since   1.6
	 */
	public function upload()
	{
		// Check for request forgeries
		JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));

		// Get some data from the request
		$input        = JFactory::getApplication()->input;
		// $files        = $input->files->get('Filedata', '', 'array');
		$files        = JRequest::getVar('Filedata', '', 'files', 'array');
		$this->folder = $input->get('folder', '', 'path');
		$return       = $input->post->get('return-url', null, 'base64');

		// Set the redirect
		if ($return)
		{
			$this->setRedirect(base64_decode($return) . '&folder=' . $this->folder);
		}

		// Authorize the user
		if (!$this->authoriseUser('create'))
		{
			return false;
		}

		$params = JComponentHelper::getParams('com_media');

		if (
			$_SERVER['CONTENT_LENGTH'] > ($params->get('upload_maxsize', 0) * 1024 * 1024) ||
			$_SERVER['CONTENT_LENGTH'] > (int) (ini_get('upload_max_filesize')) * 1024 * 1024 ||
			$_SERVER['CONTENT_LENGTH'] > (int) (ini_get('post_max_size')) * 1024 * 1024 ||
			$_SERVER['CONTENT_LENGTH'] > (int) (ini_get('memory_limit')) * 1024 * 1024
		)
		{
			JError::raiseWarning(100, JText::_('COM_PODCASTMEDIA_ERROR_WARNFILETOOLARGE'));
			return false;
		}

		/*
		 * Input is in the form of an associative array containing numerically indexed arrays
		 * We want a numerically indexed array containing associative arrays
		 * Cast each item as array in case the Filedata parameter was not sent as such
		 */
		$files = array_map(
			array($this, 'reformatFilesArray'),
			(array) $files['name'],
			(array) $files['type'],
			(array) $files['tmp_name'],
			(array) $files['error'],
			(array) $files['size']
		);

		// Perform basic checks on file info before attempting anything
		foreach ($files as &$file)
		{
			if ($file['error'] == 1)
			{
				JError::raiseWarning(100, JText::_('COM_PODCASTMEDIA_ERROR_WARNFILETOOLARGE'));
				return false;
			}

			if ($file['size'] > ($params->get('upload_maxsize', 0) * 1024 * 1024))
			{
				JError::raiseNotice(100, JText::_('COM_PODCASTMEDIA_ERROR_WARNFILETOOLARGE'));
				return false;
			}

			if (is_file($file['filepath']))
			{
				// A file with this name already exists
				JError::raiseWarning(100, JText::_('COM_PODCASTMEDIA_ERROR_FILE_EXISTS'));
				return false;
			}

			if (!isset($file['name']))
			{
				// No filename (after the name was cleaned by JFile::makeSafe)
				$this->setRedirect('index.php', JText::_('COM_PODCASTMEDIA_INVALID_REQUEST'), 'error');
				return false;
			}
		}

		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');
		JPluginHelper::importPlugin('content');
		$dispatcher = JDispatcher::getInstance();

		foreach ($files as &$file)
		{
			// The request is valid
			$err = null;
			if (!PodcastMediaHelper::canUpload($file, $err))
			{
				// The file can't be upload
				JError::raiseNotice(100, JText::_($err));
				return false;
			}

			// Trigger the onContentBeforeSave event.
			$object_file = new JObject($file);
			$result = $dispatcher->trigger('onContentBeforeSave', array('com_podcastmedia.file', &$object_file));
			if (in_array(false, $result, true))
			{
				// There are some errors in the plugins
				JError::raiseWarning(
					100,
					JText::plural(
						'COM_PODCASTMEDIA_ERROR_BEFORE_SAVE',
						count($errors = $object_file->getErrors()),
						implode('<br />', $errors)
					)
				);
				return false;
			}

			if (!JFile::upload($file['tmp_name'], $file['filepath']))
			{
				// Error in upload
				JError::raiseWarning(100, JText::_('COM_PODCASTMEDIA_ERROR_UNABLE_TO_UPLOAD_FILE'));
				return false;
			}
			else
			{
				// Trigger the onContentAfterSave event.
				$dispatcher->trigger('onContentAfterSave', array('com_podcastmedia.file', &$object_file, true));
				$this->setMessage(JText::sprintf('COM_PODCASTMEDIA_UPLOAD_COMPLETE', substr($file['filepath'], strlen(COM_PODCASTMEDIA_BASE))));
			}
		}

		return true;
	}

	/**
	 * Deletes paths from the current path
	 *
	 * @return  boolean  True on success
	 *
	 * @since   1.6
	 */
	public function delete()
	{
		JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));
		$app = JFactory::getApplication();
		$input = $app->input;
		$user = JFactory::getUser();

		// Get some data from the request
		$tmpl   = $input->get('tmpl', '', 'cmd');
		$paths  = $input->get('rm', array(), 'array');
		$folder = $input->get('folder', '', 'path');

		$redirect = 'index.php?option=com_podcastmedia&folder=' . $folder;
		if ($tmpl == 'component')
		{
			// We are inside the iframe
			$redirect .= '&view=mediaList&tmpl=component';
		}
		$this->setRedirect($redirect);

		// Nothing to delete
		if (empty($paths))
		{
			return true;
		}

		// Authorize the user
		if (!$this->authoriseUser('delete'))
		{
			return false;
		}

		if (!$user->authorise('core.delete', 'com_podcastmanager'))
		{
			// User is not authorised to delete
			JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));
			return false;
		}

		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');
		JPluginHelper::importPlugin('content');
		$dispatcher	= JDispatcher::getInstance();

		// Initialise variables.
		$ret = true;
		foreach ($paths as $path)
		{
			if ($path !== JFile::makeSafe($path))
			{
				// filename is not safe
				$filename = htmlspecialchars($path, ENT_COMPAT, 'UTF-8');
				JError::raiseWarning(
					100,
					JText::sprintf(
						'COM_PODCASTMEDIA_ERROR_UNABLE_TO_DELETE_FILE_WARNFILENAME',
						substr($filename, strlen(COM_PODCASTMEDIA_BASE))
					)
				);
				continue;
			}

			$fullPath = JPath::clean(implode(DIRECTORY_SEPARATOR, array(COM_PODCASTMEDIA_BASE, $folder, $path)));
			$object_file = new JObject(array('filepath' => $fullPath));
			if (is_file($fullPath))
			{
				// Trigger the onContentBeforeDelete event.
				$result = $dispatcher->trigger('onContentBeforeDelete', array('com_podcastmedia.file', &$object_file));
				if (in_array(false, $result, true))
				{
					// There are some errors in the plugins
					JError::raiseWarning(
						100,
						JText::plural(
							'COM_PODCASTMEDIA_ERROR_BEFORE_DELETE',
							count($errors = $object_file->getErrors()),
							implode('<br />', $errors)
						)
					);
					continue;
				}

				$ret &= JFile::delete($fullPath);

				// Trigger the onContentAfterDelete event.
				$dispatcher->trigger('onContentAfterDelete', array('com_podcastmedia.file', &$object_file));
				$this->setMessage(JText::sprintf('COM_PODCASTMEDIA_DELETE_COMPLETE', substr($fullPath, strlen(COM_PODCASTMEDIA_BASE))));
			}
			elseif (is_dir($fullPath))
			{
				$contents = JFolder::files($fullPath, '.', true, false, array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'index.html'));
				if (empty($contents))
				{
					// Trigger the onContentBeforeDelete event.
					$result = $dispatcher->trigger('onContentBeforeDelete', array('com_podcastmedia.folder', &$object_file));
					if (in_array(false, $result, true))
					{
						// There are some errors in the plugins
						JError::raiseWarning(
							100,
							JText::plural(
								'COM_PODCASTMEDIA_ERROR_BEFORE_DELETE',
								count($errors = $object_file->getErrors()),
								implode('<br />', $errors)
							)
						);
						continue;
					}

					$ret &= JFolder::delete($fullPath);

					// Trigger the onContentAfterDelete event.
					$dispatcher->trigger('onContentAfterDelete', array('com_podcastmedia.folder', &$object_file));
					$this->setMessage(JText::sprintf('COM_PODCASTMEDIA_DELETE_COMPLETE', substr($fullPath, strlen(COM_PODCASTMEDIA_BASE))));
				}
				else
				{
					// This makes no sense...
					JError::raiseWarning(
						100,
						JText::sprintf(
							'COM_PODCASTMEDIA_ERROR_UNABLE_TO_DELETE_FOLDER_NOT_EMPTY',
							substr($fullPath, strlen(COM_PODCASTMEDIA_BASE))
						)
					);
				}
			}
		}

		return $ret;
	}

	/**
	 * Used as a callback for array_map, turns the multi-file input array into a sensible array of files
	 * Also, removes illegal characters from the 'name' and sets a 'filepath' as the final destination of the file
	 *
	 * @param   string  $name      The file name
	 * @param   string	$type      The file type
	 * @param   string	$tmp_name  The temporary name of the file
	 * @param   string	$error     Error information about the file
	 * @param   string	$size      The file size
	 *
	 * @return  array  Array containing the file information
	 *
	 * @since   2.1
	 */
	protected function reformatFilesArray($name, $type, $tmp_name, $error, $size)
	{
		$name = JFile::makeSafe($name);

		// Remove spaces from the name to be RSS compliant
		$name = str_replace(' ', '_', $name);

		return array(
			'name'		=> $name,
			'type'		=> $type,
			'tmp_name'	=> $tmp_name,
			'error'		=> $error,
			'size'		=> $size,
			'filepath'	=> JPath::clean(implode(DIRECTORY_SEPARATOR, array(COM_PODCASTMEDIA_BASE, $this->folder, $name)))
		);
	}
}
