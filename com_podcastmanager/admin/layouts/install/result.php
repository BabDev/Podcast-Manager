<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Layout variables
 * -----------------
 * @var   array  $results  Results array of installed extensions
 * @var   array  $enabled  Results array of installed extensions enabled status
 */
extract($displayData);
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
	<tbody>
		<?php foreach ($results as $result) :
			$extension = (string) $result['name'];
			$e_type = substr($extension, 0, 3); ?>
			<tr>
				<td class="key"><?php echo JText::_(strtoupper($extension)); ?></td>
				<td>
					<strong>
						<?php if ($e_type == 'com') :
							echo JText::_('COM_INSTALLER_TYPE_COMPONENT');
						elseif ($e_type == 'mod') :
							echo JText::_('COM_INSTALLER_TYPE_MODULE');
						elseif ($e_type == 'plg') :
							echo JText::_('COM_INSTALLER_TYPE_PLUGIN');
						elseif ($e_type == 'get') :
							echo JText::_('COM_INSTALLER_TYPE_LIBRARY');
						endif; ?>
					</strong>
				</td>
				<td>
					<strong>
						<?php if ($result['result'] == true) :
							echo JText::_('PKG_PODCASTMANAGER_INSTALLED');
						else :
							echo JText::_('PKG_PODCASTMANAGER_NOT_INSTALLED');
						endif; ?>
					</strong>
				</td>
				<td>
					<strong>
						<?php if ($enabled[$extension] == 1) :
							echo JText::_('JYES');
						elseif ($enabled[$extension] == 2) :
							echo JText::_('PKG_PODCASTMANAGER_NA');
						else :
							echo JText::_('JNO');
						endif; ?>
					</strong>
				</td>
			</tr>
		<?php endforeach;?>
	</tbody>
</table>
