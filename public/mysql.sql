-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 2019-03-18 07:11:34
-- 服务器版本： 5.7.19
-- PHP Version: 7.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tp51_admin`
--

-- --------------------------------------------------------

--
-- 表的结构 `admin`
--

CREATE TABLE `admin` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '主键',
  `name` varchar(50) NOT NULL COMMENT '用户名',
  `password` varchar(32) NOT NULL COMMENT '密码',
  `encrypt` char(32) NOT NULL COMMENT '密码加盐验证',
  `login_count` int(10) DEFAULT '0' COMMENT '登陆次数',
  `create_time` int(10) NOT NULL COMMENT '注册时间',
  `create_admin` int(10) NOT NULL COMMENT '注册管理员',
  `update_time` int(10) DEFAULT '0' COMMENT '更新时间',
  `update_admin` int(10) DEFAULT '0' COMMENT '更新管理员',
  `reg_ip` bigint(20) NOT NULL COMMENT '注册IP',
  `last_time` int(10) DEFAULT '0' COMMENT '最后登录时间',
  `last_ip` varchar(32) DEFAULT '0' COMMENT '最后登录IP',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='后台管理员表';

--
-- 转存表中的数据 `admin`
--

INSERT INTO `admin` (`id`, `name`, `password`, `encrypt`, `login_count`, `create_time`, `create_admin`, `update_time`, `update_admin`, `reg_ip`, `last_time`, `last_ip`, `status`) VALUES
(1, 'admin', '607347df3f4c4a76983c0356d9eb3d0f', 'kCgYhn', 61, 1528351249, 1, 1552892005, 1, 2130706433, 1552892005, '2130706433', 1),
(2, 'liutao', '6b7c88893764ab44eeea2b046dafc353', 'pAExhQ', 4, 1528359704, 1, 1552892057, 1, 2130706433, 1549856457, '2130706433', 1);

-- --------------------------------------------------------

--
-- 表的结构 `admin_group`
--

CREATE TABLE `admin_group` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '自增主键',
  `admin_id` int(10) UNSIGNED NOT NULL COMMENT '管理员id',
  `group_id` int(10) UNSIGNED NOT NULL COMMENT '管理组id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理分组成员表';

--
-- 转存表中的数据 `admin_group`
--

INSERT INTO `admin_group` (`id`, `admin_id`, `group_id`) VALUES
(1, 1, 1),
(15, 2, 2);

-- --------------------------------------------------------

--
-- 表的结构 `admin_info`
--

CREATE TABLE `admin_info` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '主键',
  `admin_id` int(10) NOT NULL COMMENT '用户ID',
  `nickname` varchar(100) DEFAULT NULL COMMENT '姓名',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱',
  `mobile` varchar(16) DEFAULT NULL COMMENT '手机号码',
  `sex` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别',
  `avatar` varchar(200) DEFAULT NULL COMMENT '头像',
  `attach_id` int(10) NOT NULL DEFAULT '0',
  `qq` varchar(16) DEFAULT NULL COMMENT 'QQ',
  `birthday` int(10) DEFAULT NULL COMMENT '生日',
  `info` varchar(1000) DEFAULT NULL COMMENT '用户信息',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `create_admin` int(10) NOT NULL COMMENT '创建管理员',
  `update_time` int(10) DEFAULT '0' COMMENT '编辑时间',
  `update_admin` int(10) DEFAULT '0' COMMENT '编辑管理员'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='管理员详细信息';

--
-- 转存表中的数据 `admin_info`
--

INSERT INTO `admin_info` (`id`, `admin_id`, `nickname`, `email`, `mobile`, `sex`, `avatar`, `attach_id`, `qq`, `birthday`, `info`, `create_time`, `create_admin`, `update_time`, `update_admin`) VALUES
(1, 1, '学而思', '474260946@qq.com', '17608004352', 0, 'http://qiniu.chiruan.net/image1.png', 0, '474260946', NULL, '', 1528353423, 1, 1530261735, 1);

-- --------------------------------------------------------

--
-- 表的结构 `group`
--

CREATE TABLE `group` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '用户组id,自增主键',
  `name` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '用户组中文名称',
  `desc` varchar(80) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '描述信息',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户组状态：为1正常，为0禁用,-1为删除',
  `create_time` int(10) NOT NULL,
  `create_admin` int(10) NOT NULL,
  `update_time` int(10) DEFAULT NULL,
  `update_admin` int(10) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='管理权限分组表';

--
-- 转存表中的数据 `group`
--

INSERT INTO `group` (`id`, `name`, `desc`, `status`, `create_time`, `create_admin`, `update_time`, `update_admin`) VALUES
(1, '超级管理员', '拥有所有的系统操作权限', 1, 1523182741, 1, 1549858785, 1),
(2, '普通管理员', '后台系统普通操作人员', 1, 1523182775, 1, 1552889492, 1);

-- --------------------------------------------------------

--
-- 表的结构 `group_menu`
--

CREATE TABLE `group_menu` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '自增主键',
  `group_id` int(10) UNSIGNED NOT NULL COMMENT '管理组id',
  `menu_id` int(10) UNSIGNED NOT NULL COMMENT '菜单id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理组菜单表';

--
-- 转存表中的数据 `group_menu`
--

INSERT INTO `group_menu` (`id`, `group_id`, `menu_id`) VALUES
(1, 2, 1),
(2, 2, 2),
(3, 2, 5),
(4, 2, 6),
(5, 2, 3),
(6, 2, 7),
(7, 2, 8),
(8, 2, 9),
(9, 2, 4),
(10, 2, 10),
(11, 2, 11),
(12, 2, 12);

-- --------------------------------------------------------

--
-- 表的结构 `menu`
--

CREATE TABLE `menu` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '主键',
  `parent_id` int(10) UNSIGNED NOT NULL COMMENT '父id',
  `module` varchar(10) NOT NULL DEFAULT 'admin' COMMENT '所属模块',
  `level` tinyint(1) NOT NULL COMMENT '1-项目;2-模块;3-操作',
  `controller` varchar(80) NOT NULL COMMENT '控制器',
  `action` varchar(80) NOT NULL COMMENT '方法',
  `name` varchar(20) NOT NULL COMMENT '规则中文名称',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：为1正常，为0禁用',
  `is_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否显示',
  `params` varchar(500) DEFAULT NULL COMMENT '规则表达式，为空表示存在就验证，不为空表示按照条件验证',
  `icon` varchar(50) DEFAULT NULL COMMENT '节点图标',
  `sort` mediumint(8) DEFAULT '50' COMMENT '排序',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `create_admin` int(10) NOT NULL COMMENT '创建管理员',
  `update_time` int(10) DEFAULT NULL COMMENT '编辑时间',
  `update_admin` int(10) DEFAULT NULL COMMENT '编辑管理员'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='菜单表';

