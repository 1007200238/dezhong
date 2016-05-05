<?php if (!defined('THINK_PATH')) exit();?> <div class="container-span top-columns cf">
 	<?php if (session('user_auth.group_id') != C('JOIN_GROUP')){ ?>
 	<!-- 主仓库界面 -->
		<dl class="show-num-mod">
			<dt><i class="count-icon category-count-icon"></i></dt>
			<dd>
				<strong><a href="<?php echo U('Psi/stock');?>" target="_blank"><?php echo ($info["stock"]); ?></a></strong>
				<span>产品数量</span>
			</dd>
		</dl>
		<dl class="show-num-mod">
			<dt><i class="count-icon user-action-icon"></i></dt>
			<dd>
				<strong><a href="<?php echo U('Psi/inventory');?>&warning=1" target="_blank"><?php echo ($info["warning"]); ?></a></strong>
				<span>预警产品</span>                                                                                        
			</dd>
		</dl>
		<dl class="show-num-mod">
			<dt><i class="count-icon doc-count-icon"></i></dt>
			<dd>
				<strong><a href="<?php echo U('Psi/order');?>" target="_blank"><?php echo ($info["order_new"]); ?></a>/<?php echo ($info["order"]); ?></strong>
				<span>产品订单</span>
			</dd>
		</dl>
		<dl class="show-num-mod">
			<dt><i class="count-icon doc-modal-icon"></i></dt>
			<dd>
				<strong><a href="<?php echo U('Psi/apply_list');?>" target="_blank"><?php echo ($info["apply"]); ?></a></strong>
				<span>采购申请</span>
			</dd>
		</dl>
		<dl class="show-num-mod">
			<dt><i class="count-icon user-count-icon"></i></dt>
			<dd>
				<strong><a href="<?php echo U('User/index');?>" target="_blank"><?php echo ($info["customer"]); ?></a></strong>
				<span>加盟商</span>
			</dd>
		</dl>
	<?php } else { ?>
	<!-- 加盟商仓库界面 -->
		<dl class="show-num-mod">
			<dt><i class="count-icon category-count-icon"></i></dt>
			<dd>
				<strong><a href="<?php echo U('Psi/inventory');?>" target="_blank"><?php echo ($info["join_ware"]); ?></a></strong>
				<span>产品数量</span>
			</dd>
		</dl>
		<dl class="show-num-mod">
			<dt><i class="count-icon user-action-icon"></i></dt>
			<dd>
				<strong><a href="<?php echo U('Psi/inventory');?>&warning=1" target="_blank"><?php echo ($info["join_warn"]); ?></a></strong>
				<span>订单数量</span>                                                                                        
			</dd>
		</dl>
	<?php } ?>
</div>