/**
* Podcast Manager for Joomla!
*
* @copyright	Copyright (C) 2011-2014 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
* @package		PodcastManager
* @subpackage	com_podcastmedia
*
* Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
* Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
*/

(function($) {
var AudioManager = this.AudioManager = {
	initialize: function()
	{
		o = this._getUriObject(window.self.location.href);
		q = this._getQueryObject(o.query);
		this.editor = decodeURIComponent(q['e_name']);

		// Setup audio listing objects
		this.folderlist = document.getElementById('folderlist');

		this.frame		= window.frames['audioframe'];
		this.frameurl	= this.frame.location.href;

		// Setup audio listing frame
		this.audioframe = document.getElementById('audioframe');
		this.audioframe.manager = this;
		$(this.audioframe).on('load', function(){ AudioManager.onloadaudioview(); });

		// Setup folder up button
		this.upbutton = document.getElementById('upbutton');
		$(this.upbutton).off('click');
		$(this.upbutton).on('click', function(){ AudioManager.upFolder(); });
	},

	onloadaudioview: function()
	{
		// Update the frame url
		this.frameurl = this.frame.location.href;

		var folder = this.getAudioFolder();
		for(var i = 0; i < this.folderlist.length; i++)
		{
			if (folder == this.folderlist.options[i].value) {
				this.folderlist.selectedIndex = i;
				if (this.folderlist.className.test(/\bchzn-done\b/)) {
					$(this.folderlist).trigger('liszt:updated');
				}
				break;
			}
		}

		a = this._getUriObject($('#uploadForm').attr('action'));
		q = this._getQueryObject(a.query);
		q['folder'] = folder;
		var query = [];
		for (var k in q) {
			var v = q[k];
			if (q.hasOwnProperty(k) && v !== null) {
				query.push(k+'='+v);
			}
		}
		a.query = query.join('&');
		var portString = '';
		if (typeof(a.port) !== 'undefined' && a.port != 80) {
			portString = ':'+a.port;
		}
		$('#uploadForm').attr('action', a.scheme+'://'+a.domain+portString+a.path+'?'+a.query);
	},

	getAudioFolder: function()
	{
		var url 	= this.frame.location.search.substring(1);
		var args	= this.parseQuery(url);

		return args['folder'];
	},

	setFolder: function(folder,asset,author)
	{
		for(var i = 0; i < this.folderlist.length; i++)
		{
			if (folder == this.folderlist.options[i].value) {
				this.folderlist.selectedIndex = i;
				if (this.folderlist.className.test(/\bchzn-done\b/)) {
					$(this.folderlist).trigger('liszt:updated');
				}
				break;
			}
		}
		this.frame.location.href='index.php?option=com_podcastmedia&view=audiolist&tmpl=component&folder=' + folder + '&asset=' + asset + '&author=' + author;
	},

	getFolder: function() {
		return this.folderlist.value;
	},

	upFolder: function()
	{
		var currentFolder = this.getFolder();

		if(currentFolder.length < 2) {
			return false;
		}

		var folders = currentFolder.split('/');
		var search = '';

		for(var i = 0; i < folders.length - 1; i++) {
			search += folders[i];
			search += '/';
		}

		// remove the trailing slash
		search = search.substring(0, search.length - 1);

		for(var i = 0; i < this.folderlist.length; i++)
		{
			var thisFolder = this.folderlist.options[i].value;

			if(thisFolder == search)
			{
				this.folderlist.selectedIndex = i;
				var newFolder = this.folderlist.options[i].value;
				this.setFolder(newFolder);
				break;
			}
		}
	},

	populateFields: function(file)
	{
		$("#f_url").val(audio_base_path+file);
	},

	showMessage: function(text)
	{
		var message  = document.getElementById('message');
		var messages = document.getElementById('messages');

		if (message.firstChild)
			message.removeChild(message.firstChild);

		message.appendChild(document.createTextNode(text));
		messages.style.display = "block";
	},

	parseQuery: function(query)
	{
		var params = new Object();
		if (!query) {
			return params;
		}
		var pairs = query.split(/[;&]/);
		for ( var i = 0; i < pairs.length; i++ )
		{
			var KeyVal = pairs[i].split('=');
			if ( ! KeyVal || KeyVal.length != 2 ) {
				continue;
			}
			var key = unescape( KeyVal[0] );
			var val = unescape( KeyVal[1] ).replace(/\+ /g, ' ');
			params[key] = val;
	   }
	   return params;
	},

	refreshFrame: function()
	{
		this._setFrameUrl();
	},

	_setFrameUrl: function(url)
	{
		if (url != null) {
			this.frameurl = url;
		}
		this.frame.location.href = this.frameurl;
	},

	_getQueryObject: function(q) {
		var vars = q.split(/[&;]/);
		var rs = {};
		if (vars.length) vars.forEach(function(val) {
			var keys = val.split('=');
			if (keys.length && keys.length == 2) rs[encodeURIComponent(keys[0])] = encodeURIComponent(keys[1]);
		});
		return rs;
	},

	_getUriObject: function(u){
		var bitsAssociate = {}, bits = u.match(/^(?:([^:\/?#.]+):)?(?:\/\/)?(([^:\/?#]*)(?::(\d*))?)((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[\?#]|$)))*\/?)?([^?#\/]*))?(?:\?([^#]*))?(?:#(.*))?/);
		['uri', 'scheme', 'authority', 'domain', 'port', 'path', 'directory', 'file', 'query', 'fragment'].forEach(function(key, index) {
			bitsAssociate[key] = bits[index];
		});

		return (bits)
			? bitsAssociate
			: null;
	}
};
})(jQuery);

jQuery(function(){
	AudioManager.initialize();
});
