<?php

namespace Admin\Model;
use Think\Model;

/**
 * 申购计划单模型
 * @author echoshiki <echoshiki@outlook.com>
 * 2015/7/15
 */

class ApplyModel extends Model {

    protected $_validate = array(
    	array('department', 'require', '申购部门必须填写', self::EXISTS_VALIDATE), 
    	array('department','2,20','申购部门长度为4-20个字符', self::EXISTS_VALIDATE, 'length'),
    	array('no','3,30','申购单编号长度为3-30个字符', self::EXISTS_VALIDATE, 'length'),
        array('no', '', '该申购单已经存在', self::EXISTS_VALIDATE, 'unique'), 
    );


    /**    
     * 获取合并后的计划单详细情况（总表副表）
     * @author echoshiki <echoshiki@outlook.com>
     * @param id int 计划单id
     * 2015/07/16
     */
    public function show($id) {
 		$Info = D('apply_info');
    	$map['id'] = $id;
    	$main = $this->where($map)->find();
    	$main['info'] = $Info->lists($id);
    	return $main;
    }


    /**    
     * 删除计划单（总表副表）
     * @author echoshiki <echoshiki@outlook.com>
     * @param array 条件
     * 2015/07/16
     */
    public function del($map) {
 		$Info = D('apply_info');
    	$this->where($map)->delete();
    	$map_ex['fid'] = $map['id'];
    	$Info->where($map_ex)->delete();
    	return true;
    }

}