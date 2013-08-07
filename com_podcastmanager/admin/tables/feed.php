<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 *
 * @copyright   Copyright (C) 2011-2013 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

/**
 * Feed table interaction class.
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 * @since       1.7
 */
class PodcastManagerTableFeed extends JTable
{
	/**
	 * Helper object for storing and deleting tag information.
	 *
	 * @var    JHelperTags
	 * @since  2.1
	 */
	protected $tagsHelper;

	/**
	 * The class constructor.
	 *
	 * @param   JDatabase  &$db  JDatabase connector object.
	 *
	 * @since   1.7
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__podcastmanager_feeds', 'id', $db);

		// Tags support in CMS 3.1+
		if (version_compare(JVERSION, '3.1', 'ge'))
		{
			$this->tagsHelper = new JHelperTags;
			$this->tagsHelper->typeAlias = 'com_podcastmanager.feed';
		}
	}

	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form table_name.id
	 * where id is the value of the primary key of the table.
	 *
	 * @return  string
	 *
	 * @since   2.0
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;

		return 'com_podcastmanager.feed.' . (int) $this->$k;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return  string
	 *
	 * @since   2.0
	 */
	protected function _getAssetTitle()
	{
		return $this->name;
	}

	/**
	 * Method to get the parent asset id for the record
	 *
	 * @param   JTable   $table  A JTable object for the asset parent
	 * @param   integer  $id     Id to look up
	 *
	 * @return  integer  The parent id
	 *
	 * @since   11.1
	 */
	protected function _getAssetParentId($table = null, $id = null)
	{
		// Initialise variables.
		$assetId = null;
		$db = $this->getDbo();

		// Feeds are nested directly underneath the component.
		if ($assetId === null)
		{
			// Build the query to get the asset id for the component.
			$query = $db->getQuery(true);
			$query->select($db->quoteName('id'));
			$query->from($db->quoteName('#__assets'));
			$query->where($db->quoteName('name') . ' = ' . $db->quote('com_podcastmanager'));

			// Get the asset id from the database.
			$db->setQuery($query);

			if ($result = $db->loadResult())
			{
				$assetId = (int) $result;
			}
		}

		// Return the asset id.
		if ($assetId)
		{
			return $assetId;
		}
		else
		{
			return parent::_getAssetParentId($table, $id);
		}
	}

	/**
	 * Overloaded bind function.
	 *
	 * @param   array   $array   Named array
	 * @param   string  $ignore  An optional array or space separated list of properties
	 *                           to ignore while binding.
	 *
	 * @return  mixed  Null if operation was satisfactory, otherwise returns an error
	 *
	 * @since   2.0
	 * @see     JTable:bind()
	 */
	public function bind($array, $ignore = '')
	{
		// Bind the metadata.
		if (isset($array['metadata']) && is_array($array['metadata']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['metadata']);
			$array['metadata'] = (string) $registry;
		}

		// Bind the rules.
		if (isset($array['rules']) && is_array($array['rules']))
		{
			$rules = new JAccessRules($array['rules']);
			$this->setRules($rules);
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Method to perform sanity checks on the JTable instance properties to ensure
	 * they are safe to store in the database.  Child classes should override this
	 * method to make sure the data they are storing in the database is safe and
	 * as expected before storage.
	 *
	 * @return  boolean  True if the instance is sane and able to be stored in the database.
	 *
	 * @since   2.1
	 * @see     JTable::check()
	 */
	public function check()
	{
		if (trim($this->alias) == '')
		{
			$this->alias = $this->name;
		}

		$this->alias = JApplication::stringURLSafe($this->alias);

		if (trim(str_replace('-', '', $this->alias)) == '')
		{
			$this->alias = JFactory::getDate()->format('Y-m-d-H-i-s');
		}

		return true;
	}

	/**
	 * Override parent delete method to delete tags information.
	 *
	 * @param   integer  $pk  Primary key to delete.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.1
	 */
	public function delete($pk = null)
	{
		$result = parent::delete($pk);

		// Tags support in CMS 3.1+
		if (version_compare(JVERSION, '3.1', 'ge'))
		{
			$tagResult = $this->tagsHelper->deleteTagData($this, $pk);
		}
		else
		{
			$tagResult = true;
		}

		return $result && $tagResult;
	}

	/**
	 * Overriden JTable::store to set modified data and user id.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.7
	 */
	public function store($updateNulls = false)
	{
		$date = JFactory::getDate();
		$user = JFactory::getUser();

		if ($this->id)
		{
			// Existing item
			$this->modified = $date->toSQL();
			$this->modified_by = $user->get('id');
		}
		else
		{
			// New item. A feed's created field can be set by the user,
			// so we don't touch it if it is set.
			if (!intval($this->created))
			{
				$this->created = $date->toSQL();
			}

			if (empty($this->created_by))
			{
				$this->created_by = $user->get('id');
			}
		}

		// Tags support in CMS 3.1+
		if (version_compare(JVERSION, '3.1', 'ge'))
		{
			$this->tagsHelper->preStoreProcess($this);
		}

		$result = parent::store($updateNulls);

		// Tags support in CMS 3.1+
		if (version_compare(JVERSION, '3.1', 'ge'))
		{
			$tagResult = $this->tagsHelper->postStoreProcess($this);
		}
		else
		{
			$tagResult = true;
		}

		return $result && $tagResult;
	}
}
