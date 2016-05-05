<?php

// +----------------------------------------------------------------------
// | 企业进销存管理模块
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.yzsdw.com/ All rights reserved.
// +----------------------------------------------------------------------
// | Author: echoshiki <echoshiki@outlook.com>
// +----------------------------------------------------------------------
// | Date: 2015/03/25 <start>
// +----------------------------------------------------------------------

namespace Admin\Controller;

class PsiController extends AdminController {

    /**
     * 采购申请 - 采购管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/07/14
     */
     public function apply() {
        if (IS_POST) {
            $ida  = I("post.id");  //商品编号
            $ids  = implode($ida, ',');
            if ($ids[0] == '') { $this->error('未选择任何产品。'); }

            $where['id'] = array('in',$ids);
            $order = 'id DESC';
            $field = 'id,code,name,category,supplier,standard,OE,unit,store_no';
            $list  = D("Stock")->lists($where,$order,$field);
            $page  = $list['show'];
            unset($list['show']);

            $this->assign('_list',$list);
            $this->assign('_page',$page);
            $this->meta_title = '生成采购计划单';
            $this->display('apply_do'); 
        } else {
            $sup_list = D("Supplier")->field('id,company')->select();  //供应商列表
            if (I('get.s1') || I('get.s2') || I('get.s3')) {
                $string = implode(I(), ',');  //将GET方式供应商参数转换成(1,2,3,4)字符串
                $map['supplier'] = array('in',$string);  //sql筛选
            } 
            
            $field = "id,code,name,category,supplier,standard,OE,store_no";
            $order = I('get.orderby') ? I('get.field')." ".I('get.orderby') : "id desc";
            $list  = D("Stock")->lists($map,$order,$field);

            $page  = $list['show'];
            unset($list['show']);

            // 筛选指定月销量
            foreach ($list as $key => $value) {
                $chartsAll = 0;
                $monthArr  = explode(',', I('get.mon'));
                foreach ($monthArr as $k => $v) {
                    $chartsAll += D('Record')->getChartsByDate($value['code'],$v,I('get.year'));
                }
                $list[$key]['chartsAvg'] = intval($chartsAll/count($monthArr));
                $list[$key]['chartsAll'] = $chartsAll;
            }


            $this->assign('_supplier',$sup_list);
            $this->assign('_list',$list);
            $this->assign('_page',$page);
            $this->meta_title = '采购申请';
            $this->display();      
        }
     }


     /**
     * 申购单处理 - 采购管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/07/15
     */
     public function apply_do() {
        if(IS_POST){
            $Apply      = D('apply');
            $data       = $Apply->create();
            if(!$data){ $this->error($Apply->getError()); }

            $data['date']   = time();  //申购单创建日期
            $data['status'] = 1;  //申购单状态

            $res = $Apply->add($data);
            if (!$res) { $this->error("插入表1失败，请检查数据库后再试。"); }

            $list = I("post.list");
            foreach ($list as $key => $value) {
                $info = array();
                $info = $value;
                $info['date'] = strtotime($info['date']);
                $info['fid']  = $res;
                $result = D('apply_info')->add($info);    
                if (!$result) { $this->error("插入表2失败，请检查数据库后再试。"); } 
            }
            $this->success('采购产品申购单生成成功。',U('Psi/apply_list'));
        } else {
            $this->error("非法操作，请返回。");
        }
     }


     /**
     * 申购单列表 - 采购管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/07/16
     */
     public function apply_list() {
        $Apply = D('apply');
        C('LIST_ROWS',30);
        $list  = $this->lists('apply');

        $this->assign('_list',$list);
        $this->meta_title = '采购计划单列表';
        $this->display();   
     }


    /**
     * 申购单详细 - 采购管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/07/17
     */
     public function apply_info() {
        $Apply = D('apply');
        $id    = I("get.id");  //计划单id
        $list  = $Apply->show($id);
        $this->assign('_list',$list);
        $this->assign('_page',$page);
        $this->meta_title = '采购计划单详情';
        if ($_GET['print'] == '1') { $this->display('apply_print'); return false; }
        $this->display();        
     }


    /**
     * 申购单编辑 - 采购管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/07/17
     */
     public function apply_edit() {
        $Apply = D('Apply');
        $id    = I("get.id");  //计划单id
        if (IS_POST) {
            $info = I('post.list');
            $data = $Apply->create(I('post.'),2);
            // print_r($data); exit();
            if(!$data){ $this->error($Apply->getError()); }
            $Apply->save($data);   //修改主表信息
            $Info = D('Apply_info');
            foreach ($info as $key => $value) {
               if ($value['num'] <= 0) { $this->error('配件数目不能小于等于零。'); }
               if ($value['date']) {
                   $value['date'] = strtotime($value['date']);
               }
               $Info->save($value);  //修改副表信息
            }
           $this->success('修改产品成功！');
        } else {
            $list  = $Apply->show($id);
            $this->assign('_list',$list);
            $this->assign('_page',$page);
            $this->meta_title = '采购计划单编辑';
            $this->display();  
        } 
     }


    /**
     * 申购单删除 - 采购管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/07/16
     */
     public function apply_del() {
        $Apply      = D('apply');
        $id         = array_unique((array)I('id',0)); 
        if ($id[0] == 0) { $this->error('未选中任何计划单。'); }
        $map['id']  = array('in',$id);
        $Apply->del($map);
        $this->success('采购计划单删除成功！');
     }


    /**
     * 合同单生成 - 采购管理
     * @author echoshiki <echoshiki@outlook.com>
     * key：提交处理，包含几个供应商就生成多少份合同。合同编号DZ-年份-月份-第几份合同
     * 2015/07/20
     */
    public function contract() {
        $Contract = D('contract');
        $Info  = D('contract_info');
        $Apply = D('apply');
        $id    = I("get.id");  //计划单id
        if (IS_POST) {
            $data = I('post.');
            $data['num'] = $data['total'] = '';
            $data['status'] = 1;
            $data['pch_date'] = time();  //签订日期
            $data['dev_date'] = strtotime($data['dev_date']);  //交货日期
            foreach ($data['list'] as $key => $value) {  //供应商个数=合同个数
                foreach ($value as $k => $val) {
                    $data['num']   += $val['num'];
                    $data['total'] += $val['total'];       
                }
                $data['no'] = $Contract->getno();  //获取合同编号
                $data['supplier'] = $key;   //供应商id

                $fid = $Contract->add($data);  //生成的主表ID
                if (!$fid) { $this->error('主表插入出现错误。'); }
                foreach ($value as $k => $val) {
                    $val['fid'] = $fid;
                    $Info->add($val);
                }
                $data['num'] = $data['total'] = '';
            }
            $map['id'] = $data['apply'];
            $map['status'] = '2';  //已经转换成合同，更改状态
            $Apply->save($map);
            $this->success('采购合同单生成成功！',U('Psi/contract_list'));
        } else {
            $list = $Apply->show($id);
            if ($list['status'] == 2) { $this->error('非法操作，请返回。',U('Psi/apply_list')); }
            $this->assign('_list',$list);
            $this->assign('_page',$page);
            $this->meta_title = '采购计划单编辑';
            $this->display();  
        }        
    } 


    /**
     * 合同列表 - 采购管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/07/23
     */
    public function contract_list() {

        $orderby  = I('get.orderby') ? I('get.field')." ".I('get.orderby') : "id desc";  //排序
        $sup_list    = D("Supplier")->field('id,company')->select();  //供应商列表
        $status_list = C('CONTRACT');

        $map['supplier'] = I('get.s1') ? I('get.s1') : array('LIKE',"%%");  //sql筛选
        $map['status'] = I('get.status') ? I('get.status') : array('LIKE',"%%");
        /* 关键词模糊搜索 START 2015/07/10 */
        $type = 4; //默认搜索type值        
        $ask  = I("ask") ? I("ask") : ""; 
        if ($ask == 1) {
            $type     = I("type") ? I("type") : $type;   //搜索类型
            $keyword  = I("keyword") ? I("keyword") : ""; 
            $start    = I("time-start") ? strtotime(I("time-start")) : 0;
            $end      = I("time-end") ? strtotime(I("time-end")) : 9999999999;
            if ($type == 4) {
                $Supplier = D('supplier');
                $where['company'] = array('like', '%'.$keyword.'%');
                $id_arr = $Supplier->field('id')->where($where)->select();
                foreach ($id_arr as $k => $val) {  $id_tmp[] = $val['id']; }
                $ids = implode($id_tmp, ',');
                $map['supplier'] = array('in',$ids);
            }
            if ($type == 7) { $map['no'] = array('like', '%'.$keyword.'%'); }
            $map['pch_date'] = array('between',array($start,$end));  //采购时间
        }  
        /* 关键词模糊搜索 END 2015/07/10 */ 

        C('LIST_ROWS',30);
        $list  = $this->lists('contract',$map,$orderby);

        if ($_GET['export'] == 1) {
           /* excel导出 START */
            foreach ($list as $key => $value) {
                $excelData[$key][] = $key;
                $excelData[$key][] = $value['no'];
                $excelData[$key][] = get_company($value['supplier']);
                $excelData[$key][] = $value['total'];
                $excelData[$key][] = $value['num'];
                $excelData[$key][] = date('Y-m-d',$value['pch_date']);
                $excelData[$key][] = date('Y-m-d',$value['dev_date']);
                $excelData[$key][] = $value['note'];
            }
            $th = array("序号","合同单编号","供应商名称","合同总价","合同总量","采购日期","交货日期","合同备注");
            exportExcel("德众科技采购合同列表", "德众科技采购合同列表", $th, $excelData);
            /* excel导出 END */
        }
        

        $this->assign('_supplier',$sup_list);
        $this->assign('_status',$status_list);
        $this->assign('type',$type);
        $this->assign('_list',$list);
        $this->meta_title = '采购合同单列表';
        $this->display(); 
    }


    /**
     * 合同详情 - 采购管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/07/23
     */
    public function contract_info() {
        $id    = I("get.id");  //合同单id
        $list  = D('contract')->show($id);
        $this->assign('_list',$list);
        $this->meta_title = '采购合同单详情';
        if ($_GET['print'] == '1') { $this->display('contract_print'); return false; }
        if ($_GET['export'] == '1') {
           /* excel导出 START */
            foreach ($list['info'] as $key => $value) {
                $excelData[$key][] = $key+1;
                $excelData[$key][] = $value['name'];
                $excelData[$key][] = $value['code'];
                $excelData[$key][] = $value['OE'];
                $excelData[$key][] = $value['supplier_no'];
                $excelData[$key][] = $value['standard'];
                $excelData[$key][] = $value['purch_price'];
                $excelData[$key][] = $value['purch_price_off'];
                $excelData[$key][] = $value['num'];
                $excelData[$key][] = $value['num']*$value['purch_price'];
                $excelData[$key][] = $value['total'];
                $num   += $value['num'];
                $total += $value['total'];
                $total_on += $value['num']*$value['purch_price'];
            }
            $excelData[$key+1][] = "合计";
            $excelData[$key+1][8]  = $num;
            $excelData[$key+1][9] = $total_on;
            $excelData[$key+1][10] = $total;
            
            $filename = "采购合同_".$list['no'];
            $th = array("序号","产品名称","产品编码","OE号","供应商产品编号","适用车型","含税单价","不含税单价","数量","金额(含税)","金额(不含税)");
            exportExcel("德众科技采购合同详单", $filename, $th, $excelData);
            /* excel导出 END */
        }

        $this->display();  
    }


