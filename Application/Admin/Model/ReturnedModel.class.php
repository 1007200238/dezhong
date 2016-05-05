<?php

namespace Admin\Model;
use Think\Model;

/**
 * 退货单据模型
 * @author echoshiki <echoshiki@outlook.com>
 * 2015/8/28
 */

class ReturnedModel extends Model {

    protected $_validate = array(
    	array('no', '', '该退货编号已经存在', self::EXISTS_VALIDATE, 'unique'), 
        array('no', 'require', '编号不能为空', self::MUST_VALIDATE),
    );


    /**    
     * 获取合并后的退货单详细情况（总表副表）
     * @author echoshiki <echoshiki@outlook.com>
     * @param id int 退货单id
     * @param type int 1:采购退货 2:销售退货 3:自由退货
     * 2015/08/28
     */
    public function show($id) {
        $map['id'] = $id;
        $main = $this->where($map)->find();
        $type = $main['from'];
        $tmp = unserialize($main['exstock']);
        foreach ($tmp as $key => $value) {
            $exinfo = D('exstock')->find($key);
            $exinfo['num_in'] = $value;
            $main['exinfo'][] = $exinfo;
        }
        $pid  = $main['pid'];
        if ($type == '2') {
            // 销售退货 pid为客户id
            $main['pinfo'] = D('ucenter_member')->field('id,username,email,mobile,company,truename,address')->find($pid);
        } else {
            // 采购退货 pid为供应商id
            $main['pinfo'] = D('supplier')->field('id,company,address,name,consigner,mobile,fax')->find($pid);
        }
        $main['info'] = D('returned_info')->lists($id, $type);
        return $main;
    }

}