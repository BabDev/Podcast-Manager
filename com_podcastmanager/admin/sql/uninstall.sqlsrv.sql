IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[#__podcastmanager]') AND type in (N'U'))
DROP TABLE [dbo].[#__podcastmanager];

IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[#__podcastmanager_feeds]') AND type in (N'U'))
DROP TABLE [dbo].[#__podcastmanager_feeds];
