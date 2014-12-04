<?php
namespace Home\Controller;
use Think\Controller;
class AllInOneController extends Controller {
    public function _before_index(){
        $user_id = $_SESSION['user_id'];
        //trace($user_id,"user_id=");
        if("" == $user_id){
            $this->error("没有权限!");
        }
    }
    public function index(){
        //trace($_SESSION["user_id"],"user_id=");
        $this->user_id = $_SESSION["user_id"];
        $this->user_department = $_SESSION["department"];
        $this->userflag = $_SESSION['userflag'];
        //
        $this->display();
    }
    public function allmodules(){
        $this->user_id = $_SESSION["user_id"];
        $this->user_department = $_SESSION["department"];
        $this->userflag = $_SESSION['userflag'];
        //
        $dep_sx = USER_FUN_GET_DEPARTMENT_SX();
        $this->dep_sx = implode($dep_sx,",");
        //dump($this->dep_module);
        //
        $pro_id = I('pro_id');
        $this->pro_id = $pro_id; // 进行模板变量赋值
        $cond['pro_id'] = $pro_id;
        $project_detail = array();
        $Data   =   M("project");
        $project_detail = $Data->where($cond)->find();
        $module_arr = USER_FUN_GET_DEPATMENT_MODULE_ARRAY(true);
        $dep_module_name = $module_arr[$_SESSION["department"]];
        $this->dep_module_name = $dep_module_name;
        $this->dep_module_id = array_keys($dep_module_name);
        $this->dep_module = implode(array_keys($dep_module_name),",");
        //
        //
        $this->project_rec_detail = json_encode($project_detail);
        $this->pro_name = $project_detail["pro_name"];


        //得到所有的用户部门和姓名信息
        $Data = M('user'); // 实例化Data数据模型
        $condition["status"] = 2;//normal
        $list  = $Data->where($condition)->select();
        $responce = array();
        foreach($list as $item){
            $responce[$item['department']][] = array('id' => $item["id"],'user_name' => $item['user_name']);
        }
        $this->all_dep_username = json_encode($responce);
        //--------------------------------------------------------------------------------------------------------------
        //--------------------------------------------------------------------------------------------------------------
        $this->display();
    }

    function ajaxSingleFieldSave(){
        $pro_id = I('pro_id');
        $field_name = I('fieldname');
        $field_value = I('fieldvalue');
        trace($field_value,"field_value");
        if(empty($field_name)){
            $this->ajaxReturn(array('state' => false, 'msg' => "没有字段名称"));
        }
        $Data = M('project'); // 实例化Data数据模型
        $condition['pro_id'] = $pro_id;

        //
        $new_field = array();
        if(is_array($field_value)){
            foreach($field_value as $key => $value){
                //trace($value,"value");
                $new_field[] = '"' . $value . '"';
            }
            //trace($new_field,"new_field");
            $update[$field_name] = "[" . implode(",",$new_field) . "]";
        }else{
            $update[$field_name] = $field_value;
        }
        $result  = $Data->where($condition)->save($update);
        if(false === $result){
            $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置"));
        }elseif(0 == $result){
            $this->ajaxReturn(array('state' => false, 'msg' => "没有修改字段的值"));
        }else{
            $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功"));
        }
    }


    public function ajaxChengeTheme(){
        $theme = I('theme','redmond');
        $_SESSION['theme'] = $theme;
        $this->ajaxReturn(array('state' => true, 'msg' => "更改成功,请按 F5 重新刷新页面"));
    }

