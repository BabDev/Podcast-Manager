<?php
/**
 * @package   LiveUpdate
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license   GNU GPLv3 or later <https://www.gnu.org/licenses/gpl.html>
 */

defined('_JEXEC') or die();

JHtml::_('behavior.framework');
JHtml::_('behavior.modal');
?>

<div class="liveupdate">

	<?php if ($this->updateInfo->releasenotes): ?>
		<div style="display:none;">
			<div id="liveupdate-releasenotes">
				<div class="liveupdate-releasenotes-text">
					<?php echo $this->updateInfo->releasenotes ?>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<?php if (!$this->updateInfo->supported): ?>
		<div class="liveupdate-notsupported alert alert-error">
			<h3><?php echo JText::_('LIVEUPDATE_NOTSUPPORTED_HEAD') ?></h3>

			<p><?php echo JText::_('LIVEUPDATE_NOTSUPPORTED_INFO'); ?></p>

			<p class="liveupdate-url">
				<?php echo $this->escape($this->updateInfo->extInfo->updateurl) ?>
			</p>

			<p><?php echo JText::sprintf('LIVEUPDATE_NOTSUPPORTED_ALTMETHOD', $this->escape($this->updateInfo->extInfo->title)); ?></p>

			<p class="liveupdate-buttons">
				<button
					onclick="window.location='<?php echo $this->requeryURL ?>'"
					class="btn btn-inverse">
					<span class="icon icon-white icon-refresh"></span>
					<?php echo JText::_('LIVEUPDATE_REFRESH_INFO') ?>
				</button>
			</p>
		</div>

	<?php elseif ($this->updateInfo->stuck): ?>
		<div class="liveupdate-stuck alert alert-error">
			<h3><?php echo JText::_('LIVEUPDATE_STUCK_HEAD') ?></h3>

			<p><?php echo JText::_('LIVEUPDATE_STUCK_INFO'); ?></p>

			<p><?php echo JText::sprintf('LIVEUPDATE_NOTSUPPORTED_ALTMETHOD', $this->escape($this->updateInfo->extInfo->title)); ?></p>

			<p class="liveupdate-buttons">
				<button
					onclick="window.location='<?php echo $this->requeryURL ?>'"
					class="btn btn-inverse">
					<span class="icon icon-white icon-refresh"></span>
					<?php echo JText::_('LIVEUPDATE_REFRESH_INFO') ?>
				</button>
			</p>
		</div>

	<?php
	else: ?>
		<?php
		$class = $this->updateInfo->hasUpdates ? 'hasupdates' : 'noupdates';
		$class2 = $this->updateInfo->hasUpdates ? 'alert-warning' : 'alert-success';
		$iconClass = $this->updateInfo->hasUpdates ? 'icon-warning' : 'icon-ok';
		$tag = $this->updateInfo->hasUpdates ? 'hasupdates' : 'noupdates';

		// Add the authentication string to the URL
		$url = $this->updateInfo->downloadURL;
		/** @var LiveUpdateAbstractConfig $config */
		$config = LiveUpdateAbstractConfig::getInstance();

		if ($config->requiresAuthorization())
		{
			$authParams = $config->getAuthorizationParameters();

			if (!empty($authParams))
			{
				JLoader::import('joomla.uri.uri');
				$uri = new JUri($url);

				foreach ($authParams as $k => $v)
				{
					$uri->setVar($k, $v);
				}

				$url = $uri->toString();
			}
		}

		?>
		<?php if ($this->needsAuth): ?>
			<p class="liveupdate-error-needsauth alert alert-error">
				<?php echo JText::_('LIVEUPDATE_ERROR_NEEDSAUTH'); ?>
			</p>
		<?php endif; ?>
		<div class="liveupdate-<?php echo $class ?>">
			<div class="alert <?php echo $class2 ?>">
				<h4>
					<span class="icon <?php echo $iconClass ?>"></span>
					<?php echo JText::_('LIVEUPDATE_' . strtoupper($tag) . '_HEAD') ?>
				</h4>
			</div>

			<table class="liveupdate-infotable table table-striped">
				<tr class="liveupdate-row row0">
					<td class="liveupdate-label"><?php echo JText::_('LIVEUPDATE_CURRENTVERSION') ?></td>
					<td class="liveupdate-data"><?php echo $this->updateInfo->extInfo->version ?></td>
				</tr>
				<tr class="liveupdate-row row1">
					<td class="liveupdate-label"><?php echo JText::_('LIVEUPDATE_LATESTVERSION') ?></td>
					<td class="liveupdate-data"><?php echo $this->updateInfo->version ?></td>
				</tr>
				<tr class="liveupdate-row row0">
					<td class="liveupdate-label"><?php echo JText::_('LIVEUPDATE_LATESTRELEASED') ?></td>
					<td class="liveupdate-data"><?php echo $this->updateInfo->date ?></td>
				</tr>
				<tr class="liveupdate-row row1">
					<td class="liveupdate-label"><?php echo JText::_('LIVEUPDATE_DOWNLOADURL') ?></td>
					<td class="liveupdate-data"><a
							href="<?php echo $url ?>"><?php echo $this->escape($url) ?></a></td>
				</tr>
				<?php if (!empty($this->updateInfo->releasenotes) || !empty($this->updateInfo->infoURL)): ?>
					<tr class="liveupdate-row row1">
						<td class="liveupdate-label"><?php echo JText::_('LIVEUPDATE_RELEASEINFO') ?></td>
						<td class="liveupdate-data">
							<?php if ($this->updateInfo->releasenotes): ?>
								<a href="#"
								   id="btnLiveUpdateReleaseNotes"><?php echo JText::_('LIVEUPDATE_RELEASENOTES') ?></a>
								<?php
								JHTML::_('behavior.framework');
								JHTML::_('behavior.modal');

								$script = <<<JS
