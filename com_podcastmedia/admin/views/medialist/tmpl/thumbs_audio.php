<?php
/**
 * Podcast Manager for Joomla!
 *
 * @version		$Id: view.html.php 92 2011-03-12 22:49:43Z mbabker $
 * @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
 * @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 */

// No direct access.
defined('_JEXEC') or die;
$user = JFactory::getUser();
$params = new JRegistry;
$dispatcher	= JDispatcher::getInstance();
$dispatcher->trigger('onContentBeforeDisplay', array('com_podcastmedia.file', &$this->_tmp_audio, &$params));
?>
<div class="imgOutline">
<div class="imgTotal">
<div align="center" class="imgBorder"><a
	style="display: block; width: 100%; height: 100%"
	title="<?php echo $this->_tmp_audio->name; ?>"> <?php echo JHtml::_('image',$this->_tmp_audio->icon_32, $this->_tmp_audio->name, null, true, true) ? JHtml::_('image',$this->_tmp_audio->icon_32, $this->_tmp_audio->title, NULL, true) : JHtml::_('image','media/con_info.png', $this->_tmp_audio->name, NULL, true) ; ?></a>
</div>
</div>
<div class="controls"><?php if ($user->authorise('core.delete','com_podcastmanager')):?>
<a class="delete-item" target="_top"
	href="index.php?option=com_podcastmedia&amp;task=file.delete&amp;tmpl=index&amp;<?php echo JUtility::getToken(); ?>=1&amp;folder=<?php echo $this->state->folder; ?>&amp;rm[]=<?php echo $this->_tmp_audio->name; ?>"
	rel="<?php echo $this->_tmp_audio->name; ?>"><?php echo JHtml::_('image','media/remove.png', JText::_('JACTION_DELETE'), array('width' => 16, 'height' => 16), true); ?></a>
<input type="checkbox" name="rm[]"
	value="<?php echo $this->_tmp_audio->name; ?>" /> <?php endif;?></div>
<div class="imginfoBorder"
	title="<?php echo $this->_tmp_audio->name; ?>"><?php echo $this->_tmp_audio->title; ?>
</div>
</div>
<?php
$dispatcher->trigger('onContentAfterDisplay', array('com_podcastmedia.file', &$this->_tmp_audio, &$params));
?>
