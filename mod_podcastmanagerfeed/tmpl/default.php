<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  mod_podcastmanagerfeed
 *
 * @copyright   Copyright (C) 2011-2013 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;
?>
<ul class="podmanfeed<?php echo $moduleclass_sfx; ?>">
<?php foreach ($list as $item) : ?>
	<li>
		<?php if ((JPluginHelper::isEnabled('content', 'podcastmanager')) && $params->get('show_item_player') == 1) :
			echo $item->text;
		else : ?>
			<a href="<?php echo $item->link; ?>">
				<?php echo $item->title; ?>
			</a>
			<?php if ($params->get('author') == 1 && strlen($item->itAuthor) >= 1) :
				echo 'by ' . $item->itAuthor;
			endif;
		endif;
		if ($params->get('description') == 1 && strlen($item->itSummary) >= 1) :
			echo '<br />' . $item->itSummary;
		endif;
		if ($params->get('created') == 1) :
			echo '<br />' . JText::sprintf('JGLOBAL_CREATED_DATE_ON', $item->created);
		endif; ?>
	</li>
<?php endforeach; ?>
</ul>
