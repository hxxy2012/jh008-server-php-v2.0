/*
SQLyog Ultimate v11.24 (32 bit)
MySQL - 5.5.37-log : Database - gather_db
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`gather_db` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `gather_db`;

/*Table structure for table `act_business_map` */

DROP TABLE IF EXISTS `act_business_map`;

CREATE TABLE `act_business_map` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '活动所属商家关联id',
  `act_id` int(10) unsigned NOT NULL COMMENT '活动id',
  `b_id` int(10) unsigned NOT NULL COMMENT '商家id',
  `status` tinyint(4) NOT NULL DEFAULT '-1' COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `fk_actbmap_act_id` (`act_id`),
  KEY `fk_actbmap_b_id` (`b_id`),
  CONSTRAINT `fk_actbmap_act_id` FOREIGN KEY (`act_id`) REFERENCES `act_info` (`id`),
  CONSTRAINT `fk_actbmap_b_id` FOREIGN KEY (`b_id`) REFERENCES `business_info` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Table structure for table `act_checkin_map` */

DROP TABLE IF EXISTS `act_checkin_map`;

CREATE TABLE `act_checkin_map` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '活动签到关联id',
  `act_id` int(10) unsigned NOT NULL COMMENT '活动id',
  `u_id` int(10) unsigned DEFAULT NULL COMMENT '用户id',
  `lon` double unsigned DEFAULT NULL COMMENT '经度',
  `lat` double unsigned DEFAULT NULL COMMENT '纬度',
  `address` varchar(64) DEFAULT NULL COMMENT '地理位置信息',
  `status` tinyint(4) NOT NULL DEFAULT '-1' COMMENT '状态：-1删除，0正常',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `descri` varchar(120) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `fk_checkin_act_id` (`act_id`),
  KEY `fk_checkin_u_id` (`u_id`),
  CONSTRAINT `fk_checkin_act_id` FOREIGN KEY (`act_id`) REFERENCES `act_info` (`id`),
  CONSTRAINT `fk_checkin_u_id` FOREIGN KEY (`u_id`) REFERENCES `user_info` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `act_head_img_map` */

DROP TABLE IF EXISTS `act_head_img_map`;

CREATE TABLE `act_head_img_map` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '活动首图关联id',
  `act_id` int(10) unsigned NOT NULL COMMENT '活动id',
  `img_id` int(10) unsigned NOT NULL COMMENT '图像id',
  `status` tinyint(1) NOT NULL DEFAULT '-1' COMMENT '状态：-1删除，0可用，1当前使用',
  PRIMARY KEY (`id`),
  KEY `fk_ahi_act_id` (`act_id`),
  KEY `fk_ahi_img_id` (`img_id`),
  CONSTRAINT `fk_ahi_act_id` FOREIGN KEY (`act_id`) REFERENCES `act_info` (`id`),
  CONSTRAINT `fk_ahi_img_id` FOREIGN KEY (`img_id`) REFERENCES `img_info` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Table structure for table `act_img_map` */

DROP TABLE IF EXISTS `act_img_map`;

CREATE TABLE `act_img_map` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '活动图像关联id',
  `act_id` int(10) unsigned NOT NULL COMMENT '活动id',
  `img_id` int(10) unsigned NOT NULL COMMENT '图像id',
  `status` tinyint(1) NOT NULL DEFAULT '-1' COMMENT '状态：-1删除，0正常',
  PRIMARY KEY (`id`),
  KEY `fk_actimg_act_id` (`act_id`),
  KEY `fk_actimg_img_id` (`img_id`),
  CONSTRAINT `fk_actimg_act_id` FOREIGN KEY (`act_id`) REFERENCES `act_info` (`id`),
  CONSTRAINT `fk_actimg_img_id` FOREIGN KEY (`img_id`) REFERENCES `img_info` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Table structure for table `act_info` */

DROP TABLE IF EXISTS `act_info`;

