-- ********************************************************
-- *                                                      *
-- * IMPORTANT NOTE                                       *
-- *                                                      *
-- * Do not import this file manually but use the Contao  *
-- * install tool to create and maintain database tables! *
-- *                                                      *
-- ********************************************************

--
-- Table `tl_news4ward_tag`
--

CREATE TABLE `tl_news4ward_tag` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `tag` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table `tl_module`
--

CREATE TABLE `tl_module` (
  `news4ward_tags_count` int(10) unsigned NOT NULL default '0',
  `news4ward_tags_maxsize` int(10) unsigned NOT NULL default '10',
  `news4ward_tags_minsize` int(10) unsigned NOT NULL default '24',
  `news4ward_tags_tresholds` int(10) unsigned NOT NULL default '7',
  `news4ward_tags_unit` char(2) NOT NULL default '',
  `news4ward_tags_shuffle` char(1) NOT NULL default '1'
  `news4ward_tags_random` char(1) NOT NULL default '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
