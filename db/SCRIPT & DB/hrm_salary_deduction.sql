-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 16, 2022 at 01:02 PM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 8.0.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `test_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `hrm_salary_deduction`
--

CREATE TABLE `hrm_salary_deduction` (
  `id` int(10) UNSIGNED NOT NULL,
  `month_from` date NOT NULL,
  `month_to` date NOT NULL,
  `purpose` varchar(150) NOT NULL,
  `amount` double(8,2) NOT NULL,
  `hrm_salary_head_id` int(11) NOT NULL,
  `hrm_employee_id` int(10) UNSIGNED NOT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `hrm_salary_deduction`
--

INSERT INTO `hrm_salary_deduction` (`id`, `month_from`, `month_to`, `purpose`, `amount`, `hrm_salary_head_id`, `hrm_employee_id`, `status`, `is_active`, `created_at`, `updated_at`) VALUES
(2, '2022-02-01', '2022-03-01', 'Ducimus ut diam a veritatis', 1000.00, 1, 800, NULL, 1, '2022-01-16 07:18:42', '2022-01-16 09:49:30'),
(3, '2022-01-01', '2022-02-01', 'Ducimus ut diam a veritatis 2', 1500.00, 5, 1091, NULL, 1, '2022-01-16 09:38:43', '2022-01-16 09:38:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hrm_salary_deduction`
--
ALTER TABLE `hrm_salary_deduction`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hrm_salary_deduction`
--
ALTER TABLE `hrm_salary_deduction`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
