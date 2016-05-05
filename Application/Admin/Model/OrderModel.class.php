<?php

namespace Admin\Model;
use Think\Model;

/**
 * 订单模型
 * @author echoshiki 2015/4/14
 */

class OrderModel extends Model {

    protected $_validate = array(
        array('name', '1,10', '联系人姓名不能过长或者过短。', 0, 'length'),
        array('tel', '11', '请输入11位的手机号码。', 0, 'length'),
        array('address', '1,150', '输入的地址太长，请缩减后在提交。', 0, 'length'),
        array('note', '1,150', '输入的备注太长，请缩减后在提交。', 2, 'length' ),
    );

 
    /**    
     * 获取格式化后的订单详细 -- 用户出库单，未出库状态
     * @author echoshiki 2015/4/01 
     */
    public function info($id) {
        $info  			  = $this->find($id);
        $info['goods']    = unserialize($info['goods']);
        foreach ($info['goods'] as $key => $value) {
        	$info['goods'][$key] = D('Stock')->info($key);   //订单商品基础信息
        	$info['goods'][$key]['num'] = $value;   //订购数量
            $tmp3 = D('warehouse')->where(array('sid'=>$key,'warehouse'=>'1'))->field('num')->find();
            $info['goods'][$key]['last'] = $tmp3['num'] ? $tmp3['num'] : 0;   //库存数量，用于生成出库单 out_do
            $info['goods'][$key]['total_price'] = $info['goods'][$key]['sale_price'] * $value;
        }

        // 订购人信息 订单未标注则使用账号预留信息 2015/08/31
        $pinfo = D('ucenter_member')->field('id,truename,company,mobile,username,address')->find($info['uid']);
        $info['username'] = $pinfo['username'];
        $info['company']  = $info['company'] ? $info['company'] : $pinfo['company'];
        $info['name'] = $info['name'] ? $info['name'] : $pinfo['truename'];
        $info['tel']  = $info['tel'] ? $info['tel'] : $pinfo['mobile'];
        $info['address'] = $info['address'] ? $info['address'] : $pinfo['address'];

        return $info;
    }


    /**    
     * 获取格式化后的订单详细 -- 用于退货单，出库状态
     * @author echoshiki 2015/8/31 
     */
    public function show($id) {
        $info             = $this->find($id);
        $info['date']     = date("Y-m-d H:i",$info['date']);
        $info['status']   = C('ORDER.'.$info['status']);

        // 获取和该订单相对应的出库单id，以便求得该订单商品的实际出库数  2015/08/31
        $tmp1 = D('out')->where(array('from_no'=>$info['order_id']))->field('id')->find();
        $map['fid'] = $tmp1['id'];

        $info['goods']    = unserialize($info['goods']);
        foreach ($info['goods'] as $key => $value) {
            $info['goods'][$key] = D('Stock')->info($key);   //订单商品基础信息
            $info['goods'][$key]['num'] = $value;   //订购数量

            $map['sid'] = $key; 
            $tmp2 = D('out_info')->where($map)->field('num')->find();  //以商品id和出库单id取得实际出库数     
            $info['goods'][$key]['out_num'] = $tmp2['num'] ? $tmp2['num'] : 0;  //获取订单商品的实际出库数
        }

        // 订购人信息 订单未标注则使用账号预留信息 2015/08/31
        $pinfo = D('ucenter_member')->field('id,truename,company,mobile,username,address')->find($info['uid']);
        $info['username'] = $pinfo['username'];
        $info['company']  = $info['company'] ? $info['company'] : $pinfo['company'];
        $info['name'] = $info['name'] ? $info['name'] : $pinfo['truename'];
        $info['tel']  = $info['tel'] ? $info['tel'] : $pinfo['mobile'];
        $info['address'] = $info['address'] ? $info['address'] : $pinfo['address'];

        return $info;
    }



    /**    
     * 删除订购单（总表、流程表）
     * @author echoshiki <echoshiki@outlook.com>
     * @param array 条件
     * 2015/08/26
     */
    public function del($map) {
        $Info = D('order_process');
        $this->where($map)->delete();
        $map_ex['oid'] = $map['id'];
        $Info->where($map_ex)->delete();
        return true;
    }  



    /**    
     * 返回自动生成的订单编号
     * @author echoshiki <echoshiki@outlook.com>
     * @return string 编号
     * 2015/10/12
     */ 
    public function getno() {
        $num  = 1;
        $year = date('Y',time());
        $thisMonth = date('m',time());
        $nextMonth = $thisMonth + 1;
        $time1 = mktime(0,0,0,$thisMonth,1,$year);  //取得本月1日时间戳
        $time2 = mktime(0,0,0,$nextMonth,1,$year);  //取得下月1日时间戳
        $map['date'] = array('between',array($time1,$time2));
        $num += $this->where($map)->count();  //计算月间合同数
        if (strlen($num) == 1) { $num = '0'.$num; }
        $no = $year.'-'.$thisMonth.'-'.$num;
        return $no;
    }



    /**    
     * 返回指定订单总额
     * @author echoshiki <echoshiki@outlook.com>
     * @return string 编号
     * 2015/10/12
     */ 
    public function getMoney($id) {
        $info             = $this->find($id);
        $info['goods']    = unserialize($info['goods']);
        foreach ($info['goods'] as $key => $value) {
            $info['goods'][$key] = D('Stock')->info($key);   //订单商品基础信息
            $info['goods'][$key]['num'] = $value;   //订购数量
            $info['money'] += $info['goods'][$key]['sale_price_off'] * $value;
        }
        return $info['money'];
    }    

}