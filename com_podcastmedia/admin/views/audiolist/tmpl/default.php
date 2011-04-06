<?php
/**
 * Podcast Manager for Joomla!
 *
 * @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
 * @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 */

// No direct access.
defined('_JEXEC') or die;
?>
<?php if (count($this->audio) > 0 || count($this->folders) > 0) { ?>
<div class="manager"><?php for ($i=0,$n=count($this->folders); $i<$n; $i++) :
$this->setFolder($i);
echo $this->loadTemplate('folder');
endfor; ?> <?php for ($i=0,$n=count($this->audio); $i<$n; $i++) :
$this->setAudio($i);
echo $this->loadTemplate('audio');
endfor; ?></div>
<?php } else { ?>
<div id="media-noimages">
<p><?php echo JText::_('COM_PODCASTMEDIA_NO_AUDIO_FOUND'); ?></p>
</div>
<?php } ?>