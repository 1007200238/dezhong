<?php

namespace Admin\Model;
use Think\Model;

/**
 * 供应商模型
 * @author echoshiki 2015/09/08
 */

class ExstockRecordModel extends Model {

    protected $_validate = array(   
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

}