window.addEvent( 'domready' ,  function() {
	$('btnLiveUpdateReleaseNotes').addEvent('click', showLiveUpdateReleaseNotes);
});

function showLiveUpdateReleaseNotes()
{
	var liveupdateReleasenotes = $('liveupdate-releasenotes').clone();

	SqueezeBox.fromElement(
		liveupdateReleasenotes, {
			handler: 'adopt',
			size: {
				x: 450,
				y: 350
			}
		}
	);
}
JS;
								$document = JFactory::getDocument();
								$document->addScriptDeclaration($script, 'text/javascript');
								?>
							<?php endif; ?>
							<?php if ($this->updateInfo->releasenotes && $this->updateInfo->infoURL): ?>
								&nbsp;&bull;&nbsp;
							<?php endif; ?>
							<?php if ($this->updateInfo->infoURL): ?>
								<a href="<?php echo $this->updateInfo->infoURL ?>"
								   target="_blank"><?php echo JText::_('LIVEUPDATE_READMOREINFO') ?></a>
							<?php endif; ?>
						</td>
					</tr>
				<?php endif; ?>
			</table>

			<p class="liveupdate-buttons">
				<?php if ($this->updateInfo->hasUpdates): ?>
					<?php $disabled = $this->needsAuth ? 'disabled="disabled"' : '' ?>
					<button <?php echo $disabled ?>
						onclick="window.location='<?php echo $this->runUpdateURL ?>'"
						class="btn btn-large btn-primary">
						<span class="icon icon-white icon-arrow-right"></span>
						<?php echo JText::_('LIVEUPDATE_DO_UPDATE') ?>
					</button>
				<?php endif; ?>
				<button
					onclick="window.location='<?php echo $this->requeryURL ?>'"
					class="btn btn-inverse">
					<span class="icon icon-white icon-refresh"></span>
					<?php echo JText::_('LIVEUPDATE_REFRESH_INFO') ?>
				</button>
			</p>
		</div>

	<?php endif; ?>

	<p class="liveupdate-poweredby">
		Powered by <a href="https://www.akeebabackup.com/software/akeeba-live-update.html">Akeeba Live Update</a>
	</p>

</div>