    /**
     * 合同状态编辑 - 采购管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/08/05
     */
    public function contract_edit() {
        $Contract  = D('contract');

        if (IS_POST) {
            $data = I('post.');
            $map['id'] = $data['id'];
            unset($data['id']);
            $Contract->where($map)->save($data);
            $this->success("合同状态变更成功！",U('Psi/contract_list'));
        }

        $map['id'] = I("get.id");  //计划单id
        $info = $Contract->where($map)->field('id,status,note')->find();

        $this->assign('_info',$info);
        $this->meta_title = '合同单状态变更';
        $this->display();  
    }


    /**
     * 合同删除 - 采购管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/07/23
     */
    public function contract_del() {
        $Contract = D('contract');
        $Info     = D('contract_info');
        $id       = array_unique((array)I('id',0)); 
        if ($id[0] == 0) { $this->error('未选中任何合同单。'); }
        $map['id']  = array('in',$id);
        $Contract->del($map);
        $this->success('采购合同单删除成功！');        
    }


    /**
     * 采购退货 - 采购管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/08/28
     */
    public function in_return() {
        if (IS_POST) {
            /* 插入退货单据表主表 */
            $Re   = D('returned');
            $data =  $Re->create();
            if(!$data){ $this->error($Re->getError()); }
            $data['date']   = strtotime($data['date']);  //转换成时间戳
            $data['status'] = 1;  
            $data['time']   = time();
            $data['from']   = 1;  //类型为采购退货
            $data['user']   = session('user_auth.uid');
            $fid = $Re->add($data);

            /* 插入退货单据表副表、更新采购合同表（在库数）、库存表、出入库记录表 */
            $Info1 = D('returned_info');
            $Info2 = M('contract_info'); 
            $Ware  = D('warehouse');
            $list  = I('post.info');
            foreach ($list as $key => $value) {
                if ($value['num'] == 0) { continue; }  //数量为零跳过

                $value['fid'] = $fid;
                $Info1->add($value);    //插入退货单据副表

                $map['fid'] = $data['from_id'];
                $map['sid'] = $value['sid'];   
                $Info2->where($map)->setDec('in_num',$value['num']);   //在库数递减操作，更新采购合同表
                $Ware->pull($value['code'], 1, $value['num'], $data['from_id'], 3);  //出库并生成退货记录 warehouse表 record表
            }
            D('contract')->where(array('id'=>$data['from_id']))->save(array('status'=>5));  //退货状态

            $this->success('本期退货成功！',U('Psi/contract_list'));
        } else {
            $id   = I("get.id");  //采购合同单id
            $list = D('contract')->show($id);
            $this->assign('_list',$list);
            $this->assign('_page',$page);
            $this->meta_title = '采购退货';
            $this->display();  
        }         
    }


    /**
     * 退货一览 - 采购管理
     * type 1.供应退货 2.销售退货
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/07/24
     */
    public function return_list() {

        $style = I('get.style');
        /* 关键词模糊搜索 START 2015/07/10 */
        $type = 12; //默认搜索type值        
        $ask  = I("ask") ? I("ask") : ""; 
        if ($ask == 1) {
            $type     = I("type") ? I("type") : $type;   //搜索类型
            $keyword  = I("keyword") ? I("keyword") : ""; 
            $start    = I("time-start") ? strtotime(I("time-start")) : 0;
            $end      = I("time-end") ? strtotime(I("time-end")) : 9999999999;
            if ($type == 12) { $map['no'] = array('like', '%'.$keyword.'%'); }
            if ($type == 7)  { $map['from_no'] = array('like', '%'.$keyword.'%'); }
            $map['date'] = array('between',array($start,$end));  //采购时间
        }  
        /* 关键词模糊搜索 END 2015/07/10 */ 

        C('LIST_ROWS',30);
        $list = $this->lists('returned',$map);

        $this->assign('type',$type);
        $this->assign('_style',$style);   //供应退货 or 销售退货
        $this->assign('_list',$list);
        $this->meta_title = '退货一览';
        $this->display();     
    }


    /**
     * 退货详情 - 采购管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/08/29
     */
    public function return_info() {

        $id = I("get.id");  //计划单id
        $Re = D('returned');
        $list = $Re->show($id);
        $this->assign('_list',$list);
        $this->assign('_page',$page);
        $this->meta_title = '退货明细';
        if ($_GET['print'] == '1') { $this->display('return_print'); return false; }
        $this->display();    
    }   


    /**
     * 添加明细 - 采购管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/07/24
     */
    public function account_add() {
        if (IS_POST) {
            $Account = D('account');
            $data  = $Account->create(I('post.'),1);
            if(!$data){ $this->error($Account->getError()); }  //验证 

            $data['status'] = 1;
            $data['balance'] = $data['balance'] ? $data['balance'] : $data['credit'] - $data['debit'];  
            $data['date'] = strtotime($data['date']);
            
            $res = $Account->add($data);
            if (!$res) { $this->error('INSERT操作失败，请检查数据库配置。'); }
            $this->success("账目明细添加成功！");
        } else {
            $Supplier   = D("Supplier");
            //供应商列表      
            $sup_list   = $Supplier->field('id,company')->select(); 
            $this->assign('sup_list', $sup_list);
            $this->meta_title = '添加明细';
            $this->display(); 
        }
    }


    /**
     * 财务明细 - 采购管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/07/24
     */
    public function account() {
        /* 关键词模糊搜索 START 2015/07/10 */         
        $ask = I("ask") ? I("ask") : ""; 
        if ($ask == 1) {
            $type     = I("type") ? I("type") : "";   //搜索类型
            $keyword  = I("keyword") ? I("keyword") : ""; 
            $start    = I("time-start") ? strtotime(I("time-start")) : 0;
            $end      = I("time-end") ? strtotime(I("time-end")) : 9999999999;
            switch ($type) {
                case 4:  $search = "company";  break;
                case 6:  $search = "no";  break;
                default: $search = "company";  break;
            }
            $where[$search] = array('like', '%'.$keyword.'%');
            $map['date'] = array('between',array($start,$end));
        }  
        $id_arr = D('supplier')->field('id')->where($where)->select();
        foreach ($id_arr as $k => $val) {  $id_tmp[] = $val['id']; }
        $ids = implode($id_tmp, ',');
        $map['supplier_id'] = array('in',$ids);
        /* 关键词模糊搜索 END 2015/07/10 */ 

        $list = D('account')->where($map)->select();
        foreach ($list as $key => $value) {
            $info = D('supplier')->field('company,no')->find($value['supplier_id']);
            $list[$key]['company'] = $info['company'];
            $list[$key]['no']      = $info['no'];
        }

        $this->assign('type',$type);
        $this->assign('_list',$list);
        $this->meta_title = '财务明细';
        $this->display(); 
    }


    /**
     * 财务明细 - 采购管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/10/10
     */
    public function account_info() {
        $info = D('Account')->find(I('get.id'));
        $this->assign('info', $info);
        $this->meta_title = '财务明细';
        $this->display(); 
    }


    /**
     * 明细删除 - 采购管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/07/24
     */
    public function account_del() {
        $Account    = D("account");
        $id         = array_unique((array)I('id',0)); 
        if ($id[0] == 0) { $this->error('未选中任何明细记录。'); }
        $map['id']  = array('in',$id);
        $Account->where($map)->delete();
        $this->success('财务明细记录删除成功！');
    }    


    /**
     * 采购入库 - 库存管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/07/28
     */
    public function in_apply() {
        $map['status'] = array('neq',4);
        $map['no'] = array('like', '%'.I('post.no').'%'); ;

        C('LIST_ROWS',30);
        $list  = $this->lists('contract',$map);

        $this->assign('_list',$list);
        $this->meta_title = '采购入库';
        $this->display();         
    }


    /**
     * 进行入库 - 库存管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/08/03
     */
    public function in_do() {

        if (IS_POST) {
            /* 插入入库单据表主表 */
            $In   = D('in');
            $data =  $In->create();
            if(!$data){ $this->error($In->getError()); }
            $data['status'] = 1;  
            $data['date']   = time();
            $data['from']   = 1;  //类型为采购入库
            $data['user']   = session('user_auth.uid');
            $fid = $In->add($data);
            
            /* 插入入库单据表副表、更新采购合同表（在库数）、库存表、出入库记录表 */
            $Info1 = D('in_info');
            $Info2 = M('contract_info'); 
            $Ware  = D('warehouse');
            $list  = I('post.info');
            foreach ($list as $key => $value) {
                if ($value['num'] == 0) { continue; }  //数量为零跳过

                $value['fid'] = $fid;
                $Info1->add($value);    //插入入库单据副表

                $map['fid'] = $data['from_id'];
                $map['sid'] = $value['sid'];   
                $Info2->where($map)->setInc('in_num',$value['num']);   //在库数累加操作，更新采购合同表
                $Ware->push($value['code'], 1, $value['num'], $data['from_id']);  //入库并生成记录 warehouse表 record表
            }
            D('contract')->where(array('id'=>$data['from_id']))->save(array('status'=>2));  //部分到货

            $this->success('本期入库成功！',U('Psi/in_apply'));
        } else {
            $id       = I("get.id");  //计划单id
            $Contract = D('contract');
            $list = $Contract->show($id);
            $this->assign('_list',$list);
            $this->assign('_page',$page);
            $this->meta_title = '采购入库';
            $this->display();  
        }         
    }


    /**
     * 入库一览 - 库存管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/07/31
     */
    public function in_list() {   

        /* 关键词模糊搜索 START 2015/07/10 */
        $type = 8; //默认搜索type值        
        $ask  = I("ask") ? I("ask") : ""; 
        if ($ask == 1) {
            $type     = I("type") ? I("type") : $type;   //搜索类型
            $keyword  = I("keyword") ? I("keyword") : ""; 
            $start    = I("time-start") ? strtotime(I("time-start")) : 0;
            $end      = I("time-end") ? strtotime(I("time-end")) : 9999999999;
            if ($type == 8) { $map['no'] = array('like', '%'.$keyword.'%'); }
            if ($type == 7) { $map['from_no'] = array('like', '%'.$keyword.'%'); }
            $map['date'] = array('between',array($start,$end));  //采购时间
        }  
        /* 关键词模糊搜索 END 2015/07/10 */ 

        $sup_list    = D("Supplier")->field('id,company')->select();  //供应商列表
        $map['supplier'] = I('get.s1') ? I('get.s1') : array('LIKE',"%%");  //sql筛选

        $order = I('get.orderby') ? I('get.field')." ".I('get.orderby') : "id desc";
    
        C('LIST_ROWS',30);
        $list = $this->lists('in',$map,$order);

        $this->assign('type',$type);
        $this->assign('_supplier',$sup_list);
        $this->assign('_list',$list);
        $this->meta_title = '入库一览';
        $this->display();         
    }


