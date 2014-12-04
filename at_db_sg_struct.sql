
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

DROP TABLE IF EXISTS `at_pro2user`;
CREATE TABLE `at_pro2user` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pro_id` char(8) NOT NULL COMMENT '项目ID',
  `user_id` INT(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `department` CHAR(2) COMMENT '部门',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='项目2用户表';

DROP TABLE IF EXISTS `at_account`;
CREATE TABLE `at_account` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `account_name` char(20) NOT NULL COMMENT '账号名称',
  `qs_name` char(20) NOT NULL COMMENT '券商名称',
  `account_id` char(20) NOT NULL COMMENT '账号ID',
  `account_pwd` char(20) DEFAULT NULL COMMENT '账号密码',
  `account_csje` double DEFAULT NULL COMMENT '账号资金初始金额',
  `sm` char(100) DEFAULT NULL COMMENT '说明',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '标识 1：新建 2:正常已经审核 3.已经删除',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='账号表';
DROP TABLE IF EXISTS `at_policy`;
CREATE TABLE `at_policy` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `policy_name` char(30) NOT NULL COMMENT '策略名称',
  `buy` char(100) NOT NULL COMMENT '策略：买',
  `sell` char(100) NOT NULL COMMENT '策略：卖',
  `money` char(100) DEFAULT NULL COMMENT '策略：资金',
  `sm` char(100) DEFAULT NULL COMMENT '说明',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '标识 1：新建 2:正常已经审核 3.已经删除',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='策略表';
###########################################################################################################################################
DROP TABLE IF EXISTS `at_project`;
CREATE TABLE `at_project` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pro_id` char(30) NOT NULL COMMENT '项目ID',
  `pro_name` char(50) DEFAULT NULL COMMENT '项目名称',
  `pro_qx` longtext COMMENT '项目权限',
  `pro_zjze` double DEFAULT NULL COMMENT '项目资金总额',
  `zj_lock` tinyint(4) NOT NULL DEFAULT '0' COMMENT '项目资金锁定标识 1: 未锁定,2：已锁定',
  `pro_ks_time` datetime DEFAULT NULL COMMENT '项目开始日期',
  `pro_js_time` datetime DEFAULT NULL COMMENT '项目结束日期',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '标识 1：新建 2:正常已经审核过 3.已经删除',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='项目总表';  
#######################################################################################################################################
DROP TABLE IF EXISTS `at_project_account`;
CREATE TABLE `at_project_account` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pro_id` char(30) NOT NULL COMMENT '项目ID',
  `account_id` char(20) NOT NULL COMMENT '账号ID',
  `pro_account_csje` double DEFAULT NULL COMMENT '项目账号资金初始金额',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '标识 1：新建 2:正常已经审核过 3.已经删除',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='项目账号表';  
#######################################################################################################################################
DROP TABLE IF EXISTS `at_project_jobs`;
CREATE TABLE `at_project_jobs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pro_id` char(30) NOT NULL COMMENT '项目ID',
  `account_id_csje` char(250) DEFAULT NULL COMMENT '账号ID和初始金额列表:JSON结构',
  `stock_code`  char(30) NOT NULL COMMENT '股票代码',
  `exchange_type`  char(2) NOT NULL COMMENT '市场代码 :1表示上海A股，2表示深圳A股',
  `buyorsell` char(1)  DEFAULT NULL COMMENT '方向: 1买 2卖',
  `percent` tinyint(4) DEFAULT NULL  COMMENT '买入/卖出: 比例:百分比:1-100',
  `price` double DEFAULT NULL  COMMENT '买入/卖出: 价格',
  `yj_buy_je` double DEFAULT NULL  COMMENT '预计买入金额',
  `sj_buy_je` double DEFAULT NULL  COMMENT '实际买入金额',
  `sj_buy_num` double DEFAULT NULL  COMMENT '实际买入数量',
  `cur_can_sell_num` double DEFAULT NULL  COMMENT '当前可以卖出的数量(当前库存数量)',
  `yj_sell_num` double DEFAULT NULL  COMMENT '预计卖出数量',
  `sj_sell_je` double DEFAULT NULL  COMMENT '实际卖出金额',
  `sj_sell_num` double DEFAULT NULL  COMMENT '实际卖出数量',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '标识 1：新建 2:启动 3.暂停 4.重新启动 5.停止 6.删除',
  `job_ks_time` datetime DEFAULT NULL COMMENT 'job开始日期, 启动时设置',
  `job_js_time` datetime DEFAULT NULL COMMENT 'job结束日期, 停止时设置',
  `sm` char(100) DEFAULT NULL COMMENT '说明',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='项目job表';  
#######################################################################################################################################
DROP TABLE IF EXISTS `at_project_jobs_detail`;
CREATE TABLE `at_project_jobs_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pro_id` char(30) NOT NULL COMMENT '项目ID',
  `pro_job_id` int(11) unsigned DEFAULT NULL COMMENT '项目job表关联ID',
  `account_id` char(20) NOT NULL COMMENT '账号ID',
  `stock_code`  char(30) NOT NULL COMMENT '股票代码',
  `exchange_type`  char(2) NOT NULL COMMENT '市场代码 :1表示上海A股，2表示深圳A股',
  `buyorsell` char(1)  DEFAULT NULL COMMENT '方向: 1买 2卖',
  `num` tinyint(4) DEFAULT NULL  COMMENT '数量',
  `price` double DEFAULT NULL  COMMENT '价格',
  `entrust_no` char(20) DEFAULT NULL COMMENT '返回的标识',
  `status` tinyint(4) DEFAULT NULL COMMENT '标识 1：成功 2:失败',
  `opt_ks_time` datetime DEFAULT NULL COMMENT '操作开始日期',
  `opt_js_time` datetime DEFAULT NULL COMMENT '操作结束日期: 买/卖完 或强制停止时设置',
  `sm` char(100) DEFAULT NULL COMMENT '说明',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='项目job明细表';  
#######################################################################################################################################


















