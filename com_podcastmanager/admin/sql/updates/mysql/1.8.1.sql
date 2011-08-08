ALTER TABLE `#__podcastmanager` DROP `itCategory`;
ALTER TABLE `#__podcastmanager_feeds` ADD `newFeed` varchar(255) NOT NULL default '' AFTER `author`;
