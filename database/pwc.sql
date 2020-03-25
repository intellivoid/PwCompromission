-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 25, 2020 at 01:54 AM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.2.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `intellivoid`
--

-- --------------------------------------------------------

--
-- Table structure for table `pwc`
--

CREATE TABLE `pwc` (
  `id` int(255) NOT NULL COMMENT 'The Unique Internal Databse ID for this record',
  `hash` varchar(255) DEFAULT NULL COMMENT 'The hash of the record',
  `plain_text` varchar(255) DEFAULT NULL COMMENT 'The plain text verison of the record',
  `timestamp` int(255) DEFAULT NULL COMMENT 'The Unix Timestamp of when this record was registered'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Table for hosting the cache of comprised passwords';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pwc`
--
ALTER TABLE `pwc`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pwc_id_uindex` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pwc`
--
ALTER TABLE `pwc`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT COMMENT 'The Unique Internal Databse ID for this record';
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
