<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 后台公共文件
 * 主要定义后台公共函数库
 */

/* 解析列表定义规则*/

function get_list_field($data, $grid,$model){

	// 获取当前字段数据
    foreach($grid['field'] as $field){
        $array  =   explode('|',$field);
        $temp  =	$data[$array[0]];
        // 函数支持
        if(isset($array[1])){
            $temp = call_user_func($array[1], $temp);
        }
        $data2[$array[0]]    =   $temp;
    }
    if(!empty($grid['format'])){
        $value  =   preg_replace_callback('/\[([a-z_]+)\]/', function($match) use($data2){return $data2[$match[1]];}, $grid['format']);
    }else{
        $value  =   implode(' ',$data2);
    }

	// 链接支持
	if(!empty($grid['href'])){
		$links  =   explode(',',$grid['href']);
        foreach($links as $link){
            $array  =   explode('|',$link);
            $href   =   $array[0];
            if(preg_match('/^\[([a-z_]+)\]$/',$href,$matches)){
                $val[]  =   $data2[$matches[1]];
            }else{
                $show   =   isset($array[1])?$array[1]:$value;
                // 替换系统特殊字符串
                $href	=	str_replace(
                    array('[DELETE]','[EDIT]','[MODEL]'),
                    array('del?ids=[id]&model=[MODEL]','edit?id=[id]&model=[MODEL]',$model['id']),
                    $href);

                // 替换数据变量
                $href	=	preg_replace_callback('/\[([a-z_]+)\]/', function($match) use($data){return $data[$match[1]];}, $href);

                $val[]	=	'<a href="'.U($href).'">'.$show.'</a>';
            }
        }
        $value  =   implode(' ',$val);
	}
    return $value;
}

// 获取模型名称
function get_model_by_id($id){
    return $model = M('Model')->getFieldById($id,'title');
}

// 获取属性类型信息
function get_attribute_type($type=''){
    // TODO 可以加入系统配置
    static $_type = array(
        'num'       =>  array('数字','int(10) UNSIGNED NOT NULL'),
        'string'    =>  array('字符串','varchar(255) NOT NULL'),
        'textarea'  =>  array('文本框','text NOT NULL'),
        'datetime'  =>  array('时间','int(10) NOT NULL'),
        'bool'      =>  array('布尔','tinyint(2) NOT NULL'),
        'select'    =>  array('枚举','char(50) NOT NULL'),
    	'radio'		=>	array('单选','char(10) NOT NULL'),
    	'checkbox'	=>	array('多选','varchar(100) NOT NULL'),
    	'editor'    =>  array('编辑器','text NOT NULL'),
    	'picture'   =>  array('上传图片','int(10) UNSIGNED NOT NULL'),
    	'file'    	=>  array('上传附件','int(10) UNSIGNED NOT NULL'),
    );
    return $type?$_type[$type][0]:$_type;
}

/**
 * 获取对应状态的文字信息
 * @param int $status
 * @return string 状态文字 ，false 未获取到
 * @author huajie <banhuajie@163.com>
 */
function get_status_title($status = null){
    if(!isset($status)){
        return false;
    }
    switch ($status){
        case -1 : return    '已删除';   break;
        case 0  : return    '禁用';     break;
        case 1  : return    '正常';     break;
        case 2  : return    '待审核';   break;
        default : return    false;      break;
    }
}

// 获取数据的状态操作
function show_status_op($status) {
    switch ($status){
        case 0  : return    '启用';     break;
        case 1  : return    '禁用';     break;
        case 2  : return    '审核';		break;
        default : return    false;      break;
    }
}

/**
 * 获取文档的类型文字
 * @param string $type
 * @return string 状态文字 ，false 未获取到
 * @author huajie <banhuajie@163.com>
 */
function get_document_type($type = null){
    if(!isset($type)){
        return false;
    }
    switch ($type){
        case 1  : return    '目录'; break;
        case 2  : return    '主题'; break;
        case 3  : return    '段落'; break;
        default : return    false;  break;
    }
}

