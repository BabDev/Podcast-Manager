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
$user = JFactory::getUser();
$params = new JRegistry;
$dispatcher	= JDispatcher::getInstance();
$dispatcher->trigger('onContentBeforeDisplay', array('com_podcastmedia.file', &$this->_tmp_doc, &$params));
?>
		<tr>
			<td>
				<a  title="<?php echo $this->_tmp_doc->name; ?>">
					<?php  echo JHTML::_('image',$this->_tmp_doc->icon_16, $this->_tmp_doc->title, null, true, true) ? JHTML::_('image',$this->_tmp_doc->icon_16, $this->_tmp_doc->title, array('width' => 16, 'height' => 16), true) : JHTML::_('image','media/con_info.png', $this->_tmp_doc->title, array('width' => 16, 'height' => 16), true);?> </a>
			</td>
			<td class="description"  title="<?php echo $this->_tmp_doc->name; ?>">
				<?php echo $this->_tmp_doc->title; ?>
			</td>
			<td>&#160;

			</td>
			<td class="filesize">
				<?php echo MediaHelper::parseSize($this->_tmp_doc->size); ?>
			</td>
		<?php if ($user->authorise('core.delete','com_podcastmanager')):?>
			<td>
				<a class="delete-item" target="_top" href="index.php?option=com_podcastmedia&amp;task=file.delete&amp;tmpl=index&amp;<?php echo JUtility::getToken(); ?>=1&amp;folder=<?php echo $this->state->folder; ?>&amp;rm[]=<?php echo $this->_tmp_doc->name; ?>" rel="<?php echo $this->_tmp_doc->name; ?>"><?php echo JHTML::_('image','media/remove.png', JText::_('Delete'), array('width' => 16, 'height' => 16, 'border' => 0), true);?></a>
				<input type="checkbox" name="rm[]" value="<?php echo $this->_tmp_doc->name; ?>" />
			</td>
		<?php endif;?>
		</tr>
<?php
$dispatcher->trigger('onContentAfterDisplay', array('com_podcastmedia.file', &$this->_tmp_doc, &$params));
?>
