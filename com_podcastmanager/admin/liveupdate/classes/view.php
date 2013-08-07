<?php
/**
 * @package LiveUpdate
 * @copyright Copyright (c)2010-2013 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license GNU LGPLv3 or later <http://www.gnu.org/copyleft/lesser.html>
 */

defined('_JEXEC') or die();

JLoader::import('joomla.application.component.view');

if(!class_exists('JoomlaCompatView')) {
	if(interface_exists('JView')) {
		abstract class JoomlaCompatView extends JViewLegacy {}
	} else {
		class JoomlaCompatView extends JView {}
	}
}

/**
 * The Live Update MVC view
 */
class LiveUpdateView extends JoomlaCompatView
{
	public function display($tpl = null)
	{
		// Load the CSS
		$config = LiveUpdateConfig::getInstance();
		$this->assign('config', $config);
		if(!$config->addMedia()) {
			// No custom CSS overrides were set; include our own
			$document = JFactory::getDocument();
			$url = JURI::base().'/components/'.JRequest::getCmd('option','').'/liveupdate/assets/liveupdate.css';
			$document->addStyleSheet($url, 'text/css');
		}

		$requeryURL = rtrim(JURI::base(),'/').'/index.php?option='.JRequest::getCmd('option','').'&view='.JRequest::getCmd('view','liveupdate').'&force=1';
		$this->assign('requeryURL', $requeryURL);

		$model = $this->getModel();

		$extInfo = (object)$config->getExtensionInformation();
		JToolBarHelper::title($extInfo->title.' &ndash; '.JText::_('LIVEUPDATE_TASK_OVERVIEW'),'liveupdate');
		JToolBarHelper::back('JTOOLBAR_BACK', 'index.php?option='.JRequest::getCmd('option',''));

		if(version_compare(JVERSION, '3.0', 'ge')) {
			$j3css = <<<ENDCSS
div#toolbar div#toolbar-back button.btn span.icon-back::before {
	content: "";
}
ENDCSS;
			JFactory::getDocument()->addStyleDeclaration($j3css);
		}
		
		switch(JRequest::getCmd('task','default'))
		{
			case 'startupdate':
				$this->setLayout('startupdate');
				$this->assign('url','index.php?option='.JRequest::getCmd('option','').'&view='.JRequest::getCmd('view','liveupdate').'&task=download');
				break;

			case 'install':
				$this->setLayout('install');

				// Get data from the model
				$state		= $this->get('State');

				// Are there messages to display ?
				$showMessage	= false;
				if ( is_object($state) )
				{
					$message1		= $state->get('message');
					$message2		= $state->get('extension.message');
					$showMessage	= ( $message1 || $message2 );
				}

				$this->assign('showMessage',	$showMessage);
				$this->assignRef('state',		$state);

				break;

			case 'nagscreen':
				$this->setLayout('nagscreen');
				$this->assign('updateInfo', LiveUpdate::getUpdateInformation());
				$this->assign('runUpdateURL','index.php?option='.JRequest::getCmd('option','').'&view='.JRequest::getCmd('view','liveupdate').'&task=startupdate&skipnag=1');
				break;

			case 'overview':
			default:
				$this->setLayout('overview');

				$force = JRequest::getInt('force',0);
				$this->assign('updateInfo', LiveUpdate::getUpdateInformation($force));
				$this->assign('runUpdateURL','index.php?option='.JRequest::getCmd('option','').'&view='.JRequest::getCmd('view','liveupdate').'&task=startupdate');

				$needsAuth = !($config->getAuthorization()) && ($config->requiresAuthorization());
				$this->assign('needsAuth', $needsAuth);
				break;
		}

		parent::display($tpl);
	}
}
