<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  files_podcastmanager_hathor
 *
 * @copyright   Copyright (C) 2011-2012 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>
<div class="podcast-edit">
	<form action="<?php echo JRoute::_('index.php?option=com_podcastmanager&view=feed&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
		<div class="col main-section">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_PODCASTMANAGER_VIEW_FEED_FIELDSET_FEED'); ?></legend>
				<ul class="adminformlist">
					<li><?php echo $this->form->getLabel('name'); ?>
					<?php echo $this->form->getInput('name'); ?></li>

					<li><?php echo $this->form->getLabel('subtitle'); ?>
					<?php echo $this->form->getInput('subtitle'); ?></li>

					<li><?php echo $this->form->getLabel('description'); ?>
					<?php echo $this->form->getInput('description'); ?></li>

					<li><?php echo $this->form->getLabel('bp_position'); ?>
						<?php echo $this->form->getInput('bp_position'); ?></li>

					<li><?php echo $this->form->getLabel('boilerplate'); ?>
						<?php echo $this->form->getInput('boilerplate'); ?></li>

					<li><?php echo $this->form->getLabel('published'); ?>
					<?php echo $this->form->getInput('published'); ?></li>

					<li><?php echo $this->form->getLabel('copyright'); ?>
					<?php echo $this->form->getInput('copyright'); ?></li>

					<li><?php echo $this->form->getLabel('author'); ?>
					<?php echo $this->form->getInput('author'); ?></li>

					<li><?php echo $this->form->getLabel('image'); ?>
					<?php echo $this->form->getInput('image'); ?></li>

					<?php if ($this->canDo->get('core.admin'))
					{ ?>
					<li>
						<span class="faux-label"><?php echo JText::_('JGLOBAL_ACTION_PERMISSIONS_LABEL'); ?></span>
						<button type="button" onclick="document.location.href='#access-rules';">
							<?php echo JText::_('JGLOBAL_PERMISSIONS_ANCHOR'); ?>
						</button>
					</li>
					<?php } ?>

					<li><?php echo $this->form->getLabel('language'); ?>
					<?php echo $this->form->getInput('language'); ?></li>

					<li><?php echo $this->form->getLabel('id'); ?>
					<?php echo $this->form->getInput('id'); ?></li>
				</ul>
			</fieldset>
		</div>

		<div class="col options-section">
		<?php echo JHtml::_('sliders.start', 'podcastmanager-feed-sliders-' . $this->item->id);
			echo JHtml::_('sliders.panel', JText::_('JGLOBAL_FIELDSET_PUBLISHING'), 'publishing-details'); ?>
			<fieldset class="panelform">
				<legend class="element-invisible"><?php echo JText::_('JGLOBAL_FIELDSET_PUBLISHING'); ?></legend>
				<ul class="adminformlist">
					<li><?php echo $this->form->getLabel('created');
						echo $this->form->getInput('created'); ?>
					</li>

					<li><?php echo $this->form->getLabel('created_by');
						echo $this->form->getInput('created_by'); ?>
					</li>

					<li><?php echo $this->form->getLabel('publish_up');
						echo $this->form->getInput('publish_up'); ?>
					</li>

					<?php if ($this->item->modified_by)
					{ ?>
					<li><?php echo $this->form->getLabel('modified_by');
						echo $this->form->getInput('modified_by'); ?>
					</li>

					<li><?php echo $this->form->getLabel('modified');
						echo $this->form->getInput('modified'); ?>
					</li>
					<?php } ?>
				</ul>

			</fieldset>

		<?php echo JHtml::_('sliders.panel', JText::_('COM_PODCASTMANAGER_FIELDSET_ITUNES_OPTIONS'), 'itunes-options'); ?>
			<fieldset class="panelform">
				<legend class="element-invisible"><?php echo JText::_('COM_PODCASTMANAGER_FIELDSET_ITUNES_OPTIONS'); ?></legend>
				<ul class="adminformlist">
					<li><?php echo $this->form->getLabel('block');
						echo $this->form->getInput('block'); ?>
					</li>

					<li><?php echo $this->form->getLabel('explicit');
						echo $this->form->getInput('explicit'); ?>
					</li>

					<li><?php echo $this->form->getLabel('category1');
						echo $this->form->getInput('category1'); ?>
					</li>

					<li><?php echo $this->form->getLabel('category2');
						echo $this->form->getInput('category2'); ?>
					</li>

					<li><?php echo $this->form->getLabel('category3');
						echo $this->form->getInput('category3'); ?>
					</li>

					<li><?php echo $this->form->getLabel('ownername');
						echo $this->form->getInput('ownername'); ?>
					</li>

					<li><?php echo $this->form->getLabel('owneremail');
						echo $this->form->getInput('owneremail'); ?>
					</li>

					<li><?php echo $this->form->getLabel('keywords');
						echo $this->form->getInput('keywords'); ?>
					</li>

					<li><?php echo $this->form->getLabel('newFeed');
						echo $this->form->getInput('newFeed'); ?>
					</li>
				</ul>
			</fieldset>
		<?php echo JHtml::_('sliders.end'); ?>
		</div>
		<div class="clr"></div>

		<?php if ($this->canDo->get('core.admin'))
		{ ?>
		<div class="col rules-section">
			<?php echo JHtml::_('sliders.start', 'permissions-sliders-' . $this->item->id, array('useCookie' => 1));
			echo JHtml::_('sliders.panel', JText::_('COM_PODCASTMANAGER_FIELDSET_RULES'), 'access-rules'); ?>
			<fieldset class="panelform">
				<legend class="element-invisible"><?php echo JText::_('COM_PODCASTMANAGER_FIELDSET_RULES'); ?></legend>
				<?php echo $this->form->getLabel('rules');
				echo $this->form->getInput('rules'); ?>
			</fieldset>

			<?php echo JHtml::_('sliders.end'); ?>
		</div>
		<?php } ?>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>

