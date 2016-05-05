<?php

namespace Admin\Model;
use Think\Model;

/**
 * 仓库模型
 * @author echoshiki 2015/3/27
 */

class WarehouseModel extends Model {

    protected $_validate = array(
        array('min_num', 'number', '预警值必须填写数字', self::EXISTS_VALIDATE), 
        array('max_num', 'number', '预警值必须填写数字', self::EXISTS_VALIDATE), 
    );


    /**    
     * 产品入库 
     * 判断插入操作或更新操作
     * @param 产品编码、仓库归属（默认为主库1）、产品数量、采购合同/销售单id、出库类型（1采购入库、4销售退货、5客户入库
     * @author echoshiki 2015/4/02 
     */
    public function push($code, $warehouse = 1, $num = 1, $fromid = "", $type = "1"){
        $Stock = D('Stock'); 
        $info  = $Stock->where("code = '".$code."'")->find();
        $data['sid']        = $info['id'];  //产品id标识
        $data['warehouse']  = $warehouse;   //仓库归属，以userid为区分
        $data['status']     = 1;            //库存状态
        $data['date']       = time();       //最近入库时间
        $ishere = $this->isExist($data['sid'],$data['warehouse']);  //判断操作 插入OR更新
        if (!$ishere) {
            //插入数据
            $data['num']  = $num;
            $res = $this->add($data);
        } else { 
            //更新数据
            $map['sid']       = $data['sid'];
            $map['warehouse'] = $warehouse;
            $res = $this->where($map)->setInc('num',$num); // 刷量累加
        }
        if (!$res) { return false; }

        //插入入库记录
        $data['supplier']   = $info['supplier'];  //供应商
        $data['type']       = $type;  //默认采购入库
        $data['num']        = $num;
        $data['userid']     = session('user_auth.uid');  //操作员
        $data['price']      = ($warehouse==1) ? $info['purch_price'] : $info['sale_price'] ;  //入库时的价格

        $data['name']       = $info['name'];
        $data['code']       = $info['code'];
        $data['category']   = $info['category'];

        $data['from_id']    = $fromid ? $fromid : "";   //后来添加，采购合同/销售订单关联id 2015/8/5

        unset($data['status']);

        $Record = M('Record');
        $res    = $Record->add($data);
        if (!$res) { return false; }

        return true;
    }


    /**    
     * 产品出库 
     * @param 出库数据、仓库归属（默认为主库1）、产品数量、关联合同/订单、出库类型（2销售出库、3采购退货、6客户出库
     * @author echoshiki 2015/4/09 
     */
    public function pull($code, $warehouse = 1, $num = 1, $fromid ="", $type = "2"){
        $info  = D('Stock')->where("code = '".$code."'")->find();
        $data['sid']        = $info['id'];  //产品id标识
        $data['warehouse']  = $warehouse;   //仓库归属，以userid为区分
        $data['status']     = 1;            //库存状态
        $data['date']       = time();       //最近入库时间
        $ishere = $this->isExist($data['sid'],$data['warehouse']);  //判断操作 插入OR更新
        if (!$ishere) {
            // 库存内不存在出库的产品
            return false;
        } else { 
            //更新数据
            $map['sid']       = $data['sid'];
            $map['warehouse'] = $warehouse;
            $res = $this->where($map)->setDec('num',$num); // 刷量删减
        }
        if (!$res) { return false; }

        //插入出库记录
        $data['supplier']   = $info['supplier'];  //供应商
        $data['type']       = $type;  
        $data['num']        = $num;
        $data['userid']     = session('user_auth.uid');  //操作员
        $data['price']      = $info['sale_price'];  //出库时的销售价格
        $data['name']       = $info['name'];
        $data['code']       = $info['code'];
        $data['category']   = $info['category'];

        $data['from_id']    = $fromid ? $fromid : "";   //后来添加，采购合同/销售订单关联id 2015/8/5

        unset($data['status']);

        $Record = M('Record');
        $res    = $Record->add($data);
        if (!$res) { return false; }

        return true;     
    }


