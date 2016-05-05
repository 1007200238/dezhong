<?php

namespace Admin\Model;
use Think\Model;

/**
 * 记录模型
 * @author echoshiki 2015/3/27
 */

class RecordModel extends Model {

    protected $_validate = array(
        array('code', '15', '产品编码长度为15个字符', self::EXISTS_VALIDATE, 'length'),
        array('code', 'isCreate', '库存中尚未有该商品的资料，请录入后在进行入库处理。', 0, 'callback'),
        array('code', 'isLast', '库存中并未包含该产品或者库存不足。', 0, 'callback',2),
    );

    /**    
     * 验证产品是否已经录入进产品库
     * @author echoshiki 2015/4/02 
     */
    public function isCreate(){
        $code  = I('post.code');
        $Stock = D('Stock');
        return $Stock->isCreate($code);
    }

    /**    
     * 验证产品的存量
     * @author echoshiki 2015/4/09 
     */
    public function isLast(){
    	$Ware = D('Warehouse');
        $map['code']      = I('post.code');
        $num              = I('post.num') ? I('post.num') : 1;  //出库数量，默认为1
        $map['warehouse'] = $Ware->wareid(session('user_auth.uid'));  //获取仓库id

        $info  = $Ware->join('app_stock ON app_warehouse.sid = app_stock.id')
                      ->where($map)
                      ->field('num')
                      ->find();

        if ($info['num'] < $num) { return false; }
        return true;
    }


	/**    
     * 记录列表
     * @author echoshiki 2015/4/03 
     */
	public function lists($userid, $type="", $map="id!=''", $order="id desc", $page="15"){
        $Group    = D('AuthGroup');
        $Member   = D('Member');
        $Supplier = D("Supplier");

        $groupInfo = $Group->getUserGroup($userid);  //获取用户组
		if ($groupInfo[0]['group_id'] == C('JOIN_GROUP')) {
			$mode  = "user";
			$th    = array("Id","产品编号","产品名称","模式","数量","操作日期");
			$field = "id,name,code,type,num,date";   //供应商所能看到的列表选项
			$map  .= " AND warehouse='".$userid."'";	
		} else {
			$th    = array("Id","产品编号","产品名称","模式","数量","操作日期","归属","供应商","价格（元）");
			$mode  = "admin";
			$field = "*";	
		}
		if ($type != "") { $map .= " AND type='".$type."'"; }

		// 调用分页类进行数据分页
		$count = $this->field('id')->where($map)->order($order)->count();
		$Page  = new \Think\Page($count,$page); // 实例化分页类 传入总记录数和每页显示的记录数(10)
		$show  = $Page->show();// 分页显示输出
		

		$list = $this->field($field)->where($map)->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($list as $key => $value) {
            $list[$key]['warehouse']   = $value['warehouse']==1 ? "本库" : $Member->getNickName($value['warehouse']);  	
        	$list[$key]['supplier']  = $Supplier->getName($value['supplier']);
        }

        $list['show'] = $show;
		$list['th']   = $th;
		$list['mode'] = $mode;
		return $list;
	}


    /**    
     * 获取指定时间内的销量
     * @author echoshiki 2015/10/23 
     */
    public function getChartsByDate($code, $month, $year){
        $year  = $year ? $year : date('Y',time());
        $month = $month ? $month : date('m',time());
        $start = mktime(0,0,0,$month,1,$year);
        $end   = mktime(0,0,0,$month+1,1,$year);

        $map['date'] = array('between',array($start,$end));
        $map['type'] = 2;
        $map['code'] = $code;
        $field = "SUM(`num`) AS count";

        $baseData = D('record')->field($field)->where($map)->group('code')->select();
        return $baseData[0]['count'];
    }  
   
}