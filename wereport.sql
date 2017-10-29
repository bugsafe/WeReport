/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : wereport

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2017-10-29 17:51:56
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for we_bug_list
-- ----------------------------
DROP TABLE IF EXISTS `we_bug_list`;
CREATE TABLE `we_bug_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bug_name` varchar(255) NOT NULL COMMENT '漏洞名称',
  `bug_desc` text NOT NULL COMMENT '漏洞描述',
  `bug_repair` text NOT NULL COMMENT '修复建议',
  `add_time` int(11) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for we_poject_list
-- ----------------------------
DROP TABLE IF EXISTS `we_poject_list`;
CREATE TABLE `we_poject_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `temp` varchar(50) NOT NULL DEFAULT '0' COMMENT '模板id',
  `poject_name` varchar(255) NOT NULL COMMENT '项目名称',
  `poject_logo` varchar(255) DEFAULT NULL COMMENT '项目logo',
  `poject_start_time` int(11) NOT NULL COMMENT '开始时间',
  `poject_end_time` int(11) NOT NULL COMMENT '结束时间',
  `poject_com` varchar(255) NOT NULL,
  `poject_person` varchar(255) NOT NULL COMMENT '项目实施人员',
  `add_time` int(11) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for we_result_list
-- ----------------------------
DROP TABLE IF EXISTS `we_result_list`;
CREATE TABLE `we_result_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL COMMENT '报告id',
  `bid` int(11) NOT NULL COMMENT '漏洞id',
  `sid` int(11) NOT NULL COMMENT '系统id',
  `bug_url` varchar(255) NOT NULL COMMENT '漏洞链接',
  `bug_param` varchar(255) NOT NULL COMMENT '漏洞参数',
  `bug_level` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0低危,1中危,2高危,3严重',
  `bug_img` varchar(255) NOT NULL COMMENT '漏洞图片',
  `add_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for we_system_list
-- ----------------------------
DROP TABLE IF EXISTS `we_system_list`;
CREATE TABLE `we_system_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `system_name` varchar(255) NOT NULL COMMENT '漏洞名称',
  `system_url` varchar(255) NOT NULL,
  `system_ip` varchar(255) NOT NULL,
  `add_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
