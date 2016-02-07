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
 * Podcast Media Folder Controller
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmedia
 * @since       1.6
 */
class PodcastMediaControllerFolder extends JControllerLegacy
{
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

		if (!JFactory::getUser()->authorise('core.delete', 'com_podcastmanager'))
		{
			// User is not authorised to delete
			JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));

			return false;
		}

		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');

		// Initialise variables.
		$ret = true;

		if (count($paths))
		{
			JPluginHelper::importPlugin('content');
			$dispatcher = JEventDispatcher::getInstance();

			foreach ($paths as $path)
			{
				if ($path !== JFile::makeSafe($path))
				{
					$dirname = htmlspecialchars($path, ENT_COMPAT, 'UTF-8');
					JError::raiseWarning(
						100,
						JText::sprintf(
							'COM_PODCASTMEDIA_ERROR_UNABLE_TO_DELETE_FOLDER_WARNDIRNAME',
							substr($dirname, strlen(COM_PODCASTMEDIA_BASE))
						)
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

					$ret &= JFile::delete($object_file->filepath);

					// Trigger the onContentAfterDelete event.
					$dispatcher->trigger('onContentAfterDelete', ['com_podcastmedia.file', &$object_file]);
					$this->setMessage(JText::sprintf('COM_PODCASTMEDIA_DELETE_COMPLETE', substr($object_file->filepath, strlen(COM_PODCASTMEDIA_BASE))));
				}
				elseif (is_dir($object_file->filepath))
				{
					if (count(JFolder::files($object_file->filepath, '.', true, false, ['.svn', 'CVS', '.DS_Store', '__MACOSX'], ['index.html', '^\..*', '.*~'])) == 0)
					{
						// Trigger the onContentBeforeDelete event.
						$result = $dispatcher->trigger('onContentBeforeDelete', ['com_podcastmedia.folder', &$object_file]);

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

						$ret &= !JFolder::delete($object_file->filepath);

						// Trigger the onContentAfterDelete event.
						$dispatcher->trigger('onContentAfterDelete', ['com_podcastmedia.folder', &$object_file]);
						$this->setMessage(JText::sprintf('COM_PODCASTMEDIA_DELETE_COMPLETE', substr($object_file->filepath, strlen(COM_PODCASTMEDIA_BASE))));
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
		}

		return $ret;
	}

	/**
	 * Create a folder
	 *
	 * @return  mixed  Boolean false on failure, void otherwise
	 *
	 * @since   1.6
	 */
	public function create()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$folder = $this->input->getCmd('foldername', '');

		$folderCheck = (string) $this->input->getRaw('foldername', null);
		$parent      = $this->input->getPath('folderbase', '');

		$this->setRedirect('index.php?option=com_podcastmedia&folder=' . $parent . '&tmpl=' . $this->input->getCmd('tmpl', 'index'));

		if (strlen($folder) > 0)
		{
			if (!JFactory::getUser()->authorise('core.create', 'com_podcastmanager'))
			{
				// User is not authorised to delete
				JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_CREATE_NOT_PERMITTED'));

				return false;
			}

			// Set FTP credentials, if given
			JClientHelper::setCredentialsFromRequest('ftp');

			$this->input->set('folder', $parent);

			if (($folderCheck !== null) && ($folder !== $folderCheck))
			{
				$this->setMessage(JText::_('COM_PODCASTMEDIA_ERROR_UNABLE_TO_CREATE_FOLDER_WARNDIRNAME'));

				return false;
			}

			$path = JPath::clean(implode(DIRECTORY_SEPARATOR, [COM_PODCASTMEDIA_BASE, $parent, $folder]));

			if (!is_dir($path) && !is_file($path))
			{
				// Trigger the onContentBeforeSave event.
				$object_file = new JObject(['filepath' => $path]);
				JPluginHelper::importPlugin('content');
				$dispatcher = JEventDispatcher::getInstance();
				$result     = $dispatcher->trigger('onContentBeforeSave', ['com_podcastmedia.folder', &$object_file]);

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

				JFolder::create($object_file->filepath);

				// Trigger the onContentAfterSave event.
				$dispatcher->trigger('onContentAfterSave', ['com_podcastmedia.folder', &$object_file, true]);
				$this->setMessage(JText::sprintf('COM_PODCASTMEDIA_CREATE_COMPLETE', substr($object_file->filepath, strlen(COM_PODCASTMEDIA_BASE))));
			}

			$this->input->set('folder', ($parent) ? $parent . '/' . $folder : $folder);
		}
	}
}
