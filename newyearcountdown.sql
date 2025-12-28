-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2025-12-28 12:02:29
-- 服务器版本： 5.7.44-log
-- PHP 版本： 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `newyearcountdown`
--

-- --------------------------------------------------------

--
-- 表的结构 `blessings`
--

CREATE TABLE `blessings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `blessings`
--
-- 初始化为空表

-- --------------------------------------------------------

--
-- 表的结构 `bless_count`
--

CREATE TABLE `bless_count` (
  `id` int(11) NOT NULL DEFAULT '1',
  `count` bigint(20) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `bless_count`
--

INSERT INTO `bless_count` (`id`, `count`) VALUES
(1, 0);

-- --------------------------------------------------------

--
-- 表的结构 `ip_limit`
--

CREATE TABLE `ip_limit` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(50) NOT NULL COMMENT '用户IP地址',
  `last_submit_time` int(11) NOT NULL COMMENT '最后提交时间（Unix时间戳）',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='IP发言频率限制表';

--
-- 转存表中的数据 `ip_limit`
--
-- 初始化为空表

-- --------------------------------------------------------

--
-- 表的结构 `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `nickname` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `message`
--

-- 初始化为空表

--
-- 转储表的索引
--

--
-- 表的索引 `blessings`
--
ALTER TABLE `blessings`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `bless_count`
--
ALTER TABLE `bless_count`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `ip_limit`
--
ALTER TABLE `ip_limit`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_ip` (`ip_address`) COMMENT 'IP唯一索引，避免重复记录';

--
-- 表的索引 `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `blessings`
--
ALTER TABLE `blessings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- 使用表AUTO_INCREMENT `ip_limit`
--
ALTER TABLE `ip_limit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- 使用表AUTO_INCREMENT `message`
--
ALTER TABLE `message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
