-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 18, 2022 at 08:42 AM
-- Server version: 5.7.33
-- PHP Version: 7.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `icpc`
--

-- --------------------------------------------------------

--
-- Table structure for table `abouts`
--

CREATE TABLE `abouts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_bn` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_en` longtext COLLATE utf8mb4_unicode_ci,
  `description_bn` longtext COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `frontend_menus`
--

CREATE TABLE `frontend_menus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `galleries`
--

CREATE TABLE `galleries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `gallery_category_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `galleries`
--

INSERT INTO `galleries` (`id`, `gallery_category_id`, `title`, `image`, `status`, `deleted_at`, `created_by`, `updated_by`, `deleted_by`, `created_at`, `updated_at`) VALUES
(10, 1, NULL, '20220412_1649756374.jpg', 1, NULL, NULL, NULL, NULL, '2022-04-12 09:39:34', '2022-04-12 09:39:34'),
(11, 1, NULL, '20220413_1649837047.jpg', 1, NULL, NULL, NULL, NULL, '2022-04-13 08:04:07', '2022-04-13 08:04:07'),
(12, 8, NULL, '20220417_1650178448.JPG', 1, NULL, NULL, NULL, NULL, '2022-04-17 06:54:09', '2022-04-17 06:54:09');

-- --------------------------------------------------------

--
-- Table structure for table `gallery_categories`
--

CREATE TABLE `gallery_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gallery_categories`
--

INSERT INTO `gallery_categories` (`id`, `name`, `sort`, `status`, `deleted_at`, `created_by`, `updated_by`, `deleted_by`, `created_at`, `updated_at`) VALUES
(1, 'day-1', 1, 1, NULL, NULL, NULL, NULL, '2022-04-12 08:09:35', '2022-04-12 08:09:35'),
(2, 'day-2', 2, 1, NULL, NULL, NULL, NULL, '2022-04-12 08:10:01', '2022-04-12 08:10:01'),
(3, 'day-3', 3, 1, NULL, NULL, NULL, NULL, '2022-04-13 06:22:16', '2022-04-13 06:22:16'),
(4, 'day-4', 4, 1, NULL, NULL, NULL, NULL, '2022-04-13 06:23:43', '2022-04-13 06:23:55'),
(5, 'day-5', 5, 1, NULL, NULL, NULL, NULL, '2022-04-13 06:24:08', '2022-04-13 06:24:08'),
(6, 'day-6', 6, 1, NULL, NULL, NULL, NULL, '2022-04-13 06:24:21', '2022-04-13 06:24:21'),
(8, 'day-7', 2, 1, NULL, NULL, NULL, NULL, '2022-04-17 06:47:07', '2022-04-17 06:47:07');

-- --------------------------------------------------------

--
-- Table structure for table `home_links`
--

CREATE TABLE `home_links` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url_link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent` int(11) NOT NULL,
  `route` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `name`, `parent`, `route`, `sort`, `status`, `icon`, `created_at`, `updated_at`) VALUES
(1, 'Menu Management', 0, 'menu', 1, 0, '', '2018-05-16 03:20:43', '2018-12-31 21:16:50'),
(2, 'Menu List', 1, 'menu', 1, 1, '', '2018-05-16 04:03:04', '2018-05-16 04:03:04'),
(3, 'Menu Icon', 1, 'menu.icon', 2, 1, '', '2018-06-06 04:35:13', '2018-06-06 08:31:29'),
(4, 'User Management', 0, 'user', 2, 1, '', '2018-06-09 04:29:53', '2019-08-05 04:54:46'),
(5, 'View Users', 4, 'user', 1, 1, '', '2018-06-09 04:29:53', '2019-08-05 04:54:46'),
(6, 'User Roles', 4, 'user.role', 2, 1, '', '2018-06-09 04:57:27', '2019-08-05 04:55:35'),
(7, 'Menu Permission', 4, 'user.permission', 3, 1, '', '2018-06-05 06:59:51', '2019-08-05 04:56:15'),
(8, 'Profile Management', 0, 'profile-management', 3, 1, '', NULL, NULL),
(9, 'Change Password', 8, 'profile-management.change.password', 1, 1, NULL, '2019-11-16 09:08:24', '2019-11-16 09:08:24'),
(10, 'Change Profile', 8, 'profile-management.change.profile', 2, 1, NULL, '2019-11-16 09:08:24', '2019-11-16 09:08:24'),
(11, 'Homepages', 0, 'homepages', 4, 1, NULL, NULL, NULL),
(12, 'Slider', 11, 'homepages.slider.view', 1, 1, NULL, NULL, NULL),
(13, 'Gallery', 11, 'homepages.gallery', 2, 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `menu_permissions`
--

CREATE TABLE `menu_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `menu_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `permitted_route` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `menu_from` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_permissions`
--

