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
class PodcastMediaViewMedia extends JViewLegacy
{
	/**
	 * The folder list
	 *
	 * @var    object
	 * @since  1.6
	 */
	protected $folders;

	/**
	 * The folder ID
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $folders_id;

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
		$app = JFactory::getApplication();

		if (!$app->isAdmin())
		{
			return $app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'warning');
		}

		$this->session      = JFactory::getSession();
		$this->medmanparams = JComponentHelper::getParams('com_media');
		$this->state        = $this->get('state');
		$this->require_ftp  = !JClientHelper::hasCredentials('ftp');
		$this->folders_id   = ' id="media-tree"';
		$this->folders      = $this->get('folderTree');

		$this->sidebar = JHtmlSidebar::render();

		// Set the toolbar
		$this->addToolbar();

		return parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		// Get the toolbar object instance
		$bar  = JToolbar::getInstance('toolbar');
		$user = JFactory::getUser();

		// Set the titlebar text
		JToolbarHelper::title(JText::_('COM_PODCASTMEDIA'), 'podcastmanager.png');

		// Add a upload button
		if ($user->authorise('core.create', 'com_podcastmanager'))
		{
			// Instantiate a new JLayoutFile instance and render the layout
			$bar->appendButton('Custom', (new JLayoutFile('toolbar.uploadmedia'))->render([]), 'upload');
			JToolbarHelper::divider();
		}

		// Add a create folder button
		if ($user->authorise('core.create', 'com_podcastmanager'))
		{
			// Instantiate a new JLayoutFile instance and render the layout
			$bar->appendButton('Custom', (new JLayoutFile('toolbar.newfolder'))->render([]), 'upload');
			JToolbarHelper::divider();
		}

		// Add a delete button
		if ($user->authorise('core.delete', 'com_podcastmanager'))
		{
			// Instantiate a new JLayoutFile instance and render the layout
			$bar->appendButton('Custom', (new JLayoutFile('toolbar.deletemedia'))->render([]), 'upload');
			JToolbarHelper::divider();
		}

		// Add a back button
		JToolbarHelper::back('JTOOLBAR_BACK', 'index.php?option=com_podcastmanager');
		JToolbarHelper::divider();

		if ($user->authorise('core.admin', 'com_podcastmanager') || $user->authorise('core.options', 'com_podcastmanager'))
		{
			JToolbarHelper::preferences('com_podcastmedia');
			JToolbarHelper::divider();
		}
	}

	/**
	 * Function to determine the folder level
	 *
	 * @param   string  $folder  The current folder
	 *
	 * @return  string  The folder level
	 *
	 * @since   1.6
	 */
	protected function getFolderLevel($folder)
	{
		$this->folders_id = null;
		$txt              = null;

		if (isset($folder['children']) && count($folder['children']))
		{
			$tmp           = $this->folders;
			$this->folders = $folder;
			$txt           = $this->loadTemplate('folders');
			$this->folders = $tmp;
		}

		return $txt;
	}
}
