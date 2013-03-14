ALTER TABLE [#__podcastmanager] ADD  [alias] [nvarchar](255) CONSTRAINT [DF_#__podcastmanager_alias]  DEFAULT '' NOT NULL;

ALTER TABLE [#__podcastmanager_feeds] ADD  [alias] [nvarchar](255) CONSTRAINT [DF_#__podcastmanager_feeds_alias]  DEFAULT '' NOT NULL;
