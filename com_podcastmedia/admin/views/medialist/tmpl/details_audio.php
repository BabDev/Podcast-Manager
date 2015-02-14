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
$params = new JRegistry;
$dispatcher	= JDispatcher::getInstance();
$dispatcher->trigger('onContentBeforeDisplay', array('com_podcastmedia.file', &$this->_tmp_audio, &$params));
?>
<tr>
	<td>
		<a href="<?php echo COM_PODCASTMEDIA_BASEURL . '/' . $this->_tmp_audio->path_relative; ?>" title="<?php echo $this->_tmp_audio->name; ?>">
			<?php echo JHtml::_('image', $this->_tmp_audio->icon_16, $this->_tmp_audio->name, null, true, true) ? JHtml::_('image', $this->_tmp_audio->icon_16, $this->_tmp_audio->title, null, true) : JHtml::_('image', 'media/con_info.png', $this->_tmp_audio->name, null, true); ?>
		</a>
	</td>
	<td class="description">
		<a href="<?php echo COM_PODCASTMEDIA_BASEURL . '/' . $this->_tmp_audio->path_relative; ?>" title="<?php echo $this->_tmp_audio->name; ?>">
			<?php echo $this->escape($this->_tmp_audio->title); ?>
		</a>
	</td>
	<td class="filesize">
		<?php echo JHtml::_('number.bytes', $this->_tmp_audio->size); ?>
	</td>
<?php if ($user->authorise('core.delete', 'com_podcastmanager'))
{ ?>
	<td>
		<a class="delete-item" target="_top" href="index.php?option=com_podcastmedia&amp;task=file.delete&amp;tmpl=index&amp;<?php echo JSession::getFormToken(); ?>=1&amp;folder=<?php echo $this->state->folder; ?>&amp;rm[]=<?php echo $this->_tmp_audio->name; ?>" rel="<?php echo $this->_tmp_audio->name; ?>">
			<i class="icon-remove" rel="tooltip" title="<?php echo JText::_('JACTION_DELETE');?>"></i>
		</a>
		<input type="checkbox" name="rm[]" value="<?php echo $this->_tmp_audio->name; ?>" />
	</td>
<?php } ?>
</tr>
<?php
$dispatcher->trigger('onContentAfterDisplay', array('com_podcastmedia.file', &$this->_tmp_audio, &$params));
