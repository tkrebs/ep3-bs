-- phpMyAdmin SQL Dump
-- version 4.1.8
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 23. Jun 2014 um 23:00
-- Server Version: 5.5.27
-- PHP-Version: 5.4.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Datenbank: `ep3-bs`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bs_bookings`
--

CREATE TABLE IF NOT EXISTS `bs_bookings` (
  `bid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `sid` int(10) unsigned NOT NULL,
  `status` varchar(64) NOT NULL COMMENT 'single|subscription|cancelled',
  `status_billing` varchar(64) NOT NULL COMMENT 'pending|paid|cancelled|uncollectable',
  `visibility` varchar(64) NOT NULL COMMENT 'public|private',
  `quantity` int(10) unsigned NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`bid`),
  KEY `sid` (`sid`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bs_bookings_bills`
--

CREATE TABLE IF NOT EXISTS `bs_bookings_bills` (
  `bbid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bid` int(10) unsigned NOT NULL,
  `description` varchar(512) NOT NULL,
  `quantity` int(10) unsigned DEFAULT NULL,
  `time` int(10) unsigned DEFAULT NULL,
  `price` int(10) NOT NULL,
  `rate` int(10) unsigned NOT NULL,
  `gross` tinyint(1) NOT NULL,
  PRIMARY KEY (`bbid`),
  KEY `bid` (`bid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bs_bookings_meta`
--

CREATE TABLE IF NOT EXISTS `bs_bookings_meta` (
  `bmid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bid` int(10) unsigned NOT NULL,
  `key` varchar(64) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`bmid`),
  KEY `bid` (`bid`),
  KEY `key` (`key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bs_events`
--

CREATE TABLE IF NOT EXISTS `bs_events` (
  `eid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sid` int(10) unsigned DEFAULT NULL COMMENT 'NULL for all',
  `status` varchar(64) NOT NULL DEFAULT 'enabled' COMMENT 'enabled',
  `datetime_start` datetime NOT NULL,
  `datetime_end` datetime NOT NULL,
  `capacity` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`eid`),
  KEY `sid` (`sid`),
  KEY `datetime_start` (`datetime_start`),
  KEY `datetime_end` (`datetime_end`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bs_events_meta`
--

CREATE TABLE IF NOT EXISTS `bs_events_meta` (
  `emid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `eid` int(10) unsigned NOT NULL,
  `key` varchar(64) NOT NULL,
  `value` text NOT NULL,
  `locale` varchar(8) DEFAULT NULL,
  PRIMARY KEY (`emid`),
  KEY `eid` (`eid`),
  KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bs_options`
--

CREATE TABLE IF NOT EXISTS `bs_options` (
  `oid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(64) NOT NULL,
  `value` text NOT NULL,
  `locale` varchar(8) DEFAULT NULL,
  PRIMARY KEY (`oid`),
  KEY `key` (`key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bs_reservations`
--

CREATE TABLE IF NOT EXISTS `bs_reservations` (
  `rid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bid` int(10) unsigned NOT NULL,
  `date` date NOT NULL,
  `time_start` time NOT NULL,
  `time_end` time NOT NULL,
  PRIMARY KEY (`rid`),
  KEY `bid` (`bid`),
  KEY `date` (`date`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bs_reservations_meta`
--

CREATE TABLE IF NOT EXISTS `bs_reservations_meta` (
  `rmid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rid` int(10) unsigned NOT NULL,
  `key` varchar(64) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`rmid`),
  KEY `rid` (`rid`),
  KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bs_squares`
--

CREATE TABLE IF NOT EXISTS `bs_squares` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `status` varchar(64) NOT NULL DEFAULT 'enabled' COMMENT 'disabled|readonly|enabled',
  `priority` float NOT NULL DEFAULT '1',
  `capacity` int(10) unsigned NOT NULL,
  `capacity_heterogenic` tinyint(1) NOT NULL,
  `allow_notes` tinyint(1) NOT NULL,
  `time_start` time NOT NULL,
  `time_end` time NOT NULL,
  `time_block` int(10) unsigned NOT NULL,
  `time_block_bookable` int(10) unsigned NOT NULL,
  `time_block_bookable_max` int(10) unsigned DEFAULT NULL,
  `min_range_book` int(10) unsigned DEFAULT 0,
  `range_book` int(10) unsigned DEFAULT NULL,
  `max_active_bookings` int(10) unsigned DEFAULT 0,
  `range_cancel` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`sid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bs_squares_coupons`
--

CREATE TABLE IF NOT EXISTS `bs_squares_coupons` (
  `scid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sid` int(10) unsigned DEFAULT NULL COMMENT 'NULL for all',
  `code` varchar(64) NOT NULL,
  `date_start` datetime DEFAULT NULL,
  `date_end` datetime DEFAULT NULL,
  `discount_for_booking` int(10) unsigned NOT NULL,
  `discount_for_products` int(10) unsigned NOT NULL,
  `discount_in_percent` tinyint(1) NOT NULL,
  PRIMARY KEY (`scid`),
  KEY `sid` (`sid`),
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bs_squares_meta`
--

CREATE TABLE IF NOT EXISTS `bs_squares_meta` (
  `smid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sid` int(10) unsigned NOT NULL,
  `key` varchar(64) NOT NULL,
  `value` text NOT NULL,
  `locale` varchar(8) DEFAULT NULL,
  PRIMARY KEY (`smid`),
  KEY `sid` (`sid`),
  KEY `key` (`key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bs_squares_pricing`
--

CREATE TABLE IF NOT EXISTS `bs_squares_pricing` (
  `spid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sid` int(10) unsigned DEFAULT NULL COMMENT 'NULL for all',
  `priority` int(10) unsigned NOT NULL,
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `day_start` tinyint(3) unsigned DEFAULT NULL COMMENT 'Day of the week',
  `day_end` tinyint(3) unsigned DEFAULT NULL,
  `time_start` time DEFAULT NULL,
  `time_end` time DEFAULT NULL,
  `price` int(10) unsigned DEFAULT NULL,
  `rate` int(10) unsigned DEFAULT NULL,
  `gross` tinyint(1) DEFAULT NULL,
  `per_time_block` int(10) unsigned DEFAULT NULL,
  `per_quantity` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`spid`),
  KEY `sid` (`sid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bs_squares_products`
--

CREATE TABLE IF NOT EXISTS `bs_squares_products` (
  `spid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sid` int(10) unsigned DEFAULT NULL COMMENT 'NULL for all',
  `priority` int(10) unsigned NOT NULL,
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `name` varchar(128) NOT NULL,
  `description` text,
  `options` varchar(512) NOT NULL,
  `price` int(10) unsigned NOT NULL,
  `rate` int(10) unsigned NOT NULL,
  `gross` tinyint(1) NOT NULL,
  `locale` varchar(8) DEFAULT NULL,
  PRIMARY KEY (`spid`),
  KEY `sid` (`sid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bs_users`
--

CREATE TABLE IF NOT EXISTS `bs_users` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(128) NOT NULL,
  `status` varchar(64) NOT NULL DEFAULT 'placeholder' COMMENT 'placeholder|deleted|blocked|disabled|enabled|assist|admin',
  `email` varchar(128) DEFAULT NULL,
  `pw` varchar(256) DEFAULT NULL,
  `login_attempts` tinyint(3) unsigned DEFAULT NULL,
  `login_detent` datetime DEFAULT NULL,
  `last_activity` datetime DEFAULT NULL,
  `last_ip` varchar(64) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `alias` (`alias`),
  KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bs_users_meta`
--

CREATE TABLE IF NOT EXISTS `bs_users_meta` (
  `umid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `key` varchar(64) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`umid`),
  KEY `key` (`key`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `bs_bookings`
--
ALTER TABLE `bs_bookings`
  ADD CONSTRAINT `bs_bookings_ibfk_3` FOREIGN KEY (`sid`) REFERENCES `bs_squares` (`sid`),
  ADD CONSTRAINT `bs_bookings_ibfk_4` FOREIGN KEY (`uid`) REFERENCES `bs_users` (`uid`);

--
-- Constraints der Tabelle `bs_bookings_bills`
--
ALTER TABLE `bs_bookings_bills`
  ADD CONSTRAINT `bs_bookings_bills_ibfk_1` FOREIGN KEY (`bid`) REFERENCES `bs_bookings` (`bid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `bs_bookings_meta`
--
ALTER TABLE `bs_bookings_meta`
  ADD CONSTRAINT `bs_bookings_meta_ibfk_1` FOREIGN KEY (`bid`) REFERENCES `bs_bookings` (`bid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `bs_events`
--
ALTER TABLE `bs_events`
  ADD CONSTRAINT `bs_events_ibfk_1` FOREIGN KEY (`sid`) REFERENCES `bs_squares` (`sid`);

--
-- Constraints der Tabelle `bs_events_meta`
--
ALTER TABLE `bs_events_meta`
  ADD CONSTRAINT `bs_events_meta_ibfk_1` FOREIGN KEY (`eid`) REFERENCES `bs_events` (`eid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `bs_reservations`
--
ALTER TABLE `bs_reservations`
  ADD CONSTRAINT `bs_reservations_ibfk_1` FOREIGN KEY (`bid`) REFERENCES `bs_bookings` (`bid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `bs_reservations_meta`
--
ALTER TABLE `bs_reservations_meta`
  ADD CONSTRAINT `bs_reservations_meta_ibfk_1` FOREIGN KEY (`rid`) REFERENCES `bs_reservations` (`rid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `bs_squares_coupons`
--
ALTER TABLE `bs_squares_coupons`
  ADD CONSTRAINT `bs_squares_coupons_ibfk_1` FOREIGN KEY (`sid`) REFERENCES `bs_squares` (`sid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `bs_squares_meta`
--
ALTER TABLE `bs_squares_meta`
  ADD CONSTRAINT `bs_squares_meta_ibfk_1` FOREIGN KEY (`sid`) REFERENCES `bs_squares` (`sid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `bs_squares_pricing`
--
ALTER TABLE `bs_squares_pricing`
  ADD CONSTRAINT `bs_squares_pricing_ibfk_1` FOREIGN KEY (`sid`) REFERENCES `bs_squares` (`sid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `bs_squares_products`
--
ALTER TABLE `bs_squares_products`
  ADD CONSTRAINT `bs_squares_products_ibfk_1` FOREIGN KEY (`sid`) REFERENCES `bs_squares` (`sid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `bs_users_meta`
--
ALTER TABLE `bs_users_meta`
  ADD CONSTRAINT `bs_users_meta_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `bs_users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;
