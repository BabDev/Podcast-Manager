<?php
/**
* Podcast Manager for Joomla!
*
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* @package		PodcastManager
* @subpackage	com_podcastmanager
*
* Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
* Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
*/

// Restricted access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Podcast edit controller class.
 *
 * @package		PodcastManager
 * @subpackage	com_podcastmanager
 * @since		1.6
 */
class PodcastManagerControllerPodcast extends JControllerForm
{
	/**
	 * Method to run batch operations.
	 *
	 * @param	object	$model	The model of the component being processed.
	 *
	 * @return	boolean	True if successful, false otherwise and internal error is set.
	 * @since	1.8
	 */
	public function batch($model)
	{
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set the model
		$model	= $this->getModel('Podcast', 'PodcastManagerModel', array());

		// Preset the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_podcastmanager&view=podcasts'.$this->getRedirectToListAppend(), false));

		return parent::batch($model);
	}
}
