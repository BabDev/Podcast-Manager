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
 * Feed edit view class.
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 * @since       1.8
 */
class PodcastManagerViewForm extends JViewLegacy
{
	/**
	 * The user object
	 *
	 * @var    JUser
	 * @since  1.8
	 */
	protected $user;

	/**
	 * The form object
	 *
	 * @var    JForm
	 * @since  1.8
	 */
	protected $form;

	/**
	 * The item record
	 *
	 * @var    JObject
	 * @since  1.8
	 */
	protected $item;

	/**
	 * The state information
	 *
	 * @var    JObject
	 * @since  1.8
	 */
	protected $state;

	/**
	 * The params object
	 *
	 * @var    JObject
	 * @since  1.8
	 */
	protected $params;

	/**
	 * The page to return to after edit
	 *
	 * @var    string
	 * @since  1.8
	 */
	protected $return_page;

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
		// Initialise variables.
		$this->user = JFactory::getUser();

		// Get model data.
		$this->state       = $this->get('State');
		$this->item        = $this->get('Item');
		$this->form        = $this->get('Form');
		$this->return_page = $this->get('ReturnPage');
		$this->params      = $this->state->params;

		if (empty($this->item->id))
		{
			$authorised = $this->user->authorise('core.create', 'com_podcastmanager');
		}
		else
		{
			$authorised = $this->user->authorise('core.edit', 'com_podcastmanager.feed.' . $this->item->id);
		}

		if ($authorised !== true)
		{
			JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));

			return false;
		}

		if (!empty($this->item))
		{
			$this->form->bind($this->item);
		}

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseWarning(500, implode("\n", $errors));

			return false;
		}

		// Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

		// Make text JS available
		JText::script('JGLOBAL_VALIDATION_FORM_FAILED');

		$this->prepareDocument();

		return parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return  void
	 *
	 * @since   1.8
	 */
	protected function prepareDocument()
	{
		$app = JFactory::getApplication();

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $app->getMenu()->getActive();

		if (empty($this->item->id))
		{
			$head = JText::_('COM_PODCASTMANAGER_FORM_ADD_FEED');
		}
		else
		{
			$head = JText::_('COM_PODCASTMANAGER_FORM_EDIT_FEED');
		}

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', $head);
		}

		$title = $this->params->def('page_title', $head);

		if ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}

		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetaData('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetaData('robots', $this->params->get('robots'));
		}
	}
}
