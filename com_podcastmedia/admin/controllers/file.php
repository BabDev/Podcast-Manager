<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmedia
 *
 * @copyright   Copyright (C) 2011-2015 Michael Babker. All rights reserved.
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
	/**
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
		$files        = $this->input->files->get('Filedata', [], 'array');
		$return       = JFactory::getSession()->get('com_podcastmedia.return_url');
		$this->folder = $this->input->getPath('folder', '');

		// Don't redirect to an external URL.
		if (!JUri::isInternal($return))
		{
			$return = '';
		}

		// Set the redirect
		if ($return)
		{
			$this->setRedirect($return . '&folder=' . $this->folder);
		}
		else
		{
			$this->setRedirect('index.php?option=com_podcastmedia&folder=' . $this->folder);
		}

		// Authorize the user
		if (!$this->authoriseUser('create'))
		{
			return false;
		}

		// Total length of post back data in bytes.
		$contentLength = $this->input->server->getUint('CONTENT_LENGTH', 0);

		// Instantiate the media helper
		$mediaHelper = new PodcastMediaHelper;

		// Maximum allowed size of post back data in MB.
		$postMaxSize = $mediaHelper->toBytes(ini_get('post_max_size'));

		// Maximum allowed size of script execution in MB.
		$memoryLimit = $mediaHelper->toBytes(ini_get('memory_limit'));

		// Check for the total size of post back data.
		if (($postMaxSize > 0 && $contentLength > $postMaxSize)
			|| ($memoryLimit != -1 && $contentLength > $memoryLimit))
		{
			JError::raiseWarning(100, JText::_('COM_PODCASTMEDIA_ERROR_WARNFILETOOLARGE'));

			return false;
		}

		$params = JComponentHelper::getParams('com_media');

		$uploadMaxSize     = $params->get('upload_maxsize', 0) * 1024 * 1024;
		$uploadMaxFileSize = $mediaHelper->toBytes(ini_get('upload_max_filesize'));

		// Perform basic checks on file info before attempting anything
		foreach ($files as &$file)
		{
			$file['name']     = JFile::makeSafe(str_replace(' ', '_', $file['name']));
			$file['filepath'] = JPath::clean(implode(DIRECTORY_SEPARATOR, [COM_PODCASTMEDIA_BASE, $this->folder, $file['name']]));

			if (($file['error'] == 1)
				|| ($uploadMaxSize > 0 && $file['size'] > $uploadMaxSize)
				|| ($uploadMaxFileSize > 0 && $file['size'] > $uploadMaxFileSize))
			{
				// File size exceed either 'upload_max_filesize' or 'upload_maxsize'.
				JError::raiseWarning(100, JText::_('COM_PODCASTMEDIA_ERROR_WARNFILETOOLARGE'));

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
		$dispatcher = JEventDispatcher::getInstance();

		foreach ($files as &$file)
		{
			// The request is valid
			$err = null;

			if (!$mediaHelper->canUpload($file, $err))
			{
				// The file can't be uploaded
				JError::raiseNotice(100, JText::_($err));

				return false;
			}

			// Trigger the onContentBeforeSave event.
			$object_file = new JObject($file);
			$result      = $dispatcher->trigger('onContentBeforeSave', ['com_podcastmedia.file', &$object_file]);

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

			if (!JFile::upload($object_file->tmp_name, $object_file->filepath))
			{
				// Error in upload
				JError::raiseWarning(100, JText::_('COM_PODCASTMEDIA_ERROR_UNABLE_TO_UPLOAD_FILE'));

				return false;
			}

			// Trigger the onContentAfterSave event.
			$dispatcher->trigger('onContentAfterSave', ['com_podcastmedia.file', &$object_file, true]);
			$this->setMessage(JText::sprintf('COM_PODCASTMEDIA_UPLOAD_COMPLETE', substr($object_file->filepath, strlen(COM_PODCASTMEDIA_BASE))));
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

		// Get some data from the request
		$tmpl   = $this->input->getCmd('tmpl', '');
		$paths  = $this->input->get('rm', [], 'array');
		$folder = $this->input->getPath('folder', '');

		$redirect = 'index.php?option=com_podcastmedia&folder=' . $folder;

		if ($tmpl == 'component')
		{
			// We are inside the iframe
			$redirect .= '&view=medialist&tmpl=component';
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

		if (!JFactory::getUser()->authorise('core.delete', 'com_podcastmanager'))
		{
			// User is not authorised to delete
			JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));

			return false;
		}

		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');
		JPluginHelper::importPlugin('content');
		$dispatcher = JEventDispatcher::getInstance();

		// Initialise variables.
		$ret = true;
		$app = JFactory::getApplication();

		foreach ($paths as $path)
		{
			if ($path !== JFile::makeSafe($path))
			{
				// Filename is not safe
				$filename = htmlspecialchars($path, ENT_COMPAT, 'UTF-8');

				$app->enqueueMessage(
					JText::sprintf('COM_PODCASTMEDIA_ERROR_UNABLE_TO_DELETE_FILE_WARNFILENAME', substr($filename, strlen(COM_PODCASTMEDIA_BASE))),
					'warning'
				);

				continue;
			}

			$fullPath    = JPath::clean(implode(DIRECTORY_SEPARATOR, [COM_PODCASTMEDIA_BASE, $folder, $path]));
			$object_file = new JObject(['filepath' => $fullPath]);

			if (is_file($object_file->filepath))
			{
				// Trigger the onContentBeforeDelete event.
				$result = $dispatcher->trigger('onContentBeforeDelete', ['com_podcastmedia.file', &$object_file]);

				if (in_array(false, $result, true))
				{
					// There are some errors in the plugins
					$app->enqueueMessage(
						JText::plural('COM_PODCASTMEDIA_ERROR_BEFORE_DELETE', count($errors = $object_file->getErrors()), implode('<br />', $errors)),
						'warning'
					);

					continue;
				}

				$ret &= JFile::delete($object_file->filepath);

				// Trigger the onContentAfterDelete event.
				$dispatcher->trigger('onContentAfterDelete', ['com_podcastmedia.file', &$object_file]);
				$this->setMessage(JText::sprintf('COM_PODCASTMEDIA_DELETE_COMPLETE', substr($object_file->filepath, strlen(COM_PODCASTMEDIA_BASE))));

				continue;
			}

			if (is_dir($object_file->filepath))
			{
				$contents = JFolder::files($object_file->filepath, '.', true, false, ['.svn', 'CVS', '.DS_Store', '__MACOSX', 'index.html']);

				if (!empty($contents))
				{
					// This makes no sense...
					$app->enqueueMessage(
						JText::sprintf(
							'COM_PODCASTMEDIA_ERROR_UNABLE_TO_DELETE_FOLDER_NOT_EMPTY',
							substr($object_file->filepath, strlen(COM_PODCASTMEDIA_BASE))
						),
						'warning'
					);

					continue;
				}

				// Trigger the onContentBeforeDelete event.
				$result = $dispatcher->trigger('onContentBeforeDelete', ['com_podcastmedia.folder', &$object_file]);

				if (in_array(false, $result, true))
				{
					// There are some errors in the plugins
					$app->enqueueMessage(
						JText::plural(
							'COM_PODCASTMEDIA_ERROR_BEFORE_DELETE',
							count($errors = $object_file->getErrors()),
							implode('<br />', $errors)
						),
						'warning'
					);

					continue;
				}

				$ret &= JFolder::delete($object_file->filepath);

				// Trigger the onContentAfterDelete event.
				$dispatcher->trigger('onContentAfterDelete', ['com_podcastmedia.folder', &$object_file]);
				$this->setMessage(JText::sprintf('COM_PODCASTMEDIA_DELETE_COMPLETE', substr($object_file->filepath, strlen(COM_PODCASTMEDIA_BASE))));
			}
		}

		return $ret;
	}
}
