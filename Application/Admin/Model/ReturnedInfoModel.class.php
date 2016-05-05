<?php

namespace Admin\Model;
use Think\Model;

/**
 * 退货单据详情模型
 * @author echoshiki <echoshiki@outlook.com>
 * 2015/8/28
 */

class ReturnedInfoModel extends Model {

    /**    
     * 获取格式化后的退货单详情
     * @author echoshiki <echoshiki@outlook.com>
     * @param fid int 退货单id
     * @param type int 退货类型: 1.采购退货/ 2.销售退货
     * 2015/08/28
     */
    public function lists($fid, $type = '1') {
    	$Stock = D("stock");
        $map['fid'] = $fid;
        $list = $this->where($map)->select();

        foreach ($list as $key => $value) {
        	$info_ex = $Stock->info($value['sid']);
        	$list[$key]['OE']        = $info_ex['OE'];
        	$list[$key]['code']      = $info_ex['code'];
        	$list[$key]['name']      = $info_ex['name'];
        	$list[$key]['unit']      = $info_ex['unit'];
        	$list[$key]['standard']  = $info_ex['standard'];
            $list[$key]['id']        = $info_ex['id'];
            $list[$key]['price']     = $info_ex['purch_price'];
            $list[$key]['price_off'] = $info_ex['purch_price_off'];
            if ($type == 2) {
                //销售退货，表单里价格选用销售单价
                $list[$key]['price']     = $info_ex['sale_price'];
                $list[$key]['price_off'] = $info_ex['sale_price_off'];
            }
        }
		return $list;
    }

}