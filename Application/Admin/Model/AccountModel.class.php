<?php

namespace Admin\Model;
use Think\Model;

/**
 * 采购账务模型
 * @author echoshiki <echoshiki@outlook.com>
 * 2015/7/15
 */

class AccountModel extends Model {

    protected $_validate = array(
    	array('supplier_id', 'require', '请勾选供应商', self::EXISTS_VALIDATE), 
    	array('date', 'require', '请选择明细日期', self::EXISTS_VALIDATE), 
    	array('debit', 'require', '请填写借方款项', self::EXISTS_VALIDATE),
    	array('credit', 'require', '请填写贷方款项', self::EXISTS_VALIDATE),
    	array('debit', 'number', '借方款项必须填写数字', self::EXISTS_VALIDATE), 
    	array('credit', 'number', '贷方款项必须填写数字', self::EXISTS_VALIDATE), 
    );

}