    /**
     * 入库单据详情 - 库存管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/08/06
     */
    public function in_info() {

        $id = I("get.id");  //计划单id
        $In = D('in');
        $list = $In->show($id);
        $this->assign('_list',$list);
        $this->assign('_page',$page);
        $this->meta_title = '采购入库';
        if ($_GET['print'] == '1') { $this->display('in_print'); return false; }
        $this->display();    

    }   


    /**
     * 入库功能 - 库存管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/03/25
     */
    public function index() {
        if(IS_POST){

            $Ware   = D('Warehouse');  //实例化仓库模型
            $Record = D('Record');
            $data  = $Record->create($_POST,1);
            if(!$data){ $this->error($Record->getError()); }  //验证 
            $code  = I("post.code");  //商品编号

            //除加盟商外的入库操作默认进入总库，加盟商进入以其ID为标识的仓库,获取仓库id
            $warehouse = $Ware->wareid(session('user_auth.uid'));

            $res = $Ware->push($code, $warehouse);  // 入库 
            if (!$res) {  $this->error('产品入库失败！请检查数据库连接。'); }
            $this->success('产品入库成功！');

        } else {
            $Record = D('Record');
            $map    = "id!=''"; 
            if (I("get.code")) { $map .= " AND code='".I("get.code")."'"; }   //搜索

            $list   = $Record->lists(session('user_auth.uid'),1,$map);  //入库记录
            $th     = $list['th'];   //判断角色应该看到的列表选项（区分管理员和加盟商）
            $mode   = $list['mode']; //判断角色应该看到的列表选项（区分管理员和加盟商）  
            $page   = $list['show']; //传入分页样式
            unset($list['show']);          
            unset($list['th']);    //前端不会多出一行
            unset($list['mode']);

            $this->assign('_list', $list);
            $this->assign('_th', $th);
            $this->assign('mode', $mode);
            $this->assign('_page', $page);
            $this->meta_title = '管理首页';
            $this->display();
        }            
    }


    /**
     * 库存查看 - 库存管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/03/25
     */
    public function inventory() {

        $cat_pro  = D("Category")->getTree('39','id,title,pid');  //获取商品分类
        $sup_list = D("Supplier")->where($where)->field('id,company')->select();  //供应商列表

        $users = D('Member')->getCustomers();
        
        /* 关键词模糊搜索 START 2015/07/10 */         
            $type     = I("type") ? I("type") : "";   //搜索类型
            $keyword  = I("keyword") ? I("keyword") : ""; 
            switch ($type) {
                case 1:  $search = "code";  break;
                case 2:  $search = "name";  break;
                case 3:  $search = "OE";    break; 
                default: $search = "code";  break;
            }
            if ($search == 'name') {
                $sub['name']   = array('like', '%'.$keyword.'%');
                $sub['pinyin'] = array('like', '%'.$keyword.'%');
                $sub['_logic'] = 'or';
                $where['_complex'] = $sub;
            } else {
                $where[$search] = array('like', '%'.$keyword.'%');
            }

            /* 分类、供应商筛选START 2015/07/13 */
                $where['category'] = I('category') ? I('category') : array('like', '%%');
                $where['supplier'] = I('supplier') ? I('supplier') : array('like', '%%');
            /* 分类、供应商筛选END 2015/07/13 */   

            $id_arr = D("Stock")->field('id')->where($where)->select();

            foreach ($id_arr as $k => $val) {  $id_tmp[] = $val['id']; }
            $ids = implode($id_tmp, ',');
            $map['sid'] = array('in',$ids);
        /* 关键词模糊搜索 END 2015/07/10 */  

        $wareid = D('Warehouse')->wareid(session('user_auth.uid'));  //取得该用户的仓库id（管理员仓库都是1）
        
        // 筛选仓库归属
        $map['warehouse'] = I('inv') ? I('inv') : array('like', '"%%"');
        if ($wareid!='1') { $map['warehouse'] = $wareid; }     //根据用户调出相应仓库列表

        // if (I('get.warning') == '1') {
        //     $map['num'] = array('exp','< min_num OR (num > max_num AND max_num != 0)');  //报警的筛选条件
        // }
        if (I('get.warning') == '1') {
            $map['warn'] = true;  //报警的筛选条件
        }

        $main['table'] = "warehouse";  // 主表
        $main['field'] = "sid";        // 主副表对应字段
        $main['order'] = I('get.orderMain') ? I('get.fieldMain')." ".I('get.orderMain') : "";
        $sub['table']  = "stock";  // 关联副表
        $sub['field']  = 'id';     // 主副表对应字段
        $sub['order']  = I('get.orderSub') ? I('get.fieldSub')." ".I('get.orderSub') : "";  //排序规则
        $default_order = "app_warehouse.warehouse,app_stock.category asc,app_stock.name asc";

        $list = D('Stock')->listEx($main, $sub, $map, $default_order);

        $page = $list['show'];
        unset($list['show']);

        $this->assign('cat_pro', $cat_pro['_']);
        $this->assign('sup_list', $sup_list);
        $this->assign('users',$users);
        $this->assign('type',$type);

        $this->assign('_list', $list);
        $this->assign('_page', $page);
        $this->meta_title = '查看库存';
        $this->display();
    }


    /**
     * 客户选择产品出库 - 销售管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/11/03
     */
    public function out_in() {
        if(IS_GET){
            $id = I('get.id',0,'intval');
            if(!in_array($id, $_SESSION[C('SESSION_PREFIX')]['out_in'])){
                $_SESSION[C('SESSION_PREFIX')]['out_in'][$id] = $id;
            } else {
                $this->error('请勿重复添加产品。');
            }
            $this->success('成功将产品添加出货列表，请全部添加完毕后提交确认生成正式出库单。');
        }         
    }


    /**
     * 库存退货 - 库存管理
     * @author echoshiki <echoshiki@outlook.com>
     * 提交编码 —— 查询产品详细信息 —— 避免多供应商、已添加产品、库存未知等情况 —— 添加进session 
     * 勾选产品 —— 查询出session对应位置 —— 删除对应session
     * 2015/09/02
     */
    public function free_return() {
        if (IS_POST) {
            $data = I('post.');
            $info = D('stock')->field('id,supplier')->where(array('code'=>$data['code']))->find();   // 获取添加的产品信息

            if ($info['id'] == '') { $this->error('库存中没有该编号的产品。'); }
            if (in_array($info['id'],$_SESSION[C('SESSION_PREFIX')]['returned'])) {   // 判断session中是否存在改产品 
                $this->error('该编号的产品已经添加进列表。');
            }
            if (!in_array($info['supplier'],$_SESSION[C('SESSION_PREFIX')]['returned_supplier']) && current($_SESSION[C('SESSION_PREFIX')]['returned_supplier']) != '') {
               // 判断供应商session里的id和此id不一样的话，报错  *在第一次添加后进行判断
               $this->error('一份采购退货单对应一个供应商，请勿添加多供应商产品。');
            }
            $_SESSION[C('SESSION_PREFIX')]['returned'][] = $info['id'];   // 将输入的退货产品id写进session保存
            $_SESSION[C('SESSION_PREFIX')]['returned_supplier'][] = $info['supplier'];   // 将输入的退货产品所属的供应商id写进session保存

            $this->success('添加成功');
        }

        if (I('get.sid')) {
            // 从session里清除选中的产品
            $pos = array_search(I('get.sid'), $_SESSION[C('SESSION_PREFIX')]['returned']);  // 获得在session里的位置
            unset($_SESSION[C('SESSION_PREFIX')]['returned'][$pos]);  // 删除对应位置的产品id
            unset($_SESSION[C('SESSION_PREFIX')]['returned_supplier'][$pos]);  //删除对应位置的产品所属供应商id
            $this->success('清除成功',U('Psi/free_return'));
        }

        if (current($_SESSION[C('SESSION_PREFIX')]['returned']) != '') {    //存在退货产品时进行操作
            $sids = implode($_SESSION[C('SESSION_PREFIX')]['returned'], ',');
            $map['id'] = array('in',$sids);
            $list = D('stock')->lists($map);
            unset($list['show']);
            foreach ($list as $key => $value) {
               $where['sid'] = $value['id'];
               $where['warehouse'] = 1;
               $ware = D('warehouse')->field('num')->where($where)->find();   //查询出改产品库存情况
               $list[$key]['ware_num'] = $ware['num'];
            }
        }
        $this->assign('_list', $list);
        $this->assign('_page', $page);
        $this->meta_title = '库存退货';
        $this->display();
    }


    public function free_do() {
         if (IS_POST) {
            /* 插入退货单据表主表 */
            $Re   = D('returned');
            $data =  $Re->create();
            if(!$data){ $this->error($Re->getError()); }
            $data['date']   = strtotime($data['date']);  //转换成时间戳
            $data['status'] = 1;  
            $data['time']   = time();
            $data['from']   = 3;  //类型为采购退货
            $data['user']   = session('user_auth.uid');
            $fid = $Re->add($data);

            /* 插入退货单据表副表、库存表、出入库记录表 */
            $list  = I('post.info');
            foreach ($list as $key => $value) {
                if ($value['num'] == 0) { continue; }  //数量为零跳过
                $value['fid'] = $fid;
                D('returned_info')->add($value);    //插入退货单据副表 
                D('warehouse')->pull($value['code'], 1, $value['num'], '', 3);  //出库并生成退货记录 warehouse表 record表
            }
            unset($_SESSION[C('SESSION_PREFIX')]['returned']);
            unset($_SESSION[C('SESSION_PREFIX')]['returned_supplier']);

            $this->success('本期退货成功！',U('Psi/free_return'));
        } else {

            if (current($_SESSION[C('SESSION_PREFIX')]['returned']) == '') { $this->error('非法操作，请返回。'); }
            $sids      = implode($_SESSION[C('SESSION_PREFIX')]['returned'], ',');
            $map['id'] = array('in',$sids);
            $list      = D('stock')->lists($map);
            unset($list['show']);
            foreach ($list as $key => $value) {
               $where['sid'] = $value['id'];
               $where['warehouse'] = 1;
               $ware = D('warehouse')->field('num')->where($where)->find();   //查询出改产品库存情况
               $list[$key]['ware_num'] = $ware['num'];
            }

            $list['supplier'] = D('supplier')->find($list[0]['supplierid']);
            
            $this->assign('_list', $list);
            $this->meta_title = '库存退货';
            $this->display();
        }         
    }


