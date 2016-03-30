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
 * Class to create a media selection modal.
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmanager
 * @since       1.6
 */
class JFormFieldPodcastMedia extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $type = 'PodcastMedia';

	/**
	 * The authorField.
	 *
	 * @var    string
	 * @since  3.0
	 */
	protected $authorField;

	/**
	 * The asset.
	 *
	 * @var    string
	 * @since  3.0
	 */
	protected $asset;

	/**
	 * The link.
	 *
	 * @var    string
	 * @since  3.0
	 */
	protected $link;

	/**
	 * Modal width.
	 *
	 * @var    integer
	 * @since  3.0
	 */
	protected $width;

	/**
	 * Modal height.
	 *
	 * @var    integer
	 * @since  3.0
	 */
	protected $height;

	/**
	 * The directory.
	 *
	 * @var    string
	 * @since  3.0
	 */
	protected $directory;

	/**
	 * Layout to render
	 *
	 * @var    string
	 * @since  3.0
	 */
	protected $layout = 'form.field.podcastmedia';

	/**
	 * Method to get certain otherwise inaccessible properties from the form field object.
	 *
	 * @param   string  $name  The property name for which to the the value.
	 *
	 * @return  mixed  The property value or null.
	 *
	 * @since   3.0
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'authorField':
			case 'asset':
			case 'link':
			case 'width':
			case 'height':
			case 'directory':
				return $this->$name;
		}

		return parent::__get($name);
	}

	/**
	 * Method to set certain otherwise inaccessible properties of the form field object.
	 *
	 * @param   string  $name   The property name for which to the the value.
	 * @param   mixed   $value  The value of the property.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function __set($name, $value)
	{
		switch ($name)
		{
			case 'authorField':
			case 'asset':
			case 'link':
			case 'width':
			case 'height':
			case 'directory':
				$this->$name = (string) $value;
				break;

			default:
				parent::__set($name, $value);
		}
	}

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @see 	JFormField::setup()
	 * @since   3.0
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$result = parent::setup($element, $value, $group);

		if ($result == true)
		{
			$assetField = $this->element['asset_field'] ? (string) $this->element['asset_field'] : 'asset_id';

			$this->authorField = $this->element['created_by_field'] ? (string) $this->element['created_by_field'] : 'created_by';
			$this->asset       = $this->form->getValue($assetField) ? $this->form->getValue($assetField) : (string) $this->element['asset_id'];
			$this->link        = (string) $this->element['link'];
			$this->width       = isset($this->element['width']) ? (int) $this->element['width'] : 800;
			$this->height      = isset($this->element['height']) ? (int) $this->element['height'] : 500;
			$this->directory   = '';

			// Check if only one podcastmedia plugin is enabled
			$count = PodcastManagerHelper::countMediaPlugins();

			if ($count == 1)
			{
				JPluginHelper::importPlugin('podcastmedia');

				$results         = JEventDispatcher::getInstance()->trigger('onPathFind');
				$this->directory = $results['0'];
			}
			elseif ($count > 1)
			{
				// Can only handle one at a time, throw a warning and default
				JFactory::getApplication()->enqueueMessage(JText::_('COM_PODCASTMANAGER_TOO_MANY_MEDIA_PLUGINS'), 'warning');
			}
		}

		return $result;
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.6
	 */
	protected function getInput()
	{
		if (empty($this->layout))
		{
			throw new UnexpectedValueException(sprintf('%s has no layout assigned.', $this->name));
		}

		return $this->getRenderer($this->layout)->render($this->getLayoutData());
	}

	/**
	 * Get the data that is going to be passed to the layout
	 *
	 * @return  array
	 *
	 * @since   3.0
	 */
	public function getLayoutData()
	{
		// Get the basic field data
		$data = parent::getLayoutData();

		$asset = $this->asset;

		if ($asset == '')
		{
			$asset = JFactory::getApplication()->input->get('option');
		}

		$mediaDir = JComponentHelper::getParams('com_podcastmedia')->get('file_path', 'media/com_podcastmanager');

		if ($this->value && file_exists(JPATH_ROOT . '/' . $this->value))
		{
			$folder = explode('/', $this->value);

			// For some reason, using the fix from the platform for multi-level default paths isn't working (figure this out at some point)
			// array_diff_assoc($folder, explode('/', JComponentHelper::getParams('com_podcastmedia')->get('file_path', 'media/com_podcastmanager')));

			// So, we instead just pop off the first two levels of $folder and this should do the trick
			array_shift($folder);
			array_shift($folder);
			array_pop($folder);
			$folder = implode('/', $folder);
		}
		elseif (file_exists(JPATH_ROOT . '/' . $mediaDir . '/' . $this->directory))
		{
			$folder = $this->directory;
		}
		else
		{
			$folder = '';
		}

		$extraData = [
			'asset'         => $asset,
			'authorField'   => $this->authorField,
			'authorId'      => $this->form->getValue($this->authorField),
			'folder'        => $this->folder,
			'link'          => $this->link,
			'preview'       => $this->preview,
			'previewHeight' => $this->previewHeight,
			'previewWidth'  => $this->previewWidth,
		];

		return array_merge($data, $extraData);
	}

	/**
	 * Allow to override renderer include paths in child fields
	 *
	 * @return  array
	 *
	 * @since   3.0
	 */
	protected function getLayoutPaths()
	{
		// Prefer a template override in the currently active template, fall back to the layout in the component otherwise, then the core layouts folder
		return [
			JPATH_THEMES . '/' . JFactory::getApplication()->getTemplate() . '/html/layouts/com_podcastmedia',
			JPATH_ADMINISTRATOR . '/components/com_podcastmedia/layouts',
			JPATH_ROOT . '/layouts'
		];
	}
}