    public function ajaxGczcjlSearch(){
        $pagenum = I('page',1); // get the requested page
        $limitnum = I('rows',20); // get how many rows we want to have into the grid
        $sidx = I('sidx','id'); // get index row - i.e. user click to sort
        $sord = I('sord','desc'); // get the direction
        if($sidx == ""){
            $sidx = 'id';
        }

        //手动查询标志
        $searchOn = I('_search');
        //多条件查询
        $cond = array();
        if(TRUE || 'true' == $searchOn) {
            $sarr = I('param.');
            foreach( $sarr as $k=>$v) {
                switch ($k) {
                    case 'jyzj':
                    case 'ydwt':
                    case 'jjfa':
                    case 'ckwj':
                        $cond[$k] = array('LIKE', "%$v%");
                        break;
                    case 'id':
                    case 'pro_id':
                    case 'department':
                    case 'in_type':
                    case 'in_sub_type':
                        if("all" != $v){
                            $cond[$k] = $v;
                        }
                        break;
                }
            }
        }
        //单条件 find
        if(FALSE && 'true' == $searchOn){
            $searchField = I('searchField');
            $searchString = I('searchString');
            $searchOper = I('searchOper');
            $cond[$searchField] = array('LIKE', "%$searchString%");
        }
        //
        $User = M('gczcjl'); // 实例化User对象
        $count = $User->where($cond)->order(array($sidx => $sord))->count();// 查询满足要求的总记录数
        $list =  $User->where($cond)->order(array($sidx => $sord))->page($pagenum,$limitnum)->select();

        $total_pages = 0;
        if( $count >0 ) {
            $total_pages = ceil($count/$limitnum);
        }
        $responce["page"] = $pagenum;
        $responce["total"] = $total_pages;
        $responce["records"] = $count;

        $in_type_name_arr = USER_FUN_GET_GCZC_IN_TYPE_NAME();
        $dep_name_arr = USER_FUN_GET_DEPARTMENT_NAME();
        $in_sub_type_name_arr = USER_FUN_GET_GCZC_IN_SUB_TYPE_NAME();
        $i=0;
        if(!empty($list)){
            foreach($list as $item){
                $responce["rows"][$i]['id']=$item["id"];
                $responce["rows"][$i]['cell'] = array($item['id'],
                    $dep_name_arr[$item['department']],
                    $in_type_name_arr[$item['in_type']],
                    $in_sub_type_name_arr[$item['in_sub_type']],
                    $item['jyzj'],$item['ydwt'],$item['jjfa'],$item['ckwj']
                );
                $i++;
            }
        }
        $this->ajaxReturn($responce);
    }

