<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 *
 * @copyright   Copyright (C) 2011-2015 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

// Load the tooltip behavior.
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

$js = <<< JS
Joomla.submitbutton = function(task) {
	if (task == 'feed.cancel' || document.formvalidator.isValid(document.getElementById('item-form'))) {
		Joomla.submitform(task, document.getElementById('item-form'));
	}
}
JS;

JFactory::getDocument()->addScriptDeclaration($js);
?>

<form action="<?php echo JRoute::_('index.php?option=com_podcastmanager&view=feed&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form">
	<div class="row-fluid">
		<!-- Begin Content -->
		<div class="span9 form-horizontal">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#general" data-toggle="tab"><?php echo JText::_('COM_PODCASTMANAGER_VIEW_FEED_FIELDSET_FEED');?></a></li>
				<li><a href="#publishing" data-toggle="tab"><?php echo JText::_('JGLOBAL_FIELDSET_PUBLISHING');?></a></li>
				<li><a href="#itunes" data-toggle="tab"><?php echo JText::_('COM_PODCASTMANAGER_FIELDSET_ITUNES_OPTIONS');?></a></li>
				<?php if ($this->canDo->get('core.admin')) : ?>
				<li><a href="#permissions" data-toggle="tab"><?php echo JText::_('COM_PODCASTMANAGER_FIELDSET_RULES');?></a></li>
				<?php endif; ?>
			</ul>

			<div class="tab-content">
				<!-- Begin Tabs -->
				<div class="tab-pane active" id="general">
					<fieldset class="adminform">
						<?php echo $this->form->renderField('name'); ?>
						<?php echo $this->form->renderField('subtitle'); ?>
						<?php echo $this->form->renderField('description'); ?>
						<?php echo $this->form->renderField('bp_position'); ?>
						<?php echo $this->form->renderField('boilerplate'); ?>
						<?php echo $this->form->renderField('copyright'); ?>
						<?php echo $this->form->renderField('author'); ?>
						<?php echo $this->form->renderField('image'); ?>
					</fieldset>
				</div>

				<div class="tab-pane" id="publishing">
					<?php echo $this->form->renderField('alias'); ?>
					<?php echo $this->form->renderField('created'); ?>
					<?php echo $this->form->renderField('created_by'); ?>
					<?php if ($this->item->modified_by) : ?>
					<?php echo $this->form->renderField('modified_by'); ?>
					<?php echo $this->form->renderField('modified'); ?>
					<?php endif; ?>
				</div>

				<div class="tab-pane" id="itunes">
					<?php echo $this->form->renderField('block'); ?>
					<?php echo $this->form->renderField('explicit'); ?>
					<?php echo $this->form->renderField('category1'); ?>
					<?php echo $this->form->renderField('category2'); ?>
					<?php echo $this->form->renderField('category3'); ?>
					<?php echo $this->form->renderField('ownername'); ?>
					<?php echo $this->form->renderField('owneremail'); ?>
					<?php echo $this->form->renderField('keywords'); ?>
					<?php echo $this->form->renderField('newFeed'); ?>
				</div>

			<?php if ($this->canDo->get('core.admin')) : ?>
				<div class="tab-pane" id="permissions">
					<fieldset>
						<?php echo $this->form->getInput('rules'); ?>
					</fieldset>
				</div>
			<?php endif; ?>
				<!-- End Tabs -->
			</div>
			<input type="hidden" name="task" value="" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
		<!-- End Content -->
		<!-- Begin Sidebar -->
		<div class="span3">
			<h4><?php echo JText::_('JDETAILS');?></h4>
			<hr />
			<fieldset class="form-vertical">
				<?php echo $this->form->renderField('published'); ?>
				<?php echo $this->form->renderField('language'); ?>
				<?php foreach ($this->form->getFieldset('jmetadata') as $field) : ?>
				<?php echo $this->form->renderField('language'); ?>
				<div class="control-group">
					<div class="control-label">
						<?php echo $field->label; ?>
					</div>
					<div class="controls">
						<?php echo $field->input; ?>
					</div>
				</div>
				<?php endforeach ?>
				<?php echo $this->form->renderField('version_note'); ?>
				<?php echo $this->form->renderField('id'); ?>
			</fieldset>
		</div>
	<!-- End Sidebar -->
	</div>
</form>
