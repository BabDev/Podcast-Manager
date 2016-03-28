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

JHtml::_('stylesheet', 'media/popup-imagelist.css', [], true);

if (JFactory::getLanguage()->isRtl())
{
	JHtml::_('stylesheet', 'media/popup-imagelist_rtl.css', [], true);
}

JHtml::_('stylesheet', 'media/popup-medialist-details.css', [], true);
JFactory::getDocument()->addScriptDeclaration("var AudioManager = window.parent.AudioManager;");

?>
<div class="manager">
	<table class="table table-striped table-condensed">
		<thead>
			<tr>
				<th><?php echo JText::_('COM_PODCASTMEDIA_NAME'); ?></th>
				<th width="8%"><?php echo JText::_('COM_PODCASTMEDIA_FILESIZE'); ?></th>
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
</div>
