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

JHtml::_('formbehavior.chosen', 'select');

$input  = JFactory::getApplication()->input;
$user   = JFactory::getUser();
$params = JComponentHelper::getParams('com_podcastmedia');
?>
<script type='text/javascript'>
	var audio_base_path = '<?php echo $params->get('file_path', 'media/com_podcastmanager');?>/';
</script>
<form action="index.php?option=com_podcastmedia&amp;asset=<?php echo $input->getCmd('asset');?>&amp;author=<?php echo $input->getCmd('author'); ?>" class="form-vertical" id="imageForm" method="post" enctype="multipart/form-data">
	<div id="messages" style="display: none;">
		<span id="message"></span><?php echo JHtml::_('image', 'media/dots.gif', '...', array('width' => 22, 'height' => 12), true) ?>
	</div>
	<div class="well">
		<div class="row">
			<div class="span9 control-group">
				<div class="control-label">
					<label class="control-label" for="folder"><?php echo JText::_('COM_PODCASTMEDIA_DIRECTORY') ?></label>
				</div>
				<div class="controls">
					<?php echo $this->folderList; ?>
					<button class="btn" type="button" id="upbutton" title="<?php echo JText::_('COM_PODCASTMEDIA_DIRECTORY_UP') ?>"><?php echo JText::_('COM_PODCASTMEDIA_UP') ?></button>
				</div>
			</div>
			<div class="pull-right">
				<button class="btn btn-primary" type="button" onclick="window.parent.jInsertFieldValue(document.id('f_url').value,'<?php echo $this->state->get('field.id');?>');window.parent.SqueezeBox.close();"><?php echo JText::_('COM_PODCASTMEDIA_INSERT') ?></button>
				<button class="btn" type="button" onclick="window.parent.SqueezeBox.close();"><?php echo JText::_('JCANCEL') ?></button>
			</div>
		</div>
	</div>

	<iframe id="audioframe" name="audioframe" src="index.php?option=com_podcastmedia&amp;view=audioList&amp;tmpl=component&amp;folder=<?php echo $this->state->folder?>&amp;asset=<?php echo $input->getCmd('asset');?>&amp;author=<?php echo $input->getCmd('author');?>"></iframe>

	<div class="well">
		<div class="row">
			<div class="span6 control-group">
				<div class="control-label">
					<label for="f_url"><?php echo JText::_('COM_PODCASTMEDIA_FILE_URL') ?></label>
				</div>
				<div class="controls">
					<input type="text" id="f_url" value="" />
				</div>
			</div>
		</div>

		<input type="hidden" id="dirPath" name="dirPath" />
		<input type="hidden" id="f_file" name="f_file" />
		<input type="hidden" id="tmpl" name="component" />

	</div>
</form>

<?php if ($user->authorise('core.create', 'com_podcastmedia')): ?>
	<form action="<?php echo JUri::base(); ?>index.php?option=com_podcastmedia&amp;task=file.upload&amp;tmpl=component&amp;<?php echo $this->session->getName() . '=' . $this->session->getId(); ?>&amp;<?php echo JSession::getFormToken();?>=1&amp;asset=<?php echo $input->getCmd('asset');?>&amp;author=<?php echo $input->getCmd('author');?>" id="uploadForm" class="form-horizontal" name="uploadForm" method="post" enctype="multipart/form-data">
		<div id="uploadform" class="well">
			<fieldset id="upload-noflash" class="actions">
				<div class="control-group">
					<div class="control-label">
						<label for="upload-file" class="control-label"><?php echo JText::_('COM_PODCASTMEDIA_UPLOAD_FILE'); ?></label>
					</div>
					<div class="controls">
						<input type="file" id="upload-file" name="Filedata[]" multiple /><button class="btn btn-primary" id="upload-submit"><i class="icon-upload icon-white"></i> <?php echo JText::_('COM_PODCASTMEDIA_START_UPLOAD'); ?></button>
						<p class="help-block"><?php echo $this->medmanparams->get('upload_maxsize') == '0' ? JText::_('COM_PODCASTMEDIA_UPLOAD_FILES_NOLIMIT') : JText::sprintf('COM_PODCASTMEDIA_UPLOAD_FILES', $this->medmanparams->get('upload_maxsize')); ?></p>
					</div>
				</div>
			</fieldset>
			<input type="hidden" name="return-url" value="<?php echo base64_encode('index.php?option=com_podcastmedia&view=audio&tmpl=component&fieldid=' . $input->getCmd('fieldid', '') . '&e_name=' . $input->getCmd('e_name') . '&asset=' . $input->getCmd('asset') . '&author=' . $input->getCmd('author')); ?>" />
		</div>
	</form>
<?php endif; ?>