/**
 * 获取配置的类型
 * @param string $type 配置类型
 * @return string
 */
function get_config_type($type=0){
    $list = C('CONFIG_TYPE_LIST');
    return $list[$type];
}

/**
 * 获取配置的分组
 * @param string $group 配置分组
 * @return string
 */
function get_config_group($group=0){
    $list = C('CONFIG_GROUP_LIST');
    return $group?$list[$group]:'';
}

/**
 * select返回的数组进行整数映射转换
 *
 * @param array $map  映射关系二维数组  array(
 *                                          '字段名1'=>array(映射关系数组),
 *                                          '字段名2'=>array(映射关系数组),
 *                                           ......
 *                                       )
 * @author 朱亚杰 <zhuyajie@topthink.net>
 * @return array
 *
 *  array(
 *      array('id'=>1,'title'=>'标题','status'=>'1','status_text'=>'正常')
 *      ....
 *  )
 *
 */
function int_to_string(&$data,$map=array('status'=>array(1=>'正常',-1=>'删除',0=>'禁用',2=>'未审核',3=>'草稿'))) {
    if($data === false || $data === null ){
        return $data;
    }
    $data = (array)$data;
    foreach ($data as $key => $row){
        foreach ($map as $col=>$pair){
            if(isset($row[$col]) && isset($pair[$row[$col]])){
                $data[$key][$col.'_text'] = $pair[$row[$col]];
            }
        }
    }
    return $data;
}

/**
 * 动态扩展左侧菜单,base.html里用到
 * @author 朱亚杰 <zhuyajie@topthink.net>
 */
function extra_menu($extra_menu,&$base_menu){
    foreach ($extra_menu as $key=>$group){
        if( isset($base_menu['child'][$key]) ){
            $base_menu['child'][$key] = array_merge( $base_menu['child'][$key], $group);
        }else{
            $base_menu['child'][$key] = $group;
        }
    }
}

/**
 * 获取参数的所有父级分类
 * @param int $cid 分类id
 * @return array 参数分类和父类的信息集合
 * @author huajie <banhuajie@163.com>
 */
function get_parent_category($cid){
    if(empty($cid)){
        return false;
    }
    $cates  =   M('Category')->where(array('status'=>1))->field('id,title,pid')->order('sort')->select();
    $child  =   get_category($cid);	//获取参数分类的信息
    $pid    =   $child['pid'];
    $temp   =   array();
    $res[]  =   $child;
    while(true){
        foreach ($cates as $key=>$cate){
            if($cate['id'] == $pid){
                $pid = $cate['pid'];
                array_unshift($res, $cate);	//将父分类插入到数组第一个元素前
            }
        }
        if($pid == 0){
            break;
        }
    }
    return $res;
}

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function check_verify($code, $id = 1){
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}

/**
 * 获取当前分类的文档类型
 * @param int $id
 * @return array 文档类型数组
 * @author huajie <banhuajie@163.com>
 */
function get_type_bycate($id = null){
    if(empty($id)){
        return false;
    }
    $type_list  =   C('DOCUMENT_MODEL_TYPE');
    $model_type =   M('Category')->getFieldById($id, 'type');
    $model_type =   explode(',', $model_type);
    foreach ($type_list as $key=>$value){
        if(!in_array($key, $model_type)){
            unset($type_list[$key]);
        }
    }
    return $type_list;
}

/**
 * 获取当前文档的分类
 * @param int $id
 * @return array 文档类型数组
 * @author huajie <banhuajie@163.com>
 */
function get_cate($cate_id = null){
    if(empty($cate_id)){
        return false;
    }
    $cate   =   M('Category')->where('id='.$cate_id)->getField('title');
    return $cate;
}

 // 分析枚举类型配置值 格式 a:名称1,b:名称2
