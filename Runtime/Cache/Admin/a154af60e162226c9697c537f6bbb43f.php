<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>退货单打印|德众进销存管理系统</title>
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
			<p class="sub-title">退货单</p>
		</div>
		<div>
			<div class="left-7">退货编号：<?php echo ($_list["no"]); ?>&nbsp;&nbsp;</div>
			<div class="left-2">退货日期：<?php echo (date('Y-m-d',$_list["date"])); ?></div>
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
						<td>退货数量</td>
						<td>总价</td>
						<td>备注</td>
					</tr>
				</thead>
				<tbody>
					<?php if(is_array($_list["info"])): $i = 0; $__LIST__ = $_list["info"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
				            <td><?php echo ($i); ?></td>
				            <td><?php echo ($vo["OE"]); ?></td>
							<td><?php echo ($vo["code"]); ?></td>
							<td><?php echo ($vo["name"]); ?></td>
							<td><?php echo ($vo["price"]); ?></td>
							<td><?php echo ($vo["unit"]); ?></td>
							<td><?php echo ($vo["standard"]); ?></td>
							<td><?php echo ($vo["num"]); ?></td>
							<td><?php echo ($vo['num']*$vo['price']); ?></td>
							<td></td>
						</tr>
						<?php $sum += $vo['num']; ?>
						<?php $total += ($vo['num']*$vo['price']); endforeach; endif; else: echo "" ;endif; ?>
					<tr>
						<td colspan="7"> 合  计 </td>
						<td><?php echo ($sum); ?></td>
						<td><?php echo ($total); ?></td>
						<td> —— </td>
					</tr>
					<?php if ($_list['from'] == 2) { ?>					
					<tr>
						<td colspan="2" class="note"> 包装品 </td>
						<td colspan="8" class="exstock">
							<p><?php if(is_array($_list["exinfo"])): $i = 0; $__LIST__ = $_list["exinfo"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; echo ($vo["name"]); ?> x <?php echo ($vo["num_in"]); ?>&nbsp;&nbsp;<?php endforeach; endif; else: echo "" ;endif; ?></p>
							<p>其余固定属性包装品各<?php echo ($sum); ?></p>
						</td>
					</tr>
					<?php } ?>
					<tr>
						<td colspan="2" class="note"> 单据备注 </td>
						<td colspan="8"><?php echo ($_list["note"]); ?></td>
					</tr>
				</tbody>
			</table>		
		</div>
		<div class="foot">
			<div class="foot-top">
				<span>开票：</span>
				<?php echo ($_list["info1"]); ?>&nbsp;&nbsp;
				<span >审核：</span>
				<?php echo ($_list["info2"]); ?>&nbsp;&nbsp;
			</div>
			<?php if ($_list['from'] != 2) { ?>
			<div class="left-4">
				<p>供方</p>
				<p>单位名称（章）:<?php echo ($_list["pinfo"]["company"]); ?></p>
				<p>单位地址:<?php echo ($_list["pinfo"]["address"]); ?></p>
				<p>法定代表人:<?php echo ($_list["pinfo"]["name"]); ?></p>
				<p>委托代理人:<?php echo ($_list["pinfo"]["consigner"]); ?></p>
				<p>电话:<?php echo ($_list["pinfo"]["mobile"]); ?></p>
				<p>传真:<?php echo ($_list["pinfo"]["fax"]); ?></p>
				<p>开户银行:<?php echo ($_list["pinfo"]["bank"]); ?></p>
				<p>账号:<?php echo ($_list["pinfo"]["account"]); ?></p>
			</div>
			<?php } else { ?>
			<div class="left-4">
				<p>加盟商</p>
				<p>单位名称:<?php echo ($_list["pinfo"]["company"]); ?></p>
				<p>收货地址:<?php echo ($_list["pinfo"]["address"]); ?></p>
				<p>联系人:<?php echo ($_list["pinfo"]["truename"]); ?></p>
				<p>电话:<?php echo ($_list["pinfo"]["mobile"]); ?></p>
			</div>
			<?php } ?>
			<div class="left-5">
				<p>需方</p>
				<p>单位名称（章）:<?php echo C('COMPANY_NAME');?></p>
				<p>单位地址:<?php echo C('COMPANY_ADDRESS');?></p>
				<p>法定代表人:<?php echo C('COMPANY_PERSON');?></p>
				<p>委托代理人:<?php echo C('COMPANY_CONSIGNER');?></p>
				<p>公司电话:<?php echo C('COMPANY_PHONE');?></p>
				<p>公司传真:<?php echo C('COMPANY_FAX');?></p>
			</div>
		</div>
	</div>

	 <script>
		window.onload = function() {
   			 window.print(); //打印当前页面
		}
	 </script>
</body>
</html>