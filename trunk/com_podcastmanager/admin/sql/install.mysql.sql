CREATE TABLE IF NOT EXISTS `#__podcastmanager` (
  `id` int(11) NOT NULL auto_increment,
  `filename` varchar(255) default NULL COMMENT 'Path to the podcast file from the site root',
  `title` varchar(255) NOT NULL default '' COMMENT 'Title of the podcast episode',
  `published` tinyint(1) NOT NULL default '0' COMMENT 'The published state of the podcast episode',
  `itAuthor` varchar(255) NOT NULL default '' COMMENT 'The author of the podcast episode',
  `itBlock` tinyint(1) NOT NULL default '0' COMMENT 'Set if podcast episode is blocked',
  `itCategory` varchar(255) NOT NULL default '' COMMENT 'The iTunes category of the podcast episode',
  `itDuration` varchar(10) NOT NULL default '' COMMENT 'Duration of the podcast episode',
  `itExplicit` tinyint(1) NOT NULL default '0' COMMENT 'Sets clean/explicit tag in iTunes',
  `itKeywords` varchar(255) NOT NULL default '' COMMENT 'Search keywords for the podcast episode',
  `itSubtitle` varchar(255) NOT NULL default '' COMMENT 'Subtitle of the podcast episode',
  `language` char(7) NOT NULL COMMENT 'The language code for the podcast',
  PRIMARY KEY (`id`)
);