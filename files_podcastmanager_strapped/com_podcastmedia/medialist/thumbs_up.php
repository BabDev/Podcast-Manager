<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmedia
 *
 * @copyright   Copyright (C) 2011-2013 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;
?>
<li class="imgOutline thumbnail height-80 width-80 center">
	<div class="imgTotal">
		<div align="center" class="imgBorder">
			<a class="btn" href="index.php?option=com_podcastmedia&amp;view=medialist&amp;tmpl=component&amp;folder=<?php echo $this->state->parent; ?>" target="folderframe">
				<i class="icon-arrow-up"></i>
			</a>
		</div>
	</div>
	<div class="controls">
		<span>&#160;</span>
	</div>
	<div class="imginfoBorder">
		<a href="index.php?option=com_podcastmedia&amp;view=medialist&amp;tmpl=component&amp;folder=<?php echo $this->state->parent; ?>" target="folderframe">..</a>
	</div>
</li>
