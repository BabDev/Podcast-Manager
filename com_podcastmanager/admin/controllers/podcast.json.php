<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 *
 * @copyright   Copyright (C) 2011-2015 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

JLoader::register('PodcastManagerHelper', JPATH_ADMINISTRATOR . '/components/com_podcastmanager/helpers/podcastmanager.php');

/**
 * Podcast edit controller class for AJAX requests.
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 * @since       2.2
 */
class PodcastManagerControllerPodcast extends JControllerLegacy
{
	/**
	 * Retrieves the metadata for a specified podcast and returns it in a JSON string
	 *
	 * @return  void
	 *
	 * @since   2.2
	 */
	public function getMetadata()
	{
		try
		{
			$data = PodcastManagerHelper::fillMetaData($this->input->post->getString('filename', ''));

			// Grab the messages to attach them separately to the response object
			$messages = $data->messages;
			unset($data->messages);

			$response = new JResponseJson($data);
			$response->messages = $messages;
		}
		catch (RuntimeException $e)
		{
			$response = new JResponseJson($e);
		}

		echo json_encode($response);
	}
}
