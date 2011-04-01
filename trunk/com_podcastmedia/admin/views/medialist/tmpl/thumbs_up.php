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
		<div class="imgOutline">
			<div class="imgTotal">
				<div align="center" class="imgBorder">
					<a href="index.php?option=com_podcastmedia&amp;view=mediaList&amp;tmpl=component&amp;folder=<?php echo $this->state->parent; ?>" target="folderframe">
						<?php echo JHtml::_('image','media/folderup_32.png', '..', array('width' => 32, 'height' => 32), true); ?></a>
				</div>
			</div>
			<div class="controls">
				<span>&#160;</span>
			</div>
			<div class="imginfoBorder">
				<a href="index.php?option=com_podcastmedia&amp;view=mediaList&amp;tmpl=component&amp;folder=<?php echo $this->state->parent; ?>" target="folderframe">..</a>
			</div>
		</div>
