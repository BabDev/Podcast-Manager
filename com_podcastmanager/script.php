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

/**
 * Installation class to perform additional changes during install/uninstall/update
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 * @since       1.7
 */
class Com_PodcastManagerInstallerScript
{
	/**
	 * Function to act prior to installation process begins
	 *
	 * @param   string                      $type    The action being performed
	 * @param   JInstallerAdapterComponent  $parent  The class calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since   1.7
	 */
	public function preflight($type, $parent)
	{
		// Bugfix for "Can not build admin menus"
		if (in_array($type, ['install', 'discover_install']))
		{
			$this->bugfixDBFunctionReturnedNoError();
		}
		else
		{
			$this->bugfixCantBuildAdminMenus();
		}

		return true;
	}

	/**
	 * Function to perform changes during update
	 *
	 * @param   JInstallerAdapterComponent  $parent  The class calling this method
	 *
	 * @return  void
	 *
	 * @since   2.0
	 */
	public function update($parent)
	{
		// Get the pre-update version
		$version = $this->getVersion();

		// If in error, throw a message about the language files
		if ($version == 'Error')
		{
			JError::raiseNotice(null, JText::_('COM_PODCASTMANAGER_ERROR_INSTALL_UPDATE'));

			return;
		}
	}

	/**
	 * Function to perform changes during uninstall
	 *
	 * @param   JInstallerAdapterComponent  $parent  The class calling this method
	 *
	 * @return  void
	 *
	 * @since   1.8
	 */
	public function uninstall($parent)
	{
		// Build a menu record for the media component to prevent the "cannot delete admin menu" error
		// Get the component's ID from the database
		$option = 'com_podcastmedia';
		$db     = JFactory::getDbo();
		$query  = $db->getQuery(true)
			->select($db->quoteName('extension_id'))
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('element') . ' = ' . $db->quote($option));

		try
		{
			$component_id = $db->setQuery($query)->loadResult();
		}
		catch (RuntimeException $e)
		{
			// TODO - Error handling
		}

		// Add the record
		$table = JTable::getInstance('menu');

		$data = [
			'menutype'     => 'main',
			'client_id'    => 1,
			'title'        => $option,
			'alias'        => $option,
			'link'         => 'index.php?option=' . $option,
			'type'         => 'component',
			'published'    => 0,
			'parent_id'    => 1,
			'component_id' => $component_id,
			'img'          => 'class:component',
			'home'         => 0
		];

		// All the table processing without error checks since we're hacking to prevent an error message
		if (!$table->setLocation(1, 'last-child') || !$table->bind($data) || !$table->check() || !$table->store())
		{
			// Do nothing ;-)
		}

		$query = $db->getQuery(true)
			->delete($db->quoteName('#__content_types'))
			->where($db->quoteName('type_alias') . ' LIKE ' . $db->quote('com_podcastmanager.%'));

		try
		{
			$db->setQuery($query)->execute();
		}
		catch (RuntimeException $e)
		{
			// TODO - Error handling
		}

		$query->clear()
			->delete($db->quoteName('#__contentitem_tag_map'))
			->where($db->quoteName('type_alias') . ' LIKE ' . $db->quote('com_podcastmanager.%'));

		try
		{
			$db->setQuery($query)->execute();
		}
		catch (RuntimeException $e)
		{
			// TODO - Error handling
		}

		$query->clear()
			->delete($db->quoteName('#__ucm_content'))
			->where($db->quoteName('core_type_alias') . ' LIKE ' . $db->quote('com_podcastmanager.%'));