CREATE TABLE `act_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '活动id[自增]',
  `title` varchar(32) NOT NULL COMMENT '标题',
  `intro` varchar(240) DEFAULT NULL COMMENT '活动简述',
  `addr_city` varchar(24) NOT NULL COMMENT '地址（城市）',
  `addr_area` varchar(24) DEFAULT NULL COMMENT '地址（区）',
  `addr_road` varchar(24) DEFAULT NULL COMMENT '地址（路）',
  `addr_num` varchar(48) DEFAULT NULL COMMENT '地址（号）',
  `addr_route` varchar(240) DEFAULT NULL COMMENT '地址（路线）',
  `b_time` datetime NOT NULL COMMENT '开始时间',
  `e_time` datetime NOT NULL COMMENT '结束时间',
  `t_status` tinyint(1) unsigned NOT NULL DEFAULT '4' COMMENT '时间状态：1即将开始，2进行中，3筹备中，4已结束',
  `detail` varchar(240) DEFAULT NULL COMMENT '活动详情',
  `detail_all` text COMMENT '活动图文详情',
  `status` int(11) NOT NULL DEFAULT '-1' COMMENT '状态：-1删除，0正常',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL COMMENT '更新时间',
  `publish_time` datetime DEFAULT NULL COMMENT '发布时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

/*Table structure for table `act_lov_user_map` */

DROP TABLE IF EXISTS `act_lov_user_map`;

CREATE TABLE `act_lov_user_map` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '活动收藏关联id',
  `act_id` int(10) unsigned NOT NULL COMMENT '活动id',
  `u_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `status` tinyint(1) NOT NULL DEFAULT '-1' COMMENT '状态：-1删除，0正常',
  PRIMARY KEY (`id`),
  KEY `fk_au_act_id` (`act_id`),
  KEY `fk_au_u_id` (`u_id`),
  CONSTRAINT `fk_au_act_id` FOREIGN KEY (`act_id`) REFERENCES `act_info` (`id`),
  CONSTRAINT `fk_au_u_id` FOREIGN KEY (`u_id`) REFERENCES `user_info` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

/*Table structure for table `act_share_map` */

DROP TABLE IF EXISTS `act_share_map`;

CREATE TABLE `act_share_map` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '活动分享关联id',
  `act_id` int(10) unsigned NOT NULL COMMENT '活动id',
  `u_id` int(10) unsigned DEFAULT NULL COMMENT '分享者用户id',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `fk_actsh_act_id` (`act_id`),
  KEY `fk_actsh_u_id` (`u_id`),
  CONSTRAINT `fk_actsh_act_id` FOREIGN KEY (`act_id`) REFERENCES `act_info` (`id`),
  CONSTRAINT `fk_actsh_u_id` FOREIGN KEY (`u_id`) REFERENCES `user_info` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8;

/*Table structure for table `act_tag_map` */

DROP TABLE IF EXISTS `act_tag_map`;

CREATE TABLE `act_tag_map` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '活动标签关联id',
  `act_id` int(10) unsigned NOT NULL COMMENT '活动id',
  `tag_id` int(10) unsigned NOT NULL COMMENT '标签id',
  `status` tinyint(1) NOT NULL DEFAULT '-1' COMMENT '状态：-1删除，0正常',
  PRIMARY KEY (`id`),
  KEY `fk_ta_act_id` (`act_id`),
  KEY `fk_ta_tag_id` (`tag_id`),
  CONSTRAINT `fk_ta_act_id` FOREIGN KEY (`act_id`) REFERENCES `act_info` (`id`),
  CONSTRAINT `fk_ta_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `tag_info` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Table structure for table `admin_info` */

DROP TABLE IF EXISTS `admin_info`;

CREATE TABLE `admin_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '管理员id',
  `u_name` varchar(32) NOT NULL COMMENT '用户名',
  `salt` char(6) NOT NULL COMMENT '哈希标识',
  `u_pass` varchar(32) NOT NULL COMMENT '用户密码',
  `nick_name` varchar(32) DEFAULT NULL COMMENT '昵称',
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '性别：1男，2女',
  `status` tinyint(4) NOT NULL DEFAULT '-1' COMMENT '状态',
  `creat_time` datetime NOT NULL COMMENT '创建时间',
  `last_login_time` datetime NOT NULL COMMENT '最后登录时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Table structure for table `admin_operate_log` */

DROP TABLE IF EXISTS `admin_operate_log`;