function parse_config_attr($string) {
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if(strpos($string,':')){
        $value  =   array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k]   = $v;
        }
    }else{
        $value  =   $array;
    }
    return $value;
}

// 获取子文档数目
function get_subdocument_count($id=0){
    return  M('Document')->where('pid='.$id)->count();
}



 // 分析枚举类型字段值 格式 a:名称1,b:名称2
 // 暂时和 parse_config_attr功能相同
 // 但请不要互相使用，后期会调整
function parse_field_attr($string) {
    if(0 === strpos($string,':')){
        // 采用函数定义
        return   eval(substr($string,1).';');
    }
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if(strpos($string,':')){
        $value  =   array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k]   = $v;
        }
    }else{
        $value  =   $array;
    }
    return $value;
}

/**
 * 获取行为数据
 * @param string $id 行为id
 * @param string $field 需要获取的字段
 * @author huajie <banhuajie@163.com>
 */
function get_action($id = null, $field = null){
	if(empty($id) && !is_numeric($id)){
		return false;
	}
	$list = S('action_list');
	if(empty($list[$id])){
		$map = array('status'=>array('gt', -1), 'id'=>$id);
		$list[$id] = M('Action')->where($map)->field(true)->find();
	}
	return empty($field) ? $list[$id] : $list[$id][$field];
}

/**
 * 根据条件字段获取数据
 * @param mixed $value 条件，可用常量或者数组
 * @param string $condition 条件字段
 * @param string $field 需要返回的字段，不传则返回整个数据
 * @author huajie <banhuajie@163.com>
 */
function get_document_field($value = null, $condition = 'id', $field = null){
	if(empty($value)){
		return false;
	}

	//拼接参数
	$map[$condition] = $value;
	$info = M('Model')->where($map);
	if(empty($field)){
		$info = $info->field(true)->find();
	}else{
		$info = $info->getField($field);
	}
	return $info;
}

/**
 * 获取行为类型
 * @param intger $type 类型
 * @param bool $all 是否返回全部类型
 * @author huajie <banhuajie@163.com>
 */
function get_action_type($type, $all = false){
	$list = array(
		1=>'系统',
		2=>'用户',
	);
	if($all){
		return $list;
	}
	return $list[$type];
}


/**
 * 获取对应状态的文字信息
 * @param int $status, int $type
 * @return string 状态文字, false 未获取到
 * @author echoshiki <echoshiki@outlook.com>
 */
