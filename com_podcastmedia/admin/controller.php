<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmedia
 *
 * @copyright   Copyright (C) 2011-2013 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

/**
 * Podcast Media Manager Component Controller
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmedia
 * @since       1.6
 */
class PodcastMediaController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JControllerLegacy  A JControllerLegacy object to support chaining.
	 *
	 * @since   1.6
	 */
	public function display($cachable = false, $urlparams = array())
	{
		JPluginHelper::importPlugin('content');
		$app    = JFactory::getApplication();
		$input  = $app->input;
		$params = JComponentHelper::getParams('com_podcastmedia');
		$vName  = $input->get('view', 'media', 'cmd');

		switch ($vName)
		{
			case 'audio':
				$vLayout = $input->get('layout', 'default', 'cmd');
				$mName   = 'manager';

				break;

			case 'audiolist':
				$vLayout = $input->get('layout', 'default', 'cmd');
				$mName   = 'list';

				break;

			case 'medialist':
				$vLayout = $params->get('layout', 'thumbs');
				$mName   = 'list';

				break;

			case 'media':
			default:
				$vName   = 'media';
				$vLayout = $input->get('layout', 'default', 'cmd');
				$mName   = 'manager';

				break;
		}

		$vType = JFactory::getDocument()->getType();

		// Get/Create the view
		$view = $this->getView($vName, $vType);

		// Add the template path for requests coming from the site app
		if ($app->isSite())
		{
			$view->addTemplatePath(JPATH_COMPONENT_ADMINISTRATOR . '/views/' . strtolower($vName) . '/tmpl');
		}

		// Get/Create the model
		if ($model = $this->getModel($mName))
		{
			// Push the model into the view (as default)
			$view->setModel($model, true);
		}

		// Set the layout
		$view->setLayout($vLayout);

		// Display the view
		$view->display();

		return $this;
	}

	/**
	 * Function to validate FTP credentials
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function ftpValidate()
	{
		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');
	}
}