    public function ajaxGczcjlSave(){
        $Data = M('gczcjl'); // 实例化Data数据模型

        $oper = I('oper');
        $id = I('id');
        $pro_id = I('pro_id');

        $user_id = I('user_id');
        $department = I('department');
        $in_type = I('in_type');
        $in_sub_type = I('in_sub_type');
/*        `jyzj` varchar(1024) NOT NULL COMMENT '经验总结',
        `ydwt` varchar(1024) NOT NULL COMMENT '具体遇到的问题',
        `jjfa` varchar(1024) NOT NULL COMMENT '具体的解决方案',
        `ckwj` varchar(1024) NOT NULL COMMENT '参考查询文件',*/
        $jyzj = I('jyzj');
        $ydwt = I('ydwt');
        $jjfa = I('jjfa');
        $ckwj = I('ckwj');

        trace($pro_id,"pro_id");
        trace($jyzj,"jyzj");
        trace($ydwt,"ydwt");
        trace($jjfa,"jjfa");
        trace($ckwj,"ckwj");
        $condition = array();
        switch ($oper) {
            case "add"://
                if( empty($pro_id) || empty($department) || empty($in_type) || empty($in_sub_type)
                    || "all" == $department || "all" == $in_type || "all" == $in_sub_type){
                    $this->ajaxReturn(array('state' => false, 'msg' => "字段不能为空"));
                }
                $condition["pro_id"] = $pro_id;
                $condition["user_id"] = $user_id;
                $condition["department"] = $department;
                $condition['in_type'] = $in_type;
                $condition['in_sub_type'] = $in_sub_type;
                $condition['jyzj'] = $jyzj;
                $condition['ydwt'] = $ydwt;
                $condition['jjfa'] = $jjfa;
                $condition['ckwj'] = $ckwj;

                $result  = $Data->add($condition);
                if(false === $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置"));
                }else{
                    $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功", 'id' => $result));
                }
                break;
            case "edit"://
                if(empty($id) || empty($pro_id) || empty($in_type) || empty($in_sub_type)){
                    $this->ajaxReturn(array('state' => false, 'msg' => "字段不能为空", 'id' => $id));
                }
                $condition["pro_id"] = $pro_id;
                $condition["user_id"] = $user_id;
                $condition["department"] = $department;
                $condition['in_type'] = $in_type;
                $condition['in_sub_type'] = $in_sub_type;
                $condition['jyzj'] = $jyzj;
                $condition['ydwt'] = $ydwt;
                $condition['jjfa'] = $jjfa;
                $condition['ckwj'] = $ckwj;

                $condition['id'] = $id;
                $result  = $Data->save($condition);
                if(false === $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置", 'id' => $id));
                }elseif(0 == $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "信息没有修改", 'id' => $id));
                }else{
                    $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功", 'id' => $id));
                }
                break;
            case "del":
                if(empty($id)){
                    $this->ajaxReturn(array('state' => false, 'msg' => "字段不能为空", 'id' => $id));
                }
                $condition['id'] = $id;
                $result  = $Data->where($condition)->delete();
                if(false === $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置", 'id' => $id));
                }elseif(0 == $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "信息没有修改", 'id' => $id));
                }else{
                    $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功", 'id' => $id));
                }
                break;
            default:
                break;
        }
    }


    ////////////////////////////////////////
    public function ajaxGysgljdclSearch(){
        $pagenum = I('page',1); // get the requested page
        $limitnum = I('rows',20); // get how many rows we want to have into the grid
        $sidx = I('sidx','id'); // get index row - i.e. user click to sort
        $sord = I('sord','desc'); // get the direction
        if($sidx == ""){
            $sidx = 'id';
        }

        //手动查询标志
        $searchOn = I('_search');
        //多条件查询
        $cond = array();
        if(TRUE || 'true' == $searchOn) {
            $sarr = I('param.');
            foreach( $sarr as $k=>$v) {
                switch ($k) {
                    case 'zxcl_name':
                        $cond[$k] = array('LIKE', "%$v%");
                        break;
                    case 'id':
                    case 'pro_id':
                    case 'jd_pj':
                    case 'gc_pj':
                    case 'cg_pj':
                    case 'sh_pj':
                        if("all" != $v){
                            $cond[$k] = $v;
                        }
                        break;
                }
            }
        }
        //单条件 find
        if(FALSE && 'true' == $searchOn){
            $searchField = I('searchField');
            $searchString = I('searchString');
            $searchOper = I('searchOper');
            $cond[$searchField] = array('LIKE', "%$searchString%");
        }
        //
        $User = M('gys_jdcl'); // 实例化User对象
        $count = $User->where($cond)->order(array($sidx => $sord))->count();// 查询满足要求的总记录数
        $list =  $User->where($cond)->order(array($sidx => $sord))->page($pagenum,$limitnum)->select();

        $total_pages = 0;
        if( $count >0 ) {
            $total_pages = ceil($count/$limitnum);
        }
        $responce["page"] = $pagenum;
        $responce["total"] = $total_pages;
        $responce["records"] = $count;

        $myd_pj_arr = USER_FUN_GET_MYD_PJ_NAME();
        $i=0;
        if(!empty($list)){
            foreach($list as $item){
                $vozb = 0;
                if($item['ys_htje'] > 0 && $item['ys_voje'] >= 0){
                    $vozb = $item['ys_voje'] / $item['ys_htje'];
                }

                $responce["rows"][$i]['id']=$item["id"];
                $responce["rows"][$i]['cell'] = array($item['id'],
                    $item['ys_zxcl_name'],
                    $item['ys_htje'],
                    $item['ys_voje'],
                    $vozb * 100 . "%",
                    $myd_pj_arr[$item['jd_pj']],
                    $myd_pj_arr[$item['gc_pj']],
                    $myd_pj_arr[$item['cg_pj']],
                    $myd_pj_arr[$item['sh_pj']],
                    "0%",
                );
                $i++;
            }
        }
        $this->ajaxReturn($responce);
    }

    public function ajaxGysgljdclSave(){
        $Data = M('gys_jdcl'); // 实例化Data数据模型

        $oper = I('oper');
        $id = I('id');
        $pro_id = I('pro_id');

        $ys_zxcl_name = I('ys_zxcl_name');
        $ys_htje = I('ys_htje');
        $ys_voje = I('ys_voje');
        /*
  `htje` double DEFAULT NULL COMMENT '合同金额',
  `voje` double DEFAULT NULL COMMENT 'VO金额',
  `ys_pj` char(1) NOT NULL COMMENT '平均：预算部',
  `sj_pj` char(1) NOT NULL COMMENT '平均：设计部',
  `jd_pj` char(1) NOT NULL COMMENT '平均：机电部',
  `gc_pj` char(1) NOT NULL COMMENT '平均：工程部',
  `cg_pj` char(1) NOT NULL COMMENT '平均：采购部',
  `sh_pj` char(1) NOT NULL COMMENT '平均：售后部',
        */
        $jd_pj = I('jd_pj');
        $gc_pj = I('gc_pj');
        $cg_pj = I('cg_pj');
        $sh_pj = I('sh_pj');


        $condition = array();
        switch ($oper) {
            case "add"://
                if( empty($pro_id) || empty($ys_zxcl_name) || empty($ys_htje) || empty($ys_voje)){
                    $this->ajaxReturn(array('state' => false, 'msg' => "字段不能为空"));
                }
                $condition["pro_id"] = $pro_id;
                $condition["ys_zxcl_name"] = $ys_zxcl_name;
                $condition['ys_htje'] = $ys_htje;
                $condition['ys_voje'] = $ys_voje;
                $condition['jd_pj'] = $jd_pj;
                $condition['gc_pj'] = $gc_pj;
                $condition['cg_pj'] = $cg_pj;
                $condition['sh_pj'] = $sh_pj;

                $result  = $Data->add($condition);
                if(false === $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置"));
                }else{
                    $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功", 'id' => $result));
                }
                break;
            case "edit"://
                if(empty($id) || empty($pro_id) || empty($ys_zxcl_name) || empty($ys_htje) || empty($ys_voje)){
                    $this->ajaxReturn(array('state' => false, 'msg' => "字段不能为空", 'id' => $id));
                }
                $condition["pro_id"] = $pro_id;
                $condition["ys_zxcl_name"] = $ys_zxcl_name;
                $condition['ys_htje'] = $ys_htje;
                $condition['ys_voje'] = $ys_voje;
                $condition['jd_pj'] = $jd_pj;
                $condition['gc_pj'] = $gc_pj;
                $condition['cg_pj'] = $cg_pj;
                $condition['sh_pj'] = $sh_pj;

                $condition['id'] = $id;
                $result  = $Data->save($condition);
                if(false === $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置", 'id' => $id));
                }elseif(0 == $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "信息没有修改", 'id' => $id));
                }else{
                    $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功", 'id' => $id));
                }
                break;
            case "del":
                if(empty($id)){
                    $this->ajaxReturn(array('state' => false, 'msg' => "字段不能为空", 'id' => $id));
                }
                $condition['id'] = $id;
                $result  = $Data->where($condition)->delete();
                if(false === $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置", 'id' => $id));
                }elseif(0 == $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "信息没有修改", 'id' => $id));
                }else{
                    $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功", 'id' => $id));
                }
                break;
            default:
                break;
        }
    }

    //分包
    public function ajaxGysglfbSearch(){
        $pagenum = I('page',1); // get the requested page
        $limitnum = I('rows',20); // get how many rows we want to have into the grid
        $sidx = I('sidx','id'); // get index row - i.e. user click to sort
        $sord = I('sord','desc'); // get the direction
        if($sidx == ""){
            $sidx = 'id';
        }

        //手动查询标志
        $searchOn = I('_search');
        //多条件查询
        $cond = array();
        if(TRUE ||  'true' == $searchOn) {
            $sarr = I('param.');
            foreach( $sarr as $k=>$v) {
                switch ($k) {
                    case 'zxcl_name':
                        $cond[$k] = array('LIKE', "%$v%");
                        break;
                    case 'id':
                    case 'pro_id':
                    case 'ys_fb_lx':
                    case 'ys_fb_ht_lx':
                    case 'ys_pj':
                    case 'sj_pj':
                    case 'jd_pj':
                    case 'gc_pj':
                    case 'cg_pj':
                    case 'sh_pj':
                        if("all" != $v){
                            $cond[$k] = $v;
                        }
                        break;
                }
            }
        }
        //单条件 find
        if(FALSE && 'true' == $searchOn){
            $searchField = I('searchField');
            $searchString = I('searchString');
            $searchOper = I('searchOper');
            $cond[$searchField] = array('LIKE', "%$searchString%");
        }
        //
        $User = M('gys_fb'); // 实例化User对象
        $count = $User->where($cond)->order(array($sidx => $sord))->count();// 查询满足要求的总记录数
        $list =  $User->where($cond)->order(array($sidx => $sord))->page($pagenum,$limitnum)->select();

        $total_pages = 0;
        if( $count >0 ) {
            $total_pages = ceil($count/$limitnum);
        }
        $responce["page"] = $pagenum;
        $responce["total"] = $total_pages;
        $responce["records"] = $count;

        $myd_pj_arr = USER_FUN_GET_MYD_PJ_NAME();
        $my_fb_type_arr = USER_FUN_GET_FBS_TYPE_NAME();
        $my_ht_type_arr = USER_FUN_GET_HT_TYPE_NAME();
        $i=0;
        if(!empty($list)){
            foreach($list as $item){
                $vozb = 0;
                if($item['ys_fb_ht_je'] > 0 && $item['ys_fb_ht_voje'] >= 0){
                    $vozb = $item['ys_fb_ht_voje'] / $item['ys_fb_ht_je'];
                }

                $responce["rows"][$i]['id']=$item["id"];
                $responce["rows"][$i]['cell'] = array($item['id'],
                    $my_fb_type_arr[$item['ys_fb_lx']],
                    $my_ht_type_arr[$item['ys_fb_ht_lx']],
                    $item['ys_fb_ht_je'],
                    $item['ys_fb_ht_voje'],
                    $vozb * 100 . "%",
                    $myd_pj_arr[$item['ys_pj']],
                    $myd_pj_arr[$item['sj_pj']],
                    $myd_pj_arr[$item['jd_pj']],
                    $myd_pj_arr[$item['gc_pj']],
                    $myd_pj_arr[$item['cg_pj']],
                    $myd_pj_arr[$item['sh_pj']],
                    "0%",
                );
                $i++;
            }
        }
        $this->ajaxReturn($responce);
    }
    public function ajaxGysglfbSave(){
        $Data = M('gys_fb'); // 实例化Data数据模型

        $oper = I('oper');
        $id = I('id');
        $pro_id = I('pro_id');

        $ys_fb_lx = I('ys_fb_lx');
        $ys_fb_ht_lx = I('ys_fb_ht_lx');
        $ys_fb_ht_je = I('ys_fb_ht_je');
        $ys_fb_ht_voje = I('ys_fb_ht_voje');
        /*
  `htje` double DEFAULT NULL COMMENT '合同金额',
  `voje` double DEFAULT NULL COMMENT 'VO金额',
  `ys_pj` char(1) NOT NULL COMMENT '平均：预算部',
  `sj_pj` char(1) NOT NULL COMMENT '平均：设计部',
  `jd_pj` char(1) NOT NULL COMMENT '平均：机电部',
  `gc_pj` char(1) NOT NULL COMMENT '平均：工程部',
  `cg_pj` char(1) NOT NULL COMMENT '平均：采购部',
  `sh_pj` char(1) NOT NULL COMMENT '平均：售后部',
        */

        $ys_pj = I('ys_pj');
        $sj_pj = I('sj_pj');
        $jd_pj = I('jd_pj');
        $gc_pj = I('gc_pj');
        $cg_pj = I('cg_pj');
        $sh_pj = I('sh_pj');


        $condition = array();
        switch ($oper) {
            case "add"://
                if( empty($pro_id) || empty($ys_fb_lx) || empty($ys_fb_ht_lx) || empty($ys_fb_ht_je) || empty($ys_fb_ht_voje)){
                    $this->ajaxReturn(array('state' => false, 'msg' => "字段不能为空"));
                }
                $condition["pro_id"] = $pro_id;
                $condition["ys_fb_lx"] = $ys_fb_lx;
                $condition["ys_fb_ht_lx"] = $ys_fb_ht_lx;
                $condition['ys_fb_ht_je'] = $ys_fb_ht_je;
                $condition['ys_fb_ht_voje'] = $ys_fb_ht_voje;
                $condition['ys_pj'] = $ys_pj;
                $condition['sj_pj'] = $sj_pj;
                $condition['jd_pj'] = $jd_pj;
                $condition['gc_pj'] = $gc_pj;
                $condition['cg_pj'] = $cg_pj;
                $condition['sh_pj'] = $sh_pj;

                $result  = $Data->add($condition);
                if(false === $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置"));
                }else{
                    $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功", 'id' => $result));
                }
                break;
            case "edit"://
                if(empty($id) || empty($pro_id) || empty($ys_fb_lx) || empty($ys_fb_ht_lx) || empty($ys_fb_ht_je) || empty($ys_fb_ht_voje)){
                    $this->ajaxReturn(array('state' => false, 'msg' => "字段不能为空", 'id' => $id));
                }
                $condition["pro_id"] = $pro_id;
                $condition["ys_fb_lx"] = $ys_fb_lx;
                $condition["ys_fb_ht_lx"] = $ys_fb_ht_lx;
                $condition['ys_fb_ht_je'] = $ys_fb_ht_je;
                $condition['ys_fb_ht_voje'] = $ys_fb_ht_voje;
                $condition['ys_pj'] = $ys_pj;
                $condition['sj_pj'] = $sj_pj;
                $condition['jd_pj'] = $jd_pj;
                $condition['gc_pj'] = $gc_pj;
                $condition['cg_pj'] = $cg_pj;
                $condition['sh_pj'] = $sh_pj;

                $condition['id'] = $id;
                $result  = $Data->save($condition);
                if(false === $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置", 'id' => $id));
                }elseif(0 == $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "信息没有修改", 'id' => $id));
                }else{
                    $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功", 'id' => $id));
                }
                break;
            case "del":
                if(empty($id)){
                    $this->ajaxReturn(array('state' => false, 'msg' => "字段不能为空", 'id' => $id));
                }
                $condition['id'] = $id;
                $result  = $Data->where($condition)->delete();
                if(false === $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置", 'id' => $id));
                }elseif(0 == $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "信息没有修改", 'id' => $id));
                }else{
                    $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功", 'id' => $id));
                }
                break;
            default:
                break;
        }
    }
    //
    //资金流
    public function ajaxZjcSearch(){
        $pagenum = I('page',1); // get the requested page
        $limitnum = I('rows',20); // get how many rows we want to have into the grid
        $sidx = I('sidx','id'); // get index row - i.e. user click to sort
        $sord = I('sord','desc'); // get the direction
        if($sidx == ""){
            $sidx = 'id';
        }

        //手动查询标志
        $searchOn = I('_search');
        //多条件查询
        $cond = array();
        if(TRUE || 'true' == $searchOn) {
            $sarr = I('param.');
            foreach( $sarr as $k=>$v) {
                switch ($k) {
                    case 'zxcl_name':
                        $cond[$k] = array('LIKE', "%$v%");
                        break;
                    case 'id':
                    case 'pro_id':
                    case 'sk_bs':
                        if("all" != $v){
                            $cond[$k] = $v;
                        }
                        break;
                }
            }
        }
        //单条件 find
        if(FALSE && 'true' == $searchOn){
            $searchField = I('searchField');
            $searchString = I('searchString');
            $searchOper = I('searchOper');
            $cond[$searchField] = array('LIKE', "%$searchString%");
        }
        //
        $User = M('zjl_sksj'); // 实例化User对象
        $count = $User->where($cond)->order(array($sidx => $sord))->count();// 查询满足要求的总记录数
        $list =  $User->where($cond)->order(array($sidx => $sord))->page($pagenum,$limitnum)->select();

        $total_pages = 0;
        if( $count >0 ) {
            $total_pages = ceil($count/$limitnum);
        }
        $responce["page"] = $pagenum;
        $responce["total"] = $total_pages;
        $responce["records"] = $count;

        $zjl_bs_arr = USER_FUN_GET_ZJL_SKSJ_BS_NAME();
        $i=0;
        if(!empty($list)){
            foreach($list as $item){
                $responce["rows"][$i]['id']=$item["id"];
                $responce["rows"][$i]['cell'] = array($item['id'],
                    $zjl_bs_arr[$item['sk_bs']],
                    $item['sk_je'],
                    $item['sk_time'],
                );
                $i++;
            }
        }
        $this->ajaxReturn($responce);
    }
    public function ajaxZjlsksjSave(){
        $Data = M('zjl_sksj'); // 实例化Data数据模型

        $oper = I('oper');
        $id = I('id');
        $pro_id = I('pro_id');

        $sk_bs = I('sk_bs');
        $sk_je = I('sk_je');
        $sk_time = I('sk_time');

        $condition = array();
        switch ($oper) {
            case "add"://
                if( empty($pro_id) || empty($sk_bs) || empty($sk_je) || empty($sk_time)){
                    $this->ajaxReturn(array('state' => false, 'msg' => "字段不能为空"));
                }
                $condition["pro_id"] = $pro_id;
                $condition["sk_bs"] = $sk_bs;
                $condition["sk_je"] = $sk_je;
                $condition['sk_time'] = $sk_time;

                $result  = $Data->add($condition);
                if(false === $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置"));
                }else{
                    $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功", 'id' => $result));
                }
                break;
            case "edit"://
                if(empty($id) || empty($pro_id) || empty($sk_bs) || empty($sk_je) || empty($sk_time) ){
                    $this->ajaxReturn(array('state' => false, 'msg' => "字段不能为空", 'id' => $id));
                }
                $condition["pro_id"] = $pro_id;
                $condition["sk_bs"] = $sk_bs;
                $condition["sk_je"] = $sk_je;
                $condition['sk_time'] = $sk_time;

                $condition['id'] = $id;
                $result  = $Data->save($condition);
                if(false === $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置", 'id' => $id));
                }elseif(0 == $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "信息没有修改", 'id' => $id));
                }else{
                    $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功", 'id' => $id));
                }
                break;
            case "del":
                if(empty($id)){
                    $this->ajaxReturn(array('state' => false, 'msg' => "字段不能为空", 'id' => $id));
                }
                $condition['id'] = $id;
                $result  = $Data->where($condition)->delete();
                if(false === $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置", 'id' => $id));
                }elseif(0 == $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "信息没有修改", 'id' => $id));
                }else{
                    $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功", 'id' => $id));
                }
                break;
            default:
                break;
        }
    }
    //
    public function ajaxZjlskyjSearch(){
        $pagenum = I('page',1); // get the requested page
        $limitnum = I('rows',20); // get how many rows we want to have into the grid
        $sidx = I('sidx','id'); // get index row - i.e. user click to sort
        $sord = I('sord','desc'); // get the direction
        if($sidx == ""){
            $sidx = 'id';
        }

        $hetong_amt = I('hetong_amt');
        //手动查询标志
        $searchOn = I('_search');
        //多条件查询
        $cond = array();
        if(TRUE || 'true' == $searchOn) {
            $sarr = I('param.');
            foreach( $sarr as $k=>$v) {
                switch ($k) {
                    case 'zxcl_name':
                        $cond[$k] = array('LIKE', "%$v%");
                        break;
                    case 'id':
                    case 'pro_id':
                    case 'sk_bs':
                        if("all" != $v){
                            $cond[$k] = $v;
                        }
                        break;
                }
            }
        }
        //单条件 find
        if(FALSE && 'true' == $searchOn){
            $searchField = I('searchField');
            $searchString = I('searchString');
            $searchOper = I('searchOper');
            $cond[$searchField] = array('LIKE', "%$searchString%");
        }
        //
        $User = M('zjl_skyj'); // 实例化User对象
        $count = $User->where($cond)->order(array($sidx => $sord))->count();// 查询满足要求的总记录数
        $list =  $User->where($cond)->order(array($sidx => $sord))->page($pagenum,$limitnum)->select();

        $total_pages = 0;
        if( $count >0 ) {
            $total_pages = ceil($count/$limitnum);
        }
        $responce["page"] = $pagenum;
        $responce["total"] = $total_pages;
        $responce["records"] = $count;

        $zjl_bs_arr = USER_FUN_GET_ZJL_SKYJ_BS_NAME();
        $i=0;
        if(!empty($list)){
            foreach($list as $item){
                $responce["rows"][$i]['id']=$item["id"];
                $responce["rows"][$i]['cell'] = array($item['id'],
                    $zjl_bs_arr[$item['sk_bs']],
                    $item['sk_rate'],
                    floatval($hetong_amt) * floatval($item['sk_rate']),
                    $item['sk_time'],
                );
                $i++;
            }
        }
        $this->ajaxReturn($responce);
    }
    public function ajaxZjlskyjSave(){
        $Data = M('zjl_skyj'); // 实例化Data数据模型

        $oper = I('oper');
        $id = I('id');
        $pro_id = I('pro_id');


        $sk_bs = I('sk_bs');
        $sk_rate = I('sk_rate');
        $sk_time = I('sk_time');

        $condition = array();
        switch ($oper) {
            case "add"://
                if( empty($pro_id) || empty($sk_bs) || empty($sk_rate) || empty($sk_time)){
                    $this->ajaxReturn(array('state' => false, 'msg' => "字段不能为空"));
                }
                $condition["pro_id"] = $pro_id;
                $condition["sk_bs"] = $sk_bs;
                $condition["sk_rate"] = $sk_rate;
                $condition['sk_time'] = $sk_time;

                $result  = $Data->add($condition);
                if(false === $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置"));
                }else{
                    $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功", 'id' => $result));
                }
                break;
            case "edit"://
                if(empty($id) || empty($pro_id) || empty($sk_bs) || empty($sk_rate) || empty($sk_time) ){
                    $this->ajaxReturn(array('state' => false, 'msg' => "字段不能为空", 'id' => $id));
                }
                $condition["pro_id"] = $pro_id;
                $condition["sk_bs"] = $sk_bs;
                $condition["sk_rate"] = $sk_rate;
                $condition['sk_time'] = $sk_time;

                $condition['id'] = $id;
                $result  = $Data->save($condition);
                if(false === $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置", 'id' => $id));
                }elseif(0 == $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "信息没有修改", 'id' => $id));
                }else{
                    $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功", 'id' => $id));
                }
                break;
            case "del":
                if(empty($id)){
                    $this->ajaxReturn(array('state' => false, 'msg' => "字段不能为空", 'id' => $id));
                }
                $condition['id'] = $id;
                $result  = $Data->where($condition)->delete();
                if(false === $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置", 'id' => $id));
                }elseif(0 == $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "信息没有修改", 'id' => $id));
                }else{
                    $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功", 'id' => $id));
                }
                break;
            default:
                break;
        }
    }
    ///////////////////////////////////////////////////////////////////////////////////////////////
    public function ajaxZjlfkyjSearch(){
        $pagenum = I('page',1); // get the requested page
        $limitnum = I('rows',20); // get how many rows we want to have into the grid
        $sidx = I('sidx','id'); // get index row - i.e. user click to sort
        $sord = I('sord','desc'); // get the direction
        if($sidx == ""){
            $sidx = 'id';
        }

        $fb_amt = I('fb_amt');
        $cl_amt = I('cl_amt');
        //手动查询标志
        $searchOn = I('_search');
        //多条件查询
        $cond = array();
        if(TRUE || 'true' == $searchOn) {
            $sarr = I('param.');
            foreach( $sarr as $k=>$v) {
                switch ($k) {
                    case 'id':
                    case 'pro_id':
                    case 'fk_bs':
                        if("all" != $v){
                            $cond[$k] = $v;
                        }
                        break;
                }
            }
        }
        //单条件 find
        if(FALSE && 'true' == $searchOn){
            $searchField = I('searchField');
            $searchString = I('searchString');
            $searchOper = I('searchOper');
            $cond[$searchField] = array('LIKE', "%$searchString%");
        }
        //
        $User = M('zjl_fkyj'); // 实例化User对象
        $count = $User->where($cond)->order(array($sidx => $sord))->count();// 查询满足要求的总记录数
        $list =  $User->where($cond)->order(array($sidx => $sord))->page($pagenum,$limitnum)->select();

        $total_pages = 0;
        if( $count >0 ) {
            $total_pages = ceil($count/$limitnum);
        }
        $responce["page"] = $pagenum;
        $responce["total"] = $total_pages;
        $responce["records"] = $count;

        $zjl_bs_arr = USER_FUN_GET_ZJL_FKYJ_BS_NAME();
        $i=0;
        if(!empty($list)){
            foreach($list as $item){
                $responce["rows"][$i]['id']=$item["id"];
                $responce["rows"][$i]['cell'] = array($item['id'],
                    $zjl_bs_arr[$item['fk_bs']],
                    $item['fk_fb_rate_gc'],
                    floatval($fb_amt) * floatval($item['fk_fb_rate_gc']),
                    $item['fk_cl_rate_cg'],
                    floatval($cl_amt) * floatval($item['fk_cl_rate_cg']),
                    $item['fk_time'],
                );
                $i++;
            }
        }
        $this->ajaxReturn($responce);
    }
    public function ajaxZjlfkyjSave(){
        $Data = M('zjl_fkyj'); // 实例化Data数据模型

        $oper = I('oper');
        $id = I('id');
        $pro_id = I('pro_id');


        $fk_bs = I('fk_bs');
        $fk_fb_rate_gc = I('fk_fb_rate_gc');
        $fk_cl_rate_cg = I('fk_cl_rate_cg');
        $fk_time = I('fk_time');


        $condition = array();
        switch ($oper) {
            case "add"://
                if( empty($pro_id) || empty($fk_bs) ){
                    $this->ajaxReturn(array('state' => false, 'msg' => "字段不能为空"));
                }
                $condition["pro_id"] = $pro_id;
                $condition["fk_bs"] = $fk_bs;
                //只能增加笔数
                //$condition["fk_fb_rate_gc"] = $fk_fb_rate_gc;
                //$condition["fk_cl_rate_cg"] = $fk_cl_rate_cg;

                $result  = $Data->add($condition);
                if(false === $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置"));
                }else{
                    $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功", 'id' => $result));
                }
                break;
            case "edit"://
                if(empty($id) || empty($pro_id) || empty($fk_bs)  ){
                    $this->ajaxReturn(array('state' => false, 'msg' => "字段不能为空", 'id' => $id));
                }
                $condition["pro_id"] = $pro_id;
                $condition["fk_bs"] = $fk_bs;
                $condition["fk_fb_rate_gc"] = $fk_fb_rate_gc;
                $condition["fk_cl_rate_cg"] = $fk_cl_rate_cg;
                $condition["fk_time"] = $fk_time;

                $condition['id'] = $id;
                $result  = $Data->save($condition);
                if(false === $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置", 'id' => $id));
                }elseif(0 == $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "信息没有修改", 'id' => $id));
                }else{
                    $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功", 'id' => $id));
                }
                break;
            case "del":
                if(empty($id)){
                    $this->ajaxReturn(array('state' => false, 'msg' => "字段不能为空", 'id' => $id));
                }
                $condition['id'] = $id;
                $result  = $Data->where($condition)->delete();
                if(false === $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置", 'id' => $id));
                }elseif(0 == $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "信息没有修改", 'id' => $id));
                }else{
                    $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功", 'id' => $id));
                }
                break;
            default:
                break;
        }
    }
}
