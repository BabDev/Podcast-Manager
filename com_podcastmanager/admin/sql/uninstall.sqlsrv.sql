IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_asset_id]') AND type = 'D')
ALTER TABLE [#__podcastmanager] DROP CONSTRAINT [DF_#__podcastmanager_asset_id];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_title]') AND type = 'D')
ALTER TABLE [#__podcastmanager] DROP CONSTRAINT [DF_#__podcastmanager_title];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_published]') AND type = 'D')
ALTER TABLE [#__podcastmanager] DROP CONSTRAINT [DF_#__podcastmanager_published];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_created]') AND type = 'D')
ALTER TABLE [#__podcastmanager] DROP CONSTRAINT [DF_#__podcastmanager_created];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_created_by]') AND type = 'D')
ALTER TABLE [#__podcastmanager] DROP CONSTRAINT [DF_#__podcastmanager_created_by];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_modified]') AND type = 'D')
ALTER TABLE [#__podcastmanager] DROP CONSTRAINT [DF_#__podcastmanager_modified];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_modified_by]') AND type = 'D')
ALTER TABLE [#__podcastmanager] DROP CONSTRAINT [DF_#__podcastmanager_modified_by];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_checked_out]') AND type = 'D')
ALTER TABLE [#__podcastmanager] DROP CONSTRAINT [DF_#__podcastmanager_checked_out];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_checked_out_time]') AND type = 'D')
ALTER TABLE [#__podcastmanager] DROP CONSTRAINT [DF_#__podcastmanager_checked_out_time];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_publish_up]') AND type = 'D')
ALTER TABLE [#__podcastmanager] DROP CONSTRAINT [DF_#__podcastmanager_publish_up];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_itAuthor]') AND type = 'D')
ALTER TABLE [#__podcastmanager] DROP CONSTRAINT [DF_#__podcastmanager_itAuthor];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_itBlock]') AND type = 'D')
ALTER TABLE [#__podcastmanager] DROP CONSTRAINT [DF_#__podcastmanager_itBlock];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_itDuration]') AND type = 'D')
ALTER TABLE [#__podcastmanager] DROP CONSTRAINT [DF_#__podcastmanager_itDuration];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_itExplicit]') AND type = 'D')
ALTER TABLE [#__podcastmanager] DROP CONSTRAINT [DF_#__podcastmanager_itExplicit];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_itImage]') AND type = 'D')
ALTER TABLE [#__podcastmanager] DROP CONSTRAINT [DF_#__podcastmanager_itImage];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_itKeywords]') AND type = 'D')
ALTER TABLE [#__podcastmanager] DROP CONSTRAINT [DF_#__podcastmanager_itKeywords];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_itSubtitle]') AND type = 'D')
ALTER TABLE [#__podcastmanager] DROP CONSTRAINT [DF_#__podcastmanager_itSubtitle];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_itSummary]') AND type = 'D')
ALTER TABLE [#__podcastmanager] DROP CONSTRAINT [DF_#__podcastmanager_itSummary];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_mime]') AND type = 'D')
ALTER TABLE [#__podcastmanager] DROP CONSTRAINT [DF_#__podcastmanager_mime];

IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__podcastmanager]') AND type in (N'U'))
DROP TABLE [#__podcastmanager];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_feeds_asset_id]') AND type = 'D')
ALTER TABLE [#__podcastmanager_feeds] DROP CONSTRAINT [DF_#__podcastmanager_feeds_asset_id];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_feeds_name]') AND type = 'D')
ALTER TABLE [#__podcastmanager_feeds] DROP CONSTRAINT [DF_#__podcastmanager_feeds_name];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_feeds_subtitle]') AND type = 'D')
ALTER TABLE [#__podcastmanager_feeds] DROP CONSTRAINT [DF_#__podcastmanager_feeds_subtitle];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_feeds_description]') AND type = 'D')
ALTER TABLE [#__podcastmanager_feeds] DROP CONSTRAINT [DF_#__podcastmanager_feeds_description];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_feeds_copyright]') AND type = 'D')
ALTER TABLE [#__podcastmanager_feeds] DROP CONSTRAINT [DF_#__podcastmanager_feeds_copyright];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_feeds_explicit]') AND type = 'D')
ALTER TABLE [#__podcastmanager_feeds] DROP CONSTRAINT [DF_#__podcastmanager_feeds_explicit];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_feeds_block]') AND type = 'D')
ALTER TABLE [#__podcastmanager_feeds] DROP CONSTRAINT [DF_#__podcastmanager_feeds_block];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_feeds_ownername]') AND type = 'D')
ALTER TABLE [#__podcastmanager_feeds] DROP CONSTRAINT [DF_#__podcastmanager_feeds_ownername];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_feeds_owneremail]') AND type = 'D')
ALTER TABLE [#__podcastmanager_feeds] DROP CONSTRAINT [DF_#__podcastmanager_feeds_owneremail];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_feeds_keywords]') AND type = 'D')
ALTER TABLE [#__podcastmanager_feeds] DROP CONSTRAINT [DF_#__podcastmanager_feeds_keywords];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_feeds_author]') AND type = 'D')
ALTER TABLE [#__podcastmanager_feeds] DROP CONSTRAINT [DF_#__podcastmanager_feeds_author];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_feeds_newFeed]') AND type = 'D')
ALTER TABLE [#__podcastmanager_feeds] DROP CONSTRAINT [DF_#__podcastmanager_feeds_newFeed];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_feeds_image]') AND type = 'D')
ALTER TABLE [#__podcastmanager_feeds] DROP CONSTRAINT [DF_#__podcastmanager_feeds_image];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_feeds_category1]') AND type = 'D')
ALTER TABLE [#__podcastmanager_feeds] DROP CONSTRAINT [DF_#__podcastmanager_feeds_category1];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_feeds_category2]') AND type = 'D')
ALTER TABLE [#__podcastmanager_feeds] DROP CONSTRAINT [DF_#__podcastmanager_feeds_category2];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_feeds_category3]') AND type = 'D')
ALTER TABLE [#__podcastmanager_feeds] DROP CONSTRAINT [DF_#__podcastmanager_feeds_category3];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_feeds_published]') AND type = 'D')
ALTER TABLE [#__podcastmanager_feeds] DROP CONSTRAINT [DF_#__podcastmanager_feeds_published];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_feeds_created]') AND type = 'D')
ALTER TABLE [#__podcastmanager_feeds] DROP CONSTRAINT [DF_#__podcastmanager_feeds_created];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_feeds_created_by]') AND type = 'D')
ALTER TABLE [#__podcastmanager_feeds] DROP CONSTRAINT [DF_#__podcastmanager_feeds_created_by];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_feeds_modified]') AND type = 'D')
ALTER TABLE [#__podcastmanager_feeds] DROP CONSTRAINT [DF_#__podcastmanager_feeds_modified];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_feeds_modified_by]') AND type = 'D')
ALTER TABLE [#__podcastmanager_feeds] DROP CONSTRAINT [DF_#__podcastmanager_feeds_modified_by];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_feeds_checked_out]') AND type = 'D')
ALTER TABLE [#__podcastmanager_feeds] DROP CONSTRAINT [DF_#__podcastmanager_feeds_checked_out];

IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[DF_#__podcastmanager_feeds_checked_out_time]') AND type = 'D')
ALTER TABLE [#__podcastmanager_feeds] DROP CONSTRAINT [DF_#__podcastmanager_feeds_checked_out_time];

IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__podcastmanager_feeds]') AND type in (N'U'))
DROP TABLE [#__podcastmanager_feeds];
