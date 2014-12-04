<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends Controller {

    private function clearSession(){
        unset($_SESSION['user_id']);
        unset($_SESSION['department']);
        unset($_SESSION['userflag']);
        session(null);
    }
    public function login(){
        layout(false);

        $this->clearSession();
        $this->department_array = USER_FUN_GET_DEPARTMENT_ARRAY(); // 进行模板变量赋值
        $this->display();
    }
    public function logon(){
        $Data = M('user'); // 实例化Data数据模型

        $email = I('email','');
        $password = I('password','');
        $department = I('department','');
        $theme = I('theme','redmond');
        //
        $_SESSION['theme'] = $theme;
        //
        $condition["status"] = 2;//normal
        $condition["email"] = $email;
        $condition["password"] = $password;
        $condition["department"] = $department;
        $list  = $Data->where($condition)->find();

        if(empty($list)){
            $this->message = "$email,$password,$department";
            $this->error("登录失败!");
        }else{
            $_SESSION['user_id'] = $list['id'];
            $_SESSION['department'] = $list['department'];
            $_SESSION['userflag'] = $list['userflag'];
            $this->success('登录成功!',U('AllInOne/index'));
        }
    }

    public function _before_ajaxUserSave(){
        $userflag = $_SESSION['userflag'];
        if(2 != $userflag){
            $this->error("没有权限!");
        }
    }
    public function ajaxUserSave(){
        $Data = M('user'); // 实例化Data数据模型

        $oper = I('oper');
        $id = I('id');
        $user_name = I('user_name');
        $email = I('email');
        $department = I('department');
        $status = I('status');

        switch ($oper) {
            case "add"://
                if( empty($user_name) || empty($email) || empty($department)){
                    $this->ajaxReturn(array('state' => false, 'msg' => "字段不能为空"));
                }
                $condition["email"] = $email;
                $condition["department"] = $department;
                $list  = $Data->where($condition)->find();
                $dep = USER_FUN_GET_DEPARTMENT_NAME();
                $dep_name = $dep[$department];
                if(!empty($list)){
                    $this->ajaxReturn(array('state' => false, 'msg' => "$dep_name 内已经有相同 EMAIL 存在"));
                }
                $condition['user_name'] = $user_name;
                //$condition['password'] = md5($email);
                $condition['password'] = "111111";
                $result  = $Data->add($condition);
                if(false === $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置"));
                }else{
                    $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功", 'id' => $result));
                }
                break;
            case "edit"://
                if(empty($id) || empty($user_name) || empty($email) || empty($department) || empty($status)){
                    $this->ajaxReturn(array('state' => false, 'msg' => "字段不能为空", 'id' => $id));
                }
                $condition['user_name'] = $user_name;
                $condition["email"] = $email;
                $condition["department"] = $department;
                $condition['status'] = $status;
                $condition['id'] = $id;
                $result  = $Data->save($condition);
                if(false === $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置", 'id' => $id));
                }elseif(0 == $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "相同的记录,请修改用户信息", 'id' => $id));
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
                    $this->ajaxReturn(array('state' => false, 'msg' => "相同的记录,请修改用户信息", 'id' => $id));
                }else{
                    $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功", 'id' => $id));
                }
                break;
            default:
                break;
        }
    }

    public function _before_ajaxUserSearch(){
        $userflag = $_SESSION['userflag'];
        if(2 != $userflag){
            $this->error("没有权限!");
        }
    }

    public function ajaxUserSearch(){
        $pagenum = I('page',1); // get the requested page
        $limitnum = I('rows',20); // get how many rows we want to have into the grid
        $sidx = I('sidx','id'); // get index row - i.e. user click to sort
        $sord = I('sord','desc'); // get the direction
        if($sidx == ""){
            $sidx = 'id';
        }
        //手动查询标志
        $searchOn = I('_search');
        $cond = array();
        //多条件查询
        if('true' == $searchOn) {
            $sarr = I('param.');
            foreach( $sarr as $k=>$v) {
                switch ($k) {
                    case 'user_name':
                    case 'email':
                        $cond[$k] = array('LIKE', "%$v%");
                        break;
                    case 'department':
                        if("all" != $v){
                            $cond[$k] = array('LIKE', "%$v%");
                        }
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
        //单条件 find
        if(FALSE && 'true' == $searchOn){
            $searchField = I('searchField');
            $searchString = I('searchString');
            $searchOper = I('searchOper');
            $cond[$searchField] = array('LIKE', "%$searchString%");
        }
        //
        $User = M('User'); // 实例化User对象
        $count = $User->where($cond)->order(array($sidx => $sord))->count();// 查询满足要求的总记录数
        $list =  $User->where($cond)->order(array($sidx => $sord))->page($pagenum,$limitnum)->select();
        $total_pages = 0;
        if( $count >0 ) {
            $total_pages = ceil($count/$limitnum);
        }
        $responce["page"] = $pagenum;
        $responce["total"] = $total_pages;
        $responce["records"] = $count;
        trace($count,"count=");
        $i=0;
        $dep = USER_FUN_GET_DEPARTMENT_NAME();
        $st = USER_FUN_GET_USER_STATUS_NAME();
        foreach($list as $item){
            $responce["rows"][$i]['id']=$item["id"];
            $responce["rows"][$i]['cell'] = array($item['id'],
                $item['user_name'],$item['email'],$dep[$item['department']],$st[$item['status']]);
            $i++;
        }
        $this->ajaxReturn($responce);
    }

    public function usermng(){
        layout(false);
        //显示用户列表
        $this->display();
    }
    public function usersalary(){
        layout(false);
        //显示用户列表
        $this->display();
    }
    public function ajaxUserSalarySearch(){
        $pagenum = I('page',1); // get the requested page
        $limitnum = I('rows',20); // get how many rows we want to have into the grid
        $sidx = I('sidx','id'); // get index row - i.e. user click to sort
        $sord = I('sord','desc'); // get the direction
        if($sidx == ""){
            $sidx = 'id';
        }
        //手动查询标志
        $searchOn = I('_search');
        $cond = array();
        //多条件查询
        if('true' == $searchOn) {
            $sarr = I('param.');
            foreach( $sarr as $k=>$v) {
                switch ($k) {
                    case 'user_name':
                    case 'email':
                        $cond[$k] = array('LIKE', "%$v%");
                        break;
                    case 'department':
                        if("all" != $v){
                            $cond[$k] = array('LIKE', "%$v%");
                        }
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
        //只用正常的机电和工程
        $cond["status"] = 2;
        $cond['department'] = array('like',array('gc','jd'),'OR');
        //单条件 find
        if(FALSE && 'true' == $searchOn){
            $searchField = I('searchField');
            $searchString = I('searchString');
            $searchOper = I('searchOper');
            $cond[$searchField] = array('LIKE', "%$searchString%");
        }
        //
        $User = M('User'); // 实例化User对象
        $count = $User->where($cond)->order(array($sidx => $sord))->count();// 查询满足要求的总记录数
        $list =  $User->where($cond)->order(array($sidx => $sord))->page($pagenum,$limitnum)->select();
        $total_pages = 0;
        if( $count >0 ) {
            $total_pages = ceil($count/$limitnum);
        }
        $responce["page"] = $pagenum;
        $responce["total"] = $total_pages;
        $responce["records"] = $count;
        trace($count,"count=");
        $i=0;
        $dep = USER_FUN_GET_DEPARTMENT_NAME();
        $st = USER_FUN_GET_USER_STATUS_NAME();
        foreach($list as $item){
            $responce["rows"][$i]['id']=$item["id"];
            $responce["rows"][$i]['cell'] = array($item['id'],
                $item['user_name'],$item['email'],$dep[$item['department']],$item['salary']);
            $i++;
        }
        $this->ajaxReturn($responce);
    }
    public function ajaxUserSalarySave(){
        $Data = M('user'); // 实例化Data数据模型

        $oper = I('oper');
        $id = I('id');
        $salary = I('salary');

        switch ($oper) {
            case "edit"://
                if(empty($id) || empty($salary)){
                    $this->ajaxReturn(array('state' => false, 'msg' => "字段不能为空", 'id' => $id));
                }
                $condition['salary'] = $salary;
                $condition['id'] = $id;
                $result  = $Data->save($condition);
                if(false === $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置", 'id' => $id));
                }elseif(0 == $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "相同的记录,请修改用户信息", 'id' => $id));
                }else{
                    $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功", 'id' => $id));
                }
                break;
            default:
                break;
        }
    }

    public function add(){
        //layout(false);
        $this->department_array = USER_FUN_GET_DEPARTMENT_ARRAY(); // 进行模板变量赋值
        $this->display();
    }
    public function insert(){
        $Data   =   D('user');
        if($Data->create()) {
            $result =   $Data->add();
            if($result) {
                $this->success('操作成功!',"login");
            }else{
                $this->error('写入错误!');
            }
        }else{
            $this->error($Data->getError());
        }
    }

}
