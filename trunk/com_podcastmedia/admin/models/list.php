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

jimport('joomla.application.component.model');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * Podcast Media Component List Model
 *
 * @package		Podcast Manager
 * @subpackage	com_podcastmedia
 * @since		1.6
 */
class PodcastMediaModelList extends JModel
{
	function getState($property = null, $default = null)
	{
		static $set;

		if (!$set) {
			$folder = JRequest::getVar('folder', '', '', 'path');
			$this->setState('folder', $folder);

			$parent = str_replace("\\", "/", dirname($folder));
			$parent = ($parent == '.') ? null : $parent;
			$this->setState('parent', $parent);
			$set = true;
		}

		return parent::getState($property, $default);
	}

	function getFolders()
	{
		$list = $this->getList();

		return $list['folders'];
	}

	function getAudio()
	{
		$list = $this->getList();

		return $list['audio'];
	}

	/**
	 * Build list view
	 *
	 * @param string $listFolder The directory to display
	 * @since 1.6
	 */
	function getList()
	{
		static $list;

		// Only process the list once per request
		if (is_array($list)) {
			return $list;
		}

		// Get current path from request
		$current = $this->getState('folder');

		// If undefined, set to empty
		if ($current == 'undefined') {
			$current = '';
		}

		// Initialise variables.
		if (strlen($current) > 0) {
			$basePath = COM_PODCASTMEDIA_BASE.'/'.$current;
		}
		else {
			$basePath = COM_PODCASTMEDIA_BASE;
		}

		$mediaBase = str_replace(DS, '/', COM_PODCASTMEDIA_BASE.'/');

		$folders	= array ();
		$audio		= array ();

		// Get the list of files and folders from the given folder
		$fileList	= JFolder::files($basePath);
		$folderList = JFolder::folders($basePath);

		// Iterate over the files if they exist
		if ($fileList !== false) {
			foreach ($fileList as $file)
			{
				if (is_file($basePath.'/'.$file) && substr($file, 0, 1) != '.' && strtolower($file) !== 'index.html') {
					$tmp		= new JObject();
					$tmp->name	= $file;
					$tmp->title	= $file;
					$tmp->path	= str_replace(DS, '/', JPath::clean($basePath.DS.$file));
					$tmp->path_relative = str_replace($mediaBase, '', $tmp->path);
					$tmp->size	= filesize($tmp->path);

					$ext = strtolower(JFile::getExt($file));
					switch ($ext)
					{
						// Audio file
						case 'mp3':
						case 'm4a':
						case 'mov':
						case 'mp4':
						case 'm4v':
							$tmp->icon_32 = "media/mime-icon-32/".$ext.".png";
							$tmp->icon_16 = "media/mime-icon-16/".$ext.".png";
							$audio[] = $tmp;
							break;
					}
				}
			}
		}

		// Iterate over the folders if they exist
		if ($folderList !== false) {
			foreach ($folderList as $folder)
			{
				$tmp		= new JObject();
				$tmp->name	= basename($folder);
				$tmp->path	= str_replace(DS, '/', JPath::clean($basePath.DS.$folder));
				$tmp->path_relative	= str_replace($mediaBase, '', $tmp->path);
				$count		= PodcastMediaHelper::countFiles($tmp->path);
				$tmp->files	= $count[0];
				$tmp->folders	= $count[1];

				$folders[]	= $tmp;
			}
		}

		$list = array('folders' => $folders, 'audio' => $audio);

		return $list;
	}
}
