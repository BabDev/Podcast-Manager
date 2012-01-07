<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  plg_finder_podcastmanager_feeds
 *
 * @copyright   Copyright (C) 2011-2012 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.helper');

// Load the base adapter.
require_once JPATH_ADMINISTRATOR . '/components/com_finder/helpers/indexer/adapter.php';

/**
 * Finder adapter for Podcast Manager Feeds.
 *
 * @package     PodcastManager
 * @subpackage  plg_finder_podcastmanager_feeds
 * @since       2.0
 */
class PlgFinderPodcastManager_Feeds extends FinderIndexerAdapter
{
	/**
	 * The plugin identifier.
	 *
	 * @var    string
	 * @since  2.0
	 */
	protected $context = 'PodcastManager_Feeds';

	/**
	 * The extension name.
	 *
	 * @var    string
	 * @since  2.0
	 */
	protected $extension = 'com_podcastmanager';

	/**
	 * The sublayout to use when rendering the results.
	 *
	 * @var    string
	 * @since  2.0
	 */
	protected $layout = 'feed';

	/**
	 * The type of content that the adapter indexes.
	 *
	 * @var    string
	 * @since  2.0
	 */
	protected $type_title = 'Podcast Feed';

	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An array that holds the plugin configuration
	 *
	 * @since   2.0
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * Method to remove the link information for items that have been deleted.
	 *
	 * @param   string  $context  The context of the action being performed.
	 * @param   JTable  $table    A JTable object containing the record to be deleted
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.0
	 * @throws  Exception on database error.
	 */
	public function onFinderAfterDelete($context, $table)
	{
		if ($context == 'com_podcastmanager.feed')
		{
			$id = $table->id;
		}
		elseif ($context == 'com_finder.index')
		{
			$id = $table->link_id;
		}
		else
		{
			return true;
		}
		// Remove the items.
		return $this->remove($id);
	}

	/**
	 * Method to update the link information for items that have been changed
	 * from outside the edit screen. This is fired when the item is published,
	 * unpublished, archived, or unarchived from the list view.
	 *
	 * @param   string   $context  The context for the content passed to the plugin.
	 * @param   array    $pks      A list of primary key ids of the content that has changed state.
	 * @param   integer  $value    The value of the state that the content has been changed to.
	 *
	 * @return  void
	 *
	 * @since   2.0
	 */
	public function onFinderChangeState($context, $pks, $value)
	{
		// We only want to handle podcast feeds here
		if ($context == 'com_podcastmanager.feed')
		{
			foreach ($pks as $pk)
			{
				$sql = clone($this->_getStateQuery());
				$sql->where('a.id = ' . (int) $pk);

				// Get the published states.
				$this->db->setQuery($sql);
				$item = $this->db->loadObject();

				// Translate the state.
				$temp = $this->translateState($value);

				// Update the item.
				$this->change($pk, 'state', $temp);

				// Manually reindex the item since someone removed the auto updater...
				// Run the setup method.
				$this->setup();

				// Get the item.
				$item = $this->getItem($row->id);

				// Index the item.
				$this->index($item);

				// The right way to queue the item to be reindexed...
				//FinderIndexerQueue::add($context, $pk, JFactory::getDate()->toSQL());
			}
		}

		// Handle when the plugin is disabled
		if ($context == 'com_plugins.plugin' && $value === 0)
		{
			// Since multiple plugins may be disabled at a time, we need to check first
			// that we're handling podcast feeds
			foreach ($pks as $pk)
			{
				if ($this->getPluginType($pk) == 'podcastmanager_feeds')
				{
					// Get all of the podcast feeds to unindex them
					$sql = clone($this->_getStateQuery());
					$this->db->setQuery($sql);
					$items = $this->db->loadColumn();

					// Remove each item
					foreach ($items as $item)
					{
						$this->remove($item);
					}
				}
			}
		}
	}

	/**
	 * Method to index an item. The item must be a FinderIndexerResult object.
	 *
	 * @param   FinderIndexerResult  $item  The item to index as an FinderIndexerResult object.
	 *
	 * @return  void
	 *
	 * @since   2.0
	 * @throws  Exception on database error.
	 */
	protected function index(FinderIndexerResult $item)
	{
		// Check if the extension is enabled
		if (JComponentHelper::isEnabled($this->extension) == false)
		{
			return;
		}

		// Build the necessary route and path information.
		$item->url = $this->getURL($item->id, $this->extension, $this->layout);
		$item->route = PodcastManagerHelperRoute::getFeedHtmlRoute($item->id);
		$item->path = FinderIndexerHelper::getContentPath($item->route);

		// Handle the link to the meta-data.
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'link');

		// Set the language.
		$item->language = FinderIndexerHelper::getDefaultLanguage();

		// Set the metadata based on the feed's data
		$item->metaauthor = $item->author;

		// Add the metadata.
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'link');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metaauthor');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'author');

		// Add the taxonomy data.
		$item->addTaxonomy('Type', $this->type_title);
		$item->addTaxonomy('Language', $item->language);

		// Get content extras.
		FinderIndexerHelper::getContentExtras($item);

		// Index the item.
		FinderIndexer::index($item);
	}

	/**
	 * Method to setup the indexer to be run.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.0
	 */
	protected function setup()
	{
		// Load dependent classes.
		require_once JPATH_SITE . '/components/com_podcastmanager/helpers/route.php';

		return true;
	}

	/**
	 * Method to get the SQL query used to retrieve the list of content items.
	 *
	 * @param   mixed  $sql  A JDatabaseQuery object or null.
	 *
	 * @return  JDatabaseQuery  A database object.
	 *
	 * @since   2.0
	 */
	protected function getListQuery($sql = null)
	{
		$db = JFactory::getDbo();
		// Check if we can use the supplied SQL query.
		$sql = is_a($sql, 'JDatabaseQuery') ? $sql : $db->getQuery(true);
		$sql->select($this->db->quoteName('id'));
		$sql->select($this->db->quoteName('name') . ' AS title');
		$sql->select($this->db->quoteName('description') . ' AS summary');
		$sql->select($this->db->quoteName('published') . ' AS state');
		$sql->select($this->db->quoteName('created') . ' AS start_date');
		$sql->select($this->db->quoteName('author'));
		$sql->select($this->db->quoteName('language'));
		$sql->select('0 AS publish_start_date');
		$sql->select('0 AS publish_end_date');
		$sql->select('1 AS access');
		$sql->from($this->db->quoteName('#__podcastmanager_feeds'));

		return $sql;
	}

	/**
	 * Method to get the URL for the item. The URL is how we look up the link
	 * in the Finder index.
	 *
	 * @param   integer  $id         The id of the item.
	 * @param   string   $extension  The extension the item is in.
	 * @param   string   $view       The view for the URL.
	 *
	 * @return  string  The URL of the item.
	 *
	 * @since   2.0
	 */
	protected function getURL($id, $extension, $view)
	{
		return 'index.php?option=' . $extension . '&view=' . $view . '&layout=feed&feedname=' . $id;
	}

	/**
	 * Method to get a SQL query to load the published and access states for
	 * a news feed and category.
	 *
	 * @return  JDatabaseQuery  A database object.
	 *
	 * @since   2.0
	 */
	private function _getStateQuery()
	{
		$sql = $this->db->getQuery(true);
		$sql->select($this->db->quoteName('id'));
		$sql->select($this->db->quoteName('published') . ' AS state');
		$sql->from($this->db->quoteName('#__podcastmanager_feeds'));

		return $sql;
	}
}
