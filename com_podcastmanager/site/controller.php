<?php
/**
* Podcast Manager for Joomla!
*
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
*/

// Restricted access
defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

class PodcastManagerController extends JController
{
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.6
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// Initialise variables.
		$cachable	= true;
		$user		= JFactory::getUser();

		// Set the default view name and format from the Request.
		// Note we are using w_id to avoid collisions with the router and the return page.
		// Frontend is a bit messier than the backend.
		$id		= JRequest::getInt('p_id');
		$vName	= JRequest::getCmd('view', 'feed');
		JRequest::setVar('view', $vName);

		if ($user->get('id') ||($_SERVER['REQUEST_METHOD'] == 'POST' && $vName = 'feed')) {
			$cachable = false;
		}

		$safeurlparams = array(
			'id'				=> 'INT',
			'limit'				=> 'INT',
			'limitstart'		=> 'INT',
			'lang'				=> 'CMD'
		);

		return parent::display($cachable,$safeurlparams);
	}
}
