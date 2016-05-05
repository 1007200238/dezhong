<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>入库单打印|德众进销存管理系统</title>
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
			<p class="sub-title">入库单</p>
		</div>
		<div>
			<div class="left-6">入库编号：<?php echo ($_list["no"]); ?>&nbsp;&nbsp;合同编号：<?php echo ($_list["from_no"]); ?></div>
			<div class="left-2">入库日期：<?php echo (date('Y-m-d',$_list["date"])); ?></div>
			<div class="clear"></div>
		</div>
		<div class="data-area">
			<table class="tb">
				<thead>
					<tr>
						<td>序号</td>
						<td>OE号</td>
						<td>配件编码</td>
						<td>产品名称</td>
						<td>单价</td>
						<td>单位</td>
						<td>适用车型</td>
						<td>入库数量</td>
						<td>备注</td>
					</tr>
				</thead>
				<tbody>
					<?php if(is_array($_list["info"])): $i = 0; $__LIST__ = $_list["info"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
				            <td><?php echo ($i); ?></td>
				            <td><?php echo ($vo["OE"]); ?></td>
							<td><?php echo ($vo["code"]); ?></td>
							<td><?php echo ($vo["name"]); ?></td>
							<td><?php echo ($vo["purch_price"]); ?></td>
							<td><?php echo ($vo["unit"]); ?></td>
							<td><?php echo ($vo["standard"]); ?></td>
							<td><?php echo ($vo["num"]); ?></td>
							<td></td>
						</tr>
						<?php $sum += $vo['num']; endforeach; endif; else: echo "" ;endif; ?>
					<tr>
						<td colspan="7"> 合  计 </td>
						<td><?php echo ($sum); ?></td>
						<td> —— </td>
					</tr>
					<tr>
						<td colspan="2" class="note"> 单据备注 </td>
						<td colspan="7"><?php echo ($_list["note"]); ?></td>
					</tr>
				</tbody>
			</table>		
		</div>
		<div class="foot">
			<span>供应商：<?php echo ($_list["supplier"]["company"]); ?>&nbsp;&nbsp;&nbsp;</span>
		</div>
	</div>

	<script>
		window.onload = function() {
   			 window.print(); //打印当前页面
		}
	</script>
</body>
</html>