<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmedia
 *
 * @copyright   Copyright (C) 2011-2014 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

$params = new JRegistry;
$dispatcher	= JDispatcher::getInstance();
$dispatcher->trigger('onContentBeforeDisplay', array('com_podcastmedia.file', &$this->_tmp_audio, &$params));
?>
<tr>
	<td>
		<a class="img-preview" href="javascript:AudioManager.populateFields('<?php echo $this->_tmp_audio->path_relative; ?>')" title="<?php echo $this->_tmp_audio->name; ?>">
			<?php echo JHtml::_('image', $this->_tmp_audio->icon_16, $this->_tmp_audio->name, null, true, true) ? JHtml::_('image', $this->_tmp_audio->icon_16, $this->_tmp_audio->title, null, true) : JHtml::_('image', 'media/con_info.png', $this->_tmp_audio->name, null, true); ?>
		</a>
	</td>
	<td class="description">
		<a href="javascript:AudioManager.populateFields('<?php echo $this->_tmp_audio->path_relative; ?>')" title="<?php echo $this->_tmp_audio->name; ?>">
			<?php echo $this->escape($this->_tmp_audio->title); ?>
		</a>
	</td>
	<td class="filesize">
		<?php echo JHtml::_('number.bytes', $this->_tmp_audio->size); ?>
	</td>
</tr>
<?php
$dispatcher->trigger('onContentAfterDisplay', array('com_podcastmedia.file', &$this->_tmp_audio, &$params));
