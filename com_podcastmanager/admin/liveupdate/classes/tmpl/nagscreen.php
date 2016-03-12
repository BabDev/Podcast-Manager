<?php
/**
 * @package   LiveUpdate
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license   GNU GPLv3 or later <https://www.gnu.org/licenses/gpl.html>
 */

defined('_JEXEC') or die();

$stability = JText::_('LIVEUPDATE_STABILITY_' . $this->updateInfo->stability);
?>

<div class="liveupdate">

	<div id="nagscreen" class="alert alert-warning">
		<h2><?php echo JText::_('LIVEUPDATE_NAGSCREEN_HEAD') ?></h2>

		<p class="nagtext"><?php echo JText::sprintf('LIVEUPDATE_NAGSCREEN_BODY', $this->updateInfo->version, $stability) ?></p>
	</div>
	<p class="liveupdate-buttons">
		<button onclick="window.location='<?php echo $this->runUpdateURL ?>'"
				class="btn btn-danger btn-large">
			<?php echo JText::_('LIVEUPDATE_NAGSCREEN_BUTTON') ?>
		</button>
	</p>

	<p class="liveupdate-poweredby">
		Powered by <a href="https://www.akeebabackup.com/software/akeeba-live-update.html">Akeeba Live Update</a>
	</p>

</div>