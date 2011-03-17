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
		<tr>
			<td>
				<a  title="<?php echo $this->_tmp_audio->name; ?>">
					<?php  echo JHTML::_('image',$this->_tmp_audio->icon_16, $this->_tmp_audio->title, null, true, true) ? JHTML::_('image',$this->_tmp_audio->icon_16, $this->_tmp_audio->title, array('width' => 16, 'height' => 16), true) : JHTML::_('image','media/con_info.png', $this->_tmp_audio->title, array('width' => 16, 'height' => 16), true);?> </a>
			</td>
			<td class="description"  title="<?php echo $this->_tmp_audio->name; ?>">
				<?php echo $this->_tmp_audio->title; ?>
			</td>
			<td>&#160;

			</td>
			<td class="filesize">
				<?php echo MediaHelper::parseSize($this->_tmp_audio->size); ?>
			</td>
		<?php if ($user->authorise('core.delete','com_podcastmanager')):?>
			<td>
				<a class="delete-item" target="_top" href="index.php?option=com_podcastmedia&amp;task=file.delete&amp;tmpl=index&amp;<?php echo JUtility::getToken(); ?>=1&amp;folder=<?php echo $this->state->folder; ?>&amp;rm[]=<?php echo $this->_tmp_audio->name; ?>" rel="<?php echo $this->_tmp_audio->name; ?>"><?php echo JHTML::_('image','media/remove.png', JText::_('Delete'), array('width' => 16, 'height' => 16, 'border' => 0), true);?></a>
				<input type="checkbox" name="rm[]" value="<?php echo $this->_tmp_audio->name; ?>" />
			</td>
		<?php endif;?>
		</tr>
<?php
$dispatcher->trigger('onContentAfterDisplay', array('com_podcastmedia.file', &$this->_tmp_audio, &$params));
?>
