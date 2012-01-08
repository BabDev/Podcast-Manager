SET QUOTED_IDENTIFIER ON;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__podcastmanager]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__podcastmanager](
	[id] [bigint] NOT NULL,
	[asset_id] [bigint] NOT NULL,
	[filename] [nvarchar](255) NULL,
	[feedname] [bigint] NULL,
	[title] [nvarchar](255) NOT NULL,
	[published] [smallint] NOT NULL,
	[created] [datetime] NOT NULL,
	[created_by] [bigint] NOT NULL,
	[modified] [datetime] NOT NULL,
	[modified_by] [bigint] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime] NOT NULL,
	[publish_up] [datetime] NOT NULL,
	[itAuthor] [nvarchar](255) NOT NULL,
	[itBlock] [smallint] NOT NULL,
	[itDuration] [varchar](10) NOT NULL,
	[itExplicit] [smallint] NOT NULL,
	[itImage] [nvarchar](255) NOT NULL,
	[itKeywords] [nvarchar](255) NOT NULL,
	[itSubtitle] [nvarchar](255) NOT NULL,
	[itSummary] [nvarchar](max) NOT NULL,
	[language] [nchar](7) NOT NULL,
 CONSTRAINT [PK_#__podcastmanager] PRIMARY KEY CLUSTERED
(
	[id] ASC
) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)

