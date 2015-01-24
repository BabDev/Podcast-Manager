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

/**
 * HTML View class for the Podcast Media component
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmedia
 * @since       1.6
 */
class PodcastMediaViewAudio extends JViewLegacy
{
	/**
	 * The folder list
	 *
	 * @var    object
	 * @since  1.6
	 */
	protected $folderList;

	/**
	 * The component params
	 *
	 * @var    JRegistry
	 * @since  1.6
	 */
	protected $medmanparams;

	/**
	 * Whether FTP credentials are required or not
	 *
	 * @var    boolean
	 * @since  1.6
	 */
	protected $require_ftp;

	/**
	 * The session object
	 *
	 * @var    JSession
	 * @since  1.6
	 */
	protected $session;

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
		$medmanparams = JComponentHelper::getParams('com_media');
		$lang = JFactory::getLanguage();

		/*
		 * Display form for FTP credentials?
		 * Don't set them here, as there are other functions called before this one if there is any file write operation
		 */
		$ftp = !JClientHelper::hasCredentials('ftp');

		$this->session = JFactory::getSession();
		$this->medmanparams = $medmanparams;
		$this->state = $this->get('state');
		$this->folderList = $this->get('folderList');
		$this->require_ftp = $ftp;

		JHtml::_('behavior.framework', true);

		// The popup JS is jQuery based for 3.0
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			JHtml::_('jquery.framework');
		}

		JHtml::_('script', 'podcastmanager/popup-audiomanager.js', false, true);
		JHtml::_('stylesheet', 'media/popup-imagemanager.css', array(), true);

		if ($lang->isRTL())
		{
			JHtml::_('stylesheet', 'media/popup-imagemanager_rtl.css', array(), true);
		}

		return parent::display($tpl);
	}
}
