<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package    PodcastManager
 *
 * @copyright  Copyright (C) 2011-2012 Michael Babker. All rights reserved.
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
	protected $dbSupport = array('mysql', 'mysqli', 'postgresql', 'sqlsrv');

	/**
	 * Function to act prior to installation process begins
	 *
	 * @param   string             $type    The action being performed
	 * @param   JInstallerPackage  $parent  The class calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since   2.0
	 */
	public function preflight($type, $parent)
	{
		// Requires Joomla! 2.5.4
		$jversion = new JVersion;
		if (version_compare($jversion->getShortVersion(), '2.5.4', 'lt'))
		{
			JError::raiseNotice(null, JText::_('PKG_PODCASTMANAGER_ERROR_INSTALL_JVERSION'));
			return false;
		}

		// Check to see if the database type is supported
		$db = JFactory::getDbo();
		if (!in_array($db->name, $this->dbSupport))
		{
			JError::raiseNotice(null, JText::_('PKG_PODCASTMANAGER_ERROR_DB_SUPPORT'));
			return false;
		}

		return true;
	}

	/**
	 * Function to act after the installation process runs
	 *
	 * @param   string             $type     The action being performed
	 * @param   JInstallerPackage  $parent   The class calling this method
	 * @param   array              $results  The results of each installer action
	 *
	 * @return	void
	 *
	 * @since	2.0
	 */
	public function postflight($type, $parent, $results)
	{
		// Determine whether each plugin is enabled or not
		$enabled = array();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		foreach ($results as $result)
		{
			$extension = (string) $result['name'];
			$query->clear();
			$query->select($db->quoteName('enabled'));
			$query->from($db->quoteName('#__extensions'));
			$query->where($db->quoteName('name') . ' = ' . $db->quote($extension));
			$db->setQuery($query);
			$enabled[$extension] = $db->loadResult();
		}
		?>
		<table class="adminlist">
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
				<?php foreach ($results as $result)
				{
					$extension = (string) $result['name'];
					$e_type = substr($extension, 0, 3); ?>
				<tr class="row<?php echo ($result % 2); ?>">
					<td class="key"><?php echo JText::_(strtoupper($extension)); ?></td>
					<td><strong>
						<?php if ($e_type == 'com')
						{
							echo JText::_('COM_INSTALLER_TYPE_COMPONENT');
						}
						elseif ($e_type == 'mod')
						{
							echo JText::_('COM_INSTALLER_TYPE_MODULE');
						}
						elseif ($e_type == 'plg')
						{
							echo JText::_('COM_INSTALLER_TYPE_PLUGIN');
						}
						elseif ($e_type == 'get')
						{
							echo JText::_('COM_INSTALLER_TYPE_LIBRARY');
						} ?></strong>
					</td>
					<td><strong>
						<?php if ($result['result'] == true)
						{
							echo JText::_('PKG_PODCASTMANAGER_INSTALLED');
						}
						else
						{
							echo JText::_('PKG_PODCASTMANAGER_NOT_INSTALLED');
						} ?></strong>
					</td>
					<td><strong>
						<?php if ($enabled[$extension] == 1)
						{
							echo JText::_('JYES');
						}
						else
						{
							echo JText::_('JNO');
						} ?></strong>
					</td>
				</tr>
				<?php
				} ?>
			</tbody>
		</table>
		<?php
	}
}
