<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmedia
 *
 * @copyright   Copyright (C) 2011-2012 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

?>
<ul <?php echo $this->folders_id; ?>>
<?php foreach ($this->folders['children'] as $folder)
{ ?>
	<li id="<?php echo $folder['data']->relative; ?>">
		<a href="index.php?option=com_podcastmedia&amp;view=mediaList&amp;tmpl=component&amp;folder=<?php echo $folder['data']->relative; ?>" target="folderframe">
			<?php echo $folder['data']->name; ?>
		</a>
		<?php echo $this->getFolderLevel($folder); ?>
	</li>
<?php } ?>
</ul>
