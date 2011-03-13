<?php
/**
* Podcast Manager for Joomla!
*
* @version		$Id$
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
*/

// No direct access.
defined('_JEXEC') or die;
$params = new JRegistry;
$dispatcher	= JDispatcher::getInstance();
$dispatcher->trigger('onContentBeforeDisplay', array('com_podcastmedia.file', &$this->_tmp_audio, &$params));
?>
		<div class="item">
			<a href="javascript:ImageManager.populateFields('<?php echo $this->_tmp_audio->path_relative; ?>')" title="<?php echo $this->_tmp_audio->name; ?>" >
				<?php echo JHTML::_('image',$this->baseURL.'/'.$this->_tmp_audio->path_relative, JText::sprintf('COM_PODCASTMEDIA_IMAGE_TITLE', $this->_tmp_audio->title, PodcastMediaHelper::parseSize($this->_tmp_audio->size)), array('width' => $this->_tmp_img->width_32, 'height' => $this->_tmp_img->height_32)); ?>
				<span title="<?php echo $this->_tmp_audio->name; ?>"><?php echo $this->_tmp_audio->title; ?></span></a>
		</div>
<?php
$dispatcher->trigger('onContentAfterDisplay', array('com_podcastmedia.file', &$this->_tmp_audio, &$params));
?>
