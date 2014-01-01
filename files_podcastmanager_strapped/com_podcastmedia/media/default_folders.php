<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  files_podcastmanager_strapped
 *
 * @copyright   Copyright (C) 2011-2014 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

// Set up the sanitised target for the ul
$ulTarget = str_replace('/', '-', $this->folders['data']->relative);
?>
<ul class="nav nav-list collapse in" id="collapseFolder-<?php echo $ulTarget; ?>">
<?php if (isset($this->folders['children'])) :
	foreach ($this->folders['children'] as $folder) :
	// Get a sanitised name for the target
	$target = str_replace('/', '-', $folder['data']->relative); ?>
	<li id="<?php echo $target; ?>">
		<i class="icon-folder-2 pull-left" data-toggle="collapse" data-target="#collapseFolder-<?php echo $target; ?>"></i>
		<a href="index.php?option=com_podcastmedia&amp;view=medialist&amp;tmpl=component&amp;folder=<?php echo $folder['data']->relative; ?>" target="folderframe">
			<?php echo $folder['data']->name; ?>
		</a>
		<?php echo $this->getFolderLevel($folder); ?>
	</li>
<?php endforeach;
endif; ?>
</ul>
