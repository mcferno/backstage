-- phpMyAdmin SQL Dump
-- version 3.4.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 09, 2012 at 12:04 AM
-- Server version: 5.1.61
-- PHP Version: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `kennyquotemachine`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE IF NOT EXISTS `accounts` (
  `id` char(36) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `handle` varchar(64) NOT NULL,
  `user_id` varchar(64) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE IF NOT EXISTS `activities` (
  `id` char(36) NOT NULL,
  `model` varchar(25) NOT NULL,
  `foreign_key` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `model` (`model`,`foreign_key`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

CREATE TABLE IF NOT EXISTS `assets` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `type` varchar(10) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `ext` varchar(10) NOT NULL,
  `checksum` varchar(100) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `fb_id` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `assets_contests`
--

CREATE TABLE IF NOT EXISTS `assets_contests` (
  `id` char(36) NOT NULL DEFAULT '',
  `asset_id` char(36) DEFAULT NULL,
  `contest_id` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asset_id` (`asset_id`),
  KEY `contest_id` (`contest_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `contests`
--

CREATE TABLE IF NOT EXISTS `contests` (
  `id` char(36) NOT NULL DEFAULT '',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `user_id` char(36) DEFAULT NULL,
  `asset_id` char(36) DEFAULT NULL,
  `winning_asset_id` char(36) DEFAULT NULL,
  `message` text,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `asset_id` (`asset_id`),
  KEY `winning_asset_id` (`winning_asset_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `links`
--

CREATE TABLE IF NOT EXISTS `links` (
  `id` char(36) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `user_id` char(36) NOT NULL,
  `url` varchar(512) NOT NULL,
  `title` varchar(300) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `id` char(36) NOT NULL,
  `model` varchar(25) DEFAULT NULL,
  `foreign_id` char(36) DEFAULT NULL,
  `user_id` char(36) NOT NULL,
  `created` datetime NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `id` (`id`,`user_id`),
  KEY `model` (`model`,`foreign_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `id` char(36) NOT NULL DEFAULT '',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `content` longtext,
  `content_width` int(11) DEFAULT NULL,
  `uri` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uri` (`uri`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `id` char(36) NOT NULL,
  `model` varchar(64) NOT NULL,
  `foreign_key` char(36) NOT NULL,
  `body` text NOT NULL,
  `source` text NOT NULL,
  `date` int(11) NOT NULL,
  `permalink` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `model` (`model`,`foreign_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `taggings`
--

CREATE TABLE IF NOT EXISTS `taggings` (
  `id` char(36) NOT NULL DEFAULT '',
  `created` datetime DEFAULT NULL,
  `user_id` char(36) DEFAULT NULL,
  `tag_id` char(36) DEFAULT NULL,
  `model` varchar(25) DEFAULT NULL,
  `foreign_id` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tumblr`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `id` char(36) NOT NULL DEFAULT '',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `user_id` char(36) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tumblr`
--

CREATE TABLE IF NOT EXISTS `tumblr` (
  `id` char(36) NOT NULL,
  `blog_name` varchar(64) NOT NULL,
  `tumblr_id` varchar(32) NOT NULL,
  `post_url` varchar(64) NOT NULL,
  `type` varchar(32) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `text` text NOT NULL,
  `source` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `twitter`
--

CREATE TABLE IF NOT EXISTS `twitter` (
  `id` char(36) NOT NULL,
  `created_at` int(11) NOT NULL,
  `text` varchar(200) NOT NULL,
  `source` varchar(128) NOT NULL,
  `truncated` tinyint(1) unsigned NOT NULL,
  `in_reply_to_status_id` varchar(64) DEFAULT NULL,
  `in_reply_to_user_id` varchar(64) DEFAULT NULL,
  `in_reply_to_screen_name` varchar(64) DEFAULT NULL,
  `favorited` tinyint(1) unsigned NOT NULL,
  `user_id` varchar(64) NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` char(36) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` char(65) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `role` tinyint(4) DEFAULT NULL,
  `fb_target` varchar(64) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `last_seen` datetime DEFAULT NULL,
  `last_ack` datetime DEFAULT NULL,
  `last_update` datetime DEFAULT NULL,
  `session_key` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
