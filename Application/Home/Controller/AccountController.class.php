<?php
namespace Home\Controller;
use Think\Controller;
//use Think\Page;
class AccountController extends Controller {

    public function ajaxAccountmngSave(){
        $Data = M('account'); // 实例化Data数据模型

        $oper = I('oper');
        $id = I('id');

        $account_name = I('account_name');
        $qs_name = I('qs_name');
        $account_id = I('account_id');
        $account_pwd = I('account_pwd');
        $account_csje = I('account_csje');
        $sm = I('sm');
        $status = I('status');

        switch ($oper) {
            case "add"://
                if( empty($account_id) || empty($account_pwd) ){
                    $this->ajaxReturn(array('state' => false, 'msg' => "字段不能为空"));
                }
                $condition["account_name"] = $account_name;
                $condition["account_id"] = $account_id;
                $list  = $Data->where($condition)->find();
                if(!empty($list)){
                    $this->ajaxReturn(array('state' => false, 'msg' => "已经有相同 账号名称 存在"));
                }
                $condition["qs_name"] = $qs_name;
                $condition["account_pwd"] = $account_pwd;
                $condition["account_csje"] = $account_csje;
                $condition['sm'] = $sm;
                trace($condition);
                $result  = $Data->add($condition);
                if(false === $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置"));
                }else{
                    $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功", 'id' => $result));
                }
                break;
            case "edit"://
                if(empty($id) || empty($account_name)|| empty($account_id) || empty($status)){
                    $this->ajaxReturn(array('state' => false, 'msg' => "字段不能为空", 'id' => $id));
                }
                $condition["account_name"] = $account_name;
                $condition["qs_name"] = $qs_name;
                $condition["account_id"] = $account_id;
                $condition["account_pwd"] = $account_pwd;
                $condition["account_csje"] = $account_csje;
                $condition['sm'] = $sm;
                $condition['status'] = $status;
                $condition['id'] = $id;
                $result  = $Data->save($condition);
                if(false === $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置", 'id' => $id));
                }elseif(0 == $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "信息不允许修改", 'id' => $id));
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
                    $this->ajaxReturn(array('state' => false, 'msg' => "信息不允许修改", 'id' => $id));
                }else{
                    $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功", 'id' => $id));
                }
                break;
            default:
                break;
        }
    }

    public function _before_ajaxAccountmngSave(){
        $department = $_SESSION['department'];
        if("gl" != $department){
            $this->error("没有权限!");
        }
    }

    public function ajaxAccountmngSearch(){
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
                    case 'account_name':
                    case 'qs_name':
                    case 'account_id':
                    case 'sm':
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
        $User = M('account'); // 实例化User对象
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
        $account_arr = USER_FUN_GET_QS_NAME();
        $st = USER_FUN_GET_PROJECT_STATUS_NAME();
        foreach($list as $item){
            $responce["rows"][$i]['id']=$item["id"];
            $responce["rows"][$i]['cell'] = array($item['id'],
                $item['account_name'],$account_arr[$item['qs_name']],
                $item['account_id'],$item['account_pwd'],$item['account_csje'],
                $item['sm'],$st[$item['status']]);
            $i++;
        }
        $this->ajaxReturn($responce);

    }

    public function _before_accountmng(){
        $department = $_SESSION['department'];
        if("gl" != $department){
            $this->error("没有权限!");
        }
    }
    public function accountmng(){
        layout(false);
        //显示用户列表
        $this->display();
    }
}