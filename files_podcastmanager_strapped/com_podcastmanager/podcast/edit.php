<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  files_podcastmanager_strapped
 *
 * @copyright   Copyright (C) 2011-2015 Michael Babker. All rights reserved.
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
		if (task == 'podcast.cancel' || document.formvalidator.isValid(document.id('item-form'))) {
			Joomla.submitform(task, document.getElementById('item-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_podcastmanager&view=podcast&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form">
	<div class="row-fluid">
		<!-- Begin Content -->
		<div class="span9 form-horizontal">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#general" data-toggle="tab"><?php echo JText::_('COM_PODCASTMANAGER_VIEW_PODCAST_FIELDSET_METADATA');?></a></li>
				<li><a href="#publishing" data-toggle="tab"><?php echo JText::_('JGLOBAL_FIELDSET_PUBLISHING');?></a></li>
			</ul>

			<div class="tab-content">
				<!-- Begin Tabs -->
				<div class="tab-pane active" id="general">
					<fieldset class="adminform">
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('filename'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('filename'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('title'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('title'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('feedname'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('feedname'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('mime'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('mime'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('itSummary'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('itSummary'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('itImage'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('itImage'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('itAuthor'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('itAuthor'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('itBlock'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('itBlock'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('itDuration'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('itDuration'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('itExplicit'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('itExplicit'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('itKeywords'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('itKeywords'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('itSubtitle'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('itSubtitle'); ?>
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
