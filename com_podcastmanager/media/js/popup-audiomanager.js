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

if (typeof jQuery === 'undefined') {
    throw new Error('AudioManager JavaScript requires jQuery')
}

!function (jQuery, document, window) {
    'use strict';

    window.AudioManager = {
        /**
         * Basic setup
         */
        initialize: function () {
            var o = this.getUriObject(window.self.location.href),
                q = this.getQueryObject(o.query);

            this.editor = decodeURIComponent(q.e_name);

            // Setup audio listing objects
            this.folderlist = document.getElementById('folderlist');
            this.frame = window.frames.audioframe;
            this.frameurl = this.frame.location.href;

            // Setup audio listing frame
            jQuery('#audioframe').on('load', function () {
                AudioManager.onloadaudioview();
            });

            // Setup folder up button
            jQuery('#upbutton').off('click').on('click', function () {
                AudioManager.upFolder();
            });
        },

        /**
         * Called when the iframe is reloaded and updates the form action with the correct folder
         */
        onloadaudioview: function () {
            var folder = this.getAudioFolder(),
                $form = jQuery('#uploadForm'),
                portString = '';

            // Update the frame url
            this.frameurl = this.frame.location.href;
            this.setFolder(folder);

            var a = this.getUriObject($form.attr('action')),
                q = this.getQueryObject(a.query);

            q.folder = folder;
            a.query = jQuery.param(q);

            if (typeof (a.port) !== 'undefined' && a.port != 80) {
                portString = ':' + a.port;
            }

            $form.attr('action', a.scheme + '://' + a.domain + portString + a.path + '?' + a.query);
        },

        /**
         * Get the current directory based on the query string of the iframe
         *
         * @returns {String}
         */
        getAudioFolder: function () {
            return this.getQueryObject(this.frame.location.search.substring(1)).folder;
        },

        /**
         * Called when the directory selector is used.
         *
         * @param {String} folder The folder to switch to
         * @param {Number} asset  Probably an integer or undefined, optional
         * @param {Number} author Probably an integer or undefined, optional
         */
        setFolder: function (folder, asset, author) {
            for (var i = 0, l = this.folderlist.length; i < l; i++) {
                if (folder == this.folderlist.options[i].value) {
                    this.folderlist.selectedIndex = i;

                    jQuery(this.folderlist)
                        .trigger('liszt:updated') // Mootools
                        .trigger('chosen:updated'); // jQuery

                    break;
                }
            }

            if (!!asset || !!author) {
                this.setFrameUrl(folder, asset, author);
            }
        },

        /**
         * Move up one directory
         *
         * @return  void
         */
        upFolder: function () {
            var path = this.folderlist.value.split('/'),
                search;

            path.pop();
            search = path.join('/');

            this.setFolder(search);
            this.setFrameUrl(search);
        },

        /**
         * Called when a file is selected
         *
         * @param {String} file Relative path to the file.
         */
        populateFields: function (file) {
            jQuery('#f_url').val(audio_base_path + file);
        },

        /**
         * Not used.
         * Should display messages. There are none.
         *
         * @param {String} text The message text
         */
        showMessage: function (text) {
            var $message = jQuery('#message');

            $message.find('>:first-child').remove();
            $message.append(text);
            jQuery('#messages').css('display', 'block');
        },

        /**
         * Not used.
         * Refreshes the iframe
         *
         * @return  void
         */
        refreshFrame: function () {
            this.frame.location.href = this.frameurl;
        },

        /**
         * Sets the iframe URL, loading a new page. Usually for changing directory.
         *
         * @param {String} folder Relative path to directory
         * @param {Number} asset  Probably an integer or undefined, optional
         * @param {Number} author Probably an integer or undefined, optional
         */
        setFrameUrl: function (folder, asset, author) {
            var qs = {
                option: 'com_podcastmedia',
                view: 'audiolist',
                tmpl: 'component',
                asset: asset,
                author: author
            };

            // Don't run folder through params because / will end up double encoded.
            this.frameurl = 'index.php?' + jQuery.param(qs) + '&folder=' + folder;
            this.frame.location.href = this.frameurl;
        },

        /**
         * Convert a query string to an object
         *
         * @param {String} q A query string (no leading ?)
         * @returns {Object}
         */
        getQueryObject: function (q) {
            var rs = {};

            jQuery.each((q || '').split(/[&;]/), function (key, val) {
                var keys = val.split('=');

                rs[keys[0]] = keys.length == 2 ? keys[1] : null;
            });

            return rs;
        },

        /**
         * Break a URL into its component parts
         *
         * @param {String} u URL
         * @return {Object}
         */
        getUriObject: function (u) {
            var bitsAssociate = {},
                bits = u.match(/^(?:([^:\/?#.]+):)?(?:\/\/)?(([^:\/?#]*)(?::(\d*))?)((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[\?#]|$)))*\/?)?([^?#\/]*))?(?:\?([^#]*))?(?:#(.*))?/);

            jQuery.each(['uri', 'scheme', 'authority', 'domain', 'port', 'path', 'directory', 'file', 'query', 'fragment'], function (key, index) {
                bitsAssociate[index] = (!!bits && !!bits[key]) ? bits[key] : '';
            });

            return bitsAssociate;
        }
    }

    jQuery(function () {
        AudioManager.initialize();
    });
}(jQuery, document, window);
