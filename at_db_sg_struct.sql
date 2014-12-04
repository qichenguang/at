
DROP TABLE IF EXISTS `at_alert`;
CREATE TABLE `at_alert` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pro_id` char(20) NOT NULL COMMENT '��ĿID',
  `pro_name` char(50) NOT NULL COMMENT '��Ŀ����',
  `depsx` char(20) DEFAULT NULL COMMENT '�����б�',
  `alert_msg` char(50) DEFAULT NULL COMMENT '������Ϣ',
  `lx` tinyint(4) DEFAULT NULL COMMENT '������Ϣ����',
  `msg_md5` char(50) DEFAULT NULL COMMENT '������ϢΨһ��',
	`alert_time` datetime DEFAULT NULL COMMENT '���ɱ�������',
	`alert_day_num` tinyint(4) DEFAULT NULL COMMENT '������������',
	`alert_last_day` datetime DEFAULT NULL COMMENT '������ֹ����',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='������';

CREATE TABLE `at_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` char(30) NOT NULL COMMENT '�û�����',
  `email` char(30) NOT NULL COMMENT '�����ʼ�,��Ϊ�û���¼��',
  `password` char(100) NOT NULL COMMENT '��¼����',
  `department` char(2) DEFAULT NULL COMMENT '����',
  `userflag` tinyint(4) NOT NULL DEFAULT '1' COMMENT '�û���ʶ 1:��ͨ�û� 2.�û�����Ա',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '�û���ʶ 1���½���ע���û� 2:�����Ѿ���˹����û� 3.�Ѿ�ɾ���û�',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='�û���';

DROP TABLE IF EXISTS `at_pro2user`;
CREATE TABLE `at_pro2user` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pro_id` char(8) NOT NULL COMMENT '��ĿID',
  `user_id` INT(11) UNSIGNED NOT NULL COMMENT '�û�ID',
  `department` CHAR(2) COMMENT '����',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='��Ŀ2�û���';

DROP TABLE IF EXISTS `at_account`;
CREATE TABLE `at_account` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `account_name` char(20) NOT NULL COMMENT '�˺�����',
  `qs_name` char(20) NOT NULL COMMENT 'ȯ������',
  `account_id` char(20) NOT NULL COMMENT '�˺�ID',
  `account_pwd` char(20) DEFAULT NULL COMMENT '�˺�����',
  `account_csje` double DEFAULT NULL COMMENT '�˺��ʽ��ʼ���',
  `sm` char(100) DEFAULT NULL COMMENT '˵��',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '��ʶ 1���½� 2:�����Ѿ���� 3.�Ѿ�ɾ��',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='�˺ű�';
DROP TABLE IF EXISTS `at_policy`;
CREATE TABLE `at_policy` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `policy_name` char(30) NOT NULL COMMENT '��������',
  `buy` char(100) NOT NULL COMMENT '���ԣ���',
  `sell` char(100) NOT NULL COMMENT '���ԣ���',
  `money` char(100) DEFAULT NULL COMMENT '���ԣ��ʽ�',
  `sm` char(100) DEFAULT NULL COMMENT '˵��',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '��ʶ 1���½� 2:�����Ѿ���� 3.�Ѿ�ɾ��',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='���Ա�';
###########################################################################################################################################
DROP TABLE IF EXISTS `at_project`;
CREATE TABLE `at_project` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pro_id` char(30) NOT NULL COMMENT '��ĿID',
  `pro_name` char(50) DEFAULT NULL COMMENT '��Ŀ����',
  `pro_qx` longtext COMMENT '��ĿȨ��',
  `pro_zjze` double DEFAULT NULL COMMENT '��Ŀ�ʽ��ܶ�',
  `zj_lock` tinyint(4) NOT NULL DEFAULT '0' COMMENT '��Ŀ�ʽ�������ʶ 1: δ����,2��������',
  `pro_ks_time` datetime DEFAULT NULL COMMENT '��Ŀ��ʼ����',
  `pro_js_time` datetime DEFAULT NULL COMMENT '��Ŀ��������',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '��ʶ 1���½� 2:�����Ѿ���˹� 3.�Ѿ�ɾ��',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='��Ŀ�ܱ�';  
#######################################################################################################################################
DROP TABLE IF EXISTS `at_project_account`;
CREATE TABLE `at_project_account` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pro_id` char(30) NOT NULL COMMENT '��ĿID',
  `account_id` char(20) NOT NULL COMMENT '�˺�ID',
  `pro_account_csje` double DEFAULT NULL COMMENT '��Ŀ�˺��ʽ��ʼ���',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '��ʶ 1���½� 2:�����Ѿ���˹� 3.�Ѿ�ɾ��',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='��Ŀ�˺ű�';  
