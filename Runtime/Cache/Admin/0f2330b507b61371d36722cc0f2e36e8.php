<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>采购合同单打印|德众进销存管理系统</title>
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

			<p class="sub-title">奥迈德品牌<br>采购合同单</p>
		</div>
		<div>
			<div class="left-1">合同编号：<?php echo ($_list["no"]); ?></div>
			<div class="left-3">签订日期：<?php echo (date('Y-m-d',$_list["pch_date"])); ?>&nbsp;&nbsp;交货日期：<?php echo (date('Y-m-d',$_list["dev_date"])); ?></div>
			<div class="clear"></div>
		</div>
		<div class="data-area">
			<table class="tb">
				<thead>
					<tr>
						<td>序号</td>
						<td>产品名称</td>
						<td>编号</td>
						<td>OE码</td>
						<td>厂家编码</td>
						<td>适用车型</td>
						<td>单价</td>
						<td>数量</td>
						<td>金额</td>
						<td>彩盒尺寸</td>
						<td>彩带尺寸</td>
						<td>库位号</td>
						<td>备注</td>
					</tr>
				</thead>
				<tbody>
					<?php if(is_array($_list["info"])): $i = 0; $__LIST__ = $_list["info"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
				            <td><?php echo ($i); ?></td>
				            <td><?php echo ($vo["name"]); ?></td>
				            <td><?php echo ($vo["code"]); ?></td>
				            <td><?php echo ($vo["OE"]); ?></td>
				            <td><?php echo ($vo["supplier_no"]); ?></td>
							<td><?php echo ($vo["standard"]); ?></td>
							<td><?php echo ($vo["purch_price"]); ?></td>
							<td><?php echo ($vo["num"]); ?></td>
							<td><?php echo ($vo["total"]); ?></td>
							<td><?php echo ($vo["box_size"]); ?></td>
							<td><?php echo ($vo["bag_size"]); ?></td>
							<td><?php echo ($vo["store_no"]); ?></td>
							<td></td>
						</tr><?php endforeach; endif; else: echo "" ;endif; ?>	
				</tbody>
				<tr>
					<td colspan="5"> 合  计 </td>
					<td> —— </td>
					<td> —— </td>
					<td><?php echo ($_list["num"]); ?></td>
					<td><?php echo ($_list["total"]); ?></td>
					<td> —— </td>
					<td> —— </td>
					<td> —— </td>
					<td> —— </td>
				</tr>
				<tr>
					<td> 合同备注 </td>
					<td colspan="12">
						<?php if(!$_list['note']) { echo "打我们公司标，外包装注明产品名称及型号，内附发货清单。"; } ?>
					</td>
				</tr>
			</table>		
		</div>
		<div class="foot">
			<div class="left-4">
				<p>供方</p>
				<p>单位名称（章）:<?php echo ($_list["supplier"]["company"]); ?></p>
				<p>单位地址:<?php echo ($_list["supplier"]["address"]); ?></p>
				<p>法定代表人:<?php echo ($_list["supplier"]["name"]); ?></p>
				<p>委托代理人:<?php echo ($_list["supplier"]["consigner"]); ?></p>
				<p>电话:<?php echo ($_list["supplier"]["mobile"]); ?></p>
				<p>传真:<?php echo ($_list["supplier"]["fax"]); ?></p>
				<p>开户银行:<?php echo ($_list["supplier"]["bank"]); ?></p>
				<p>账号:<?php echo ($_list["supplier"]["account"]); ?></p>
			</div>
			<div class="left-logo">
				<img src="/Public/Admin/images/print_logo.jpg">
			</div>
			<div class="left-5">
				<p>需方</p>
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