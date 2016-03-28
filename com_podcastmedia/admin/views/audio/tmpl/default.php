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

$input  = JFactory::getApplication()->input;
$user   = JFactory::getUser();
$params = JComponentHelper::getParams('com_podcastmedia');

JHtml::_('formbehavior.chosen', 'select');

// Load tooltip instance without HTML support because we have a HTML tag in the tip
JHtml::_('bootstrap.tooltip', '.noHtmlTip', array('html' => false));

// Include jQuery
JHtml::_('jquery.framework');
JHtml::_('script', 'podcastmanager/popup-audiomanager.js', false, true);
JHtml::_('stylesheet', 'media/popup-imagemanager.css', [], true);

if (JFactory::getLanguage()->isRtl())
{
	JHtml::_('stylesheet', 'media/popup-imagemanager_rtl.css', [], true);
}

JFactory::getDocument()->addScriptDeclaration("var audio_base_path = '" . $params->get('file_path', 'media/com_podcastmanager') . "/';");
?>
<div class="tabbable">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#browse_tab" data-toggle="tab"><?php echo JText::_('COM_PODCASTMEDIA_BROWSE_FILES') ?></a></li>
		<li><a href="#upload_tab" data-toggle="tab"><?php echo JText::_('COM_PODCASTMEDIA_UPLOAD') ?></a></li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane active" id="browse_tab">
			<form action="index.php?option=com_podcastmedia&amp;asset=<?php echo $input->getCmd('asset');?>&amp;author=<?php echo $input->getCmd('author'); ?>" class="form-vertical" id="imageForm" method="post" enctype="multipart/form-data">
				<div id="messages" style="display: none;">
					<span id="message"></span><?php echo JHtml::_('image', 'media/dots.gif', '...', array('width' => 22, 'height' => 12), true) ?>
				</div>
				<div class="well">
					<div class="row">
						<div class="span12 control-group">
							<div class="control-label">
								<label class="control-label" for="folder"><?php echo JText::_('COM_PODCASTMEDIA_DIRECTORY') ?></label>
							</div>
							<div class="controls">
								<?php echo $this->folderList; ?>
								<button class="btn" type="button" id="upbutton" title="<?php echo JText::_('COM_PODCASTMEDIA_DIRECTORY_UP') ?>"><?php echo JText::_('COM_PODCASTMEDIA_UP') ?></button>
							</div>
						</div>
						<div class="pull-right">
							<button class="btn btn-success button-save-selected" type="button" <?php if (!$this->state->get('field.id', true)) : ?>onclick="AudioManager.onok();window.parent.jModalClose();window.parent.jQuery('.modal.in').modal('hide');"<?php endif;?> data-dismiss="modal"><?php echo JText::_('COM_PODCASTMEDIA_INSERT') ?></button>
							<button class="btn button-cancel" type="button" onclick="window.parent.jQuery('.modal.in').modal('hide');<?php if (!$this->state->get('field.id', true)) : ?>window.parent.jModalClose();<?php endif ?>" data-dismiss="modal"><?php echo JText::_('JCANCEL') ?></button>
						</div>
					</div>
				</div>

				<iframe id="audioframe" name="audioframe" src="index.php?option=com_podcastmedia&amp;view=audiolist&amp;tmpl=component&amp;folder=<?php echo $this->state->folder?>&amp;asset=<?php echo $input->getCmd('asset');?>&amp;author=<?php echo $input->getCmd('author');?>"></iframe>

				<div class="well">
					<div class="row">
						<div class="span8 control-group">
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
		</div>
		<div class="tab-pane" id="upload_tab">
			<?php if ($user->authorise('core.create', 'com_podcastmedia')): ?>
				<form action="<?php echo JUri::base(); ?>index.php?option=com_podcastmedia&amp;task=file.upload&amp;tmpl=component&amp;<?php echo $this->session->getName() . '=' . $this->session->getId(); ?>&amp;<?php echo JSession::getFormToken();?>=1&amp;asset=<?php echo $input->getCmd('asset');?>&amp;author=<?php echo $input->getCmd('author');?>&amp;view=audio" id="uploadForm" class="form-horizontal" name="uploadForm" method="post" enctype="multipart/form-data">
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
						<?php JFactory::getSession()->set('com_podcastmedia.return_url', 'index.php?option=com_podcastmedia&view=audio&tmpl=component&fieldid=' . $input->getCmd('fieldid', '') . '&e_name=' . $input->getCmd('e_name') . '&asset=' . $input->getCmd('asset') . '&author=' . $input->getCmd('author')); ?>
					</div>
				</form>
			<?php endif; ?>
		</div>
	</div>
</div>