    /**
     * 设置预警量 - 库存管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/08/17
     */
    public function warning() {
        if (IS_POST) {
            $Ware = D('warehouse');
            $id   = I('post.ids');
            $ids  = explode(',', $id);
            if ($ids[0] == 0) { $this->error('你并没有选择任何产品,请返回勾选后再试。'); }
            $map['sid'] = array('in',$ids);
            $map['warehouse'] = D('warehouse')->wareid(session('user_auth.uid'));
            $data = $Ware->create($_POST);
            if (!$data) { $this->error($Ware->getError()); }
            $Ware->where($map)->save($data);
            $this->success("预警量设置成功！", U('Psi/inventory'));
        } else {
            $id   = array_unique((array)I('id',0));
            $ids  = explode(',', $id[0]);
            if ( count($ids) == 1 ) {  //单独设置情况（有值
                $map['sid'] = $ids[0];
                $map['warehouse'] = D('warehouse')->wareid(session('user_auth.uid'));
                $vo = D('warehouse')->where($map)->field('min_num,max_num')->find();
                $min_num = $vo['min_num'];
                $max_num = $vo['max_num'];
            } else {  //批量设置情况
                $min_num = 0;
                $max_num = 0;
            }

            $this->assign('_min_num',$min_num);
            $this->assign('_max_num',$max_num);
            $this->assign('_ids', $id[0]);
            $this->meta_title = '设置预警量';
            $this->display();
        }  
    }


    /**
     * 库存调节 - 库存管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/11/23
     */
    public function ware_set() {
        if (IS_POST) {
            $Ware = D('warehouse');
            $id   = I('post.ids');
            $ids  = explode(',', $id);
            if ($ids[0] == 0) { $this->error('你并没有选择任何产品,请返回勾选后再试。'); }
            $map['sid'] = array('in',$ids);
            $map['warehouse'] = D('warehouse')->wareid(session('user_auth.uid'));
            $data = $Ware->create($_POST);
            if (!$data) { $this->error($Ware->getError()); }
            $Ware->where($map)->save($data);
            $this->success("手动设置库存量设置成功！", U('Psi/inventory'));
        } else {
            $id   = array_unique((array)I('id',0));
            $ids  = explode(',', $id[0]);
            if ( count($ids) == 1 ) {  //单独设置情况（有值
                $map['sid'] = $ids[0];
                $map['warehouse'] = D('warehouse')->wareid(session('user_auth.uid'));
                $vo = D('warehouse')->where($map)->field('num')->find();
                $num = $vo['num'];
            }
            $this->assign('_num',$num);
            $this->assign('_ids', $id[0]);
            $this->meta_title = '设置库存量';
            $this->display();
        }  
    }


    /** 
     * 操作记录 - 库存管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/03/25
     */
    public function record() {
        $Record = D('Record');
        $map    = "id!=''"; 
        if (I("get.code")) { $map .= " AND code='".I("get.code")."'"; }   //搜索

        $list   = $Record->lists(session('user_auth.uid'),"",$map);
        $th     = $list['th'];   //判断角色应该看到的列表选项（区分管理员和加盟商）
        $mode   = $list['mode']; //判断角色应该看到的列表选项（区分管理员和加盟商）
        $page   = $list['show']; //传入分页样式
        unset($list['th']);    //前端不会多出一行
        unset($list['mode']);
        unset($list['show']);

        $this->assign('_list', $list);
        $this->assign('_th', $th);
        $this->assign('mode', $mode);
        $this->assign('_page', $page);
        $this->meta_title = '操作记录';
        $this->display();
    }


    /** 
     * 包装品操作记录 - 库存管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/09/08
     */
    public function exstock_record() {

        C('LIST_ROWS',30);
        $list = $this->lists('exstock_record',$map);

        $this->assign('_list', $list);
        $this->meta_title = '包装品记录';
        $this->display();
    }


    /**
     * 销售出库 - 销售管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/08/18
     */
    public function out_order() {
        if (session('user_auth.group_id') != C('JOIN_GROUP')){ 
            $map['order_id'] = array('like', '%'.I('post.no').'%');
            C('LIST_ROWS',30);
            $list  = $this->lists('order',$map);
            $this->assign('_list',$list);
            $this->meta_title = '销售出库';
            $this->display(); 
        } else {
            // 加盟商出库页
             
            $this->meta_title = '销售出库';
            $this->display('out_customer'); 
        }        
    }


    /**
     * 进行出库 - 销售管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/08/03
     */
    public function out_do() {
        if (IS_POST) {

            /* 插入出库单据表主表 */
            $Out  = D('out');
            $data =  $Out->create();
            if(!$data){ $this->error($Out->getError()); }
            $data['status'] = 1;  
            $data['time']   = time();
            $data['date']   = strtotime($data['date']);   //转成时间戳
            $data['from']   = 1;  //类型为销售出库
            $data['user']   = session('user_auth.uid'); 
            $list  = I('post.info');
            $sum = 0;   // 计算总量 包装品
            foreach ($list as $key => $value) {
                /* 验证数据 */
                if ($value['num'] == '') { $this->error('请完整填写本期出货数，如不出货请填写0。'); }
                if ($value['box_no'] == '') { $this->error('请完整填写箱号。'); }
                // if ($value['num'] > $value['ware']) { $this->error('填写的出货数超过了库存量，请重写填写。'); }

                $sum += $value['num'];  // 计算总量 包装品
            }

            /* 处理包装品 包括库存增减，记录生成，存入出库单等 */
            $tmp1 = I('post.exstock');
            $tmp2 = D('exstock')->compare($sum);  // 格式化出固定属性包装品数组 id=>num
            $exstock = array_replace($tmp1,$tmp2);

            // 计算总量 包装品 组合数组
            foreach ($exstock as $key => $value) {
                $res = D('exstock')->pull($key, $value, '2', $data['no']);
                if (!$res) { $this->error('包装品模块出现错误。'); }
            }
            $data['exstock'] = serialize(I('post.exstock'));  // 包装品
            $fid = $Out->add($data);            

            /* 插入出库单据表副表，更新库存表、出入库记录表 */
            foreach ($list as $key => $value) { 
                if ($value['num'] == 0) { continue; }  //数量为零跳过
                $value['fid'] = $fid;
                D('out_info')->add($value);    //插入出库单据副表

                //出库并生成记录 warehouse表 record表  
                $res = D('warehouse')->pull($value['code'], 1, $value['num'], $data['from_id']); 
                if (!$res) { $this->error('出库模块出现错误1。'); }

                //出库对象（加盟商）库存相应增加
                $res = D('warehouse')->push($value['code'], $data['customer'], $value['num'], $data['from_id'], 5);
                if (!$res) { $this->error('入库模块出现错误。'); }
            }

            /* 更新订单状态 已出货 order:status */
            $map['id'] = $data['from_id'];
            $update['status'] = 3;  
            D('order')->where($map)->save($update);

            /* 更新订单追踪流程 已出货 order_process */
            $info_process['date']    = time();
            $info_process['oid']     = $data['from_id'];
            $info_process['process'] = "订单已出库";
            $info_process['userid']  = session('user_auth.uid');
            $res = D('OrderProcess')->add($info_process);
            if (!$res) { $this->error("插入流程表失败，请检查数据库后再试。"); }

            $this->success('本期出库成功！',U('Psi/out_order'));
        } else {
            $id   = I("get.id");  //订购单id
            $list = D('order')->info($id);

            $map['type'] = 2; 
            $ex_list = D('exstock')->lists($map);  //非固定属性包装品
            unset($ex_list['show']);

            $this->assign('_list',$list);
            $this->assign('_page',$page);
            $this->assign('_ex_list',$ex_list);

            $this->meta_title = '订单出库';
            $this->display();  
        }         
    }


    /**
     * 客户出库 - 销售管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/11/03
     */
    public function out_do_customer() {
        if (IS_POST) {

            /* 插入出库单据表主表 */
            $Out  = D('out');
            $data =  $Out->create();
            if(!$data){ $this->error($Out->getError()); }
            
            $data['status']  = 1;  
            $data['time']    = time();
            $data['date']    = strtotime($data['date']);   //转成时间戳
            $data['from']    = 3;  //类型为客户出库
            $data['from_id'] = 0;  
            $data['user']    = session('user_auth.uid'); 

            $list  = I('post.info');
            foreach ($list as $key => $value) {
                /* 验证数据 */
                if ($value['num'] == '') { $this->error('请完整填写本期出货数。'); }
                if ($value['num'] > $value['ware']) { $this->error('填写的出货数超过了库存量，请重写填写。'); }
            }

            $fid = $Out->add($data);            

            /* 插入出库单据表副表，更新库存表、出入库记录表 */
            foreach ($list as $key => $value) { 
                if ($value['num'] == 0) { continue; }  //数量为零跳过
                $value['fid'] = $fid;
                D('out_info')->add($value);    //插入出库单据副表

                //出库并生成记录 warehouse表 record表  
                $res = D('warehouse')->pull($value['code'], session('user_auth.uid'), $value['num'], "", 6); 
                if (!$res) { $this->error('出库模块出现错误。'); }
            }
            unset($_SESSION[C('SESSION_PREFIX')]['out_in']);

            $this->success('本期出库成功！',U('Psi/out_list'));
        } else {
            if (I('get.id')) {
                // 删除订单中的产品
                $ids = I('get.id'); 
                $ids = explode(',', $ids);
                foreach ($ids as $key => $value) {
                    unset($_SESSION[C('SESSION_PREFIX')]['out_in'][$value]);
                }
                $this->success("取消产品成功！".$str);
            }

            $out   = session('out_in') ? session('out_in') : "";
            $ids   = implode($out, ',');
            $map['sid']       = array('in',$ids);
            $map['warehouse'] = session('user_auth.uid');
            $order = "id DESC";
            $list  = D("warehouse")->lists($map,$order);
            $page  = $list['show'];
            unset($list['show']);

            $auth = D('ucenter_member')->field('id,username,email,mobile,company,truename,address')->find(session('user_auth.uid'));

            $this->assign('_list',$list);
            $this->assign('_auth',$auth);
            $this->assign('_page',$page);

            $this->meta_title = '订单确认';
            $this->display();
        }         
    }


    /**
     * 出库一览 - 销售管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/08/26
     */
    public function out_list() {
        if (session('user_auth.group_id') != C('JOIN_GROUP')){
            /* 关键词模糊搜索 START 2015/08/26 */
            $type = 9; //默认搜索type值        
            $ask  = I("ask") ? I("ask") : ""; 
            if ($ask == 1) {
                $type     = I("type") ? I("type") : $type;   //搜索类型
                $keyword  = I("keyword") ? I("keyword") : ""; 
                $start    = I("time-start") ? strtotime(I("time-start")) : 0;
                $end      = I("time-end") ? strtotime(I("time-end")) : 9999999999;
                if ($type == 10) { $map['no'] = array('like', '%'.$keyword.'%'); }
                if ($type == 9)  { $map['from_no'] = array('like', '%'.$keyword.'%'); }
                $map['date'] = array('between',array($start,$end));  //采购时间
            }  
            /* 关键词模糊搜索 END 2015/08/26 */ 
            $map['customer'] = I('inv') ? I('inv') : array('like', '%%');

            C('LIST_ROWS',30);
            $order = I('get.orderby') ? I('get.field')." ".I('get.orderby') : "id desc";
            $list  = $this->lists('out',$map,$order);
            $users = D('Member')->getCustomers();

            $this->assign('type',$type);
            $this->assign('users',$users);
            $this->assign('_list',$list);
            $this->meta_title = '出库一览';
            $this->display();   
        } else {
            /* 关键词模糊搜索 START 2015/08/26 */
            $type = 9; //默认搜索type值        
            $ask  = I("ask") ? I("ask") : ""; 
            if ($ask == 1) {
                $type     = I("type") ? I("type") : $type;   //搜索类型
                $keyword  = I("keyword") ? I("keyword") : ""; 
                $start    = I("time-start") ? strtotime(I("time-start")) : 0;
                $end      = I("time-end") ? strtotime(I("time-end")) : 9999999999;
                if ($type == 10) { $map['no'] = array('like', '%'.$keyword.'%'); }
                if ($type == 9)  { $map['from_no'] = array('like', '%'.$keyword.'%'); }
                $map['date'] = array('between',array($start,$end));  //采购时间
            }  
            /* 关键词模糊搜索 END 2015/08/26 */ 
            $map['from'] = 3; // 客户出库
            $map['user'] = session('user_auth.uid');  // 客户

            C('LIST_ROWS',30);
            $order = I('get.orderby') ? I('get.field')." ".I('get.orderby') : "id desc";
            $list  = $this->lists('out',$map,$order);
            $this->assign('type',$type);
            $this->assign('_list',$list);
            $this->meta_title = '出库一览';
            $this->display("out_list_customer");             
        }
    }


