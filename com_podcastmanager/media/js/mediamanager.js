/**
 * Podcast Manager for Joomla!
 *
 * @package		PodcastManager
 * @subpackage	com_podcastmanager
 *
 * @copyright	Copyright (C) 2011-2015 Michael Babker. All rights reserved.
 * @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * Podcast Manager is based upon the ideas found in Podcast Suite created by Joe LeBlanc
 * Original copyright (c) 2005 - 2008 Joseph L. LeBlanc and released under the GPLv2 license
 */

if (typeof jQuery === 'undefined') {
    throw new Error('PodcastMediaManager JavaScript requires jQuery')
}

!function (jQuery, window) {
	'use strict';

	var PodcastMediaManager = window.PodcastMediaManager = {
		/**
		 * Basic setup
		 *
		 * @return  void
		 */
		initialize: function () {
			this.folderpath = jQuery('#folderpath');
			this.updatepaths = jQuery('input.update-folder');
			this.frame = window.frames.folderframe;
			this.frameurl = this.frame.location.href;
		},

		/**
		 * Submit the form
		 *
		 * @param {String} task
		 */
		submit: function (task) {
			var form = this.frame.document.getElementById('mediamanager-form');
			form.task.value = task;

			if (jQuery('#username').length) {
				form.username.value = jQuery('#username').val();
				form.password.value = jQuery('#password').val();
			}

			form.submit();
		},

		/**
		 * Behavior for when the frame is loaded
		 */
		onloadframe: function () {
			// Update the frame url
			this.frameurl = this.frame.location.href;

			var folder = this.getFolder() || '',
				query = [],
				a = getUriObject(jQuery('#uploadForm').attr('action')),
				q = getQueryObject(a.query),
				k,
				v;

			this.updatepaths.each(function (path, el) {
				el.value = folder;
			});

			this.folderpath.value = basepath + (folder ? '/' + folder : '');

			q.folder = folder;

			for (k in q) {
				if (!q.hasOwnProperty(k)) {
					continue;
				}

				v = q[k];
				query.push(k + (v === null ? '' : '=' + v));
			}

			a.query = query.join('&');
			a.fragment = null;

			jQuery('#uploadForm').attr('action', buildUri(a));
			jQuery('#' + viewstyle).addClass('active');
		},

		/**
		 * Switch the view type
		 *
		 * @param {String} type
		 */
		setViewType: function (type) {
			jQuery('#' + type).addClass('active');
			jQuery('#' + viewstyle).removeClass('active');
			viewstyle = type;
			var folder = this.getFolder();

			this.setFrameUrl('index.php?option=com_podcastmedia&view=medialist&tmpl=component&folder=' + folder + '&layout=' + type);
		},

		/**
		 * Refresh the iframe
		 */
		refreshFrame: function () {
			this.setFrameUrl();
		},

		/**
		 * Get the folder name
		 *
		 * @returns {String}
         */
		getFolder: function () {
			var args = getQueryObject(this.frame.location.search.substring(1));

			args.folder = args.folder === undefined ? '' : args.folder;

			return args.folder;
		},

		/**
		 * Set the frame's URL
		 *
		 * @param {String} url
         */
		setFrameUrl: function (url) {
			if (url !== null) {
				this.frameurl = url;
			}

			this.frame.location.href = this.frameurl;
		},
	};

	/**
	 * Convert a query string to an object
	 *
	 * @param {String} string A query string (no leading ?)
	 * @returns {Object}
	 */
	function getQueryObject(string) {
		var rs = {};

		string = string || '';

		jQuery.each(string.split(/[&;]/),
			function (key, val) {
				var keys = val.split('=');

				rs[decodeURIComponent(keys[0])] = keys.length == 2 ? decodeURIComponent(keys[1]) : null;
			});

		return rs;
	}

	/**
	 * Break a url into its component parts
	 *
	 * @param {String} url URL to process
	 * @returns {Object}
	 */
	function getUriObject(url) {
		var bitsAssociate = {},
			bits = url.match(/^(?:([^:\/?#.]+):)?(?:\/\/)?(([^:\/?#]*)(?::(\d*))?)((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[\?#]|$)))*\/?)?([^?#\/]*))?(?:\?([^#]*))?(?:#(.*))?/);

		jQuery.each(['uri', 'scheme', 'authority', 'domain', 'port', 'path', 'directory', 'file', 'query', 'fragment'],
			function (key, index) {
				bitsAssociate[index] = ( !!bits && !!bits[key] ) ? bits[key] : '';
			});

		return bitsAssociate;
	}

	/**
	 * Build a url from component parts
	 *
	 * @param {Object} object Such as the return value of `getUriObject()`
	 *
	 * @returns {String}
	 */
	function buildUri(object) {
		return object.scheme + '://' + object.domain +
			(object.port ? ':' + object.port : '') +
			(object.path ? object.path : '/') +
			(object.query ? '?' + object.query : '') +
			(object.fragment ? '#' + object.fragment : '');
	}

	jQuery(function () {
		// Added to populate data on iframe load
		PodcastMediaManager.initialize();

		document.updateUploader = function () {
			PodcastMediaManager.onloadframe();
		};

		PodcastMediaManager.onloadframe();
	});
}(jQuery, window);
