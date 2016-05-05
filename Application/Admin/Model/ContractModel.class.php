<?php

namespace Admin\Model;
use Think\Model;

/**
 * 采购合同单模型
 * @author echoshiki <echoshiki@outlook.com>
 * 2015/7/20
 */

class ContractModel extends Model {

    /**    
     * 获取提交合同的编号
     * @author echoshiki <echoshiki@outlook.com>
     * @return string 编号
     * 2015/07/21
     */	
    public function getno() {
    	$num  = 1;
    	$year = date('Y',time());
    	$thisMonth = date('m',time());
    	$nextMonth = $thisMonth + 1;
    	$time1 = mktime(0,0,0,$thisMonth,1,$year);  //取得本月1日时间戳
    	$time2 = mktime(0,0,0,$nextMonth,1,$year);  //取得下月1日时间戳
    	$map['pch_date'] = array('between',array($time1,$time2));
    	$num += $this->where($map)->count();  //计算月间合同数
    	if (strlen($num) == 1) { $num = '0'.$num; }
    	$no = 'DZ'.$year.'-'.$thisMonth.'-'.$num;
    	return $no;
    }


    /**    
     * 获取合并后的合同单详细情况（总表副表）
     * @author echoshiki <echoshiki@outlook.com>
     * @param id int 合同单id
     * 2015/07/23
     */
    public function show($id) {
        $map['id'] = $id;
        $main = $this->where($map)->find();
        $main['supplier'] = D('supplier')->where('id='.$main['supplier'])->find();
        $main['info'] = D('contract_info')->lists($id);
        return $main;
    }


    /**    
     * 删除合同单（总表副表）
     * @author echoshiki <echoshiki@outlook.com>
     * @param array 条件
     * 2015/07/23
     */
    public function del($map) {
        $Info = D('contract_info');
        $this->where($map)->delete();
        $map_ex['fid'] = $map['id'];
        $Info->where($map_ex)->delete();
        return true;
    }    



}