--
-- Drop metadata columns
--

ALTER TABLE `#__podcastmanager` DROP `metadata`;
ALTER TABLE `#__podcastmanager_feeds` DROP `metadata`;

--
-- Alter tables for InnoDB engine support
--

ALTER TABLE `#__podcastmanager` ENGINE=InnoDB;
ALTER TABLE `#__podcastmanager_feeds` ENGINE=InnoDB;

--
-- Alter tables for UTF8MB4 support
--
-- Step 1: Enlarge columns to avoid data loss on later conversion to utf8mb4
--

ALTER TABLE `#__podcastmanager` MODIFY `alias` varchar(400) NOT NULL DEFAULT '';
ALTER TABLE `#__podcastmanager_feeds` MODIFY `alias` varchar(400) NOT NULL DEFAULT '';

--
-- Step 2: Convert all tables to utf8mb4 chracter set with utf8mb4_unicode_ci collation
-- Note: The MySQL database drivers will change utf8mb4 to utf8 if utf8mb4 is not supported
--

ALTER TABLE `#__podcastmanager` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__podcastmanager_feeds` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

--
-- Step 3: Set collation to utf8mb4_bin for formerly utf8_bin collated columns
--

ALTER TABLE `#__podcastmanager` MODIFY `alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '';
ALTER TABLE `#__podcastmanager_feeds` MODIFY `alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '';

--
-- Step 4: Set default character set and collation for all tables
--

ALTER TABLE `#__podcastmanager` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__podcastmanager_feeds` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
