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

jimport('joomla.application.component.view');

/**
 * HTML View class for the Podcast Media component
 *
 * @since		1.6
 */
class PodcastMediaViewAudioList extends JView
{
	function display($tpl = null)
	{
		// Do not allow cache
		JResponse::allowCache(false);

		$app = JFactory::getApplication();

		$lang	= JFactory::getLanguage();

		JHtml::_('stylesheet','media/popup-imagelist.css', array(), true);
		if ($lang->isRTL()) :
			JHtml::_('stylesheet','media/popup-imagelist_rtl.css', array(), true);
		endif;

		$document = JFactory::getDocument();
		$document->addScriptDeclaration("var AudioManager = window.parent.AudioManager;");

		$audio		= $this->get('audio');
		$folders	= $this->get('folders');
		$state		= $this->get('state');

		$this->assign('baseURL', COM_PODCASTMEDIA_BASEURL);
		$this->assignRef('audio', $audio);
		$this->assignRef('folders', $folders);
		$this->assignRef('state', $state);

		parent::display($tpl);
	}


	function setFolder($index = 0)
	{
		if (isset($this->folders[$index])) {
			$this->_tmp_folder = &$this->folders[$index];
		} else {
			$this->_tmp_folder = new JObject;
		}
	}

	function setAudio($index = 0)
	{
		if (isset($this->audio[$index])) {
			$this->_tmp_audio = &$this->audio[$index];
		} else {
			$this->_tmp_audio = new JObject;
		}
	}
}