function get_status_type($status = null, $type = ""){
    if(!isset($status)){ return false; }

    if ($type == "contract") {  //合同单状态
        switch ($status){
            case 1 : return    '<span style="color:#999">尚未到货</span>';     break;
            case 2 : return    '<span style="color:#009BFF">部分到货</span>';     break;
            case 3 : return    '<span style="color:red">全部到货</span>';     break;
            case 4 : return    '<span style="color:red">已经结束</span>';     break;
            case 5 : return    '<span style="color:grey">退货中</span>';     break;
            default : return  '尚未到货';      break;
        } 
    }

    if ($type == "in") {  //合同单状态
        switch ($status){
            case 1 : return    '采购入库';     break;
            case 2 : return    '销售退货';     break;
            default : return   '未知操作';     break;
        } 
    }

    if ($type == "out") {  //出库单状态
        switch ($status){
            case 1 : return    '销售出库';     break;
            case 2 : return    '退货出库';     break;
            case 3 : return    '客户出库';     break;
            default : return   '未知操作';     break;
        } 
    }

    if ($type == "record") {  //操作记录
        switch ($status){
            case 1 : return    '采购入库';     break;
            case 2 : return    '销售出库';     break;
            case 3 : return    '采购退货';     break;
            case 4 : return    '销售退货';     break;
            case 5 : return    '客户入库';     break;
            case 6 : return    '客户出库';     break;
            default : return   '未知操作';     break;
        } 
    }

    if ($type == "returned") {  //退货单
        switch ($status){
            case 1 : return    '采购退货';     break;
            case 2 : return    '销售退货';     break;
            case 3 : return    '库存退货';     break;
            default : return   '退货';     break;
        } 
    }

    if ($type == "order") {  //订单状态
        switch ($status){
            case 1 : return    '已创建';     break;
            case 2 : return    '<span style="color:green">已确认</span>';     break;
            case 3 : return    '<span style="color:blue">已出库</span>';     break;
            case 4 : return    '<span style="color:green">已发货</span>';     break;
            case 5 : return    '<span style="color:red">已完成</span>';     break;
            case 6 : return    '<span style="color:grey">已关闭</span>';     break;
            case 7 : return    '<span style="color:grey">退货中</span>';     break;  
            default : return  '未知操作';     break;
        } 
    }

    if ($type == "exstock") {
        switch ($status){
            case 1 : return    '固定属性';     break;
            case 2 : return    '非固定属性';     break;
            default : return   '未知操作';     break;
        } 
    }

    if ($type == "exstock_record") {
        switch ($status){
            case 1 : return    '入库';     break;
            case 2 : return    '出库';     break;
            case 3 : return    '退货';     break;
            default : return   '未知操作';     break;
        } 
    }

    switch ($status){  //其余情况
        case 1 : return    '产品编号';     break;
        case 2 : return    '产品名称';     break;
        case 3 : return    'OE码';         break;
        case 4 : return    '供应商名称';   break;
        case 5 : return    '法定代表';     break;
        case 6 : return    '供应商编号';   break;
        case 7 : return    '合同单编号';   break;
        case 8 : return    '入库单编号';   break;
        case 9 : return    '订购单编号';   break;
        case 10 : return   '出库单编号';   break;
        case 11 : return   '联系电话';     break;
        case 12 : return   '退货单编号';   break;
        case 13 : return   '加盟商查询';   break;
        case 14 : return   '联系人';   break;
        case 15 : return   '订购账号';   break;
        default : return    false;         break;
    }
}


/**
 * 获取对应供应商id的名字
 * @param int $id
 * @return string 名称
 * @author echoshiki <echoshiki@outlook.com>
 */
function get_company($id, $type = 1) {
    $Mod  = ($type == 2) ? D('ucenter_member') : D('supplier');
    $info = $Mod->field('company')->find($id);
    return $info['company'];
}


/**
 * 转移上传文件，并清理原文件
 * @param url $from
 * @return string 原图片地址
 * @author echoshiki <echoshiki@outlook.com>
 */
function thumb_move($from) {
    if(!file_exists($from)) { return false; }
    $path = "Public/Uploads/Stock/";
    $info = pathinfo($from);  //获取扩展名
    $file_name = date('YmdHis',time()).rand(1000,9999).'.'.$info['extension'];  // 生成文件名
    $file_path = $path.$file_name;
    copy($from, $file_path);  //复制文件
    unlink($from);  //删除文件
    return $file_path;  //返回真实目录地址
}


/**
 * 错误字典
 * @param int $num
 * @return string 代码意义
 * @author echoshiki <echoshiki@outlook.com>
 */
function error_trans($num = ''){
    switch ($num) {
        case '1002':
            return "库存内部存在该出库产品。";
            break;
        
        default:
            return true;
            break;
    }

}


/**    
 * 数组排序套 库存排序 升序
 * @author echoshiki 2015/10/20 
 */
function up_sort($x,$y){ 
    if($x['num'] == $y['num']) 
        return 0; 
    elseif($x['num'] < $y['num']) 
        return -1; 
    else 
        return 1; 
}  


/**    
 * 数组排序套 库存排序 降序
 * @author echoshiki 2015/10/20 
 */
function down_sort($x,$y){ 
    if($x['num'] == $y['num']) 
        return 0; 
    elseif($x['num'] > $y['num']) 
        return -1; 
    else 
        return 1; 
} 