    /**
     * 出库单据详情 - 库存管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/08/26
     */
    public function out_info() {
        $id = I("get.id"); 
        $Out = D('out');
        $list = $Out->show($id);
        $this->assign('_list',$list);
        $this->assign('_page',$page);
        $this->meta_title = '出库详单';
        if (session('user_auth.group_id') != C('JOIN_GROUP')){
            if ($_GET['print'] == '1') { $this->display('out_print'); return false; }
            $this->display();    
        } else {
            $this->display("out_info_customer"); 
        }
    }   


    /**
     * 出库单删除 - 销售管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/08/26
     */
    public function out_del() {
        $id       = array_unique((array)I('id',0)); 
        if ($id[0] == 0) { $this->error('未选中任何出库单。'); }
        $map['id']  = array('in',$id);
        D('out')->del($map);
        $this->success('出库单删除成功！');        
    }


    /**
     * 特殊出库 - 销售管理管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/04/02
     */
    public function sale() {
        if(IS_POST){
            $Record = D('Record');
            $Ware   = D("Warehouse");
            
            $data   = $Record->create($_POST,2);
            if (!$data){ $this->error($Record->getError()); }
            if (!$data['custom'] && !$data['joiner']) { $this->error("请选择出库对象。"); }
            $warehouse = $Ware->wareid(session('user_auth.uid'));
            $res = $Ware->out($data, $warehouse);
            if (!$res) {  $this->error('产品出库失败！请检查数据库连接。'); }
            $this->success('产品出库成功！');

        } else {
            $AuthGroup = D("AuthGroup");
            $joiners   = $AuthGroup->memberInGroup(C('JOIN_GROUP'));  //获取加盟商用户组用户列表
            
            $this->assign('_joiner', $joiners);
            $this->meta_title = '销售出库';
            $this->display();
        }         
    }


    /**
     * 订单一览 - 销售管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/04/13
     */
    public function order() {
        if (IS_POST) {
            // 客户确认订单  AJAX  
            $data['id']     = I('post.id'); 
            $data['status'] = 2;   //已经确认
            D('Order')->save($data);    
            $process['status']  = 2;  
            $process['date']    = time(); //编辑时间
            $process['oid']     = $data['id'];
            $process['process'] = "订购客户确认订单。";
            $process['userid']  = session('user_auth.uid');
            D('OrderProcess')->add($process);
            echo "确认订单成功，我们将尽快给您发货。";
            return false;
        }

        /* 关键词模糊搜索 START 2015/08/26 */
        $type = 9; //默认搜索type值        
        $ask  = I("ask") ? I("ask") : ""; 
        if ($ask == 1) {
            $type     = I("type") ? I("type") : $type;   //搜索类型
            $keyword  = I("keyword") ? I("keyword") : ""; 
            $start    = I("time-start") ? strtotime(I("time-start")) : 0;
            $end      = I("time-end") ? strtotime(I("time-end")) : 9999999999;
            if ($type == 11) { $map['tel'] = array('like', '%'.$keyword.'%'); }
            if ($type == 9)  { $map['order_id'] = array('like', '%'.$keyword.'%'); }
            if ($type == 14) { $map['name'] = array('like', '%'.$keyword.'%'); }
            if ($type == 15) {
                $mapII['username'] = array('like', '%'.$keyword.'%');
                $result = M('ucenter_member')->field('id')->where($mapII)->select();
                foreach ($result as $key => $value) { $arr[] = $value['id']; }
                $string = implode($arr, ',');
                $map['uid'] = array('in',$string);
            }
            $map['date'] = array('between',array($start,$end));  //采购时间
        }  
        /* 关键词模糊搜索 END 2015/08/26 */ 

        $uid = D('Warehouse')->wareid(session('user_auth.uid'));  //取得该用户的仓库id（管理员仓库都是1）
        // 筛选仓库归属
        if ($uid!='1') { $map['uid'] = $uid; }     //根据用户调出相应仓库列表

        $list   = $this->lists('Order',$map);
        foreach ($list as $key => $value) {
            $list[$key]['money'] = D("Order")->getMoney($value['id']);
        }

        $this->assign('type',$type);
        $this->assign('uid',$uid);
        $this->assign('_list',$list);
        $this->meta_title = '订单一览';
        $this->display();
    }


    /**
     * 订单编辑 - 销售管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/10/29
     */
    public function order_edit() {
        if (IS_POST) {
            $Order = D("Order");
            $data  = $Order->create($_POST);
            if(!$data){ $this->error($Order->getError()); }

            $data['goods']    = serialize($data['goods']);  //订单产品详情（id x num）
            $data['status']   = 1;   //变回未确认状态
            $Order->save($data);             

            $process['status'] = 1;  
            $process['date']   = time(); //编辑时间
            $process['oid']     = $data['id'];
            $process['process'] = "编辑订单";
            $process['userid']  = session('user_auth.uid');
            D('OrderProcess')->add($process);

            $this->success('订单编辑成功！请再次让订单客户进行确认。');
        }
        $id   = I('get.id');
        $info = D("Order")->info($id);
        $info['process'] = D('OrderProcess')->lists($id);

        $this->assign('info',$info); 
        $this->meta_title = '订单详细';
        $this->display();
    }


    /**
     * 订单详情 - 销售管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/04/15
     */
    public function order_info() {
        $id   = I('get.id');
        $info = D("Order")->info($id);
        $info['process'] = D('OrderProcess')->lists($id);

        $this->assign('info',$info); 
        $this->meta_title = '订单详细';
        if ($_GET['print'] == '1') { $this->display('order_print'); return false; }
        $this->display();
    }


    /**
     * 订单流程 - 销售管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/04/20
     */
    public function order_process() {
        if(IS_POST){    
            $Process = D('OrderProcess'); 
            $data    = $Process->create($_POST);
            if (!$data){ $this->error($Process->getError()); }

            $info_process['date']    = $data['date'] ? strtotime($data['date']) : time();
            $info_process['oid']     = $data['oid'];
            $info_process['status']  =  I('post.status');
            $info_process['process'] = $data['process'];
            $info_process['userid']  = session('user_auth.uid');

            $info_order['status'] = I('post.status');

            $res = $Process->add($info_process);
            if (!$res) { $this->error("插入流程表失败，请检查数据库后再试。"); }
            D('Order')->where('id='.$data['oid'])->save($info_order);
            $this->success('订单流程添加成功！');

        } else {
            $id = I('get.id'); 
            $status['list'] = C("ORDER");
            $status['now']  = D('Order')->field('status')->find($id);
            $status['now']  = $status['now']['status'];

            $Process = D('OrderProcess');
            $list = $Process->lists($id);

            $this->assign('_list',$list);
            $this->assign('_status',$status);
            $this->assign('_id',$id);
            $this->meta_title = '添加流程';
            $this->display();
        }
    }


    /**
     * 订单删除 - 销售管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/08/26
     */
    public function order_del() {
        $id       = array_unique((array)I('id',0)); 
        if ($id[0] == 0) { $this->error('未选中任何订购单。'); }
        $map['id']  = array('in',$id);
        D('order')->del($map);
        $this->success('订购单删除成功！');        
    }


    /**
     * 订单退货 - 订单管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/08/31
     */
    public function order_return() {
        if (IS_POST) {
            /* 插入退货单据表主表 */
            $Re   = D('returned');
            $data =  $Re->create();
            if(!$data){ $this->error($Re->getError()); }
            $data['date']   = strtotime($data['date']);  //转换成时间戳
            $data['status'] = 1;  
            $data['time']   = time();
            $data['from']   = 2;  //类型为销售退货
            $data['user']   = session('user_auth.uid');

            $list = I('post.info');
            
            $sum  = 0;   // 计算总量 包装品
            foreach ($list as $key => $value) {
                /* 验证数据 */
                if ($value['num'] == '') { $this->error('请完整填写本期退货数，如不出货请填写0。'); }
                $sum += $value['num'];  // 计算总量 包装品
            }

            /* 处理包装品 包括库存增减，记录生成，存入出库单等 */
            $tmp1 = I('post.exstock');
            $tmp2 = D('exstock')->compare($sum);  // 格式化出固定属性包装品数组 id=>num
            $exstock = array_replace($tmp1,$tmp2);

            // 计算总量 包装品 组合数组
            foreach ($exstock as $key => $value) {
                $res = D('exstock')->push($key, $value, '1', $data['no']);
                if (!$res) { $this->error('包装品模块出现错误。'); }
            }
            $data['exstock'] = serialize(I('post.exstock'));  // 包装品

            $fid = $Re->add($data);
            /* 插入退货单据表副表、更新订单表状态、库存表(两方：主加客减)、出入库记录表 */
            $list  = I('post.info');
            foreach ($list as $key => $value) {
                if ($value['num'] == 0) { continue; }  //数量为零跳过
                $value['fid'] = $fid;
                D('returned_info')->add($value);    //插入退货单据副表
                //退货入库并生成退货记录 warehouse表 record表
                D('warehouse')->push($value['code'], 1, $value['num'], $data['from_id'], 4);   // 4:销售退货
                //出库对象（加盟商）库存相应减少
                D('warehouse')->pull($value['code'], $data['pid'], $value['num'], $data['from_id'], 6);   // 6:客户出库
            }

            /* 更新订单状态 已出货 order:status */
            $map['id'] = $data['from_id'];
            $update['status'] = 7;  // 退货中
            D('order')->where($map)->save($update);

            /* 更新订单追踪流程 退货处理中 order_process */
            $info_process['date']    = time();
            $info_process['oid']     = $data['from_id'];
            $info_process['process'] = "该订单部分商品退货处理中。退货单号: ".$data['no'];
            $info_process['userid']  = session('user_auth.uid');
            $res = D('OrderProcess')->add($info_process);
            if (!$res) { $this->error("插入流程表失败，请检查数据库后再试。"); }

            $this->success('本期退货成功！',U('Psi/return_list'));
        } else {
            $id   = I("get.id");   //订购单id
            $list = D('order')->show($id);

            $map['type'] = 2; 
            $ex_list = D('exstock')->lists($map);  //非固定属性包装品
            unset($ex_list['show']);

            $this->assign('_list',$list);
            $this->assign('_page',$page);
            $this->assign('_ex_list',$ex_list);
            $this->meta_title = '订单退货';
            $this->display();  
        }         
    }


