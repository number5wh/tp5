/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50725
Source Host           : localhost:3306
Source Database       : gamefc

Target Server Type    : MYSQL
Target Server Version : 50725
File Encoding         : 65001

Date: 2019-03-19 17:59:05
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for bankinfo
-- ----------------------------
DROP TABLE IF EXISTS `bankinfo`;
CREATE TABLE `bankinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proxy_id` varchar(100) DEFAULT NULL,
  `alipay` varchar(300) DEFAULT NULL,
  `alipay_name` varchar(100) DEFAULT NULL,
  `cardaccount` varchar(100) DEFAULT NULL COMMENT '卡号 ',
  `bank` varchar(100) DEFAULT NULL COMMENT '开户行',
  `name` varchar(300) DEFAULT NULL COMMENT '姓名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for checklog
-- ----------------------------
DROP TABLE IF EXISTS `checklog`;
CREATE TABLE `checklog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orderid` varchar(30) DEFAULT NULL,
  `proxy_id` varchar(255) DEFAULT NULL,
  `amount` decimal(18,2) DEFAULT '0.00' COMMENT '结算金额',
  `balance` decimal(18,2) DEFAULT '0.00' COMMENT '结算余额',
  `checktype` int(11) DEFAULT NULL,
  `alipay` varchar(300) DEFAULT NULL,
  `alipay_name` varchar(100) DEFAULT NULL,
  `bank` varchar(100) DEFAULT NULL,
  `name` varchar(30) DEFAULT NULL,
  `cardaccount` varchar(50) DEFAULT NULL,
  `descript` varchar(1000) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `createtime` int(11) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL,
  `checktime` datetime DEFAULT NULL,
  `checkuser` varchar(100) DEFAULT NULL,
  `info` varchar(1000) DEFAULT NULL,
  `tax` decimal(18,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `proxy_id` (`proxy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for dictionary
-- ----------------------------
DROP TABLE IF EXISTS `dictionary`;
CREATE TABLE `dictionary` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(30) DEFAULT NULL,
  `value` varchar(500) DEFAULT NULL,
  `descript` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for incomelog
-- ----------------------------
DROP TABLE IF EXISTS `incomelog`;
CREATE TABLE `incomelog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_id` varchar(50) DEFAULT NULL COMMENT '玩家直接代理',
  `proxy_id` varchar(50) DEFAULT NULL,
  `typeid` int(11) DEFAULT '0',
  `totaltax` varchar(50) DEFAULT NULL,
  `changmoney` varchar(100) DEFAULT NULL,
  `date` int(11) DEFAULT NULL,
  `createtime` int(11) DEFAULT NULL,
  `createday` int(11) DEFAULT NULL COMMENT '新建日期',
  `descript` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for paytime
-- ----------------------------
DROP TABLE IF EXISTS `paytime`;
CREATE TABLE `paytime` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proxy_id` varchar(30) DEFAULT NULL,
  `userid` varchar(30) DEFAULT NULL,
  `totalfee` decimal(10,2) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL,
  `updatetime` datetime DEFAULT NULL,
  `createday` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='玩家充值明细表';

-- ----------------------------
-- Table structure for planlog
-- ----------------------------
DROP TABLE IF EXISTS `planlog`;
CREATE TABLE `planlog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `plan` varchar(50) DEFAULT '' COMMENT '计划任务名称',
  `day` int(11) DEFAULT '0' COMMENT '执行日期',
  `inserttime` datetime DEFAULT NULL,
  `updatetime` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx` (`plan`,`day`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for player
-- ----------------------------
DROP TABLE IF EXISTS `player`;
CREATE TABLE `player` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gameid` varchar(30) DEFAULT NULL COMMENT '游戏id',
  `proxy_id` varchar(100) DEFAULT NULL,
  `userid` int(11) DEFAULT NULL COMMENT '用户id',
  `accountid` varchar(300) DEFAULT NULL COMMENT '用户账号',
  `leftmoney` decimal(10,2) DEFAULT '0.00' COMMENT '余额',
  `nickname` varchar(100) DEFAULT NULL,
  `ismobile` tinyint(1) DEFAULT '0' COMMENT '是否绑定手机',
  `regtime` varchar(30) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL,
  `updatemoney` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `userid` (`userid`) USING BTREE,
  KEY `proxy_id` (`proxy_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for playergame
-- ----------------------------
DROP TABLE IF EXISTS `playergame`;
CREATE TABLE `playergame` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `nickname` varchar(100) DEFAULT NULL,
  `roomname` varchar(100) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL,
  `changemoney` decimal(10,2) DEFAULT '0.00' COMMENT '输赢情况',
  `inserttime` datetime DEFAULT NULL,
  `proxy_id` varchar(100) DEFAULT NULL,
  `updatetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `proxy_id` (`proxy_id`) USING BTREE,
  KEY `userid` (`userid`,`roomname`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for playerorder
-- ----------------------------
DROP TABLE IF EXISTS `playerorder`;
CREATE TABLE `playerorder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gameid` int(11) DEFAULT NULL,
  `proxy_id` varchar(100) DEFAULT NULL COMMENT '分成代理',
  `userid` varchar(30) DEFAULT NULL,
  `game` varchar(100) DEFAULT NULL,
  `total_tax` decimal(18,2) DEFAULT '0.00',
  `date` int(11) DEFAULT NULL,
  `createday` int(11) DEFAULT '0',
  `createtime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index1` (`proxy_id`,`createtime`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='税收获取';

-- ----------------------------
-- Table structure for proxy
-- ----------------------------
DROP TABLE IF EXISTS `proxy`;
CREATE TABLE `proxy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) DEFAULT NULL COMMENT '生成6位代理号',
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(300) DEFAULT NULL,
  `salt` varchar(30) DEFAULT NULL,
  `allow_addproxy` tinyint(1) DEFAULT '0' COMMENT '允许添加下级代理0=不 1=是',
  `parent_id` varchar(255) DEFAULT NULL,
  `proxy_id` varchar(20) DEFAULT NULL,
  `lock` int(11) DEFAULT NULL,
  `grade` int(11) DEFAULT NULL COMMENT '代理等级',
  `nickname` varchar(30) DEFAULT NULL,
  `avatar` varchar(1000) DEFAULT NULL,
  `percent` int(11) DEFAULT NULL,
  `bind_mobile` varchar(20) DEFAULT NULL,
  `bind_ip` int(11) DEFAULT NULL,
  `balance` decimal(10,2) DEFAULT '0.00',
  `historyin` decimal(10,2) DEFAULT '0.00',
  `last_login` datetime DEFAULT NULL,
  `regtime` datetime DEFAULT NULL,
  `descript` text,
  `check_pass` varchar(100) DEFAULT NULL,
  `last_ip` varchar(20) DEFAULT NULL,
  `last_time` int(11) DEFAULT NULL,
  `logtimes` int(11) DEFAULT NULL,
  `islock` int(11) DEFAULT '0',
  `ban` int(11) DEFAULT '0',
  `isdel` int(11) DEFAULT '0',
  `updatetime` int(11) DEFAULT '0',
  `token` varchar(32) DEFAULT NULL COMMENT '记住登录标识',
  `identifier` varchar(32) DEFAULT NULL COMMENT '记住登录验证',
  `timeout` int(11) DEFAULT NULL COMMENT '记住登录超时时间',
  PRIMARY KEY (`id`),
  KEY `code` (`code`) USING BTREE,
  KEY `parent` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for teamlevel
-- ----------------------------
DROP TABLE IF EXISTS `teamlevel`;
CREATE TABLE `teamlevel` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL COMMENT '用户账号',
  `proxy_id` varchar(50) DEFAULT NULL COMMENT '用户Id',
  `parent_id` varchar(50) DEFAULT NULL COMMENT '上级Id',
  `level` int(10) DEFAULT NULL COMMENT '层级',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for thirdpaytime
-- ----------------------------
DROP TABLE IF EXISTS `thirdpaytime`;
CREATE TABLE `thirdpaytime` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `loginid` varchar(50) DEFAULT NULL,
  `totalfee` varchar(50) DEFAULT NULL,
  `updatetime` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx` (`loginid`,`totalfee`,`updatetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for thirdplayer
-- ----------------------------
DROP TABLE IF EXISTS `thirdplayer`;
CREATE TABLE `thirdplayer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` varchar(30) DEFAULT NULL,
  `accountid` varchar(100) DEFAULT NULL,
  `nickname` varchar(100) DEFAULT NULL,
  `regtime` varchar(50) DEFAULT NULL,
  `ismobile` tinyint(1) DEFAULT '0',
  `balance` decimal(10,2) DEFAULT '0.00' COMMENT '玩家余额',
  `inserttime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `userid` (`userid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for thirdplayergame
-- ----------------------------
DROP TABLE IF EXISTS `thirdplayergame`;
CREATE TABLE `thirdplayergame` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `nickname` varchar(100) DEFAULT NULL,
  `roomname` varchar(100) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL,
  `changemoney` varchar(100) DEFAULT '0',
  `inserttime` datetime DEFAULT NULL,
  `updatetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx` (`userid`,`roomname`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for third_player_order
-- ----------------------------
DROP TABLE IF EXISTS `third_player_order`;
CREATE TABLE `third_player_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` varchar(100) DEFAULT NULL,
  `game` varchar(300) DEFAULT NULL,
  `tax` varchar(100) DEFAULT NULL,
  `date` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
