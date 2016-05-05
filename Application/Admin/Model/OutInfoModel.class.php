<?php

namespace Admin\Model;
use Think\Model;

/**
 * 出库单据详情模型
 * @author echoshiki <echoshiki@outlook.com>
 * 2015/8/21
 */

class OutInfoModel extends Model {

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
        	$list[$key]['standard'] = $info_ex['standard'];
            $list[$key]['sale_price'] = $info_ex['sale_price'];
            $list[$key]['sale_price_off'] = $info_ex['sale_price_off'];
            $list[$key]['id']   = $info_ex['id'];
        }
		return $list;
    }

}