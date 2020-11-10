-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 26, 2020 at 06:10 AM
-- Server version: 10.4.8-MariaDB
-- PHP Version: 7.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `w2w_mcn`
--

-- --------------------------------------------------------

--
-- Table structure for table `asset`
--

CREATE TABLE `asset` (
  `asset_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `asset_type` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1 For Product',
  `asset_type_id` bigint(20) UNSIGNED NOT NULL,
  `asset_path` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 For InActive, 1 For Active',
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `asset`
--

INSERT INTO `asset` (`asset_id`, `uuid`, `asset_type`, `asset_type_id`, `asset_path`, `status`, `updated_at`, `updated_by`) VALUES
(4, '79417119-ede1-11ea-b0fa-082e5f2acf88', 1, 4, 'uploads/products/1599136333_0.jpg', 1, '2020-09-03 12:30:13', 1),
(5, '79684f33-ede1-11ea-b0fa-082e5f2acf88', 1, 4, 'uploads/products/1599136333_1.jpg', 1, '2020-09-03 12:30:13', 1),
(6, '76b21ae2-f8ac-11ea-9f8c-082e5f2acf88', 1, 10, 'uploads/products/1600323025_0.jpg', 1, '2020-09-17 01:30:28', 1),
(7, '5ad5a9e4-fe30-11ea-b53c-082e5f2acf88', 1, 11, 'uploads/products/1600929422_0.jpg', 1, '2020-09-24 13:51:10', 1);

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `localID` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `employee_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `terminal_id` int(11) NOT NULL,
  `in_out` int(11) NOT NULL,
  `in_out_datetime` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `server_id` int(11) NOT NULL,
  `sync` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `localID`, `employee_id`, `branch_id`, `terminal_id`, `in_out`, `in_out_datetime`, `created_by`, `created_date`, `updated_date`, `server_id`, `sync`, `created_at`, `updated_at`) VALUES
(1, '', 2, 1, 1, 1, '2020-09-14 08:52:24', 0, '2020-09-14 08:52:24', '2020-09-14 08:52:24', 1, 1, '2020-09-15 13:49:17', '2020-09-15 13:49:17'),
(2, '', 2, 1, 1, 1, '2020-09-14 09:30:53', 0, '2020-09-14 09:30:53', '2020-09-14 09:30:53', 2, 1, '2020-09-15 13:49:17', '2020-09-15 13:49:17'),
(3, '', 2, 1, 1, 1, '2020-09-15 12:51:24', 0, '2020-09-15 12:51:24', '2020-09-15 12:51:24', 3, 1, '2020-09-15 13:49:17', '2020-09-15 13:49:17'),
(4, '', 2, 1, 1, 1, '2020-09-15 12:54:02', 0, '2020-09-15 12:54:02', '2020-09-15 12:54:02', 4, 1, '2020-09-15 13:49:17', '2020-09-15 13:49:17'),
(5, '', 2, 1, 1, 1, '2020-09-15 01:17:16', 0, '2020-09-15 01:17:16', '2020-09-15 01:17:16', 5, 1, '2020-09-15 13:49:17', '2020-09-15 13:49:17'),
(6, '', 2, 1, 1, 0, '2020-09-15 01:30:18', 0, '2020-09-15 01:30:18', '2020-09-15 01:30:18', 6, 1, '2020-09-15 13:49:17', '2020-09-15 13:49:17'),
(7, '', 2, 1, 1, 1, '2020-09-15 01:38:40', 0, '2020-09-15 01:38:40', '2020-09-15 01:38:40', 7, 1, '2020-09-15 13:49:17', '2020-09-15 13:49:17'),
(8, '', 2, 1, 1, 0, '2020-09-15 01:38:53', 0, '2020-09-15 01:38:53', '2020-09-15 01:38:53', 8, 1, '2020-09-15 13:49:17', '2020-09-15 13:49:17');

-- --------------------------------------------------------

--
-- Table structure for table `attributes`
--

