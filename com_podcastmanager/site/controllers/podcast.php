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

use Joomla\Utilities\ArrayHelper;

/**
 * Podcast edit controller class.
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 * @since       1.8
 */
class PodcastManagerControllerPodcast extends JControllerForm
{
	/**
	 * The prefix of the models
	 *
	 * @var    string
	 * @since  3.0
	 */
	protected $model_prefix = 'PodcastManagerModel';

	/**
	 * The default single item view
	 *
	 * @var    string
	 * @since  1.8
	 */
	protected $view_item = 'podcast';

	/**
	 * The default list view
	 *
	 * @var    string
	 * @since  1.8
	 */
	protected $view_list = 'feed';

	/**
	 * Method to add a new record.
	 *
	 * @return  boolean  True if a podcast can be added, a JError object if not.
	 *
	 * @since   1.8
	 */
	public function add()
	{
		if ($result = parent::add())
		{
			// Redirect to the return page.
			$this->setRedirect($this->getReturnPage());
		}

		return $result;
	}

	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param   array  $data  An array of input data.
	 *
	 * @return  boolean  True if allowed
	 *
	 * @since   2.0
	 */
	protected function allowAdd($data = [])
	{
		// Initialise variables.
		$feedId = ArrayHelper::getValue($data, 'feedname', $this->input->getUint('filter_feedname', ''), 'int');

		if ($feedId)
		{
			// If the feed has been passed in the data or URL check it.
			return JFactory::getUser()->authorise('core.create', 'com_podcastmanager.feed.' . $feedId);
		}

		// In the absence of better information, revert to the component permissions.
		return parent::allowAdd();
	}

	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key.
	 *
	 * @return  boolean  True if allowed
	 *
	 * @since   2.0
	 */
	protected function allowEdit($data = [], $key = 'id')
	{
		// Initialise variables.
		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;
		$user     = JFactory::getUser();

		// Check feed edit permission.
		if ($user->authorise('core.edit', 'com_podcastmanager.podcast.' . $recordId))
		{
			return true;
		}

		// Fallback on edit.own.
		// First test if the permission is available.
		if ($user->authorise('core.edit.own', 'com_podcastmanager.podcast.' . $recordId) || $user->authorise('core.edit.own', 'com_podcastmanager'))
		{
			// Now test the owner is the user.
			$ownerId = (int) isset($data['created_by']) ? $data['created_by'] : 0;

			if (empty($ownerId) && $recordId)
			{
				// Need to do a lookup from the model.
				$record = $this->getModel()->getItem($recordId);

				if (empty($record))
				{
					return false;
				}

				$ownerId = $record->created_by;
			}

			// If the owner matches 'me' then do the test.
			if ($ownerId == $user->id)
			{
				return true;
			}
		}

		// Check component permissions if a record is not available
		return parent::allowEdit($data, $key);
	}

	/**
	 * Method to cancel an edit.
	 *
	 * @param   string  $key  The name of the primary key of the URL variable.
	 *
	 * @return  boolean  True if access level checks pass, false otherwise.
	 *
	 * @since   1.8
	 */
	public function cancel($key = 'p_id')
	{
		$result = parent::cancel($key);

		// Redirect to the return page.
		$this->setRedirect($this->getReturnPage());

		return $result;
	}

	/**
	 * Method to edit an existing record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if access level check and checkout passes, false otherwise.
	 *
	 * @since   1.8
	 */
	public function edit($key = null, $urlVar = 'p_id')
	{
		// Initialise variables.
		$model   = $this->getModel();
		$table   = $model->getTable();
		$cid     = $this->input->post->get('cid', []);
		$context = "$this->option.edit.$this->context";

		// Determine the name of the primary key for the data.
		if (empty($key))
		{
			$key = $table->getKeyName();
		}

		// To avoid data collisions the urlVar may be different from the primary key.
		if (empty($urlVar))
		{
			$urlVar = $key;
		}

		// Get the previous record id (if any) and the current record id.
		$recordId = (int) (count($cid) ? $cid[0] : $this->input->get($urlVar, '', 'int'));
		$checkin  = property_exists($table, 'checked_out');

		// Access check.
		if (!$this->allowEdit([$key => $recordId], $key))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend(), false
				)
			);

			return false;
		}

		// Attempt to check-out the new record for editing and redirect.
		if ($checkin && !$model->checkout($recordId))
		{
			// Check-out failed, display a notice but allow the user to see the record.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKOUT_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(
				'index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId, $urlVar)
			);

			return false;
		}

		// Check-out succeeded, push the new record id into the session.
		$this->holdEditId($context, $recordId);
		JFactory::getApplication()->setUserState($context . '.data', null);
		$this->setRedirect(
			'index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId, $urlVar)
		);

		return true;
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   integer  $recordId  The primary key id for the item.
	 * @param   string   $urlVar    The name of the URL variable for the id.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   1.8
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = null)
	{
		$append = parent::getRedirectToItemAppend($recordId, $urlVar);

		if ($itemId = $this->input->getUint('Itemid', ''))
		{
			$append .= '&Itemid=' . $itemId;
		}

		if ($return = $this->getReturnPage())
		{
			$append .= '&return=' . base64_encode($return);
		}

		return $append;
	}

	/**
	 * Get the return URL if a "return" variable has been passed in the request
	 *
	 * @return  string  The return URL.
	 *
	 * @since   1.8
	 */
	protected function getReturnPage()
	{
		$return = $this->input->getBase64('return', null);

		if (empty($return) || !JUri::isInternal(base64_decode($return)))
		{
			return JUri::base();
		}

		return base64_decode($return);
	}

	/**
	 * Function that allows child controller access to model data after the data has been saved.
	 *
	 * @param   JModelLegacy  $model      The data model object.
	 * @param   array         $validData  The validated data.
	 *
	 * @return  void
	 *
	 * @since   1.8
	 */
	protected function postSaveHook(JModelLegacy $model, $validData = [])
	{
		if ($this->getTask() == 'save')
		{
			$this->setRedirect(JRoute::_('index.php?option=com_podcastmanager&view=feed&id=' . $validData['feedname'], false));
		}
	}

	/**
	 * Method to save a record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if successful, false otherwise.
	 *
	 * @since   1.8
	 */
	public function save($key = null, $urlVar = 'p_id')
	{
		// If ok, redirect to the return page.
		if ($result = parent::save($key, $urlVar))
		{
			$this->setRedirect($this->getReturnPage());
		}

		return $result;
	}
}