    /**
     * 出库记录 - 销售管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/04/20
     */
    public function sale_record() {
        $Record = D('Record');
        $map    = "id!=''"; 
        if (I("get.code")) { $map .= " AND code='".I("get.code")."'"; }   //搜索

        $list   = $Record->lists(session('user_auth.uid'),2,$map);  //入库记录
        $th     = $list['th'];   //判断角色应该看到的列表选项（区分管理员和加盟商）
        $mode   = $list['mode']; //判断角色应该看到的列表选项（区分管理员和加盟商）  
        $page   = $list['show']; //传入分页样式

        unset($list['show']);          
        unset($list['th']);    //前端不会多出一行
        unset($list['mode']);

        $this->assign('_list', $list);
        $this->assign('_th', $th);
        $this->assign('mode', $mode);
        $this->assign('_page', $page);

        $this->meta_title = '出库记录';
        $this->display();
    }


    /**
     * 添加产品 - 产品管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/04/02
     */
    public function stock_add() {
        if(IS_POST){
            $Stock      = D("Stock");

            $data       = $Stock->create($_POST,1);
            if(!$data){ $this->error($Stock->getError()); }
            if(!$data['status']) { $data['status'] = array('1'); }
            $data['date']       = time();  //录入时间
            $data['standard']   = serialize(I('post.car'));  //序列化适用车型，存进数据库
            $data['status'] = implode('9',$data['status']);   // 促销、新品
            $thumbs = I('post.thumb');
            foreach ($thumbs as $key => $value) {
                if ($key > 5) { break; }
                $thumb = thumb_move($value);
                if(!$thumb) { $this->error('上传文件出现错误(0);'); }
                $tmp[] = $thumb;
            }
            $data['thumb_1'] = serialize($tmp);  // 序列化图片组
            $data['pinyin'] = pinyin($data['name']);  // 自动生成首字母组合
            $res = $Stock->add($data);

            if (!$res) {  $this->error('添加产品失败！请检查数据库连接。'); }
            $this->success('添加产品成功！');
        } else {
            $Category   = D("Category");
            $cat_pro    = $Category->getTree('39','id,title,pid');  //获取商品分类
            $cat_car    = $Category->getTree('47','id,title,pid');  //获取车型列表
            $sup_list   = D("Supplier")->field('id,company')->select();  //供应商列表
            
            $this->assign('cat_pro', $cat_pro['_']);
            $this->assign('cat_car', $cat_car['_']);
            $this->assign('sup_list', $sup_list);
            $this->meta_title = '添加产品';
            $this->display();
        }
    }


    /**
     * 产品目录 - 产品管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/03/25
     */
    public function stock() {

        $cat_pro  = D("Category")->getTree('39','id,title,pid');  //获取商品分类
        $sup_list = D("Supplier")->where($where)->field('id,company')->select();  //供应商列表

        /* 关键词模糊搜索 START 2015/07/10 */         
        $ask = I("ask") ? I("ask") : ""; 
        if ($ask == 1) {
            $type     = I("type") ? I("type") : "";   //搜索类型
            $keyword  = I("keyword") ? I("keyword") : ""; 
            switch ($type) {
                case 1:  $search = "code";  break;
                case 2:  $search = "name";  break;
                case 3:  $search = "OE";    break; 
                default: $search = "name";  break;
            }
            if ($search == 'name') {
                $sub['name']   = array('like', '%'.$keyword.'%');
                $sub['pinyin'] = array('like', '%'.$keyword.'%');
                $sub['_logic'] = 'or';
                $where['_complex'] = $sub;
            } else {
                $where[$search] = array('like', '%'.$keyword.'%');
            }
            
        }       
        /* 关键词模糊搜索 END 2015/07/10 */ 

        /* 分类、供应商筛选START 2015/07/13 */
            $where['category'] = I('category') ? I('category') : array('like', '%%');
            $where['supplier'] = I('supplier') ? I('supplier') : array('like', '%%');
            $where['status']   = I('status') ? array('like','%'.I('status').'%') : array('like', '%%');
        /* 分类、供应商筛选END 2015/07/13 */

        $field    = "id,code,name,category,supplier,standard,OE,sale_price,sale_price_off,status";

        if (I('get.orderby')) {
            $order = I('get.field')." ".I('get.orderby');
        } else {
            $order = "category asc,name asc";
        }

        $list     = D("Stock")->lists($where,$order,$field);
        $page     = $list['show'];
        unset($list['show']);
        $this->assign('cat_pro', $cat_pro['_']);
        $this->assign('sup_list', $sup_list);
        $this->assign('_list',$list);
        $this->assign('_page',$page);
        $this->assign('type',$type);
        $this->meta_title = '产品管理';
        $this->display();
    }


    /**
     * 产品详细 - 产品管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/04/01
     */
    public function stock_info() {
        $Stock    = D("Stock");

        $id   = I('get.id');
        $info = $Stock->info($id);

        $this->assign('info',$info);
        $this->meta_title = '产品详细';
        $this->display();
    }

    /**
     * 产品修改 - 产品管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/04/01
     */
    public function stock_edit() {
        $Stock      = D("stock");
        $Category   = D("Category");
        $Supplier   = D("Supplier");

        if(IS_POST){
            $id   = I('post.id');
            $data = $Stock->create($_POST,2);
            if(!$data){ $this->error($Stock->getError()); }
            $data['standard'] = serialize(I('post.car'));  //序列化适用车型，存进数据库

            $thumbs = I('post.thumb');
            foreach ($thumbs as $key => $value) {
                if ($key > 2) { break; }
                $thumb = thumb_move($value);
                if(!$thumb) { $this->error('上传文件出现错误(0);'); }
                $tmp[] = $thumb;
            }
            $data['thumb_1'] = serialize($tmp);
            $data['status'] = implode($data['status'], '9');   // 促销、新品
 
            $res = $Stock->where("id=".$id)->save($data);
            if (!$res) {  $this->error('修改产品失败！请检查数据库连接。'); }
            $this->success('修改产品成功！');
        } else {  
            $id   = I('get.id');
            $info = $Stock->info($id);
            $info['car'] = explode('|', $info['standard']);
            $info['status'] = explode('9', $info['status']);
            $cat_pro     = $Category->getTree('39','id,title,pid');  //获取商品分类
            $cat_car     = $Category->getTree('47','id,title,pid');  //获取车型列表
            $sup_list    = $Supplier->field('id,company')->select();  //供应商列表
            $this->assign('cat_pro', $cat_pro['_']);
            $this->assign('cat_car', $cat_car['_']);
            $this->assign('sup_list', $sup_list);
            $this->assign('info',$info);

            $this->meta_title = '产品详细';
            $this->display();
        }
    }


    /**
     * 删除产品 - 产品管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/04/02
     */
    public function stock_del() {
        $Stock      = D("Stock");
        $id         = array_unique((array)I('id',0)); 
        if ($id[0] == 0) { $this->error('未选中任何产品。'); }
        $map['id']  = array('in',$id);
        $Stock->where($map)->delete();
        $this->success('产品删除成功！');
    }


    /**
     * 添加包装品 - 包装品管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/09/08
     */
    public function exstock_add() {
        if(IS_POST){
            $Ex   = D("Exstock");
            $data = $Ex->create($_POST,1);
            if(!$data){ $this->error($Ex->getError()); }
            $data['status'] = 1;
            $data['num']    = $data['num'] ? $data['num'] : 0;  // 初始库存
            $data['date']   = time();  // 添加时间
            $res = $Ex->add($data);
            if (!$res) {  $this->error('添加包装品失败！请检查数据库连接。'); }
            $this->success('添加包装品成功！',U('Psi/exstock'));
        } else {
            $this->meta_title = '添加包装品';
            $this->display();
        }
    }


    /**
     * 包装品目录 - 包装品管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/09/08
     */
    public function exstock() {
        $list  = D("Exstock")->lists($where,"id DESC");
        $page  = $list['show'];
        unset($list['show']);

        $this->assign('_list',$list);
        $this->assign('_page',$page);

        $this->meta_title = '包装品管理';
        $this->display();
    }


    /**
     * 包装品入库 - 包装品管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/09/08
     */
    public function exstock_in() {
        if(IS_POST){
            $info = I('post.');
            $res  = D('Exstock')->push($info['id'], $info['num_in']);         
            if (!$res) {  $this->error('包装品入库失败！请检查数据库连接。'); }
            $this->success('包装品入库成功！');
        } else {  
            $id   = I('get.id');
            $info = D("Exstock")->find($id);
            $this->assign('info',$info);
            $this->meta_title = '包装品入库';
            $this->display();
        }
    }


    /**
     * 包装品详细 - 包装品管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/09/09
     */
    public function exstock_info() {
        $id   = I('get.id');
        $info = D('exstock')->find($id);

        $this->assign('info',$info);
        $this->meta_title = '包装品详细';
        $this->display();
    }


    /**
     * 包装品修改 - 包装品管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/09/08
     */
    public function exstock_edit() {
        if(IS_POST){
            $Ex   = D("Exstock");
            $data = $Ex->create(I('post.'),2);
            if(!$data){ $this->error($Ex->getError()); }
            $res = $Ex->save($data);
            if (!$res) {  $this->error('修改包装品失败！请检查数据库连接。'); }
            $this->success('修改包装品成功！');
        } else {  
            $id   = I('get.id');
            $info = D("Exstock")->find($id);
            $this->assign('info',$info);
            $this->meta_title = '包装品详细';
            $this->display();
        }
    }


    /**
     * 删除包装品 - 包装品管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/09/08
     */
    public function exstock_del() {
        $Ex = D("Exstock");
        $id = array_unique((array)I('id',0)); 
        if ($id[0] == 0) { $this->error('未选中任何包装品。'); }
        $map['id']  = array('in',$id);
        $Ex->where($map)->delete();
        $this->success('包装品删除成功！');
    }


    /**
     * 订购产品 - 销售管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/04/13
     */
    public function cart_in() {
        if(IS_GET){
            $id = I('get.id',0,'intval');
            if(!in_array($id, $_SESSION[C('SESSION_PREFIX')]['cart_in'])){
                $_SESSION[C('SESSION_PREFIX')]['cart_in'][$id] = $id;
            } else {
                $this->error('请勿重复添加产品。');
            }
            $this->success('成功将产品添加进订购单，请进入订购单确认后生成正式订单。');
        }         
    }


