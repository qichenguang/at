<?php

function CMP_MONTH($a, $b){
    if ($a == $b) {
        return 0;
    }
    return ($a < $b) ? -1 : 1;
}

function USER_FUN_GET_STOCK_MARKET_NAME(){
    $in_type = array(
        '0' => "全部",
        '1' => "上海A股",
        '2' => "深圳A股",
    );
    return $in_type;
}
function USER_FUN_GET_PROJECT_JOB_STATUS_NAME(){
    $st = array(
        '1' => "新增",
        '2' => "启动",
        '3' => "暂停",
        '4' => "重新启动",
        '5' => "停止",
        '6' => "删除",
    );
    return $st;
}
function USER_FUN_GET_TRADE_NAME(){
    $in_type = array(
        'A' => "IT",
        'B' => "金融",
        'C' => "医药",
        'D' => "食品",
        'E' => "工业",
        'F' => "物流",
        'G' => "汽车",
        'H' => "能源",
        'I' => "零售",
        'J' => "日用品",
        'K' => "咨询",
        'L' => "其它",
    );
    return $in_type;
}


function USER_FUN_GET_TB_TYPE_NAME(){
    $in_type = array(
        'A' => "框架协议",
        'B' => "意向协商",
        'C' => "投标"
    );
    return $in_type;
}

function USER_FUN_GET_GYS_FB_HT_LX_NAME(){
    $arr = array(
        'A' => "投标",
        'B' => "询价",
        'C' => "直接指定",
        'D' => "客户指定",
        'E' => "长期协议");
    return $arr;
}
function USER_FUN_GET_ZJL_SKSJ_BS_NAME(){
    $arr = array(
        '0.1'=>'首付款',
        '2'=>'第2笔',
        '3'=>'第3笔',
        '4'=>'第4笔',
        '5'=>'第5笔',
        '6'=>'第6笔',
        '7'=>'第7笔',
        '8'=>'第8笔',
        '9'=>'竣工款',
        '10'=>'第10笔',
        '11'=>'第11笔',
        '12'=>'第12笔',
        '40'=>'质保金',
    );
    return $arr;
}
function USER_FUN_GET_ZJL_SKYJ_BS_NAME(){
    $arr = array(
        '0.1'=>'首付款',
        '1'=>'进度款1',
        '2'=>'进度款2',
        '3'=>'进度款3',
        '4'=>'进度款4',
        '5'=>'进度款5',
        '6'=>'进度款6',
        '7'=>'进度款7',
        '8'=>'进度款8',
        '20'=>'竣工款',
        '30'=>'结算款',
        '40'=>'质保金',
    );
    return $arr;
}
function USER_FUN_GET_ZJL_FKYJ_BS_NAME(){
    $arr = array(
        '0.1'=>'首付款',
        '1'=>'进度款1：开工第2月',
        '2'=>'进度款2：开工第3月',
        '3'=>'进度款3：开工第4月',
        '4'=>'进度款4：开工第5月',
        '5'=>'进度款5：开工第6月',
        '6'=>'进度款6：开工第7月',
        '7'=>'进度款7：开工第8月',
        '8'=>'进度款8：开工第9月',
        '20'=>'竣工结算款',
        '30'=>'竣工结算款次月',
        '40'=>'质保金',
         '0'=>'全部'
    );
    return $arr;
}
function USER_FUN_GET_MYD_PJ_NAME(){
    $myd_pj = array(
        "A" => '非常满意',
        "B" => '满意',
        "C" => '基本满意',
        "D" => '不太满意',
        "E" => '不满意',
    );
    return $myd_pj;
}

function USER_FUN_GET_GCZC_IN_TYPE_NAME(){
    $in_type = array(
        'A' => "管理",
        'B' => "设计",
        'C' => "施工",
    );
    return $in_type;
}

function USER_FUN_GET_GCZC_IN_SUB_TYPE_NAME(){
    $in_sub_type = array(
        //大厦；管理公司；客户；顾问；消防局；建委；客户设施维护FM;
        //天花；地面；墙；门；五金；饰面；系统家具；固定家具；EL,HVAC,FS,PD,IT,SEC,AV，管理,平面,效果图
        'A' => "大厦",
        'B' => "管理公司",
        'C' => "客户",
        'D' => "顾问",
        'E' => "消防局",
        'F' => "建委",
        'G' => "客户设施维护FM",
        'H' => "天花",
        'I' => "地面",
        'J' => "墙",
        'K' => "门",
        'L' => "五金",
        'M' => "饰面",
        'N' => "系统家具",
        'O' => "固定家具",
        'P' => "EL",
        'Q' => "HVAC",
        'R' => "FS",
        'S' => "PD",
        'T' => "IT",
        'U' => "SEC",
        'V' => "AV",
        'W' => "管理",
        'X' => "平面",
        'Y' => "效果图",
    );
    return $in_sub_type;
}


