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
?>
<form target="_parent" action="index.php?option=com_podcastmedia&amp;tmpl=index&amp;folder=<?php echo $this->state->folder; ?>" method="post" id="mediamanager-form" name="mediamanager-form">
	<ul class="manager thumbnails">
		<?php echo $this->loadTemplate('up');

		// Load the folders
		for ($i = 0, $n = count($this->folders); $i < $n; $i++)
		{
			$this->setFolder($i);
			echo $this->loadTemplate('folder');
		}

		// Load the files
		for ($i = 0, $n = count($this->audio); $i < $n; $i++)
		{
			$this->setAudio($i);
			echo $this->loadTemplate('audio');
		} ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="username" value="" />
		<input type="hidden" name="password" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</ul>
</form>