		try
		{
			$db->setQuery($query)->execute();
		}
		catch (RuntimeException $e)
		{
			// TODO - Error handling
		}
	}

	/**
	 * Function to perform changes when component is initially installed
	 *
	 * @param   string                      $type    The action being performed
	 * @param   JInstallerAdapterComponent  $parent  The class calling this method
	 *
	 * @return  void
	 *
	 * @since   2.1
	 */
	public function postflight($type, $parent)
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		if ($type == 'install')
		{
			if (!is_dir(JPATH_ROOT . '/media/com_podcastmanager'))
			{
				JFolder::create(JPATH_ROOT . '/media/com_podcastmanager');
			}
		}

		// Deal with UCM support
		include_once JPATH_ADMINISTRATOR . '/components/com_podcastmanager/helpers/podcastmanager.php';

		PodcastManagerHelper::insertUcmRecords();
	}

	/**
	 * Joomla! 1.6+ bugfix for "DB function returned no error"
	 *
	 * @author  Nicholas K. Dionysopoulos (https://www.akeebabackup.com)
	 *
	 * @return  void
	 *
	 * @since   1.8
	 */
	private function bugfixDBFunctionReturnedNoError()
	{
		$db = JFactory::getDbo();

		// Fix broken #__assets records
		$query = $db->getQuery(true)
			->select($db->quoteName('id'))
			->from($db->quoteName('#__assets'))
			->where($db->quoteName('name') . ' = ' . $db->quote('com_podcastmanager'));

		try
		{
			$ids = $db->setQuery($query)->loadColumn();
		}
		catch (RuntimeException $e)
		{
			$ids = [];
		}

		if (!empty($ids))
		{
			foreach ($ids as $id)
			{
				$query->clear()
					->delete($db->quoteName('#__assets'))
					->where($db->quoteName('id') . ' = ' . $db->quote($id));

				try
				{
					$db->setQuery($query)->execute();
				}
				catch (RuntimeException $e)
				{
					// TODO - Error handling
				}
			}
		}

		// Fix broken #__extensions records
		$query->clear()
			->select($db->quoteName('extension_id'))
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('element') . ' = ' . $db->quote('com_podcastmanager'));

		try
		{
			$ids = $db->setQuery($query)->loadColumn();
		}
		catch (RuntimeException $e)
		{
			$ids = [];
		}

		if (!empty($ids))
		{
			foreach ($ids as $id)
			{
				$query->clear()
					->delete($db->quoteName('#__extensions'))
					->where($db->quoteName('extension_id') . ' = ' . $db->quote($id));

				try
				{
					$db->setQuery($query)->execute();
				}
				catch (RuntimeException $e)
				{
					// TODO - Error handling
				}
			}
		}

		// Fix broken #__menu records
		$query->clear()
			->select($db->quoteName('id'))
			->from($db->quoteName('#__menu'))
			->where($db->quoteName('type') . ' = ' . $db->quote('component'))
			->where($db->quoteName('menutype') . ' = ' . $db->quote('main'))
			->where($db->quoteName('link') . ' LIKE ' . $db->quote('index.php?option=com_podcastmanager%'));

		try
		{
			$ids = $db->setQuery($query)->loadColumn();
		}
		catch (RuntimeException $e)
		{
			$ids = [];
		}

		if (!empty($ids))
		{
			foreach ($ids as $id)
			{
				$query->clear()
					->delete($db->quoteName('#__menu'))
					->where($db->quoteName('id') . ' = ' . $db->quote($id));

				try
				{
					$db->setQuery($query)->execute();
				}
				catch (RuntimeException $e)
				{
					// TODO - Error handling
				}
			}
		}
	}

	/**
	 * Joomla! 1.6+ bugfix for "Can not build admin menus"
	 *
	 * @author  Nicholas K. Dionysopoulos (https://www.akeebabackup.com)
	 *
	 * @return  void
	 *
	 * @since   1.8
	 */
	private function bugfixCantBuildAdminMenus()
	{
		$db = JFactory::getDbo();

		// If there are multiple #__extensions record, keep one of them
		$query = $db->getQuery(true)
			->select($db->quoteName('extension_id'))
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('element') . ' = ' . $db->quote('com_podcastmanager'));

		try
		{
			$ids = $db->setQuery($query)->loadColumn();
		}
		catch (RuntimeException $e)
		{
			$ids = [];
		}

		if (count($ids) > 1)
		{
			asort($ids);

			// Keep the oldest id
			$extension_id = array_shift($ids);

			foreach ($ids as $id)
			{
				$query->clear()
					->delete($db->quoteName('#__extensions'))
					->where($db->quoteName('extension_id') . ' = ' . $db->quote($id));

				try
				{
					$db->setQuery($query)->execute();
				}
				catch (RuntimeException $e)
				{
					// TODO - Error handling
				}
			}
		}

		// If there are multiple assets records, delete all except the oldest one
		$query->clear()
			->select($db->quoteName('id'))
			->from($db->quoteName('#__assets'))
			->where($db->quoteName('name') . ' = ' . $db->quote('com_podcastmanager'));

		try
		{
			$ids = $db->setQuery($query)->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			$ids = [];
		}

		if (count($ids) > 1)
		{
			asort($ids);

			// Keep the oldest id
			$asset_id = array_shift($ids);

			foreach ($ids as $id)
			{
				$query->clear()
					->delete($db->quoteName('#__assets'))
					->where($db->quoteName('id') . ' = ' . $db->quote($id));

				try
				{
					$db->setQuery($query)->execute();
				}
				catch (RuntimeException $e)
				{
					// TODO - Error handling
				}
			}
		}

		// Remove #__menu records for good measure!
		$query->clear()
			->select($db->quoteName('id'))
			->from($db->quoteName('#__menu'))
			->where($db->quoteName('type') . ' = ' . $db->quote('component'))
			->where($db->quoteName('menutype') . ' = ' . $db->quote('main'))
			->where($db->quoteName('link') . ' LIKE ' . $db->quote('index.php?option=com_podcastmanager%'));

		try
		{
			$ids = $db->setQuery($query)->loadColumn();
		}
		catch (RuntimeException $e)
		{
			$ids = [];
		}

		if (!empty($ids))
		{
			foreach ($ids as $id)
			{
				$query->clear()
					->delete($db->quoteName('#__menu'))
					->where($db->quoteName('id') . ' = ' . $db->quote($id));

				try
				{
					$db->setQuery($query)->execute();
				}
				catch (RuntimeException $e)
				{
					// TODO - Error handling
				}
			}
		}
	}

	/**
	 * Function to get the currently installed version from the manifest cache
	 *
	 * @return  string  The version that is installed
	 *
	 * @since   1.7
	 */
	private function getVersion()
	{
		static $version;

		// Only retrieve the version info once
		if (!$version)
		{
			return $version;
		}

		// Get the record from the database
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('manifest_cache'))
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('element') . ' = ' . $db->quote('com_podcastmanager'));

		try
		{
			$manifest = $db->setQuery($query)->loadObject();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $e->getMessage()));

			return 'Error';
		}

		// Decode the JSON
		$record = json_decode($manifest->manifest_cache);

		// Get the version
		return $record->version;
	}
}
