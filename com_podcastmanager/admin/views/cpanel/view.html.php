<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 *
 * @copyright   Copyright (C) 2011-2015 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

/**
 * Cpanel view class.
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 * @since       1.8
 */
class PodcastManagerViewCpanel extends JViewLegacy
{
	/**
	 * Container for Joomla! 3 migration errors
	 *
	 * @var    array
	 * @since  2.2
	 */
	protected $migrationErrors = array();

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @since   1.8
	 */
	public function display($tpl = null)
	{
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		// Check for migration errors for Joomla! 3
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$this->migrationErrors = $this->get('migrationErrors');
		}

		// Add the component media
		JHtml::_('stylesheet', 'podcastmanager/template.css', false, true, false);

		$this->addToolbar();

		return parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.8
	 */
	protected function addToolbar()
	{
		$canDo = PodcastManagerHelper::getActions();

		JToolBarHelper::title(JText::_('COM_PODCASTMANAGER'), 'podcastmanager.png');

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_podcastmanager');
		}
	}

	/**
	 * Generates the data for the control panel buttons
	 *
	 * @return  array  Array of button data
	 *
	 * @since   2.1
	 */
	protected function getButtons()
	{
		// Initialize the array of button options
		$buttons = array();

		// Set the array of views
		$views = array('feeds', 'podcasts', 'files');

		// Set the icons
		$iconBase = JUri::root() . 'media/podcastmanager/images/icons/';
		$icons = array(
			'feeds' => $iconBase . 'feeds.png',
			'podcasts' => $iconBase . 'podcasts.png',
			'files' => $iconBase . 'files.png'
		);

		// Set the BS classes
		$classes = array(
			'feeds' => 'feed',
			'podcasts' => 'broadcast',
			'files' => 'pictures'
		);

		// Build the buttons array
		foreach ($views as $view)
		{
			$button = array();

			if ($view == 'files')
			{
				$button['link'] = JRoute::_('index.php?option=com_podcastmedia&view=media');
			}
			else
			{
				$button['link'] = JRoute::_('index.php?option=com_podcastmanager&view=' . $view);
			}

			if (version_compare(JVERSION, '3.0', 'ge'))
			{
				$button['image'] = $classes[$view];
			}
			else
			{
				$button['image'] = $icons[$view];
			}

			$button['text'] = JText::_('COM_PODCASTMANAGER_SUBMENU_' . strtoupper($view));

			$buttons[] = $button;
		}

		return $buttons;
	}
}
