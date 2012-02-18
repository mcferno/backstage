-- phpMyAdmin SQL Dump
-- version 3.4.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 18, 2012 at 01:27 PM
-- Server version: 5.1.52
-- PHP Version: 5.3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

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

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;