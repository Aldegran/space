-- phpMyAdmin SQL Dump
-- version 4.0.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 05, 2014 at 06:00 PM
-- Server version: 5.5.30
-- PHP Version: 5.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `sp`
--

-- --------------------------------------------------------

--
-- Table structure for table `planets`
--

DROP TABLE IF EXISTS `planets`;
CREATE TABLE IF NOT EXISTS `planets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `diameter` int(9) NOT NULL,
  `size` double(9,3) NOT NULL,
  `people` int(11) NOT NULL,
  `type` int(2) NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `technology` int(11) NOT NULL,
  `task` int(2) NOT NULL,
  `resources` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sciences`
--

DROP TABLE IF EXISTS `sciences`;
CREATE TABLE IF NOT EXISTS `sciences` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `title` varchar(255) NOT NULL,
  `parent` int(9) DEFAULT NULL,
  `replaced` int(1) DEFAULT NULL,
  `cost` int(11) NOT NULL,
  `rules` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sciences_to_planets`
--

DROP TABLE IF EXISTS `sciences_to_planets`;
CREATE TABLE IF NOT EXISTS `sciences_to_planets` (
  `science_id` int(9) NOT NULL,
  `planet_id` int(11) NOT NULL,
  PRIMARY KEY (`science_id`,`planet_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
