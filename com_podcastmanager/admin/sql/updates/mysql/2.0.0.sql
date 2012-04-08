ALTER TABLE `#__podcastmanager` ADD `asset_id` int(11) UNSIGNED NOT NULL default '0' AFTER `id`;
ALTER TABLE `#__podcastmanager` ADD `created_by` integer unsigned NOT NULL default '0' AFTER `created`;
ALTER TABLE `#__podcastmanager` ADD `itImage` varchar(255) NOT NULL default '' AFTER `itExplicit`;
ALTER TABLE `#__podcastmanager` ADD `mime` varchar(20) NOT NULL default '' AFTER `itSummary`;

ALTER TABLE `#__podcastmanager_feeds` ADD `asset_id` int(11) UNSIGNED NOT NULL default '0' AFTER `id`;
ALTER TABLE `#__podcastmanager_feeds` ADD `created_by` integer unsigned NOT NULL default '0' AFTER `created`;
ALTER TABLE `#__podcastmanager_feeds` ADD `boilerplate` varchar(5120) NOT NULL default '' AFTER `description`;
ALTER TABLE `#__podcastmanager_feeds` ADD `bp_position` integer unsigned NOT NULL default '0' AFTER `boilerplate`;
