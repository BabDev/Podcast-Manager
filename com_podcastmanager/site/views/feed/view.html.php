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

JLoader::register('PodcastManagerHelper', JPATH_ADMINISTRATOR . '/components/com_podcastmanager/helpers/podcastmanager.php');

/**
 * Feed HTML view class.
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 * @since       1.8
 */
class PodcastManagerViewFeed extends JViewLegacy
{
	/**
	 * The user object
	 *
	 * @var    JUser
	 * @since  1.8
	 */
	protected $user;

	/**
	 * The params object
	 *
	 * @var    JObject
	 * @since  1.8
	 */
	protected $params;

	/**
	 * The state information
	 *
	 * @var    JObject
	 * @since  1.8
	 */
	protected $state;

	/**
	 * The items to display
	 *
	 * @var    array
	 * @since  1.8
	 */
	protected $items;

	/**
	 * The feed record
	 *
	 * @var    object
	 * @since  1.8
	 */
	protected $feed;

	/**
	 * The pagination object
	 *
	 * @var    JPagination
	 * @since  1.8
	 */
	protected $pagination;

	/**
	 * The CSS class suffix for the page
	 *
	 * @var    string
	 * @since  1.8
	 */
	protected $pageclass_sfx;

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
		$app = JFactory::getApplication();

		// Initialise the params and user objects
		$this->params = $app->getParams();
		$this->user = JFactory::getUser();

		// Get some data from the models
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->feed = $this->get('Feed');
		$this->pagination = $this->get('Pagination');

		// Items shortcut
		$items = $this->items;

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		// Prepare the content (runs content plugins).
		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			$item = &$items[$i];
			$item->player = '{podcast ' . $item->title . '}';

			// Set the text object to prevent errors with other plugins
			$item->text = '';
			$dispatcher = JEventDispatcher::getInstance();

			// Process the content plugins.
			JPluginHelper::importPlugin('content');
			$dispatcher->trigger('onContentPrepare', ['com_podcastmanager.feed', &$item, &$this->params]);
		}

		// Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

		// Check for layout override only if this is not the active menu item
		// If it is the active menu item, then the view and category id will match
		$active = $app->getMenu()->getActive();

		if (isset($active->query['layout']))
		{
			// We need to set the layout in case this is an alternative menu item (with an alternative layout)
			$this->setLayout($active->query['layout']);
		}

		$this->prepareDocument($this->feed);

		// Add external behaviors
		JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
		JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

		return parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @param   object  $feed  The feed object
	 *
	 * @return  void
	 *
	 * @since   1.8
	 */
	protected function prepareDocument($feed)
	{
		$app = JFactory::getApplication();
		$menus = $app->getMenu();
		$pathway = $app->getPathway();

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_PODCASTMANAGER_DEFAULT_PAGE_TITLE'));
		}

		$id = (int) @$menu->query['id'];

		if (isset($this->feed->id) && $menu && ($menu->query['option'] != 'com_podcastmanager' || $id != $this->feed->id))
		{
			$this->params->set('page_subheading', $this->feed->name);
			$path = [['title' => $this->feed->name, 'link' => '']];

			$path = array_reverse($path);

			foreach ($path as $item)
			{
				$pathway->addItem($item['title'], $item['link']);
			}
		}

		$title = $this->params->get('page_title', '');

		if (empty($title))
		{
			$title = $app->get('sitename');
		}
		elseif ($app->get('sitename_pagetitles', 0) == 1)
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
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}

		if (isset($this->feed->author))
		{
			$this->document->setMetaData('author', $this->feed->author);
		}

		// Add alternative feed link
		if (isset($this->feed->id) && $this->params->get('show_feed_link', 1) == 1)
		{
			$link	= '&format=raw&layout=default&feedname=' . $this->feed->id;
			$attribs = ['type' => 'application/rss+xml', 'title' => 'RSS 2.0'];
			$this->document->addHeadLink(JRoute::_($link), 'alternate', 'rel', $attribs);
		}
	}
}