#######################################################################################################################################
DROP TABLE IF EXISTS `at_project_jobs`;
CREATE TABLE `at_project_jobs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pro_id` char(30) NOT NULL COMMENT '��ĿID',
  `account_id_csje` char(250) DEFAULT NULL COMMENT '�˺�ID�ͳ�ʼ����б�:JSON�ṹ',
  `stock_code`  char(30) NOT NULL COMMENT '��Ʊ����',
  `exchange_type`  char(2) NOT NULL COMMENT '�г����� :1��ʾ�Ϻ�A�ɣ�2��ʾ����A��',
  `buyorsell` char(1)  DEFAULT NULL COMMENT '����: 1�� 2��',
  `percent` tinyint(4) DEFAULT NULL  COMMENT '����/����: ����:�ٷֱ�:1-100',
  `price` double DEFAULT NULL  COMMENT '����/����: �۸�',
  `yj_buy_je` double DEFAULT NULL  COMMENT 'Ԥ��������',
  `sj_buy_je` double DEFAULT NULL  COMMENT 'ʵ��������',
  `sj_buy_num` double DEFAULT NULL  COMMENT 'ʵ����������',
  `cur_can_sell_num` double DEFAULT NULL  COMMENT '��ǰ��������������(��ǰ�������)',
  `yj_sell_num` double DEFAULT NULL  COMMENT 'Ԥ����������',
  `sj_sell_je` double DEFAULT NULL  COMMENT 'ʵ���������',
  `sj_sell_num` double DEFAULT NULL  COMMENT 'ʵ����������',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '��ʶ 1���½� 2:���� 3.��ͣ 4.�������� 5.ֹͣ 6.ɾ��',
  `job_ks_time` datetime DEFAULT NULL COMMENT 'job��ʼ����, ����ʱ����',
  `job_js_time` datetime DEFAULT NULL COMMENT 'job��������, ֹͣʱ����',
  `sm` char(100) DEFAULT NULL COMMENT '˵��',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='��Ŀjob��';  
#######################################################################################################################################
DROP TABLE IF EXISTS `at_project_jobs_detail`;
CREATE TABLE `at_project_jobs_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pro_id` char(30) NOT NULL COMMENT '��ĿID',
  `pro_job_id` int(11) unsigned DEFAULT NULL COMMENT '��Ŀjob�����ID',
  `account_id` char(20) NOT NULL COMMENT '�˺�ID',
  `stock_code`  char(30) NOT NULL COMMENT '��Ʊ����',
  `exchange_type`  char(2) NOT NULL COMMENT '�г����� :1��ʾ�Ϻ�A�ɣ�2��ʾ����A��',
  `buyorsell` char(1)  DEFAULT NULL COMMENT '����: 1�� 2��',
  `num` tinyint(4) DEFAULT NULL  COMMENT '����',
  `price` double DEFAULT NULL  COMMENT '�۸�',
  `entrust_no` char(20) DEFAULT NULL COMMENT '���صı�ʶ',
  `status` tinyint(4) DEFAULT NULL COMMENT '��ʶ 1���ɹ� 2:ʧ��',
  `opt_ks_time` datetime DEFAULT NULL COMMENT '������ʼ����',
  `opt_js_time` datetime DEFAULT NULL COMMENT '������������: ��/���� ��ǿ��ֹͣʱ����',
  `sm` char(100) DEFAULT NULL COMMENT '˵��',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='��Ŀjob��ϸ��';  
#######################################################################################################################################


















