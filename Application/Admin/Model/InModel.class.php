<?php

namespace Admin\Model;
use Think\Model;

/**
 * 入库单据模型
 * @author echoshiki <echoshiki@outlook.com>
 * 2015/7/31
 */

class InModel extends Model {

    protected $_validate = array(
    	array('no', '', '该入库编号已经存在', self::EXISTS_VALIDATE, 'unique'), 
        array('no', 'require', '编号不能为空', self::MUST_VALIDATE),
    );


    /**    
     * 获取合并后的入库单详细情况（总表副表）
     * @author echoshiki <echoshiki@outlook.com>
     * @param id int 入库单id
     * 2015/08/06
     */
    public function show($id) {
        $map['id'] = $id;
        $main = $this->where($map)->find();
        $main['supplier'] = D('supplier')->where('id='.$main['supplier'])->field('id,company')->find();

        $main['info'] = D('in_info')->lists($id);
        return $main;
    }

}