CREATE TABLE `admin_operate_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '管理员日志id',
  `op_ter` int(10) unsigned DEFAULT NULL COMMENT '操作者id',
  `type` int(10) unsigned DEFAULT NULL COMMENT '日志类型',
  `operate` varchar(120) DEFAULT NULL COMMENT '操作内容',
  `status` tinyint(4) NOT NULL DEFAULT '-1' COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `fk_operlog_admin_id` (`op_ter`),
  CONSTRAINT `fk_operlog_admin_id` FOREIGN KEY (`op_ter`) REFERENCES `admin_info` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `app_info` */

DROP TABLE IF EXISTS `app_info`;

CREATE TABLE `app_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '版本id',
  `type` int(10) unsigned NOT NULL COMMENT '类型：1安卓',
  `code` int(10) unsigned NOT NULL COMMENT '版本号',
  `name` varchar(32) NOT NULL COMMENT '版本名称',
  `descri` varchar(120) DEFAULT NULL COMMENT '版本描述',
  `up_id` int(10) unsigned NOT NULL COMMENT '安装文件id',
  `status` tinyint(4) NOT NULL DEFAULT '-1' COMMENT '状态',
  `creat_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `fk_app_up_id` (`up_id`),
  CONSTRAINT `fk_app_up_id` FOREIGN KEY (`up_id`) REFERENCES `up_info` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Table structure for table `business_info` */

DROP TABLE IF EXISTS `business_info`;

CREATE TABLE `business_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '商家id',
  `u_name` varchar(32) NOT NULL COMMENT '用户名',
  `salt` char(6) NOT NULL COMMENT '哈希标识',
  `u_pass` varchar(32) NOT NULL COMMENT '用户密码',
  `status` tinyint(4) NOT NULL DEFAULT '-1' COMMENT '状态',
  `creat_time` datetime NOT NULL COMMENT '创建时间',
  `last_login_time` datetime NOT NULL COMMENT '最后登录时间',
  `name` varchar(64) DEFAULT NULL COMMENT '商户名称',
  `address` varchar(64) DEFAULT NULL COMMENT '地址',
  `contact_phone` varchar(32) DEFAULT NULL COMMENT '联系电话',
  `contact_email` varchar(32) DEFAULT NULL COMMENT '联系邮箱',
  `contact_descri` varchar(120) DEFAULT NULL COMMENT '其他联系方式',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Table structure for table `business_logo_img_map` */

DROP TABLE IF EXISTS `business_logo_img_map`;

CREATE TABLE `business_logo_img_map` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '商家logo关联id',
  `b_id` int(10) unsigned NOT NULL COMMENT '商家id',
  `img_id` int(10) unsigned NOT NULL COMMENT '图像id',
  `status` tinyint(4) NOT NULL COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `fk_bloimg_busi_id` (`b_id`),
  KEY `fk_bloimg_img_id` (`img_id`),
  CONSTRAINT `fk_bloimg_busi_id` FOREIGN KEY (`b_id`) REFERENCES `business_info` (`id`),
  CONSTRAINT `fk_bloimg_img_id` FOREIGN KEY (`img_id`) REFERENCES `img_info` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `business_operate_log` */

DROP TABLE IF EXISTS `business_operate_log`;

CREATE TABLE `business_operate_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '商家操作日志id',
  `op_ter` int(10) unsigned DEFAULT NULL COMMENT '操作者id',
  `type` int(10) unsigned DEFAULT NULL COMMENT '日志类型',
  `operate` varchar(120) DEFAULT NULL COMMENT '操作内容',
  `status` tinyint(4) NOT NULL COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `fk_buoper_busi_id` (`op_ter`),
  CONSTRAINT `fk_buoper_busi_id` FOREIGN KEY (`op_ter`) REFERENCES `business_info` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `img_info` */

DROP TABLE IF EXISTS `img_info`;

CREATE TABLE `img_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '图像id',
  `ori_name` varchar(32) DEFAULT NULL COMMENT '原始名称',
  `img_url` varchar(32) DEFAULT NULL COMMENT '图像url',
  `status` tinyint(1) NOT NULL DEFAULT '-1' COMMENT '状态：-1删除，0正常',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `descri` varchar(120) DEFAULT NULL COMMENT '描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=474 DEFAULT CHARSET=utf8;

/*Table structure for table `img_up_admin_map` */

