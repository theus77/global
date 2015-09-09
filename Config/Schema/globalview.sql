CREATE TABLE gv_wysiwyg (
  id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `key` VARCHAR(255),
  `value` text NOT NULL,
  created DATETIME,
  modified DATETIME,
  UNIQUE KEY `gv_wysiwyg` (`key`)
);
