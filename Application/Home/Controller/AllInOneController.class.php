<?php
namespace Home\Controller;
use Think\Controller;
use Home\Model\RedisModel;

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
        $this->zj_lock = $project_detail["zj_lock"];
        $this->pro_zjze = $project_detail["pro_zjze"];

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
    public function ajaxChengeTheme(){
        $theme = I('theme','redmond');
        $_SESSION['theme'] = $theme;
        $this->ajaxReturn(array('state' => true, 'msg' => "更改成功,请按 F5 重新刷新页面"));
    }
    public function ajaxZjcSearch(){
        $pagenum = I('page',1); // get the requested page
        $limitnum = I('rows',20); // get how many rows we want to have into the grid
        $sidx = I('sidx','id'); // get index row - i.e. user click to sort
        $sord = I('sord','desc'); // get the direction
        if($sidx == ""){
            $sidx = 'id';
        }

        //查询标志
        $pro_id = I('pro_id');
        $zj_lock = I('zj_lock');
        //多条件查询
        $cond = array();
        $param_arr = I('param.');
        if(2 != intval($zj_lock)) {
            //没有锁定
            foreach( $param_arr as $k=>$v) {
                switch ($k) {
                    case 'account_name':
                        $cond[$k] = array('LIKE', "%$v%");
                        break;
                    case 'id':
                        if("all" != $v){
                            $cond[$k] = $v;
                        }
                        break;
                }
            }
        }else{
            //已经锁定
            foreach( $param_arr as $k=>$v) {
                switch ($k) {
                    case 'id':
                    case 'pro_id':
                        if("all" != $v){
                            $cond[$k] = $v;
                        }
                        break;
                }
            }
        }

        //查询 pro_id zj_lock=2 的 各个账号的初始金额的总和
        $list = M()->query("SELECT a.account_id as it_id,SUM(a.pro_account_csje) as it_sum
                            FROM at_project_account as a, at_project as b
                            WHERE a.pro_id=b.pro_id and b.zj_lock=2 group by it_id");

        $sum_pro_account_csje = array();
        if(!empty($list)){
            foreach($list as $cur){
                $sum_pro_account_csje[$cur["it_id"]] = $cur["it_sum"];
            }
        }
        trace($sum_pro_account_csje,"sum_pro_account_csje");
        //查询 pro_id  的 各个账号的初始金额的总和,包括目前已经录入没有锁定的 zj_lock=1 状态的
        $list = M()->query("select account_id,pro_account_csje from at_project_account where pro_id='" . $pro_id . "'");
        $pro_account_csje = array();
        if(!empty($list)){
            foreach($list as $cur){
                $pro_account_csje[$cur["account_id"]] = $cur["pro_account_csje"];
            }
        }
        trace($pro_account_csje,"pro_account_csje");
        //
        if(2 != intval($zj_lock)) {
            //没有锁定
            $Account = M('account'); // 实例化Account对象
            $count = $Account->where($cond)->order(array($sidx => $sord))->count();// 查询满足要求的总记录数
            $list =  $Account->where($cond)->order(array($sidx => $sord))->page($pagenum,$limitnum)->select();

            $total_pages = 0;
            if( $count >0 ) {
                $total_pages = ceil($count/$limitnum);
            }
            $responce["page"] = $pagenum;
            $responce["total"] = $total_pages;
            $responce["records"] = $count;

            $i=0;
            if(!empty($list)){
                foreach($list as $item){
                    $responce["rows"][$i]['id']=$item["id"];
                    $responce["rows"][$i]['cell'] = array($item['id'],
                        $item['account_name'],
                        $item['account_id'],
                        $item['account_csje'],
                        floatval($item['account_csje']) - floatval($sum_pro_account_csje[$item['account_id']]),
                        floatval($pro_account_csje[$item['account_id']]),
                    );
                    $i++;
                }
            }
            $this->ajaxReturn($responce);
        }else{
            //已经锁定
            $Account = M('account'); // 实例化Account对象
            $list = $Account->where("status = 2")->select();
            $account_name = array();
            $account_csje = array();
            if(!empty($list)){
                foreach($list as $cur){
                    $account_name[$cur["account_id"]] = $cur["account_name"];
                    $account_csje[$cur["account_id"]] = $cur["account_csje"];
                }
            }
            //
            $ProAccount = M("project_account");
            $count = $ProAccount->where($cond)->order(array($sidx => $sord))->count();// 查询满足要求的总记录数
            $list =  $ProAccount->where($cond)->order(array($sidx => $sord))->page($pagenum,$limitnum)->select();
            $total_pages = 0;
            if( $count >0 ) {
                $total_pages = ceil($count/$limitnum);
            }
            $responce["page"] = $pagenum;
            $responce["total"] = $total_pages;
            $responce["records"] = $count;
            //
            $i=0;
            if(!empty($list)){
                foreach($list as $item){
                    $responce["rows"][$i]['id']=$item["id"];
                    $responce["rows"][$i]['cell'] = array($item['id'],
                        $account_name[$item['account_id']],
                        $item['account_id'],
                        $account_csje[$item['account_id']],
                        floatval($account_csje[$item['account_id']]) - floatval($sum_pro_account_csje[$item['account_id']]),
                        floatval($item['pro_account_csje']),
                    );
                    $i++;
                }
            }
            $this->ajaxReturn($responce);
        }
    }
    //分配各个账号的可用资金
    public function ajaxZjcSave(){
        $Data = M('project_account'); // 实例化Data数据模型

        $oper = I('oper');
        $id = I('id');
        $pro_id = I('pro_id');
        $pro_account_csje = I('pro_account_csje');
        //以后强化用户权限是使用
        $user_id = I('user_id');
        $department = I('department');
        //
        $condition = array();
        switch ($oper) {
            case "edit"://
                if(empty($id) || empty($pro_id) || empty($pro_account_csje)){
                    $this->ajaxReturn(array('state' => false, 'msg' => "字段不能为空", 'id' => $id));
                }
                //id 位 account 表 ID，通过该ID 找到 account_id
                $Account = M('account');
                $acco_cond = array('id' => $id);
                $list = $Account->where($acco_cond)->limit(1)->select();// 查询满足要求的总记录数
                trace($list,"list");
                $account_id = $list[0]['account_id'];
                //
                $condition["pro_id"] = $pro_id;
                $condition["account_id"] = $account_id;
                $list = $Data->where($condition)->select();// 查询满足要求的总记录数
                $id = -1;
                if(!empty($list)){
                    foreach($list as $item){
                        $id = $item['id'];
                    }
                }
                trace($id,"id");
                //
                $result = false;
                $condition["pro_account_csje"] = $pro_account_csje;
                if($id > 0){
                    $condition["id"] = $id;
                    $result = $Data->save($condition);
                }else{
                    $result  = $Data->add($condition);
                }
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
    public function ajaxJobBuySearch(){
        $pagenum = I('page',1); // get the requested page
        $limitnum = I('rows',20); // get how many rows we want to have into the grid
        $sidx = I('sidx','id'); // get index row - i.e. user click to sort
        $sord = I('sord','desc'); // get the direction
        if($sidx == ""){
            $sidx = 'id';
        }

        //查询标志
        $pro_id = I('pro_id');
        $pro_zjze = I('pro_zjze');
        //
        $buyorsell = I('buyorsell');
        $buyorsell = "1";
        //多条件查询
        $cond = array();
        $param_arr = I('param.');
        foreach( $param_arr as $k=>$v) {
            switch ($k) {
                case 'sm':
                    $cond[$k] = array('LIKE', "%$v%");
                    break;
                case 'id':
                case 'pro_id':
                case 'stock_code':
                case 'exchange_type':
                case 'price':
                case 'status':
                    if("0" != $v && "all" != $v && "" != $v){
                        $cond[$k] = $v;
                    }
                    break;
            }
        }
        //
        $cond['buyorsell'] = $buyorsell;
        //
        $ProJobs = M("project_jobs");
        $count = $ProJobs->where($cond)->order(array($sidx => $sord))->count();// 查询满足要求的总记录数
        $list =  $ProJobs->where($cond)->order(array($sidx => $sord))->page($pagenum,$limitnum)->select();
        $total_pages = 0;
        if( $count >0 ) {
            $total_pages = ceil($count/$limitnum);
        }
        $responce["page"] = $pagenum;
        $responce["total"] = $total_pages;
        $responce["records"] = $count;
        //
        $i=0;
        if(!empty($list)){
            $market_array = USER_FUN_GET_STOCK_MARKET_NAME();
            $status_array = USER_FUN_GET_PROJECT_JOB_STATUS_NAME();
            foreach($list as $item){
                //实时计算实际成交金额和数量
                $job_id = $item["id"];
                $calc_array = $this->get_sj_total_buy_num_price($job_id);
                //
                $responce["rows"][$i]['id']=$item["id"];
                $responce["rows"][$i]['cell'] = array($item['id'],
                    $item['stock_code'],
                    $market_array[$item['exchange_type']],
                    $item['price'],
                    $item['percent'],
                    floatval($item['percent']) * floatval($pro_zjze) /100,
                    floatval($calc_array['total_money']),
                    intval($calc_array['total_num']),
                    $status_array[$item['status']],
                    $item['job_ks_time'],
                    $item['job_js_time'],
                );
                $i++;
            }
        }
        $this->ajaxReturn($responce);
    }
    public function getRedisObj(){
/*        $redis = $_SESSION['redis'];
        trace($redis,"redis");
        if($redis == ""){
            $redis = new RedisModel();
            trace($redis,"redis");
            $_SESSION['redis'] = $redis;
        }
        return $redis;*/
        $redis =  new RedisModel();
        //trace($redis,"redis");
        return $redis;
    }
    public function get_sj_total_buy_num_price($job_id){
        $redis = $this->getRedisObj();
        $all_sj_num = $redis->hashGet("jobs:buy:result:all_sj_num",$job_id,1);
        $all_sj_price = $redis->hashGet("jobs:buy:result:all_sj_price",$job_id,1);
        return array("total_num" => $all_sj_num, "total_money" => ($all_sj_price));
/*        //
        $job_id_result_key = "jobs:result:" . $job_id;
        //
        $redis = new RedisModel();
        $list = $redis->listGet($job_id_result_key,0,-1);
        //trace($list,"list");
        $total_num = 0;
        $total_money = 0.0;
        if(!empty($list)){
            foreach($list as $item_str){
                $item = json_decode($item_str,true);
                $total_num += $item['sj_buy_num'];
                $total_money += $item['sj_buy_price'] * $item['sj_buy_num'];
            }
        }
        return array("total_num" => $total_num, "total_money" => $total_money);*/
    }
    public function get_sj_total_sell_num_price($job_id){
        $redis = $this->getRedisObj();
        $all_sj_num = $redis->hashGet("jobs:sell:result:all_sj_num",$job_id,1);
        $all_sj_price = $redis->hashGet("jobs:sell:result:all_sj_price",$job_id,1);
        return array("total_num" => $all_sj_num, "total_money" => ($all_sj_price));
    }
    public function ajaxJobBuySave(){
        $Data = M('project_jobs'); // 实例化Data数据模型
        //
        $oper = I('oper');
        $id = I('id');
        $pro_id = I('pro_id');
        //
        $user_id = I('user_id');
        $department = I('department');
        //
        $pro_zjze = I('pro_zjze');
        $stock_code = I('stock_code');
        $exchange_type = I('exchange_type');
        $percent = I('percent');
        $price = I('price');
        $yj_buy_je = I('yj_buy_je');
        $sj_buy_je = I('sj_buy_je');
        $status = I('status');
        $sm = I('sm');
        //
        $buyorsell = I('buyorsell');
        $buyorsell = "1";
        //
        trace($pro_id,"pro_id");
        //
        $condition = array();
        switch ($oper) {
            case "add"://
                if( empty($pro_id) || empty($stock_code) || empty($percent) || empty($price)){
                    $this->ajaxReturn(array('state' => false, 'msg' => "字段不能为空"));
                }
                $status = "1";
                //
                $condition["pro_id"] = $pro_id;
                //$condition["user_id"] = $user_id;
                //$condition["department"] = $department;
                $condition['stock_code'] = $stock_code;
                $condition['exchange_type'] = $exchange_type;
                $condition['percent'] = $percent;
                $condition['price'] = $price;
                $condition['sm'] = $sm;
                $condition['buyorsell'] = $buyorsell;
                $condition['status'] = $status;

                $result  = $Data->add($condition);
                if(false === $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置"));
                }else{
                    $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功", 'id' => $result));
                }
                break;
            case "edit"://
                if(empty($id) || empty($pro_id) || empty($stock_code) || empty($percent) || empty($price) || empty($status)){
                    $this->ajaxReturn(array('state' => false, 'msg' => "字段不能为空", 'id' => $id));
                }
                $condition["pro_id"] = $pro_id;
                //$condition["user_id"] = $user_id;
                //$condition["department"] = $department;
                $condition['stock_code'] = $stock_code;
                $condition['exchange_type'] = $exchange_type;
                $condition['percent'] = $percent;
                $condition['price'] = $price;
                $condition['sm'] = $sm;
                $condition['buyorsell'] = $buyorsell;
                $condition['status'] = $status;

                $condition['id'] = $id;
                $result  = $Data->save($condition);
                if(false === $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置", 'id' => $id));
                }elseif(0 == $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "信息没有修改", 'id' => $id));
                }else{
                    if(6 == $status){
                        //del
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
                    }else{
                        //设置Jobs队列
                        $jobs = new Jobs();
                        $jobs->jobs_start($condition);
                        //
                        $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功", 'id' => $id));
                    }
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
    public function ajaxJobBuyDetailSearch(){
        $pagenum = I('page',1); // get the requested page
        $limitnum = I('rows',20); // get how many rows we want to have into the grid
        $sidx = I('sidx','id'); // get index row - i.e. user click to sort
        $sord = I('sord','desc'); // get the direction
        if($sidx == ""){
            $sidx = 'id';
        }

        $job_id = I('job_id');
        $job_id_result_key = "jobs:buy:result:" . $job_id;
        //
        $redis = $this->getRedisObj();
        $count = $redis->listSize($job_id_result_key);
        trace($count,"list size");
        $total_pages = 0;
        if( $count >0 ) {
            $total_pages = ceil($count/$limitnum);
        }
        $responce["page"] = $pagenum;
        $responce["total"] = $total_pages;
        $responce["records"] = $count;
        //
        $start = ($pagenum - 1) * $limitnum;
        $end = $pagenum * $limitnum -1;
        $list = $redis->listGet($job_id_result_key,$start,$end);
        //trace($list,"job_detail");
        //
        //"account":"5678","entrust_no":"","job_item_id":732,"sj_buy_num":0,"sj_buy_price:0,"yj_buy_num":95}
        $i=0;
        if(!empty($list)){
            foreach($list as $item_str){
                $item = json_decode($item_str,true);
                trace($item,"item");
                $responce["rows"][$i]['id']=$item['job_msg_id'];
                $responce["rows"][$i]['cell'] = array($item['job_msg_id'],
                    $item['account'],
                    $item['yj_buy_num'],
                    $item['sj_buy_num'],
                    $item['sj_buy_price'],
                );
                $i++;
            }
        }
        $this->ajaxReturn($responce);
    }
    public function ajaxCurInventorySearch(){
        $pagenum = I('page',1); // get the requested page
        $limitnum = I('rows',2000); // get how many rows we want to have into the grid
        $sidx = I('sidx','id'); // get index row - i.e. user click to sort
        $sord = I('sord','desc'); // get the direction
        if($sidx == ""){
            $sidx = 'id';
        }
        //
        $order_account = false;
        $sidx_array = explode(",",$sidx);
        if(is_array($sidx_array)){
            if(false === strstr($sidx_array[0],"account")){
            }else{
                $order_account = true;
            }
        }
        trace($order_account,"order_account");
        //
        $pro_id = I('pro_id');
        $pro_id_stocks_key = "cur:stocks:" . $pro_id;
        //
        $redis = $this->getRedisObj();
        $all_stocks = $redis->setMembers($pro_id_stocks_key);
        $i = 0;
        $responce_account = array();
        $account_arr = array();
        if(!empty($all_stocks)){
            foreach($all_stocks as $cur_stock){
                $pro_id_stocks_cur = "cur:stocks:" . $pro_id . ":" . $cur_stock;
                $cur_account_array = $redis->hashGet($pro_id_stocks_cur,"",2);
                if(!empty($cur_account_array)){
                    foreach($cur_account_array as $account => $num_price_str){
                        //
                        $account_arr[$account] = 1;
                        //
                        $num_arr = explode("|",$num_price_str);
                        $responce["rows"][$i]['id'] = $i + 1;
                        $responce["rows"][$i]['cell'] = array($i + 1,
                            $cur_stock,
                            trim($account),
                            intval($num_arr[0]),
                            floatval($num_arr[1]),
                            floatval($num_arr[1]/$num_arr[0]),
                        );
                        $i++;
                    }
                }
            }
        }
        //
        if($order_account){
            //
            trace($account_arr,"account_arr");
            //以 account order 排序
            $k=0;
            foreach($account_arr as $account => $val){
                for($j=0;$j<$i;$j++){
                    if($responce["rows"][$j]['cell'][2] == $account){
                        $responce_account["rows"][$k]['id'] = $k + 1;
                        $responce_account["rows"][$k]['cell'] = $responce["rows"][$j]['cell'];
                        $k++;
                    }
                }
            }
            $responce["rows"] = $responce_account["rows"];
        }
        //
        $responce["page"] = 1;
        $responce["total"] = 1;
        $responce["records"] = $i;
        //
        $this->ajaxReturn($responce);
    }
    public function ajaxGetCanSellStockCodeOnly(){
        //查询标志
        $pro_id = I('pro_id');
        $pro_id_stocks_key = "cur:stocks:" . $pro_id;
        //
        $redis = $this->getRedisObj();
        $all_stocks = $redis->setMembers($pro_id_stocks_key);

        $resp = array();
        if(!empty($all_stocks)){
            foreach($all_stocks as $cur_stock){
                $pro_id_stocks_cur = "cur:stocks:" . $pro_id . ":" . $cur_stock;
                $cur_account_array = $redis->hashGet($pro_id_stocks_cur,"",2);
                if(!empty($cur_account_array)){
                    $cur_num = 0;
                    $cur_price = 0.0;
                    foreach($cur_account_array as $account => $num_price_str){
                        $num_arr = explode("|",$num_price_str);
                        $cur_num += intval($num_arr[0]);
                        $cur_price += floatval($num_arr[1]);
                    }
                    $resp[] = array('stock_code' => $cur_stock, "num" => $cur_num,"price" => $cur_price);
                }
            }
        }

        $resp_str = "<select><option value='0'>请选择</option>";
        foreach($resp as $item){
            if(intval($item["num"]) > 0 ){
                $resp_str = $resp_str . "<option value='" . $item['stock_code'] . "'>" . $item['stock_code'] . "</option>";
            }
        }
        $resp_str = $resp_str . "</select>";
        //trace($resp_str,"ajaxGetCanSellStockCodeOnly");
        echo $resp_str;
    }
    public function ajaxGetCanSellStockCodeAndNum(){
        //查询标志
        $pro_id = I('pro_id');
        $pro_id_stocks_key = "cur:stocks:" . $pro_id;
        //
        $redis = $this->getRedisObj();
        $all_stocks = $redis->setMembers($pro_id_stocks_key);

        $resp = array();
        if(!empty($all_stocks)){
            foreach($all_stocks as $cur_stock){
                $pro_id_stocks_cur = "cur:stocks:" . $pro_id . ":" . $cur_stock;
                $cur_account_array = $redis->hashGet($pro_id_stocks_cur,"",2);
                if(!empty($cur_account_array)){
                    $cur_num = 0;
                    $cur_price = 0.0;
                    foreach($cur_account_array as $account => $num_price_str){
                        $num_arr = explode("|",$num_price_str);
                        $cur_num += intval($num_arr[0]);
                        $cur_price += floatval($num_arr[1]);
                    }
                    $resp[] = array('stock_code' => $cur_stock, "num" => $cur_num,"price" => $cur_price);
                }
            }
        }

        $this->ajaxReturn(array('num' => count($resp), 'content' => $resp));
    }
    public function ajaxJobSellSave(){
        $Data = M('project_jobs'); // 实例化Data数据模型
        //
        $oper = I('oper');
        $id = I('id');
        $pro_id = I('pro_id');
        //
        $user_id = I('user_id');
        $department = I('department');
        //
        $stock_code = I('stock_code');
        $exchange_type = I('exchange_type');
        $percent = I('percent');
        $cur_can_sell_num = I('cur_can_sell_num');
        $price = I('price');
        $yj_buy_je = I('yj_buy_je');
        $sj_buy_je = I('sj_buy_je');
        $status = I('status');
        $sm = I('sm');
        //
        $buyorsell = I('buyorsell');
        $buyorsell = "2";
        //
        trace($pro_id,"pro_id");
        //
        $condition = array();
        switch ($oper) {
            case "add"://
                if( empty($pro_id) || empty($stock_code) || empty($percent)
                    || empty($cur_can_sell_num)|| empty($price)){
                    $this->ajaxReturn(array('state' => false, 'msg' => "字段不能为空"));
                }
                $status = "1";
                //
                $condition["pro_id"] = $pro_id;
                //$condition["user_id"] = $user_id;
                //$condition["department"] = $department;
                $condition['stock_code'] = $stock_code;
                $condition['exchange_type'] = $exchange_type;
                $condition['percent'] = $percent;
                $condition['price'] = $price;
                $condition['cur_can_sell_num'] = $cur_can_sell_num;
                $condition['sm'] = $sm;
                $condition['buyorsell'] = $buyorsell;
                $condition['status'] = $status;

                $result  = $Data->add($condition);
                if(false === $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置"));
                }else{
                    $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功", 'id' => $result));
                }
                break;
            case "edit"://
                if(empty($id) || empty($pro_id) || empty($stock_code) || empty($percent)
                    || empty($cur_can_sell_num) || empty($price) || empty($status)){
                    $this->ajaxReturn(array('state' => false, 'msg' => "字段不能为空", 'id' => $id));
                }
                $condition["pro_id"] = $pro_id;
                //$condition["user_id"] = $user_id;
                //$condition["department"] = $department;
                $condition['stock_code'] = $stock_code;
                $condition['exchange_type'] = $exchange_type;
                $condition['percent'] = $percent;
                $condition['cur_can_sell_num'] = $cur_can_sell_num;
                $condition['price'] = $price;
                $condition['sm'] = $sm;
                $condition['buyorsell'] = $buyorsell;
                $condition['status'] = $status;

                $condition['id'] = $id;
                $result  = $Data->save($condition);
                if(false === $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "存盘失败,请检查数据库连接设置", 'id' => $id));
                }elseif(0 == $result){
                    $this->ajaxReturn(array('state' => false, 'msg' => "信息没有修改", 'id' => $id));
                }else{
                    if(6 == $status){
                        //del
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
                    }else{
                        //设置Jobs队列
                        $jobs = new Jobs();
                        $jobs->jobs_start($condition);
                        //
                        $this->ajaxReturn(array('state' => true, 'msg' => "存盘成功", 'id' => $id));
                    }
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
    public function ajaxJobSellSearch(){
        $pagenum = I('page',1); // get the requested page
        $limitnum = I('rows',20); // get how many rows we want to have into the grid
        $sidx = I('sidx','id'); // get index row - i.e. user click to sort
        $sord = I('sord','desc'); // get the direction
        if($sidx == ""){
            $sidx = 'id';
        }
        //查询标志
        $pro_id = I('pro_id');
        //
        $buyorsell = I('buyorsell');
        $buyorsell = "2";
        //多条件查询
        $cond = array();
        $param_arr = I('param.');
        foreach( $param_arr as $k=>$v) {
            switch ($k) {
                case 'sm':
                    $cond[$k] = array('LIKE', "%$v%");
                    break;
                case 'id':
                case 'pro_id':
                case 'stock_code':
                case 'exchange_type':
                case 'price':
                case 'status':
                    if("0" != $v && "all" != $v && "" != $v){
                        $cond[$k] = $v;
                    }
                    break;
            }
        }
        //
        $cond['buyorsell'] = $buyorsell;
        //
        $ProJobs = M("project_jobs");
        $count = $ProJobs->where($cond)->order(array($sidx => $sord))->count();// 查询满足要求的总记录数
        $list =  $ProJobs->where($cond)->order(array($sidx => $sord))->page($pagenum,$limitnum)->select();
        $total_pages = 0;
        if( $count >0 ) {
            $total_pages = ceil($count/$limitnum);
        }
        $responce["page"] = $pagenum;
        $responce["total"] = $total_pages;
        $responce["records"] = $count;
        //
        $i=0;
        if(!empty($list)){
            $market_array = USER_FUN_GET_STOCK_MARKET_NAME();
            $status_array = USER_FUN_GET_PROJECT_JOB_STATUS_NAME();
            foreach($list as $item){
                //实时计算实际成交金额和数量
                $job_id = $item["id"];
                $calc_array = $this->get_sj_total_sell_num_price($job_id);
                //
                $responce["rows"][$i]['id']=$item["id"];
                $responce["rows"][$i]['cell'] = array($item['id'],
                    $item['stock_code'],
                    $market_array[$item['exchange_type']],
                    $item['price'],
                    $item['cur_can_sell_num'],
                    $item['percent'],
                    intval(floatval($item['percent']) * floatval($item['cur_can_sell_num']) /100),
                    floatval($calc_array['total_money']),
                    intval($calc_array['total_num']),
                    $status_array[$item['status']],
                    $item['job_ks_time'],
                    $item['job_js_time'],
                );
                $i++;
            }
        }
        $this->ajaxReturn($responce);
    }
}
