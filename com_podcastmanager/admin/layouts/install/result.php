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
		<?php foreach ($results as $result) : ?>
			<?php $extension = (string) $result['name']; ?>
			<?php $e_type = substr($extension, 0, 3); ?>
			<tr>
				<td class="key"><?php echo JText::_(strtoupper($extension)); ?></td>
				<td>
					<strong>
						<?php if ($e_type == 'com') : ?>
							<?php echo JText::_('COM_INSTALLER_TYPE_COMPONENT'); ?>
						<?php elseif ($e_type == 'mod') : ?>
							<?php echo JText::_('COM_INSTALLER_TYPE_MODULE'); ?>
						<?php elseif ($e_type == 'plg') : ?>
							<?php echo JText::_('COM_INSTALLER_TYPE_PLUGIN'); ?>
						<?php elseif ($e_type == 'get') : ?>
							<?php echo JText::_('COM_INSTALLER_TYPE_LIBRARY'); ?>
						<?php endif; ?>
					</strong>
				</td>
				<td>
					<strong>
						<?php if ($result['result'] == true) : ?>
							<?php echo JText::_('PKG_PODCASTMANAGER_INSTALLED'); ?>
						<?php else : ?>
							<?php echo JText::_('PKG_PODCASTMANAGER_NOT_INSTALLED'); ?>
						<?php endif; ?>
					</strong>
				</td>
				<td>
					<strong>
						<?php if ($enabled[$extension] == 1) : ?>
							<?php echo JText::_('JYES'); ?>
						<?php elseif ($enabled[$extension] == 2) : ?>
							<?php echo JText::_('PKG_PODCASTMANAGER_NA'); ?>
						<?php else : ?>
							<?php echo JText::_('JNO'); ?>
						<?php endif; ?>
					</strong>
				</td>
			</tr>
		<?php endforeach;?>
	</tbody>
</table>
