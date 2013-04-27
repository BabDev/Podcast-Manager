ALTER TABLE `#__podcastmanager` ADD `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' AFTER `title`;
ALTER TABLE `#__podcastmanager` ADD `metadata` text NOT NULL AFTER `language`;

ALTER TABLE `#__podcastmanager_feeds` ADD `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' AFTER `name`;
ALTER TABLE `#__podcastmanager_feeds` ADD `metadata` text NOT NULL AFTER `language`;
