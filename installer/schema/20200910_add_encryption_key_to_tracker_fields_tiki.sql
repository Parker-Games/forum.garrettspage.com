ALTER TABLE `tiki_tracker_fields`
  ADD `encryptionKeyId` int(11) NULL,
  ADD INDEX `encryptionKeyId` (`encryptionKeyId`);
