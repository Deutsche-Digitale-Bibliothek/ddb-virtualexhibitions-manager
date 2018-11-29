SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `omeka_exhxxxx-exhibit-number-xxxx_elements`;
CREATE TABLE `omeka_exhxxxx-exhibit-number-xxxx_elements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `element_set_id` int(10) unsigned NOT NULL,
  `order` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `comment` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_element_set_id` (`element_set_id`,`name`),
  UNIQUE KEY `order_element_set_id` (`element_set_id`,`order`),
  KEY `element_set_id` (`element_set_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `omeka_exhxxxx-exhibit-number-xxxx_elements` (`id`, `element_set_id`, `order`, `name`, `description`, `comment`) VALUES
(37,  1,  8,  'Contributor',  'An entity responsible for making contributions to the resource', ''),
(38,  1,  15, 'Coverage', 'The spatial or temporal topic of the resource, the spatial applicability of the resource, or the jurisdiction under which the resource is relevant', ''),
(39,  1,  4,  'Creator',  'An entity primarily responsible for making the resource',  ''),
(40,  1,  7,  'Date', 'A point or period of time associated with an event in the lifecycle of the resource',  ''),
(41,  1,  3,  'Description',  'An account of the resource', ''),
(42,  1,  11, 'Format', 'The file format, physical medium, or dimensions of the resource',  ''),
(43,  1,  14, 'Identifier', 'An unambiguous reference to the resource within a given context',  ''),
(44,  1,  12, 'Language', 'A language of the resource', ''),
(45,  1,  6,  'Publisher',  'An entity responsible for making the resource available',  ''),
(46,  1,  10, 'Relation', 'A related resource', ''),
(47,  1,  9,  'Rights', 'Information about rights held in and over the resource', ''),
(48,  1,  5,  'Source', 'A related resource from which the described resource is derived',  ''),
(49,  1,  2,  'Subject',  'The topic of the resource',  ''),
(50,  1,  1,  'Title',  'A name given to the resource', ''),
(51,  1,  13, 'Type', 'The nature or genre of the resource',  ''),
(52,  3,  1,  'Titel',  'Beschreibung Metadatenfeld Titel', 'Zusatzvermerk Metadatenfeld Titel'),
(54,  3,  17, 'Material/Technik', 'Beschreibung Metadatenfeld Material/Technik',  'Zusatzvermerk Metadatenfeld Material/Technik'),
(55,  3,  16, 'Maße/Umfang',  'Beschreibung Metadatenfeld Maße/Umfang', 'Zusatzvermerk Metadatenfeld Maße/Umfang'),
(56,  3,  15, 'Ort',  'Beschreibung Metadatenfeld Ort', 'Zusatzvermerk Metadatenfeld Ort'),
(57,  3,  19, 'Identifikator',  'Beschreibung Metadatenfeld Identifikator', 'Zusatzvermerk Metadatenfeld Identifikator'),
(59,  3,  3,  'Beschreibung', 'Beschreibung Metadatenfeld Beschreibung',  'Zusatzvermerk Metadatenfeld Beschreibung'),
(64,  3,  10,  'Typ',  'Beschreibung Metadatenfeld Typ', 'Zusatzvermerk Metadatenfeld Typ'),
(65,  3,  12, 'Thema',  'Beschreibung Metadatenfeld Thema', 'Zusatzvermerk Metadatenfeld Thema'),
(67,  3,  14, 'Zeit', 'Beschreibung Metadatenfeld Zeit',  'Zusatzvermerk Metadatenfeld Zeit'),
(71,  3,  18, 'Sprache',  'Beschreibung Metadatenfeld Sprache', 'Zusatzvermerk Metadatenfeld Sprache'),
(72,  3,  9, 'Rechtsstatus', 'Beschreibung Metadatenfeld Rechtsstatus',  'Zusatzvermerk Metadatenfeld Rechtsstatus'),
(74,  3,  22, 'Videoquelle',  'Beschreibung Metadatenfeld Videoquelle', 'Zusatzvermerk Metadatenfeld Videoquelle'),
(75,  3,  2,  'Weiterer Titel', 'Beschreibung Metadatenfeld Weiterer Titel',  'Zusatzvermerk Metadatenfeld Weiterer Titel'),
(76,  3,  5,  'Name der Institution', 'Beschreibung Metadatenfeld Name der Institution',  'Zusatzvermerk Metadatenfeld Name der Institution'),
(77,  3,  7,  'Link zum Objekt in der DDB', 'Beschreibung Metadatenfeld Link zum Objekt in der DDB',  'Zusatzvermerk Metadatenfeld Link zum Objekt in der DDB'),
(78,  3,  8,  'Link zum Objekt bei der datengebenden Institution',  'Beschreibung Metadatenfeld Link zum Objekt bei der datengebenden Institution', 'Zusatzvermerk Metadatenfeld Link zum Objekt bei der datengebenden Institution'),
(79,  3,  11,  'Teil von', 'Beschreibung Metadatenfeld Teil von',  'Zusatzvermerk Metadatenfeld Teil von'),
(80,  3,  4,  'Kurzbeschreibung', 'Beschreibung Metadatenfeld Kurzbeschreibung',  'Zusatzvermerk Metadatenfeld Kurzbeschreibung'),
(81,  3,  13, 'Beteiligte Personen und Organisationen', 'Beschreibung Metadatenfeld Beteiligte Personen und Organisationen',  'Zusatzvermerk Metadatenfeld Beteiligte Personen und Organisationen'),
(82,  3,  20, 'Anmerkungen',  'Beschreibung Metadatenfeld Anmerkungen', 'Zusatzvermerk Metadatenfeld Anmerkungen'),
(83,  3,  21, 'Förderung',  'Beschreibung Metadatenfeld Förderung', 'Zusatzvermerk Metadatenfeld Förderung'),
(84,  3,  23, 'Imagemap', 'Beschreibung Metadatenfeld Imagemap',  'Zusatzvermerk Metadatenfeld Imagemap'),
(85,  3,  6, 'URL der Institution',  'Beschreibung Metadatenfeld URL der Institution', 'Zusatzvermerk Metadatenfeld URL der Institution');

DROP TABLE IF EXISTS `omeka_exhxxxx-exhibit-number-xxxx_collections`;
CREATE TABLE `omeka_exhxxxx-exhibit-number-xxxx_collections` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `public` tinyint(4) NOT NULL,
  `featured` tinyint(4) NOT NULL,
  `added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `owner_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `public` (`public`),
  KEY `featured` (`featured`),
  KEY `owner_id` (`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `omeka_exhxxxx-exhibit-number-xxxx_element_sets`;
CREATE TABLE `omeka_exhxxxx-exhibit-number-xxxx_element_sets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `record_type` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `record_type` (`record_type`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `omeka_exhxxxx-exhibit-number-xxxx_element_sets` (`id`, `record_type`, `name`, `description`) VALUES
(1, NULL, 'Dublin Core',  'The Dublin Core metadata element set is common to all Omeka records, including items, files, and collections. For more information see, http://dublincore.org/documents/dces/.'),
(3, 'Item', 'Item Type Metadata', 'The item type metadata element set, consisting of all item type elements bundled with Omeka and all item type elements created by an administrator.');

DROP TABLE IF EXISTS `omeka_exhxxxx-exhibit-number-xxxx_element_texts`;
CREATE TABLE `omeka_exhxxxx-exhibit-number-xxxx_element_texts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `record_id` int(10) unsigned NOT NULL,
  `record_type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `element_id` int(10) unsigned NOT NULL,
  `html` tinyint(4) NOT NULL,
  `text` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `record_type_record_id` (`record_type`,`record_id`),
  KEY `element_id` (`element_id`),
  KEY `text` (`text`(20))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `omeka_exhxxxx-exhibit-number-xxxx_exhibits`;
CREATE TABLE `omeka_exhxxxx-exhibit-number-xxxx_exhibits` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `credits` text COLLATE utf8_unicode_ci,
  `featured` tinyint(1) DEFAULT '0',
  `public` tinyint(1) DEFAULT '0',
  `theme` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `theme_options` text COLLATE utf8_unicode_ci,
  `slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `owner_id` int(10) unsigned DEFAULT NULL,
  `banner` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'optional exhibit banner file path',
  `widget` text COLLATE utf8_unicode_ci COMMENT 'widget content',
  `cover` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'optional exhibit banner cover',
  `widget_top_first` text COLLATE utf8_unicode_ci COMMENT 'widget top first content',
  `widget_top_second` text COLLATE utf8_unicode_ci COMMENT 'widget top second content',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `public` (`public`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `omeka_exhxxxx-exhibit-number-xxxx_exhibits` (`id`, `title`, `description`, `credits`, `featured`, `public`, `theme`, `theme_options`, `slug`, `added`, `modified`, `owner_id`, `banner`, `widget`, `cover`, `widget_top_first`, `widget_top_second`) VALUES
(1, 'xxxx-exhibit-title-xxxx', 'xxxx-exhibit-description-xxxx', '', 0, 1, 'ddb', NULL, 'xxxx-exhibit-slug-xxxx', '2014-10-02 09:18:26', '2014-10-02 09:20:26', 1, NULL, '', NULL, '', '');

DROP TABLE IF EXISTS `omeka_exhxxxx-exhibit-number-xxxx_exhibit_pages`;
CREATE TABLE `omeka_exhxxxx-exhibit-number-xxxx_exhibit_pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `exhibit_id` int(10) unsigned NOT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `layout` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `order` tinyint(3) unsigned NOT NULL,
  `widget` text COLLATE utf8_unicode_ci COMMENT 'widget content',
  `pagethumbnail` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'page navigation thumbnail',
  PRIMARY KEY (`id`),
  UNIQUE KEY `exhibit_id_parent_id_slug` (`exhibit_id`,`parent_id`,`slug`),
  KEY `exhibit_id_order` (`exhibit_id`,`order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `omeka_exhxxxx-exhibit-number-xxxx_exhibit_pages` (`id`, `exhibit_id`, `parent_id`, `title`, `slug`, `layout`, `order`, `widget`, `pagethumbnail`) VALUES
(38,  1,  NULL, 'Inhalt der Ausstellung', 'inhalt-der-ausstellung',  'ddb-summary',  1, '', NULL);

DROP TABLE IF EXISTS `omeka_exhxxxx-exhibit-number-xxxx_exhibit_page_entries`;
CREATE TABLE `omeka_exhxxxx-exhibit-number-xxxx_exhibit_page_entries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned DEFAULT NULL,
  `file_id` int(10) unsigned DEFAULT NULL,
  `page_id` int(10) unsigned NOT NULL,
  `text` text COLLATE utf8_unicode_ci,
  `caption` text COLLATE utf8_unicode_ci,
  `order` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_id_order` (`page_id`,`order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `omeka_exhxxxx-exhibit-number-xxxx_files`;
CREATE TABLE `omeka_exhxxxx-exhibit-number-xxxx_files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL,
  `order` int(10) unsigned DEFAULT NULL,
  `size` bigint(20) unsigned NOT NULL,
  `has_derivative_image` tinyint(1) NOT NULL,
  `authentication` char(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mime_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type_os` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `filename` text COLLATE utf8_unicode_ci NOT NULL,
  `original_filename` text COLLATE utf8_unicode_ci NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `stored` tinyint(1) NOT NULL DEFAULT '0',
  `metadata` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `omeka_exhxxxx-exhibit-number-xxxx_items`;
CREATE TABLE `omeka_exhxxxx-exhibit-number-xxxx_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_type_id` int(10) unsigned DEFAULT NULL,
  `collection_id` int(10) unsigned DEFAULT NULL,
  `featured` tinyint(4) NOT NULL,
  `public` tinyint(4) NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `owner_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_type_id` (`item_type_id`),
  KEY `collection_id` (`collection_id`),
  KEY `public` (`public`),
  KEY `featured` (`featured`),
  KEY `owner_id` (`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `omeka_exhxxxx-exhibit-number-xxxx_item_types`;
CREATE TABLE `omeka_exhxxxx-exhibit-number-xxxx_item_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `omeka_exhxxxx-exhibit-number-xxxx_item_types` (`id`, `name`, `description`) VALUES
(18,  'VA DDB', 'Objekttyp für Virtuelle Ausstellungen der Deutschen Digitalen Bibliothek');

DROP TABLE IF EXISTS `omeka_exhxxxx-exhibit-number-xxxx_item_types_elements`;
CREATE TABLE `omeka_exhxxxx-exhibit-number-xxxx_item_types_elements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_type_id` int(10) unsigned NOT NULL,
  `element_id` int(10) unsigned NOT NULL,
  `order` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `item_type_id_element_id` (`item_type_id`,`element_id`),
  KEY `item_type_id` (`item_type_id`),
  KEY `element_id` (`element_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `omeka_exhxxxx-exhibit-number-xxxx_item_types_elements` (`id`, `item_type_id`, `element_id`, `order`) VALUES
(48,  18, 52, 1),
(50,  18, 54, 17),
(51,  18, 55, 16),
(52,  18, 56, 15),
(53,  18, 57, 19),
(55,  18, 59, 3),
(75,  18, 64, 10),
(76,  18, 65, 12),
(78,  18, 67, 14),
(82,  18, 71, 18),
(85,  18, 74, 22),
(86,  18, 75, 2),
(87,  18, 76, 5),
(88,  18, 77, 7),
(89,  18, 78, 8),
(90,  18, 79, 11),
(91,  18, 80, 4),
(92,  18, 81, 13),
(93,  18, 82, 20),
(94,  18, 83, 21),
(95,  18, 72, 9),
(96,  18, 84, 23),
(98,  18, 85, 6);

DROP TABLE IF EXISTS `omeka_exhxxxx-exhibit-number-xxxx_keys`;
CREATE TABLE `omeka_exhxxxx-exhibit-number-xxxx_keys` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `label` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `key` char(40) COLLATE utf8_unicode_ci NOT NULL,
  `ip` varbinary(16) DEFAULT NULL,
  `accessed` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `omeka_exhxxxx-exhibit-number-xxxx_options`;
CREATE TABLE `omeka_exhxxxx-exhibit-number-xxxx_options` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `omeka_exhxxxx-exhibit-number-xxxx_options` (`id`, `name`, `value`) VALUES
(14,  'admin_theme',  'default'),
(20,  'display_system_info',  '1'),
(27,  'api_enable', ''),
(28,  'api_per_page', '50'),
(30,  'exhibit_builder_sort_browse',  'alpha'),
(69,  'public_theme', 'ddb'),
(89,  'fullsize_constraint',  '1920'),
(90,  'thumbnail_constraint', '360'),
(91,  'square_thumbnail_constraint',  '360'),
(92,  'per_page_admin', '150'),
(93,  'per_page_public',  '10'),
(94,  'show_empty_elements',  '0'),
(95,  'search_record_types',  'a:3:{i:0;s:4:\"Item\";i:1;s:4:\"File\";i:2;s:11:\"ExhibitPage\";}'),
(124, 'bookreader_custom_css',  '/plugins/BookReader/views/shared/css/BookReaderCustom.css'),
(125, 'bookreader_favicon_url', '/admin/themes/ddb/images/favicon.ico'),
(127, 'bookreader_sorting_mode',  ''),
(128, 'bookreader_mode_page', '1'),
(129, 'bookreader_embed_functions', '0'),
(130, 'bookreader_class', 'ddb-bookreader'),
(131, 'bookreader_width', '100%'),
(132, 'bookreader_height',  '480'),
(158, 'disable_default_file_validation',  '1'),
(159, 'file_extension_whitelist', 'aac,aif,aiff,asf,asx,avi,bmp,c,cc,class,css,divx,doc,docx,exe,gif,gz,gzip,h,ico,j2k,jp2,jpe,jpeg,jpg,m4a,mdb,mid,midi,mov,mp2,mp3,mp4,mpa,mpe,mpeg,mpg,mpp,odb,odc,odf,odg,odp,ods,odt,ogg, pdf,png,pot,pps,ppt,pptx,qt,ra,ram,rtf,rtx,swf,tar,tif,tiff,txt, wav,wax,wma,wmv,wmx,wri,xla,xls,xlsx,xlt,xlw,zip'),
(160, 'file_mime_type_whitelist', 'application/msword,application/ogg,application/pdf,application/rtf,application/vnd.ms-access,application/vnd.ms-excel,application/vnd.ms-powerpoint,application/vnd.ms-project,application/vnd.ms-write,application/vnd.oasis.opendocument.chart,application/vnd.oasis.opendocument.database,application/vnd.oasis.opendocument.formula,application/vnd.oasis.opendocument.graphics,application/vnd.oasis.opendocument.presentation,application/vnd.oasis.opendocument.spreadsheet,application/vnd.oasis.opendocument.text,application/x-ms-wmp,application/x-ogg,application/x-gzip,application/x-msdownload,application/x-shockwave-flash,application/x-tar,application/zip,audio/aac,audio/aiff,audio/mid,audio/midi,audio/mp3,audio/mp4,audio/mpeg,audio/mpeg3,audio/ogg,audio/wav,audio/wma,audio/x-aac,audio/x-aiff,audio/x-midi,audio/x-mp3,audio/x-mp4,audio/x-mpeg,audio/x-mpeg3,audio/x-mpegaudio,audio/x-ms-wax,audio/x-realaudio,audio/x-wav,audio/x-wma,image/bmp,image/gif,image/icon,image/jpeg,image/pjpeg,image/png,image/tiff,image/x-icon,image/x-ms-bmp,text/css,text/plain,text/richtext,text/rtf,video/asf,video/avi,video/divx,video/mp4,video/mpeg,video/msvideo,video/ogg,video/quicktime,video/x-ms-wmv,video/x-msvideo'),
(161, 'recaptcha_public_key', ''),
(162, 'recaptcha_private_key',  ''),
(163, 'html_purifier_is_enabled', '1'),
(164, 'html_purifier_allowed_html_elements',  'p,br,strong,em,span,div,ul,ol,li,a,h1,h2,h3,h4,h5,h6,address,pre,table,tr,td,blockquote,thead,tfoot,tbody,th,dl,dt,dd,q,small,strike,sup,sub,b,i,big,small,tt,cite'),
(165, 'html_purifier_allowed_html_attributes',  '*.style,*.class,a.href,a.title,a.target'),
(176, 'bookreader_custom_library',  '/homepages/23/d27028830/htdocs/omeka-25-multisite/lib/omeka/plugins/BookReader/libraries/BookReaderCustom.php'),
(177, 'show_element_set_headings',  '1'),
(178, 'use_square_thumbnail', '1'),
(179, 'omeka_version',  '2.6.1'),
(191, 'theme_ddb_options',  'a:11:{s:4:\"logo\";N;s:17:\"header_background\";N;s:18:\"header_title_color\";s:6:\"000000\";s:21:\"display_featured_item\";s:1:\"0\";s:27:\"display_featured_collection\";s:1:\"0\";s:24:\"display_featured_exhibit\";s:1:\"1\";s:21:\"homepage_recent_items\";s:0:\"\";s:13:\"homepage_text\";s:0:\"\";s:11:\"footer_text\";s:0:\"\";s:24:\"display_footer_copyright\";s:1:\"0\";s:17:\"item_file_gallery\";s:1:\"1\";}'),
(219, 'site_title', 'xxxx-exhibit-title-xxxx'),
(220, 'description',  'DDB Omeka Ausstellungen'),
(221, 'administrator_email',  'service@deutsche-digitale-bibliothek.de'),
(222, 'copyright',  ''),
(223, 'author', ''),
(224, 'tag_delimiter',  ','),
(225, 'path_to_convert',  '/usr/bin/'),
(232, 'simple_vocab_files', '0'),
(247, 'public_navigation_main', '[{\"uid\":\"\\/xxxx-instance-slug-xxxx\\/items\\/browse\",\"can_delete\":false,\"type\":\"Omeka_Navigation_Page_Uri\",\"label\":\"Objekte durchsuchen\",\"fragment\":null,\"id\":null,\"class\":null,\"title\":null,\"target\":null,\"accesskey\":null,\"rel\":[],\"rev\":[],\"customHtmlAttribs\":[],\"order\":1,\"resource\":null,\"privilege\":null,\"active\":false,\"visible\":true,\"pages\":[],\"uri\":\"\\/xxxx-instance-slug-xxxx\\/items\\/browse\"},{\"uid\":\"\\/xxxx-instance-slug-xxxx\\/collections\\/browse\",\"can_delete\":false,\"type\":\"Omeka_Navigation_Page_Uri\",\"label\":\"Sammlungen durchsuchen\",\"fragment\":null,\"id\":null,\"class\":null,\"title\":null,\"target\":null,\"accesskey\":null,\"rel\":[],\"rev\":[],\"customHtmlAttribs\":[],\"order\":2,\"resource\":null,\"privilege\":null,\"active\":false,\"visible\":false,\"pages\":[],\"uri\":\"\\/xxxx-instance-slug-xxxx\\/collections\\/browse\"},{\"uid\":\"\\/xxxx-instance-slug-xxxx\\/exhibits\",\"can_delete\":false,\"type\":\"Omeka_Navigation_Page_Uri\",\"label\":\"Ausstellungen durchst\\u00f6bern\",\"fragment\":null,\"id\":null,\"class\":null,\"title\":null,\"target\":null,\"accesskey\":null,\"rel\":[],\"rev\":[],\"customHtmlAttribs\":[],\"order\":3,\"resource\":null,\"privilege\":null,\"active\":false,\"visible\":true,\"pages\":[],\"uri\":\"\\/xxxx-instance-slug-xxxx\\/exhibits\"},{\"uid\":\"\\/xxxx-instance-slug-xxxx\\/exhibits\\/show\\/xxxx-exhibit-slug-xxxx\",\"can_delete\":true,\"type\":\"Omeka_Navigation_Page_Uri\",\"label\":\"Ausstellung\",\"fragment\":null,\"id\":null,\"class\":null,\"title\":null,\"target\":null,\"accesskey\":null,\"rel\":[],\"rev\":[],\"customHtmlAttribs\":[],\"order\":4,\"resource\":null,\"privilege\":null,\"active\":false,\"visible\":true,\"pages\":[],\"uri\":\"\\/xxxx-instance-slug-xxxx\\/exhibits\\/show\\/xxxx-exhibit-slug-xxxx\"},{\"uid\":\"\\/xxxx-instance-slug-xxxx\\/exhibits\\/show\\/xxxx-exhibit-slug-xxxx\\/inhalt-der-ausstellung\",\"can_delete\":true,\"label\":\"Inhalt\",\"fragment\":null,\"id\":null,\"class\":null,\"title\":null,\"target\":null,\"accesskey\":null,\"rel\":[],\"rev\":[],\"customHtmlAttribs\":[],\"order\":5,\"resource\":null,\"privilege\":null,\"active\":false,\"visible\":true,\"type\":\"Omeka_Navigation_Page_Uri\",\"pages\":[],\"uri\":\"\\/xxxx-instance-slug-xxxx\\/exhibits\\/show\\/xxxx-exhibit-slug-xxxx\\/inhalt-der-ausstellung\"}]'),
(248, 'homepage_uri', '/xxxx-instance-slug-xxxx/exhibits/show/xxxx-exhibit-slug-xxxx/inhalt-der-ausstellung'),
(260, 'gina_admin_mod_dashboard_panel_title', 'Wenn Sie Unterstützung benötigen: '),
(261, 'gina_admin_mod_dashboard_panel_content', '<p><a title=\"Kuratoren-Handbuch online\" href=\"https://deutsche-digitale-bibliothek.github.io/ddb-virtualexhibitions-docs/\" target=\"_blank\">Benutzungs-Handbuch online</a></p>\r\n<h4>Ansprechpersonen:</h4>\r\n<p>Laura Schr&ouml;der<br /><a href=\"mailto:L.Schroeder@dnb.de\">L.Schroeder@dnb.de</a><br />Tel.:&nbsp;<span>+49 69 1525-1793<br /></span></p>\r\n<p>Lisa Landes<br /><a href=\"mailto:L.Landes@dnb.de\" target=\"_self\">L.Landes@dnb.de</a><br />Tel.:&nbsp;<span>+49 69 1525-1797<br /><br /></span></p>'),
(262,'omeka_update','a:2:{s:14:\"latest_version\";s:6:\"2.6.1\n\";s:12:\"last_updated\";i:1541081260;}')
;

DROP TABLE IF EXISTS `omeka_exhxxxx-exhibit-number-xxxx_plugins`;
CREATE TABLE `omeka_exhxxxx-exhibit-number-xxxx_plugins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(4) NOT NULL,
  `version` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `active_idx` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `omeka_exhxxxx-exhibit-number-xxxx_plugins` (`id`, `name`, `active`, `version`) VALUES
(1, 'ExhibitBuilder', 1,  '2.1.1'),
(3, 'BookReader', 1,  '2.3'),
(4, 'X3d',  1,  '1.0.0'),
(5, 'GinaImageConvert', 1,  '1.0.0'),
(6, 'GinaAdminMod', 1,  '1.0.0'),
(7, 'SimpleVocab',  1,  '2.1');

DROP TABLE IF EXISTS `omeka_exhxxxx-exhibit-number-xxxx_processes`;
CREATE TABLE `omeka_exhxxxx-exhibit-number-xxxx_processes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `class` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `pid` int(10) unsigned DEFAULT NULL,
  `status` enum('starting','in progress','completed','paused','error','stopped') COLLATE utf8_unicode_ci NOT NULL,
  `args` text COLLATE utf8_unicode_ci NOT NULL,
  `started` timestamp NOT NULL DEFAULT '1999-12-31 23:00:00',
  `stopped` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `pid` (`pid`),
  KEY `started` (`started`),
  KEY `stopped` (`stopped`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `omeka_exhxxxx-exhibit-number-xxxx_records_tags`;
CREATE TABLE `omeka_exhxxxx-exhibit-number-xxxx_records_tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `record_id` int(10) unsigned NOT NULL,
  `record_type` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `tag_id` int(10) unsigned NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag` (`record_type`,`record_id`,`tag_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `omeka_exhxxxx-exhibit-number-xxxx_schema_migrations`;
CREATE TABLE `omeka_exhxxxx-exhibit-number-xxxx_schema_migrations` (
  `version` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `unique_schema_migrations` (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `omeka_exhxxxx-exhibit-number-xxxx_schema_migrations` (`version`) VALUES
('20100401000000'),('20100810120000'),('20110113000000'),('20110124000001'),('20110301103900'),('20110328192100'),('20110426181300'),('20110601112200'),('20110627223000'),('20110824110000'),('20120112100000'),('20120220000000'),('20120221000000'),('20120224000000'),('20120224000001'),('20120402000000'),('20120516000000'),('20120612112000'),('20120623095000'),('20120710000000'),('20120723000000'),('20120808000000'),('20120808000001'),('20120813000000'),('20120914000000'),('20121007000000'),('20121015000000'),('20121015000001'),('20121018000001'),('20121110000000'),('20121218000000'),('20130422000000'),('20130426000000'),('20130429000000'),('20130701000000'),('20130809000000'),('20140304131700'),('20150211000000'),('20150310141100'),('20150814155100'),('20151118214800'),('20151209103299'),('20151209103300'),('20161209171900'),('20170331084000'),('20170405125800');

DROP TABLE IF EXISTS `omeka_exhxxxx-exhibit-number-xxxx_search_texts`;
CREATE TABLE `omeka_exhxxxx-exhibit-number-xxxx_search_texts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `record_type` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `record_id` int(10) unsigned NOT NULL,
  `public` tinyint(1) NOT NULL,
  `title` mediumtext COLLATE utf8_unicode_ci,
  `text` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `record_name` (`record_type`,`record_id`),
  FULLTEXT KEY `text` (`text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `omeka_exhxxxx-exhibit-number-xxxx_sessions`;
CREATE TABLE `omeka_exhxxxx-exhibit-number-xxxx_sessions` (
  `id` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `modified` bigint(20) DEFAULT NULL,
  `lifetime` int(11) DEFAULT NULL,
  `data` blob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `omeka_exhxxxx-exhibit-number-xxxx_simple_vocab_terms`;
CREATE TABLE `omeka_exhxxxx-exhibit-number-xxxx_simple_vocab_terms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `element_id` int(10) unsigned NOT NULL,
  `terms` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `element_id` (`element_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `omeka_exhxxxx-exhibit-number-xxxx_simple_vocab_terms` (`id`, `element_id`, `terms`) VALUES
(1, 72, '[[license:CC-PD-M1]]|||Public Domain Mark 1.0\n[[license:CC-PD-U1]]|||CC0 1.0 Universell - Public Domain Dedication\n[[license:G-RR-AF]]|||Rechte vorbehalten - Freier Zugang\n[[license:G-RR-AA]]|||Rechte vorbehalten - Zugang nach Autorisierung\n[[license:CC-BY-3.0-DEU]]|||Namensnennung 3.0 Deutschland\n[[license:CC-BY-4.0-INT]]|||Namensnennung 4.0 International\n[[license:CC-BY-SA-3.0-DEU]]|||Namensnennung - Weitergabe unter gleichen Bedingungen 3.0 Deutschland\n[[license:CC-BY-SA-4.0-INT]]|||Namensnennung - Weitergabe unter gleichen Bedingungen 4.0 International\n[[license:CC-BY-ND-3.0-DEU]]|||Namensnennung - Keine Bearbeitung 3.0 Deutschland\n[[license:CC-BY-ND-4.0-INT]]|||Namensnennung - Keine Bearbeitung 4.0 International\n[[license:CC-BY-NC-3.0-DEU]]|||Namensnennung - Nicht kommerziell 3.0 Deutschland\n[[license:CC-BY-NC-4.0-INT]]|||Namensnennung - Nicht kommerziell 4.0 International\n[[license:CC-BY-NC-SA-3.0-DEU]]|||Namensnennung - Nicht kommerziell - Weitergabe unter gleichen Bedingungen 3.0 Deutschland\n[[license:CC-BY-NC-SA-4.0-INT]]|||Namensnennung - Nicht kommerziell - Weitergabe unter gleichen Bedingungen 4.0 International\n[[license:CC-BY-NC-ND-3.0-DEU]]|||Namensnennung - Nicht kommerziell - Keine Bearbeitung 3.0 Deutschland\n[[license:CC-BY-NC-ND-4.0-INT]]|||Namensnennung - Nicht kommerziell - Keine Bearbeitung 4.0 International\n[[license:G-VW]]|||Verwaistes Werk\n[[license:G-NUG-KKN]]|||Nicht urheberrechtlich geschützt - Keine kommerzielle Nachnutzung');

DROP TABLE IF EXISTS `omeka_exhxxxx-exhibit-number-xxxx_tags`;
CREATE TABLE `omeka_exhxxxx-exhibit-number-xxxx_tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `omeka_exhxxxx-exhibit-number-xxxx_users`;
CREATE TABLE `omeka_exhxxxx-exhibit-number-xxxx_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `email` text COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `salt` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(4) NOT NULL,
  `role` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'default',
  `confirm_use` tinyint(4) NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `active_idx` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `omeka_exhxxxx-exhibit-number-xxxx_users_activations`;
CREATE TABLE `omeka_exhxxxx-exhibit-number-xxxx_users_activations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `url` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `omeka_exhxxxx-exhibit-number-xxxx_x3ds`;
CREATE TABLE `omeka_exhxxxx-exhibit-number-xxxx_x3ds` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint(20) unsigned NOT NULL,
  `directory` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `x3d_file` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `texture_file` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `thumbnail` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;