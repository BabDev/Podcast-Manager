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

$input = JFactory::getApplication()->input;
$user = JFactory::getUser();
$params = JComponentHelper::getParams('com_podcastmedia');
?>
<script type='text/javascript'>
var audio_base_path = '<?php echo $params->get('file_path', 'media/com_podcastmanager');?>/';
</script>
<form action="index.php?option=com_podcastmedia&amp;asset=<?php echo $input->get('asset', '', 'cmd');?>&amp;author=<?php echo $input->get('author', '', 'cmd');?>" id="imageForm" method="post" enctype="multipart/form-data">
	<div id="messages" style="display: none;">
		<span id="message"></span><?php echo JHtml::_('image', 'media/dots.gif', '...', array('width' => 22, 'height' => 12), true)?>
	</div>
	<fieldset>
		<div class="fltlft">
			<label for="folder"><?php echo JText::_('COM_PODCASTMEDIA_DIRECTORY') ?></label>
			<?php echo $this->folderList; ?>
			<button type="button" id="upbutton" title="<?php echo JText::_('COM_PODCASTMEDIA_DIRECTORY_UP') ?>"><?php echo JText::_('COM_PODCASTMEDIA_UP') ?></button>
		</div>
		<div class="fltrt">
			<button type="button" onclick="window.parent.jInsertFieldValue(document.id('f_url').value,'<?php echo $this->state->get('field.id');?>');window.parent.SqueezeBox.close();"><?php echo JText::_('COM_PODCASTMEDIA_INSERT') ?></button>
			<button type="button" onclick="window.parent.SqueezeBox.close();"><?php echo JText::_('JCANCEL') ?></button>
		</div>
	</fieldset>

	<iframe id="audioframe" name="audioframe" src="index.php?option=com_podcastmedia&amp;view=audioList&amp;tmpl=component&amp;folder=<?php echo $this->state->folder?>&amp;asset=<?php echo $input->get('asset', '', 'cmd');?>&amp;author=<?php echo $input->get('author', '', 'cmd');?>"></iframe>

	<fieldset>
		<table class="properties">
			<tr>
				<td><label for="f_url"><?php echo JText::_('COM_PODCASTMEDIA_FILE_URL') ?></label></td>
				<td><input type="text" id="f_url" value="" /></td>
			</tr>
		</table>

		<input type="hidden" id="dirPath" name="dirPath" />
		<input type="hidden" id="f_file" name="f_file" />
		<input type="hidden" id="tmpl" name="component" />

	</fieldset>
</form>

<?php if ($user->authorise('core.create', 'com_podcastmanager'))
{ ?>
<form action="<?php echo JURI::base(); ?>index.php?option=com_podcastmedia&amp;task=file.upload&amp;tmpl=component&amp;<?php echo $this->session->getName() . '=' . $this->session->getId(); ?>&amp;<?php echo $this->session->getFormToken();?>=1&amp;asset=<?php echo $input->get('asset', '', 'cmd');?>&amp;author=<?php echo $input->get('author', '', 'cmd');?>&amp;format=<?php echo $this->medmanparams->get('enable_flash') == '1' ? 'json' : '' ?>" id="uploadForm" name="uploadForm" method="post" enctype="multipart/form-data">
	<fieldset id="uploadform">
		<legend><?php echo $this->medmanparams->get('upload_maxsize') == '0' ? JText::_('COM_PODCASTMEDIA_UPLOAD_FILES_NOLIMIT') : JText::sprintf('COM_PODCASTMEDIA_UPLOAD_FILES', $this->medmanparams->get('upload_maxsize')); ?></legend>
		<fieldset id="upload-noflash" class="actions">
			<label for="upload-file" class="hidelabeltxt"><?php echo JText::_('COM_PODCASTMEDIA_UPLOAD_FILE'); ?></label>
			<input type="file" id="upload-file" name="Filedata[]" multiple />
			<label for="upload-submit" class="hidelabeltxt"><?php echo JText::_('COM_PODCASTMEDIA_START_UPLOAD'); ?></label>
			<input type="submit" id="upload-submit" value="<?php echo JText::_('COM_PODCASTMEDIA_START_UPLOAD'); ?>"/>
		</fieldset>
		<div id="upload-flash" class="hide">
			<ul>
				<li><a href="#" id="upload-browse"><?php echo JText::_('COM_PODCASTMEDIA_BROWSE_FILES'); ?></a></li>
				<li><a href="#" id="upload-clear"><?php echo JText::_('COM_PODCASTMEDIA_CLEAR_LIST'); ?></a></li>
				<li><a href="#" id="upload-start"><?php echo JText::_('COM_PODCASTMEDIA_START_UPLOAD'); ?></a></li>
			</ul>
			<div class="clr"></div>
			<p class="overall-title"></p>
			<?php echo JHtml::_('image', 'media/bar.gif', JText::_('COM_PODCASTMEDIA_OVERALL_PROGRESS'), array('class' => 'progress overall-progress'), true); ?>
			<div class="clr"></div>
			<p class="current-title"></p>
			<?php echo JHtml::_('image', 'media/bar.gif', JText::_('COM_PODCASTMEDIA_CURRENT_PROGRESS'), array('class' => 'progress current-progress'), true); ?>
			<p class="current-text"></p>
		</div>
		<ul class="upload-queue" id="upload-queue">
			<li style="display: none"></li>
		</ul>
		<input type="hidden" name="return-url" value="<?php echo base64_encode('index.php?option=com_podcastmedia&view=audio&tmpl=component&fieldid=' . $input->get('fieldid', '', 'cmd') . '&e_name=' . $input->get('e_name', '', 'cmd') . '&asset=' . $input->get('asset', '', 'cmd') . '&author=' . $input->get('author', '', 'cmd')); ?>" />
	</fieldset>
</form>
<?php }
