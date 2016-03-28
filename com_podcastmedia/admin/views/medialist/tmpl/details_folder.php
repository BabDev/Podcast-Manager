<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmedia
 *
 * @copyright   Copyright (C) 2011-2015 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

$user = JFactory::getUser();

JHtml::_('bootstrap.tooltip');
?>
<tr>
	<td class="imgTotal">
		<a href="index.php?option=com_podcastmedia&amp;view=medialist&amp;tmpl=component&amp;folder=<?php echo $this->_tmp_folder->path_relative; ?>" target="folderframe">
			<span class="icon-folder-2"></span>
		</a>
	</td>
	<td class="description">
		<a href="index.php?option=com_podcastmedia&amp;view=medialist&amp;tmpl=component&amp;folder=<?php echo $this->_tmp_folder->path_relative; ?>" target="folderframe"><?php echo $this->_tmp_folder->name; ?></a>
	</td>
	<td>&#160;</td>
	<?php if ($user->authorise('core.delete', 'com_podcastmanager')) : ?>
		<td>
			<a class="delete-item" target="_top" href="index.php?option=com_podcastmedia&amp;task=folder.delete&amp;tmpl=index&amp;folder=<?php echo $this->state->folder; ?>&amp;<?php echo JSession::getFormToken(); ?>=1&amp;rm[]=<?php echo $this->_tmp_folder->name; ?>" rel="<?php echo $this->_tmp_folder->name; ?>' :: <?php echo $this->_tmp_folder->files + $this->_tmp_folder->folders; ?>"><span class="icon-remove hasTooltip" title="<?php echo JHtml::_('tooltipText', 'JACTION_DELETE');?>"></span></a>
			<input type="checkbox" name="rm[]" value="<?php echo $this->_tmp_folder->name; ?>"/>
		</td>
	<?php endif ?>
</tr>
