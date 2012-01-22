<?php
/**
* Podcast Manager for Joomla!
*
* @copyright	Copyright (C) 2011-2012 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* @package		PodcastManager
* @subpackage	com_podcastmedia
*
* Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
* Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
*/

// Restricted access
defined('_JEXEC') or die;

/**
 * Podcast Manager Media Component Helper
 *
 * @package		PodcastManager
 * @subpackage	com_podcastmedia
 * @since		1.6
 */
class PodcastMediaHelper
{
	/**
	 * Gets the icon type
	 *
	 * @param	string	$fileName	The filename
	 *
	 * @return	string	The extension of the file
	 * @since	1.6
	 */
	function getTypeIcon($fileName)
	{
		// Get file extension
		return strtolower(substr($fileName, strrpos($fileName, '.') + 1));
	}

	/**
	 * Checks if the file can be uploaded
	 * @param	array	$file	File information
	 * @param 	string	$err	An error message to be returned
	 *
	 * @return	boolean	True on success, false on failure
	 * @since	1.6
	 */
	function canUpload($file, &$err)
	{
		$medmanparams = JComponentHelper::getParams('com_media');

		jimport('joomla.filesystem.file');
		$format = JFile::getExt($file['name']);

		$allowable = explode(',', 'mp3,m4a,mov,mp4,m4v');

		if (!in_array($format, $allowable)) {
			$err = JText('COM_PODCASTMEDIA_ERROR_WARNFILETYPE');
			return false;
		}

		$maxSize = (int) ($medmanparams->get('upload_maxsize', 0) * 1024 * 1024);

		if ($maxSize > 0 && (int) $file['size'] > $maxSize) {
			$err = JText('COM_PODCASTMEDIA_ERROR_WARNFILETOOLARGE');

			return false;
		}

		return true;
	}

	/**
	 * Function to parse a file size to the correct measurement
	 *
	 * @param	string	$size	The file size in bytes
	 *
	 * @return	JText	A translated string with the converted file size
	 */
	public static function parseSize($size)
	{
		if ($size < 1024) {
			return JText::sprintf('COM_PODCASTMEDIA_FILESIZE_BYTES', $size);
		}
		else if ($size < 1024 * 1024) {
			return JText::sprintf('COM_PODCASTMEDIA_FILESIZE_KILOBYTES', sprintf('%01.2f', $size / 1024.0));
		}
		else {
			return JText::sprintf('COM_PODCASTMEDIA_FILESIZE_MEGABYTES', sprintf('%01.2f', $size / (1024.0 * 1024)));
		}
	}

	/**
	 * Count the number of files in a directory
	 *
	 * @param	string	$dir	Path to the directory to be counted
	 *
	 * @return	array	An array containing the number of files and folders
	 * @since	1.6
	 */
	function countFiles($dir)
	{
		$total_file = 0;
		$total_dir = 0;

		if (is_dir($dir)) {
			$d = dir($dir);

			while (false !== ($entry = $d->read()))
			{
				if (substr($entry, 0, 1) != '.' && is_file($dir.DIRECTORY_SEPARATOR.$entry) && strpos($entry, '.html') === false && strpos($entry, '.php') === false) {
					$total_file++;
				}

				if (substr($entry, 0, 1) != '.' && is_dir($dir.DIRECTORY_SEPARATOR.$entry)) {
					$total_dir++;
				}
			}

			$d->close();
		}

		return array ($total_file, $total_dir);
	}
}
