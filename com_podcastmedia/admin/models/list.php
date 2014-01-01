<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmedia
 *
 * @copyright   Copyright (C) 2011-2014 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * Podcast Media Component List Model
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmedia
 * @since       1.6
 */
class PodcastMediaModelList extends JModelLegacy
{
	/**
	 * Method to get model state variables
	 *
	 * @param   string  $property  Optional parameter name [optional]
	 * @param   mixed   $default   Optional default value [optional]
	 *
	 * @return  object  The property where specified, the state object where omitted
	 *
	 * @since   1.6
	 */
	public function getState($property = null, $default = null)
	{
		static $set;

		if (!$set)
		{
			$folder = JFactory::getApplication()->input->get('folder', '', 'path');
			$this->setState('folder', $folder);

			$parent = str_replace("\\", "/", dirname($folder));
			$parent = ($parent == '.') ? null : $parent;
			$this->setState('parent', $parent);
			$set = true;
		}

		return parent::getState($property, $default);
	}

	/**
	 * Function to retrieve the available folders
	 *
	 * @return  array  An array of folders
	 *
	 * @since   1.6
	 */
	public function getFolders()
	{
		$list = $this->getList();

		return $list['folders'];
	}

	/**
	 * Function to retrieve the available audio files
	 *
	 * @return  array  An array of audio files
	 *
	 * @since   1.6
	 */
	public function getAudio()
	{
		$list = $this->getList();

		return $list['audio'];
	}

	/**
	 * Build list view
	 *
	 * @return  array  An array of data for the list view
	 *
	 * @since   1.6
	 */
	public function getList()
	{
		static $list;

		// Only process the list once per request
		if (is_array($list))
		{
			return $list;
		}

		// Get current path from request
		$current = $this->getState('folder');

		// If undefined, set to empty
		if ($current == 'undefined')
		{
			$current = '';
		}

		// Check if current is the same as the configured default path
		if ($current == JComponentHelper::getParams('com_podcastmedia')->get('file_path', 'media/com_podcastmanager'))
		{
			$current = '';
		}

		// Initialise variables.
		if (strlen($current) > 0)
		{
			$basePath = COM_PODCASTMEDIA_BASE . '/' . $current;
		}
		else
		{
			$basePath = COM_PODCASTMEDIA_BASE;
		}

		$mediaBase = str_replace(DIRECTORY_SEPARATOR, '/', COM_PODCASTMEDIA_BASE . '/');

		$folders = array();
		$audio = array();

		// Get the list of files and folders from the given folder
		$fileList = JFolder::files($basePath);
		$folderList = JFolder::folders($basePath);

		// Iterate over the files if they exist
		if ($fileList !== false)
		{
			foreach ($fileList as $file)
			{
				if (is_file($basePath . '/' . $file) && substr($file, 0, 1) != '.' && strtolower($file) !== 'index.html')
				{
					$tmp = new stdClass;
					$tmp->name = $file;
					$tmp->title = $file;
					$tmp->path = str_replace(DIRECTORY_SEPARATOR, '/', JPath::clean($basePath . '/' . $file));
					$tmp->path_relative = str_replace($mediaBase, '', $tmp->path);
					$tmp->size = filesize($tmp->path);

					$ext = strtolower(JFile::getExt($file));

					switch ($ext)
					{
						// Allowed media file
						case 'mp3':
						case 'm4a':
						case 'mov':
						case 'mp4':
						case 'm4v':
							$tmp->icon_32 = "media/mime-icon-32/" . $ext . ".png";
							$tmp->icon_16 = "media/mime-icon-16/" . $ext . ".png";
							$audio[] = $tmp;
							break;
					}
				}
			}
		}

		// Iterate over the folders if they exist
		if ($folderList !== false)
		{
			foreach ($folderList as $folder)
			{
				$tmp = new stdClass;
				$tmp->name = basename($folder);
				$tmp->path = str_replace(DIRECTORY_SEPARATOR, '/', JPath::clean($basePath . '/' . $folder));
				$tmp->path_relative = str_replace($mediaBase, '', $tmp->path);
				$count = PodcastMediaHelper::countFiles($tmp->path);
				$tmp->files = $count[0];
				$tmp->folders = $count[1];

				$folders[] = $tmp;
			}
		}

		$list = array('folders' => $folders, 'audio' => $audio);

		return $list;
	}
}
