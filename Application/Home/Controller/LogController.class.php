<?php
namespace Home\Controller;
use Think\Controller;
//use Think\Page;
class LogController extends Controller {
    public function logsearch(){
        layout(false);
        //显示用户列表
        $this->display();
    }
    public function ajaxLogSearchSearch(){
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
                    case 'log_time':
                    case 'log_msg':
                        $cond[$k] = array('LIKE', "%$v%");
                        break;
                    case 'id':
                    case 'lx':
                        if("0" != $v && "all" != $v && "" != $v){
                            $cond[$k] = $v;
                        }
                        break;
                }
            }
        }
        //
        $sidx = "at_log." . $sidx;
        //
        $LOG = M('log'); // 实例化对象
        $count = $LOG->where($cond)->order(array($sidx => $sord))->count();// 查询满足要求的总记录数
        $list =  $LOG->where($cond)->order(array($sidx => $sord))->page($pagenum,$limitnum)->select();
        $total_pages = 0;
        if( $count >0 ) {
            $total_pages = ceil($count/$limitnum);
        }
        $total_pages = 0;
        if( $count >0 ) {
            $total_pages = ceil($count/$limitnum);
        }
        $responce["page"] = $pagenum;
        $responce["total"] = $total_pages;
        $responce["records"] = $count;

        $i=0;
        $st = USER_FUN_GET_LOG_TYPE_NAME();
        foreach($list as $item){
            $responce["rows"][$i]['id']=$item["id"];
            $responce["rows"][$i]['cell'] = array($item['id'],
                $item['log_time'] == "" ? "" : date("Y-m-d H:i",strtotime($item['log_time'])),
                $st[$item['lx']],
                $item['log_msg'],
                );
            $i++;
        }
        $this->ajaxReturn($responce);
    }
    //
    public function ajaxLogSearchDetail(){
        $pagenum = I('page',1); // get the requested page
        $limitnum = I('rows',1); // get how many rows we want to have into the grid
        $sidx = I('sidx','id'); // get index row - i.e. user click to sort
        $sord = I('sord','desc'); // get the direction
        if($sidx == ""){
            $sidx = 'id';
        }
        //手动查询标志
        $searchOn = I('_search');
        $cond = array();
        //多条件查询
        $searchOn = true;
        if('true' == $searchOn) {
            $sarr = I('param.');
            foreach( $sarr as $k=>$v) {
                switch ($k) {
                    case 'id':
                        $cond[$k] = $v;
                        break;
                }
            }
        }
        //trace($cond,"cond");
        //
        $sidx = "at_log." . $sidx;
        //
        $LOG = M('log'); // 实例化对象
        $count = $LOG->where($cond)->order(array($sidx => $sord))->count();// 查询满足要求的总记录数
        $list =  $LOG->where($cond)->order(array($sidx => $sord))->page($pagenum,$limitnum)->select();
        $total_pages = 0;
        if( $count >0 ) {
            $total_pages = ceil($count/$limitnum);
        }
        $total_pages = 0;
        if( $count >0 ) {
            $total_pages = ceil($count/$limitnum);
        }
        $responce["page"] = $pagenum;
        $responce["total"] = $total_pages;
        $responce["records"] = $count;

        $i=0;
        foreach($list as $item){
            $responce["rows"][$i]['id']=$item["id"];
            $responce["rows"][$i]['cell'] = array($item['id'],
                //$item['log_detail'],
                $item['log_msg'],
            );
            $i++;
        }
        //trace($responce,"subgrid");
        $this->ajaxReturn($responce);
    }
    //
    public function ajaxLogSearchSave(){
        $Data = M('log'); // 实例化Data数据模型

        $oper = I('oper');
        $id = I('id');

        switch ($oper) {
            case "add"://
                break;
            case "edit"://
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
                    $this->ajaxReturn(array('state' => false, 'msg' => "信息不允许修改", 'id' => $id));
                }else{
                    $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功", 'id' => $id));
                }
                break;
            default:
                break;
        }
    }

    public function _before_ajaxLogSearchSave(){
        $department = $_SESSION['department'];
        if("gl" != $department){
            $this->error("没有权限!");
        }
    }
}