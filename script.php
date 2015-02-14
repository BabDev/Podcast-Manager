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
class Pkg_PodcastManagerInstallerScript
{
	/**
	 * An array of supported database types
	 *
	 * @var    array
	 * @since  2.0
	 */
	protected $dbSupport = array('mysql', 'mysqli', 'postgresql', 'sqlsrv', 'sqlazure');

	/**
	 * Function to act prior to installation process begins
	 *
	 * @param   string                    $type    The action being performed
	 * @param   JInstallerAdapterPackage  $parent  The class calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since   2.0
	 */
	public function preflight($type, $parent)
	{
		// Requires PHP 5.3
		if (version_compare(PHP_VERSION, '5.3', 'lt'))
		{
			JError::raiseNotice(null, JText::_('PKG_PODCASTMANAGER_ERROR_INSTALL_PHPVERSION'));

			return false;
		}

		// Requires Joomla! 3.4.0
		if (version_compare(JVERSION, '3.4.0', 'lt'))
		{
			JError::raiseNotice(null, JText::_('PKG_PODCASTMANAGER_ERROR_INSTALL_JVERSION'));

			return false;
		}

		// Check to see if the database type is supported
		if (!in_array(JFactory::getDbo()->name, $this->dbSupport))
		{
			JError::raiseNotice(null, JText::_('PKG_PODCASTMANAGER_ERROR_DB_SUPPORT'));

			return false;
		}

		return true;
	}

	/**
	 * Function to perform changes during uninstall
	 *
	 * @param   JInstallerAdapterPackage  $parent  The class calling this method
	 *
	 * @return  void
	 *
	 * @since   2.1
	 */
	public function uninstall($parent)
	{
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
		// Get the pre-update version
		$version = $this->getVersion();

		// If in error, throw a message about the language files
		if ($version == 'Error')
		{
			JError::raiseNotice(null, JText::_('COM_PODCASTMANAGER_ERROR_INSTALL_UPDATE'));

			return;
		}

		// If coming from 2.x, remove the strapped extension
		if (version_compare($version, '3.0', 'lt'))
		{
			$this->removeStrappedExtension();
		}
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
		$enabled = array();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('enabled'));
		$query->from($db->quoteName('#__extensions'));

		foreach ($results as $result)
		{
			$extension = (string) $result['name'];
			$query->clear('where');
			$query->where($db->quoteName('name') . ' = ' . $db->quote($extension));
			$db->setQuery($query);

			try
			{
				$enabled[$extension] = $db->loadResult();
			}
			catch (RuntimeException $e)
			{
				$enabled[$extension] = 2;
			}
		}
		?>
		<table class="table table-striped">
			<thead>
				<tr>
					<th class="title"><?php echo JText::_('PKG_PODCASTMANAGER_EXTENSION'); ?></th>
					<th class="title" width="20%"><?php echo JText::_('PKG_PODCASTMANAGER_TYPE'); ?></th>
					<th class="title" width="20%"><?php echo JText::_('JSTATUS'); ?></th>
					<th class="title" width="15%"><?php echo JText::_('JENABLED'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="4"></td>
				</tr>
			</tfoot>
			<tbody>
				<?php foreach ($results as $result) :
					$extension = (string) $result['name'];
					$e_type = substr($extension, 0, 3); ?>
				<tr class="row<?php echo ($result % 2); ?>">
					<td class="key"><?php echo JText::_(strtoupper($extension)); ?></td>
					<td><strong>
						<?php if ($e_type == 'com') :
							echo JText::_('COM_INSTALLER_TYPE_COMPONENT');
						elseif ($e_type == 'mod') :
							echo JText::_('COM_INSTALLER_TYPE_MODULE');
						elseif ($e_type == 'plg') :
							echo JText::_('COM_INSTALLER_TYPE_PLUGIN');
						elseif ($e_type == 'get') :
							echo JText::_('COM_INSTALLER_TYPE_LIBRARY');
						endif; ?></strong>
					</td>
					<td><strong>
						<?php if ($result['result'] == true) :
							echo JText::_('PKG_PODCASTMANAGER_INSTALLED');
						else :
							echo JText::_('PKG_PODCASTMANAGER_NOT_INSTALLED');
						endif; ?></strong>
					</td>
					<td><strong>
						<?php if ($enabled[$extension] == 1) :
							echo JText::_('JYES');
						elseif ($enabled[$extension] == 2) :
							echo JText::_('PKG_PODCASTMANAGER_NA');
						else :
							echo JText::_('JNO');
						endif; ?></strong>
					</td>
				</tr>
				<?php endforeach;?>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Function to get the currently installed version from the manifest cache
	 *
	 * @return  string  The version that is installed
	 *
	 * @since   3.0
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
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('manifest_cache'));
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('element') . ' = ' . $db->quote('pkg_podcastmanager'));
		$db->setQuery($query);

		try
		{
			$manifest = $db->loadObject();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $e->getMessage()));
			$version = 'Error';

			return $version;
		}

		// Decode the JSON
		$record = json_decode($manifest->manifest_cache);

		// Get the version
		$version = $record->version;

		return $version;
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
		/*
		 * Since the adapter doesn't remove folders with content, we have to remove the content here
		 * And, lucky us, the file scriptfile isn't copied!
		 */
		// Import dependencies
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		// First, the array of folders we need to get the children for
		$folders = array('html/com_podcastmanager', 'html/com_podcastmedia', 'js/podcastmanager');

		// Set up our base path
		$base = JPATH_ADMINISTRATOR . '/templates/isis/';

		// Process our parent folders
		foreach ($folders as $folder)
		{
			// Set up our full path to the folder
			$path = $base . $folder;

			// Get the list of child folders
			$children = JFolder::folders($path);

			if (count($children))
			{
				// Process the child folders and remove their files
				foreach ($children as $child)
				{
					// Set the path for the child
					$cPath = $path . '/' . $child;

					// Get the list of files
					$files = JFolder::files($cPath);

					// Now, remove the files
					foreach ($files as $file)
					{
						JFile::delete($cPath . '/' . $file);
					}
				}
			}
		}

		$failed = false;

		// We need to get the extension ID for our Strapped layouts first
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('extension_id'));
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('name') . ' = ' . $db->quote('files_podcastmanager_strapped'));
		$db->setQuery($query);

		try
		{
			$id = $db->loadResult();

			// Instantiate a new installer instance and uninstall the layouts if present
			if ($id)
			{
				$installer = new JInstaller;

				if (!$installer->uninstall('file', $id))
				{
					$failed = true;
				}
			}
		}
		catch (RuntimeException $e)
		{
			$failed = true;
		}

		if ($failed)
		{
			JError::raiseWarning(1, JText::_('PKG_PODCASTMANAGER_FAILED_REMOVING_STRAPPED_EXTENSION'));
		}
	}
}
