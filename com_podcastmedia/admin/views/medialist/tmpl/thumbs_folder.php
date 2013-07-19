<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmedia
 *
 * @copyright   Copyright (C) 2011-2013 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

$user = JFactory::getUser();
?>
<div class="imgOutline">
	<div class="imgTotal">
		<div align="center" class="imgBorder">
			<a href="index.php?option=com_podcastmedia&amp;view=medialist&amp;tmpl=component&amp;folder=<?php echo $this->_tmp_folder->path_relative; ?>" target="folderframe">
				<?php echo JHtml::_('image', 'media/folder.png', JText::_('COM_PODCASTMEDIA_FOLDER'), array('width' => 80, 'height' => 80, 'border' => 0), true); ?>
			</a>
		</div>
	</div>
	<div class="controls">
	<?php if ($user->authorise('core.delete', 'com_podcastmanager'))
	{ ?>
		<a class="delete-item" target="_top" href="index.php?option=com_podcastmedia&amp;task=folder.delete&amp;tmpl=index&amp;<?php echo JFactory::getSession()->getFormToken(); ?>=1&amp;folder=<?php echo $this->state->folder; ?>&amp;rm[]=<?php echo $this->_tmp_folder->name; ?>" rel="<?php echo $this->_tmp_folder->name; ?> :: <?php echo $this->_tmp_folder->files + $this->_tmp_folder->folders; ?>">
			<?php echo JHtml::_('image', 'media/remove.png', JText::_('JACTION_DELETE'), array('width' => 16, 'height' => 16, 'border' => 0), true); ?>
		</a>
		<input type="checkbox" name="rm[]" value="<?php echo $this->_tmp_folder->name; ?>" />
	<?php } ?>
	</div>
	<div class="imginfoBorder">
		<a href="index.php?option=com_podcastmedia&amp;view=medialist&amp;tmpl=component&amp;folder=<?php echo $this->_tmp_folder->path_relative; ?>" target="folderframe">
			<?php echo substr($this->_tmp_folder->name, 0, 10) . (strlen($this->_tmp_folder->name) > 10 ? '...' : ''); ?>
		</a>
	</div>
</div>
