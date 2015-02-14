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
$user = JFactory::getUser();
?>
<tr>
	<td class="imgTotal">
		<a href="index.php?option=com_podcastmedia&amp;view=medialist&amp;tmpl=component&amp;folder=<?php echo $this->state->parent; ?>" target="folderframe">
			<i class="icon-arrow-up"></i>
		</a>
	</td>
	<td class="description">
		<a href="index.php?option=com_podcastmedia&amp;view=medialist&amp;tmpl=component&amp;folder=<?php echo $this->state->parent; ?>" target="folderframe">
			..
		</a>
	</td>
	<td>&#160;</td>
<?php if ($user->authorise('core.delete', 'com_podcastmanager'))
{ ?>
	<td>&#160;</td>
<?php } ?>
</tr>
