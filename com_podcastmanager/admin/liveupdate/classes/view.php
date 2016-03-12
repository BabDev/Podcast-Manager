<?php
/**
 * @package   LiveUpdate
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license   GNU GPLv3 or later <https://www.gnu.org/licenses/gpl.html>
 */

defined('_JEXEC') or die();

JLoader::import('joomla.application.component.view');

/**
 * The Live Update MVC view
 */
class LiveUpdateView extends JViewLegacy
{
	public function display($tpl = null)
	{
		$input = JFactory::getApplication()->input;

		// Load the CSS
		$config = LiveUpdateConfig::getInstance();
		$this->config = $config;
		if (!$config->addMedia() && !defined('AKEEBASTRAPPER_VERSION') && version_compare(JVERSION, '3.0', 'lt'))
		{
			// No custom CSS overrides were set; include our own
			$document = JFactory::getDocument();
			$url = JURI::base() . '/components/' . $input->getCmd('option', '') . '/liveupdate/assets/liveupdate.css';
			$document->addStyleSheet($url, 'text/css');
		}

		$requeryURL = rtrim(JURI::base(), '/') . '/index.php?option=' . $input->getCmd('option', '') . '&view=' . $input->getCmd('view', 'liveupdate') . '&force=1';
		$this->requeryURL = $requeryURL;

		$model = $this->getModel();

		$extInfo = (object)$config->getExtensionInformation();
		JToolBarHelper::title($extInfo->title . ' &ndash; ' . JText::_('LIVEUPDATE_TASK_OVERVIEW'), 'liveupdate');
		JToolBarHelper::back('JTOOLBAR_BACK', 'index.php?option=' . $input->getCmd('option', ''));

		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$j3css = <<<CSS
div#toolbar div#toolbar-back button.btn span.icon-back::before {
	content: "";
}
CSS;
			JFactory::getDocument()->addStyleDeclaration($j3css);
		}

		switch ($input->getCmd('task', 'default'))
		{
			case 'startupdate':
				$this->setLayout('startupdate');
				$this->url = 'index.php?option=' . $input->getCmd('option', '') . '&view=' . $input->getCmd('view', 'liveupdate') . '&task=download';
				break;

			case 'install':
				$this->setLayout('install');

				// Get data from the model
				$state = $this->get('State');

				// Are there messages to display ?
				$showMessage = false;
				if (is_object($state))
				{
					$message1 = $state->get('message');
					$message2 = $state->get('extension.message');
					$showMessage = ($message1 || $message2);
				}

				$this->showMessage = $showMessage;
				$this->state = & $state;

				break;

			case 'nagscreen':
				$this->setLayout('nagscreen');
				$this->updateInfo = LiveUpdate::getUpdateInformation();
				$this->runUpdateURL = 'index.php?option=' . $input->getCmd('option', '') . '&view=' . $input->getCmd('view', 'liveupdate') . '&task=startupdate&skipnag=1';
				break;

			case 'overview':
			default:
				$this->setLayout('overview');

				$force = $input->getInt('force', 0);
				$this->updateInfo = LiveUpdate::getUpdateInformation($force);
				$this->runUpdateURL = 'index.php?option=' . $input->getCmd('option', '') . '&view=' . $input->getCmd('view', 'liveupdate') . '&task=startupdate';

				$needsAuth = !($config->getAuthorization()) && ($config->requiresAuthorization());
				$this->needsAuth = $needsAuth;
				break;
		}

		parent::display($tpl);
	}
}