INSERT INTO `menu_permissions` (`id`, `menu_id`, `role_id`, `permitted_route`, `menu_from`, `created_at`, `updated_at`) VALUES
(10, 11, 2, 'homepages', 'menu', '2022-04-12 07:42:38', '2022-04-12 07:42:38'),
(11, 12, 2, 'homepages.slider.view', 'menu', '2022-04-12 07:42:38', '2022-04-12 07:42:38'),
(12, 5, 1, 'user', 'menu', '2022-04-12 09:34:58', '2022-04-12 09:34:58'),
(13, 4, 1, 'user', 'menu', '2022-04-12 09:34:58', '2022-04-12 09:34:58'),
(14, 6, 1, 'user.role', 'menu', '2022-04-12 09:34:58', '2022-04-12 09:34:58'),
(15, 7, 1, 'user.permission', 'menu', '2022-04-12 09:34:58', '2022-04-12 09:34:58'),
(16, 9, 1, 'profile-management.change.password', 'menu', '2022-04-12 09:34:58', '2022-04-12 09:34:58'),
(17, 8, 1, 'profile-management', 'menu', '2022-04-12 09:34:58', '2022-04-12 09:34:58'),
(18, 10, 1, 'profile-management.change.profile', 'menu', '2022-04-12 09:34:58', '2022-04-12 09:34:58'),
(19, 12, 1, 'homepages.slider.view', 'menu', '2022-04-12 09:34:58', '2022-04-12 09:34:58'),
(20, 11, 1, 'homepages', 'menu', '2022-04-12 09:34:58', '2022-04-12 09:34:58'),
(21, 13, 1, 'homepages.gallery', 'menu', '2022-04-12 09:34:58', '2022-04-12 09:34:58');

-- --------------------------------------------------------

--
-- Table structure for table `menu_posts`
--

CREATE TABLE `menu_posts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu_routes`
--

CREATE TABLE `menu_routes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `menu_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `route` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort` int(11) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_01_090751_create_site_settings_table', 1),
(4, '2019_10_26_132331_create_roles_table', 1),
(5, '2019_12_02_062512_create_menu_posts_table', 1),
(6, '2019_12_02_062605_create_frontend_menus_table', 1),
(7, '2021_04_07_051329_create_sliders_table', 1),
(8, '2021_04_07_051709_create_abouts_table', 1),
(9, '2022_01_04_102754_add_status_to_users', 1),
(10, '2022_04_12_111912_add_mobile_to_users', 1),
(11, '2022_04_12_112358_create_user_roles_table', 1),
(12, '2022_04_12_113534_create_menus_table', 1),
(13, '2022_04_12_114408_create_home_links_table', 1),
(14, '2022_04_12_123912_create_menu_routes_table', 1),
(15, '2022_04_12_133544_create_menu_permissions_table', 2),
(16, '2022_04_12_135500_create_gallery_categories_table', 3),
(17, '2022_04_12_141551_create_galleries_table', 4),
(18, '2022_04_13_144713_create_notices_table', 5),
(19, '2022_04_13_144857_create_events_table', 5),
(20, '2022_04_13_144929_create_news_table', 5);

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notices`
--

