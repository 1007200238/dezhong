<?php
// +----------------------------------------------------------------------
// | 订单系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2015 http://www.lanrenmaku.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: smallbat <smallbat@126.com> QQ:71512398
// +----------------------------------------------------------------------
/*
*+----------------------------------------------------------------------
*   支付宝支付控制器
*+----------------------------------------------------------------------
*/

namespace Alipay\Controller;



class IndexController extends Controller {

    public function index() {
        echo $_SERVER['DOCUMENT_ROOT'];
        // import('Alipay.Extend.Payment.Alipay.Alipay',$_SERVER['DOCUMENT_ROOT'],'.php');
    }

    // 处理下单
    public function dobuy(){
        // 把订单信息添加到订单表
        // $row = M('product')->where(array('id'=>I('product')))->find();
        $out_trade_no = date('YmdHis');//订单编号
        $data = array(
            // 'product_id'=>$row['id'],
            'ordid'=>$out_trade_no,
            'ordtime'=>time(),
            // 'phone'=>I('phone'),
            // 'money'=>$row['price'],
            // 'qq'=>I('qq'),
            // 'mail'=>I('email'),
            // 'way'=>'支付宝',
            'ordstatus'=>0,
            // 'downurl'=>$row['downurl'],
            // 'downpasswd'=>$row['downpasswd'],
            // 'title'=>$row['name']
        );
        M('orderlist')->add($data);

        // 提交数据给支付宝
        $baseurl = 'http://'.$_SERVER['HTTP_HOST'];
        $args = array(
            'out_trade_no'=>$out_trade_no,// 商户订单号
            'notify_url'=>$baseurl.'/index.php/Alipay/Index/notifyurl.html',// 异步跳转处理
            'return_url'=>$baseurl.'/index.php/Alipay/Index/returnurl.html',// 同步跳转处理
            'name'=>$row['name'],// 订单名称
            'total'=>$row['price'],// 订单金额
            'content'=>'测试描述',// 订单描述
            'show_url'=>$baseurl,// 商品展示地址
        );

        Alipay\Alipay::pay(C('alipay'),$args);
    }

    // 同步跳转
    public function returnurl(){



        $alipay_config = C('alipay');
        // $alipayNotify = new Alipay\Test();exit();
        //计算得出通知验证结果
        //
        echo "hello";
        vendor('PHPExcel.PHPExcel');

        $objPHPExcel = new PHPExcel();

        exit();


        vendor('Alipay.AlipayNotify');

        $alipayNotify = new AlipayNotify($alipay_config);

        $verify_result = $alipayNotify->verifyReturn();
        if($verify_result) {//验证成功
            //商户订单号
            $out_trade_no = $_GET['out_trade_no'];
            //支付宝交易号
            $trade_no = $_GET['trade_no'];
            //交易状态
            $trade_status = $_GET['trade_status'];
            if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
                $order = M('order');
                $row = $order->where(array('number'=>$out_trade_no))->find();
                if($row){
                    if($order->where(array('id'=>$row['id']))->save(array('status'=>1))){
                        $this->assign('row',$row);
                        $this->display('buy_success');
                    }else{
                        $this->error('错误');    
                    }
                }else{
                    $this->error('错误');
                }
            }else {
              echo "trade_status=".$_GET['trade_status'];
            }
                
            echo "验证成功<br />";
        }
        else {
            //验证失败
            //如要调试，请看alipay_notify.php页面的verifyReturn函数
            echo "验证失败";
        }
    }
    // 异步跳转
    public function notifyurl(){
        $alipay_config = C('alipay');
        //计算得出通知验证结果
        $alipayNotify = new Alipay\AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
        if($verify_result) {//验证成功
            $out_trade_no = $_POST['out_trade_no'];
            //支付宝交易号
            $trade_no = $_POST['trade_no'];
            //交易状态
            $trade_status = $_POST['trade_status'];

            if($_POST['trade_status'] == 'TRADE_FINISHED'||$_POST['trade_status'] == 'TRADE_SUCCESS') {
               $order = M('order');
               $row = $order->where(array('number'=>$out_trade_no))->find();
                if($row){
                    if($order->where(array('id'=>$row['id']))->save(array('status'=>1))){
                        echo "success";
                    }else{
                        echo "fail";
                    }
                }else{
                    echo "fail";
                }
            }
            echo "success";     //请不要修改或删除
            
        }
        else {
            //验证失败
            echo "fail";

            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
    }
}