<?php

namespace Admin\Model;
use Think\Model;

/**
 * 订单流程模型
 * @author echoshiki 2015/4/14
 */

class OrderProcessModel extends Model {
 
    protected $_validate = array(
        array('process', '1,20', '订单流程不能过长或者过短。', 0, 'length'),
    );

    public function lists($oid){
        $Member  = D('Member');
        $list = $this->where('oid='.$oid)->order('id')->select();
        foreach ($list as $key => $value) {
            $list[$key]['date']     = date("Y-m-d",$value['date']);
            $list[$key]['username'] = $Member->getNickName($value['userid']); 
        }
        return $list;
    }

}