/*
SQLyog 企业版 - MySQL GUI v8.14 
MySQL - 5.5.36 : Database - at
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`at` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_bin */;

USE `at`;

/*Table structure for table `at_alert` */

DROP TABLE IF EXISTS `at_alert`;

CREATE TABLE `at_alert` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pro_id` char(20) NOT NULL COMMENT '项目ID',
  `pro_name` char(50) NOT NULL COMMENT '项目名称',
  `depsx` char(20) DEFAULT NULL COMMENT '部门列表',
  `alert_msg` char(50) DEFAULT NULL COMMENT '报警信息',
  `lx` tinyint(4) DEFAULT NULL COMMENT '报警信息类型',
  `msg_md5` char(50) DEFAULT NULL COMMENT '报警信息唯一码',
  `alert_time` datetime DEFAULT NULL COMMENT '生成报警日期',
  `alert_day_num` tinyint(4) DEFAULT NULL COMMENT '报警持续天数',
  `alert_last_day` datetime DEFAULT NULL COMMENT '报警截止日期',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='报警表';

/*Data for the table `at_alert` */

/*Table structure for table `at_pro2user` */

DROP TABLE IF EXISTS `at_pro2user`;

CREATE TABLE `at_pro2user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pro_id` char(8) NOT NULL COMMENT '项目ID',
  `user_id` int(11) unsigned NOT NULL COMMENT '用户ID',
  `department` char(2) DEFAULT NULL COMMENT '部门',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='项目2用户表';

/*Data for the table `at_pro2user` */

/*Table structure for table `at_project` */

DROP TABLE IF EXISTS `at_project`;

CREATE TABLE `at_project` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pro_id` char(30) NOT NULL COMMENT '项目ID',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '标识 1：新建 2:正常已经审核过 3.已经删除',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='项目总表';

/*Data for the table `at_project` */

/*Table structure for table `at_user` */

DROP TABLE IF EXISTS `at_user`;

CREATE TABLE `at_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` char(30) NOT NULL COMMENT '用户姓名',
  `email` char(30) NOT NULL COMMENT '电子邮件,作为用户登录名',
  `password` char(100) NOT NULL COMMENT '登录密码',
  `department` char(2) DEFAULT NULL COMMENT '部门',
  `userflag` tinyint(4) NOT NULL DEFAULT '1' COMMENT '用户标识 1:普通用户 2.用户管理员',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '用户标识 1：新建自注册用户 2:正常已经审核过的用户 3.已经删除用户',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='用户表';

/*Data for the table `at_user` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
