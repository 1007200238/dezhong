<?php

namespace Admin\Model;
use Think\Model;

/**
 * 供应商模型
 * @author echoshiki 2015/3/27
 */

class SupplierModel extends Model {

    protected $_validate = array(
        array('no', '2,30', '供应商编码长度为2-30个字符', self::EXISTS_VALIDATE, 'length'),
        array('no', '', '供应商编码被占用', self::EXISTS_VALIDATE, 'unique',1), //编码已经存在
        array('company', '2,20', '供应商名称长度为2-20个字符', self::EXISTS_VALIDATE, 'length'),
        array('company', '', '供应商名称被占用', self::EXISTS_VALIDATE, 'unique',1), //名称被占用
        // array('name', '2,10', '联系人名称长度为2-10个字符', self::EXISTS_VALIDATE, 'length'),
        // array('address', '2,40', '供应商地址长度为2-40个字符', self::EXISTS_VALIDATE, 'length'),
        // array('note', '2,40', '备注名称长度为2-40个字符', 2, 'length'),
        // array('mobile', 'number', '联系电话必须为数字', self::EXISTS_VALIDATE), 
    );

    public function lists($map = "status = 1", $order = 'id DESC', $field = true, $page = "15"){
        // 调用分页类进行数据分页
        $count = $this->field('id')->where($map)->order($order)->count();
        $Page  = new \Think\Page($count,$page); // 实例化分页类 传入总记录数和每页显示的记录数(10)
        $show  = $Page->show();// 分页显示输出

        $list = $this->field($field)->where($map)->limit($Page->firstRow.','.$Page->listRows)->order($order)->select(); 
        $list['show'] = $show;

        return $list;
    }

    public function getName($id){
        $info = $this->field('company')->where('id="'.$id.'"')->find();
        return $info['company'];
    }

}