DROP TABLE IF EXISTS `img_up_admin_map`;

CREATE TABLE `img_up_admin_map` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '管理员上传关联id',
  `up_id` int(10) unsigned NOT NULL COMMENT '文件id',
  `admin_id` int(10) unsigned NOT NULL COMMENT '管理员id',
  `status` tinyint(4) NOT NULL DEFAULT '-1' COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `fk_iuamap_admin_id` (`admin_id`),
  KEY `fk_iuamap_up_id` (`up_id`),
  CONSTRAINT `fk_iuamap_admin_id` FOREIGN KEY (`admin_id`) REFERENCES `admin_info` (`id`),
  CONSTRAINT `fk_iuamap_up_id` FOREIGN KEY (`up_id`) REFERENCES `up_info` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `img_up_business_map` */

DROP TABLE IF EXISTS `img_up_business_map`;

CREATE TABLE `img_up_business_map` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '商家上传关联id',
  `up_id` int(10) unsigned NOT NULL COMMENT '文件id',
  `business_id` int(10) unsigned NOT NULL COMMENT '商家id',
  `status` tinyint(4) NOT NULL COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `fk_iubmap_up_id` (`up_id`),
  KEY `fk_iubmap_busi_id` (`business_id`),
  CONSTRAINT `fk_iubmap_busi_id` FOREIGN KEY (`business_id`) REFERENCES `business_info` (`id`),
  CONSTRAINT `fk_iubmap_up_id` FOREIGN KEY (`up_id`) REFERENCES `up_info` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `img_up_user_map` */

DROP TABLE IF EXISTS `img_up_user_map`;

CREATE TABLE `img_up_user_map` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '图像上传者关联id',
  `img_id` int(10) unsigned NOT NULL COMMENT '图像id',
  `u_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `status` tinyint(1) NOT NULL DEFAULT '-1' COMMENT '状态：-1删除，0可用',
  PRIMARY KEY (`id`),
  KEY `fk_imgupu_img_id` (`img_id`),
  KEY `fk_imgupu_u_id` (`u_id`),
  CONSTRAINT `fk_imgupu_img_id` FOREIGN KEY (`img_id`) REFERENCES `img_info` (`id`),
  CONSTRAINT `fk_imgupu_u_id` FOREIGN KEY (`u_id`) REFERENCES `user_info` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=467 DEFAULT CHARSET=utf8;

/*Table structure for table `index_page_act_list` */

DROP TABLE IF EXISTS `index_page_act_list`;

CREATE TABLE `index_page_act_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '首页显示活动关联id',
  `filter` varchar(12) NOT NULL COMMENT '搜索关键字（状态_标签id,标签id[升序]）',
  `act_list` text COMMENT '活动id数组序列化',
  `update_time` datetime NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;

/*Table structure for table `msg_info` */

DROP TABLE IF EXISTS `msg_info`;

CREATE TABLE `msg_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '消息id',
  `type_id` int(10) unsigned NOT NULL COMMENT '类型id',
  `content` varchar(240) DEFAULT NULL COMMENT '内容',
  `filter` varchar(120) DEFAULT NULL COMMENT '跳转',
  `status` tinyint(4) NOT NULL DEFAULT '-1' COMMENT '状态:-1删除，0正常',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `publish_time` datetime NOT NULL COMMENT '发布时间',
  PRIMARY KEY (`id`),
  KEY `fk_msg_type_id` (`type_id`),
  CONSTRAINT `fk_msg_type_id` FOREIGN KEY (`type_id`) REFERENCES `msg_type` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Table structure for table `msg_rev_user_map` */

DROP TABLE IF EXISTS `msg_rev_user_map`;

CREATE TABLE `msg_rev_user_map` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '消息接收者关联id',
  `u_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `msg_id` int(10) unsigned NOT NULL COMMENT '消息id',
  `status` tinyint(4) NOT NULL DEFAULT '-1' COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `fk_msgru_u_id` (`u_id`),
  KEY `fk_msgru_msg_id` (`msg_id`),
  CONSTRAINT `fk_msgru_msg_id` FOREIGN KEY (`msg_id`) REFERENCES `msg_info` (`id`),
  CONSTRAINT `fk_msgru_u_id` FOREIGN KEY (`u_id`) REFERENCES `user_info` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