ALTER TABLE [dbo].[#__podcastmanager] ADD  CONSTRAINT [DF_#__podcastmanager_asset_id]  DEFAULT ((0)) FOR [asset_id]

ALTER TABLE [dbo].[#__podcastmanager] ADD  CONSTRAINT [DF_#__podcastmanager_title]  DEFAULT ((N'')) FOR [title]

ALTER TABLE [dbo].[#__podcastmanager] ADD  CONSTRAINT [DF_#__podcastmanager_published]  DEFAULT ((0)) FOR [published]

ALTER TABLE [dbo].[#__podcastmanager] ADD  CONSTRAINT [DF_#__podcastmanager_created]  DEFAULT (('1900-01-01 00:00:00')) FOR [created]

ALTER TABLE [dbo].[#__podcastmanager] ADD  CONSTRAINT [DF_#__podcastmanager_created_by]  DEFAULT ((0)) FOR [created_by]

ALTER TABLE [dbo].[#__podcastmanager] ADD  CONSTRAINT [DF_#__podcastmanager_modified]  DEFAULT (('1900-01-01 00:00:00')) FOR [modified]

ALTER TABLE [dbo].[#__podcastmanager] ADD  CONSTRAINT [DF_#__podcastmanager_modified_by]  DEFAULT ((0)) FOR [modified_by]

ALTER TABLE [dbo].[#__podcastmanager] ADD  CONSTRAINT [DF_#__podcastmanager_checked_out]  DEFAULT ((0)) FOR [checked_out]

ALTER TABLE [dbo].[#__podcastmanager] ADD  CONSTRAINT [DF_#__podcastmanager_checked_out_time]  DEFAULT (('1900-01-01 00:00:00')) FOR [checked_out_time]

ALTER TABLE [dbo].[#__podcastmanager] ADD  CONSTRAINT [DF_#__podcastmanager_publish_up]  DEFAULT (('1900-01-01 00:00:00')) FOR [publish_up]

ALTER TABLE [dbo].[#__podcastmanager] ADD  CONSTRAINT [DF_#__podcastmanager_itAuthor]  DEFAULT ((N'')) FOR [itAuthor]

ALTER TABLE [dbo].[#__podcastmanager] ADD  CONSTRAINT [DF_#__podcastmanager_itBlock]  DEFAULT ((0)) FOR [itBlock]

ALTER TABLE [dbo].[#__podcastmanager] ADD  CONSTRAINT [DF_#__podcastmanager_itDuration]  DEFAULT ((N'')) FOR [itDuration]

ALTER TABLE [dbo].[#__podcastmanager] ADD  CONSTRAINT [DF_#__podcastmanager_itExplicit]  DEFAULT ((0)) FOR [itExplicit]

ALTER TABLE [dbo].[#__podcastmanager] ADD  CONSTRAINT [DF_#__podcastmanager_itImage]  DEFAULT ((N'')) FOR [itImage]

ALTER TABLE [dbo].[#__podcastmanager] ADD  CONSTRAINT [DF_#__podcastmanager_itKeywords]  DEFAULT ((N'')) FOR [itKeywords]

ALTER TABLE [dbo].[#__podcastmanager] ADD  CONSTRAINT [DF_#__podcastmanager_itSubtitle]  DEFAULT ((N'')) FOR [itSubtitle]

ALTER TABLE [dbo].[#__podcastmanager] ADD  CONSTRAINT [DF_#__podcastmanager_itSummary]  DEFAULT ((N'')) FOR [itSummary]

END;

SET QUOTED_IDENTIFIER ON;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__podcastmanager_feeds]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__podcastmanager_feeds](
	[id] [bigint] NOT NULL,
	[asset_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NULL,
	[subtitle] [nvarchar](255) NOT NULL,
	[description] [nvarchar](max) NOT NULL,
	[copyright] [nvarchar](255) NOT NULL,
	[explicit] [smallint] NOT NULL,
	[block] [smallint] NOT NULL,
	[ownername] [nvarchar](255) NOT NULL,
	[owneremail] [nvarchar](255) NOT NULL,
	[keywords] [nvarchar](255) NOT NULL,
	[author] [nvarchar](255) NOT NULL,
	[newFeed] [nvarchar](255) NOT NULL,
	[image] [nvarchar](255) NOT NULL,
	[category1] [nvarchar](255) NOT NULL,
	[category2] [nvarchar](255) NOT NULL,
	[category3] [nvarchar](255) NOT NULL,
	[published] [smallint] NOT NULL,
	[created] [datetime] NOT NULL,
	[created_by] [bigint] NOT NULL,
	[modified] [datetime] NOT NULL,
	[modified_by] [bigint] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime] NOT NULL,
	[language] [nchar](7) NOT NULL,
 CONSTRAINT [PK_#__podcastmanager_feeds] PRIMARY KEY CLUSTERED
(
	[id] ASC
) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)


ALTER TABLE [dbo].[#__podcastmanager_feeds] ADD  CONSTRAINT [DF_#__podcastmanager_feeds_asset_id]  DEFAULT ((0)) FOR [asset_id]

ALTER TABLE [dbo].[#__podcastmanager_feeds] ADD  CONSTRAINT [DF_#__podcastmanager_feeds_name]  DEFAULT ((N'')) FOR [name]

ALTER TABLE [dbo].[#__podcastmanager_feeds] ADD  CONSTRAINT [DF_#__podcastmanager_feeds_subtitle]  DEFAULT ((N'')) FOR [subtitle]

ALTER TABLE [dbo].[#__podcastmanager_feeds] ADD  CONSTRAINT [DF_#__podcastmanager_feeds_description]  DEFAULT ((N'')) FOR [description]

ALTER TABLE [dbo].[#__podcastmanager_feeds] ADD  CONSTRAINT [DF_#__podcastmanager_feeds_copyright]  DEFAULT ((N'')) FOR [copyright]

ALTER TABLE [dbo].[#__podcastmanager_feeds] ADD  CONSTRAINT [DF_#__podcastmanager_feeds_explicit]  DEFAULT ((0)) FOR [explicit]

ALTER TABLE [dbo].[#__podcastmanager_feeds] ADD  CONSTRAINT [DF_#__podcastmanager_feeds_block]  DEFAULT ((0)) FOR [block]

ALTER TABLE [dbo].[#__podcastmanager_feeds] ADD  CONSTRAINT [DF_#__podcastmanager_feeds_ownername]  DEFAULT ((N'')) FOR [ownername]

ALTER TABLE [dbo].[#__podcastmanager_feeds] ADD  CONSTRAINT [DF_#__podcastmanager_feeds_owneremail]  DEFAULT ((N'')) FOR [owneremail]

ALTER TABLE [dbo].[#__podcastmanager_feeds] ADD  CONSTRAINT [DF_#__podcastmanager_feeds_keywords]  DEFAULT ((N'')) FOR [keywords]

ALTER TABLE [dbo].[#__podcastmanager_feeds] ADD  CONSTRAINT [DF_#__podcastmanager_feeds_author]  DEFAULT ((N'')) FOR [author]

ALTER TABLE [dbo].[#__podcastmanager_feeds] ADD  CONSTRAINT [DF_#__podcastmanager_feeds_newFeed]  DEFAULT ((N'')) FOR [newFeed]

ALTER TABLE [dbo].[#__podcastmanager_feeds] ADD  CONSTRAINT [DF_#__podcastmanager_feeds_image]  DEFAULT ((N'')) FOR [image]

ALTER TABLE [dbo].[#__podcastmanager_feeds] ADD  CONSTRAINT [DF_#__podcastmanager_feeds_category1]  DEFAULT ((N'')) FOR [category1]

ALTER TABLE [dbo].[#__podcastmanager_feeds] ADD  CONSTRAINT [DF_#__podcastmanager_feeds_category2]  DEFAULT ((N'')) FOR [category2]

ALTER TABLE [dbo].[#__podcastmanager_feeds] ADD  CONSTRAINT [DF_#__podcastmanager_feeds_category3]  DEFAULT ((N'')) FOR [category3]

ALTER TABLE [dbo].[#__podcastmanager_feeds] ADD  CONSTRAINT [DF_#__podcastmanager_feeds_published]  DEFAULT ((0)) FOR [published]

ALTER TABLE [dbo].[#__podcastmanager_feeds] ADD  CONSTRAINT [DF_#__podcastmanager_feeds_created]  DEFAULT (('1900-01-01 00:00:00')) FOR [created]

ALTER TABLE [dbo].[#__podcastmanager_feeds] ADD  CONSTRAINT [DF_#__podcastmanager_feeds_created_by]  DEFAULT ((0)) FOR [created_by]

ALTER TABLE [dbo].[#__podcastmanager_feeds] ADD  CONSTRAINT [DF_#__podcastmanager_feeds_modified]  DEFAULT (('1900-01-01 00:00:00')) FOR [modified]

ALTER TABLE [dbo].[#__podcastmanager_feeds] ADD  CONSTRAINT [DF_#__podcastmanager_feeds_modified_by]  DEFAULT ((0)) FOR [modified_by]

ALTER TABLE [dbo].[#__podcastmanager_feeds] ADD  CONSTRAINT [DF_#__podcastmanager_feeds_checked_out]  DEFAULT ((0)) FOR [checked_out]

ALTER TABLE [dbo].[#__podcastmanager_feeds] ADD  CONSTRAINT [DF_#__podcastmanager_feeds_checked_out_time]  DEFAULT (('1900-01-01 00:00:00')) FOR [checked_out_time]

END;
