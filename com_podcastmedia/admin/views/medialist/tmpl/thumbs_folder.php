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
?>
<li class="imgOutline thumbnail height-80 width-80 center">
	<?php if ($user->authorise('core.delete', 'com_podcastmanager')):?>
		<a class="close delete-item" target="_top" href="index.php?option=com_podcastmedia&amp;task=folder.delete&amp;tmpl=index&amp;<?php echo JSession::getFormToken(); ?>=1&amp;folder=<?php echo $this->state->folder; ?>&amp;rm[]=<?php echo $this->_tmp_folder->name; ?>" rel="<?php echo $this->_tmp_folder->name; ?> :: <?php echo $this->_tmp_folder->files + $this->_tmp_folder->folders; ?>" title="<?php echo JText::_('JACTION_DELETE'); ?>">&#215;</a>
		<input class="pull-left" type="checkbox" name="rm[]" value="<?php echo $this->_tmp_folder->name; ?>" />
		<div class="clearfix"></div>
	<?php endif;?>
	<div class="height-50">
		<a href="index.php?option=com_podcastmedia&amp;view=medialist&amp;tmpl=component&amp;folder=<?php echo $this->_tmp_folder->path_relative; ?>" target="folderframe">
			<span class="icon-folder-2"></span>
		</a>
	</div>
	<div class="small">
		<a href="index.php?option=com_podcastmedia&amp;view=mediaList&amp;tmpl=component&amp;folder=<?php echo $this->_tmp_folder->path_relative; ?>" target="folderframe"><?php echo JHtml::_('string.truncate', $this->_tmp_folder->name, 10, false); ?></a>
	</div>
</li>
