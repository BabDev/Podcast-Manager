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
		$medmanparams = JComponentHelper::getParams('com_media');
		$params       = JComponentHelper::getParams('com_podcastmedia');

		$style = $params->get('layout', 'thumbs');

		JHtml::_('behavior.framework', true);
		JHtml::_('behavior.modal');

		$document = JFactory::getDocument();
		$document->addScriptDeclaration(
			"window.addEvent('domready', function() {
				document.preview = SqueezeBox;
			});"
		);

		JHtml::_('stylesheet', 'podcastmanager/template.css', false, true, false);
		JHtml::_('script', 'podcastmanager/mediamanager.js', false, true);

		if (DIRECTORY_SEPARATOR == '\\')
		{
			$base = str_replace(DIRECTORY_SEPARATOR, "\\\\", COM_PODCASTMEDIA_BASE);
		}
		else
		{
			$base = COM_PODCASTMEDIA_BASE;
		}

		$js = <<< JS
			var basepath = '"$base"';
			var viewstyle = '"$style"';
JS;
		$document->addScriptDeclaration($js);

		/*
		 * Display form for FTP credentials?
		 * Don't set them here, as there are other functions called before this one if there is any file write operation
		 */
		$ftp = !JClientHelper::hasCredentials('ftp');

		$this->session      = JFactory::getSession();
		$this->medmanparams = $medmanparams;
		$this->state        = $this->get('state');
		$this->require_ftp  = $ftp;
		$this->folders_id   = ' id="media-tree"';
		$this->folders      = $this->get('folderTree');

		// Set the toolbar
		$this->addToolbar();

		JHtml::_('behavior.keepalive');

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
		$bar  = JToolBar::getInstance('toolbar');
		$user = JFactory::getUser();

		// Set the titlebar text
		JToolbarHelper::title(JText::_('COM_PODCASTMEDIA'), 'podcastmanager.png');

		// Add a upload button
		if ($user->authorise('core.create', 'com_podcastmanager'))
		{
			$title = JText::_('JTOOLBAR_UPLOAD');
			$dhtml = '<button data-toggle="collapse" data-target="#collapseUpload" class="btn btn-small btn-success">
						<i class="icon-plus icon-white" title="' . $title . '"></i>
						' . $title . '</button>';
			$bar->appendButton('Custom', $dhtml, 'upload');
			JToolbarHelper::divider();
		}

		// Add a create folder button
		if ($user->authorise('core.create', 'com_podcastmanager'))
		{
			$title = JText::_('COM_PODCASTMEDIA_CREATE_FOLDER');
			$dhtml = '<button data-toggle="collapse" data-target="#collapseFolder" class="btn btn-small">
						<i class="icon-folder" title="' . $title . '"></i>
						' . $title . '</button>';
			$bar->appendButton('Custom', $dhtml, 'folder');
			JToolbarHelper::divider();
		}

		// Add a delete button
		if ($user->authorise('core.delete', 'com_podcastmanager'))
		{
			$title = JText::_('JTOOLBAR_DELETE');

			$dhtml = '<button href="#" onclick="PodcastMediaManager.submit("folder.delete")" class="btn btn-small">
						<i class="icon-remove" title="' . $title . '"></i>
						' . $title . '</button>';

			$bar->appendButton('Custom', $dhtml, 'delete');
			JToolbarHelper::divider();
		}

		// Add a back button
		JToolbarHelper::back('JTOOLBAR_BACK', 'index.php?option=com_podcastmanager');
		JToolbarHelper::divider();

		if ($user->authorise('core.admin', 'com_podcastmanager'))
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