/*Table structure for table `msg_type` */

DROP TABLE IF EXISTS `msg_type`;

CREATE TABLE `msg_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '消息类型id',
  `name` varchar(32) NOT NULL COMMENT '名称',
  `status` tinyint(4) NOT NULL DEFAULT '-1' COMMENT '状态:-1删除，0正常',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Table structure for table `push_msg_info` */

DROP TABLE IF EXISTS `push_msg_info`;

CREATE TABLE `push_msg_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'push消息id',
  `send_type` int(10) unsigned NOT NULL COMMENT '发送类型',
  `recv` varchar(1024) DEFAULT NULL COMMENT '接收者',
  `type` int(10) unsigned DEFAULT NULL COMMENT '类型',
  `title` varchar(32) DEFAULT NULL COMMENT '标题',
  `text` varchar(64) DEFAULT NULL COMMENT '文字',
  `url` varchar(240) DEFAULT NULL COMMENT '链接',
  `filter` varchar(64) DEFAULT NULL COMMENT '属性',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `publish_time` datetime NOT NULL COMMENT '发布时间',
  `status` tinyint(4) NOT NULL COMMENT '状态：-1删除，0未发送，1成功',
  `fail_num` int(11) NOT NULL DEFAULT '0' COMMENT '失败次数',
  `last_fail_time` datetime DEFAULT NULL COMMENT '最后一次失败时间',
  PRIMARY KEY (`id`),
  KEY `fk_pushmsg_type_id` (`type`),
  CONSTRAINT `fk_pushmsg_type_id` FOREIGN KEY (`type`) REFERENCES `push_msg_type` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `push_msg_type` */

DROP TABLE IF EXISTS `push_msg_type`;

CREATE TABLE `push_msg_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'push消息类型id',
  `name` varchar(32) NOT NULL COMMENT '名称',
  `status` tinyint(4) NOT NULL COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

/*Table structure for table `recommend_act` */

DROP TABLE IF EXISTS `recommend_act`;

CREATE TABLE `recommend_act` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '活动推荐id',
  `u_id` int(10) unsigned DEFAULT NULL COMMENT '用户id',
  `img_id` int(10) unsigned NOT NULL COMMENT '图像id',
  `act_name` varchar(64) DEFAULT NULL COMMENT '名称',
  `act_time` varchar(64) DEFAULT NULL COMMENT '时间',
  `act_address` varchar(64) DEFAULT NULL COMMENT '地址',
  `act_contact` varchar(64) DEFAULT NULL COMMENT '联系方式',
  `remark` varchar(240) DEFAULT NULL COMMENT '备注',
  `lon` double unsigned DEFAULT NULL COMMENT '经度',
  `lat` double unsigned DEFAULT NULL COMMENT '纬度',
  `address` varchar(64) DEFAULT NULL COMMENT '上传地址',
  `status` tinyint(4) NOT NULL DEFAULT '-1' COMMENT '状态：-1删除，0正常',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `fk_recommend_u_id` (`u_id`),
  KEY `fk_recommend_img_id` (`img_id`),
  CONSTRAINT `fk_recommend_img_id` FOREIGN KEY (`img_id`) REFERENCES `img_info` (`id`),
  CONSTRAINT `fk_recommend_u_id` FOREIGN KEY (`u_id`) REFERENCES `user_info` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

/*Table structure for table `tag_info` */

DROP TABLE IF EXISTS `tag_info`;

CREATE TABLE `tag_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '标签id',
  `name` varchar(16) NOT NULL COMMENT '名称',
  `status` int(11) NOT NULL DEFAULT '-1' COMMENT '状态：-1删除，0正常',
  `count` int(10) unsigned NOT NULL COMMENT '所属活动数量（不包括已结束）',
  `update_time` datetime NOT NULL COMMENT '最后更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*Table structure for table `tag_user_map` */

DROP TABLE IF EXISTS `tag_user_map`;

