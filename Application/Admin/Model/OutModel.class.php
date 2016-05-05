<?php

namespace Admin\Model;
use Think\Model;

/**
 * 出库单据模型
 * @author echoshiki <echoshiki@outlook.com>
 * 2015/8/21
 */

class OutModel extends Model {

    protected $_validate = array(
    	array('no', '', '该出库编号已经存在', self::EXISTS_VALIDATE, 'unique'), 
    );


    /**    
     * 获取合并后的出库单详细情况（总表副表）
     * @author echoshiki <echoshiki@outlook.com>
     * @param id int 出库单id
     * 2015/08/21
     */
    public function show($id) {
        $map['id'] = $id;
        $main = $this->where($map)->find();
        $tmp = unserialize($main['exstock']);
        foreach ($tmp as $key => $value) {
            $exinfo = D('exstock')->find($key);
            $exinfo['num_in'] = $value;
            $main['exinfo'][] = $exinfo;
        }

        $main['info'] = D('out_info')->lists($id);
        return $main;
    }


    /**    
     * 删除出库单（总表副表）
     * @author echoshiki <echoshiki@outlook.com>
     * @param array 条件
     * 2015/08/26
     */
    public function del($map) {
        $Info = D('out_info');
        $this->where($map)->delete();
        $map_ex['fid'] = $map['id'];
        $Info->where($map_ex)->delete();
        return true;
    }   

}