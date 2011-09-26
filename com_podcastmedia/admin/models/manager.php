<?php
/**
* Podcast Manager for Joomla!
*
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* @package		PodcastManager
* @subpackage	com_podcastmedia
*
* Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
* Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
*/

// Restricted access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

/**
 * Podcast Media Component Manager Model
 *
 * @package		PodcastManager
 * @subpackage	com_podcastmedia
 * @since		1.6
 */
class PodcastMediaModelManager extends JModel
{
	/**
	* Method to get model state variables
	*
	* @param	string	$property	Optional parameter name
	* @param	mixed	$default	Optional default value
	*
	* @return	object	The property where specified, the state object where omitted
	* @since   1.6
	*/
	function getState($property = null, $default = null)
	{
		static $set;

		if (!$set) {
			$folder = JRequest::getVar('folder', '', '', 'path');
			$this->setState('folder', $folder);

			$fieldid = JRequest::getCmd('fieldid', '');
			$this->setState('field.id', $fieldid);

			$parent = str_replace("\\", "/", dirname($folder));
			$parent = ($parent == '.') ? null : $parent;
			$this->setState('parent', $parent);
			$set = true;
		}

		return parent::getState($property, $default);
	}

	/**
	 * Retrieves the list of folders
	 *
	 * @param	string	$base	The base file path
	 *
	 * @return	object	$list	A formatted object containing a list of folders
	 * @since	1.6
	 */
	function getFolderList($base = null)
	{
		// Get some paths from the request
		if (empty($base)) {
			$base = COM_PODCASTMEDIA_BASE;
		}

		// Corrections for Windows paths
		$base = str_replace(DS, '/', $base);
		$comPodcastMediaBaseUni = str_replace(DS, '/', COM_PODCASTMEDIA_BASE);

		// Get the list of folders
		jimport('joomla.filesystem.folder');
		$folders = JFolder::folders($base, '.', true, true);

		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_PODCASTMEDIA_INSERT_IMAGE'));

		// Build the array of select options for the folder list
		$options[] = JHtml::_('select.option', "","/");

		foreach ($folders as $folder) {
			$folder		= str_replace($comPodcastMediaBaseUni, "", str_replace(DS, '/', $folder));
			$value		= substr($folder, 1);
			$text		= str_replace(DS, "/", $folder);
			$options[]	= JHtml::_('select.option', $value, $text);
		}

		// Sort the folder list array
		if (is_array($options)) {
			sort($options);
		}

		// Create the drop-down folder select list
		$asset = JRequest::getVar('asset');
		$author = JRequest::getVar('author');
		$list = JHtml::_('select.genericlist',  $options, 'folderlist', 'class="inputbox" size="1" onchange="AudioManager.setFolder(this.options[this.selectedIndex].value, "'.htmlspecialchars($asset).'","'.htmlspecialchars($author).'")" ', 'value', 'text', $base);

		return $list;
	}

	/**
	 * Creates a tree of folders
	 *
	 * @param	string	$base	The base file path
	 *
	 * @return	array	$list	An array containing the folders and their children
	 * @since	1.6
	 */
	function getFolderTree($base = null)
	{
		// Get some paths from the request
		if (empty($base)) {
			$base = COM_PODCASTMEDIA_BASE;
		}

		$mediaBase = str_replace(DS, '/', COM_PODCASTMEDIA_BASE.'/');

		// Get the list of folders
		jimport('joomla.filesystem.folder');
		$folders = JFolder::folders($base, '.', true, true);

		$tree = array();

		foreach ($folders as $folder) {
			$folder		= str_replace(DS, '/', $folder);
			$name		= substr($folder, strrpos($folder, '/') + 1);
			$relative	= str_replace($mediaBase, '', $folder);
			$absolute	= $folder;
			$path		= explode('/', $relative);
			$node		= (object) array('name' => $name, 'relative' => $relative, 'absolute' => $absolute);

			$tmp = &$tree;
			for ($i=0,$n=count($path); $i<$n; $i++) {
				if (!isset($tmp['children'])) {
					$tmp['children'] = array();
				}

				if ($i == $n-1) {
					// We need to place the node
					$tmp['children'][$relative] = array('data' =>$node, 'children' => array());
					break;
				}

				if (array_key_exists($key = implode('/', array_slice($path, 0, $i+1)), $tmp['children'])) {
					$tmp = &$tmp['children'][$key];
				}
			}
		}
		$tree['data'] = (object) array('name' => JText::_('COM_PODCASTMEDIA_MEDIA'), 'relative' => '', 'absolute' => $base);

		return $tree;
	}
}