function USER_FUN_GET_USER_STATUS_NAME(){
    $st = array(
        '1' => "新增(待审核)",
        '2' => "正常(已审核)",
        '3' => "注销(已删除)",
    );
    return $st;
}



function USER_FUN_GET_PROJECT_STATUS_NAME(){
    $st = array(
        '1' => "新增(待审核)",
        '2' => "正常(已审核)",
        '3' => "注销(已删除)",
    );
    return $st;
}

function USER_FUN_GET_LOG_TYPE_NAME(){
    $st = array(
        '101' => "H_I_S",
    );
    return $st;
}

function USER_FUN_GET_QS_NAME(){
    $vo = array(
        'zx'=> "中信证券",
    );
    return $vo;
}
function USER_FUN_GET_FBS_LX_VALUE(){
    $vo = array(
        'zx'=> 1,
        'dq' => 2,
        'kt' => 3,
        'xf' => 4,
        'jps' => 5,
        'it' => 6,
        'sec' => 7,
        'av' => 8,
    );
    return $vo;
}
function USER_FUN_GET_QQ_TYPE_NAME(){
    $vo = array(
        'A' => "物业管理费用",
        'B' => "办公用品1",
        'C' => "办公用品2",
        'D' => "临时水电设施费",
        'E' => "临时水电费用",
        'F' => "施工手续费",
        'G' => "大楼审图等管理费",
        'H' => "设备租赁费",
        'I' => "空气检测费",
        'J' => "现场保护及成品保护",
        'K' => "招待费",
        'L' => "劳防用品",
        'M' => "生活交通安置费",
        'N' => "工程保险费",
        'O' => "施工清洁费1(外包)",
        'P' => "施工清洁费2(内部)",
        'Q' => "税金",
        'R' => "报审图打图费用",
        'S' => "临时办公室费用",
        'T' => "材料报检费用",
        'U' => "垃圾清运费",
        'V' => "施工图和竣工图",
        'W' => "其它费用",
    );
    return $vo;
}

function USER_FUN_GET_HT_TYPE_NAME(){
    $arr = array(
        'A'=> "投标",
        'B' => "询价",
        'C' => "直接指定",
        'D' => "客户指定",
    );
    return $arr;
}
function USER_FUN_GET_CUSTOMER_VO_RESON_NAME(){
    $vo_reson = array(
        'A' => "客户变更",
        'B' => "范围变更",
        'C' => "代客户采购",
        'D' => "设计公司变更",
        'E' => "大厦或消防要求",
        'F' => "现场条件不符",
        'G' => "其它",
    );
    return $vo_reson;
}
function USER_FUN_GET_FB_VO_RESON_NAME(){
    /*
1. 客户要求-有VO；
2. 客户要求- 无VO;
3. 分包（或供应商）报价范围变更（此项对应与客户合同有应收）：
4. 装修设计疏漏：请详细说明情况
5. 机电设计疏漏：请详细说明情况
6. 预算漏项或少算：请详细说明情况
7. 现场控制疏漏：请详细说明情况
8. 采购疏漏：请详细说明情况
9. 其它（必须有详细情况说明）：
    */
    $vo_reson = array(
        'A' => "客户要求-有VO",
        'B' => "客户要求-无VO",
        'C' => "分包(或供应商)报价范围变更",
        'D' => "装修设计疏漏",
        'E' => "机电设计疏漏",
        'F' => "预算漏项或少算",
        'G' => "现场控制疏漏",
        'H' => "采购疏漏",
        'I' => "其它",
    );
    return $vo_reson;
}
function USER_FUN_GET_DEPARTMENT_NAME(){
    $dep = array(
        'kf' => "开发管理部",
        'cl' => "策略管理部",
        'zj' => "资金管理部",
        'gl' => "管理部",
    );
    return $dep;
}
function USER_FUN_GET_DEPARTMENT_ARRAY(){
    $dep = array(
        0 => array('id' =>'kf', 'name'=> "开发管理部"),
        1 => array('id' =>'cl', 'name'=> "策略管理部"),
        2 => array('id' =>'zj', 'name'=> "资金管理部"),
        3 => array('id' =>'gl', 'name'=> "管理部"),
    );
    return $dep;
}

