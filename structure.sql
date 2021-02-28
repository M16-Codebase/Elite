-- phpMyAdmin SQL Dump
-- version 3.3.8.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 17, 2015 at 01:30 PM
-- Server version: 5.6.17
-- PHP Version: 5.6.5RC1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `lps`
--

-- --------------------------------------------------------

--
-- Table structure for table `actions`
--

CREATE TABLE IF NOT EXISTS `actions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module_class` varchar(255) NOT NULL,
  `module_url` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `admin` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `module_url` (`module_url`,`action`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE IF NOT EXISTS `banners` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `image_id` int(10) NOT NULL,
  `title` varchar(1024) DEFAULT NULL,
  `description` varchar(1024) DEFAULT NULL,
  `position` smallint(6) NOT NULL,
  `destination` varchar(255) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `top` tinyint(1) NOT NULL DEFAULT '0',
  `showmode` enum('image','description') NOT NULL DEFAULT 'image',
  `link_type` enum('local','external') NOT NULL DEFAULT 'local',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `banners_uri`
--

CREATE TABLE IF NOT EXISTS `banners_uri` (
  `banner_id` smallint(6) NOT NULL,
  `uri` varchar(255) DEFAULT NULL,
  UNIQUE KEY `uri_to_id` (`uri`,`banner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `catalog_data_cache`
--

CREATE TABLE IF NOT EXISTS `catalog_data_cache` (
  `id` int(11) NOT NULL,
  `type` enum('item','variant') NOT NULL,
  `cache` longtext NOT NULL,
  UNIQUE KEY `id` (`id`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `status` enum('public','delete') NOT NULL DEFAULT 'public',
  `collection_id` int(10) unsigned DEFAULT NULL,
  `dt` datetime NOT NULL,
  `ip` int(11) DEFAULT NULL,
  `text` text,
  `pub_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `dt` (`dt`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `key` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `value` text,
  `type` enum('basic','notification','contacts','seo','social_auth','sphinx','system') NOT NULL DEFAULT 'basic',
  `data_type` enum('text','checkbox','textarea','radio','select','serialized') NOT NULL DEFAULT 'text',
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cron_current_task`
--

CREATE TABLE IF NOT EXISTS `cron_current_task` (
  `name` varchar(255) NOT NULL,
  `start` datetime NOT NULL,
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cron_tasks`
--

CREATE TABLE IF NOT EXISTS `cron_tasks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `time_create` datetime DEFAULT NULL,
  `time_start` datetime DEFAULT NULL,
  `time_end` datetime DEFAULT NULL,
  `status` enum('new','processed','complete','cancel','sent') NOT NULL,
  `data` longtext,
  `segment_id` tinyint(3) unsigned DEFAULT NULL,
  `percent` tinyint(3) unsigned DEFAULT NULL,
  `errors` text COMMENT 'для общих мелких ошибок, для больших - отдельная таблица',
  `type` varchar(50) NOT NULL COMMENT 'тип задачи',
  `user_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cron_tasks_errors`
--

CREATE TABLE IF NOT EXISTS `cron_tasks_errors` (
  `task_id` int(10) unsigned NOT NULL,
  `number` int(11) NOT NULL COMMENT 'номер строки, итерации, id сущности',
  `error` varchar(255) NOT NULL,
  KEY `task_id` (`task_id`,`number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `database_migrations`
--

CREATE TABLE IF NOT EXISTS `database_migrations` (
  `name` varchar(255) NOT NULL,
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Хранит список примененных миграций';

-- --------------------------------------------------------

--
-- Table structure for table `email_logs`
--

CREATE TABLE IF NOT EXISTS `email_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('callback','edit_profile','bonus','item_question','variant_request','documents_request') DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(25) DEFAULT NULL,
  `data` text,
  `time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `enum_properties`
--

CREATE TABLE IF NOT EXISTS `enum_properties` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `property_id` smallint(5) unsigned NOT NULL,
  `value` varchar(255) NOT NULL,
  `position` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `enum_values_hidden`
--

CREATE TABLE IF NOT EXISTS `enum_values_hidden` (
  `type_id` int(10) unsigned NOT NULL,
  `property_id` int(10) unsigned NOT NULL,
  `enum_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `type_id` (`type_id`,`property_id`,`enum_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE IF NOT EXISTS `files` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `title` tinytext NOT NULL,
  `type` enum('none','manuf','order','feedback','property_value') NOT NULL DEFAULT 'none',
  `ext` varchar(4) NOT NULL,
  `size` int(11) NOT NULL,
  `show_in` tinyint(1) DEFAULT NULL,
  `cover_id` int(10) unsigned DEFAULT NULL,
  `position` int(10) unsigned NOT NULL DEFAULT '1',
  `date` date DEFAULT NULL,
  `number_1C` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `giftcard_list`
--

CREATE TABLE IF NOT EXISTS `giftcard_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `nominal_id` smallint(6) NOT NULL,
  `valid_date` datetime NOT NULL,
  `add_date` datetime NOT NULL,
  `user_id` smallint(6) DEFAULT NULL,
  `inn` bigint(20) DEFAULT NULL,
  `assign_date` datetime DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`,`nominal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `giftcard_nominal`
--

CREATE TABLE IF NOT EXISTS `giftcard_nominal` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `nominal_value` smallint(6) NOT NULL,
  `cost` smallint(6) NOT NULL,
  `provider_id` smallint(6) NOT NULL,
  `image_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `giftcard_provider`
--

CREATE TABLE IF NOT EXISTS `giftcard_provider` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `provider_name` varchar(255) NOT NULL,
  `provider_site` varchar(255) DEFAULT NULL,
  `description` text,
  `provider_image_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `giftcard_request`
--

CREATE TABLE IF NOT EXISTS `giftcard_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` smallint(6) NOT NULL,
  `inn` bigint(20) DEFAULT NULL,
  `name` varchar(100) NOT NULL COMMENT 'Имя получателя карты, может отличаться от имени пользователя',
  `status` enum('new','complete','discard') NOT NULL DEFAULT 'new',
  `creation_date` datetime NOT NULL,
  `processing_date` datetime DEFAULT NULL,
  `comment` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `giftcard_request_items`
--

CREATE TABLE IF NOT EXISTS `giftcard_request_items` (
  `request_id` smallint(6) NOT NULL,
  `nominal_id` smallint(6) NOT NULL,
  `count` tinyint(4) NOT NULL,
  UNIQUE KEY `request_id` (`request_id`,`nominal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE IF NOT EXISTS `images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `collection_id` int(11) DEFAULT NULL,
  `width` int(11) NOT NULL DEFAULT '0',
  `height` int(11) NOT NULL DEFAULT '0',
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `num` int(11) DEFAULT NULL,
  `gravity` enum('L','R','T','B','TL','TR','BL','BR','C') NOT NULL DEFAULT 'C',
  `ext` varchar(4) DEFAULT NULL,
  `info` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `image_collection`
--

CREATE TABLE IF NOT EXISTS `image_collection` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cover` int(10) unsigned NOT NULL DEFAULT '0',
  `data` text,
  `default` int(11) DEFAULT NULL,
  `type` enum('Default','TypeCover','Item','Comment','ItemsDefault','Files','Manuf','Property','Variant','PropertyValue') NOT NULL DEFAULT 'Default',
  `position` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `internal_links`
--

CREATE TABLE IF NOT EXISTS `internal_links` (
  `target_type` enum('catalog_item','file','catalog_type','catalog_variant','property') NOT NULL,
  `target_id` int(10) unsigned NOT NULL,
  `obj_type` enum('catalog_type','catalog_item','article','file','catalog_variant') NOT NULL,
  `obj_id` int(10) unsigned NOT NULL,
  `comment` varchar(255) NOT NULL,
  PRIMARY KEY (`target_type`,`target_id`,`obj_type`,`obj_id`),
  KEY `obj_type` (`obj_type`,`obj_id`),
  KEY `target_type` (`target_type`,`target_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE IF NOT EXISTS `items` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) DEFAULT NULL,
  `type_id` smallint(4) unsigned NOT NULL,
  `time` datetime NOT NULL,
  `post_id` int(6) unsigned DEFAULT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `recreate_view` tinyint(1) NOT NULL DEFAULT '0',
  `recreate_sphinx` tinyint(1) NOT NULL DEFAULT '0',
  `recreate_range` tinyint(1) NOT NULL DEFAULT '0',
  `position` smallint(6) NOT NULL DEFAULT '1',
  `last_update` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `items_properties_float`
--

CREATE TABLE IF NOT EXISTS `items_properties_float` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` smallint(6) unsigned NOT NULL,
  `property_id` smallint(5) unsigned NOT NULL,
  `value` decimal(14,5) DEFAULT NULL,
  `segment_id` tinyint(1) unsigned DEFAULT NULL,
  `position` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`,`property_id`,`segment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `items_properties_int`
--

CREATE TABLE IF NOT EXISTS `items_properties_int` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` smallint(6) unsigned NOT NULL,
  `property_id` smallint(5) unsigned NOT NULL,
  `value` int(11) DEFAULT NULL,
  `segment_id` tinyint(1) unsigned DEFAULT NULL,
  `position` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`,`property_id`,`segment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `items_properties_string`
--

CREATE TABLE IF NOT EXISTS `items_properties_string` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` smallint(6) unsigned NOT NULL,
  `property_id` smallint(5) unsigned NOT NULL,
  `value` text,
  `segment_id` tinyint(1) unsigned DEFAULT NULL,
  `position` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`,`property_id`,`segment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `item_types`
--

CREATE TABLE IF NOT EXISTS `item_types` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер типа',
  `title` varchar(255) NOT NULL,
  `key` varchar(255) DEFAULT NULL,
  `item_prefix` varchar(10) DEFAULT NULL COMMENT 'Префикс ключа айтема, подставляется только в урл, служит для различия между ключами типов и айтемов',
  `url` varchar(255) DEFAULT NULL,
  `parent_id` tinyint(2) unsigned DEFAULT NULL,
  `position` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `auto_mult` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('visible','hidden','delete') NOT NULL DEFAULT 'visible',
  `counters` char(255) DEFAULT NULL,
  `parents` char(255) NOT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `allow_children` tinyint(1) NOT NULL,
  `post_id` int(10) unsigned DEFAULT NULL,
  `annotation` text,
  `fixed` tinyint(1) NOT NULL DEFAULT '0',
  `cover_image_id` int(10) unsigned DEFAULT NULL,
  `default_image_id` int(11) DEFAULT NULL COMMENT 'id картинки для дефолтного отображения обложки товаров в данном типе',
  `nested_in` int(10) unsigned DEFAULT NULL COMMENT 'вариант со смежными типами. Если 1, значит это каталог смежного вида',
  `last_update` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `item_types_fields`
--

CREATE TABLE IF NOT EXISTS `item_types_fields` (
  `type_id` int(10) unsigned NOT NULL,
  `field` varchar(255) NOT NULL,
  `value` text,
  `segment_id` int(10) unsigned DEFAULT NULL,
  UNIQUE KEY `type_id_2` (`type_id`,`field`,`segment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE IF NOT EXISTS `menu` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `menu_item`
--

CREATE TABLE IF NOT EXISTS `menu_item` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `menu_id` smallint(6) NOT NULL,
  `parent_id` smallint(6) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `image_id` int(11) DEFAULT NULL,
  `permissions` varchar(255) DEFAULT NULL,
  `position` smallint(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menu_id` (`menu_id`,`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `payment_types`
--

CREATE TABLE IF NOT EXISTS `payment_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `commission_project` double(4,2) NOT NULL COMMENT 'процент комиссии для продавца',
  `commission_user` double(4,2) NOT NULL COMMENT 'процент комиссии для пользователя',
  `currency` varchar(5) NOT NULL COMMENT 'валюта',
  `payment_min` int(10) unsigned NOT NULL COMMENT 'минимальный платеж',
  `payment_max` int(10) unsigned NOT NULL COMMENT 'максимальный платеж',
  `mode` tinyint(1) NOT NULL COMMENT 'можно ли оплатить минуя платежный агергатор',
  `attributes` text COMMENT 'дополнительные атрибуты',
  `used` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'используется ли на сайте',
  `system_used` tinyint(1) NOT NULL COMMENT 'стоит ли галка в агрегаторе о том, что такая оплата используется',
  `group_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `payment_types_groups`
--

CREATE TABLE IF NOT EXISTS `payment_types_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `position` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_id` int(10) unsigned NOT NULL DEFAULT '0',
  `last_id` int(10) unsigned NOT NULL DEFAULT '0',
  `type` enum('default','pages','article','blog','items','types','tags','news','uses','property') NOT NULL DEFAULT 'default',
  `theme_id` tinyint(4) DEFAULT NULL,
  `status` enum('tmp','new','public','close','mod','delete','hidden') NOT NULL DEFAULT 'public',
  `data` text,
  `title` varchar(255) DEFAULT NULL,
  `annotation` text,
  `top` tinyint(1) NOT NULL DEFAULT '0',
  `comments` smallint(6) DEFAULT NULL,
  `readers_count` mediumint(9) DEFAULT NULL,
  `len` int(10) unsigned DEFAULT NULL,
  `num` int(11) DEFAULT NULL,
  `segment_id` int(11) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `key` varchar(255) DEFAULT NULL COMMENT 'Ключ поста, у обычных постов (новости, записи блога, акции и т.п.), участвует в формировании урла, у текстов к страницам - используется для извлечение нужного поста по ключу',
  `page_url_id` smallint(6) DEFAULT NULL COMMENT 'Id урла страницы, к которой привязан данный пост (только для постов к страницам, тип pages)',
  `complete_text` text COMMENT 'текст поста со вставленными ссылками',
  `site_links_done` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'флаг о том что перелинковщик уже обработал этот пост',
  `full_version` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `first_id` (`first_id`,`last_id`,`status`,`top`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE IF NOT EXISTS `properties` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` tinyint(3) unsigned NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` char(255) DEFAULT NULL,
  `filter_title` varchar(255) DEFAULT NULL,
  `key` varchar(255) NOT NULL,
  `external_key` varchar(255) DEFAULT NULL,
  `data_type` varchar(10) DEFAULT NULL,
  `catalog_id` int(2) DEFAULT NULL COMMENT 'ключ каталога, если тип свойства item или вариант',
  `major` tinyint(1) unsigned DEFAULT NULL COMMENT 'признак главного параметра',
  `major_count` varchar(10) DEFAULT NULL,
  `search_type` enum('between','select','autocomplete','check','none') NOT NULL DEFAULT 'none',
  `visible` tinyint(1) unsigned DEFAULT NULL,
  `values` text,
  `mask` varchar(255) DEFAULT NULL,
  `necessary` tinyint(1) unsigned DEFAULT NULL COMMENT 'признак необходимости к заполнению данного свойства',
  `unique` tinyint(1) unsigned DEFAULT NULL COMMENT 'признак уникальности значение этого свойства',
  `position` smallint(3) unsigned NOT NULL DEFAULT '0',
  `multiple` tinyint(1) unsigned DEFAULT NULL COMMENT 'Признак расщепляемости параметра',
  `group_id` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fixed` varchar(255) NOT NULL DEFAULT '0' COMMENT 'опредляет, запретить ли пользователям изменять это свойство',
  `image_id` int(10) DEFAULT NULL,
  `segment` tinyint(1) DEFAULT NULL,
  `export` tinyint(1) DEFAULT NULL,
  `read_only` tinyint(1) DEFAULT NULL,
  `set` tinyint(1) DEFAULT NULL,
  `filter_visible` tinyint(4) DEFAULT NULL,
  `public_description` text,
  `filter_slide` tinyint(1) DEFAULT NULL,
  `context` text,
  `sort` enum('default','alphabet','financial') DEFAULT NULL,
  `default_prop` tinyint(1) DEFAULT NULL COMMENT '1 для свойств по-умолчанию (автоматически создающиеся при создании конечного типа)',
  `default_key` varchar(255) DEFAULT NULL COMMENT 'ключ, который будет установлен свойству по-умолчанию у конечного типа',
  `default_value` varchar(255) DEFAULT NULL,
  `search_by_sphinx` tinyint(1) DEFAULT NULL COMMENT 'Используется ли свойство для поиска в sphinx',
  PRIMARY KEY (`id`),
  UNIQUE KEY `item_type_id` (`type_id`,`key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `properties_fields`
--

CREATE TABLE IF NOT EXISTS `properties_fields` (
  `property_id` int(10) unsigned NOT NULL,
  `field` varchar(255) NOT NULL,
  `value` tinytext NOT NULL,
  `segment_id` int(10) unsigned DEFAULT NULL,
  UNIQUE KEY `property_id` (`property_id`,`field`,`segment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `properties_hidden`
--

CREATE TABLE IF NOT EXISTS `properties_hidden` (
  `type_id` int(10) unsigned NOT NULL,
  `property_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `type_id` (`type_id`,`property_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `property_groups`
--

CREATE TABLE IF NOT EXISTS `property_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(11) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `position` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `key` varchar(255) NOT NULL,
  `group` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `restore_pass`
--

CREATE TABLE IF NOT EXISTS `restore_pass` (
  `user_id` int(11) NOT NULL,
  `check` char(32) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `search`
--

CREATE TABLE IF NOT EXISTS `search` (
  `phrase` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  UNIQUE KEY `phrase` (`phrase`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `search_log`
--

CREATE TABLE IF NOT EXISTS `search_log` (
  `phrase` varchar(255) NOT NULL,
  `count` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  UNIQUE KEY `phrase` (`phrase`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `segments`
--

CREATE TABLE IF NOT EXISTS `segments` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `key` varchar(5) NOT NULL,
  `position` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `segment_city`
--

CREATE TABLE IF NOT EXISTS `segment_city` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `segment_id` int(11) NOT NULL,
  `weekday` varchar(15) NOT NULL DEFAULT '..',
  `rect` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `segment_text_url`
--

CREATE TABLE IF NOT EXISTS `segment_text_url` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `position` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uri` (`url`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `selection_pages`
--

CREATE TABLE IF NOT EXISTS `selection_pages` (
  `key` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `uri` varchar(255) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `seo`
--

CREATE TABLE IF NOT EXISTS `seo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_uid` varchar(255) NOT NULL,
  `moduleUrl` varchar(255) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `keywords` text,
  `description` text,
  `descr` varchar(255) DEFAULT NULL,
  `canonical` varchar(255) DEFAULT NULL,
  `text` text,
  `enabled` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'показывает включена ли данная строка',
  `site_links_done` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'флаг о том что перелинковщик уже обработал текст',
  `complete_text` text COMMENT 'текст со вставленными ссылками',
  `need_rebuild` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_uid` (`page_uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Таблица для сео модуля.';

-- --------------------------------------------------------

--
-- Table structure for table `seo_links`
--

CREATE TABLE IF NOT EXISTS `seo_links` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `phrase` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `page_limit` tinyint(2) NOT NULL,
  `modified` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `seo_links_inserted`
--

CREATE TABLE IF NOT EXISTS `seo_links_inserted` (
  `from` varchar(255) NOT NULL,
  `to` varchar(255) NOT NULL,
  UNIQUE KEY `from` (`from`,`to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Содержит список перелинкованных направлений без привязки к ключевым словам';

-- --------------------------------------------------------

--
-- Table structure for table `seo_redirect`
--

CREATE TABLE IF NOT EXISTS `seo_redirect` (
  `fr` varchar(255) NOT NULL,
  `to` varchar(255) NOT NULL,
  `old_to` varchar(255) DEFAULT NULL,
  `auto` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 для автоматически созданных редиректов',
  KEY `fr` (`fr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `seo_sitemap_additional_url_rules`
--

CREATE TABLE IF NOT EXISTS `seo_sitemap_additional_url_rules` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `type` enum('allow','disallow') NOT NULL,
  `url` varchar(255) NOT NULL,
  `regex` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `seo_sitemap_allow_urls`
--

CREATE TABLE IF NOT EXISTS `seo_sitemap_allow_urls` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `priority` char(4) NOT NULL,
  `last_modification` datetime DEFAULT NULL,
  `valid` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'невалидным урл становится в случае конфликта с правилами robots.txt',
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sphinx_index_last_update`
--

CREATE TABLE IF NOT EXISTS `sphinx_index_last_update` (
  `index_name` varchar(50) NOT NULL,
  `last_update` datetime NOT NULL,
  PRIMARY KEY (`index_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sphinx_wordforms`
--

CREATE TABLE IF NOT EXISTS `sphinx_wordforms` (
  `src_form` varchar(255) NOT NULL,
  `dst_form` varchar(255) NOT NULL,
  `normalized_form` varchar(255) NOT NULL,
  `errors` text,
  PRIMARY KEY (`src_form`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `subscribe_groups`
--

CREATE TABLE IF NOT EXISTS `subscribe_groups` (
  `id` varchar(20) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `main_list` tinyint(1) DEFAULT NULL,
  `type` enum('list') NOT NULL DEFAULT 'list',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `subscribe_list_members`
--

CREATE TABLE IF NOT EXISTS `subscribe_list_members` (
  `group_id` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  UNIQUE KEY `group_id` (`group_id`,`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `subscribe_members`
--

CREATE TABLE IF NOT EXISTS `subscribe_members` (
  `email` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `surname` varchar(255) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `create_time` datetime NOT NULL,
  `lockconfirm` tinyint(1) DEFAULT NULL,
  `lockremove` datetime DEFAULT NULL,
  `inner` tinyint(1) DEFAULT NULL,
  `need_update` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `themes`
--

CREATE TABLE IF NOT EXISTS `themes` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` smallint(5) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `hide` text,
  `show` text,
  `keyword` varchar(255) NOT NULL,
  `position` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(250) NOT NULL,
  `pass_hash` char(32) NOT NULL COMMENT 'md5(login:salt:pass)',
  `reg_ip` varchar(15) NOT NULL,
  `reg_date` datetime NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `surname` varchar(255) DEFAULT NULL,
  `patronymic` varchar(255) DEFAULT NULL,
  `email_valid` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('new','active','banned','expired','deleted') NOT NULL DEFAULT 'expired',
  `auth` char(32) DEFAULT NULL,
  `expired` datetime DEFAULT NULL,
  `segment_id` int(11) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `organisation_phone` varchar(20) DEFAULT NULL,
  `organisation_fax` varchar(20) DEFAULT NULL,
  `display_phone` varchar(15) DEFAULT NULL,
  `requisites` text,
  `jure_address` text,
  `document_address` text,
  `person_type` enum('fiz','org','man') DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `inn` bigint(20) DEFAULT NULL,
  `okpo` varchar(10) DEFAULT NULL,
  `referer` int(10) unsigned DEFAULT NULL,
  `referal_number` varchar(32) NOT NULL,
  `bonus` int(11) NOT NULL DEFAULT '0',
  `last_update_bonus` datetime DEFAULT NULL,
  `program` enum('master','profi') DEFAULT NULL COMMENT 'Бонусная программа',
  `master` tinyint(1) DEFAULT NULL,
  `all_bonus` decimal(8,2) NOT NULL DEFAULT '0.00',
  `bonus_sum` decimal(8,2) DEFAULT NULL COMMENT 'Сумма баллов, при достижении которой пользователю бонусятина',
  `subscribe` tinyint(1) DEFAULT NULL,
  `order_status` tinyint(1) DEFAULT NULL,
  `ogrn` bigint(20) unsigned DEFAULT NULL,
  `kpp` int(10) unsigned DEFAULT NULL,
  `scope` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `manager` varchar(255) DEFAULT NULL,
  `delivery` int(11) DEFAULT NULL,
  `last_update` datetime DEFAULT NULL,
  `discount` varchar(255) DEFAULT NULL,
  `discount_tool` varchar(20) DEFAULT NULL,
  `discount_equip` varchar(20) DEFAULT NULL,
  `skype` varchar(255) DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `show_in_contacts` tinyint(1) DEFAULT NULL,
  `image_id` int(10) DEFAULT NULL,
  `money_balance` double NOT NULL DEFAULT '0',
  `1C_id` varchar(255) DEFAULT NULL,
  `import` tinyint(1) DEFAULT NULL,
  `id_1C` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `display_phone` (`display_phone`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users_address`
--

CREATE TABLE IF NOT EXISTS `users_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `address` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users_social_data`
--

CREATE TABLE IF NOT EXISTS `users_social_data` (
  `user_id` smallint(6) NOT NULL,
  `identity` varchar(255) NOT NULL,
  `network` varchar(30) NOT NULL,
  UNIQUE KEY `identity` (`identity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_code_actions`
--

CREATE TABLE IF NOT EXISTS `user_code_actions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_code` varchar(7) NOT NULL,
  `action` enum('redirect') NOT NULL,
  `data` text NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_code` (`user_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_data_1c`
--

CREATE TABLE IF NOT EXISTS `user_data_1c` (
  `inn` bigint(20) unsigned DEFAULT NULL,
  `ogrn` bigint(20) unsigned DEFAULT NULL,
  `kpp` bigint(20) unsigned DEFAULT NULL,
  `okpo` varchar(255) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `surname` varchar(255) DEFAULT NULL,
  `patronymic` varchar(255) DEFAULT NULL,
  `discount_tool` varchar(255) DEFAULT NULL,
  `discount_equip` varchar(255) DEFAULT NULL,
  `requisites` text,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `organisation_phone` varchar(255) DEFAULT NULL,
  `organisation_fax` varchar(255) DEFAULT NULL,
  `jure_address` text,
  `document_address` text,
  `money_balance` decimal(10,2) DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `id_1C` varchar(255) DEFAULT NULL,
  UNIQUE KEY `1C_id` (`id_1C`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_favorites`
--

CREATE TABLE IF NOT EXISTS `user_favorites` (
  `user_id` int(10) unsigned NOT NULL,
  `variant_ids` text,
  `comments` text,
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

CREATE TABLE IF NOT EXISTS `user_permissions` (
  `action_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  `permission` enum('enable','disable') NOT NULL,
  UNIQUE KEY `action_id` (`action_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE IF NOT EXISTS `user_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `default_permission` enum('enable','disable') NOT NULL,
  `after_login_redirect` varchar(255) DEFAULT NULL,
  `site_id` tinyint(3) unsigned DEFAULT NULL,
  `position` tinyint(2) NOT NULL,
  `group_id` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_roles_groups`
--

CREATE TABLE IF NOT EXISTS `user_roles_groups` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `access_level` tinyint(4) NOT NULL,
  `access_area` enum('admin','public') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `values_log`
--

CREATE TABLE IF NOT EXISTS `values_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('create','edit','images','delete','attr','attr_group','post','assoc','position') DEFAULT NULL COMMENT 'Действие, произведенное над объектом',
  `time` varchar(255) DEFAULT NULL,
  `entity_type` enum('item','variant','property','video','item_type','manuf','config','post','collection','user','order','file','banner','region','video_type','icon','post_theme','engine_system','filial','filial_region','pool','city','days') NOT NULL,
  `entity_id` smallint(5) unsigned NOT NULL,
  `attr_id` varchar(50) DEFAULT NULL,
  `segment_id` tinyint(4) unsigned DEFAULT NULL,
  `user_id` smallint(6) unsigned DEFAULT NULL,
  `comment` text,
  `additional_data` longtext,
  `cli` tinyint(1) DEFAULT NULL COMMENT 'изменения были произведены через крон',
  PRIMARY KEY (`id`),
  KEY `user` (`user_id`),
  KEY `EntityAttr` (`entity_type`,`entity_id`,`attr_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `variants`
--

CREATE TABLE IF NOT EXISTS `variants` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) DEFAULT NULL,
  `item_id` smallint(6) unsigned NOT NULL,
  `status` tinyint(1) NOT NULL,
  `time` datetime NOT NULL,
  `position` smallint(6) NOT NULL DEFAULT '1',
  `last_update` datetime DEFAULT NULL,
  `recreate_view` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `variants_properties_float`
--

CREATE TABLE IF NOT EXISTS `variants_properties_float` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `variant_id` smallint(6) unsigned NOT NULL,
  `property_id` smallint(5) unsigned NOT NULL,
  `value` decimal(14,5) DEFAULT NULL,
  `segment_id` tinyint(1) unsigned DEFAULT NULL,
  `position` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `variant_id` (`variant_id`,`property_id`,`segment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `variants_properties_int`
--

CREATE TABLE IF NOT EXISTS `variants_properties_int` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `variant_id` smallint(6) unsigned NOT NULL,
  `property_id` smallint(5) unsigned NOT NULL,
  `value` bigint(20) DEFAULT NULL,
  `segment_id` tinyint(1) unsigned DEFAULT NULL,
  `position` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `variant_id` (`variant_id`,`property_id`,`segment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `variants_properties_string`
--

CREATE TABLE IF NOT EXISTS `variants_properties_string` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `variant_id` smallint(6) unsigned NOT NULL,
  `property_id` smallint(5) unsigned NOT NULL,
  `value` text,
  `segment_id` tinyint(1) unsigned DEFAULT NULL,
  `position` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `variant_id` (`variant_id`,`property_id`,`segment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure for view `catalog_search`
--
DROP VIEW IF EXISTS `catalog_search`;

CREATE VIEW `catalog_search` AS select ((if(isnull(`v`.`id`),0,(`v`.`id` * 1000000)) + (`i`.`id` * 100)) + if((`p`.`multiple` = 1),if(isnull(`pv`.`segment_id`),0,`pv`.`segment_id`),if(isnull(`pi`.`segment_id`),0,`pi`.`segment_id`))) AS `id`,`v`.`id` AS `variant_id`,`i`.`id` AS `item_id`,`i`.`type_id` AS `type_id`,if((`p`.`multiple` = 1),`pv`.`value`,`pi`.`value`) AS `value`,if((`p`.`multiple` = 1),if(isnull(`pv`.`segment_id`),0,`pv`.`segment_id`),if(isnull(`pi`.`segment_id`),0,`pi`.`segment_id`)) AS `segment_id`,`v`.`last_update` AS `last_update` from ((((`items` `i` left join `variants` `v` on((`i`.`id` = `v`.`item_id`))) join `properties` `p` on(((`i`.`type_id` = `p`.`type_id`) and (`p`.`key` = 'sphinx_search_value')))) left join `variants_properties_string` `pv` on(((`pv`.`variant_id` = `v`.`id`) and (`p`.`id` = `pv`.`property_id`)))) left join `items_properties_string` `pi` on(((`pi`.`item_id` = `i`.`id`) and (`p`.`id` = `pi`.`property_id`))));

-- --------------------------------------------------------

--
-- Structure for view `post_search`
--
DROP VIEW IF EXISTS `post_search`;

CREATE VIEW `post_search` AS select `p`.`id` AS `id`,`p`.`type` AS `type`,`p`.`status` AS `status`,`p`.`title` AS `title`,`p`.`annotation` AS `annotation`,`p`.`segment_id` AS `segment_id`,`p`.`key` AS `key`,`c`.`id` AS `comment_id`,`c`.`text` AS `text`,`c`.`pub_date` AS `pub_date` from (`posts` `p` join `comments` `c` on((`p`.`id` = `c`.`post_id`)));

--
-- Структура для представления `lps_metatags`
--
DROP VIEW IF EXISTS `lps_metatags`;

CREATE VIEW `lps_metatags` AS select `seo`.`id` AS `id`,`seo`.`text` AS `text` from `seo` where 1;