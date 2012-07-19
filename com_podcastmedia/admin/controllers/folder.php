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

		$input = JFactory::getApplication()->input;
		$user  = JFactory::getUser();

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

		if (!$user->authorise('core.delete', 'com_podcastmanager'))
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
			$dispatcher = JDispatcher::getInstance();
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
					if (count(JFolder::files($fullPath, '.', true, false, array('.svn', 'CVS', '.DS_Store', '__MACOSX'), array('index.html', '^\..*', '.*~'))) == 0)
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

						$ret &= !JFolder::delete($fullPath);

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

		$user = JFactory::getUser();

		$input       = JFactory::getApplication()->input;
		$folder      = $input->get('foldername', '', 'cmd');
		$folderCheck = JRequest::getVar('foldername', null, '', 'string', JREQUEST_ALLOWRAW);
		$parent      = $input->get('folderbase', '', 'path');

		$this->setRedirect('index.php?option=com_podcastmedia&folder=' . $parent . '&tmpl=' . $input->get('tmpl', 'index', 'cmd'));

		if (strlen($folder) > 0)
		{
			if (!$user->authorise('core.create', 'com_podcastmanager'))
			{
				// User is not authorised to delete
				JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_CREATE_NOT_PERMITTED'));
				return false;
			}

			// Set FTP credentials, if given
			JClientHelper::setCredentialsFromRequest('ftp');

			$input->set('folder', $parent);

			if (($folderCheck !== null) && ($folder !== $folderCheck))
			{
				$this->setMessage(JText::_('COM_PODCASTMEDIA_ERROR_UNABLE_TO_CREATE_FOLDER_WARNDIRNAME'));
				return false;
			}

			$path = JPath::clean(implode(DIRECTORY_SEPARATOR, array(COM_PODCASTMEDIA_BASE, $parent, $folder)));
			if (!is_dir($path) && !is_file($path))
			{
				// Trigger the onContentBeforeSave event.
				$object_file = new JObject(array('filepath' => $path));
				JPluginHelper::importPlugin('content');
				$dispatcher = JDispatcher::getInstance();
				$result = $dispatcher->trigger('onContentBeforeSave', array('com_podcastmedia.folder', &$object_file));
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

				JFolder::create($path);
				$data = '<!DOCTYPE html><title></title>';
				JFile::write($path . '/' . 'index.html', $data);

				// Trigger the onContentAfterSave event.
				$dispatcher->trigger('onContentAfterSave', array('com_podcastmedia.folder', &$object_file, true));
				$this->setMessage(JText::sprintf('COM_PODCASTMEDIA_CREATE_COMPLETE', substr($path, strlen(COM_PODCASTMEDIA_BASE))));
			}
			$input->set('folder', ($parent) ? $parent . '/' . $folder : $folder);
		}
	}
}
