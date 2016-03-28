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

$user   = JFactory::getUser();
$params = JComponentHelper::getParams('com_podcastmedia');

?>
<form target="_parent" action="index.php?option=com_podcastmedia&amp;tmpl=index&amp;folder=<?php echo $this->state->folder; ?>" method="post" id="mediamanager-form" name="mediamanager-form">
	<div class="muted">
		<p>
			<span class="icon-folder"></span>
			<?php if ($this->state->folder != '') : ?>
				<?php echo JText::_('JGLOBAL_ROOT') . ': ' . $params->get('file_path', 'media/com_podcastmanager') . '/' . $this->state->folder; ?>
			<?php else : ?>
				<?php echo JText::_('JGLOBAL_ROOT') . ': ' . $params->get('file_path', 'media/com_podcastmanager'); ?>
			<?php endif; ?>
		</p>
	</div>

	<div class="manager">
		<table class="table table-striped table-condensed">
			<thead>
				<tr>
					<th width="1%"><?php echo JText::_('JGLOBAL_PREVIEW'); ?></th>
					<th><?php echo JText::_('COM_PODCASTMEDIA_NAME'); ?></th>
					<th width="8%"><?php echo JText::_('COM_PODCASTMEDIA_FILESIZE'); ?></th>
					<?php if ($user->authorise('core.delete', 'com_podcastmanager')) : ?>
						<th width="8%"><?php echo JText::_('JACTION_DELETE'); ?></th>
					<?php endif; ?>
				</tr>
			</thead>
			<tbody>
				<?php echo $this->loadTemplate('up'); ?>

				<?php for ($i = 0, $n = count($this->folders); $i < $n; $i++) :
					$this->setFolder($i);
					echo $this->loadTemplate('folder');
				endfor; ?>

				<?php for ($i = 0, $n = count($this->audio); $i < $n; $i++) :
					$this->setAudio($i);
					echo $this->loadTemplate('audio');
				endfor; ?>
			</tbody>
		</table>
		<input type="hidden" name="task" value="list" />
		<input type="hidden" name="username" value="" />
		<input type="hidden" name="password" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
