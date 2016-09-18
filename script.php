<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package    PodcastManager
 *
 * @copyright  Copyright (C) 2011-2015 Michael Babker. All rights reserved.
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

/**
 * Installation class to perform additional changes during install/uninstall/update
 *
 * @package  PodcastManager
 * @since    2.0
 */
class Pkg_PodcastManagerInstallerScript extends JInstallerScript
{
	/**
	 * An array of supported database types
	 *
	 * @var    array
	 * @since  2.0
	 */
	protected $dbSupport = ['mysql', 'mysqli', 'pdomysql'];

	/**
	 * Extension script constructor.
	 *
	 * @since   3.0
	 */
	public function __construct()
	{
		$this->extension     = 'pkg_podcastmanager';
		$this->minimumJoomla = '3.6';
		$this->minimumPhp    = '5.4';
	}

	/**
	 * Function to act prior to installation process begins
	 *
	 * @param   string                    $type    The action being performed
	 * @param   JInstallerAdapterPackage  $parent  The class calling this method
	 *
	 * @return  boolean
	 *
	 * @since   2.0
	 * @throws  RuntimeException
	 */
	public function preflight($type, $parent)
	{
		$return = parent::preflight($type, $parent);

		if ($return)
		{
			// Check to see if the database type is supported
			if (!in_array(JFactory::getDbo()->name, $this->dbSupport))
			{
				throw new RuntimeException(JText::_('PKG_PODCASTMANAGER_ERROR_DB_SUPPORT'));
			}
		}

		return $return;
	}

	/**
	 * Function to perform changes during update
	 *
	 * @param   JInstallerAdapterPackage  $parent  The class calling this method
	 *
	 * @return  void
	 *
	 * @since   2.1
	 */
	public function update($parent)
	{
		$this->removeHathorExtension();
		$this->removeStrappedExtension();
	}

	/**
	 * Function to act after the installation process runs
	 *
	 * @param   string                    $type     The action being performed
	 * @param   JInstallerAdapterPackage  $parent   The class calling this method
	 * @param   array                     $results  The results of each installer action
	 *
	 * @return	void
	 *
	 * @since	2.0
	 */
	public function postflight($type, $parent, $results)
	{
		// Determine whether each extension is enabled or not
		$enabled = [];
		$db      = JFactory::getDbo();
		$query   = $db->getQuery(true)
			->select($db->quoteName('enabled'))
			->from($db->quoteName('#__extensions'));

		foreach ($results as $result)
		{
			$extension = (string) $result['name'];
			$query->clear('where')
				->where($db->quoteName('name') . ' = ' . $db->quote($extension));

			try
			{
				$enabled[$extension] = $db->setQuery($query)->loadResult();
			}
			catch (RuntimeException $e)
			{
				$enabled[$extension] = 2;
			}
		}

		// Add the result table for the post-install screen
		echo (new JLayoutFile('install.result', JPATH_ADMINISTRATOR . '/components/com_podcastmanager/layouts'))->render(
			[
				'results' => $results, 'enabled' => $enabled
			]
		);
	}

	/**
	 * Removes the files_podcastmanager_hathor extension
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	private function removeHathorExtension()
	{
		// We need to get the extension ID for our Hathor layouts first
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('extension_id'))
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('name') . ' = ' . $db->quote('files_podcastmanager_hathor'));

		try
		{
			$id = $db->setQuery($query)->loadResult();
		}
		catch (RuntimeException $e)
		{
			$id = 0;
		}

		// If we don't have an ID we can assume the extension isn't installed
		if (!$id)
		{
			return;
		}

		/*
		 * Since the adapter doesn't remove folders with content, we have to remove the content here
		 * And, lucky us, the file scriptfile isn't copied!
		 */
		// Import dependencies
		jimport('joomla.filesystem.folder');

		// First, the array of folders we need to get the children for
		$folders = ['html/com_podcastmanager'];

		// Set up our full path to the folder
		$path = JPATH_ADMINISTRATOR . '/templates/hathor/html/com_podcastmanager';

		if (is_dir($path))
		{
			JFolder::delete($path);
		}

		// Now uninstall the extension
		if (!(new JInstaller)->uninstall('file', $id))
		{
			JFactory::getApplication()->enqueueMessage(JText::_('PKG_PODCASTMANAGER_FAILED_REMOVING_HATHOR_EXTENSION'), 'warning');
		}
		else
		{
			JFactory::getApplication()->enqueueMessage(JText::_('PKG_PODCASTMANAGER_HATHOR_EXTENSION_REMOVED'), 'notice');
		}
	}

	/**
	 * Removes the files_podcastmanager_strapped extension
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	private function removeStrappedExtension()
	{
		// We need to get the extension ID for our Strapped layouts first
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('extension_id'))
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('name') . ' = ' . $db->quote('files_podcastmanager_strapped'));

		try
		{
			$id = $db->setQuery($query)->loadResult();
		}
		catch (RuntimeException $e)
		{
			$failed = true;
		}

		// If we don't have an ID we can assume the extension isn't installed
		if (!$id)
		{
			return;
		}

		/*
		 * Since the adapter doesn't remove folders with content, we have to remove the content here
		 * And, lucky us, the file scriptfile isn't copied!
		 */
		// Import dependencies
		jimport('joomla.filesystem.folder');

		// Set up our base path
		$base = JPATH_ADMINISTRATOR . '/templates/isis/';

		// Process our parent folders
		foreach (['html/com_podcastmanager', 'html/com_podcastmedia', 'js/podcastmanager'] as $folder)
		{
			// Set up our full path to the folder
			$path = $base . $folder;

			if (is_dir($path))
			{
				JFolder::delete($path);
			}
		}

		if (!(new JInstaller)->uninstall('file', $id))
		{
			JFactory::getApplication()->enqueueMessage(JText::_('PKG_PODCASTMANAGER_FAILED_REMOVING_STRAPPED_EXTENSION'), 'warning');
		}
		else
		{
			JFactory::getApplication()->enqueueMessage(JText::_('PKG_PODCASTMANAGER_STRAPPED_EXTENSION_REMOVED'), 'notice');
		}
	}
}
