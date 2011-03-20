<?php
/**
* Podcast Manager for Joomla!
*
* @version		$Id$
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
*/

// No direct access
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Folder Media Controller
 *
 * @package		Podcast Manager
 * @subpackage	com_podcastmedia
 * @since		1.6
 */
class PodcastMediaControllerFolder extends JController
{

	/**
	 * Deletes paths from the current path
	 *
	 * @param string $listFolder The directory to delete a file from
	 * @since 1.6
	 */
	function delete()
	{
		JRequest::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));

		$user	= JFactory::getUser();

		// Get some data from the request
		$tmpl	= JRequest::getCmd('tmpl');
		$paths	= JRequest::getVar('rm', array(), '', 'array');
		$folder = JRequest::getVar('folder', '', '', 'path');

		if ($tmpl == 'component') {
			// We are inside the iframe
			$this->setRedirect('index.php?option=com_podcastmedia&view=mediaList&folder='.$folder.'&tmpl=component');
		} else {
			$this->setRedirect('index.php?option=com_podcastmedia&folder='.$folder);
		}

		if (!$user->authorise('core.delete','com_podcastmanager'))
		{
			// User is not authorised to delete
			JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));
			return false;
		}
		else
		{
			// Set FTP credentials, if given
			jimport('joomla.client.helper');
			JClientHelper::setCredentialsFromRequest('ftp');

			// Initialise variables.
			$ret = true;

			if (count($paths)) {
				JPluginHelper::importPlugin('content');
				$dispatcher	= JDispatcher::getInstance();
				foreach ($paths as $path) {
					if ($path !== JFile::makeSafe($path)) {
						$dirname = htmlspecialchars($path, ENT_COMPAT, 'UTF-8');
						JError::raiseWarning(100, JText::sprintf('COM_PODCASTMEDIA_ERROR_UNABLE_TO_DELETE_FOLDER_WARNDIRNAME', substr($dirname, strlen(COM_PODCASTMEDIA_BASE))));
						continue;
					}

					$fullPath = JPath::clean(COM_PODCASTMEDIA_BASE.DS.$folder.DS.$path);
					$object_file = new JObject(array('filepath' => $fullPath));
					if (is_file($fullPath))
					{
						// Trigger the onContentBeforeDelete event.
						$result = $dispatcher->trigger('onContentBeforeDelete', array('com_podcastmedia.file', &$object_file));
						if (in_array(false, $result, true)) {
							// There are some errors in the plugins
							JError::raiseWarning(100, JText::plural('COM_PODCASTMEDIA_ERROR_BEFORE_DELETE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));
							continue;
						}

						$ret &= JFile::delete($fullPath);

						// Trigger the onContentAfterDelete event.
						$dispatcher->trigger('onContentAfterDelete', array('com_podcastmedia.file', &$object_file));
						$this->setMessage(JText::sprintf('COM_PODCASTMEDIA_DELETE_COMPLETE', substr($fullPath, strlen(COM_PODCASTMEDIA_BASE))));
					}
					else if (is_dir($fullPath))
					{
						if (count(JFolder::files($fullPath, '.', true, false, array('.svn', 'CVS','.DS_Store','__MACOSX'), array('index.html', '^\..*','.*~'))) == 0)
						{
							// Trigger the onContentBeforeDelete event.
							$result = $dispatcher->trigger('onContentBeforeDelete', array('com_podcastmedia.folder', &$object_file));
							if (in_array(false, $result, true)) {
								// There are some errors in the plugins
								JError::raiseWarning(100, JText::plural('COM_PODCASTMEDIA_ERROR_BEFORE_DELETE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));
								continue;
							}

							$ret &= !JFolder::delete($fullPath);

							// Trigger the onContentAfterDelete event.
							$dispatcher->trigger('onContentAfterDelete', array('com_podcastmedia.folder', &$object_file));
							$this->setMessage(JText::sprintf('COM_PODCASTMEDIA_DELETE_COMPLETE', substr($fullPath, strlen(COM_PODCASTMEDIA_BASE))));
						}
						else
						{
							//This makes no sense...
							JError::raiseWarning(100, JText::sprintf('COM_PODCASTMEDIA_ERROR_UNABLE_TO_DELETE_FOLDER_NOT_EMPTY',substr($fullPath, strlen(COM_PODCASTMEDIA_BASE))));
						}
					}
				}
			}
			return $ret;
		}
	}

	/**
	 * Create a folder
	 *
	 * @param string $path Path of the folder to create
	 * @since 1.6
	 */
	function create()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$user = JFactory::getUser();

		$folder			= JRequest::getCmd('foldername', '');
		$folderCheck	= JRequest::getVar('foldername', null, '', 'string', JREQUEST_ALLOWRAW);
		$parent			= JRequest::getVar('folderbase', '', '', 'path');

		$this->setRedirect('index.php?option=com_podcastmedia&folder='.$parent.'&tmpl='.JRequest::getCmd('tmpl', 'index'));

		if (strlen($folder) > 0)
		{
			if (!$user->authorise('core.create','com_podcastmanager'))
			{
				// User is not authorised to delete
				JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_CREATE_NOT_PERMITTED'));
				return false;
			}

			// Set FTP credentials, if given
			jimport('joomla.client.helper');
			JClientHelper::setCredentialsFromRequest('ftp');

			JRequest::setVar('folder', $parent);

			if (($folderCheck !== null) && ($folder !== $folderCheck)) {
				$this->setMessage(JText::_('COM_PODCASTMEDIA_ERROR_UNABLE_TO_CREATE_FOLDER_WARNDIRNAME'));
				return false;
			}

			$path = JPath::clean(COM_PODCASTMEDIA_BASE.DS.$parent.DS.$folder);
			if (!is_dir($path) && !is_file($path))
			{
				// Trigger the onContentBeforeSave event.
				$object_file = new JObject(array('filepath' => $path));
				JPluginHelper::importPlugin('content');
				$dispatcher	= JDispatcher::getInstance();
				$result = $dispatcher->trigger('onContentBeforeSave', array('com_podcastmedia.folder', &$object_file));
				if (in_array(false, $result, true)) {
					// There are some errors in the plugins
					JError::raiseWarning(100, JText::plural('COM_PODCASTMEDIA_ERROR_BEFORE_SAVE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));
					continue;
				}

				JFolder::create($path);
				$data = "<html>\n<body bgcolor=\"#FFFFFF\">\n</body>\n</html>";
				JFile::write($path.DS."index.html", $data);

				// Trigger the onContentAfterSave event.
				$dispatcher->trigger('onContentAfterSave', array('com_podcastmedia.folder', &$object_file, true));
				$this->setMessage(JText::sprintf('COM_PODCASTMEDIA_CREATE_COMPLETE', substr($path, strlen(COM_PODCASTMEDIA_BASE))));
			}
			JRequest::setVar('folder', ($parent) ? $parent.'/'.$folder : $folder);
		}
	}
}
