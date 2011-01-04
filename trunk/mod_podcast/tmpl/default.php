<?php defined('_JEXEC') or die('Restricted access'); ?>
<div><?php echo $params->get('text'); ?></div>
<div><a href="<?php echo $link; ?>"><?php echo $img; ?></a></div>
<?php if($params->get('plainlink')) { ?><div><a href="<?php echo $plainlink; ?>">Full Feed</a></div><?php } ?>
