<?php
/**
* Podcast Manager for Joomla!
*
* @version		$Id$
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* 
*/

// Restricted access
defined('_JEXEC') or die();

JHTML::stylesheet('administrator/components/com_podcastmanager/media/css/info.css', false, false, false);
JHTML::stylesheet('administrator/components/com_podcastmanager/media/css/template.css', false, false, false);

//TODO: Convert this page to JText

?>
<div class="podMan-welcome">Thank you for installing the Podcast Manager extension suite for Joomla!  For any support issues, please visit <a href="http://www.flbab.com/extensions/podcast-manager" target="_blank">http://www.flbab.com/extensions/podcast-manager</a>.</div>

<div class="podMan-header">How Podcast Manager Works</div>
<div class="divider"></div>
<div>
	<p>Podcast Manager allows you to manage a podcast feed via your Joomla! website.  The suite is bundled with five extensions:</p>
	<ul>
		<li>Component - Used to upload all podcast files, edit podcast metadata, and manage whether podcasts are published or not</li>
		<li>getID3 Library - The getID3 library is used to extract metadata from uploaded files to pre-fill your metadata form.</li>
		<li>Module - Displays a link to the podcast feed</li>
		<li>Content Plugin - The content plugin allows users to add a podcast player directly into an article by adding {podcast title}{/podcast} into the article editor</li>
		<li>Editor Plugin - Integrates into the article editor by adding a "Podcast" button, allowing the user to select a podcast to insert into an article</li>
	</ul>
</div>

<div class="podMan-header">What to expect</div>
<div class="divider"></div>
<div>
	<p>The Podcast Manager component gives the user full flexibility over their podcast items, and can expect an experience similar to the Article Manager but fine tuned for podcasting.</p>
	<p>Podcast Manager allows users to upload podcasts through the Joomla! interface via a refactored version of the image insertion tool found in numerous admin options specifically for this component.  On upload, information about the podcast file will be pre-populated thanks to integration from the getID3 library.  Users will be able to manage their podcast files through the core Media Manager as well.</p>
	<p>Users are able to "stage" a podcast for a feed by specifying a publish time, perfect for uploading a podcast while the feed owner is away.</p>
	<p>Podcast Manager takes full and efficient use of the Joomla! framework and is primed for full integration and further expansion with minimal coding changes.</p>
	<p>The final product from Podcast Manager, be it the RSS feed that can be inserted into iTunes or the feed module, is standards compliant and passes all appropriate compliance tests.</p>
</div>

<div class="podMan-header">New Beginnings</div>
<div class="divider"></div>
<div>
	<p>Podcast Manager is a fresh adaption on a popular extension; Joe LeBlanc's Podcast Suite (having been produced since the days of Joomla! 1.0).  Users familiar with Joe's product will immediately see the similarities while being introduced to more control and more power over the podcast feed.  Because of the scope of change within the component's code, migration from Podcast Suite 1.5 is not possible.</p>
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
	<ul>
		<li><b><a href="http://www.getid3.org">getID3</a></b> developed by James Heinrich and released under the GPL v2 license</li>
		<li><b><a href="http://musicplayer.sourceforge.net">XSPF Player Lite</a></b> developed by Fabricio Zuardi and released under the BSD license (see xspf_license.txt in the content plugin for license details)</li>
	</ul>
</div>