CREATE TABLE `tag_user_map` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户感兴趣的标签关联id',
  `tag_id` int(10) unsigned NOT NULL COMMENT '标签id',
  `u_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `status` tinyint(1) NOT NULL DEFAULT '-1' COMMENT '状态：-1删除，0正常',
  PRIMARY KEY (`id`),
  KEY `fk_tagu_tag_id` (`tag_id`),
  KEY `fk_tagu_u_id` (`u_id`),
  CONSTRAINT `fk_tagu_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `tag_info` (`id`),
  CONSTRAINT `fk_tagu_u_id` FOREIGN KEY (`u_id`) REFERENCES `user_info` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

/*Table structure for table `time_log` */

DROP TABLE IF EXISTS `time_log`;

CREATE TABLE `time_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `create_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Table structure for table `up_info` */

DROP TABLE IF EXISTS `up_info`;

CREATE TABLE `up_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '上传文件id',
  `ori_name` varchar(32) DEFAULT NULL COMMENT '原始名称',
  `url` varchar(32) DEFAULT NULL COMMENT '文件url',
  `status` tinyint(4) NOT NULL DEFAULT '-1' COMMENT '状态',
  `creat_time` datetime NOT NULL COMMENT '创建时间',
  `descri` varchar(120) DEFAULT NULL COMMENT '描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Table structure for table `user_head_img_map` */

DROP TABLE IF EXISTS `user_head_img_map`;

CREATE TABLE `user_head_img_map` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户头像关联id',
  `u_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `img_id` int(10) unsigned NOT NULL COMMENT '图像id',
  `status` tinyint(1) NOT NULL DEFAULT '-1' COMMENT '状态：-1删除，0可用，1当前使用',
  PRIMARY KEY (`id`),
  KEY `fk_uhi_u_id` (`u_id`),
  KEY `fk_uhi_img_id` (`img_id`),
  CONSTRAINT `fk_uhi_img_id` FOREIGN KEY (`img_id`) REFERENCES `img_info` (`id`),
  CONSTRAINT `fk_uhi_u_id` FOREIGN KEY (`u_id`) REFERENCES `user_info` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8;

/*Table structure for table `user_info` */

DROP TABLE IF EXISTS `user_info`;

CREATE TABLE `user_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id[自增]',
  `user_name` varchar(32) DEFAULT NULL COMMENT '用户名',
  `salt` char(6) DEFAULT NULL COMMENT '哈希标识',
  `is_regist` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已完成注册',
  `user_pass` varchar(32) DEFAULT NULL COMMENT '用户密码',
  `pho_num` varchar(32) DEFAULT NULL COMMENT '手机号用户名',
  `pho_idfy_code` varchar(32) DEFAULT NULL COMMENT '手机验证码',
  `pho_idfy_c_time` datetime DEFAULT NULL COMMENT '手机验证码创建时间',
  `pho_idfy_valid_time` int(10) unsigned NOT NULL DEFAULT '3600' COMMENT '手机验证码有效期（秒）',
  `sina_openid` varchar(32) DEFAULT NULL COMMENT '新浪微博openid',
  `sina_token` varchar(32) DEFAULT NULL COMMENT '新浪微博token',
  `sina_expires_in` bigint(10) unsigned DEFAULT NULL COMMENT '新浪过期时间',
  `qq_openid` varchar(32) DEFAULT NULL COMMENT '腾讯QQopenid',
  `qq_token` varchar(32) DEFAULT NULL COMMENT '腾讯QQtoken',
  `qq_expires_in` bigint(10) unsigned DEFAULT NULL COMMENT '腾讯过期时间',
  `status` tinyint(4) NOT NULL DEFAULT '-1' COMMENT '状态：-1删除，0正常',
  `create_time` datetime NOT NULL COMMENT '用户注册时间',
  `last_login_time` datetime NOT NULL COMMENT '用户最后登录时间',
  `nick_name` varchar(32) DEFAULT NULL COMMENT '昵称',
  `sex` tinyint(1) unsigned DEFAULT NULL COMMENT '用户性别：1男，2女',
  `birth` datetime DEFAULT NULL COMMENT '用户生日',
  `address` varchar(64) DEFAULT NULL COMMENT '地址',
  `email` varchar(32) DEFAULT NULL COMMENT '邮箱',
  `real_name` varchar(32) DEFAULT NULL COMMENT '真实姓名',
  `contact_qq` varchar(32) DEFAULT NULL COMMENT '联系qq',
  `contact_phone` varchar(32) DEFAULT NULL COMMENT '联系电话',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
