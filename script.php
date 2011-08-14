<?php
/**
* Podcast Manager for Joomla!
*
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* @package		PodcastManager
* @subpackage	files_podcastmanager_minima
*
* Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
* Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
*/

/**
 * Installation class to perform additional changes during install/uninstall/update
 *
 * @package		PodcastManager
 * @subpackage	files_podcastmanager_minima
 * @since		1.8
 */
class pkg_PodcastManagerInstallerScript
{
	/**
	 * Function to perform changes during uninstall
	 *
	 * @param	string	$parent	The function calling this method
	 *
	 * @return	void
	 * @since	1.8
	 */
	function uninstall($parent)
	{
		JError::raiseNotice(null, JText::_('COM_PODCASTMANAGER_ERROR_INSTALL_J17'));
	}

	/**
	 * Function to act after the installation process runs
	 *
	 * @param	string	$type		The action being performed
	 * @param	string	$parent		The function calling this method
	 * @param	array	$results	The results of each installer action
	 *
	 * @return	void
	 * @since	1.8
	 */
	function postflight($type, $parent, $results) {
?>
<?php $rows = 0;?>
<table class="adminlist">
	<thead>
		<tr>
			<th class="title"><?php echo JText::_('PKG_PODCASTMANAGER_EXTENSION'); ?></th>
			<th width="30%"><?php echo JText::_('JSTATUS'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="2"></td>
		</tr>
	</tfoot>
	<tbody>
		<?php foreach ($results as $result) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo JText::_(strtoupper($result['name'])); ?></td>
			<td><strong>
				<?php if ($result['result'] == true) {
					echo JText::_('PKG_PODCASTMANAGER_INSTALLED');
				} else {
					JText::_('PKG_PODCASTMANAGER_NOT_INSTALLED');
				} ?></strong></td>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>
<?php }
}
