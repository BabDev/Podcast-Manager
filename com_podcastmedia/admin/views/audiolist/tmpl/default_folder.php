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

$input = JFactory::getApplication()->input;
?>
<tr>
	<td class="description">
		<a href="index.php?option=com_podcastmedia&amp;view=audiolist&amp;tmpl=component&amp;folder=<?php echo $this->_tmp_folder->path_relative; ?>&amp;asset=<?php echo $input->get('asset', '', 'cmd'); ?>&amp;author=<?php echo $input->get('author', '', 'cmd'); ?>">
			<i class="icon-folder-2"></i> <?php echo $this->_tmp_folder->name; ?>
		</a>
	</td>
	<td>&#160;</td>
</tr>