CREATE TABLE `notices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `status`, `deleted_at`, `created_by`, `updated_by`, `deleted_by`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'Admin', 1, NULL, NULL, NULL, NULL, '2022-04-12 07:01:25', '2022-04-12 07:01:25'),
(2, 'Developer', 'Developer', 1, NULL, NULL, NULL, NULL, '2022-04-12 07:01:36', '2022-04-12 07:01:36');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_name` text COLLATE utf8mb4_unicode_ci,
  `site_title` text COLLATE utf8mb4_unicode_ci,
  `site_title_bn` text COLLATE utf8mb4_unicode_ci,
  `site_short_description` text COLLATE utf8mb4_unicode_ci,
  `site_short_description_bn` text COLLATE utf8mb4_unicode_ci,
  `site_header_logo` text COLLATE utf8mb4_unicode_ci,
  `site_footer_logo` text COLLATE utf8mb4_unicode_ci,
  `site_favicon` text COLLATE utf8mb4_unicode_ci,
  `site_banner_image` text COLLATE utf8mb4_unicode_ci,
  `site_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_phone_primary` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_phone_secondary` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_address` text COLLATE utf8mb4_unicode_ci,
  `mail_driver` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mail_host` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mail_port` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mail_username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mail_password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mail_encryption` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook_url` text COLLATE utf8mb4_unicode_ci,
  `twitter_url` text COLLATE utf8mb4_unicode_ci,
  `google_plus_url` text COLLATE utf8mb4_unicode_ci,
  `linkedin_url` text COLLATE utf8mb4_unicode_ci,
  `youtube_url` text COLLATE utf8mb4_unicode_ci,
  `instagram_url` text COLLATE utf8mb4_unicode_ci,
  `pinterest_url` text COLLATE utf8mb4_unicode_ci,
  `tumblr_url` text COLLATE utf8mb4_unicode_ci,
  `flickr_url` text COLLATE utf8mb4_unicode_ci,
  `recaptcha_key` text COLLATE utf8mb4_unicode_ci,
  `recaptcha_secret` text COLLATE utf8mb4_unicode_ci,
  `facebook_key` text COLLATE utf8mb4_unicode_ci,
  `facebook_secret` text COLLATE utf8mb4_unicode_ci,
  `twitter_key` text COLLATE utf8mb4_unicode_ci,
  `twitter_secret` text COLLATE utf8mb4_unicode_ci,
  `google_plus_key` text COLLATE utf8mb4_unicode_ci,
  `google_plus_secret` text COLLATE utf8mb4_unicode_ci,
  `google_map_api` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_width` text COLLATE utf8mb4_unicode_ci,
  `image_height` text COLLATE utf8mb4_unicode_ci,
  `image_size` text COLLATE utf8mb4_unicode_ci,
  `file_type` text COLLATE utf8mb4_unicode_ci,
  `notification_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = toastr; 2 = sweetalert; 3 = notifyjs',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `company_name`, `site_title`, `site_title_bn`, `site_short_description`, `site_short_description_bn`, `site_header_logo`, `site_footer_logo`, `site_favicon`, `site_banner_image`, `site_email`, `site_phone_primary`, `site_phone_secondary`, `site_address`, `mail_driver`, `mail_host`, `mail_port`, `mail_username`, `mail_password`, `mail_encryption`, `facebook_url`, `twitter_url`, `google_plus_url`, `linkedin_url`, `youtube_url`, `instagram_url`, `pinterest_url`, `tumblr_url`, `flickr_url`, `recaptcha_key`, `recaptcha_secret`, `facebook_key`, `facebook_secret`, `twitter_key`, `twitter_secret`, `google_plus_key`, `google_plus_secret`, `google_map_api`, `image_width`, `image_height`, `image_size`, `file_type`, `notification_type`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Laravel', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sliders`
--

CREATE TABLE `sliders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `description_en` longtext COLLATE utf8mb4_unicode_ci,
  `description_bn` longtext COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sliders`
--

INSERT INTO `sliders` (`id`, `description_en`, `description_bn`, `image`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, '202204121300202204041129slider1.jpg', 1, 1, NULL, '2022-04-12 07:00:55', '2022-04-12 07:00:55'),
(2, NULL, NULL, '202204121501202204041129slider3.jpg', 1, 1, NULL, '2022-04-12 09:01:28', '2022-04-12 09:01:28');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `mobile`, `email_verified_at`, `status`, `password`, `deleted_at`, `image`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Web Admin', NULL, 'admin@gmail.com', NULL, NULL, 1, '$2y$10$dQm/Qmb59i2siK7B.uu5tummJhFg8G.MZch/wHRpk08KDzjVlsa06', NULL, '202204131408male_1.2.png', NULL, NULL, '2022-04-13 08:09:13'),
(2, 'Mamun', NULL, 'mamun@gmail.com', '01759457406', NULL, 1, '$2y$10$CcWz27EWCYI53zwLeQdU0ebSBbSLT8fZg8V90NEdLVuJqcvNlLDsC', NULL, NULL, NULL, '2022-04-12 07:43:41', '2022-04-12 07:43:41');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 2, 2, '2022-04-12 07:43:41', '2022-04-12 07:43:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `abouts`
--
ALTER TABLE `abouts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `frontend_menus`
--
ALTER TABLE `frontend_menus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `galleries`
--
ALTER TABLE `galleries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `galleries_gallery_category_id_foreign` (`gallery_category_id`);

--
-- Indexes for table `gallery_categories`
--
ALTER TABLE `gallery_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `home_links`
--
ALTER TABLE `home_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_permissions`
--
ALTER TABLE `menu_permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_permissions_menu_id_foreign` (`menu_id`),
  ADD KEY `menu_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `menu_posts`
--
ALTER TABLE `menu_posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_routes`
--
ALTER TABLE `menu_routes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notices`
--
ALTER TABLE `notices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sliders`
--
ALTER TABLE `sliders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_roles_user_id_foreign` (`user_id`),
  ADD KEY `user_roles_role_id_foreign` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `abouts`
--
ALTER TABLE `abouts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `frontend_menus`
--
ALTER TABLE `frontend_menus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `galleries`
--
ALTER TABLE `galleries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `gallery_categories`
--
ALTER TABLE `gallery_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `home_links`
--
ALTER TABLE `home_links`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `menu_permissions`
--
ALTER TABLE `menu_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `menu_posts`
--
ALTER TABLE `menu_posts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_routes`
--
ALTER TABLE `menu_routes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notices`
--
ALTER TABLE `notices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sliders`
--
ALTER TABLE `sliders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `galleries`
--
ALTER TABLE `galleries`
  ADD CONSTRAINT `galleries_gallery_category_id_foreign` FOREIGN KEY (`gallery_category_id`) REFERENCES `gallery_categories` (`id`);

--
-- Constraints for table `menu_permissions`
--
ALTER TABLE `menu_permissions`
  ADD CONSTRAINT `menu_permissions_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`),
  ADD CONSTRAINT `menu_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  ADD CONSTRAINT `user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
