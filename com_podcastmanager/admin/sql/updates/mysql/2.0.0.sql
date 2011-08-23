ALTER TABLE `#__podcastmanager_feeds` ADD `asset_id` int(11) UNSIGNED NOT NULL default '0' AFTER `id`;
ALTER TABLE `#__podcastmanager_feeds` ADD `created_by` integer unsigned NOT NULL default '0' AFTER `created`;
