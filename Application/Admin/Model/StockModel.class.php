<?php

namespace Admin\Model;
use Think\Model;

/**
 * 供应商模型
 * @author echoshiki 2015/3/27
 */

class StockModel extends Model {

    protected $_validate = array(
        array('code', '15', '产品编码长度为15个字符', self::EXISTS_VALIDATE, 'length'),
        array('code', '', '该产品编码已经被占用', self::EXISTS_VALIDATE, 'unique',1),
        array('name', '3,50', '产品名称长度为3-30个字符', self::EXISTS_VALIDATE, 'length'),
        array('purch_price', 'number', '进货价格必须填写数字', self::EXISTS_VALIDATE), 
        array('sale_price', 'number', '建议销售价格必须填写数字', self::EXISTS_VALIDATE), 
    );

    /**    
     * 获取格式化后的产品详细（转换好分类名等）
     * @author echoshiki 2015/4/01 
     */
    public function info($id, $type="1") {
        $Supplier = D("Supplier");
        $Category = D("Category");
        $info  = $this->find($id);
        $info['supplierid'] = $info['supplier'];
        $info['category'] = $Category->getName($info['category']);  //获取分类名称
        $info['supplier'] = $Supplier->getName($info['supplier']);  //获取供应商名称
        $info['thumb']    = unserialize($info['thumb_1']);   //处理图片
        $standard = unserialize($info['standard']);
        foreach ($standard as $k => $val) {
           $standardArr[] = $Category->getName($val); 
        }
        $info['standard'] = implode($standardArr, "|");
        return $info;
    }


    /**    
     * 获取格式化后的产品列表（转换好分类名等）
     * @author echoshiki 2015/4/01 
     */
    public function lists($where = "id!=0", $order = 'id DESC', $field = true, $page = "20") {
        if ($order=="num desc" || $order=="num asc") {  // 如果是库存排序
            $num_order = $order;
            $order = 'id DESC';
        }

        // 调用分页类进行数据分页
        $count = $this->field('id')->where($where)->order($order)->count();
        $Page  = new \Think\Page($count,$page); // 实例化分页类 传入总记录数和每页显示的记录数(10)
        $show  = $Page->show();// 分页显示输出

        $list  = $this->where($where)->field($field)->limit($Page->firstRow.','.$Page->listRows)->order($order)->select();;
        foreach ($list as $key => $value) {
            $list[$key]['category'] = D("Category")->getName($value['category']);  //获取分类名称
            $list[$key]['supplier'] = D("Supplier")->getName($value['supplier']);  //获取供应商名称
            $list[$key]['supplierid'] = $value['supplier'];
            $standard = unserialize($value['standard']);
            
            foreach ($standard as $k => $val) {
               $standardArr[] = D("Category")->getName($val); 
            }
            $list[$key]['standard'] = implode($standardArr, " ");
            unset($standardArr);

            $info = M('Warehouse')->where('sid='.$value['id'])->find();
            $list[$key]['num'] = $info['num'];
            $list[$key]['min_num'] = $info['min_num'];
            $list[$key]['max_num'] = $info['max_num'];
        }

        if ($num_order == "num desc") {
            usort($list,"down_sort");
        } elseif ($num_order == "num asc") {
            usort($list,"up_sort");
        } 

        $list['show'] = $show;
        return $list;
    }


    /**    
     * 验证产品是否已经录入进产品库
     * @author echoshiki 2015/4/02 
     */
    public function isCreate($code) {
        $map['code']   = $code;
        $field = "id";
        $num = $this->where($map)->field($field)->count();
        if ( $num > 0 ) { return true; }
        return false;
    }


    /**    
     * 联表查询列出数据
     * 参数1：model名、对应字段、对应排序(id asc,name asc)
     * 参数2：副表
     * 参数3：where条件（主表）
     * 参数4：默认排序
     * @author echoshiki 2015/10/31 
     */
    public function listEx($main, $sub, $map, $default="app_stock.id asc", $field="*", $group="app_stock.id", $page="30") {

        $baseModel = $main['table'];          // 主表模型
        $baseTable = 'app_'.$main['table'];   // 主表名称
        $subTable  = 'app_'.$sub['table'];    // 副表名称
        $baseField = $baseTable.".".$main['field'];  // 主表对应字段
        $subField  = $subTable.".".$sub['field'];    // 副表对应字段

        // 组合JOIN条件
        $join = $subTable." ON ".$baseField." = ".$subField;  // LEFT JOIN app_stock ON app_warehouse.sid = app_stock.id

        // 组合where条件（只有table1表，table2表条件在先前已经处理）
        foreach ($map as $key => $value) {
            if ($key == "warn" && $value == true) {
                $doc[] = "( app_warehouse.num < app_warehouse.min_num OR (app_warehouse.num > app_warehouse.max_num AND app_warehouse.max_num != 0))";
                unset($map[$key]);
                continue;
            }

            $doc1 = $baseTable.".".$key;
            $nbsp = ' ';
            $doc2 = (gettype($value)=='array') ? $value[0] : " = ";
            $doc3 = (gettype($value)=='array') ? "(".$value[1].")" : $value;
            $doc3 = ($value[0]=="between") ? $value[1][0]." AND ".$value[1][1] : $doc3;  //特殊情况 between(date1 and date2)
            $doc[] = $doc1.$nbsp.$doc2.$nbsp.$doc3;
        }
        $map = implode($doc, ' AND ');   // WHERE app_warehouse.sid in (12,16,17,18,19,20,21,23,24,25) AND app_warehouse.warehouse like ("%%")

        // 组合order语句
        if ($main['order'] != "") {
            $orderTmp = explode(',', $main['order']);
            foreach ($orderTmp as $key => $value) { $orderArr[] = $baseTable.".".$value; }
        }
        if ($sub['order'] != "") {
            $orderTmp = explode(',', $sub['order']);
            foreach ($orderTmp as $key => $value) { $orderArr[] = $subTable.".".$value; }
        }
        if ($main['sp'] != "") {  // 特殊字段 total
            $orderTmp = explode(',', $main['sp']);
            foreach ($orderTmp as $key => $value) { $orderArr[] = $value; }
        }

        $order = empty($orderArr) ? $default : implode($orderArr, ",");  // ORDER BY app_stock.name

        // 调用分页类进行数据分页
        $countTmp = D($baseModel)->field("COUNT(*) as num")->join($join)->where($map)->group($group)->select();
        $count = count($countTmp);
        $Page  = new \Think\Page($count,$page); // 实例化分页类 传入总记录数和每页显示的记录数
        $show  = $Page->show();  // 分页显示输出

        $data = D($baseModel)->field($field)->join($join)->where($map)->limit($Page->firstRow.','.$Page->listRows)->order($order)->group($group)->select(); 
        //echo D($baseModel)->getLastSql();

        $Category = D("Category");
        foreach ($data as $key => $value) {
            $data[$key]['category'] = $Category->getName($value['category']);
            $standardArr = unserialize($value['standard']);
            foreach ($standardArr as $k => $val) { $standardArr[$k] = $Category->getName($val); }
            $data[$key]['standard']  = implode($standardArr, " ");
            $data[$key]['warehouse'] = $value['warehouse']==1 ? "本库" : D("Member")->getNickName($value['warehouse']);
        }
        $data['show'] = $show;  //分页
        return $data;
    }

}