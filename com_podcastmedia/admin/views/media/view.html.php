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

jimport('joomla.application.component.helper');

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
		$params = JComponentHelper::getParams('com_podcastmedia');

		$lang = JFactory::getLanguage();

		$style = $params->get('layout', 'thumbs');

		$document = JFactory::getDocument();

		JHtml::_('behavior.framework', true);

		JHtml::_('behavior.modal');
		$document->addScriptDeclaration(
			"window.addEvent('domready', function() {
				document.preview = SqueezeBox;
			});"
		);

		JHtml::stylesheet('administrator/components/com_podcastmanager/media/css/template.css', false, false, false);
		JHtml::script('administrator/components/com_podcastmedia/media/js/mediamanager.js', false, false);

		if (version_compare(JVERSION, '3.0', 'lt'))
		{
			$document->setBuffer($this->loadTemplate('navigation'), 'modules', 'submenu');

			JHtml::_('stylesheet', 'media/mediamanager.css', array(), true);

			if ($lang->isRTL())
			{
				JHtml::_('stylesheet', 'media/mediamanager_rtl.css', array(), true);
			}

			JHtml::_('script', 'system/mootree.js', true, true, false, false);
			JHtml::_('stylesheet', 'system/mootree.css', array(), true);

			if ($lang->isRTL())
			{
				JHtml::_('stylesheet', 'media/mootree_rtl.css', array(), true);
			}
		}

		if ($medmanparams->get('enable_flash', 1))
		{
			$fileTypes = 'mp3,m4a,mov,mp4,m4v';
			$types = explode(',', $fileTypes);

			// These types are what the user sees
			$displayTypes = '';

			// This is what controls the logic
			$filterTypes = '';
			$firstType = true;

			foreach ($types AS $type)
			{
				if (!$firstType)
				{
					$displayTypes .= ', ';
					$filterTypes .= '; ';
				}
				else
				{
					$firstType = false;
				}
				$displayTypes .= '*.' . $type;
				$filterTypes .= '*.' . $type;
			}
			$typeString = '{ \'' . JText::_('COM_PODCASTMEDIA_FILES', 'true') . ' (' . $displayTypes . ')\': \'' . $filterTypes . '\' }';

			JHtml::_(
				'behavior.uploader', 'upload-flash', array(
															'onBeforeStart' => 'function(){ Uploader.setOptions({url: document.id(\'uploadForm\').action + \'&folder=\' + document.id(\'mediamanager-form\').folder.value}); }',
															'onComplete' => 'function(){ PodcastMediaManager.refreshFrame(); }',
															'targetURL' => '\\document.id(\'uploadForm\').action',
															'typeFilter' => $typeString,
															'fileSizeMax' => (int) ($medmanparams->get('upload_maxsize', 0) * 1024 * 1024)
														)
			);
		}

		if (DIRECTORY_SEPARATOR == '\\')
		{
			$base = str_replace(DIRECTORY_SEPARATOR, "\\\\", COM_PODCASTMEDIA_BASE);
		}
		else
		{
			$base = COM_PODCASTMEDIA_BASE;
		}

		$js = "
			var basepath = '" . $base . "';
			var viewstyle = '" . $style . "';
		";
		$document->addScriptDeclaration($js);

		/*
		 * Display form for FTP credentials?
		 * Don't set them here, as there are other functions called before this one if there is any file write operation
		 */
		$ftp = !JClientHelper::hasCredentials('ftp');

		$this->session = JFactory::getSession();
		$this->medmanparams = $medmanparams;
		$this->state = $this->get('state');
		$this->require_ftp = $ftp;
		$this->folders_id = ' id="media-tree"';
		$this->folders = $this->get('folderTree');

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
		$bar = JToolBar::getInstance('toolbar');
		$user = JFactory::getUser();

		// Set the titlebar text
		JToolBarHelper::title(JText::_('COM_PODCASTMEDIA'), 'podcastmanager.png');

		// Add a upload button
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			if ($user->authorise('core.create', 'com_podcastmanager'))
			{
				$title = JText::_('JTOOLBAR_UPLOAD');
				$dhtml = '<button data-toggle="collapse" data-target="#collapseUpload" class="btn btn-small btn-success">
							<i class="icon-plus icon-white" title="' . $title . '"></i>
							' . $title . '</button>';
				$bar->appendButton('Custom', $dhtml, 'upload');
				JToolBarHelper::divider();
			}
		}

		// Add a create folder button
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			if ($user->authorise('core.create', 'com_podcastmanager'))
			{
				$title = JText::_('COM_PODCASTMEDIA_CREATE_FOLDER');
				$dhtml = '<button data-toggle="collapse" data-target="#collapseFolder" class="btn btn-small">
							<i class="icon-folder" title="' . $title . '"></i>
							' . $title . '</button>';
				$bar->appendButton('Custom', $dhtml, 'folder');
				JToolBarHelper::divider();
			}
		}

		// Add a delete button
		if ($user->authorise('core.delete', 'com_podcastmanager'))
		{
			$title = JText::_('JTOOLBAR_DELETE');

			if (version_compare(JVERSION, '3.0', 'ge'))
			{
				$dhtml = '<button href="#" onclick="PodcastMediaManager.submit("folder.delete")" class="btn btn-small">
							<i class="icon-remove" title="' . $title . '"></i>
							' . $title . '</button>';
			}
			else
			{
				$dhtml = '<a href="#" onclick="PodcastMediaManager.submit("folder.delete")" class="toolbar">
							<span class="icon-32-delete" title="' . $title . '"></span>
							' . $title . '</a>';
			}
			$bar->appendButton('Custom', $dhtml, 'delete');
			JToolBarHelper::divider();
		}

		if ($user->authorise('core.admin', 'com_podcastmanager'))
		{
			JToolBarHelper::preferences('com_podcastmedia');
			JToolBarHelper::divider();
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
		$txt = null;

		if (isset($folder['children']) && count($folder['children']))
		{
			$tmp = $this->folders;
			$this->folders = $folder;
			$txt = $this->loadTemplate('folders');
			$this->folders = $tmp;
		}

		return $txt;
	}
}