--
-- 转存表中的数据 `menu`
--

INSERT INTO `menu` (`id`, `parent_id`, `module`, `level`, `controller`, `action`, `name`, `status`, `is_show`, `params`, `icon`, `sort`, `create_time`, `create_admin`, `update_time`, `update_admin`) VALUES
(1, 0, 'admin', 1, '', '', '系统管理', 1, 1, '', '', 1, 1549855351, 1, 1550197885, 1),
(2, 1, 'admin', 2, 'menu', 'index', '菜单管理', 1, 1, '', '', 0, 1549855487, 1, 1552892960, 1),
(3, 1, 'admin', 2, 'group', 'index', '管理组', 1, 1, '', '', 0, 1549855558, 1, 1549855558, NULL),
(4, 1, 'admin', 2, 'admin', 'index', '管理员', 1, 1, '', '', 0, 1549855664, 1, 1549855664, NULL),
(5, 2, 'admin', 3, 'menu', 'add', '添加菜单', 1, 1, '', '', 0, 1549855909, 1, 1552889453, 1),
(6, 2, 'admin', 3, 'menu', 'edit', '编辑菜单', 1, 1, '', '', 0, 1549855938, 1, 1549855938, NULL),
(7, 3, 'admin', 3, 'group', 'add', '添加管理组', 1, 1, '', '', 0, 1549856041, 1, 1549856041, NULL),
(8, 3, 'admin', 3, 'group', 'edit', '编辑管理组', 1, 1, '', '', 0, 1549856117, 1, 1549856117, NULL),
(9, 3, 'admin', 3, 'group', 'setPrivilege', '设置权限', 1, 1, '', '', 0, 1549856219, 1, 1549856219, NULL),
(10, 4, 'admin', 3, 'admin', 'add', '添加管理员', 1, 1, '', '', 0, 1549856283, 1, 1549856283, NULL),
(11, 4, 'admin', 3, 'admin', 'edit', '编辑管理员', 1, 1, '', '', 0, 1549856301, 1, 1549856301, NULL),
(12, 4, 'admin', 3, 'admin', 'detail', '管理员详情', 1, 1, '', '', 0, 1549856338, 1, 1549856338, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `migrations`
--

CREATE TABLE `migrations` (
  `version` bigint(20) NOT NULL,
  `migration_name` varchar(100) DEFAULT NULL,
  `start_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `end_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `breakpoint` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `wx_info`
--

CREATE TABLE `wx_info` (
  `id` int(11) NOT NULL,
  `open_id` varchar(128) NOT NULL COMMENT 'openid',
  `nickname` varchar(128) NOT NULL COMMENT '昵称',
  `header_img_url` varchar(200) NOT NULL COMMENT '头像',
  `sex` int(2) NOT NULL DEFAULT '0' COMMENT ' 性别：1-男 2-女 0-未知 ',
  `country` varchar(32) DEFAULT NULL COMMENT '国家',
  `province` varchar(32) DEFAULT NULL COMMENT '省份',
  `city` varchar(32) DEFAULT NULL COMMENT '城市',
  `union_id` varchar(64) DEFAULT NULL COMMENT '公众号联合id',
  `type` int(5) NOT NULL DEFAULT '0',
  `login_count` int(10) NOT NULL DEFAULT '0' COMMENT '登陆次数',
  `privilege` varchar(1024) DEFAULT NULL COMMENT '用户特权信息	',
  `create_ip` varchar(64) DEFAULT NULL,
  `update_ip` varchar(64) DEFAULT NULL,
  `user_agent` varchar(1024) DEFAULT NULL COMMENT '用户端	',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信小程序授权变';

--
-- 转存表中的数据 `wx_info`
--

INSERT INTO `wx_info` (`id`, `open_id`, `nickname`, `header_img_url`, `sex`, `country`, `province`, `city`, `union_id`, `type`, `login_count`, `privilege`, `create_ip`, `update_ip`, `user_agent`, `create_time`, `update_time`) VALUES
(1, 'ojYPyswKr6x6AmPPz3GKgmy5pJXY', '꯭十꯭年꯭丶꯭老꯭鱼꯭', 'http://thirdwx.qlogo.cn/mmopen/vi_32/4AjPYvyBdqehNmhl4vpywkBpibtO9kTwKqlc6WiaaB97dH9P7K6DHkgeS8J32MWXCgOuC9zpB7s9grnDSSwDpNmg/132', 1, '中国', '四川', '成都', '', 1, 1, '', '127.0.0.1', '127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1', '2019-02-28 07:04:47', '2019-02-18 07:04:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_group`
--
ALTER TABLE `admin_group`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_info`
--
ALTER TABLE `admin_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `group`
--
ALTER TABLE `group`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `group_menu`
--
ALTER TABLE `group_menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`) USING BTREE,
  ADD KEY `module` (`module`) USING BTREE,
  ADD KEY `level` (`level`) USING BTREE,
  ADD KEY `controller` (`controller`) USING BTREE,
  ADD KEY `action` (`action`) USING BTREE;

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `wx_info`
--
ALTER TABLE `wx_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `open_id` (`open_id`),
  ADD KEY `nickname` (`nickname`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键', AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `admin_group`
--
ALTER TABLE `admin_group`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增主键', AUTO_INCREMENT=16;

--
-- 使用表AUTO_INCREMENT `admin_info`
--
ALTER TABLE `admin_info`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键', AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `group`
--
ALTER TABLE `group`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户组id,自增主键', AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `group_menu`
--
ALTER TABLE `group_menu`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增主键', AUTO_INCREMENT=13;

--
-- 使用表AUTO_INCREMENT `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键', AUTO_INCREMENT=13;

--
-- 使用表AUTO_INCREMENT `wx_info`
--
ALTER TABLE `wx_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
