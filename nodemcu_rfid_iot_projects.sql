-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 20, 2020 at 12:40 PM
-- Server version: 10.1.38-MariaDB
-- PHP Version: 7.3.3
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
--
-- Database: `nodemcu_rfid_iot_projects`
--
-- --------------------------------------------------------
--
-- Table structure for table `table_the_iot_projects`
--
CREATE TABLE `table_the_iot_projects` (
  `name` varchar(100) NOT NULL,
  `id` varchar(100) NOT NULL PRIMARY KEY,
  `gender` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



CREATE TABLE attendance_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(100) CHARACTER SET latin1 NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Present', 'Absent', 'Denied') NOT NULL,
    FOREIGN KEY (user_id) REFERENCES table_the_iot_projects(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE gatepass_logs (
    entry_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(100) CHARACTER SET latin1 NOT NULL,
    entry_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    exit_time DATETIME NULL,
    FOREIGN KEY (user_id) REFERENCES table_the_iot_projects(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Create access_logs table with explicit charset matching the parent table
CREATE TABLE IF NOT EXISTS `access_logs` (
    `log_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` varchar(100) NOT NULL,
    `access_time` DATETIME NOT NULL,
    `success` TINYINT(1) NOT NULL DEFAULT 0,
    CONSTRAINT `access_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `table_the_iot_projects`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `table_the_iot_projects`
--
INSERT INTO `table_the_iot_projects` (`name`, `id`, `gender`, `email`, `mobile`) VALUES
('Alsan', '39EAB06D', 'Male', 'mydigitalnepal@gmail.com', '9800998787'),
('John', '769174F8', 'Male', 'john@email.com', '23456789'),
('Thvhm,b', '81A3DC79', 'Female', 'jgkhkkmanjil@gmail.com', '45768767564'),
('The IoT Projects', '866080F8', 'Male', 'ask.theiotprojects@gmail.com', '9800988978');

-- No need for the ALTER TABLE statement anymore since we defined PRIMARY KEY inline
-- ALTER TABLE `table_the_iot_projects` ADD PRIMARY KEY (`id`);

COMMIT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;