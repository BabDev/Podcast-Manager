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

/**
 * HTML View class for the Podcast Media component
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmedia
 * @since       1.6
 */
class PodcastMediaViewAudioList extends JViewLegacy
{
	/**
	 * An array of audio files
	 *
	 * @var    array
	 * @since  1.6
	 */
	protected $audio;

	/**
	 * The base URL
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $baseURL;

	/**
	 * An array of folders
	 *
	 * @var    array
	 * @since  1.6
	 */
	protected $folders;

	/**
	 * The state information
	 *
	 * @var    JObject
	 * @since  1.6
	 */
	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @since   1.6
	 */
	public function display($tpl = null)
	{
		// Do not allow cache
		JResponse::allowCache(false);

		$lang = JFactory::getLanguage();

		JHtml::_('stylesheet', 'media/popup-imagelist.css', array(), true);
		if ($lang->isRTL())
		{
			JHtml::_('stylesheet', 'media/popup-imagelist_rtl.css', array(), true);
		}

		$document = JFactory::getDocument();
		$document->addStyleSheet('../media/media/css/medialist-details.css');
		$document->addScriptDeclaration("var AudioManager = window.parent.AudioManager;");

		$this->baseURL = COM_PODCASTMEDIA_BASEURL;
		$this->audio = $this->get('Audio');
		$this->folders = $this->get('Folders');
		$this->state = $this->get('State');

		parent::display($tpl);
	}

	/**
	 * Function to set the current folder
	 *
	 * @param   integer  $index  The current index value
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function setFolder($index = 0)
	{
		if (isset($this->folders[$index]))
		{
			$this->_tmp_folder = $this->folders[$index];
		}
		else
		{
			$this->_tmp_folder = new JObject;
		}
	}

	/**
	 * Function to set the current audio
	 *
	 * @param   integer  $index  The current index value
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function setAudio($index = 0)
	{
		if (isset($this->audio[$index]))
		{
			$this->_tmp_audio = $this->audio[$index];
		}
		else
		{
			$this->_tmp_audio = new JObject;
		}
	}
}
