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
		<div class="imgOutline">
			<div class="imgTotal">
				<div align="center" class="imgBorder">
					<a style="display: block; width: 100%; height: 100%" title="<?php echo $this->_tmp_doc->name; ?>" >
						<?php echo JHTML::_('image',$this->_tmp_doc->icon_32, $this->_tmp_doc->name, null, true, true) ? JHTML::_('image',$this->_tmp_doc->icon_32, $this->_tmp_doc->title, NULL, true) : JHTML::_('image','media/con_info.png', $this->_tmp_doc->name, NULL, true) ; ?></a>
				</div>
			</div>
			<div class="controls">
			<?php if ($user->authorise('core.delete','com_podcastmanager')):?>
				<a class="delete-item" target="_top" href="index.php?option=com_podcastmedia&amp;task=file.delete&amp;tmpl=index&amp;<?php echo JUtility::getToken(); ?>=1&amp;folder=<?php echo $this->state->folder; ?>&amp;rm[]=<?php echo $this->_tmp_doc->name; ?>" rel="<?php echo $this->_tmp_doc->name; ?>"><?php echo JHTML::_('image','media/remove.png', JText::_('JACTION_DELETE'), array('width' => 16, 'height' => 16), true); ?></a>
				<input type="checkbox" name="rm[]" value="<?php echo $this->_tmp_doc->name; ?>" />
			<?php endif;?>
			</div>
			<div class="imginfoBorder" title="<?php echo $this->_tmp_doc->name; ?>" >
				<?php echo $this->_tmp_doc->title; ?>
			</div>
		</div>
<?php
$dispatcher->trigger('onContentAfterDisplay', array('com_podcastmedia.file', &$this->_tmp_doc, &$params));
?>