    /**    
     * 特殊出库  废弃
     * @param 出库数据、仓库归属（默认为主库1）、产品数量
     * @author echoshiki 2015/4/09 
     */
    public function out($outinfo, $warehouse = 1, $num = 1){
        $Stock = D('Stock'); 
        $code  = $outinfo['code'];
        $info  = $Stock->where("code = '".$code."'")->find();
        $data['sid']       = $info['id'];  //产品id标识
        $data['warehouse'] = $warehouse;   //仓库归属，以userid为区分
        $data['num']       = $num;  //数量

        $map['sid']       = $data['sid'];
        $map['warehouse'] = $warehouse;
        $res = $this->where($map)->setDec('num',$num); // 余量减少
        if (!$res) { return false; }

        //插入出库记录
        $data['supplier']   = $info['supplier'];  //供应商
        $data['type']       = 2;  //出库
        $data['num']        = $num;
        $data['userid']     = session('user_auth.uid');  //操作员
               
        $data['name']       = $info['name'];
        $data['code']       = $info['code'];
        $data['category']   = $info['category'];

        $data['price']      = $outinfo['price'] ? $outinfo['price'] : $info['sale_price'];  //出库时的价格
        $data['custom']     = $outinfo['custom'] ? $outinfo['custom'] : "";
        $data['joiner']     = $outinfo['joiner'] ? $outinfo['joiner'] : "";
        $data['date']       = time();

        unset($data['status']);
        unset($data['sid']);

        $Record = M('Record');
        $res    = $Record->add($data);
        if (!$res) { return false; }

        return true;        
    }


    /**    
     * 验证产品是否已经存在于库存中
     * @author echoshiki 2015/4/02 
     */
    public function isExist($sid, $warehouse){
        $map['sid']       = $sid;
        $map['warehouse'] = $warehouse;
        $res = $this->where($map)->field('id')->find();
        if ( !$res ) { return false; }
        return true;
    }


    /**    
     * 根据用户id调出相对应的仓库id
     * 注：管理员组（1）的仓库id均为1，加盟商以userid为仓库id
     * @author echoshiki 2015/4/09 
     */
    public function wareid($uid){
        $Group     = D('AuthGroup');
        $groupInfo = $Group->getUserGroup($uid);  //获取用户组
        $wareid    = $groupInfo[0]['group_id'] == C('JOIN_GROUP') ? $uid : 1; //获取仓库id
        return $wareid;
    }


    /**    
     * 列出库存数据
     * @author echoshiki 2015/4/07 
     * edit: 2015/8/07
     */
    public function lists($map="",$order="id desc",$page="15"){
        $Category = D("Category");

        // 调用分页类进行数据分页
        $count = $this->field('id')->where($map)->order($order)->count();
        $Page  = new \Think\Page($count,$page); // 实例化分页类 传入总记录数和每页显示的记录数(10)
        $show  = $Page->show();  // 分页显示输出

        $list = $this->where($map)->limit($Page->firstRow.','.$Page->listRows)->order($order)->select();
        foreach ($list as $key => $value) {
            $info = array();
            $where['id'] = $value['sid'];
            $info = D("stock")->field('code,name,category,standard,unit,OE,store_no')->where($where)->find();
            $info['category'] = $Category->getName($info['category']);   // 格式化获取分类名称
            $standard = unserialize($info['standard']);
            foreach ($standard as $k => $val) { $standardArr[] = $Category->getName($val); }
            $info['standard'] = implode($standardArr, " ");
            unset($standardArr);
            $list[$key]['warehouse'] = $value['warehouse']==1 ? "本库" : D("Member")->getNickName($value['warehouse']);

            $list[$key] = array_merge($list[$key],$info);  
        }
        $list['show'] = $show;  //分页
        return $list;
    }

}