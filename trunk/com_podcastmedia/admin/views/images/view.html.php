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

jimport('joomla.application.component.view');

/**
 * HTML View class for the Podcast Media component
 *
 * @package		Podcast Manager
 * @subpackage	com_podcastmedia
 * @since		1.6
 */
class MediaViewImages extends JView
{
	function display($tpl = null)
	{
		$medmanparams	= JComponentHelper::getParams('com_media');
		$podmanparams	= JComponentHelper::getParams('com_podcastmanager');
		$podmedparams	= JComponentHelper::getParams('com_podcastmedia');
		$app			= JFactory::getApplication();
		$lang			= JFactory::getLanguage();
		$append 		= '';

		JHtml::_('behavior.framework', true);
		JHTML::_('script','media/popup-imagemanager.js', true, true);
		JHTML::_('stylesheet','media/popup-imagemanager.css', array(), true);

		if ($lang->isRTL()) {
			JHTML::_('stylesheet','media/popup-imagemanager_rtl.css', array(), true);
		}

		if ($medmanparams->get('enable_flash', 1)) {
			$fileTypes = $medmanparams->get('image_extensions', 'bmp,gif,jpg,png,jpeg');
			$types = explode(',', $fileTypes);
			$displayTypes = '';		// this is what the user sees
			$filterTypes = '';		// this is what controls the logic
			$firstType = true;

			foreach($types AS $type)
			{
				if(!$firstType) {
					$displayTypes .= ', ';
					$filterTypes .= '; ';
				}
				else {
					$firstType = false;
				}

				$displayTypes .= '*.'.$type;
				$filterTypes .= '*.'.$type;
			}

			$typeString = '{ \''.JText::_('COM_MEDIA_FILES','true').' ('.$displayTypes.')\': \''.$filterTypes.'\' }';

			JHtml::_('behavior.uploader', 'upload-flash',
				array(
					'onBeforeStart' => 'function(){ Uploader.setOptions({url: document.id(\'uploadForm\').action + \'&folder=\' + document.id(\'imageForm\').folderlist.value}); }',
					'onComplete' 	=> 'function(){ window.frames[\'imageframe\'].location.href = window.frames[\'imageframe\'].location.href; }',
					'targetURL' 	=> '\\document.id(\'uploadForm\').action',
					'typeFilter' 	=> $typeString,
					'fileSizeMax'	=> (int) ($medmanparams->get('upload_maxsize',0) * 1024 * 1024),
				)
			);
		}

		/*
		 * Display form for FTP credentials?
		 * Don't set them here, as there are other functions called before this one if there is any file write operation
		 */
		jimport('joomla.client.helper');
		$ftp = !JClientHelper::hasCredentials('ftp');

		$this->assignRef('session',			JFactory::getSession());
		$this->assignRef('medmanparams',	$medmanparams);
		$this->assignRef('state',			$this->get('state'));
		$this->assignRef('folderList',		$this->get('folderList'));
		$this->assign('require_ftp', $ftp);

		parent::display($tpl);
	}
}