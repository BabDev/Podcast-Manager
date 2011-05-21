<?php
/**
* Podcast Manager for Joomla!
*
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
* Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
* Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
*/

// Restricted access
defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

class PodcastManagerController extends JController
{
	function __construct($config = array())
	{
		// Frontpage Editor podcast proxying:
		if(JRequest::getCmd('view') === 'podcasts' && JRequest::getCmd('layout') === 'modal') {
			JHtml::_('stylesheet', 'system/adminlist.css', array(), true);
			$config['base_path'] = JPATH_COMPONENT_ADMINISTRATOR;
		}

		parent::__construct($config);
	}

	/**
	 * Method to display a view.
	 *
	 * @param	boolean	$cachable	If true, the view output will be cached
	 * @param	array	$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object is to support chaining.
	 * @since	1.6
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// Initialise variables.
		$cachable	= true;
		$user		= JFactory::getUser();

		// Set the default view name and format from the Request.
		$feed	= JRequest::getInt('feedname');
		$vName	= JRequest::getCmd('view', 'feed');
		JRequest::setVar('view', $vName);

		if ($user->get('id') ||($_SERVER['REQUEST_METHOD'] == 'POST' && $vName = 'feed')) {
			$cachable = false;
		}

		$safeurlparams = array(
			'id'				=> 'INT',
			'feedname'			=> 'INT',
			'limit'				=> 'INT',
			'limitstart'		=> 'INT',
			'lang'				=> 'CMD'
		);

		return parent::display($cachable, $safeurlparams);
	}
}
