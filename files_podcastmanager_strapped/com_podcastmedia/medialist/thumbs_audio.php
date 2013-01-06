<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  files_podcastmanager_strapped
 *
 * @copyright   Copyright (C) 2011-2013 Michael Babker. All rights reserved.
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
<li class="imgOutline thumbnail height-80 width-80 center">
	<?php if ($user->authorise('core.delete', 'com_podcastmanager')):?>
		<a class="close delete-item" target="_top" href="index.php?option=com_podcastmedia&amp;task=file.delete&amp;tmpl=index&amp;<?php echo JSession::getFormToken(); ?>=1&amp;folder=<?php echo $this->state->folder; ?>&amp;rm[]=<?php echo $this->_tmp_audio->name; ?>" rel="<?php echo $this->_tmp_audio->name; ?>" title="<?php echo JText::_('JACTION_DELETE');?>">
			x
		</a>
		<input class="pull-left" type="checkbox" name="rm[]" value="<?php echo $this->_tmp_audio->name; ?>" />
		<div class="clearfix"></div>
	<?php endif;?>
	<div class="height-50">
		<a title="<?php echo $this->_tmp_audio->name; ?>" >
			<?php echo JHtml::_('image', $this->_tmp_audio->icon_32, $this->_tmp_audio->name, null, true, true) ? JHtml::_('image', $this->_tmp_audio->icon_32, $this->_tmp_audio->title, null, true) : JHtml::_('image', 'media/con_info.png', $this->_tmp_audio->name, null, true); ?>
		</a>
	</div>
	<div class="small">
		<?php echo JHtml::_('string.truncate', $this->_tmp_audio->name, 10, false); ?>
	</div>
</li>
<?php
$dispatcher->trigger('onContentAfterDisplay', array('com_podcastmedia.file', &$this->_tmp_audio, &$params));