    /**
     * 订单确认 - 销售管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/04/14
     */
    public function cart() {
        if(IS_POST){
            if (!$_SESSION[C('SESSION_PREFIX')]['cart_in']) {
                $this->error("你并没有在订购清单中添加产品。");
            }
            $Order = D("Order");
            $data  = $Order->create($_POST);
            if(!$data){ $this->error($Order->getError()); }

            $data['goods']    = serialize($data['goods']);  //订单产品详情（id x num）
            $data['order_id'] = D('order')->getno();  //生成订单号 年-月-顺序
            $data['uid']      = session('user_auth.uid');
            $data['status']   = 1;  //订单状态 已提交
            $data['date']     = time(); //提交时间
            $res = $Order->add($data);

            unset($_SESSION[C('SESSION_PREFIX')]['cart_in']); //销毁session
             
            if (!$res) {  $this->error('订单提交失败！请检查数据库连接。'); }
            $this->success('订单提交成功！我们会尽快为你处理。', U("Psi/stock"));
        } elseif(I('get.id')){
            // 删除订单中的产品
            $ids = I('get.id'); 
            $ids = explode(',', $ids);
            foreach ($ids as $key => $value) {
                unset($_SESSION[C('SESSION_PREFIX')]['cart_in'][$value]);
            }
            $this->success("取消产品成功！".$str);
        } else {
            $cart  = session('cart_in') ? session('cart_in') : "";
            $ids   = implode($cart, ',');
            $where    = "id in (".$ids.")";
            $field    = "id,code,name,sale_price,sale_price_off";
            $order    = "id DESC";
            $list     = D("Stock")->lists($where,$order,$field,'99');
            $page     = $list['show'];
            unset($list['show']);

            $auth = D('ucenter_member')->field('id,username,email,mobile,company,truename,address')->find(session('user_auth.uid'));

            $this->assign('_list',$list);
            $this->assign('_auth',$auth);
            $this->assign('_page',$page);

            $this->meta_title = '订单确认';
            $this->display();
        }        
    }


    /**
     * 添加供应商 - 供应商管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/03/25
     */
    public function supplier_add() {
        if(IS_POST){
            $Supplier   = D("Supplier");
            $data       = $Supplier->create($_POST,1);
            if(!$data){ $this->error($Supplier->getError()); }
            $data['status'] = 1;
            $res = $Supplier->add($data);
            if (!$res) {  $this->error('添加供应商失败！请检查数据库连接。'); }
            $this->success('添加供应商成功！');
        } else {
            $this->meta_title = '添加供应商';
            $this->display();
        }
    }


    /**
     * 供应商一览 - 供应商管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/03/25
     */
    public function supplier() {
        /* 关键词模糊搜索 START 2015/07/10 */         
        $ask = I("ask") ? I("ask") : ""; 
        if ($ask == 1) {
            $type     = I("type") ? I("type") : "";   //搜索类型
            $keyword  = I("keyword") ? I("keyword") : ""; 
            switch ($type) {
                case 4:  $search = "company";   break;
                case 5:  $search = "name";      break;
                default: $search = "company";   break;
            }
            $where[$search] = array('like', '%'.$keyword.'%');
        }       
        /* 关键词模糊搜索 END 2015/07/10 */ 
        $field    = "*";
        $order    = "id DESC";
        $list = D("Supplier")->lists($where,$order,$field);

        $page = $list['show'];
        unset($list['show']);

        $this->assign('type',$type);
        $this->assign('_list', $list);
        $this->assign('_page', $page);
        $this->meta_title = '供应商一览';
        $this->display();
    }


    /**
     * 修改供应商 - 供应商管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/03/31
     */
    public function supplier_edit() {
        $Supplier   = D("Supplier");
        if(IS_POST){
            $id         = I('post.id');
            $data       = $Supplier->create($_POST,2);
            if(!$data){ $this->error($Supplier->getError()); }
            $res = $Supplier->where("id=".$id)->save($data);
            if (!$res) {  $this->error('修改供应商失败！请检查数据库连接。'); }
            $this->success('修改供应商成功！');
        } else {  
            $id     = I('get.id');    
            $data   = $Supplier->where("id=".$id)->find();
            $this->assign('data', $data);
            $this->meta_title = '修改供应商';
            $this->display();
        }
    }


    /**
     * 删除供应商 - 供应商管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/04/01
     */
    public function supplier_del() {
        $Supplier   = D("Supplier");
        $id         = array_unique((array)I('id',0)); 
        if ($id[0] == 0) { $this->error('未选中任何供应商。'); }
        $map['id']  = array('in',$id);
        $Supplier->where($map)->delete();
        $this->success('供应商删除成功！');
    }


    /**
     * 供应商详细资料 - 供应商管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/07/24
     */
    public function supplier_info() {
        $Supplier   = D("Supplier");
        $id   = I('get.id');
        $info = $Supplier->find($id);
        $this->assign('info',$info);
        $this->meta_title = '供应商详细资料';
        $this->display();
    }


    public function upload(){
        if (!empty($_FILES)) {
            //图片上传设置
            $config = array(   
                'maxSize'    =>    3145728, 
                'rootPath'   =>    'Public',
                'savePath'   =>    '/Uploads/',  
                'saveName'   =>    array('uniqid',''), 
                'exts'       =>    array('jpg', 'gif', 'png', 'jpeg'),  
                'autoSub'    =>    false,   
                'subName'    =>    array('date','Ymd'),
            );
            $upload = new \Think\Upload($config);// 实例化上传类
            $images = $upload->upload();
            //判断是否有图
            if($images){
                $info=$images['Filedata']['savename'];
                //返回文件地址和名给JS作回调用
                echo $info;
            } else {
                $this->error($upload->getError());//获取失败信息
            }
        }
    }


    public function charts_1() {
        $sel['category'] = D("Category")->getTree('39','id,title,pid');  //获取商品分类
        $sel['supplier'] = D("Supplier")->where($where)->field('id,company')->select();  //供应商列表
        for ($i=2014; $i < 2020; $i++) { 
            $sel['year'][$i] = '';
            if ($i == date('Y',time())) { $sel['year'][$i] = 'selected'; }   
        }

        $data = $this->returnCharts("charts_1",1);

        $this->meta_title = '数据分析一';
        $this->assign('_sel',$sel);
        $this->assign('data',$data);
        $this->display();
    }


    public function charts_2() {
        $sel['category'] = D("Category")->getTree('39','id,title,pid');  //获取商品分类
        $sel['supplier'] = D("Supplier")->where($where)->field('id,company')->select();  //供应商列表
        for ($i=2014; $i < 2020; $i++) { 
            $sel['year'][$i] = '';
            if ($i == date('Y',time())) { $sel['year'][$i] = 'selected'; }   
        }
        for ($i=1; $i < 13; $i++) { 
            $sel['month'][$i] = '';
        }
        $data = $this->returnCharts("charts_2",1);

        $this->meta_title = '数据分析二';
        $this->assign('_sel',$sel);
        $this->assign('data',$data);
        $this->display();
    } 


    public function charts_3() {
        $sel['category'] = D("Category")->getTree('39','id,title,pid');  //获取商品分类
        $sel['supplier'] = D("Supplier")->where($where)->field('id,company')->select();  //供应商列表
        for ($i=2014; $i < 2020; $i++) { 
            $sel['year'][$i] = '';
            if ($i == date('Y',time())) { $sel['year'][$i] = 'selected'; }   
        }

        $data = $this->returnCharts("charts_3",1);

        $this->meta_title = '数据分析三';
        $this->assign('_sel',$sel);
        $this->assign('data',$data);
        $this->display();
    }    


    public function charts_4() {
        $sel['category'] = D("Category")->getTree('39','id,title,pid');  //获取商品分类
        $sel['supplier'] = D("Supplier")->where($where)->field('id,company')->select();  //供应商列表
        for ($i=2014; $i < 2020; $i++) { 
            $sel['year'][$i] = '';
            if ($i == date('Y',time())) { $sel['year'][$i] = 'selected'; }   
        }
        for ($i=1; $i < 13; $i++) { 
            $sel['month'][$i] = '';
        }
        $data = $this->returnCharts("charts_4",1);

        $this->meta_title = '数据分析二';
        $this->assign('_sel',$sel);
        $this->assign('data',$data);
        $this->display();
    } 



    /**
     * 前端图表json数据回调 - 分析管理
     * @param string 图表名称, int 返回或者输出
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/10/14
     */    
    public function returnCharts($obj = "", $method = "") {
        // 默认本年度
        $start = mktime(0,0,0,1,1,date('Y',time()));
        $end   = mktime(0,0,0,1,1,date('Y',time())+1);
        $obj = $obj ? $obj : I('post.obj');

        if ($obj == "charts_1") {   // charts_1图表格式整理
            /* 筛选条件 START */
            if (I('post.time')) {
                $start = mktime(0,0,0,1,1,I('post.time'));
                $end   = mktime(0,0,0,1,1,(I('post.time')+1));
            }
            if (I('post.cat')) { $map['category'] = I('post.cat'); }   // 产品分类
            if (I('post.sup')) { $map['supplier'] = I('post.sup'); }   // 筛选供应商
            /* 筛选条件 END */

            $map['date'] = array('between',array($start,$end));
            $map['type'] = 2;

            $field    = "date_format(FROM_UNIXTIME(`date`),'%c') as month, SUM(`num`) AS num";
            $baseData = D('record')->field($field)->where($map)->group('month')->select();
        
            /* 整理成json格式的数组 array(0=>array(month,value),1=>array(month,value)...) */
            for ($i=0; $i < 12; $i++) { $data[$i] = array($i+1,0); }  // 初始化数据数组，包含1-12月
            foreach ($baseData as $key => $value) {  // 覆盖有数据的月份
                $data[$value['month']-1][0] = $baseData[$key]['month'];
                $data[$value['month']-1][1] = intval($value['num']);
            }
            foreach ($data as $key => $value) { $data[$key][0] = $value[0]."月"; }
        }

        if ($obj == "charts_2") {   // charts_2图表格式整理
            $month = I('post.month') ? I('post.month') : 1;
            $year  = I('post.year') ? I('post.year') : date('Y',time());
            $start = mktime(0,0,0,$month,1,$year);

            if (I('post.month')) {
                $end = mktime(0,0,0,$month+1,1,$year);
            } else {
                $end = mktime(0,0,0,1,1,$year+1);
            }

            if (I('post.cat')) { $map['category'] = I('post.cat'); }   // 产品分类
            if (I('post.sup')) { $map['supplier'] = I('post.sup'); }   // 筛选供应商

            $map['date'] = array('between',array($start,$end));
            $map['type'] = 2;

            $field = "name, SUM(`num`) AS num";
            $baseData  = D('record')->field($field)->where($map)->group('name')->order('num desc')->limit(10)->select();
            for ($i=0; $i < 10; $i++) { 
                $data[$i][] = $baseData[$i]['name'] ? $baseData[$i]['name'] : '暂无数据';
                $data[$i][] = intval($baseData[$i]['num']);
            }
        }

        if ($obj == "charts_3") {   // charts_1图表格式整理
            /* 筛选条件 START */
            if (I('post.time')) {
                $start = mktime(0,0,0,1,1,I('post.time'));
                $end   = mktime(0,0,0,1,1,(I('post.time')+1));
            }
            if (I('post.cat')) { $map['category'] = I('post.cat'); }   // 产品分类
            if (I('post.sup')) { $map['supplier'] = I('post.sup'); }   // 筛选供应商
            /* 筛选条件 END */

            $map['date'] = array('between',array($start,$end));
            $map['type'] = 2;

            $field    = "date_format(FROM_UNIXTIME(`date`),'%c') as month, SUM(`num`*`price`) AS num";
            $baseData = D('record')->field($field)->where($map)->group('month')->select();
        
            /* 整理成json格式的数组 array(0=>array(month,value),1=>array(month,value)...) */
            for ($i=0; $i < 12; $i++) { $data[$i] = array($i+1,0); }  // 初始化数据数组，包含1-12月
            foreach ($baseData as $key => $value) {  // 覆盖有数据的月份
                $data[$value['month']-1][0] = $baseData[$key]['month'];
                $data[$value['month']-1][1] = intval($value['num']);
            }
            foreach ($data as $key => $value) { $data[$key][0] = $value[0]."月"; }
        }

        if ($obj == "charts_4") {   // charts_2图表格式整理
            $month = I('post.month') ? I('post.month') : 1;
            $year  = I('post.year') ? I('post.year') : date('Y',time());
            $start = mktime(0,0,0,$month,1,$year);

            if (I('post.month')) {
                $end = mktime(0,0,0,$month+1,1,$year);
            } else {
                $end = mktime(0,0,0,1,1,$year+1);
            }

            if (I('post.cat')) { $map['category'] = I('post.cat'); }   // 产品分类

            $map['date'] = array('between',array($start,$end));
            $map['type'] = 2;

            $field = "supplier, SUM(`num`) AS num";
            $baseData  = D('record')->field($field)->where($map)->group('supplier')->order('num desc')->limit(10)->select();
            for ($i=0; $i < 10; $i++) { 
                $data[$i][] = $baseData[$i]['supplier'] ? get_company($baseData[$i]['supplier']) : '暂无数据';
                $data[$i][] = intval($baseData[$i]['num']);
            }
        }

        /* 整理数组 END */

        if (!$method) { echo json_encode($data); }
        return json_encode($data);
    }