function exportExcel($title="导出的EXCEL",$name="table",$th,$data){
    vendor('PHPExcel.PHPExcel');
    error_reporting(E_ALL);
    ini_set('display_errors', TRUE);
    ini_set('display_startup_errors', TRUE);
    date_default_timezone_set('Europe/London');
    $objPHPExcel = new PHPExcel();

    // Excel属性编辑
    $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                ->setLastModifiedBy("Maarten Balliauw")
                ->setTitle($title)
                ->setSubject("Office 2007 XLSX Test Document")
                ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");

    // 添加Excel的各列标题:chr函数将数字转成A,B,C,D…
    foreach ($th as $key => $value) {
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr(65+$key).'1', $value);   
    }

    //添加Excel的正文数据
    foreach ($data as $key => $value) {
        foreach ($value as $k => $val) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr(65+$k).($key+2), $val);
        }
    }

    // Rename worksheet
    $objPHPExcel->getActiveSheet()->setTitle($name);


    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);

    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$name.'.csv"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
    //set CSV file to UTF-8
    $objWriter->setUseBOM(true);
    $objWriter->save('php://output');
    exit;
}

//清洗数组的下标
function clearArr($array){
   $n = -1;
   foreach ($array as $key => $value) {
      $n++;
      if(is_array($value)){
         $arrayNew[$n] = clearArr($value);
      }else{
         $arrayNew[$n] = $value;
      }
    }
    return $arrayNew;
}

// 将中文字符串转换成数组
function str_split_unicode($str, $l = 0) {
    if ($l > 0) {
        $ret = array();
        $len = mb_strlen($str, "UTF-8");
        for ($i = 0; $i < $len; $i += $l) {
            $ret[] = mb_substr($str, $i, $l, "UTF-8");
        }
        return $ret;
    }
    return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
}

// 将中文转换成首字母
function getFirstCharter($str){
    if(empty($str)){return '';}
    $fchar=ord($str{0});
    if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});
    $s1=iconv('UTF-8','gb2312',$str);
    $s2=iconv('gb2312','UTF-8',$s1);
    $s=$s2==$str?$s1:$str;
    $asc=ord($s{0})*256+ord($s{1})-65536;
    if($asc>=-20319&&$asc<=-20284) return 'A';
    if($asc>=-20283&&$asc<=-19776) return 'B';
    if($asc>=-19775&&$asc<=-19219) return 'C';
    if($asc>=-19218&&$asc<=-18711) return 'D';
    if($asc>=-18710&&$asc<=-18527) return 'E';
    if($asc>=-18526&&$asc<=-18240) return 'F';
    if($asc>=-18239&&$asc<=-17923) return 'G';
    if($asc>=-17922&&$asc<=-17418) return 'H';
    if($asc>=-17417&&$asc<=-16475) return 'J';
    if($asc>=-16474&&$asc<=-16213) return 'K';
    if($asc>=-16212&&$asc<=-15641) return 'L';
    if($asc>=-15640&&$asc<=-15166) return 'M';
    if($asc>=-15165&&$asc<=-14923) return 'N';
    if($asc>=-14922&&$asc<=-14915) return 'O';
    if($asc>=-14914&&$asc<=-14631) return 'P';
    if($asc>=-14630&&$asc<=-14150) return 'Q';
    if($asc>=-14149&&$asc<=-14091) return 'R';
    if($asc>=-14090&&$asc<=-13319) return 'S';
    if($asc>=-13318&&$asc<=-12839) return 'T';
    if($asc>=-12838&&$asc<=-12557) return 'W';
    if($asc>=-12556&&$asc<=-11848) return 'X';
    if($asc>=-11847&&$asc<=-11056) return 'Y';
    if($asc>=-11055&&$asc<=-10247) return 'Z';
    return null;
}

function pinyin($str) {
    $arr = str_split_unicode($str);
    foreach ($arr as $key => $value) {
        $result .= getFirstCharter($value);
    }
    return $result;
}





