<?php

namespace Admin\Model;
use Think\Model;

/**
 * 入库单据详情模型
 * @author echoshiki <echoshiki@outlook.com>
 * 2015/7/31
 */

class InInfoModel extends Model {

    /**    
     * 获取格式化后的入库单详情
     * @author echoshiki <echoshiki@outlook.com>
     * @param fid int 入库单id
     * 2015/08/06
     */
    public function lists($fid) {
    	$Stock = D("stock");
        $map['fid'] = $fid;
        $list = $this->where($map)->select();

        foreach ($list as $key => $value) {
        	$info_ex = $Stock->info($value['sid']);
        	$list[$key]['OE']   = $info_ex['OE'];
        	$list[$key]['code'] = $info_ex['code'];
        	$list[$key]['name'] = $info_ex['name'];
        	$list[$key]['unit'] = $info_ex['unit'];
            $list[$key]['purch_price'] = $info_ex['purch_price'];
        	$list[$key]['standard'] = $info_ex['standard'];
            $list[$key]['id']   = $info_ex['id'];
        }
		return $list;
    }

}