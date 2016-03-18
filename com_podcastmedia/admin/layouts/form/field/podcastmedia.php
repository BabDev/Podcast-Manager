<?php
/**
 * Podcast Manager for Joomla!
 *
 * @package     PodcastManager
 * @subpackage  com_podcastmedia
 *
 * @copyright   Copyright (C) 2011-2015 Michael Babker. All rights reserved.
 * @license     GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

defined('_JEXEC') or die;

/**
 * Layout variables
 * -----------------
 * @var   string   $autocomplete    Autocomplete attribute for the field.
 * @var   boolean  $autofocus       Is autofocus enabled?
 * @var   string   $class           Classes for the input.
 * @var   string   $description     Description of the field.
 * @var   boolean  $disabled        Is this field disabled?
 * @var   string   $group           Group the field belongs to. <fields> section in form XML.
 * @var   boolean  $hidden          Is this field hidden in the form?
 * @var   string   $hint            Placeholder for the field.
 * @var   string   $id              DOM id of the field.
 * @var   string   $label           Label of the field.
 * @var   string   $labelclass      Classes to apply to the label.
 * @var   boolean  $multiple        Does this field support multiple values?
 * @var   string   $name            Name of the input field.
 * @var   string   $onchange        Onchange attribute for the field.
 * @var   string   $onclick         Onclick attribute for the field.
 * @var   string   $pattern         Pattern (Reg Ex) of value of the form field.
 * @var   boolean  $readonly        Is this field read only?
 * @var   boolean  $repeat          Allows extensions to duplicate elements.
 * @var   boolean  $required        Is this field required?
 * @var   integer  $size            Size attribute of the input.
 * @var   boolean  $spellcheck      Spellcheck state for the form field.
 * @var   string   $validate        Validation rules to apply.
 * @var   string   $value           Value attribute of the field.
 * @var   array    $checkedOptions  Options that will be set as checked.
 * @var   boolean  $hasValue        Has this field a value assigned?
 * @var   array    $options         Options available for this field.
 *
 * @var   string   $asset           The asset text
 * @var   string   $authorField     The label text
 * @var   string   $authorId        The author ID
 * @var   string   $folder          The folder text
 * @var   string   $link            The link text
 */
extract($displayData);

// The button.
if ($disabled != true)
{
	JHtml::_('bootstrap.tooltip');
}

$attr = '';

// Initialize some field attributes.
$attr .= !empty($class) ? ' class="input-small hasTooltip field-podcastmedia-input ' . $class . '"' : ' class="input-small hasTooltip field-podcastmedia-input"';
$attr .= !empty($size) ? ' size="' . $size . '"' : '';

// Initialize JavaScript field attributes.
$attr .= !empty($onchange) ? ' onchange="' . $onchange . '"' : '';

// The url for the modal
$url = ($readonly
	? ''
	: ($link ? $link
		: 'index.php?option=com_podcastmedia&amp;view=audio&amp;tmpl=component&amp;asset=' . $asset . '&amp;author=' . $authorId)
	. '&amp;fieldid={field-podcastmedia-id}&amp;folder=' . $folder);
?>
<div class="field-podcastmedia-wrapper"
	data-basepath="<?php echo JUri::root(); ?>"
	data-url="<?php echo $url; ?>"
	data-modal=".modal"
	data-modal-width="100%"
	data-modal-height="400px"
	data-input=".field-podcastmedia-input"
	data-button-select=".button-select"
	data-button-clear=".button-clear"
	data-button-save-selected=".button-save-selected"
	>
	<?php
	// Render the modal
	echo JHtml::_(
		'bootstrap.renderModal',
		'mediaModal_' . $id,
		[
			'title'       => JText::_('COM_PODCASTMANAGER_SELECT_PODCAST'),
			'closeButton' => true,
			'footer'      => '<button type="button" class="btn" data-dismiss="modal">' . JText::_('JCANCEL') . '</button>'
		]
	);

	JHtml::_('script', 'podcastmanager/podcast.js', false, true);
	JHtml::_('script', 'podcastmanager/mediafield.js', false, true);

	?>
	<div class="input-append">
		<input type="text" name="<?php echo $name; ?>" id="<?php echo $id; ?>" value="<?php echo htmlspecialchars($value, ENT_COMPAT, 'UTF-8'); ?>"<?php if ($disabled == true): ?> readonly="readonly"<?php endif; ?><?php echo $attr; ?>/>
		<?php if ($disabled != true) : ?>
			<a class="btn add-on button-select"><?php echo JText::_('JLIB_FORM_BUTTON_SELECT'); ?></a>
			<a class="btn icon-remove hasTooltip add-on button-clear" title="<?php echo JText::_('JLIB_FORM_BUTTON_CLEAR'); ?>"></a>
			<a class="btn" title="<?php echo JText::_('COM_PODCASTMANAGER_PARSE_METADATA'); ?>" href="#" onclick="PodcastManager.parseMetadata()"><i class="icon-loop"></i> <?php echo JText::_('COM_PODCASTMANAGER_PARSE_METADATA'); ?></a>
		<?php endif; ?>
	</div>
</div>
