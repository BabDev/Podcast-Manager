<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 *
 * @copyright   Copyright (C) 2011 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Podcast Manager base class.
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 * @since       1.6
 */
class PodcastManagerController extends JController
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   1.6
	 * @see     JController
	 */
	function __construct($config = array())
	{
		$input = JFactory::getApplication()->input;
		// Frontpage Editor podcast proxying:
		if ($input->get('view', '', 'cmd') === 'podcasts' && $input->get('layout', '', 'cmd') === 'modal')
		{
			JHtml::_('stylesheet', 'system/adminlist.css', array(), true);
			$config['base_path'] = JPATH_COMPONENT_ADMINISTRATOR;
		}

		parent::__construct($config);
	}

	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types,
	 *                               for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController  This object is to support chaining.
	 *
	 * @since   1.6
	 */
	public function display($cachable = false, $urlparams = array())
	{
		// Initialise variables.
		$input = JFactory::getApplication()->input;
		$cachable = true;
		$user = JFactory::getUser();

		// Set the default view name and format from the Request.
		$id = $input->get('p_id', '', 'int');
		$feed = $input->get('feedname', '', 'int');
		$vName = $input->get('view', 'feed', 'cmd');
		$input->set('view', $vName);

		if ($user->get('id') || ($_SERVER['REQUEST_METHOD'] == 'POST' && $vName = 'feed'))
		{
			$cachable = false;
		}

		$safeurlparams = array(
			'id' => 'INT', 'feedname' => 'INT', 'limit' => 'INT', 'limitstart' => 'INT', 'filter_order' => 'CMD', 'filter_order_Dir' => 'CMD', 'lang' => 'CMD'
		);

		// Check for edit form.
		if ($vName == 'podcast' && !$this->checkEditId('com_podcastmanager.edit.podcast', $id))
		{
			// Somehow the person just went to the form - we don't allow that.
			return JError::raiseError(403, JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
		}

		return parent::display($cachable, $safeurlparams);
	}
}
