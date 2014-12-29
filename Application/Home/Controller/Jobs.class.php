<?php
/**
 * Created by PhpStorm.
 * User: chenguang2
 * Date: 14-11-4
 * Time: 下午2:50
 */

namespace Home\Controller;
use Think\Controller;
use Home\Model\RedisModel;
class Jobs {
    public function jobs_start($condition){
        $pro_id = $condition['pro_id'];
        $job_id = $condition['id'];
        $buyorsell = $condition['buyorsell'];
        $stock_code = $condition['stock_code'];
        //
        $redis = new RedisModel();
        $old_status = $redis->hashGet("jobs:status",$job_id,1);
        trace($old_status,"old_status");
        //
        $status = intval($condition['status']);
        if($status == 2 || $status == 3  || $status == 4  || $status == 5 ){
            $return = $redis->hashSet("jobs:status",array($job_id => $status ));
        }
        //
        //if(empty($old_status)){
            if($status == 2 || $status == 3  || $status == 4 ){
                if("1" == $buyorsell){
                    //查询 pro_id  的 各个账号的初始金额的总和,包括目前已经录入没有锁定的 zj_lock=1 状态的
                    $list = M()->query("select account_id,pro_account_csje from at_project_account where pro_id='" . $pro_id . "'");
                    $pro_account_csje = array();
                    if(!empty($list)){
                        foreach($list as $cur){
                            $pro_account_csje[$cur["account_id"]] = $cur["pro_account_csje"];
                        }
                    }
                    trace($pro_account_csje,"pro_account_csje");
                    $condition['pro_account_csje'] = $pro_account_csje;
                    $condition['createtime'] = date("YmdHis");
                }else if("2" == $buyorsell){
                    $pro_id_stocks_cur = "cur:stocks:" . $pro_id . ":" . $stock_code;
                    $cur_account_array = $redis->hashGet($pro_id_stocks_cur,"",2);
                    $pro_account_cur_num = array();
                    if(!empty($cur_account_array)){
                        foreach($cur_account_array as $account => $num_price_str){
                            //
                            $num_arr = explode("|",$num_price_str);
                            $pro_account_cur_num[trim($account)] = $num_arr[0];
                        }
                    }
                    trace($pro_account_cur_num,"pro_account_cur_num");
                    $condition['pro_account_cur_num'] = $pro_account_cur_num;
                    $condition['createtime'] = date("YmdHis");
                }

                //new job: push to beanstalk
                $pheanstalk = new \Pheanstalk\Pheanstalk(C('BEANSTALK_HOST'));
                // ----------------------------------------
                // producer (queues jobs)
                $pheanstalk
                    ->useTube('jobs')
                    ->put(json_encode($condition));
                //----------------------------------------
            }
        //}
        return $return;
    }
} 