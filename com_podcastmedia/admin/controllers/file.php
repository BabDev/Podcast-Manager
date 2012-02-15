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
class PodcastMediaControllerFile extends JController
{
	/**
	 * Upload a file
	 *
	 * @return  boolean  True on success
	 *
	 * @since   1.6
	 */
	function upload()
	{
		// Check for request forgeries
		JRequest::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));

		// Get the user
		$user = JFactory::getUser();

		// Get some data from the request
		$input = JFactory::getApplication()->input;
		//$file = $input->files->get('Filedata', '', 'array');
		$file = JRequest::getVar('Filedata', '', 'files', 'array');
		$folder = $input->get('folder', '', 'path');
		$return = $input->post->get('return-url', null, 'base64');

		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');

		// Set the redirect
		if ($return)
		{
			$this->setRedirect(base64_decode($return) . '&folder=' . $folder);
		}

		// Make the filename safe
		$file['name'] = JFile::makeSafe($file['name']);

		if (isset($file['name']))
		{
			// The request is valid
			$err = null;
			if (!PodcastMediaHelper::canUpload($file, $err))
			{
				// The file can't be upload
				JError::raiseNotice(100, JText::_($err));
				return false;
			}

			// Remove spaces from the file name for RSS validation
			$filename = str_replace(' ', '_', $file['name']);

			$filepath = JPath::clean(COM_PODCASTMEDIA_BASE . '/' . $folder . '/' . strtolower($filename));

			// Trigger the onContentBeforeSave event.
			JPluginHelper::importPlugin('content');
			$dispatcher = JDispatcher::getInstance();
			$object_file = new JObject($file);
			$object_file->filepath = $filepath;
			$result = $dispatcher->trigger('onContentBeforeSave', array('com_podcastmedia.file', &$object_file));
			if (in_array(false, $result, true))
			{
				// There are some errors in the plugins
				JError::raiseWarning(100, JText::plural('COM_PODCASTMEDIA_ERROR_BEFORE_SAVE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));
				return false;
			}
			$file = (array) $object_file;

			if (JFile::exists($file['filepath']))
			{
				// File exists
				JError::raiseWarning(100, JText::_('COM_PODCASTMEDIA_ERROR_FILE_EXISTS'));
				return false;
			}
			elseif (!$user->authorise('core.create', 'com_podcastmanager'))
			{
				// File does not exist and user is not authorised to create
				JError::raiseWarning(403, JText::_('COM_PODCASTMEDIA_ERROR_CREATE_NOT_PERMITTED'));
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
				return true;
			}
		}
		else
		{
			$this->setRedirect('index.php', JText::_('COM_PODCASTMEDIA_INVALID_REQUEST'), 'error');
			return false;
		}
	}

	/**
	 * Deletes paths from the current path
	 *
	 * @return  boolean  True on success
	 *
	 * @since   1.6
	 */
	function delete()
	{
		JRequest::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));
		$app = JFactory::getApplication();
		$input = $app->input;
		$user = JFactory::getUser();

		// Get some data from the request
		$tmpl = $input->get('tmpl', '', 'cmd');
		$paths = $input->get('rm', array(), 'array');
		$folder = $input->get('folder', '', 'path');

		if ($tmpl == 'component')
		{
			// We are inside the iframe
			$this->setRedirect('index.php?option=com_podcastmedia&view=mediaList&folder=' . $folder . '&tmpl=component');
		}
		else
		{
			$this->setRedirect('index.php?option=com_podcastmedia&folder=' . $folder);
		}

		if (!$user->authorise('core.delete', 'com_podcastmanager'))
		{
			// User is not authorised to delete
			JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));
			return false;
		}
		else
		{
			// Set FTP credentials, if given
			JClientHelper::setCredentialsFromRequest('ftp');

			// Initialise variables.
			$ret = true;

			if (count($paths))
			{
				JPluginHelper::importPlugin('content');
				$dispatcher = JDispatcher::getInstance();
				foreach ($paths as $path)
				{
					if ($path !== JFile::makeSafe($path))
					{
						// filename is not safe
						$filename = htmlspecialchars($path, ENT_COMPAT, 'UTF-8');
						JError::raiseWarning(100, JText::sprintf('COM_PODCASTMEDIA_ERROR_UNABLE_TO_DELETE_FILE_WARNFILENAME', substr($filename, strlen(COM_PODCASTMEDIA_BASE))));
						continue;
					}

					$fullPath = JPath::clean(COM_PODCASTMEDIA_BASE . '/' . $folder . '/' . $path);
					$object_file = new JObject(array('filepath' => $fullPath));
					if (is_file($fullPath))
					{
						// Trigger the onContentBeforeDelete event.
						$result = $dispatcher->trigger('onContentBeforeDelete', array('com_podcastmedia.file', &$object_file));
						if (in_array(false, $result, true))
						{
							// There are some errors in the plugins
							JError::raiseWarning(100, JText::plural('COM_PODCASTMEDIA_ERROR_BEFORE_DELETE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));
							continue;
						}

						$ret = JFile::delete($fullPath);

						// Trigger the onContentAfterDelete event.
						$dispatcher->trigger('onContentAfterDelete', array('com_podcastmedia.file', &$object_file));
						$this->setMessage(JText::sprintf('COM_PODCASTMEDIA_DELETE_COMPLETE', substr($fullPath, strlen(COM_PODCASTMEDIA_BASE))));
					}
					elseif (is_dir($fullPath))
					{
						if (count(JFolder::files($fullPath, '.', true, false, array('.svn', 'CVS', '.DS_Store', '__MACOSX'), array('index.html', '^\..*', '.*~'))) == 0)
						{
							// Trigger the onContentBeforeDelete event.
							$result = $dispatcher->trigger('onContentBeforeDelete', array('com_podcastmedia.folder', &$object_file));
							if (in_array(false, $result, true))
							{
								// There are some errors in the plugins
								JError::raiseWarning(100, JText::plural('COM_PODCASTMEDIA_ERROR_BEFORE_DELETE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));
								continue;
							}

							$ret &= JFolder::delete($fullPath);

							// Trigger the onContentAfterDelete event.
							$dispatcher->trigger('onContentAfterDelete', array('com_podcastmedia.folder', &$object_file));
							$this->setMessage(JText::sprintf('COM_PODCASTMEDIA_DELETE_COMPLETE', substr($fullPath, strlen(COM_PODCASTMEDIA_BASE))));
						}
						else
						{
							//This makes no sense...
							JError::raiseWarning(100, JText::sprintf('COM_PODCASTMEDIA_ERROR_UNABLE_TO_DELETE_FOLDER_NOT_EMPTY', substr($fullPath, strlen(COM_PODCASTMEDIA_BASE))));
						}
					}
				}
			}
			return $ret;
		}
	}
}
