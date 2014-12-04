<?php
namespace Home\Controller;
use Think\Controller;
//use Think\Page;
class ProjectController extends Controller {
    public function ajaxProjectmngSave(){
        $Data = M('project'); // 实例化Data数据模型

        $oper = I('oper');
        $id = I('id');
        $pro_id = I('pro_id');
        $pro_name = I('pro_name');
        $status = I('status');

        switch ($oper) {
            case "add"://
                if( empty($pro_id) || empty($pro_name) ){
                    $this->ajaxReturn(array('state' => false, 'msg' => "字段不能为空"));
                }
                $condition["pro_id"] = $pro_id;
                $list  = $Data->where($condition)->find();
                if(!empty($list)){
                    $this->ajaxReturn(array('state' => false, 'msg' => "$pro_id 已经有相同 项目ID 存在"));
                }
                $condition['pro_name'] = $pro_name;
                $condition["status"] = 1;
                $result  = $Data->add($condition);
                if(false === $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置"));
                }else{
                    $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功", 'id' => $result));
                }
                break;
            case "edit"://
                if(empty($id) || empty($pro_id) || empty($pro_name) || empty($status)){
                    $this->ajaxReturn(array('state' => false, 'msg' => "字段不能为空", 'id' => $id));
                }
                $condition['pro_name'] = $pro_name;
                $condition['status'] = $status;
                $condition['id'] = $id;
                $result  = $Data->save($condition);
                if(false === $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置", 'id' => $id));
                }elseif(0 == $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "项目ID信息不允许修改", 'id' => $id));
                }else{
                    $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功", 'id' => $id));
                }
                break;
            case "del":
                if(empty($id)){
                    $this->ajaxReturn(array('state' => false, 'msg' => "字段不能为空", 'id' => $id));
                }
                $condition['id'] = $id;
                $condition['status'] = 3;
                $result  = $Data->save($condition);
                if(false === $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置", 'id' => $id));
                }elseif(0 == $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "项目ID信息不允许修改", 'id' => $id));
                }else{
                    $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功", 'id' => $id));
                }
                break;
            default:
                break;
        }
    }
    public function _before_ajaxProjectmngSearch(){
        $department = $_SESSION['department'];
        if("gl" != $department){
            $this->error("没有权限!");
        }
    }
    public function ajaxProjectmngSearch(){
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
        if('true' == $searchOn) {
            $sarr = I('param.');
            foreach( $sarr as $k=>$v) {
                switch ($k) {
                    case 'pro_id':
                    case 'pro_name':
                        $cond[$k] = array('LIKE', "%$v%");
                        break;
                    case 'id':
                    case 'status':
                        if("0" != $v){
                            $cond[$k] = $v;
                        }
                        break;
                }
            }
        }
        //
        //单条件 find
        if(FALSE && 'true' == $searchOn){
            $searchField = I('searchField');
            $searchString = I('searchString');
            $searchOper = I('searchOper');
            $cond[$searchField] = array('LIKE', "%$searchString%");
        }
        //
        $User = M('project'); // 实例化User对象
        $count = $User->where($cond)->order(array($sidx => $sord))->count();// 查询满足要求的总记录数
        $list =  $User->where($cond)->order(array($sidx => $sord))->page($pagenum,$limitnum)->select();

        $total_pages = 0;
        if( $count >0 ) {
            $total_pages = ceil($count/$limitnum);
        }
        $responce["page"] = $pagenum;
        $responce["total"] = $total_pages;
        $responce["records"] = $count;

        $i=0;
        $st = USER_FUN_GET_PROJECT_STATUS_NAME();
        foreach($list as $item){
            $responce["rows"][$i]['id']=$item["id"];
            $responce["rows"][$i]['cell'] = array($item['id'],
                $item['pro_id'],$item['pro_name'],$st[$item['status']]);
            $i++;
        }
        $this->ajaxReturn($responce);

    }
    public function _before_projectmng(){
        $department = $_SESSION['department'];
        if("gl" != $department){
            $this->error("没有权限!");
        }
    }
    public function projectmng(){
        layout(false);
        //显示用户列表
        $this->display();
    }
    public function ajaxPrivilegeSave(){
        $search_pro_id = I('pro2user_pro_id');
        $pro2user_all_str = I("pro2user_all_str");
        $Pro2user = M('pro2user'); // 实例化Data数据模型
        $cond["pro_id"] = $search_pro_id;
        $result =  $Pro2user->where($cond)->delete();
        if(false === $result){
            $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置", 'pro_id' => $search_pro_id));
        }
        //
        //trace($search_pro_id);
        //trace($pro2user_all_str);
        $rec_arr = explode("|",$pro2user_all_str);
        //trace($rec_arr);
        if(empty($rec_arr)){
            $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,没有需要存储的记录", 'pro_id' => $search_pro_id));
        }

        $i=0;
        foreach($rec_arr as $rec){
            $field_arr = explode(",",$rec);
            if(empty($field_arr) || count($field_arr) < 3){
               continue;
            }
            $cond["department"]=$field_arr[1];
            $cond["user_id"]=$field_arr[2];
            $result  = $Pro2user->add($cond);
            if(false === $result){
                $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置"));
            }
            $i++;
        }
        $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功", 'pro_id' => $i));
    }
    public function ajaxPrivilegeGet(){
        $search_pro_id = I('pro_id');
        $User = M('user'); // 实例化Data数据模型
        $condition['status'] = 2;//normal
        $list =  $User->where($condition)->select();
        $dep = USER_FUN_GET_DEPARTMENT_NAME();
        $all_user_arr = array();

        $i=1;
        $dep_node = array();
        foreach($dep as $key => $value){
            $tmp = array();
            $tmp["pId"] = 0;
            $tmp["id"] = $i++;
            $tmp["name"] = $value;
            $tmp["isParent"] = true;
            $all_user_arr[] = $tmp;
            $dep_node[$key] = $tmp["id"];
        }
        //user
        $i=101;
        foreach($list as $item){
            $tmp = array();
            $tmp["pId"] = $dep_node[$item['department']];
            $tmp["id"] = $i++;
            $tmp["name"] = $item['user_name'];
            $tmp["department"] = $item['department'];
            $tmp["user_id"] = $item['id'];
            $tmp["pro_id"] = $search_pro_id;
            $all_user_arr[] = $tmp;
        }

        //得到项目相关用户列表
        $Pro2user = M('pro2user'); // 实例化Data数据模型
        $cond["pro_id"] = $search_pro_id;
        $userlist =  $Pro2user->where($cond)->select();
        if(count($userlist) >=1){
            foreach($userlist as $item){
                foreach($all_user_arr as &$tmp){
                    if($tmp["pro_id"] == $item['pro_id']
                        && $tmp["department"] == $item['department']
                        && $tmp["user_id"] == $item['user_id']){
                        $tmp["checked"] = true;
                    }
                }
            }
        }
        trace($all_user_arr);
        $this->ajaxReturn($all_user_arr);
    }
    public function projectsearch(){
        layout(false);
        //显示用户列表
        $this->display();
    }
    public function ajaxProjectSearchSearch(){
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
        if('true' == $searchOn) {
            $sarr = I('param.');
            foreach( $sarr as $k=>$v) {
                switch ($k) {
                    case 'pro_id':
                    case 'pro_name':
                        $cond[$k] = array('LIKE', "%$v%");
                        break;
                    case 'id':
                    case 'zj_lock':
                    case 'status':
                        if("0" != $v && "all" != $v && "" != $v){
                            $cond[$k] = $v;
                        }
                        break;
                }
            }
        }
        //强制为 正常的项目
        $cond['at_project.status'] = 2;//normal
        $sidx = "at_project." . $sidx;
        //
        $PROJECT = M('project'); // 实例化对象
        $count = $PROJECT
            ->join('at_pro2user ON at_project.pro_id = at_pro2user.pro_id and at_pro2user.user_id = ' . session("user_id") . " and at_pro2user.department = '" . session("department") . "'")
            ->where($cond)->order(array($sidx => $sord))->count();// 查询满足要求的总记录数
        $list =  $PROJECT
            ->join('at_pro2user ON at_project.pro_id = at_pro2user.pro_id and at_pro2user.user_id = ' . session("user_id") . " and at_pro2user.department ='" . session("department") . "'")
            ->where($cond)->order(array($sidx => $sord))->page($pagenum,$limitnum)->select();

        $total_pages = 0;
        if( $count >0 ) {
            $total_pages = ceil($count/$limitnum);
        }
        $responce["page"] = $pagenum;
        $responce["total"] = $total_pages;
        $responce["records"] = $count;

        $i=0;
        $st = USER_FUN_GET_PROJECT_STATUS_NAME();
        foreach($list as $item){
            $responce["rows"][$i]['id']=$item["id"];
            $responce["rows"][$i]['cell'] = array($item['id'],
                $item['pro_id'],$item['pro_name'],$item['pro_zjze'],((intval($item['zj_lock']) == 2) ? "已锁定":"未锁定"),
                $item['pro_ks_time'] == "" ? "" : date("Y-m-d",strtotime($item['pro_ks_time'])),
                $item['pro_js_time'] == "" ? "" : date("Y-m-d",strtotime($item['pro_js_time'])),
                );
            $i++;
        }
        $this->ajaxReturn($responce);
    }
}