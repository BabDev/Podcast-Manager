CREATE TABLE "#__podcastmanager_new" (
  "id" serial NOT NULL,
  "asset_id" bigint DEFAULT 0 NOT NULL,
  "filename" character varying(255) DEFAULT '' NULL,
  "feedname" bigint NOT NULL,
  "title" character varying(255) DEFAULT '' NOT NULL,
  "alias" character varying(255) DEFAULT '' NOT NULL,
  "published" smallint DEFAULT 0 NOT NULL,
  "created" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
  "created_by" bigint DEFAULT 0 NOT NULL,
  "modified" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
  "modified_by" bigint DEFAULT 0 NOT NULL,
  "checked_out" bigint DEFAULT 0 NOT NULL,
  "checked_out_time" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
  "publish_up" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
  "itAuthor" character varying(255) DEFAULT '' NOT NULL,
  "itBlock" smallint DEFAULT 0 NOT NULL,
  "itDuration" character varying(10) DEFAULT '' NOT NULL,
  "itExplicit" smallint DEFAULT 0 NOT NULL,
  "itImage" character varying(255) DEFAULT '' NOT NULL,
  "itKeywords" character varying(255) DEFAULT '' NOT NULL,
  "itSubtitle" character varying(255) DEFAULT '' NOT NULL,
  "itSummary" character varying(5120) DEFAULT '' NOT NULL,
  "mime" character varying(20) DEFAULT '' NOT NULL,
  "language" character varying(7) NOT NULL,
  PRIMARY KEY ("id")
);

INSERT INTO "#__podcastmanager_new" SELECT * FROM "#__podcastmanager";
DROP TABLE "#__podcastmanager" CASCADE;
ALTER TABLE "#__podcastmanager_new" RENAME TO "#__podcastmanager";

CREATE TABLE "#__podcastmanager_feeds_new" (
  "id" serial NOT NULL,
  "asset_id" bigint DEFAULT 0 NOT NULL,
  "name" character varying(255) DEFAULT '' NULL,
  "alias" character varying(255) DEFAULT '' NOT NULL,
  "subtitle" character varying(255) DEFAULT '' NOT NULL,
  "description" character varying(5120) DEFAULT '' NOT NULL,
  "boilerplate" character varying(5120) DEFAULT '' NOT NULL,
  "bp_position" bigint DEFAULT 0 NOT NULL,
  "copyright" character varying(255) DEFAULT '' NOT NULL,
  "explicit" smallint DEFAULT 0 NOT NULL,
  "block" smallint DEFAULT 0 NOT NULL,
  "ownername" character varying(255) DEFAULT '' NOT NULL,
  "owneremail" character varying(255) DEFAULT '' NOT NULL,
  "keywords" character varying(255) DEFAULT '' NOT NULL,
  "author" character varying(255) DEFAULT '' NOT NULL,
  "newFeed" character varying(255) DEFAULT '' NOT NULL,
  "image" character varying(255) DEFAULT '' NOT NULL,
  "category1" character varying(255) DEFAULT '' NOT NULL,
  "category2" character varying(255) DEFAULT '' NOT NULL,
  "category3" character varying(255) DEFAULT '' NOT NULL,
  "published" smallint DEFAULT 0 NOT NULL,
  "created" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
  "created_by" bigint DEFAULT 0 NOT NULL,
  "modified" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
  "modified_by" bigint DEFAULT 0 NOT NULL,
  "checked_out" bigint DEFAULT 0 NOT NULL,
  "checked_out_time" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
  "language" character varying(7) NOT NULL,
  PRIMARY KEY ("id")
);

INSERT INTO "#__podcastmanager_feeds_new" SELECT * FROM "#__podcastmanager_feeds";
DROP TABLE "#__podcastmanager_feeds" CASCADE;
ALTER TABLE "#__podcastmanager_feeds_new" RENAME TO "#__podcastmanager_feeds";