    /**
     * 单品销售数据表 - 分析管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/10/31
     */ 
    public function data_1() {
        $sel['category'] = D("Category")->getTree('39','id,title,pid');  //获取商品分类
        $sel['supplier'] = D("Supplier")->where($where)->field('id,company')->select();  //供应商列表

        $map['supplier'] = I('get.supplier') ? I('get.supplier') : array('LIKE',"'%%'");  //sql筛选
        $map['category'] = I('get.category') ? I('get.category') : array('LIKE',"'%%'");

        /* 关键词模糊搜索 START 2015/07/10 */  #ask问题#
        $type = 1; //默认搜索type值        
        $ask  = I("ask") ? I("ask") : ""; 
        if ($ask == 1) {
            $type     = I("type") ? I("type") : $type;   // 搜索类型
            $keyword  = I("keyword") ? I("keyword") : ""; 
            $start    = I("time-start") ? strtotime(I("time-start")) : 0;
            $end      = I("time-end") ? strtotime(I("time-end")) : 9999999999;
            if ($type == 1) { $map['code'] = array('like', '"%'.$keyword.'%"'); }
            if ($type == 2) { $map['name'] = array('like', '"%'.$keyword.'%"'); }
            $map['date'] = array('between',array($start,$end));  // 记录时间
        }  
        /* 关键词模糊搜索 END 2015/07/10 */

        $map['type'] = 2;
        $map['warehouse'] = 1;

        $main['table'] = "record";  // 主表
        $main['field'] = "code";        // 主副表对应字段
        $main['order'] = I('get.orderMain') ? I('get.fieldMain')." ".I('get.orderMain') : "";
        $sub['table']  = "stock";  // 关联副表
        $sub['field']  = 'code';     // 主副表对应字段
        $sub['order']  = I('get.orderSub') ? I('get.fieldSub')." ".I('get.orderSub') : "";  //排序规则

        $main['sp'] = I('get.orderSp') ? I('get.fieldSp')." ".I('get.orderSp') : "";

        $default_order = "app_record.id asc";
        
        $field = "*,SUM(app_record.num) as num,SUM(app_record.num)*app_record.price as total";
        $group = "app_record.code";

        $data = D('Stock')->listEx($main, $sub, $map, $default_order, $field, $group);
        $page = $data['show'];
        unset($data['show']);

        $this->meta_title = '单品销售数据表';
        $this->assign('_sel',$sel);
        $this->assign('data',$data);
        $this->assign('_page',$page);
        $this->display();
    } 


    // /**
    //  * 客户销售数据表 - 分析管理
    //  * @author echoshiki <echoshiki@outlook.com>
    //  * 2015/11/02
    //  */ 
    // public function data_2() {
    //     $sel['category'] = D("Category")->getTree('39','id,title,pid');  //获取商品分类
    //     $sel['supplier'] = D("Supplier")->where($where)->field('id,company')->select();  //供应商列表

    //     $map['supplier'] = I('get.supplier') ? I('get.supplier') : array('LIKE',"'%%'");  //sql筛选
    //     $map['category'] = I('get.category') ? I('get.category') : array('LIKE',"'%%'");

    //     /* 关键词模糊搜索 START 2015/07/10 */  #ask问题#
    //     $type = 1; //默认搜索type值        
    //     $ask  = I("ask") ? I("ask") : ""; 
    //     if ($ask == 1) {
    //         $type     = I("type") ? I("type") : $type;   // 搜索类型
    //         $keyword  = I("keyword") ? I("keyword") : ""; 
    //         $start    = I("time-start") ? strtotime(I("time-start")) : 0;
    //         $end      = I("time-end") ? strtotime(I("time-end")) : 9999999999;
    //         if ($type == 1) { $map['code'] = array('like', '"%'.$keyword.'%"'); }
    //         if ($type == 2) { $map['name'] = array('like', '"%'.$keyword.'%"'); }
    //         $map['date'] = array('between',array($start,$end));  // 记录时间
    //     }  
    //     /* 关键词模糊搜索 END 2015/07/10 */

    //     $map['type'] = 5;  //客户入库

    //     $main['table'] = "record";  // 主表
    //     $main['field'] = "warehouse";        // 主副表对应字段
    //     $main['order'] = I('get.orderMain') ? I('get.fieldMain')." ".I('get.orderMain') : "";
    //     $sub['table']  = "ucenter_member";  // 关联副表
    //     $sub['field']  = 'id';     // 主副表对应字段
    //     $sub['order']  = I('get.orderSub') ? I('get.fieldSub')." ".I('get.orderSub') : "";  //排序规则

    //     $main['sp'] = I('get.orderSp') ? I('get.fieldSp')." ".I('get.orderSp') : "";

    //     $default_order = "app_record.id asc";
        
    //     $field = "*,SUM(app_record.num) as num,SUM(app_record.num)*app_record.price as total";
    //     $group = "app_record.warehouse";

    //     $data = D('Stock')->listEx($main, $sub, $map, $default_order, $field, $group);
    //     $page = $data['show'];
    //     unset($data['show']);

    //     $this->meta_title = '数据分析二';
    //     $this->assign('_sel',$sel);
    //     $this->assign('data',$data);
    //     $this->assign('_page',$page);
    //     $this->display();
    // } 

    /**
     * 客户销售数据表 - 分析管理
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/11/02
     */ 
    public function data_2() {
        $sel['category'] = D("Category")->getTree('39','id,title,pid');  //获取商品分类
        $sel['supplier'] = D("Supplier")->where($where)->field('id,company')->select();  //供应商列表

        $map['supplier'] = I('get.supplier') ? I('get.supplier') : array('LIKE',"'%%'");  //sql筛选
        $map['category'] = I('get.category') ? I('get.category') : array('LIKE',"'%%'");

        /* 关键词模糊搜索 START 2015/07/10 */  #ask问题#
        $type = 1; //默认搜索type值        
        $ask  = I("ask") ? I("ask") : ""; 
        if ($ask == 1) {
            $type     = I("type") ? I("type") : $type;   // 搜索类型
            $keyword  = I("keyword") ? I("keyword") : ""; 
            $start    = I("time-start") ? strtotime(I("time-start")) : 0;
            $end      = I("time-end") ? strtotime(I("time-end")) : 9999999999;
            if ($type == 1) { $map['code'] = array('like', '"%'.$keyword.'%"'); }
            if ($type == 2) { $map['name'] = array('like', '"%'.$keyword.'%"'); }
            $map['date'] = array('between',array($start,$end));  // 记录时间
        }  
        /* 关键词模糊搜索 END 2015/07/10 */

        $map['type'] = 5;  //客户入库

        $main['table'] = "record";  // 主表
        $main['field'] = "warehouse";        // 主副表对应字段
        $main['order'] = I('get.orderMain') ? I('get.fieldMain')." ".I('get.orderMain') : "";
        $sub['table']  = "ucenter_member";  // 关联副表
        $sub['field']  = 'id';     // 主副表对应字段
        $sub['order']  = I('get.orderSub') ? I('get.fieldSub')." ".I('get.orderSub') : "";  //排序规则

        $main['sp'] = I('get.orderSp') ? I('get.fieldSp')." ".I('get.orderSp') : "";

        $default_order = "app_record.id asc";
        
        $field = "*,SUM(app_record.num) as num,SUM(app_record.num)*app_record.price as total";
        $group = "app_record.warehouse";

        $data = D('Stock')->listEx($main, $sub, $map, $default_order, $field, $group);
        $page = $data['show'];
        unset($data['show']);

        $this->meta_title = '数据分析二';
        $this->assign('_sel',$sel);
        $this->assign('data',$data);
        $this->assign('_page',$page);
        $this->display();
    }




    /**
     * 系统帮助 - 帮助说明
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/09/10
     */
    public function help() {
        $this->meta_title = '供应商详细资料';
        $this->display();
    }


    /**
     * 文件删除1 - 用于刚上传的图片
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/09/11
     */
    public function thumb_del() {
        if (I('post.file_name')) {
            $path = "Public/Uploads/";
            $filepath = $path.I('post.file_name');
            if (!unlink($filepath)) { echo "删除失败"; return false; }
            return false;
        }
    } 

    /**
     * 文件删除2 - 用于上传后的图片  **危险
     * @author echoshiki <echoshiki@outlook.com>
     * 2015/09/13
     */
    public function thumb_remove() {
        if (I('post.file_name')) {
            $filepath = I('post.file_name');
            if (!unlink($filepath)) { echo "删除失败"; return false; }
            return false;
        }
    } 


    public function test() {
        // 商品资料一键转换库存初始量
        // $list = M('Stock')->field('id')->select();
        // foreach ($list as $key => $value) {
        //     $data['sid']  = $value['id'];
        //     $data['num']  = 0;
        //     $data['date'] = time();
        //     $data['warehouse'] = 1;
        //     $data['status'] = 1;
        //     M('Warehouse')->add($data);
        //     unset($data);
        // }
        // 
        // 一键生成产品表所有数据的首字母pinyin
        // $list = M('Stock')->field('id,name')->select(); 
        // foreach ($list as $key => $value) {
        //     $data['pinyin'] = pinyin($value['name']);
        //     $map['id'] = $value['id'];
        //     M('Stock')->where($map)->save($data);
        // }  
        $this->meta_title = '测试网页';
        $this->display();
    }


}