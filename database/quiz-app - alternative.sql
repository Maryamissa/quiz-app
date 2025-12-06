-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3315
-- Generation Time: Dec 06, 2025 at 12:03 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `quiz-app`;
USE `quiz-app`;

-- --------------------------------------------------------
-- Table structure for table `messages`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `messages` (
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `message` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `reviews`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `reviews` varchar(50) NOT NULL,
  `rate` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `reviews`
INSERT INTO `reviews` (`id`, `email`, `reviews`, `rate`) VALUES
(1, 'a@gmail.com', 'wow amazing', 1)
ON DUPLICATE KEY UPDATE email=email;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Username` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`Username`),
  KEY `fk-email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `users`
INSERT INTO `users` (`id`, `Username`, `email`, `password`) VALUES
(2, 'nabih', 'gptchateshterak111@hotmail.com', '$2y$10$lyTCzeDL0Izjm7yLIV16xelJcd1yTqYfawzNhwlyBTQ'),
(3, 'delbo', 'delbanikk@gmail.com', '$2y$10$3EC1Wj2PkMQmu7HHEQrcPOOWqXEcwF0ROTSFyNXkDyg'),
(4, 'a', 'a@gmail.com', '$2y$10$5w6dZfqKr1sb871VgUGE8etNLIdw0MUChCaVEJcoorR'),
(5, 'b', 'b@gmail.com', '$2y$10$J5RSYVv5Bjy0aArQjjRfyOqB4W.rMP6la/ad0Dz8rV9'),
(6, 'c', 'c@gmail.com', '$2y$10$6MugJpEo47d.DRmsPAX58eIACWPpoFKfcyCMJ875Txr')
ON DUPLICATE KEY UPDATE email=email;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
