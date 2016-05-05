<?php

namespace Admin\Model;
use Think\Model;

/**
 * 采购合同单副表模型
 * @author echoshiki <echoshiki@outlook.com>
 * 2015/7/20
 */

class ContractInfoModel extends Model {

    /**    
     * 获取格式化后的计划单详情
     * @author echoshiki <echoshiki@outlook.com>
     * @param fid int 合同单id
     * 2015/07/16
     */
    public function lists($fid) {
    	$Stock = D("stock");
        $map['fid'] = $fid;
        $list = $this->where($map)->select();

        foreach ($list as $key => $value) {
        	$info_ex = $Stock->info($value['sid']);
        	$list[$key]['standard'] = $info_ex['standard'];
        	$list[$key]['OE']   = $info_ex['OE'];
        	$list[$key]['code'] = $info_ex['code'];
        	$list[$key]['name'] = $info_ex['name'];
        	$list[$key]['unit'] = $info_ex['unit'];
        	$list[$key]['note'] = $value['note'] ? $value['note'] : "暂无";
            $list[$key]['purch_price'] = $info_ex['purch_price'];
            $list[$key]['purch_price_off'] = $info_ex['purch_price_off'];
            $list[$key]['thumb_1'] = $info_ex['thumb_1'];
            $list[$key]['supplier_no'] = $info_ex['supplier_no'];
            $list[$key]['supplier'] = $info_ex['supplierid'];
            $list[$key]['id'] = $info_ex['id'];

            $list[$key]['bag_size'] = $info_ex['bag_size'];
            $list[$key]['box_size'] = $info_ex['box_size'];
            $list[$key]['store_no'] = $info_ex['store_no'];
        }
		return $list;
    }

}
