<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmedia
 *
 * @copyright   Copyright (C) 2011 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');
jimport('joomla.application.component.helper');

/**
 * HTML View class for the Podcast Media component
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmedia
 * @since       1.6
 */
class PodcastMediaViewMedia extends JView
{
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @since   1.6
	 */
	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$medmanparams = JComponentHelper::getParams('com_media');

		$lang = JFactory::getLanguage();

		$style = $app->getUserStateFromRequest('podcastmedia.list.layout', 'layout', 'thumbs', 'word');

		$document = JFactory::getDocument();
		$document->setBuffer($this->loadTemplate('navigation'), 'modules', 'submenu');

		JHtml::_('behavior.framework', true);

		JHtml::stylesheet('administrator/components/com_podcastmanager/media/css/template.css', false, false, false);
		JHtml::script('administrator/components/com_podcastmedia/media/js/mediamanager.js', false, false);
		JHtml::_('stylesheet', 'media/mediamanager.css', array(), true);
		if ($lang->isRTL())
		{
			JHtml::_('stylesheet', 'media/mediamanager_rtl.css', array(), true);
		}

		JHtml::_('behavior.modal');
		$document->addScriptDeclaration(
			"window.addEvent('domready', function() {
				document.preview = SqueezeBox;
			});"
		);

		JHtml::_('script', 'system/mootree.js', true, true, false, false);
		JHtml::_('stylesheet', 'system/mootree.css', array(), true);
		if ($lang->isRTL())
		{
			JHtml::_('stylesheet', 'media/mootree_rtl.css', array(), true);
		}

		if ($medmanparams->get('enable_flash', 1))
		{
			$fileTypes = 'mp3,m4a,mov,mp4,m4v';
			$types = explode(',', $fileTypes);
			$displayTypes = ''; // this is what the user sees
			$filterTypes = ''; // this is what controls the logic
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

		if (DS == '\\')
		{
			$base = str_replace(DS, "\\\\", COM_PODCASTMEDIA_BASE);
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
		jimport('joomla.client.helper');
		$ftp = !JClientHelper::hasCredentials('ftp');

		$session = JFactory::getSession();
		$state = $this->get('state');
		$this->assignRef('session', $session);
		$this->assignRef('medmanparams', $medmanparams);
		$this->assignRef('state', $state);
		$this->assign('require_ftp', $ftp);
		$this->assign('folders_id', ' id="media-tree"');
		$this->assign('folders', $this->get('folderTree'));

		// Set the toolbar
		$this->addToolbar();

		parent::display($tpl);
		echo JHtml::_('behavior.keepalive');
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

		// Add a delete button
		if ($user->authorise('core.delete', 'com_podcastmanager'))
		{
			$title = JText::_('JTOOLBAR_DELETE');
			$dhtml = '<a href="#" onclick="PodcastMediaManager.submit("folder.delete")" class="toolbar">
						<span class="icon-32-delete" title="' . $title . '"></span>
						' . $title . '</a>';
			$bar->appendButton('Custom', $dhtml, 'delete');
			JToolBarHelper::divider();
		}
		if ($user->authorise('core.admin', 'com_podcastmanager'))
		{
			JToolBarHelper::preferences('com_podcastmedia');
			JToolBarHelper::divider();
		}
		JToolBarHelper::help('JHELP_CONTENT_MEDIA_MANAGER');
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
	function getFolderLevel($folder)
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