function USER_FUN_GET_DEPARTMENT_SX(){
    $dep = array(
        0 => 'kf',
        1 => 'cl',
        2 => 'zj',
        3 => 'gl',
    );
    return $dep;
}


function USER_FUN_GET_PROJECT_MODULE_ARRAY(){
    $mod = array(
        'module_xmjbxx'=> "项目基本信息",
        'module_xmscfx'=> "项目市场分析",
        'module_khbyd'=> "客户满意度",
        'module_xmrlzygl'=> "项目人力资源管理",
        'module_xmsjgl'=> "项目时间管理",
        'module_xmcbgl'=> "项目成本管理",
        'module_xmzjlgl'=> "项目资金流管理",
        'module_gysgl'=> "供应商管理",
        'module_ygjxgl'=> "员工绩效管理",
        'module_gczcgl'=> "过程资产管理",
        'module_jobbuy'=> "项目买入管理",
        'module_zjcgl'=> "资金池管理",
        'module_curInventory'=> "项目当前库存管理",
        'module_jobsell'=> "项目卖出管理",
    );
    return $mod;
}

function USER_FUN_GET_PROJECT_MODULE_NAME(){
    $mod = array(
        'module_xmjbxx',
        'module_xmscfx',
        'module_khbyd',
        'module_xmrlzygl',
        'module_xmsjgl',
        'module_xmcbgl',
        'module_xmzjlgl',
        'module_gysgl',
        'module_ygjxgl',
        'module_jobbuy',
        'module_zjcgl',
    );
    return $mod;
}

function USER_FUN_GET_DEPATMENT_MODULE_ARRAY($zb_flag){
    $dep = array();
    //sc
    $mod = USER_FUN_GET_PROJECT_MODULE_ARRAY();
    unset($mod['module_xmcbgl']);
    unset($mod['module_xmzjlgl']);
    unset($mod['module_gysgl']);
    unset($mod['module_dxmgl']);
    $dep['sc']=$mod;
    //ys
    $mod = USER_FUN_GET_PROJECT_MODULE_ARRAY();
    unset($mod['module_xmscfx']);
    unset($mod['module_ygjxgl']);
    unset($mod['module_dxmgl']);
    $dep['ys']=$mod;
    //sj
    $mod = USER_FUN_GET_PROJECT_MODULE_ARRAY();
    unset($mod['module_xmscfx']);
    unset($mod['module_xmcbgl']);
    unset($mod['module_xmzjlgl']);
    unset($mod['module_ygjxgl']);
    unset($mod['module_dxmgl']);
    $dep['sj']=$mod;
    //jd
    $mod = USER_FUN_GET_PROJECT_MODULE_ARRAY();
    unset($mod['module_xmscfx']);
    unset($mod['module_xmcbgl']);
    unset($mod['module_xmzjlgl']);
    unset($mod['module_ygjxgl']);
    unset($mod['module_dxmgl']);
    $dep['jd']=$mod;
    //gc
    $mod = USER_FUN_GET_PROJECT_MODULE_ARRAY();
    unset($mod['module_xmscfx']);
    unset($mod['module_ygjxgl']);
    unset($mod['module_dxmgl']);
    $dep['gc']=$mod;
    //cg
    $mod = USER_FUN_GET_PROJECT_MODULE_ARRAY();
    unset($mod['module_xmscfx']);
    unset($mod['module_ygjxgl']);
    unset($mod['module_dxmgl']);
    $dep['cg']=$mod;
    //sh
    $mod = USER_FUN_GET_PROJECT_MODULE_ARRAY();
    unset($mod['module_xmscfx']);
    unset($mod['module_xmcbgl']);
    unset($mod['module_xmzjlgl']);
    unset($mod['module_ygjxgl']);
    unset($mod['module_dxmgl']);
    $dep['sh']=$mod;
    //hr
    $mod = USER_FUN_GET_PROJECT_MODULE_ARRAY();
    unset($mod['module_xmscfx']);
    unset($mod['module_xmsjgl']);
    unset($mod['module_xmzjlgl']);
    unset($mod['module_gysgl']);
    unset($mod['module_gczcgl']);
    unset($mod['module_dxmgl']);
    $dep['hr']=$mod;
    //ht
    $mod = USER_FUN_GET_PROJECT_MODULE_ARRAY();
    unset($mod['module_xmscfx']);
    unset($mod['module_gysgl']);
    unset($mod['module_ygjxgl']);
    unset($mod['module_gczcgl']);
    $dep['ht']=$mod;
    //gl
    $mod = USER_FUN_GET_PROJECT_MODULE_ARRAY();
    $dep['gl']=$mod;
    return $dep;
}