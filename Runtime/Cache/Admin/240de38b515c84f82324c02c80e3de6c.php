<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>订单打印|德众进销存管理系统</title>
    <link href="/Public/favicon.ico" type="image/x-icon" rel="shortcut icon">
    <!--[if lt IE 9]>
    <script type="text/javascript" src="/Public/static/jquery-1.10.2.min.js"></script>
    <![endif]--><!--[if gte IE 9]><!-->
    <script type="text/javascript" src="/Public/static/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="/Public/Admin/js/jquery.mousewheel.js"></script>
    <!--<![endif]-->
	<link rel="stylesheet" type="text/css" href="/Public/Admin/css/print.css" media="all">
</head>
<body>
	<div class="main">
		<div>
			<p class="title"><?php echo C('COMPANY_NAME');?></p>

			<p class="sub-title">奥迈德品牌<br>销售订单</p>
		</div>
		<div>
			<div class="left-1">订单编号：<?php echo ($info["order_id"]); ?></div>
			<div class="left-3">订购日期：<?php echo (date('Y-m-d H:i',$info["date"])); ?></div>
			<div class="clear"></div>
		</div>
		<div class="data-area">
			<table class="tb">
				<thead>
					<tr>
						<td>序号</td>
						<td>产品名称</td>
						<td>产品编码</td>
						<td>OE码</td>
						<td>适用车型</td>
						<td>单价</td>
						<td>订购量</td>
						<td>金额（元）</td>
					</tr>
				</thead>
				<tbody>
					<?php if(is_array($info["goods"])): $i = 0; $__LIST__ = $info["goods"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
				            <td><?php echo ($i); ?></td>
				            <td><?php echo ($vo["name"]); ?></td>
				            <td><?php echo ($vo["code"]); ?></td>
				            <td><?php echo ($vo["OE"]); ?></td>
							<td><?php echo ($vo["standard"]); ?></td>
							<td><?php echo ($vo["sale_price_off"]); ?></td>
							<td><?php echo ($vo["num"]); ?></td>
							<td><?php echo $vo['sale_price_off']*$vo['num']; ?></td>
							<?php $sum += $vo['sale_price_off']*$vo['num']; ?>
						</tr><?php endforeach; endif; else: echo "" ;endif; ?>	
				</tbody>
				<tr>
					<td colspan="5"> 合  计 </td>
					<td> —— </td>
					<td> —— </td>
					<td><?php echo ($sum); ?></td>
				</tr>
				<tr>
					<td> 合同备注 </td>
					<td colspan="12">
						<?php echo ($info["note"]); ?>
					</td>
				</tr>
			</table>		
		</div>
		<div class="foot">
			<div class="left-4">
				<p>客户信息</p>
				<p>客户名称:<?php echo ($info["company"]); ?></p>
				<p>订购账号:<?php echo ($info["username"]); ?></p>
				<p>联系人:<?php echo ($info["name"]); ?></p>
				<p>联系电话:<?php echo ($info["tel"]); ?></p>
				<p>收货地址:<?php echo ($info["address"]); ?></p>
			</div>
			<div class="left-logo">
				<img src="/Public/Admin/images/print_logo.jpg">
			</div>
			<div class="left-5">
				<p>商户信息</p>
				<p>单位名称（章）:<?php echo C('COMPANY_NAME');?></p>
				<p>单位地址:<?php echo C('COMPANY_ADDRESS');?></p>
				<p>法定代表人:<?php echo C('COMPANY_PERSON');?></p>
				<p>委托代理人:<?php echo C('COMPANY_CONSIGNER');?></p>
				<p>公司电话:<?php echo C('COMPANY_PHONE');?></p>
				<p>公司传真:<?php echo C('COMPANY_FAX');?></p>
			</div>
			<div class="clear"></div>
		</div>
	</div>
	<script>
		window.onload = function() {
   			 window.print(); //打印当前页面
		}
	</script>
</body>
</html>