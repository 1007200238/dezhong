<?php

namespace Admin\Model;
use Think\Model;

/**
 * 供应商模型
 * @author echoshiki 2015/09/08
 */

class ExstockModel extends Model {

    protected $_validate = array(   
        array('name', '2,50', '包装品名称长度为3-30个字符', self::EXISTS_VALIDATE, 'length'),
        array('name', '', '包装品名称已经被占用', self::EXISTS_VALIDATE, 'unique',1),
        array('type', 'require', '必须选择商品属性', self::EXISTS_VALIDATE),
        array('num', 'number', '初始库存必须填写数字', self::EXISTS_VALIDATE),
        array('price', 'number', '成本价格必须填写数字', self::EXISTS_VALIDATE), 

    );


    /**    
     * 获取包装品列表
     * @author echoshiki 2015/09/08 
     */
    public function lists($where = "status = 1", $order = 'id DESC', $field = true, $page = "20") {
        // 调用分页类进行数据分页
        $count = $this->field('id')->where($where)->order($order)->count();
        $Page  = new \Think\Page($count,$page); // 实例化分页类 传入总记录数和每页显示的记录数(10)
        $show  = $Page->show();// 分页显示输出
        $list  = $this->where($where)->field($field)->limit($Page->firstRow.','.$Page->listRows)->order($order)->select();
        $list['show'] = $show;
        return $list;
    }


    /**    
     * 包装品入库
     * 更新库存量、生成操作记录
     * @author echoshiki 2015/09/08 
     */
    public function push($id, $num, $type='1', $from_no='') {
       $map['id'] = $id;
       $rec = $this->where($map)->setInc('num',$num);  // 包装品表相应包装品库存累加
       if (!$rec) { return false; }

       $info = $this->find($id);
       $data['name'] = $info['name'];
       $data['num']  = $num;
       $data['type'] = $type; // 入库 or 退货
       $data['date'] = time();
       $data['user'] = session('user_auth.uid');  //操作员
       $data['from_no'] = $from_no;
       $rec = D('exstock_record')->add($data);
       if (!$rec) { return false; }
       return true;
    }


    /**    
     * 包装品出库
     * 更新库存量、生成操作记录
     * @author echoshiki 2015/09/08 
     */
    public function pull($id, $num, $type='2', $from_no='') {

       $map['id'] = $id;
       $rec = $this->where($map)->setDec('num',$num);  // 包装品表相应包装品库存递减
       if (!$rec) { return false; }

       $info = $this->find($id);
       $data['name'] = $info['name'];
       $data['num']  = $num;
       $data['type'] = $type; // 出库
       $data['date'] = time();
       $data['user'] = session('user_auth.uid');  //操作员
       $data['from_no'] = $from_no;  // 关联id，例如退货单，出库单等
       $rec = D('exstock_record')->add($data);
       if (!$rec) { return false; }
       return true;
    }


    /**    
     * 格式化固定属性包装品，组合数组
     * Array(id=>num);
     * @author echoshiki 2015/09/08 
     */
    public function compare($num) {
       $map['type'] = 1;
       $list = $this->field('id')->where($map)->select();  // 固定属性包装品
       foreach ($list as $key => $value) {
          $new[$value['id']] = $num;
       }
       return $new;
    }


}