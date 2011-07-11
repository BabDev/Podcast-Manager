<?php
/**
* Podcast Manager for Joomla!
*
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
*
* Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
* Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
*/

// Restricted access
defined('_JEXEC') or die;

// Customized info CSS
JHtml::stylesheet('administrator/templates/minima/html/com_podcastmanager/info/info.css', false, false, false);

?>
<p class="podMan-welcome">Thank you for installing the Podcast Manager extension suite for Joomla!  For any support issues, please visit <a href="http://www.flbab.com/extensions/podcast-manager" target="_blank">http://www.flbab.com/extensions/podcast-manager</a> for documentation and links for bug reporting.</p>

<div class="podMan-header">How Podcast Manager Works</div>
<div class="divider"></div>
<div>
	<p>Podcast Manager allows you to manage a podcast feed via your Joomla! website.  The suite is bundled with six extensions:</p>
	<ul class="minimaList">
		<li>Podcast Manager Component - Used to manage feeds, podcasts, and their associated metadata; the front-end includes management tools as well as a feed view for public listing</li>
		<li>Podcast Media Component - Used to manage the podcast files; based off the Joomla! Media Manager with modifications specific to this suite's operation</li>
		<li>getID3 Library - The getID3 library is used to extract metadata from uploaded files to pre-fill your metadata form</li>
		<li>Feed Module - Displays links to the latest episodes in a specified feed</li>
		<li>Link Module - Displays a link to the podcast feed</li>
		<li>Content Plugin - The content plugin allows users to add a podcast player directly into an article by adding {podcast Title} into the article editor; supports Flash, Javascript, QuickTime, experimental future support for HTML5 with Flash fallback, and allows for a custom code definition</li>
		<li>Editor Plugin - Integrates into the article editor by adding a "Podcast" button, allowing the user to select a podcast to insert into an article</li>
	</ul>
</div>

<div class="podMan-header">Allowed File Types</div>
<div class="divider"></div>
<div>
	<p>iTunes only allows certain file types as podcast items.  Therefore, the media component has been hard coded to only allow the following file types:</p>
	<ul class="minimaList">
		<li>Audio - MP3, M4A</li>
		<li>Video - MP4, M4V, MOV</li>
	</ul>
</div>

<div class="podMan-header">What to expect</div>
<div class="divider"></div>
<div>
	<p>The Podcast Manager component gives the user full flexibility over their feeds and podcast items, and can expect an experience similar to the Article Manager but fine tuned for podcasting.</p>
	<p>Podcast Manager allows users to upload podcasts through the Joomla! interface via a refactored version of the image insertion tool found in numerous admin options specifically for this component.  On upload, information about the podcast file will be pre-populated thanks to integration from the getID3 library.  Users will be able to manage their podcast files through a customized distribution of the core Media Manager as well.</p>
	<p>Users are able to "stage" a podcast and feed for publishing by specifying a publish time, perfect for uploading a podcast while the feed owner is away.</p>
	<p>Podcast Manager takes full and efficient use of the Joomla! framework and is primed for full integration and further expansion with minimal coding changes.</p>
	<p>The final product from Podcast Manager, be it the RSS feed, feed listing module, or front-end feed view, is standards compliant and passes all appropriate compliance tests.  All views are derived from core Joomla! component views, re-using the same styling options to allow for quick integration into any site.  Additionally, HTML layout overrides are available for the Hathor and Minima administrator templates, adding to the seemless integration.</p>
</div>

<div class="podMan-header">Translations</div>
<div class="divider"></div>
<div>
	<p>Podcast Manager is distributed in the same language as the default Joomla! installation; British English (en-GB).  The entire extension suite has fully customizable language strings to allow for translation to any language.  At this time, no translations are distributed with the extension.  However, translation packs that are contributed will be distributed with full credit to the translators.</p>
</div>

<div class="podMan-header">Credits and Licensing</div>
<div class="divider"></div>
<div>
	<p>Podcast Manager is distributed with the same license as Joomla!; the GPL v2 License.  In order to function with all the features programmed, additional projects have also been included.  These projects are:</p>
	<ul class="minimaList">
		<li><b><a href="http://www.getid3.org" target="_blank">getID3</a></b> developed by James Heinrich and released under the GPL v2 license</li>
		<li><b><a href="http://www.schillmania.com/projects/soundmanager2/" target="_blank">SoundManager2</a></b> developed by Scott Schiller and released under the BSD license (see soundmanager/license.txt in the content plugin for license details)</li>
		<li><b><a href="http://musicplayer.sourceforge.net" target="_blank">XSPF Player Lite</a></b> developed by Fabricio Zuardi and released under the BSD license (see podcast/xspf_license.txt in the content plugin for license details)</li>
	</ul>
</div>
