<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  files_podcastmanager_strapped
 *
 * @copyright   Copyright (C) 2011-2014 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'feed.cancel' || document.formvalidator.isValid(document.id('item-form'))) {
			Joomla.submitform(task, document.getElementById('item-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_podcastmanager&view=feed&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form">
	<div class="row-fluid">
		<!-- Begin Content -->
		<div class="span10 form-horizontal">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#general" data-toggle="tab"><?php echo JText::_('COM_PODCASTMANAGER_VIEW_FEED_FIELDSET_FEED');?></a></li>
				<li><a href="#publishing" data-toggle="tab"><?php echo JText::_('JGLOBAL_FIELDSET_PUBLISHING');?></a></li>
				<li><a href="#itunes" data-toggle="tab"><?php echo JText::_('COM_PODCASTMANAGER_FIELDSET_ITUNES_OPTIONS');?></a></li>
				<?php if ($this->canDo->get('core.admin'))
				{ ?>
				<li><a href="#permissions" data-toggle="tab"><?php echo JText::_('COM_PODCASTMANAGER_FIELDSET_RULES');?></a></li>
				<?php } ?>
			</ul>

			<div class="tab-content">
				<!-- Begin Tabs -->
				<div class="tab-pane active" id="general">
					<fieldset class="adminform">
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('name'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('name'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('subtitle'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('subtitle'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('description'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('description'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('bp_position'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('bp_position'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('boilerplate'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('boilerplate'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('copyright'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('copyright'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('author'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('author'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('image'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('image'); ?>
							</div>
						</div>
					</fieldset>
				</div>

				<div class="tab-pane" id="publishing">
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('alias'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('alias'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('created'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('created'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('created_by'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('created_by'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('publish_up'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('publish_up'); ?>
						</div>
					</div>
					<?php if ($this->item->modified_by)
					{ ?>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('modified_by'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('modified_by'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('modified'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('modified'); ?>
						</div>
					</div>
					<?php } ?>
				</div>

				<div class="tab-pane" id="itunes">
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('block'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('block'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('explicit'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('explicit'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('category1'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('category1'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('category2'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('category2'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('category3'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('category3'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('ownername'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('ownername'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('owneremail'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('owneremail'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('keywords'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('keywords'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('newFeed'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('newFeed'); ?>
						</div>
					</div>
				</div>

			<?php if ($this->canDo->get('core.admin'))
			{ ?>
				<div class="tab-pane" id="permissions">
					<fieldset>
						<?php echo $this->form->getInput('rules'); ?>
					</fieldset>
				</div>
			<?php } ?>
				<!-- End Tabs -->
			</div>
			<input type="hidden" name="task" value="" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
		<!-- End Content -->
		<!-- Begin Sidebar -->
		<div class="span2">
			<h4><?php echo JText::_('JDETAILS');?></h4>
			<hr />
			<fieldset class="form-vertical">
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('published'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('published'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('language'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('language'); ?>
					</div>
				</div>
				<?php if (version_compare(JVERSION, '3.1', 'ge')) : ?>
					<?php foreach ($this->form->getFieldset('jmetadata') as $field) : ?>
					<div class="control-group">
						<div class="control-label">
							<?php echo $field->label; ?>
						</div>
						<div class="controls">
							<?php echo $field->input; ?>
						</div>
					</div>
					<?php endforeach ?>
				<?php endif; ?>
				<?php if (version_compare(JVERSION, '3.2', 'ge')) : ?>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('version_note'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('version_note'); ?>
						</div>
					</div>
				<?php endif; ?>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('id'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('id'); ?>
					</div>
				</div>
			</fieldset>
		</div>
	<!-- End Sidebar -->
	</div>
</form>