CREATE TABLE `attributes` (
  `attribute_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ca_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_default` tinyint(4) NOT NULL DEFAULT 0 COMMENT '1 For Default,0 For not Default',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 For InActive, 1 For Active',
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attributes`
--

INSERT INTO `attributes` (`attribute_id`, `uuid`, `ca_id`, `name`, `is_default`, `status`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
(1, 'bd5acfba-ec52-11ea-be97-00163e24cc89', 1, 'Xs', 1, 1, '2020-09-01 12:57:56', 1, NULL, NULL),
(2, '7707aec3-f247-11ea-be97-00163e24cc89', 1, 'S', 0, 1, '2020-09-09 10:52:14', 1, NULL, NULL),
(3, '7b44ecdf-f247-11ea-be97-00163e24cc89', 2, 'Small', 0, 1, '2020-09-09 10:52:21', 1, NULL, NULL),
(4, '7eb627e1-f247-11ea-be97-00163e24cc89', 2, 'Big', 0, 1, '2020-09-09 10:52:27', 1, NULL, NULL),
(5, '8dc27c58-f247-11ea-be97-00163e24cc89', 3, 'Extra Spicy', 0, 1, '2020-09-09 10:52:52', 1, NULL, NULL),
(6, '94b34026-f247-11ea-be97-00163e24cc89', 4, 'Spicy', 0, 1, '2020-09-09 10:53:03', 1, NULL, NULL),
(7, 'b730b782-fd58-11ea-8767-082e5f2acf88', 3, 'Medium Spicy', 0, 1, '2020-09-23 12:53:26', 1, NULL, NULL),
(8, '521b08c5-fd67-11ea-8767-082e5f2acf88', 3, 'dfsgdf', 0, 2, '2020-09-23 15:43:37', 1, '2020-09-23 10:13:37', 1);

-- --------------------------------------------------------

--
-- Table structure for table `banner`
--

CREATE TABLE `banner` (
  `banner_id` int(10) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `banner_for_mobile` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `banner_for_web` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` datetime NOT NULL,
  `updated_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `box`
--

CREATE TABLE `box` (
  `box_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `rac_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `box_limit` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 For disabled, 1 For enabled,2 For deleted',
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branch`
--

CREATE TABLE `branch` (
  `branch_id` smallint(5) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `open_from` time NOT NULL,
  `closed_on` time NOT NULL,
  `tax` int(11) NOT NULL,
  `branch_banner` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `longitude` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 For InActive, 1 For Active',
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `order_prefix` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_start` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branch`
--

INSERT INTO `branch` (`branch_id`, `uuid`, `name`, `slug`, `address`, `contact_no`, `email`, `contact_person`, `open_from`, `closed_on`, `tax`, `branch_banner`, `latitude`, `longitude`, `status`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`, `order_prefix`, `invoice_start`) VALUES
(1, 'fe7a0a1b-eb47-11ea-b7e8-082e5f2acf88', 'Branch1', 'branch1', 'Ahmedabad', '8752316486', 'branch@gmail.com', 'Smith', '10:30:00', '14:30:00', 5, 'uploads/branch_banner/1598850505.jpg', '23.0225', '72.5714', 1, '2020-09-22 12:48:04', 1, NULL, NULL, 'BC', '0000'),
(2, '609f2780-ec15-11ea-b0fa-082e5f2acf88', 'Branch2\'d', 'branch2d', 'Ahmedabad', '9563215201', 'branch2@gmail.com', 'John', '11:07:00', '15:07:00', 2, 'uploads/branch_banner/1598938714.jpg', '23.0225', '72.5714', 1, '2020-09-22 12:45:55', 1, NULL, NULL, 'BC', '0000');

-- --------------------------------------------------------

--
-- Table structure for table `branch_tax`
--

CREATE TABLE `branch_tax` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tax_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `rate` decimal(10,2) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 For disabled, 1 For enabled,2 For Deleted',
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branch_tax`
--

INSERT INTO `branch_tax` (`id`, `tax_id`, `branch_id`, `rate`, `status`, `updated_at`, `updated_by`) VALUES
(1, 1, 2, '7.00', 1, '2020-09-22 12:45:55', 1),
(2, 1, 1, '7.00', 1, '2020-09-22 12:48:04', 1);

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `localID` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `device_id` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `table_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `sub_total` double(15,2) NOT NULL,
  `discount` double(15,2) NOT NULL,
  `discount_type` int(11) DEFAULT NULL,
  `voucher_id` int(11) DEFAULT NULL,
  `voucher_detail` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sub_total_after_discount` double(10,2) DEFAULT NULL,
  `source` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1 For WEB, 2 For APP',
  `total_item` int(11) DEFAULT NULL,
  `cart_payment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `cart_payment_response` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cart_payment_status` tinyint(4) NOT NULL COMMENT '0 For Pending, 1 Complete',
  `remark` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax` double(15,2) NOT NULL,
  `tax_json` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grand_total` double(15,2) NOT NULL,
  `total_qty` double(10,2) NOT NULL,
  `customer_terminal` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 For No, 1 For Yes',
  `created_at` datetime DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `uuid`, `localID`, `user_id`, `device_id`, `branch_id`, `table_id`, `product_id`, `sub_total`, `discount`, `discount_type`, `voucher_id`, `voucher_detail`, `sub_total_after_discount`, `source`, `total_item`, `cart_payment_id`, `cart_payment_response`, `cart_payment_status`, `remark`, `tax`, `tax_json`, `grand_total`, `total_qty`, `customer_terminal`, `is_deleted`, `created_at`, `created_by`) VALUES
(6, '9dd9a347-ff34-11ea-bd8c-082e5f2acf88', NULL, NULL, 'vLyJg0wyfdzo1nE3j5c3vFTlTItDyNJW1601015449', 1, NULL, 4, 526.00, 0.00, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, 0, NULL, 36.82, '[{\"id\":1,\"tax_id\":1,\"branch_id\":2,\"rate\":\"7.00\",\"status\":1,\"updated_at\":\"2020-09-22 12:45:55\",\"updated_by\":1,\"taxAmount\":36.82,\"taxCode\":\"servicetax\"}]', 562.82, 5.00, NULL, 0, '2020-09-25 21:10:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cart_detail`
--

CREATE TABLE `cart_detail` (
  `cart_detail_id` bigint(20) UNSIGNED NOT NULL,
  `cart_id` bigint(20) UNSIGNED NOT NULL,
  `localID` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_price` double(10,2) NOT NULL,
  `product_old_price` bigint(20) UNSIGNED DEFAULT NULL,
  `product_qty` double(10,2) NOT NULL,
  `product_detail` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_id` int(11) DEFAULT NULL,
  `tax_value` bigint(20) UNSIGNED DEFAULT NULL,
  `discount` bigint(20) UNSIGNED DEFAULT NULL,
  `discount_type` int(11) DEFAULT NULL,
  `remark` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_deleted` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 For No, 1 For Yes',
  `is_send_kichen` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 For No, 1 For Yes',
  `has_composite_inventory` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 For No, 1 For Yes',
  `item_unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart_detail`
--

INSERT INTO `cart_detail` (`cart_detail_id`, `cart_id`, `localID`, `product_id`, `product_name`, `product_price`, `product_old_price`, `product_qty`, `product_detail`, `tax_id`, `tax_value`, `discount`, `discount_type`, `remark`, `is_deleted`, `is_send_kichen`, `has_composite_inventory`, `item_unit`, `created_at`, `created_by`) VALUES
(18, 6, NULL, 4, 'Oranges', 100.00, 120, 1.00, '{\"product_id\":4,\"uuid\":\"ced0522f-ec59-11ea-b0fa-082e5f2acf88\",\"name\":\"Oranges\",\"slug\":\"oranges\",\"description\":\"<p><strong>Lorem Ipsum<\\/strong>&nbsp;is simply dummy text of the printing and typesetting industry<\\/p>\",\"sku\":\"451\",\"price_type_id\":1,\"price_type_value\":\"1 KG\",\"price\":100,\"old_price\":120,\"has_inventory\":1,\"has_rac_managemant\":0,\"status\":1,\"updated_at\":\"2020-09-23 16:31:41\",\"deleted_at\":null,\"updated_by\":1,\"deleted_by\":null}', NULL, NULL, 0, NULL, NULL, 0, 0, 0, NULL, '2020-09-25 21:10:43', NULL),
(21, 6, NULL, 11, 'Fish', 100.00, NULL, 2.00, '{\"product_id\":11,\"uuid\":\"5ad29225-fe30-11ea-b53c-082e5f2acf88\",\"name\":\"Fish\",\"slug\":\"fish\",\"description\":\"<p>tesst<\\/p>\",\"sku\":\"fish\",\"price_type_id\":1,\"price_type_value\":\"1\",\"price\":100,\"old_price\":null,\"has_inventory\":1,\"has_rac_managemant\":0,\"status\":1,\"updated_at\":\"2020-09-24 19:24:49\",\"deleted_at\":null,\"updated_by\":1,\"deleted_by\":null}', NULL, NULL, 0, NULL, NULL, 0, 0, 0, NULL, '2020-09-25 21:10:43', NULL),
(22, 6, NULL, 4, 'Oranges', 100.00, 120, 1.00, '{\"product_id\":4,\"uuid\":\"ced0522f-ec59-11ea-b0fa-082e5f2acf88\",\"name\":\"Oranges\",\"slug\":\"oranges\",\"description\":\"<p><strong>Lorem Ipsum<\\/strong>&nbsp;is simply dummy text of the printing and typesetting industry<\\/p>\",\"sku\":\"451\",\"price_type_id\":1,\"price_type_value\":\"1 KG\",\"price\":100,\"old_price\":120,\"has_inventory\":1,\"has_rac_managemant\":0,\"status\":1,\"updated_at\":\"2020-09-23 16:31:41\",\"deleted_at\":null,\"updated_by\":1,\"deleted_by\":null}', NULL, NULL, 0, NULL, NULL, 0, 0, 0, NULL, '2020-09-25 21:10:43', NULL),
(23, 6, NULL, 4, 'Oranges', 100.00, 120, 1.00, '{\"product_id\":4,\"uuid\":\"ced0522f-ec59-11ea-b0fa-082e5f2acf88\",\"name\":\"Oranges\",\"slug\":\"oranges\",\"description\":\"<p><strong>Lorem Ipsum<\\/strong>&nbsp;is simply dummy text of the printing and typesetting industry<\\/p>\",\"sku\":\"451\",\"price_type_id\":1,\"price_type_value\":\"1 KG\",\"price\":100,\"old_price\":120,\"has_inventory\":1,\"has_rac_managemant\":0,\"status\":1,\"updated_at\":\"2020-09-23 16:31:41\",\"deleted_at\":null,\"updated_by\":1,\"deleted_by\":null}', NULL, NULL, 0, NULL, NULL, 0, 0, 0, NULL, '2020-09-25 21:10:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cart_sub_detail`
--

CREATE TABLE `cart_sub_detail` (
  `csd_id` bigint(20) UNSIGNED NOT NULL,
  `cart_detail_id` bigint(20) UNSIGNED NOT NULL,
  `cart_id` bigint(20) UNSIGNED NOT NULL,
  `localID` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `modifier_id` int(11) DEFAULT NULL,
  `modifire_price` double(10,2) DEFAULT NULL,
  `attribute_id` int(11) DEFAULT NULL,
  `attribute_price` double(10,2) DEFAULT NULL,
  `ca_id` int(11) DEFAULT NULL,
  `is_deleted` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 For No, 1 For Yes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart_sub_detail`
--

INSERT INTO `cart_sub_detail` (`csd_id`, `cart_detail_id`, `cart_id`, `localID`, `product_id`, `modifier_id`, `modifire_price`, `attribute_id`, `attribute_price`, `ca_id`, `is_deleted`) VALUES
(29, 18, 6, NULL, 4, 2, 26.00, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` bigint(20) NOT NULL,
  `is_for_web` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 for Backend 1 for Web',
  `has_rac_managemant` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 For No, 1 For Yes',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1 For Active,0 For De-active',
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `uuid`, `name`, `slug`, `category_icon`, `parent_id`, `is_for_web`, `has_rac_managemant`, `status`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
(1, '267d3834-ec4e-11ea-be97-00163e24cc89', 'Beer', 'beer', 'uploads/category/1600857597.png', 0, 1, 1, 1, '2020-09-24 14:33:04', 1, NULL, NULL),
(2, '3acc0451-ec4e-11ea-be97-00163e24cc89', 'Drink', 'drink', 'uploads/category/1600856668.png', 0, 1, 0, 1, '2020-09-24 14:33:13', 1, NULL, NULL),
(3, '6c2bd4a6-ec4e-11ea-be97-00163e24cc89', 'Salad', 'salad', 'uploads/category/1600856678.png', 1, 1, 0, 1, '2020-09-24 14:33:21', 1, NULL, NULL),
(4, '2b84dac5-f0d7-11ea-be97-00163e24cc89', 'Cold Drink', 'cold-drink', 'uploads/category/1600856686.png', 2, 1, 0, 1, '2020-09-23 18:24:46', 2, NULL, NULL),
(5, '61e6f0a0-f129-11ea-be97-00163e24cc89', 'Rice', 'rice', 'uploads/category/1600856697.png', 1, 1, 0, 1, '2020-09-23 18:24:57', 2, NULL, NULL),
(6, 'cdcd5b7a-f1c7-11ea-be97-00163e24cc89', 'Sushi', 'sushi', 'uploads/category/1600856713.png', 0, 1, 0, 1, '2020-09-23 18:25:13', 2, NULL, NULL),
(7, '200d6191-f265-11ea-be97-00163e24cc89', 'test', 'test', 'uploads/category/1599632673.jpg', 0, 1, 0, 1, NULL, 2, '2020-09-09 12:05:32', 1),
(8, 'b334528c-f27f-11ea-be97-00163e24cc89', 'test1', 'test1', 'uploads/category/1599644086.jpg', 0, 1, 0, 1, NULL, 2, '2020-09-09 12:05:14', 1),
(9, '5fda985e-f281-11ea-be97-00163e24cc89', 'Ice cream', 'ice-cream', 'uploads/category/1600857326.png', 1, 1, 0, 1, '2020-09-23 18:35:26', 2, NULL, NULL),
(10, '2499843e-f74b-11ea-be97-00163e24cc89', 'Fish', 'fish', 'uploads/category/1600857582.png', 0, 1, 1, 1, '2020-09-23 18:39:42', 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `category_attribute`
--

CREATE TABLE `category_attribute` (
  `ca_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 For disabled, 1 For enabled',
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category_attribute`
--

INSERT INTO `category_attribute` (`ca_id`, `uuid`, `name`, `slug`, `status`, `updated_at`, `updated_by`) VALUES
(1, 'e4c70ef6-053d-11ea-b86f-f8852fd8996b', 'Spicy', 'spicy', 0, '2020-09-09 15:33:07', 1),
(2, 'e4c70ef6-053d-11ea-b86f-f8852fs7886b', 'Spicy paste', 'spicy paste', 1, '2020-09-09 11:39:00', 1),
(3, 'cdde97a9-a4cc-11ea-bf2c-00163e28gg98', 'Medium', 'medium', 1, '2020-09-09 12:12:01', 1),
(4, 'e4c70ef6-053d-11ea-b87d-d8826sf7906d', 'L', 'l', 1, '2020-09-09 12:14:11', 1),
(5, 'c474d498-f4d8-11ea-8db3-082e5f2acf88', 'Colors', 'colors', 1, '2020-09-12 17:17:24', 1);

-- --------------------------------------------------------

--
-- Table structure for table `category_branch`
--

CREATE TABLE `category_branch` (
  `cb_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` smallint(5) UNSIGNED NOT NULL,
  `display_order` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1 For Active,0 For De-active	2 For delete',
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category_branch`
--

INSERT INTO `category_branch` (`cb_id`, `uuid`, `category_id`, `branch_id`, `display_order`, `status`, `updated_at`, `updated_by`) VALUES
(3, '57ea44d1-ec1f-11ea-b0fa-082e5f2acf88', 6, 2, 1, 1, '2020-09-12 14:41:33', 1),
(4, 'ccaa73f2-fe2f-11ea-b53c-082e5f2acf88', 1, 1, 1, 1, '2020-09-24 14:33:04', 1),
(5, 'ccab3424-fe2f-11ea-b53c-082e5f2acf88', 1, 2, 2, 1, '2020-09-24 14:33:04', 1),
(6, 'd23cf5a4-fe2f-11ea-b53c-082e5f2acf88', 2, 1, 1, 1, '2020-09-24 14:33:13', 1),
(7, 'd23db590-fe2f-11ea-b53c-082e5f2acf88', 2, 2, 1, 1, '2020-09-24 14:33:13', 1),
(8, 'd71ac171-fe2f-11ea-b53c-082e5f2acf88', 3, 1, 1, 1, '2020-09-24 14:33:21', 1),
(9, 'd71b666d-fe2f-11ea-b53c-082e5f2acf88', 3, 2, 2, 1, '2020-09-24 14:33:21', 1);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `app_id` int(11) DEFAULT NULL,
  `terminal_id` int(11) DEFAULT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` smallint(5) UNSIGNED NOT NULL,
  `phonecode` int(11) DEFAULT NULL,
  `mobile` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `state_id` int(11) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `zipcode` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 For InActive, 1 For Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `uuid`, `app_id`, `terminal_id`, `first_name`, `last_name`, `name`, `username`, `email`, `role`, `phonecode`, `mobile`, `password`, `address`, `country_id`, `state_id`, `city_id`, `zipcode`, `api_token`, `profile`, `last_login`, `status`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`, `deleted_by`) VALUES
(1, '0199cd96-f104-11ea-9f3f-082e5f2acf88', NULL, NULL, NULL, NULL, 'Leham', 'Leham', 'lehan@mailinator.com', 2, NULL, '4465236586', '$2y$10$as6h6YVdL0LuTFiE4KLXQujNBDFOzb0adekBt8n8xFhRPta4VMtVC', NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/customers/1599481013.jpg', '2020-09-24 12:50:53', 2, '2020-09-07 14:46:53', '2020-09-24 08:00:10', NULL, 1, 1, NULL),
(2, '51ab89dd-f1c3-11ea-9f3f-082e5f2acf88', NULL, NULL, NULL, NULL, 'Bill John', 'billJohn', 'Bill@gmail.com', 2, NULL, '4465236568', '$2y$10$y7DcAnXooVA6f4bzCzTxxehHdqWemsf6kust1OHvm2jqhRdi7tuza', NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/customers/1599563184.jpg', NULL, 1, '2020-09-08 13:36:25', '2020-09-08 13:36:24', NULL, 1, 1, NULL),
(3, 'a8328ead-f1c3-11ea-9f3f-082e5f2acf88', NULL, NULL, NULL, NULL, 'jack', 'Jack', 'jack@mailinator.com', 2, NULL, '6523124578', '$2y$10$eQM9XWRjNXrVaF6qzW4.y.gS8KstYu5p6u7VCkYePdWohVlSvBluu', NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/customers/1599563330.jpg', NULL, 1, '2020-09-08 13:38:50', '2020-09-08 13:38:50', NULL, 1, 1, NULL),
(14, '1163297d-fe67-11ea-b53c-082e5f2acf88', NULL, NULL, NULL, NULL, 'Jacky', 'Jacky', 'jacky@mailinator.com', 2, NULL, '8520134697', '$2y$10$liDEbH2.i0aq7aC4Lhgp1OrAluvP6xN7A9u4RPqSGLmTx3KyWDx26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-09-24 13:51:10', 1, '2020-09-24 08:21:10', '2020-09-24 15:38:43', NULL, 14, 14, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customer_address`
--

CREATE TABLE `customer_address` (
  `address_id` bigint(20) UNSIGNED NOT NULL,
  `app_id` bigint(20) DEFAULT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `address_line1` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `address_line2` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `longitude` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_default` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1 For Yes, 0 For No',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1 For Active, 2 For InActive',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_address`
--

INSERT INTO `customer_address` (`address_id`, `app_id`, `uuid`, `user_id`, `address_line1`, `address_line2`, `latitude`, `longitude`, `is_default`, `status`, `deleted_at`, `updated_at`, `updated_by`) VALUES
(1, NULL, '434fc557-f1c0-11ea-9f3f-082e5f2acf88', 1, 'sfg', 'njmghjhg', '1.02300000', '23.32000000', 1, 1, '2020-09-08 13:20:30', '2020-09-08 17:02:03', 1),
(2, NULL, '18e8d93b-f1c1-11ea-9f3f-082e5f2acf88', 1, 'sfg', 'njmghjhg', '1.02300000', '23.32000000', 0, 1, '2020-09-08 13:20:37', '2020-09-08 17:02:03', 1),
(3, NULL, '1d139009-f1c1-11ea-9f3f-082e5f2acf88', 1, 'sfg', 'njmghjhg', '1.02300000', '23.32000000', 1, 1, '2020-09-08 13:24:20', '2020-09-08 17:02:03', 1),
(4, NULL, 'a1feebea-f1c1-11ea-9f3f-082e5f2acf88', 1, 'sfg', 'njmghjhg', '1.02300000', '23.32000000', 1, 1, '2020-09-08 13:30:50', '2020-09-08 17:02:03', 1),
(5, NULL, 'a1ffce3d-f1c1-11ea-9f3f-082e5f2acf88', 1, 'malacca', NULL, '2.1896', '102.2501', 0, 1, '2020-09-08 13:30:50', '2020-09-08 17:02:03', 1),
(6, NULL, '8a755688-f1c2-11ea-9f3f-082e5f2acf88', 1, 'sfg', 'njmghjhg', '1.02300000', '23.32000000', 0, 1, '2020-09-08 13:31:04', '2020-09-08 17:02:03', 1),
(7, NULL, '8a7657ac-f1c2-11ea-9f3f-082e5f2acf88', 1, 'malacca', NULL, '2.1896', '102.2501', 1, 2, '2020-09-08 13:31:04', '2020-09-08 17:02:03', 1),
(8, NULL, '927d239f-f1c2-11ea-9f3f-082e5f2acf88', 1, 'malacca', NULL, '2.1896', '102.2501', 1, 1, '2020-09-08 13:31:54', '2020-09-08 17:02:03', 1),
(9, NULL, 'b05e1358-f1c2-11ea-9f3f-082e5f2acf88', 1, 'malacca', NULL, '2.1896', '102.2501', 1, 1, NULL, '2020-09-08 17:02:03', 1);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kitchen_department`
--

CREATE TABLE `kitchen_department` (
  `kitchen_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` smallint(5) UNSIGNED NOT NULL,
  `kitchen_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kitchen_printer_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 For InActive, 1 For Active',
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

CREATE TABLE `language` (
  `language_id` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_id` smallint(6) NOT NULL,
  `icon` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency_sign` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `language`
--

INSERT INTO `language` (`language_id`, `name`, `code`, `country_id`, `icon`, `currency`, `currency_sign`, `created_at`, `updated_at`) VALUES
(1, 'English', 'en', 230, 'images/languages/eng.png', 'USD', '$', NULL, NULL),
(2, 'Chinese', 'ch', 191, 'images/languages/chn.png', 'SR', 'SR', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE `log` (
  `log_id` bigint(20) UNSIGNED NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1 for admin,2 for web,3 for app',
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `function` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `details` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `log`
--

INSERT INTO `log` (`log_id`, `type`, `file_name`, `function`, `details`, `created_by`, `ip_address`, `created_at`, `updated_at`) VALUES
(1, 1, 'Modifier', 'Update', 'Update Modifier acf5beec-ec14-11ea-b0fa-082e5f2acf88', 1, '::1', '2020-09-09 15:10:15', '2020-09-09 15:10:15'),
(2, 1, 'Modifier', 'Store', 'Add new Modifier 99b2fb5c-f299-11ea-9f3f-082e5f2acf88', 1, '::1', '2020-09-09 15:10:21', '2020-09-09 15:10:21'),
(3, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-10 07:15:06', '2020-09-10 07:15:06'),
(4, 1, 'Terminal', 'Store', 'Add new terminal Exception SQLSTATE[23000]: Integrity constraint violation: 1048 Column \'status\' cannot be null (SQL: insert into `terminal` (`uuid`, `terminal_device_id`, `branch_id`, `terminal_name`, `terminal_key`, `terminal_type`, `terminal_is_mother`, `status`, `updated_at`, `updated_by`) values (3a9b7045-f32f-11ea-bc98-082e5f2acf88, ?, 1, Attendance Terminal, fLQQ, 3, 0, ?, 2020-09-10 12:47:53, 1))', 1, '::1', '2020-09-10 09:01:16', '2020-09-10 09:01:16'),
(5, 1, 'Terminal', 'Store', 'Add new Terminal 3e3fe880-f32f-11ea-bc98-082e5f2acf88', 1, '::1', '2020-09-10 09:01:22', '2020-09-10 09:01:22'),
(6, 1, 'Customers', 'Destroy', 'Destroy Customers 0199cd96-f104-11ea-9f3f-082e5f2acf88', 1, '::1', '2020-09-10 09:52:58', '2020-09-10 09:52:58'),
(7, 1, 'Terminal', 'Update', 'Update Terminal 3e3fe880-f32f-11ea-bc98-082e5f2acf88', 1, '::1', '2020-09-10 10:56:14', '2020-09-10 10:56:14'),
(8, 1, 'Tax', 'Store', 'Add new Tax 2b33ae69-f363-11ea-bc98-082e5f2acf88', 1, '::1', '2020-09-10 15:13:05', '2020-09-10 15:13:05'),
(9, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-11 06:22:52', '2020-09-11 06:22:52'),
(10, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-11 15:54:30', '2020-09-11 15:54:30'),
(11, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-11 16:04:12', '2020-09-11 16:04:12'),
(12, 1, 'Role', 'Update', 'Update role 3', 1, '::1', '2020-09-11 16:05:18', '2020-09-11 16:05:18'),
(13, 1, 'Role', 'Update', 'Update role 3', 1, '::1', '2020-09-11 16:05:25', '2020-09-11 16:05:25'),
(14, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-12 09:11:13', '2020-09-12 09:11:13'),
(15, 1, 'Category', 'Update', 'Update category 6', 1, '::1', '2020-09-12 09:11:33', '2020-09-12 09:11:33'),
(16, 1, 'Role', 'Update', 'Update role 3', 1, '::1', '2020-09-12 09:12:32', '2020-09-12 09:12:32'),
(17, 1, 'Role', 'Update', 'Update role 3', 1, '::1', '2020-09-12 09:12:42', '2020-09-12 09:12:42'),
(18, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-12 10:13:23', '2020-09-12 10:13:23'),
(19, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-12 11:26:17', '2020-09-12 11:26:17'),
(20, 1, 'CategoryAttribute', 'Store', 'Add new category attribute c474d498-f4d8-11ea-8db3-082e5f2acf88', 1, '::1', '2020-09-12 11:47:24', '2020-09-12 11:47:24'),
(21, 1, 'Product', 'Update', 'Update product 5', 1, '::1', '2020-09-12 12:05:39', '2020-09-12 12:05:39'),
(22, 1, 'Product', 'Update', 'Update product 5', 1, '::1', '2020-09-12 12:10:10', '2020-09-12 12:10:10'),
(23, 1, 'Product', 'Update', 'Update product 5', 1, '::1', '2020-09-12 12:18:05', '2020-09-12 12:18:05'),
(24, 1, 'Product', 'Update', 'Update product 5', 1, '::1', '2020-09-12 12:18:21', '2020-09-12 12:18:21'),
(25, 1, 'Product', 'Update', 'Update product 5', 1, '::1', '2020-09-12 12:48:28', '2020-09-12 12:48:28'),
(26, 1, 'Product', 'Update', 'Update product 5', 1, '::1', '2020-09-12 12:52:15', '2020-09-12 12:52:15'),
(27, 1, 'Product', 'Store', 'Add new product Exception Undefined offset: 2', 1, '::1', '2020-09-12 13:07:38', '2020-09-12 13:07:38'),
(28, 1, 'Product', 'Store', 'Add new product Exception Undefined offset: 2', 1, '::1', '2020-09-12 13:07:54', '2020-09-12 13:07:54'),
(29, 1, 'Product', 'Store', 'Add new product Exception Undefined offset: 0', 1, '::1', '2020-09-12 13:08:13', '2020-09-12 13:08:13'),
(30, 1, 'Product', 'Update', 'Update product 5', 1, '::1', '2020-09-12 13:56:02', '2020-09-12 13:56:02'),
(31, 1, 'Product', 'Update', 'Update product 5', 1, '::1', '2020-09-12 14:11:01', '2020-09-12 14:11:01'),
(32, 1, 'Product', 'Update', 'Update product 5', 1, '::1', '2020-09-12 14:30:35', '2020-09-12 14:30:35'),
(33, 1, 'Product', 'Update', 'Update product 5', 1, '::1', '2020-09-12 14:31:22', '2020-09-12 14:31:22'),
(34, 1, 'Product', 'Update', 'Update product 4', 1, '::1', '2020-09-12 14:31:35', '2020-09-12 14:31:35'),
(35, 1, 'Product', 'Update', 'Update product 5', 1, '::1', '2020-09-12 14:31:52', '2020-09-12 14:31:52'),
(36, 1, 'Product', 'Update', 'Update product 5', 1, '::1', '2020-09-12 14:56:19', '2020-09-12 14:56:19'),
(37, 1, 'Product', 'Update', 'Update product 5', 1, '::1', '2020-09-12 15:10:48', '2020-09-12 15:10:48'),
(38, 1, 'Product', 'Update', 'Update product 5', 1, '::1', '2020-09-12 15:11:00', '2020-09-12 15:11:00'),
(39, 1, 'Product', 'Store', 'Add new product 10', 1, '::1', '2020-09-12 15:13:27', '2020-09-12 15:13:27'),
(40, 1, 'Product', 'Update', 'Update product 10', 1, '::1', '2020-09-12 15:13:45', '2020-09-12 15:13:45'),
(41, 1, 'Product', 'Update', 'Update product 10', 1, '::1', '2020-09-12 15:15:23', '2020-09-12 15:15:23'),
(42, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-14 06:39:00', '2020-09-14 06:39:00'),
(43, 1, 'Product', 'Update', 'Update product 4', 1, '::1', '2020-09-14 07:26:44', '2020-09-14 07:26:44'),
(44, 1, 'Product', 'Update', 'Update product 10', 1, '::1', '2020-09-14 08:07:26', '2020-09-14 08:07:26'),
(45, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-14 11:17:07', '2020-09-14 11:17:07'),
(46, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-14 16:02:36', '2020-09-14 16:02:36'),
(47, 1, 'Table', 'Store', 'Add new table ce78b8e9-f68e-11ea-8db3-082e5f2acf88', 1, '::1', '2020-09-14 16:03:10', '2020-09-14 16:03:10'),
(48, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-15 08:53:44', '2020-09-15 08:53:44'),
(49, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-15 12:09:23', '2020-09-15 12:09:23'),
(50, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-15 14:13:34', '2020-09-15 14:13:34'),
(51, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-15 15:38:38', '2020-09-15 15:38:38'),
(52, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-16 07:41:27', '2020-09-16 07:41:27'),
(53, 1, 'Branch', 'Update', 'Update Branch 609f2780-ec15-11ea-b0fa-082e5f2acf88', 1, '::1', '2020-09-16 07:48:21', '2020-09-16 07:48:21'),
(54, 1, 'Branch', 'Update', 'Update Branch 609f2780-ec15-11ea-b0fa-082e5f2acf88', 1, '::1', '2020-09-16 11:16:07', '2020-09-16 11:16:07'),
(55, 1, 'Branch', 'Update', 'Update Branch fe7a0a1b-eb47-11ea-b7e8-082e5f2acf88', 1, '::1', '2020-09-16 11:16:19', '2020-09-16 11:16:19'),
(56, 1, 'Branch', 'Update', 'Update Branch 609f2780-ec15-11ea-b0fa-082e5f2acf88', 1, '::1', '2020-09-16 11:26:59', '2020-09-16 11:26:59'),
(57, 1, 'Product', 'Update', 'Update product 10', 1, '::1', '2020-09-16 11:43:56', '2020-09-16 11:43:56'),
(58, 1, 'Product', 'Update', 'Update product 10', 1, '::1', '2020-09-16 11:44:15', '2020-09-16 11:44:15'),
(59, 1, 'Voucher', 'Update', 'Update Voucher b807aa42-f1b4-11ea-9f3f-082e5f2acf88', 1, '::1', '2020-09-16 14:51:13', '2020-09-16 14:51:13'),
(60, 1, 'Voucher', 'Update', 'Update Voucher b807aa42-f1b4-11ea-9f3f-082e5f2acf88', 1, '::1', '2020-09-16 14:51:46', '2020-09-16 14:51:46'),
(61, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-16 20:00:47', '2020-09-16 20:00:47'),
(62, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-17 08:28:44', '2020-09-17 08:28:44'),
(63, 1, 'Product', 'Update', 'Update product 10', 1, '::1', '2020-09-17 08:40:26', '2020-09-17 08:40:26'),
(64, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-18 07:31:34', '2020-09-18 07:31:34'),
(65, 1, 'Price-type', 'Update', 'Update price type 7d4c3a01-ec14-11ea-b0fa-082e5f2acf88', 1, '::1', '2020-09-18 07:55:23', '2020-09-18 07:55:23'),
(66, 1, 'Price-type', 'Update', 'Update price type 7d4c3a01-ec14-11ea-b0fa-082e5f2acf88', 1, '::1', '2020-09-18 07:55:28', '2020-09-18 07:55:28'),
(67, 1, 'Price-type', 'Update', 'Update price type 7d4c3a01-ec14-11ea-b0fa-082e5f2acf88', 1, '::1', '2020-09-18 07:55:35', '2020-09-18 07:55:35'),
(68, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-18 13:26:42', '2020-09-18 13:26:42'),
(69, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-18 13:26:55', '2020-09-18 13:26:55'),
(70, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-18 16:06:51', '2020-09-18 16:06:51'),
(71, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-19 06:26:01', '2020-09-19 06:26:01'),
(72, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-21 07:20:20', '2020-09-21 07:20:20'),
(73, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-21 07:27:23', '2020-09-21 07:27:23'),
(74, 1, 'Users', 'Update', 'Update User 2afa874e-f1a4-11ea-9f3f-082e5f2acf88', 1, '::1', '2020-09-21 09:14:32', '2020-09-21 09:14:32'),
(75, 1, 'Users', 'Update', 'Update User 2afa874e-f1a4-11ea-9f3f-082e5f2acf88', 1, '::1', '2020-09-21 09:29:12', '2020-09-21 09:29:12'),
(76, 1, 'Users', 'Update', 'Update User 2afa874e-f1a4-11ea-9f3f-082e5f2acf88', 1, '::1', '2020-09-21 09:29:59', '2020-09-21 09:29:59'),
(77, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-21 11:46:10', '2020-09-21 11:46:10'),
(78, 1, 'Voucher', 'Store', 'Add new Voucher Exception SQLSTATE[23000]: Integrity constraint violation: 1048 Column \'voucher_code\' cannot be null (SQL: insert into `voucher` (`uuid`, `voucher_name`, `voucher_code`, `voucher_banner`, `voucher_discount_type`, `voucher_discount`, `minimum_amount`, `maximum_amount`, `uses_total`, `uses_customer`, `voucher_applicable_from`, `voucher_applicable_to`, `voucher_categories`, `voucher_products`, `status`, `updated_at`, `updated_by`) values (31b95e1e-fbec-11ea-b4b3-082e5f2acf88, , ?, , ?, ?, ?, 0, 0, 0, 1970-01-01, 1970-01-01, , , ?, 2020-09-21 17:24:15, 1))', 1, '::1', '2020-09-21 11:54:15', '2020-09-21 11:54:15'),
(79, 1, 'Voucher', 'Store', 'Add new Voucher Exception SQLSTATE[23000]: Integrity constraint violation: 1048 Column \'voucher_code\' cannot be null (SQL: insert into `voucher` (`uuid`, `voucher_name`, `voucher_code`, `voucher_banner`, `voucher_discount_type`, `voucher_discount`, `minimum_amount`, `maximum_amount`, `uses_total`, `uses_customer`, `voucher_applicable_from`, `voucher_applicable_to`, `voucher_categories`, `voucher_products`, `status`, `updated_at`, `updated_by`) values (35bfc47c-fbec-11ea-b4b3-082e5f2acf88, , ?, , ?, ?, ?, 0, 0, 0, 1970-01-01, 1970-01-01, , , ?, 2020-09-21 17:24:22, 1))', 1, '::1', '2020-09-21 11:54:22', '2020-09-21 11:54:22'),
(80, 1, 'Voucher', 'Store', 'Add new Voucher Exception Invalid argument supplied for foreach()', 1, '::1', '2020-09-21 12:21:50', '2020-09-21 12:21:50'),
(81, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-21 15:04:51', '2020-09-21 15:04:51'),
(82, 1, 'Voucher', 'Update', 'Update Voucher b807aa42-f1b4-11ea-9f3f-082e5f2acf88', 1, '::1', '2020-09-21 15:06:58', '2020-09-21 15:06:58'),
(83, 1, 'Voucher', 'Update', 'Update Voucher b807aa42-f1b4-11ea-9f3f-082e5f2acf88', 1, '::1', '2020-09-21 15:13:54', '2020-09-21 15:13:54'),
(84, 1, 'Voucher', 'Update', 'Update Voucher b807aa42-f1b4-11ea-9f3f-082e5f2acf88', 1, '::1', '2020-09-21 15:14:20', '2020-09-21 15:14:20'),
(85, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-22 07:05:42', '2020-09-22 07:05:42'),
(86, 1, 'Branch', 'Update', 'Update Branch Exception SQLSTATE[42S22]: Column not found: 1054 Unknown column \'order_prefix\' in \'field list\' (SQL: update `branch` set `name` = Branch2\'d, `slug` = branch2d, `address` = Ahmedabad, `contact_no` = 9563215201, `email` = branch2@gmail.com, `contact_person` = John, `open_from` = 11:07:00, `closed_on` = 15:07:00, `tax` = 2, `latitude` = 23.0225, `longitude` = 72.5714, `status` = 1, `order_prefix` = BC, `invoice_start` = 0000, `updated_at` = 2020-09-22 12:37:19, `updated_by` = 1 where `uuid` = 609f2780-ec15-11ea-b0fa-082e5f2acf88 and `branch`.`deleted_at` is null)', 1, '::1', '2020-09-22 07:07:19', '2020-09-22 07:07:19'),
(87, 1, 'Branch', 'Update', 'Update Branch Exception SQLSTATE[42S22]: Column not found: 1054 Unknown column \'order_prefix\' in \'field list\' (SQL: update `branch` set `name` = Branch2\'d, `slug` = branch2d, `address` = Ahmedabad, `contact_no` = 9563215201, `email` = branch2@gmail.com, `contact_person` = John, `open_from` = 11:07:00, `closed_on` = 15:07:00, `tax` = 2, `latitude` = 23.0225, `longitude` = 72.5714, `status` = 1, `order_prefix` = BC, `invoice_start` = 0000, `updated_at` = 2020-09-22 12:37:43, `updated_by` = 1 where `uuid` = 609f2780-ec15-11ea-b0fa-082e5f2acf88 and `branch`.`deleted_at` is null)', 1, '::1', '2020-09-22 07:07:43', '2020-09-22 07:07:43'),
(88, 1, 'Branch', 'Update', 'Update Branch 609f2780-ec15-11ea-b0fa-082e5f2acf88', 1, '::1', '2020-09-22 07:15:55', '2020-09-22 07:15:55'),
(89, 1, 'Branch', 'Update', 'Update Branch fe7a0a1b-eb47-11ea-b7e8-082e5f2acf88', 1, '::1', '2020-09-22 07:18:04', '2020-09-22 07:18:04'),
(90, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-22 11:41:31', '2020-09-22 11:41:31'),
(91, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-23 07:22:39', '2020-09-23 07:22:39'),
(92, 1, 'Attributes', 'Store', 'Add new attributes b730b782-fd58-11ea-8767-082e5f2acf88', 1, '::1', '2020-09-23 07:23:26', '2020-09-23 07:23:26'),
(93, 1, 'Product', 'Update', 'Update product 4', 1, '::1', '2020-09-23 07:24:25', '2020-09-23 07:24:25'),
(94, 1, 'Product', 'Update', 'Update product 4', 1, '::1', '2020-09-23 07:24:42', '2020-09-23 07:24:42'),
(95, 1, 'Attributes', 'Store', 'Add new attributes 521b08c5-fd67-11ea-8767-082e5f2acf88', 1, '::1', '2020-09-23 09:07:59', '2020-09-23 09:07:59'),
(96, 1, 'Product', 'Update', 'Update product 4', 1, '::1', '2020-09-23 09:26:31', '2020-09-23 09:26:31'),
(97, 1, 'Product', 'Update', 'Update product 4', 1, '::1', '2020-09-23 09:28:25', '2020-09-23 09:28:25'),
(98, 1, 'Product', 'Update', 'Update product 4', 1, '::1', '2020-09-23 09:28:49', '2020-09-23 09:28:49'),
(99, 1, 'Product', 'Update', 'Update product 4', 1, '::1', '2020-09-23 10:10:57', '2020-09-23 10:10:57'),
(100, 1, 'Product', 'Update', 'Update product 4', 1, '::1', '2020-09-23 10:11:09', '2020-09-23 10:11:09'),
(101, 1, 'Product', 'Update', 'Update product 4', 1, '::1', '2020-09-23 10:11:26', '2020-09-23 10:11:26'),
(102, 1, 'Product', 'Update', 'Update product 4', 1, '::1', '2020-09-23 10:11:56', '2020-09-23 10:11:56'),
(103, 1, 'Product', 'Update', 'Update product 5', 1, '::1', '2020-09-23 10:12:17', '2020-09-23 10:12:17'),
(104, 1, 'Attributes', 'Destroy', 'Destroy Attributes 521b08c5-fd67-11ea-8767-082e5f2acf88', 1, '::1', '2020-09-23 10:13:37', '2020-09-23 10:13:37'),
(105, 1, 'Product', 'Update', 'Update product 4', 1, '::1', '2020-09-23 10:14:00', '2020-09-23 10:14:00'),
(106, 1, 'Product', 'Update', 'Update product 5', 1, '::1', '2020-09-23 10:14:33', '2020-09-23 10:14:33'),
(107, 1, 'Product', 'Update', 'Update product 4', 1, '::1', '2020-09-23 10:14:54', '2020-09-23 10:14:54'),
(108, 1, 'Product', 'Update', 'Update product 4', 1, '::1', '2020-09-23 11:01:41', '2020-09-23 11:01:41'),
(109, 1, 'Voucher', 'Update', 'Update Voucher b807aa42-f1b4-11ea-9f3f-082e5f2acf88', 1, '::1', '2020-09-23 11:11:40', '2020-09-23 11:11:40'),
(110, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-24 07:59:57', '2020-09-24 07:59:57'),
(111, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-24 08:10:14', '2020-09-24 08:10:14'),
(112, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-24 08:49:38', '2020-09-24 08:49:38'),
(113, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-24 09:02:34', '2020-09-24 09:02:34'),
(114, 1, 'Category', 'Update', 'Update category 1', 1, '::1', '2020-09-24 09:03:04', '2020-09-24 09:03:04'),
(115, 1, 'Category', 'Update', 'Update category 2', 1, '::1', '2020-09-24 09:03:13', '2020-09-24 09:03:13'),
(116, 1, 'Category', 'Update', 'Update category 3', 1, '::1', '2020-09-24 09:03:21', '2020-09-24 09:03:21'),
(117, 1, 'Product', 'Store', 'Add new product 11', 1, '::1', '2020-09-24 09:07:03', '2020-09-24 09:07:03'),
(118, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-24 09:44:38', '2020-09-24 09:44:38'),
(119, 1, 'Admin', 'Login', 'User login ', 1, '::1', '2020-09-24 13:23:17', '2020-09-24 13:23:17'),
(120, 1, 'Printer', 'Store', 'Add new Printer 35b57e50-fe54-11ea-b53c-082e5f2acf88', 1, '::1', '2020-09-24 13:23:43', '2020-09-24 13:23:43'),
(121, 1, 'Printer', 'Store', 'Add new Printer 42ad7269-fe54-11ea-b53c-082e5f2acf88', 1, '::1', '2020-09-24 13:24:04', '2020-09-24 13:24:04'),
(122, 1, 'Product', 'Update', 'Update product 11', 1, '::1', '2020-09-24 13:48:54', '2020-09-24 13:48:54'),
(123, 1, 'Product', 'Update', 'Update product 11', 1, '::1', '2020-09-24 13:49:04', '2020-09-24 13:49:04'),
(124, 1, 'Product', 'Update', 'Update product 11', 1, '::1', '2020-09-24 13:49:37', '2020-09-24 13:49:37'),
(125, 1, 'Product', 'Update', 'Update product 11', 1, '::1', '2020-09-24 13:54:49', '2020-09-24 13:54:49');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_08_19_000000_create_failed_jobs_table', 1),
(2, '2020_08_29_063408_create_role_table', 1),
(3, '2020_08_29_063733_create_password_resets_table', 1),
(4, '2020_08_29_064012_create_users_table', 1),
(5, '2020_08_29_082251_create_language_table', 1),
(6, '2020_08_29_115118_create_permission_table', 2),
(7, '2020_08_29_120408_create_role_permission_table', 3),
(8, '2020_08_29_131117_add_role_id_to_role_permission_table', 4),
(9, '2020_08_30_153554_create_branch_table', 5),
(10, '2020_08_31_072559_add_commision_column_to_users_table', 6),
(11, '2020_08_31_052546_create_attributes_table', 7),
(12, '2020_08_31_093247_create_user_branch_table', 8),
(13, '2020_08_31_070221_create_modifier_table', 9),
(14, '2020_08_31_095815_create_user_permission_table', 9),
(15, '2020_08_31_045251_create_category', 10),
(16, '2020_08_31_045259_create_category_branch', 10),
(17, '2020_08_31_092631_create_price_type_table', 10),
(18, '2020_08_31_121138_create_customer_address_table', 10),
(19, '2020_08_31_123533_create_product_table', 11),
(20, '2020_08_31_124912_create_product_category_table', 11),
(21, '2020_08_31_130832_create_product_attribute_table', 11),
(22, '2020_08_31_130919_create_product_modifier_table', 11),
(23, '2020_08_31_131014_add_email_column_to_users_table', 11),
(24, '2020_08_31_105933_create_payment_master_table', 12),
(25, '2020_08_31_110011_create_table_table', 12),
(26, '2020_08_31_155941_create_printer_table', 12),
(27, '2020_09_01_054708_create_kitchen_department_table', 13),
(28, '2020_09_02_095746_create_product_branch_table', 14),
(29, '2020_09_01_093135_create_voucher_table', 15),
(30, '2020_09_02_105422_create_terminal_table', 15),
(31, '2020_09_03_054306_add_api_token_column_to_users_table', 16),
(32, '2020_09_03_103101_create_asset_table', 17),
(33, '2020_09_04_044720_create_product_store_inventory', 18),
(34, '2020_09_04_083005_create_product_store_inventory_log', 19),
(35, '2020_09_04_094931_create_system_setting_table', 20),
(36, '2020_09_07_154659_create_customer_table', 21),
(37, '2020_09_07_164840_create_shift_table', 22),
(38, '2020_09_07_165857_create_shift_details_table', 22),
(39, '2020_09_07_184306_add_role_column_to_customer_table', 23),
(40, '2020_09_07_194110_add_username_column_to_customer_table', 24),
(41, '2020_09_07_123644_create_banner_table', 25),
(42, '2020_09_08_132909_create_password_reset_table', 25),
(43, '2020_09_07_100732_add_field_table', 26),
(44, '2020_09_08_144558_add_field_branch_table', 26),
(45, '2020_09_08_150719_add_field_product_table', 26),
(47, '2020_09_08_200533_add_stocklevel_qty_to_product_branch_table', 27),
(48, '2020_09_08_205354_create_order_table', 28),
(49, '2020_09_08_211130_create_order_detail_table', 28),
(50, '2020_09_08_212119_create_order_modifier_table', 28),
(51, '2020_09_09_125148_create_order_payment_table', 28),
(52, '2020_09_09_131125_create_category_attribute_table', 28),
(53, '2020_09_09_131402_add_ca_id_to_attributes_table', 28),
(54, '2020_09_09_133106_add_status_to_category_attribute_table', 29),
(55, '2020_09_09_141812_add_field_category_branch', 30),
(56, '2020_09_09_164812_create_log_table', 31),
(57, '2020_09_09_174740_add_category_att_id_to_product_attribute_table', 31),
(58, '2020_09_09_193101_create_tax_table', 32),
(59, '2020_09_10_133226_add_deivce_token_column_to_terminal_table', 33),
(60, '2020_09_10_152108_create_attendance_table', 34),
(61, '2020_09_11_150449_create_branch_tax_table', 35),
(62, '2020_09_11_203715_create_terminal_log_table', 36),
(63, '2020_09_11_194050_create_rac_table', 37),
(64, '2020_09_11_194516_create_box_table', 37),
(65, '2020_09_11_194940_add_has_rac_managemant_to_category_table', 37),
(66, '2020_09_12_151014_add_box_limit_to_box_table', 37),
(67, '2020_09_12_172309_add_has_rac_managemant_to_product_table', 37),
(68, '2020_09_14_011905_add_rac_and_box_column_to_product_store_inventory_table', 38),
(69, '2020_09_14_163152_add_product_price_column_to_order_detail_table', 38),
(70, '2020_09_16_195618_add_limit_amount_column_to_voucher_table', 39),
(71, '2020_09_18_194300_create_cart_table', 40),
(72, '2020_09_18_204537_create_cart_detail_table', 40),
(73, '2020_09_18_205905_create_cart_sub_detail_table', 40),
(74, '2020_09_19_163005_add_product_id_to_cart_table', 41),
(75, '2020_09_21_192238_create_voucher_history_table', 42),
(76, '2020_09_21_115028_add_field_branch_prefix', 43),
(77, '2020_09_21_120742_add_field_branch_invoice_starat_prefix', 43),
(78, '2020_09_22_150625_change_table_no_column_order_table', 44),
(79, '2020_09_22_151519_change_column_to_order_detail_table', 44),
(80, '2020_09_22_152206_change_column_to_order_payment_table', 44),
(81, '2020_09_22_152631_change_column_to_order_modifier_table', 44),
(82, '2020_09_22_163850_create_order_attributes_table', 45),
(83, '2020_09_22_191431_add_tax_json_to_cart_table', 46),
(84, '2020_09_22_191638_add_tax_json_to_order_table', 46),
(85, '2020_09_23_173553_change_column_nullable_to_cart_detail_table', 47),
(86, '2020_09_23_190509_add_product_discount_column_to_order_detail_table', 47),
(87, '2020_09_24_184522_add_printer_column_to_product_branch_table', 48),
(88, '2020_09_25_130913_add_device_id_column_to_cart_table', 49),
(89, '2020_09_25_151435_add_voucher_detail_column_to_order_table', 49),
(90, '2020_09_25_151608_add_product_detail_column_to_order_detail_table', 49),
(91, '2020_09_25_152756_add_product_detail_column_to_cart_detail_table', 50);

-- --------------------------------------------------------

--
-- Table structure for table `modifier`
--

CREATE TABLE `modifier` (
  `modifier_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_default` tinyint(4) NOT NULL DEFAULT 0 COMMENT '1 For Selected , 0 For Not Selected',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 For InActive, 1 For Active',
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `modifier`
--

INSERT INTO `modifier` (`modifier_id`, `uuid`, `name`, `is_default`, `status`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
(1, 'acf5beec-ec14-11ea-b0fa-082e5f2acf88', 'M1', 0, 1, '2020-09-09 20:40:15', 1, NULL, NULL),
(2, '99b2fb5c-f299-11ea-9f3f-082e5f2acf88', 'M2', 0, 1, '2020-09-09 20:40:21', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `terminal_id` bigint(20) UNSIGNED NOT NULL,
  `app_id` bigint(20) UNSIGNED DEFAULT NULL,
  `table_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `table_id` bigint(20) UNSIGNED DEFAULT NULL,
  `invoice_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tax_percent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_amount` double(8,2) NOT NULL,
  `tax_json` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `voucher_id` bigint(20) UNSIGNED DEFAULT NULL,
  `voucher_amount` double(8,2) DEFAULT NULL,
  `voucher_detail` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sub_total` double(8,2) NOT NULL,
  `sub_total_after_discount` double(8,2) NOT NULL,
  `grand_total` double(8,2) NOT NULL,
  `order_source` tinyint(4) NOT NULL DEFAULT 2 COMMENT '1 For Web,2 For App',
  `order_status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1 For New,2 For Ongoing,3 For cancelled,4 For Completed,5 For Refunded',
  `order_item_count` int(11) NOT NULL,
  `order_date` date NOT NULL,
  `order_by` bigint(20) UNSIGNED NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`order_id`, `uuid`, `branch_id`, `terminal_id`, `app_id`, `table_no`, `table_id`, `invoice_no`, `customer_id`, `tax_percent`, `tax_amount`, `tax_json`, `voucher_id`, `voucher_amount`, `voucher_detail`, `sub_total`, `sub_total_after_discount`, `grand_total`, `order_source`, `order_status`, `order_item_count`, `order_date`, `order_by`, `updated_at`, `updated_by`) VALUES
(1, '401ffb79-fd94-11ea-8767-082e5f2acf88', 1, 0, NULL, NULL, 1, 'BC00001', NULL, NULL, 0.00, '[{\"id\":2,\"tax_id\":1,\"branch_id\":1,\"rate\":\"7.00\",\"status\":1,\"updated_at\":\"2020-09-22 12:48:04\",\"updated_by\":1,\"taxAmount\":16.17}]', 1, 20.00, NULL, 251.00, 231.00, 247.17, 1, 1, 2, '2020-09-23', 1, '2020-09-23 00:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_attributes`
--

CREATE TABLE `order_attributes` (
  `oa_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `detail_id` bigint(20) UNSIGNED NOT NULL,
  `terminal_id` bigint(20) UNSIGNED DEFAULT NULL,
  `app_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `attribute_id` bigint(20) UNSIGNED NOT NULL,
  `attr_price` double(10,2) NOT NULL,
  `ca_id` int(11) DEFAULT NULL,
  `oa_status` tinyint(4) NOT NULL COMMENT '0 For InActive, 1 For Active',
  `oa_datetime` datetime DEFAULT NULL,
  `oa_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_detail`
--

CREATE TABLE `order_detail` (
  `detail_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `terminal_id` bigint(20) UNSIGNED NOT NULL,
  `app_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_price` double DEFAULT NULL,
  `product_old_price` double DEFAULT NULL,
  `product_discount` double(10,2) DEFAULT NULL,
  `product_detail` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `detail_amount` double(8,2) NOT NULL,
  `detail_qty` int(11) NOT NULL,
  `detail_status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1 For Placed,2 For Served,3 For Cancelled,4 For Returned',
  `detail_datetime` datetime NOT NULL,
  `detail_by` bigint(20) UNSIGNED NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_detail`
--

INSERT INTO `order_detail` (`detail_id`, `uuid`, `order_id`, `branch_id`, `terminal_id`, `app_id`, `product_id`, `category_id`, `product_price`, `product_old_price`, `product_discount`, `product_detail`, `detail_amount`, `detail_qty`, `detail_status`, `detail_datetime`, `detail_by`, `updated_at`, `updated_by`) VALUES
(1, '4024a65a-fd94-11ea-8767-082e5f2acf88', 1, 1, 0, NULL, 4, NULL, 100, 120, 10.00, NULL, 100.00, 1, 1, '2020-09-23 19:59:37', 1, '2020-09-23 19:59:37', NULL),
(2, '4026127c-fd94-11ea-8767-082e5f2acf88', 1, 1, 0, NULL, 4, NULL, 100, 120, 10.00, NULL, 100.00, 1, 1, '2020-09-23 19:59:37', 1, '2020-09-23 19:59:37', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_modifier`
--

CREATE TABLE `order_modifier` (
  `om_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `detail_id` bigint(20) UNSIGNED NOT NULL,
  `terminal_id` bigint(20) UNSIGNED NOT NULL,
  `app_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `modifier_id` bigint(20) UNSIGNED NOT NULL,
  `om_amount` double(8,2) NOT NULL,
  `om_status` tinyint(4) NOT NULL,
  `om_datetime` datetime NOT NULL,
  `om_by` bigint(20) UNSIGNED NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_payment`
--

CREATE TABLE `order_payment` (
  `op_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `terminal_id` bigint(20) UNSIGNED NOT NULL,
  `app_id` bigint(20) UNSIGNED DEFAULT NULL,
  `op_method_id` bigint(20) UNSIGNED NOT NULL,
  `op_amount` double(8,2) NOT NULL,
  `op_method_response` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `op_status` tinyint(4) NOT NULL COMMENT '1 For success , 2 For Failed',
  `op_datetime` datetime NOT NULL,
  `op_by` bigint(20) UNSIGNED NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_payment`
--

INSERT INTO `order_payment` (`op_id`, `uuid`, `order_id`, `branch_id`, `terminal_id`, `app_id`, `op_method_id`, `op_amount`, `op_method_response`, `op_status`, `op_datetime`, `op_by`, `updated_at`, `updated_by`) VALUES
(1, '40272783-fd94-11ea-8767-082e5f2acf88', 1, 1, 0, NULL, 1, 251.00, NULL, 1, '2020-09-23 19:59:37', 1, '2020-09-23 19:59:37', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset`
--

CREATE TABLE `password_reset` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_admin` tinyint(4) NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_admin` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`email`, `token`, `is_admin`, `created_at`) VALUES
('admin@admin.com', 'Nm8yOXJZSp2hKWYKgdff68R6drcxZTn81599543858', 1, '2020-09-08 08:14:18'),
('lehan@mailinator.com', 'ze9OK3gx27cM7jQt3H2nfZppn5mw6R0S1600939166', 0, '2020-09-24 11:49:26'),
('leham@gmail.com', 'gZcAJX0ScauJjZfvaIrMEsrAbp77nCLw1600938992', 0, '2020-09-24 11:46:32'),
('leham@mailinator.com', 'vw7qaDdpZg0dJ5mmakjdpxq7aSkMRrtG1600939073', 0, '2020-09-24 11:47:53');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 For InActive, 1 For Active',
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`payment_id`, `uuid`, `name`, `status`, `updated_at`, `updated_by`) VALUES
(1, '8d09ceb7-f74f-11ea-9f8c-082e5f2acf88', 'iPay88', 1, '2020-09-15 20:32:43', 1),
(2, '06f930ac-f750-11ea-9f8c-082e5f2acf88', 'Cash-on delivery', 1, '2020-09-15 20:36:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `permission`
--

CREATE TABLE `permission` (
  `permission_id` smallint(5) UNSIGNED NOT NULL,
  `permission_name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permission_updated_at` datetime DEFAULT NULL,
  `permission_updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permission`
--

INSERT INTO `permission` (`permission_id`, `permission_name`, `permission_updated_at`, `permission_updated_by`) VALUES
(1, 'view_dashboard', NULL, NULL),
(2, 'view_roles', NULL, NULL),
(3, 'add_roles', NULL, NULL),
(4, 'edit_roles', NULL, NULL),
(5, 'delete_roles', NULL, NULL),
(6, 'view_customer', NULL, NULL),
(7, 'add_customer', NULL, NULL),
(8, 'edit_customer', NULL, NULL),
(9, 'delete_customer', NULL, NULL),
(10, 'view_branch', NULL, NULL),
(11, 'add_branch', NULL, NULL),
(12, 'edit_branch', NULL, NULL),
(13, 'delete_branch', NULL, NULL),
(14, 'view_cashier', NULL, NULL),
(15, 'add_cashier', NULL, NULL),
(16, 'edit_cashier', NULL, NULL),
(17, 'delete_cashier', NULL, NULL),
(18, 'view_waiter', NULL, NULL),
(19, 'add_waiter', NULL, NULL),
(20, 'edit_waiter', NULL, NULL),
(21, 'delete_waiter', NULL, NULL),
(22, 'view_attributes', NULL, NULL),
(23, 'add_attributes', NULL, NULL),
(24, 'edit_attributes', NULL, NULL),
(25, 'delete_attributes', NULL, NULL),
(26, 'view_modifier', NULL, NULL),
(27, 'add_modifier', NULL, NULL),
(28, 'edit_modifier', NULL, NULL),
(29, 'delete_modifier', NULL, NULL),
(30, 'view_category', NULL, NULL),
(31, 'add_category', NULL, NULL),
(32, 'edit_category', NULL, NULL),
(33, 'delete_category', NULL, NULL),
(34, 'view_price_type', NULL, NULL),
(35, 'add_price_type', NULL, NULL),
(36, 'edit_price_type', NULL, NULL),
(37, 'delete_price_type', NULL, NULL),
(38, 'view_printer', NULL, NULL),
(39, 'add_printer', NULL, NULL),
(40, 'edit_printer', NULL, NULL),
(41, 'delete_printer', NULL, NULL),
(42, 'view_table', NULL, NULL),
(43, 'add_table', NULL, NULL),
(44, 'edit_table', NULL, NULL),
(45, 'delete_table', NULL, NULL),
(46, 'view_kitchen', NULL, NULL),
(47, 'add_kitchen', NULL, NULL),
(48, 'edit_kitchen', NULL, NULL),
(49, 'delete_kitchen', NULL, NULL),
(50, 'delete_kitchen', NULL, NULL),
(51, 'view_banner', NULL, NULL),
(52, 'add_banner', NULL, NULL),
(53, 'edit_banner', NULL, NULL),
(54, 'delete_banner', NULL, NULL),
(55, 'view_product', NULL, NULL),
(56, 'add_product', NULL, NULL),
(57, 'edit_product', NULL, NULL),
(58, 'delete_product', NULL, NULL),
(59, 'view_product_inventory', NULL, NULL),
(60, 'add_product_inventory', NULL, NULL),
(61, 'edit_product_inventory', NULL, NULL),
(62, 'delete_product_inventory', NULL, NULL),
(63, 'view_tax', NULL, NULL),
(64, 'add_tax', NULL, NULL),
(65, 'edit_tax', NULL, NULL),
(66, 'delete_tax', NULL, NULL),
(67, 'view_category_attribute', NULL, NULL),
(68, 'add_category_attribute', NULL, NULL),
(69, 'edit_category_attribute', NULL, NULL),
(70, 'delete_category_attribute', NULL, NULL),
(71, 'view_logs', NULL, NULL),
(72, 'add_logs', NULL, NULL),
(73, 'edit_logs', NULL, NULL),
(74, 'delete_logs', NULL, NULL),
(75, 'view_attendance', NULL, NULL),
(76, 'add_attendance', NULL, NULL),
(77, 'edit_attendance', NULL, NULL),
(78, 'delete_attendance', NULL, NULL),
(79, 'view_rac', NULL, NULL),
(80, 'add_rac', NULL, NULL),
(81, 'edit_rac', NULL, NULL),
(82, 'delete_rac', NULL, NULL),
(83, 'view_box', NULL, NULL),
(84, 'add_box', NULL, NULL),
(85, 'edit_box', NULL, NULL),
(86, 'delete_box', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `price_type`
--

CREATE TABLE `price_type` (
  `pt_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 For InActive, 1 For Active',
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `price_type`
--

INSERT INTO `price_type` (`pt_id`, `uuid`, `name`, `status`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
(1, '7d4c3a01-ec14-11ea-b0fa-082e5f2acf88', 'Rm', 1, '2020-09-18 13:25:35', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `printer`
--

CREATE TABLE `printer` (
  `printer_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `printer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `printer_ip` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `printer_is_cashier` tinyint(4) NOT NULL DEFAULT 0 COMMENT '1 for Assigned,0 For not assigned',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 For InActive, 1 For Active',
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `printer`
--

INSERT INTO `printer` (`printer_id`, `uuid`, `branch_id`, `printer_name`, `printer_ip`, `printer_is_cashier`, `status`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
(1, '35b57e50-fe54-11ea-b53c-082e5f2acf88', 1, 'Printer1', '102.0.0.1', 1, 1, '2020-09-24 18:53:43', 1, NULL, NULL),
(2, '42ad7269-fe54-11ea-b53c-082e5f2acf88', 2, 'PrinterB2', '101.0.0.0', 1, 1, '2020-09-24 18:54:04', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sku` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price_type_id` int(10) UNSIGNED NOT NULL,
  `price_type_value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` double(10,2) NOT NULL,
  `old_price` double(10,2) DEFAULT NULL,
  `has_inventory` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 For Off, 1 For On',
  `has_rac_managemant` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 For No, 1 For Yes',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 For disabled, 1 For enabled',
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `uuid`, `name`, `slug`, `description`, `sku`, `price_type_id`, `price_type_value`, `price`, `old_price`, `has_inventory`, `has_rac_managemant`, `status`, `updated_at`, `deleted_at`, `updated_by`, `deleted_by`) VALUES
(4, 'ced0522f-ec59-11ea-b0fa-082e5f2acf88', 'Oranges', 'oranges', '<p><strong>Lorem Ipsum</strong>&nbsp;is simply dummy text of the printing and typesetting industry</p>', '451', 1, '1 KG', 100.00, 120.00, 1, 0, 1, '2020-09-23 16:31:41', NULL, 1, NULL),
(5, '2e152a34-f2a2-11ea-9f3f-082e5f2acf88', 'Mini pizza', 'mini-pizza', '<p>Mini pizza</p>', 'MP89L', 1, '1 KG', 10.00, 12.00, 0, 0, 1, '2020-09-23 15:44:33', NULL, 1, NULL),
(10, '8d2126ef-f4f5-11ea-8db3-082e5f2acf88', 'Pizza', 'pizza', NULL, 'P12Z', 1, '1 KG', 50.00, 60.00, 0, 0, 1, '2020-09-17 14:10:25', NULL, 1, NULL),
(11, '5ad29225-fe30-11ea-b53c-082e5f2acf88', 'Fish', 'fish', '<p>tesst</p>', 'fish', 1, '1', 100.00, NULL, 1, 0, 1, '2020-09-24 19:24:49', NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_attribute`
--

CREATE TABLE `product_attribute` (
  `pa_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ca_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `attribute_id` bigint(20) UNSIGNED NOT NULL,
  `price` double(10,2) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 For InActive, 1 For Active',
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_attribute`
--

INSERT INTO `product_attribute` (`pa_id`, `uuid`, `ca_id`, `product_id`, `attribute_id`, `price`, `status`, `updated_at`, `updated_by`) VALUES
(1, '7eb7b9e7-f29f-11ea-9f3f-082e5f2acf88', 2, 4, 3, 10.00, 1, '2020-09-23 16:31:41', 1),
(2, '7eb86e30-f29f-11ea-9f3f-082e5f2acf88', 2, 4, 4, 20.00, 1, '2020-09-23 16:31:41', 1),
(7, 'e3ae85a5-f2a0-11ea-9f3f-082e5f2acf88', 3, 4, 5, 25.00, 1, '2020-09-23 16:31:41', 1),
(8, '2e1793d6-f2a2-11ea-9f3f-082e5f2acf88', 2, 5, 3, 12.00, 1, '2020-09-23 15:44:33', 1),
(9, '2e17ec93-f2a2-11ea-9f3f-082e5f2acf88', 2, 5, 4, 14.25, 1, '2020-09-23 15:44:33', 1),
(10, '2e181320-f2a2-11ea-9f3f-082e5f2acf88', 3, 5, 5, 17.00, 2, '2020-09-23 15:44:33', 1),
(11, '2e289664-f4f5-11ea-8db3-082e5f2acf88', 4, 5, 6, 12.00, 1, '2020-09-23 15:44:33', 1),
(12, '8d255eb5-f4f5-11ea-8db3-082e5f2acf88', 2, 10, 3, 10.00, 1, '2020-09-17 14:10:26', 1),
(13, '8d25aaa1-f4f5-11ea-8db3-082e5f2acf88', 2, 10, 4, 20.00, 1, '2020-09-17 14:10:26', 1),
(14, '8d25c6e9-f4f5-11ea-8db3-082e5f2acf88', 3, 10, 5, 30.00, 2, '2020-09-17 14:10:26', 1),
(15, '1df20772-fd70-11ea-8767-082e5f2acf88', 3, 4, 7, 17.00, 1, '2020-09-23 16:31:41', 1),
(16, '1df2e597-fd70-11ea-8767-082e5f2acf88', 3, 4, 8, 5.00, 1, '2020-09-23 15:41:56', 1),
(17, '4debd8c9-fd70-11ea-8767-082e5f2acf88', 3, 5, 7, 10.00, 2, '2020-09-23 15:44:33', 1),
(18, '4deca942-fd70-11ea-8767-082e5f2acf88', 3, 5, 8, 0.00, 2, '2020-09-23 15:44:33', 1),
(19, '5ad695d5-fe30-11ea-b53c-082e5f2acf88', 3, 11, 5, 10.00, 1, '2020-09-24 19:24:49', 1),
(20, '5ae050da-fe30-11ea-b53c-082e5f2acf88', 3, 11, 7, 5.00, 1, '2020-09-24 19:24:49', 1),
(21, '5ae09d5b-fe30-11ea-b53c-082e5f2acf88', 2, 11, 3, 10.00, 1, '2020-09-24 19:24:49', 1),
(22, '5ae0cd20-fe30-11ea-b53c-082e5f2acf88', 2, 11, 4, 20.00, 1, '2020-09-24 19:24:49', 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_branch`
--

CREATE TABLE `product_branch` (
  `pb_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `warningStockLevel` bigint(20) UNSIGNED DEFAULT NULL,
  `display_order` int(11) DEFAULT NULL,
  `printer_id` int(11) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 For InActive, 1 For Active',
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_branch`
--

INSERT INTO `product_branch` (`pb_id`, `uuid`, `product_id`, `branch_id`, `warningStockLevel`, `display_order`, `printer_id`, `status`, `updated_at`, `updated_by`) VALUES
(57, 'b31184a8-f295-11ea-9f3f-082e5f2acf88', 4, 1, NULL, 1, NULL, 1, '2020-09-23 16:31:41', 1),
(58, 'c296ff99-f295-11ea-9f3f-082e5f2acf88', 4, 2, NULL, 2, NULL, 1, '2020-09-23 16:31:41', 1),
(59, '2e18c1bf-f2a2-11ea-9f3f-082e5f2acf88', 5, 1, 10, 1, NULL, 2, '2020-09-23 15:44:33', 1),
(60, '8d2637e6-f4f5-11ea-8db3-082e5f2acf88', 10, 1, 10, 1, NULL, 1, '2020-09-17 14:10:26', 1),
(61, '5aff039f-fe30-11ea-b53c-082e5f2acf88', 11, 1, NULL, 1, NULL, 1, '2020-09-24 19:24:49', 1),
(62, 'ba995b93-fe57-11ea-b53c-082e5f2acf88', 11, 2, NULL, 2, 2, 1, '2020-09-24 19:24:49', 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_category`
--

CREATE TABLE `product_category` (
  `pc_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `display_order` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 For InActive, 1 For Active',
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_category`
--

INSERT INTO `product_category` (`pc_id`, `product_id`, `category_id`, `branch_id`, `display_order`, `status`, `updated_at`, `updated_by`) VALUES
(72, 4, 6, 0, 0, 2, '2020-09-23 16:31:41', 1),
(73, 4, 2, 0, 0, 1, '2020-09-23 16:31:41', 1),
(74, 4, 3, 0, 0, 1, '2020-09-23 16:31:41', 1),
(75, 4, 4, 0, 0, 1, '2020-09-23 16:31:41', 1),
(76, 4, 5, 0, 0, 1, '2020-09-23 16:31:41', 1),
(77, 5, 1, 0, 0, 1, '2020-09-23 15:44:33', 1),
(78, 5, 5, 0, 0, 1, '2020-09-23 15:44:33', 1),
(83, 10, 1, 0, 0, 1, '2020-09-17 14:10:26', 1),
(84, 10, 5, 0, 0, 1, '2020-09-17 14:10:26', 1),
(85, 11, 3, 0, 0, 1, '2020-09-24 19:24:49', 1),
(86, 11, 4, 0, 0, 1, '2020-09-24 19:24:49', 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_modifier`
--

CREATE TABLE `product_modifier` (
  `pm_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `modifier_id` bigint(20) UNSIGNED NOT NULL,
  `price` double(10,2) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 For InActive, 1 For Active',
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_modifier`
--

INSERT INTO `product_modifier` (`pm_id`, `uuid`, `product_id`, `modifier_id`, `price`, `status`, `updated_at`, `updated_by`) VALUES
(49, 'ea606414-f296-11ea-9f3f-082e5f2acf88', 4, 1, 25.00, 1, '2020-09-23 16:31:41', 1),
(50, 'a1da4c73-f299-11ea-9f3f-082e5f2acf88', 4, 2, 26.00, 1, '2020-09-23 16:31:41', 1),
(51, '2e186898-f2a2-11ea-9f3f-082e5f2acf88', 5, 1, 10.00, 1, '2020-09-23 15:44:33', 1),
(52, 'ca2e6986-f401-11ea-8688-082e5f2acf88', 5, 2, 20.00, 1, '2020-09-23 15:44:33', 1),
(53, '8d25e271-f4f5-11ea-8db3-082e5f2acf88', 10, 1, 20.00, 1, '2020-09-17 14:10:26', 1),
(54, '5aeda407-fe30-11ea-b53c-082e5f2acf88', 11, 1, 10.00, 1, '2020-09-24 19:24:49', 1),
(55, '5afecbf3-fe30-11ea-b53c-082e5f2acf88', 11, 2, 15.00, 1, '2020-09-24 19:24:49', 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_store_inventory`
--

CREATE TABLE `product_store_inventory` (
  `inventory_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `qty` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rac_id` int(11) DEFAULT NULL,
  `box_id` int(11) DEFAULT NULL,
  `warningStockLevel` bigint(20) UNSIGNED NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 For InActive, 1 For Active',
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_store_inventory`
--

INSERT INTO `product_store_inventory` (`inventory_id`, `uuid`, `product_id`, `branch_id`, `qty`, `rac_id`, `box_id`, `warningStockLevel`, `status`, `updated_at`, `updated_by`) VALUES
(1, '7b4cd02f-ee93-11ea-9ae0-082e5f2acf88', 4, 1, '10', NULL, NULL, 5, 1, '2020-09-04 06:16:22', 1),
(2, 'a7cccf23-f1d1-11ea-9f3f-082e5f2acf88', 4, 2, '10', NULL, NULL, 0, 1, '2020-09-08 20:06:31', 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_store_inventory_log`
--

CREATE TABLE `product_store_inventory_log` (
  `il_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `inventory_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `employe_id` bigint(20) UNSIGNED NOT NULL,
  `il_type` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1 For Add, 2 For Deduct',
  `qty` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty_before_change` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qty_after_change` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_store_inventory_log`
--

INSERT INTO `product_store_inventory_log` (`il_id`, `uuid`, `inventory_id`, `branch_id`, `product_id`, `employe_id`, `il_type`, `qty`, `qty_before_change`, `qty_after_change`, `updated_at`, `updated_by`) VALUES
(1, '7b519087-ee93-11ea-9ae0-082e5f2acf88', 1, 1, 4, 1, 1, '10', '0', '10', '2020-09-04 06:16:22', 1),
(2, 'a7d42237-f1d1-11ea-9f3f-082e5f2acf88', 2, 2, 4, 1, 1, '10', '0', '10', '2020-09-08 20:06:31', 1);

-- --------------------------------------------------------

--
-- Table structure for table `rac`
--

CREATE TABLE `rac` (
  `rac_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 For disabled, 1 For enabled,2 For deleted',
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `role_id` smallint(5) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1 For active,0 for deactive',
  `role_updated_at` datetime DEFAULT NULL,
  `role_updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`role_id`, `uuid`, `role_name`, `role_status`, `role_updated_at`, `role_updated_by`) VALUES
(1, '516026b0-e9db-11ea-a349-082e5f2acf88', 'Super Admin', 1, '2020-08-29 09:37:57', 1),
(2, 'ae5fdd1f-e9e6-11ea-a349-082e5f2acf88', 'Customer', 1, NULL, NULL),
(3, '1e681e72-e9fa-11ea-a349-082e5f2acf88', 'Branch', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `role_permission`
--

CREATE TABLE `role_permission` (
  `rp_id` smallint(5) UNSIGNED NOT NULL,
  `rp_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rp_role_id` bigint(20) UNSIGNED NOT NULL,
  `rp_permission_id` bigint(20) UNSIGNED NOT NULL,
  `rp_updated_at` datetime DEFAULT NULL,
  `rp_updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permission`
--

INSERT INTO `role_permission` (`rp_id`, `rp_uuid`, `rp_role_id`, `rp_permission_id`, `rp_updated_at`, `rp_updated_by`) VALUES
(5, '6b85b71c-e9f9-11ea-a349-082e5f2acf88', 2, 12, '2020-08-29 13:13:26', 1),
(7, '94d1c788-e9f9-11ea-a349-082e5f2acf88', 2, 6, '2020-08-29 13:14:36', 1),
(8, '94d21f88-e9f9-11ea-a349-082e5f2acf88', 2, 9, '2020-08-29 13:14:36', 1),
(9, '94d2683b-e9f9-11ea-a349-082e5f2acf88', 2, 11, '2020-08-29 13:14:36', 1),
(10, '94d2cc3f-e9f9-11ea-a349-082e5f2acf88', 2, 15, '2020-08-29 13:14:36', 1),
(11, '94d324e5-e9f9-11ea-a349-082e5f2acf88', 2, 16, '2020-08-29 13:14:36', 1),
(12, '94d36ee0-e9f9-11ea-a349-082e5f2acf88', 2, 18, '2020-08-29 13:14:36', 1),
(13, '94d3ae09-e9f9-11ea-a349-082e5f2acf88', 2, 21, '2020-08-29 13:14:36', 1),
(14, '1e6c5a23-e9fa-11ea-a349-082e5f2acf88', 3, 1, '2020-08-29 13:18:26', 1),
(15, '1e6cddf7-e9fa-11ea-a349-082e5f2acf88', 3, 10, '2020-08-29 13:18:26', 1),
(16, '1e6d35c8-e9fa-11ea-a349-082e5f2acf88', 3, 11, '2020-08-29 13:18:26', 1),
(17, '1e6d893b-e9fa-11ea-a349-082e5f2acf88', 3, 12, '2020-08-29 13:18:26', 1),
(18, '1e6dcb07-e9fa-11ea-a349-082e5f2acf88', 3, 13, '2020-08-29 13:18:26', 1),
(19, '87a5c3b7-e9fa-11ea-a349-082e5f2acf88', 3, 18, '2020-08-29 13:21:23', 1);

-- --------------------------------------------------------

--
-- Table structure for table `shift`
--

CREATE TABLE `shift` (
  `shift_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `terminal_id` bigint(20) UNSIGNED NOT NULL,
  `app_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) NOT NULL,
  `branch_id` bigint(20) NOT NULL,
  `start_amount` double(10,2) DEFAULT NULL,
  `end_amount` double(10,2) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 For InActive, 1 For Active',
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shift`
--

INSERT INTO `shift` (`shift_id`, `uuid`, `terminal_id`, `app_id`, `user_id`, `branch_id`, `start_amount`, `end_amount`, `status`, `updated_at`, `updated_by`) VALUES
(1, 'cdde97a9-a4cc-11ea-bf2c-00161hht521f', 1, NULL, 2, 1, 120.00, 500.00, 1, '2020-09-09 06:43:09', 1);

-- --------------------------------------------------------

--
-- Table structure for table `shift_details`
--

CREATE TABLE `shift_details` (
  `shift_details_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `terminal_id` bigint(20) UNSIGNED NOT NULL,
  `app_id` bigint(20) DEFAULT NULL,
  `shift_id` bigint(20) NOT NULL,
  `invoice_id` bigint(20) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 For InActive, 1 For Active',
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_setting`
--

CREATE TABLE `system_setting` (
  `system_setting_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_system_setting` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 For No, 1 For Yes',
  `display_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1 For String, 2 For Integer, 3 For Float, 4 For Boolean, 5 For Color, 6 For Minutes',
  `namespace` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_setting`
--

INSERT INTO `system_setting` (`system_setting_id`, `uuid`, `is_system_setting`, `display_name`, `type`, `namespace`, `key`, `value`, `deleted_at`, `updated_at`, `updated_by`) VALUES
(1, '203938df-eea5-11ea-9ae0-082e5f2acf88', 1, 'TimeZone', 1, 'tomezone', 'timezone', 'Asia/Kuala_Lumpur', NULL, '2020-09-04 06:16:22', 1),
(2, '3f2bf52b-f71c-11ea-9f8c-082e5f2acf88', 1, 'Sync Timer Minutes', 6, 'sync timer minutes', 'sync_timer_minutes', '10', NULL, '2020-09-16 12:43:23', 1),
(3, 'b436ad88-f7e9-11ea-9f8c-082e5f2acf88', 1, 'Warning Stock Level', 4, 'Warning Stock Level', 'warning_stock_level', 'true', NULL, '2020-09-16 12:43:23', 1);

-- --------------------------------------------------------

--
-- Table structure for table `table`
--

CREATE TABLE `table` (
  `table_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` smallint(5) UNSIGNED NOT NULL,
  `table_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `table_type` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1 for Dine-in 2 For Take away',
  `table_qr` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `table_capacity` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 For InActive, 1 For Active',
  `available_status` int(11) NOT NULL DEFAULT 1 COMMENT '1 for Free',
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `table`
--

INSERT INTO `table` (`table_id`, `uuid`, `branch_id`, `table_name`, `table_type`, `table_qr`, `table_capacity`, `status`, `available_status`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
(1, 'ce78b8e9-f68e-11ea-8db3-082e5f2acf88', 1, 'T1', 1, 'ZWznSNLIq91600090389', 10, 1, 1, NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tax`
--

CREATE TABLE `tax` (
  `tax_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rate` decimal(10,2) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 For disabled, 1 For enabled',
  `is_fixed` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 For NOT Fixed, 1 For Fixed',
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tax`
--

INSERT INTO `tax` (`tax_id`, `uuid`, `code`, `description`, `rate`, `status`, `is_fixed`, `updated_at`, `updated_by`) VALUES
(1, '2b33ae69-f363-11ea-bc98-082e5f2acf88', 'servicetax', 'service tax', '7.00', 1, 0, '2020-09-10 20:43:05', 1);

-- --------------------------------------------------------

--
-- Table structure for table `terminal`
--

CREATE TABLE `terminal` (
  `terminal_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `terminal_device_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `terminal_device_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` smallint(5) UNSIGNED NOT NULL,
  `terminal_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `terminal_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `terminal_type` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1 for Cashier,2 For Waiter,3 For Attendance',
  `terminal_is_mother` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1 for Yes , 0 For No',
  `terminal_verified_at` datetime DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 For InActive, 1 For Active',
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `terminal`
--

INSERT INTO `terminal` (`terminal_id`, `uuid`, `terminal_device_id`, `terminal_device_token`, `branch_id`, `terminal_name`, `terminal_key`, `terminal_type`, `terminal_is_mother`, `terminal_verified_at`, `status`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
(1, '3e3fe880-f32f-11ea-bc98-082e5f2acf88', '123dfg2df3g2df3g2df3g2df', 'erg65g65df6g5dfg5d5g5d5fg5dgd6', 1, 'Attendance Terminal', 'fLQQ', 3, 0, '2020-09-12 19:10:19', 1, '2020-09-10 16:14:18', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `terminal_log`
--

CREATE TABLE `terminal_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `terminal_id` bigint(20) UNSIGNED DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `module_name` enum('','Login','Verify Key','Customer','Customer Address','Role','Attribute','Modifier','Category','Category Branch','Kitchen','Clear','Check in','Check out','Branch','Shift open','Shift close','Product','Delete all items','Save order','Open order','Split order','Invoice','Refund','Open Cash Drawer','Print Receipt','Print Test Receipt','Auto sync','Print Full Receipt','Print Half Receipt') COLLATE utf8mb4_unicode_ci DEFAULT '',
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activity_date` date DEFAULT NULL,
  `activity_time` time DEFAULT NULL,
  `table_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 For InActive, 1 For Active',
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `terminal_log`
--

INSERT INTO `terminal_log` (`id`, `uuid`, `terminal_id`, `branch_id`, `module_name`, `description`, `activity_date`, `activity_time`, `table_name`, `entity_id`, `status`, `updated_at`, `updated_by`) VALUES
(1, 'f65b5ee0-f434-11ea-8688-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-11', '21:44:51', 'Branch,Users,Role', NULL, 1, '2020-09-11 20:56:33', NULL),
(2, '059599cc-f4b9-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '13:30:08', 'Branch,Users,Role', NULL, 1, '2020-09-11 20:56:33', NULL),
(3, '0dc365a3-f4b9-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '13:30:22', 'Branch,Users,Role', NULL, 1, '2020-09-11 20:56:33', NULL),
(4, '70d254c7-f4bb-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '13:47:27', 'Branch,Users,Role', NULL, 1, '2020-09-11 20:56:33', NULL),
(5, '73dbe1fa-f4bc-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '13:54:42', 'Branch,Users,Role', NULL, 1, '2020-09-11 20:56:33', NULL),
(6, 'afd73bb1-f4bc-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '13:56:23', 'Branch,Users,Role', NULL, 1, '2020-09-11 20:56:33', NULL),
(7, 'e319c2aa-f4bc-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '13:57:49', 'Branch,Users,Role', NULL, 1, '2020-09-11 20:56:33', NULL),
(8, '6e05bf9e-f4bd-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '14:01:42', 'Branch,Users,Role', NULL, 1, '2020-09-11 20:56:33', NULL),
(9, '0adfb501-f4be-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '14:06:05', 'category,product,attribute,modifier', NULL, 1, '2020-09-11 20:56:33', NULL),
(10, '2f944855-f4be-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '14:07:06', 'category,product,attribute,modifier', NULL, 1, '2020-09-11 20:56:33', NULL),
(11, '34f8d6cd-f4be-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '14:07:15', 'category,product,attribute,modifier', NULL, 1, '2020-09-11 20:56:33', NULL),
(12, '37dba03e-f4be-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '14:07:20', 'category,product,attribute,modifier', NULL, 1, '2020-09-11 20:56:33', NULL),
(13, '3f422dde-f4be-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '14:07:33', 'category,product,attribute,modifier', NULL, 1, '2020-09-11 20:56:33', NULL),
(14, '45aed142-f4be-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '14:07:43', 'category,product,attribute,modifier', NULL, 1, '2020-09-11 20:56:33', NULL),
(15, '48dde8b1-f4be-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '14:07:49', 'category,product,attribute,modifier', NULL, 1, '2020-09-11 20:56:33', NULL),
(16, '4e33d8f8-f4be-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '14:07:58', 'category,product,attribute,modifier', NULL, 1, '2020-09-11 20:56:33', NULL),
(17, 'a8f831f7-f4be-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '14:10:30', 'category,product,attribute,modifier', NULL, 1, '2020-09-11 20:56:33', NULL),
(18, 'd80b8425-f4be-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '14:11:49', 'category,product,attribute,modifier', NULL, 1, '2020-09-11 20:56:33', NULL),
(19, 'de9084cb-f4be-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '14:12:00', 'category,product,attribute,modifier', NULL, 1, '2020-09-11 20:56:33', NULL),
(20, 'e8a3df4f-f4be-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '14:12:17', 'category,product,attribute,modifier', NULL, 1, '2020-09-11 20:56:33', NULL),
(21, 'ed810929-f4be-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '14:12:25', 'category,product,attribute,modifier', NULL, 1, '2020-09-11 20:56:33', NULL),
(22, '02050ed4-f4bf-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '14:12:59', 'product_attribute,product_modifier,product_cateogry,product_branch', NULL, 1, '2020-09-11 20:56:33', NULL),
(23, 'b01c186d-f4c0-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '14:25:01', 'Branch,Users,Role', NULL, 1, '2020-09-11 20:56:33', NULL),
(24, 'fa0a0890-f4c3-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '14:48:34', 'Branch,Users,Role', NULL, 1, '2020-09-12 14:41:06', NULL),
(25, '1623d232-f4c4-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '14:49:21', 'Branch,Users,Role', NULL, 1, '2020-09-12 14:41:06', NULL),
(26, '2a156b61-f4c4-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '14:49:54', 'Branch,Users,Role', NULL, 1, '2020-09-12 14:41:06', NULL),
(27, '64e98ed5-f4c4-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '14:51:33', 'Branch,Users,Role', NULL, 1, '2020-09-12 14:41:06', NULL),
(28, '92ede09e-f4c4-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '14:52:50', 'Branch,Users,Role', NULL, 1, '2020-09-12 14:41:06', NULL),
(29, '7b652aad-f4c6-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '15:06:30', 'price_type,printer', NULL, 1, '2020-09-12 14:41:06', NULL),
(30, '599b902a-f4c7-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '15:12:43', 'price_type,printer', NULL, 1, '2020-09-12 14:41:06', NULL),
(31, '5bc19b82-f4c7-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '15:12:46', 'price_type,printer', NULL, 1, '2020-09-12 14:41:06', NULL),
(32, '5e2f07ec-f4c7-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '15:12:50', 'price_type,printer', NULL, 1, '2020-09-12 14:41:06', NULL),
(33, '62ceede7-f4c7-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '15:12:58', 'price_type,printer', NULL, 1, '2020-09-12 14:41:06', NULL),
(34, '926e5451-f4c9-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '15:28:37', 'Branch,Users,Role', NULL, 1, '2020-09-12 14:41:06', NULL),
(35, '2de05ca5-f4cb-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '15:40:07', 'Branch,Users,Role', NULL, 1, '2020-09-12 14:41:06', NULL),
(36, '7198a669-f4fa-11ea-8db3-082e5f2acf88', NULL, NULL, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-12', '21:18:28', 'attendance', NULL, 1, '2020-09-12 19:10:19', NULL),
(37, '4082c6f0-f649-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-14', '13:15:14', 'Branch,Users,Role', NULL, 1, '2020-09-12 19:10:19', NULL),
(38, '8b60a0fa-f653-11ea-8db3-082e5f2acf88', 1, 1, '', 'Attendance Terminal verify done', '2020-09-14', '14:28:55', 'terminal', NULL, 1, '2020-09-12 19:10:19', NULL),
(39, '70616393-f672-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'Attendance SynchronizeAppdata Synchronize Successfully done', '2020-09-14', '18:10:05', 'users', NULL, 1, '2020-09-12 19:10:19', NULL),
(40, '08cd07e8-f675-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'Attendance SynchronizeAppdata Synchronize Successfully done', '2020-09-14', '18:28:40', 'users', NULL, 1, '2020-09-12 19:10:19', NULL),
(41, '24668559-f675-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'Attendance SynchronizeAppdata Synchronize Successfully done', '2020-09-14', '18:29:26', 'users', NULL, 1, '2020-09-12 19:10:19', NULL),
(42, '549eb504-f675-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'Attendance SynchronizeAppdata Synchronize Successfully done', '2020-09-14', '18:30:47', 'users', NULL, 1, '2020-09-12 19:10:19', NULL),
(43, '72df1b20-f675-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'Attendance SynchronizeAppdata Synchronize Successfully done', '2020-09-14', '18:31:38', 'users', NULL, 1, '2020-09-12 19:10:19', NULL),
(44, '368fdeb0-f676-11ea-8db3-082e5f2acf88', 1, 1, 'Auto sync', 'Attendance SynchronizeAppdata Synchronize Successfully done', '2020-09-14', '18:37:06', 'users', NULL, 1, '2020-09-12 19:10:19', NULL),
(45, 'd9a425a1-f713-11ea-9f8c-082e5f2acf88', NULL, NULL, 'Auto sync', 'Trying to get property \'localID\' of non-object', '2020-09-15', '13:25:21', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(46, '487d69c4-f714-11ea-9f8c-082e5f2acf88', NULL, NULL, 'Auto sync', 'Attempt to assign property \'server_id\' of non-object', '2020-09-15', '13:28:27', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(47, '65d74017-f714-11ea-9f8c-082e5f2acf88', NULL, NULL, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '13:29:16', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(48, '64e16d2e-f718-11ea-9f8c-082e5f2acf88', NULL, NULL, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '13:57:53', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(49, '7f383c43-f718-11ea-9f8c-082e5f2acf88', NULL, NULL, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '13:58:37', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(50, '7f4041f9-f718-11ea-9f8c-082e5f2acf88', NULL, NULL, 'Auto sync', 'Method Illuminate\\Http\\JsonResponse::toArray does not exist.', '2020-09-15', '13:58:37', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(51, 'a6df39f6-f718-11ea-9f8c-082e5f2acf88', NULL, NULL, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '13:59:44', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(52, 'b99b57b3-f718-11ea-9f8c-082e5f2acf88', NULL, NULL, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '14:00:15', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(53, 'd286a96f-f718-11ea-9f8c-082e5f2acf88', NULL, NULL, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '14:00:57', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(54, '222886b8-f719-11ea-9f8c-082e5f2acf88', NULL, NULL, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '14:03:10', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(55, '951ebe2a-f719-11ea-9f8c-082e5f2acf88', NULL, NULL, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '14:06:23', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(56, 'c60b0bbe-f719-11ea-9f8c-082e5f2acf88', NULL, NULL, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '14:07:45', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(57, 'dd2005bf-f719-11ea-9f8c-082e5f2acf88', NULL, NULL, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '14:08:24', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(58, '4cec0b0a-f71a-11ea-9f8c-082e5f2acf88', NULL, NULL, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '14:11:32', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(59, '2bfd18e1-f71b-11ea-9f8c-082e5f2acf88', NULL, NULL, 'Auto sync', 'Trying to get property \'id\' of non-object', '2020-09-15', '14:17:46', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(60, '43238bc1-f71b-11ea-9f8c-082e5f2acf88', NULL, NULL, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '14:18:25', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(61, '7685a4e9-f71b-11ea-9f8c-082e5f2acf88', NULL, NULL, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '14:19:51', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(62, '7c0879a7-f71b-11ea-9f8c-082e5f2acf88', NULL, NULL, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '14:20:00', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(63, '7cda14ad-f71b-11ea-9f8c-082e5f2acf88', NULL, NULL, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '14:20:02', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(64, '90b083e3-f71b-11ea-9f8c-082e5f2acf88', NULL, NULL, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '14:20:35', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(65, '9fcc971a-f71b-11ea-9f8c-082e5f2acf88', NULL, NULL, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '14:21:00', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(66, 'a22d09ad-f71b-11ea-9f8c-082e5f2acf88', NULL, NULL, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '14:21:04', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(67, '51350eab-f71d-11ea-9f8c-082e5f2acf88', 1, 1, 'Auto sync', 'Attendance SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '14:33:07', 'users', NULL, 1, '2020-09-15 12:29:14', NULL),
(68, '35986157-f71e-11ea-9f8c-082e5f2acf88', 1, 1, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '14:39:31', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(69, 'c7a57d32-f722-11ea-9f8c-082e5f2acf88', 1, 1, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '15:12:14', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(70, 'e76c4660-f722-11ea-9f8c-082e5f2acf88', 1, 1, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '15:13:07', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(71, '915b12de-f725-11ea-9f8c-082e5f2acf88', 1, 1, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '15:32:11', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(72, 'd27ac32e-f725-11ea-9f8c-082e5f2acf88', 1, 1, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '15:34:00', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(73, 'df854970-f726-11ea-9f8c-082e5f2acf88', 1, 1, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '15:41:32', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(74, 'f9b4fb72-f726-11ea-9f8c-082e5f2acf88', 1, 1, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '15:42:16', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(75, 'aa1a3a30-f731-11ea-9f8c-082e5f2acf88', 1, 1, 'Auto sync', 'Attendance SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '16:58:47', 'users', NULL, 1, '2020-09-15 12:29:14', NULL),
(76, '4ee7b680-f732-11ea-9f8c-082e5f2acf88', 1, 1, 'Auto sync', 'Attendance SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '17:03:23', 'users', NULL, 1, '2020-09-15 12:29:14', NULL),
(77, '38f04a1b-f733-11ea-9f8c-082e5f2acf88', 1, 1, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '17:09:56', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(78, '5b2bb40d-f733-11ea-9f8c-082e5f2acf88', 1, 1, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '17:10:53', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(79, 'ed59e75f-f733-11ea-9f8c-082e5f2acf88', 1, 1, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '17:14:59', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(80, '9a868ef2-f735-11ea-9f8c-082e5f2acf88', 1, 1, 'Auto sync', 'Attendance SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '17:26:59', 'users', NULL, 1, '2020-09-15 12:29:14', NULL),
(81, 'c1b1bc62-f736-11ea-9f8c-082e5f2acf88', 1, 1, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '17:35:14', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(82, '8ba9400f-f73a-11ea-9f8c-082e5f2acf88', 1, 1, 'Auto sync', 'Attendance SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '18:02:21', 'users', NULL, 1, '2020-09-15 12:29:14', NULL),
(83, 'c5a2a853-f73a-11ea-9f8c-082e5f2acf88', 1, 1, 'Auto sync', 'Attendance SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '18:03:59', 'users', NULL, 1, '2020-09-15 12:29:14', NULL),
(84, 'd124284d-f73a-11ea-9f8c-082e5f2acf88', 1, 1, 'Auto sync', 'Attendance SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '18:04:18', 'users', NULL, 1, '2020-09-15 12:29:14', NULL),
(85, 'd8398e93-f73a-11ea-9f8c-082e5f2acf88', 1, 1, 'Auto sync', 'Attendance SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '18:04:30', 'users', NULL, 1, '2020-09-15 12:29:14', NULL),
(86, '0f993e64-f73b-11ea-9f8c-082e5f2acf88', 1, 1, 'Auto sync', 'Attendance SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '18:06:03', 'users', NULL, 1, '2020-09-15 12:29:14', NULL),
(87, '15b25d78-f73b-11ea-9f8c-082e5f2acf88', 1, 1, 'Auto sync', 'Attendance SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '18:06:13', 'users', NULL, 1, '2020-09-15 12:29:14', NULL),
(88, '77a6b30a-f73b-11ea-9f8c-082e5f2acf88', 1, 1, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '18:08:57', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(89, '44e9e38e-f73c-11ea-9f8c-082e5f2acf88', NULL, 1, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '18:14:42', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(90, '48727a65-f73e-11ea-9f8c-082e5f2acf88', 1, 1, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '18:29:07', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(91, 'd78d0594-f741-11ea-9f8c-082e5f2acf88', 1, 1, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '18:54:35', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(92, 'a0a3ad3e-f744-11ea-9f8c-082e5f2acf88', 1, 1, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '19:14:32', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(93, 'e6fc1438-f744-11ea-9f8c-082e5f2acf88', 1, 1, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '19:16:30', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(94, 'f1147713-f744-11ea-9f8c-082e5f2acf88', 1, 1, 'Auto sync', 'Trying to get property \'id\' of non-object', '2020-09-15', '19:16:47', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(95, '0225794d-f745-11ea-9f8c-082e5f2acf88', 1, 1, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '19:17:15', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(96, '4ab43bdb-f745-11ea-9f8c-082e5f2acf88', 1, 1, 'Auto sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', '2020-09-15', '19:19:17', 'attendance', NULL, 1, '2020-09-15 12:29:14', NULL),
(97, '18472e88-f99a-11ea-9f8c-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-18', '18:31:31', 'order', NULL, 1, '2020-09-18 13:15:56', NULL),
(98, 'b9ca39a6-fbc8-11ea-b4b3-082e5f2acf88', 1, 1, 'Auto sync', 'Attendance SynchronizeAppdata Synchronize Successfully done', '2020-09-21', '13:10:21', 'users', NULL, 1, '2020-09-21 12:57:15', NULL),
(99, '2dca1320-fbcc-11ea-b4b3-082e5f2acf88', 1, 1, 'Auto sync', 'Attendance SynchronizeAppdata Synchronize Successfully done', '2020-09-21', '13:35:04', 'users', NULL, 1, '2020-09-21 12:57:15', NULL),
(100, 'f8483bbe-fbdf-11ea-b4b3-082e5f2acf88', 1, 1, 'Auto sync', 'Attendance SynchronizeAppdata Synchronize Successfully done', '2020-09-21', '15:56:45', 'users', NULL, 1, '2020-09-21 12:57:15', NULL),
(101, '916b4eae-fbe5-11ea-b4b3-082e5f2acf88', 1, 1, 'Auto sync', 'Attendance SynchronizeAppdata Synchronize Successfully done', '2020-09-21', '16:36:49', 'users', NULL, 1, '2020-09-21 12:57:15', NULL),
(102, 'f13832f7-fcd3-11ea-b4b3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-22', '21:03:14', 'Branch,Users,Role', NULL, 1, '2020-09-22 15:27:22', NULL),
(103, '262d8811-fcd4-11ea-b4b3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-22', '21:04:43', 'Branch,Users,Role', NULL, 1, '2020-09-22 15:27:22', NULL),
(104, '72d7df4a-fcd4-11ea-b4b3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-22', '21:06:52', 'Branch,Users,Role', NULL, 1, '2020-09-22 15:27:22', NULL),
(105, 'c817634b-fcd4-11ea-b4b3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-22', '21:09:15', 'Branch,Users,Role', NULL, 1, '2020-09-22 15:27:22', NULL),
(106, 'e2325207-fcd4-11ea-b4b3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-22', '21:09:58', 'Branch,Users,Role', NULL, 1, '2020-09-22 15:27:22', NULL),
(107, '025c4ee2-fcd5-11ea-b4b3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-22', '21:10:52', 'Branch,Users,Role', NULL, 1, '2020-09-22 15:27:22', NULL),
(108, '3ee869f5-fcd5-11ea-b4b3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-22', '21:12:34', 'Branch,Users,Role', NULL, 1, '2020-09-22 15:27:22', NULL),
(109, '46169959-fcd5-11ea-b4b3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-22', '21:12:46', 'Branch,Users,Role', NULL, 1, '2020-09-22 15:27:22', NULL),
(110, 'bd4b6903-fcf4-11ea-b4b3-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-23', '00:58:01', 'Branch,Users,Role', NULL, 1, '2020-09-22 15:27:22', NULL),
(111, '546242cf-fe5f-11ea-b53c-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-24', '20:13:19', 'Branch,Users,Role', NULL, 1, '2020-09-24 13:51:10', NULL),
(112, '66ec59ae-ff26-11ea-bd8c-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-25', '19:58:19', 'Branch,Users,Role', NULL, 1, '2020-09-25 15:18:44', NULL),
(113, '20f1519e-ff27-11ea-bd8c-082e5f2acf88', 1, 1, 'Auto sync', 'SynchronizeAppdata Synchronize Successfully done', '2020-09-25', '20:03:31', 'Branch,Users,Role', NULL, 1, '2020-09-25 15:18:44', NULL),
(114, '3f89ec9a-ff2a-11ea-bd8c-082e5f2acf88', 1, 1, '', 'Web Order Synchronize Appdata Synchronize Successfully done', '2020-09-25', '20:25:51', 'cart', NULL, 1, '2020-09-25 15:18:44', NULL),
(115, '6f9bb53c-ff2a-11ea-bd8c-082e5f2acf88', 1, 1, '', 'Web Order Synchronize Appdata Synchronize Successfully done', '2020-09-25', '20:27:12', 'cart', NULL, 1, '2020-09-25 15:18:44', NULL),
(116, '8d5ed6cd-ff2b-11ea-bd8c-082e5f2acf88', 1, 1, '', 'Web Order Synchronize Appdata Synchronize Successfully done', '2020-09-25', '20:35:11', 'cart', NULL, 1, '2020-09-25 15:18:44', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `app_id` bigint(20) DEFAULT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` smallint(5) UNSIGNED NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_code` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `commision_percent` double(8,2) DEFAULT NULL,
  `user_pin` int(11) NOT NULL,
  `api_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1 For Active,0 For Deactive',
  `is_admin` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 For Not admin,1 For Admin',
  `device_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auth_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `remember_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `app_id`, `uuid`, `name`, `email`, `role`, `username`, `password`, `country_code`, `mobile`, `profile`, `commision_percent`, `user_pin`, `api_token`, `status`, `is_admin`, `device_id`, `device_token`, `auth_key`, `last_login`, `remember_token`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`, `deleted_by`) VALUES
(1, NULL, '5166f81c-e9db-11ea-a349-082e5f2acf88', 'Administrator', 'admin@admin.com', 1, 'admin', '$2y$10$UYUf6o6jb//Cq.9IvsrxH.1GbswxYD4Onr1jVXbtCPDmnS5OdcG.q', '91', '1234567890', 'backend/images/user.png', NULL, 0, NULL, 1, 1, NULL, NULL, NULL, '2020-09-24 13:51:10', NULL, '2020-08-29 04:07:57', '2020-09-24 14:51:32', NULL, 1, 1, NULL),
(2, NULL, '079f1d2e-eb62-11ea-b7e8-082e5f2acf88', 'Kiran', 'kiran@gmail.com', 3, 'kiran', '$2y$10$NgTcf0yc5NSGzfNpir3FWe8/mfT4y2p5LFqWOz72ComM/4CpEYRxW', '91', '9523652155', 'uploads/profile/1598939129.jpg', 5.00, 505060, 'Pqk3Q9kj0HVcW9g4ZJtmDBVSRccohB77I6XAw6JDgllto8MjOy1599112257', 1, 1, NULL, NULL, NULL, '2020-09-03 05:50:57', NULL, '2020-08-31 02:44:48', '2020-09-08 09:12:36', NULL, 1, 1, NULL),
(5, NULL, '96ac9bf8-ec22-11ea-b0fa-082e5f2acf88', 'john', 'lehan@mailinator.com', 3, 'john', '$2y$10$khEGNtUzgVcaHya2Ma8ZyuujrESMNQkkRMYxz0wWVt.T8SAk2cxi6', '60', '12378948', 'uploads/profile/1598944389.jpg', NULL, 422974, NULL, 1, 1, NULL, NULL, NULL, '2020-09-08 14:46:17', NULL, '2020-09-01 01:43:09', '2020-09-08 09:33:21', NULL, 1, 1, NULL),
(6, NULL, '4b30aa37-f1a1-11ea-9f3f-082e5f2acf88', 'evan', 'evan@gmail.com', 3, 'evan', '$2y$10$og3UeSdRx/j9RiHvDZLlTO0G.IkVwT55G8xCAGZNAi3jffY.ICW6W', '60', '6532124578', 'uploads/profile/1599548570.jpg', NULL, 648489, NULL, 1, 1, NULL, NULL, NULL, '2020-09-08 14:46:17', NULL, '2020-09-08 09:32:50', '2020-09-08 09:53:35', NULL, 1, 1, NULL),
(7, NULL, '2afa874e-f1a4-11ea-9f3f-082e5f2acf88', 'smith', 'smith@gmail.com', 3, 'smith', '$2y$10$xytOS1YXVIfKMuF4K96GgOc5Qt3KlF7XuF/6rYZ/NIas51IQKS3CO', '60', '8521394658', 'uploads/profile/1599549805.jpg', NULL, 528571, NULL, 1, 1, NULL, NULL, NULL, '2020-09-08 14:46:17', NULL, '2020-09-08 09:53:25', '2020-09-21 09:29:59', NULL, 1, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_branch`
--

CREATE TABLE `user_branch` (
  `ub_id` bigint(20) UNSIGNED NOT NULL,
  `ub_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 For InActive, 1 For Active',
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_branch`
--

INSERT INTO `user_branch` (`ub_id`, `ub_uuid`, `user_id`, `branch_id`, `status`, `updated_at`, `updated_by`) VALUES
(17, '49f88375-ec3a-11ea-b0fa-082e5f2acf88', 5, 1, 1, '2020-09-01 10:02:21', 1),
(18, '49f93e32-ec3a-11ea-b0fa-082e5f2acf88', 5, 2, 1, '2020-09-01 10:02:21', 1),
(19, '7755e4db-f19e-11ea-9f3f-082e5f2acf88', 2, 1, 1, '2020-09-08 13:42:33', 1),
(20, '77570064-f19e-11ea-9f3f-082e5f2acf88', 2, 2, 1, '2020-09-08 13:42:33', 1),
(21, '4b418c9c-f1a1-11ea-9f3f-082e5f2acf88', 6, 1, 1, '2020-09-08 14:46:17', 1),
(29, 'e1d262bd-fbd5-11ea-b4b3-082e5f2acf88', 7, 1, 1, '2020-09-21 14:59:59', 1),
(30, 'ee338307-fbd7-11ea-b4b3-082e5f2acf88', 7, 2, 2, '2020-09-21 14:59:59', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_permission`
--

CREATE TABLE `user_permission` (
  `up_id` bigint(20) UNSIGNED NOT NULL,
  `up_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 For InActive, 1 For Active',
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_permission`
--

INSERT INTO `user_permission` (`up_id`, `up_uuid`, `user_id`, `status`, `permission_id`, `updated_at`, `updated_by`) VALUES
(188, '49f9ee51-ec3a-11ea-b0fa-082e5f2acf88', 5, 1, 12, '2020-09-01 10:02:48', 1),
(189, '49fa342d-ec3a-11ea-b0fa-082e5f2acf88', 5, 1, 6, '2020-09-01 10:02:48', 1),
(190, '49fa59fd-ec3a-11ea-b0fa-082e5f2acf88', 5, 1, 9, '2020-09-01 10:02:48', 1),
(191, '49faa261-ec3a-11ea-b0fa-082e5f2acf88', 5, 1, 11, '2020-09-01 10:02:48', 1),
(192, '49facd6e-ec3a-11ea-b0fa-082e5f2acf88', 5, 1, 15, '2020-09-01 10:02:48', 1),
(193, '49faf162-ec3a-11ea-b0fa-082e5f2acf88', 5, 1, 16, '2020-09-01 10:02:48', 1),
(194, '49fb154f-ec3a-11ea-b0fa-082e5f2acf88', 5, 1, 18, '2020-09-01 10:02:48', 1),
(195, '49fb5fcc-ec3a-11ea-b0fa-082e5f2acf88', 5, 1, 21, '2020-09-01 10:02:48', 1),
(196, '77648e70-f19e-11ea-9f3f-082e5f2acf88', 2, 1, 1, '2020-09-08 14:42:36', 1),
(197, '7770d565-f19e-11ea-9f3f-082e5f2acf88', 2, 1, 10, '2020-09-08 14:42:36', 1),
(198, '777f6e7d-f19e-11ea-9f3f-082e5f2acf88', 2, 1, 11, '2020-09-08 14:42:36', 1),
(199, '777fac9d-f19e-11ea-9f3f-082e5f2acf88', 2, 1, 12, '2020-09-08 14:42:36', 1),
(200, '777fd43c-f19e-11ea-9f3f-082e5f2acf88', 2, 1, 13, '2020-09-08 14:42:36', 1),
(201, '777ff75f-f19e-11ea-9f3f-082e5f2acf88', 2, 1, 18, '2020-09-08 14:42:36', 1),
(243, 'e6bb853f-f1a3-11ea-9f3f-082e5f2acf88', 6, 1, 1, '2020-09-08 15:21:30', 1),
(244, 'e6bc85e9-f1a3-11ea-9f3f-082e5f2acf88', 6, 1, 6, '2020-09-08 15:21:30', 1),
(245, 'e6bccb7f-f1a3-11ea-9f3f-082e5f2acf88', 6, 1, 8, '2020-09-08 15:21:30', 1),
(246, 'e6bd13cd-f1a3-11ea-9f3f-082e5f2acf88', 6, 1, 11, '2020-09-08 15:21:30', 1),
(247, 'e6bd544e-f1a3-11ea-9f3f-082e5f2acf88', 6, 1, 12, '2020-09-08 15:21:30', 1),
(248, 'e6bd95d5-f1a3-11ea-9f3f-082e5f2acf88', 6, 1, 13, '2020-09-08 15:21:30', 1),
(249, 'e6bdd4f4-f1a3-11ea-9f3f-082e5f2acf88', 6, 1, 18, '2020-09-08 15:21:30', 1),
(250, 'e6be0f75-f1a3-11ea-9f3f-082e5f2acf88', 6, 1, 22, '2020-09-08 15:21:30', 1),
(251, 'e6be52cb-f1a3-11ea-9f3f-082e5f2acf88', 6, 1, 30, '2020-09-08 15:21:30', 1),
(323, '02d6dc0e-f1b1-11ea-9f3f-082e5f2acf88', 7, 1, 1, '2020-09-08 16:55:21', 1),
(324, '02d7d40a-f1b1-11ea-9f3f-082e5f2acf88', 7, 1, 7, '2020-09-08 16:55:21', 1),
(325, '02d81ae8-f1b1-11ea-9f3f-082e5f2acf88', 7, 1, 8, '2020-09-08 16:55:21', 1),
(326, '02d86edf-f1b1-11ea-9f3f-082e5f2acf88', 7, 1, 9, '2020-09-08 16:55:21', 1),
(327, '02d8b4a1-f1b1-11ea-9f3f-082e5f2acf88', 7, 1, 10, '2020-09-08 16:55:21', 1),
(328, '02d8f30d-f1b1-11ea-9f3f-082e5f2acf88', 7, 1, 11, '2020-09-08 16:55:21', 1),
(329, '02d932ad-f1b1-11ea-9f3f-082e5f2acf88', 7, 1, 12, '2020-09-08 16:55:21', 1),
(330, '02d98659-f1b1-11ea-9f3f-082e5f2acf88', 7, 1, 13, '2020-09-08 16:55:21', 1),
(331, '02d9c6e4-f1b1-11ea-9f3f-082e5f2acf88', 7, 1, 14, '2020-09-08 16:55:21', 1),
(332, '02da0543-f1b1-11ea-9f3f-082e5f2acf88', 7, 1, 18, '2020-09-08 16:55:21', 1),
(333, '02da4430-f1b1-11ea-9f3f-082e5f2acf88', 7, 1, 55, '2020-09-08 16:55:21', 1);

-- --------------------------------------------------------

--
-- Table structure for table `voucher`
--

CREATE TABLE `voucher` (
  `voucher_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `voucher_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `voucher_banner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `voucher_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `voucher_discount_type` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1 for Fixed , 2 For Percentage',
  `voucher_discount` double(8,2) NOT NULL,
  `minimum_amount` double(15,4) NOT NULL,
  `maximum_amount` double(15,4) NOT NULL,
  `uses_total` int(11) NOT NULL,
  `uses_customer` int(11) NOT NULL,
  `voucher_applicable_from` datetime NOT NULL,
  `voucher_applicable_to` datetime NOT NULL,
  `voucher_categories` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `voucher_products` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 For InActive, 1 For Active',
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `voucher`
--

INSERT INTO `voucher` (`voucher_id`, `uuid`, `voucher_name`, `voucher_banner`, `voucher_code`, `voucher_discount_type`, `voucher_discount`, `minimum_amount`, `maximum_amount`, `uses_total`, `uses_customer`, `voucher_applicable_from`, `voucher_applicable_to`, `voucher_categories`, `voucher_products`, `status`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
(1, 'b807aa42-f1b4-11ea-9f3f-082e5f2acf88', 'cap', 'uploads/voucher/1599556913.jpg', 'C2P', 1, 10.00, 10.0000, 1000.0000, 1, 5, '2020-09-23 00:00:00', '2020-09-24 00:00:00', '6', '4', 1, '2020-09-23 16:41:40', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `voucher_history`
--

CREATE TABLE `voucher_history` (
  `voucher_history_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `voucher_id` int(11) NOT NULL,
  `order_id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` double(15,4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `asset`
--
ALTER TABLE `asset`
  ADD PRIMARY KEY (`asset_id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attributes`
--
ALTER TABLE `attributes`
  ADD PRIMARY KEY (`attribute_id`);

--
-- Indexes for table `banner`
--
ALTER TABLE `banner`
  ADD PRIMARY KEY (`banner_id`);

--
-- Indexes for table `box`
--
ALTER TABLE `box`
  ADD PRIMARY KEY (`box_id`);

--
-- Indexes for table `branch`
--
ALTER TABLE `branch`
  ADD PRIMARY KEY (`branch_id`);

--
-- Indexes for table `branch_tax`
--
ALTER TABLE `branch_tax`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`);

--
-- Indexes for table `cart_detail`
--
ALTER TABLE `cart_detail`
  ADD PRIMARY KEY (`cart_detail_id`);

--
-- Indexes for table `cart_sub_detail`
--
ALTER TABLE `cart_sub_detail`
  ADD PRIMARY KEY (`csd_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `category_attribute`
--
ALTER TABLE `category_attribute`
  ADD PRIMARY KEY (`ca_id`);

--
-- Indexes for table `category_branch`
--
ALTER TABLE `category_branch`
  ADD PRIMARY KEY (`cb_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `customer_address`
--
ALTER TABLE `customer_address`
  ADD PRIMARY KEY (`address_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kitchen_department`
--
ALTER TABLE `kitchen_department`
  ADD PRIMARY KEY (`kitchen_id`);

--
-- Indexes for table `language`
--
ALTER TABLE `language`
  ADD PRIMARY KEY (`language_id`);

--
-- Indexes for table `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `modifier`
--
ALTER TABLE `modifier`
  ADD PRIMARY KEY (`modifier_id`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_attributes`
--
ALTER TABLE `order_attributes`
  ADD PRIMARY KEY (`oa_id`);

--
-- Indexes for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`detail_id`);

--
-- Indexes for table `order_modifier`
--
ALTER TABLE `order_modifier`
  ADD PRIMARY KEY (`om_id`);

--
-- Indexes for table `order_payment`
--
ALTER TABLE `order_payment`
  ADD PRIMARY KEY (`op_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `permission`
--
ALTER TABLE `permission`
  ADD PRIMARY KEY (`permission_id`);

--
-- Indexes for table `price_type`
--
ALTER TABLE `price_type`
  ADD PRIMARY KEY (`pt_id`);

--
-- Indexes for table `printer`
--
ALTER TABLE `printer`
  ADD PRIMARY KEY (`printer_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `product_attribute`
--
ALTER TABLE `product_attribute`
  ADD PRIMARY KEY (`pa_id`);

--
-- Indexes for table `product_branch`
--
ALTER TABLE `product_branch`
  ADD PRIMARY KEY (`pb_id`);

--
-- Indexes for table `product_category`
--
ALTER TABLE `product_category`
  ADD PRIMARY KEY (`pc_id`);

--
-- Indexes for table `product_modifier`
--
ALTER TABLE `product_modifier`
  ADD PRIMARY KEY (`pm_id`);

--
-- Indexes for table `product_store_inventory`
--
ALTER TABLE `product_store_inventory`
  ADD PRIMARY KEY (`inventory_id`);

--
-- Indexes for table `product_store_inventory_log`
--
ALTER TABLE `product_store_inventory_log`
  ADD PRIMARY KEY (`il_id`);

--
-- Indexes for table `rac`
--
ALTER TABLE `rac`
  ADD PRIMARY KEY (`rac_id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `role_permission`
--
ALTER TABLE `role_permission`
  ADD PRIMARY KEY (`rp_id`);

--
-- Indexes for table `shift`
--
ALTER TABLE `shift`
  ADD PRIMARY KEY (`shift_id`);

--
-- Indexes for table `shift_details`
--
ALTER TABLE `shift_details`
  ADD PRIMARY KEY (`shift_details_id`);

--
-- Indexes for table `system_setting`
--
ALTER TABLE `system_setting`
  ADD PRIMARY KEY (`system_setting_id`);

--
-- Indexes for table `table`
--
ALTER TABLE `table`
  ADD PRIMARY KEY (`table_id`);

--
-- Indexes for table `tax`
--
ALTER TABLE `tax`
  ADD PRIMARY KEY (`tax_id`);

--
-- Indexes for table `terminal`
--
ALTER TABLE `terminal`
  ADD PRIMARY KEY (`terminal_id`);

--
-- Indexes for table `terminal_log`
--
ALTER TABLE `terminal_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_branch`
--
ALTER TABLE `user_branch`
  ADD PRIMARY KEY (`ub_id`);

--
-- Indexes for table `user_permission`
--
ALTER TABLE `user_permission`
  ADD PRIMARY KEY (`up_id`);

--
-- Indexes for table `voucher`
--
ALTER TABLE `voucher`
  ADD PRIMARY KEY (`voucher_id`);

--
-- Indexes for table `voucher_history`
--
ALTER TABLE `voucher_history`
  ADD PRIMARY KEY (`voucher_history_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `asset`
--
ALTER TABLE `asset`
  MODIFY `asset_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `attributes`
--
ALTER TABLE `attributes`
  MODIFY `attribute_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `banner`
--
ALTER TABLE `banner`
  MODIFY `banner_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `box`
--
ALTER TABLE `box`
  MODIFY `box_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branch`
--
ALTER TABLE `branch`
  MODIFY `branch_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `branch_tax`
--
ALTER TABLE `branch_tax`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `cart_detail`
--
ALTER TABLE `cart_detail`
  MODIFY `cart_detail_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `cart_sub_detail`
--
ALTER TABLE `cart_sub_detail`
  MODIFY `csd_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `category_attribute`
--
ALTER TABLE `category_attribute`
  MODIFY `ca_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `category_branch`
--
ALTER TABLE `category_branch`
  MODIFY `cb_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `customer_address`
--
ALTER TABLE `customer_address`
  MODIFY `address_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kitchen_department`
--
ALTER TABLE `kitchen_department`
  MODIFY `kitchen_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `language`
--
ALTER TABLE `language`
  MODIFY `language_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `log`
--
ALTER TABLE `log`
  MODIFY `log_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `modifier`
--
ALTER TABLE `modifier`
  MODIFY `modifier_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `order_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `order_attributes`
--
ALTER TABLE `order_attributes`
  MODIFY `oa_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_detail`
--
ALTER TABLE `order_detail`
  MODIFY `detail_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_modifier`
--
ALTER TABLE `order_modifier`
  MODIFY `om_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_payment`
--
ALTER TABLE `order_payment`
  MODIFY `op_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `permission`
--
ALTER TABLE `permission`
  MODIFY `permission_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `price_type`
--
ALTER TABLE `price_type`
  MODIFY `pt_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `printer`
--
ALTER TABLE `printer`
  MODIFY `printer_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `product_attribute`
--
ALTER TABLE `product_attribute`
  MODIFY `pa_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `product_branch`
--
ALTER TABLE `product_branch`
  MODIFY `pb_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `product_category`
--
ALTER TABLE `product_category`
  MODIFY `pc_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `product_modifier`
--
ALTER TABLE `product_modifier`
  MODIFY `pm_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `product_store_inventory`
--
ALTER TABLE `product_store_inventory`
  MODIFY `inventory_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `product_store_inventory_log`
--
ALTER TABLE `product_store_inventory_log`
  MODIFY `il_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rac`
--
ALTER TABLE `rac`
  MODIFY `rac_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `role_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `role_permission`
--
ALTER TABLE `role_permission`
  MODIFY `rp_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `shift`
--
ALTER TABLE `shift`
  MODIFY `shift_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `shift_details`
--
ALTER TABLE `shift_details`
  MODIFY `shift_details_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_setting`
--
ALTER TABLE `system_setting`
  MODIFY `system_setting_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `table`
--
ALTER TABLE `table`
  MODIFY `table_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tax`
--
ALTER TABLE `tax`
  MODIFY `tax_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `terminal`
--
ALTER TABLE `terminal`
  MODIFY `terminal_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `terminal_log`
--
ALTER TABLE `terminal_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_branch`
--
ALTER TABLE `user_branch`
  MODIFY `ub_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `user_permission`
--
ALTER TABLE `user_permission`
  MODIFY `up_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=334;

--
-- AUTO_INCREMENT for table `voucher`
--
ALTER TABLE `voucher`
  MODIFY `voucher_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `voucher_history`
--
ALTER TABLE `voucher_history`
  MODIFY `voucher_history_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
