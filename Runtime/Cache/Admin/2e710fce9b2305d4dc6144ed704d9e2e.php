<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo ($meta_title); ?>|德众进销存管理系统</title>
    <link href="/Public/favicon.ico" type="image/x-icon" rel="shortcut icon">
    <link rel="stylesheet" type="text/css" href="/Public/Admin/css/base.css" media="all">
    <link rel="stylesheet" type="text/css" href="/Public/Admin/css/common.css" media="all">
    <link rel="stylesheet" type="text/css" href="/Public/Admin/css/module.css">
    <link rel="stylesheet" type="text/css" href="/Public/Admin/css/style.css" media="all">
	<link rel="stylesheet" type="text/css" href="/Public/Admin/css/<?php echo (C("COLOR_STYLE")); ?>.css" media="all">

   <!--  <link rel="stylesheet" type="text/css" href="/Public/static/bootstrap/css/bootstrap.min.css" media="all"> -->

     <!--[if lt IE 9]>
    <script type="text/javascript" src="/Public/static/jquery-1.10.2.min.js"></script>
    <![endif]--><!--[if gte IE 9]><!-->
    <script type="text/javascript" src="/Public/static/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="/Public/Admin/js/jquery.mousewheel.js"></script>
    <!--<![endif]-->
    
</head>
<body>
    <!-- 头部 -->
    <div class="header">
        <!-- Logo -->
        <span class="logo"></span>
        <!-- /Logo -->

        <!-- 主导航 -->
        <ul class="main-nav">
            <?php if(is_array($__MENU__["main"])): $i = 0; $__LIST__ = $__MENU__["main"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i; if( $menu['title'] == "商品" && session('user_auth.group_id') == C('JOIN_GROUP') ) { ?>
                        <li class="<?php echo ((isset($menu["class"]) && ($menu["class"] !== ""))?($menu["class"]):''); ?>"><a href="<?php echo (u($menu["url"])); ?>">采购</a></li>
                <?php } elseif( $menu['title'] == "销售" && session('user_auth.group_id') == C('JOIN_GROUP') ) { ?>
                        <li class="<?php echo ((isset($menu["class"]) && ($menu["class"] !== ""))?($menu["class"]):''); ?>"><a href="<?php echo (u($menu["url"])); ?>">单据</a></li>
                <?php } else { ?>
                        <li class="<?php echo ((isset($menu["class"]) && ($menu["class"] !== ""))?($menu["class"]):''); ?>"><a href="<?php echo (u($menu["url"])); ?>"><?php echo ($menu["title"]); ?></a></li>
                <?php } endforeach; endif; else: echo "" ;endif; ?>
        </ul>
        <!-- /主导航 -->

        <!-- 用户栏 -->
        <div class="user-bar">
            <a href="javascript:;" class="user-entrance"><i class="icon-user"></i></a>
            <ul class="nav-list user-menu hidden">
                <li class="manager">你好，<em title="<?php echo session('user_auth.username');?>"><?php echo session('user_auth.username');?></em></li>
                <li><a href="<?php echo U('User/updatePassword');?>">修改密码</a></li>
                <li><a href="<?php echo U('User/updateNickname');?>">修改昵称</a></li>
                <li><a href="<?php echo U('Public/logout');?>">退出</a></li>
            </ul>
        </div>
    </div>
    <!-- /头部 -->

    <!-- 边栏 -->
    <div class="sidebar">
        <!-- 子导航 -->
        
            <div id="subnav" class="subnav">
                <?php if(!empty($_extra_menu)): ?>
                    <?php echo extra_menu($_extra_menu,$__MENU__); endif; ?>
                <?php if(is_array($__MENU__["child"])): $i = 0; $__LIST__ = $__MENU__["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub_menu): $mod = ($i % 2 );++$i;?><!-- 子导航 -->
                    <?php if(!empty($sub_menu)): if(!empty($key)): ?><h3><i class="icon icon-unfold"></i><?php echo ($key); ?></h3><?php endif; ?>
                        <ul class="side-sub-menu">
                            <?php if(is_array($sub_menu)): $i = 0; $__LIST__ = $sub_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li>
                                <?php if($menu['title'] == "订单一览" && session('user_auth.group_id') == C('JOIN_GROUP')) { ?>
                                        <a class="item" href="<?php echo (u($menu["url"])); ?>">采购订单</a>
                                <?php } elseif($menu['title'] == "出库一览" && session('user_auth.group_id') == C('JOIN_GROUP')) { ?>
                                        <a class="item" href="<?php echo (u($menu["url"])); ?>">销售订单</a>
                                <?php } else { ?>
                                        <a class="item" href="<?php echo (u($menu["url"])); ?>"><?php echo ($menu["title"]); ?></a>
                                <?php } ?>
                                </li>

                                <?php if($menu['title'] == "商品目录" && session('user_auth.group_id') == C('JOIN_GROUP')) { ?>
                                    <li><a class="item" href="/index.php?s=/Admin/Psi/order.html">采购订单</a></li>
                                <?php } endforeach; endif; else: echo "" ;endif; ?>
                        </ul><?php endif; ?>
                    <!-- /子导航 --><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        
        <!-- /子导航 -->
    </div>
    <!-- /边栏 -->

    <!-- 内容区 -->
    <div id="main-content">
        <div id="top-alert" class="fixed alert alert-error" style="display: none;">
            <button class="close fixed" style="margin-top: 4px;">&times;</button>
            <div class="alert-content">这是内容</div>
        </div>
        <div id="main" class="main">
            
            <!-- nav -->
            <?php if(!empty($_show_nav)): ?><div class="breadcrumb">
                <span>您的位置:</span>
                <?php $i = '1'; ?>
                <?php if(is_array($_nav)): foreach($_nav as $k=>$v): if($i == count($_nav)): ?><span><?php echo ($v); ?></span>
                    <?php else: ?>
                    <span><a href="<?php echo ($k); ?>"><?php echo ($v); ?></a>&gt;</span><?php endif; ?>
                    <?php $i = $i+1; endforeach; endif; ?>
            </div><?php endif; ?>
            <!-- nav -->
            

            
<style>
	.columns-mod { width:460px; margin-top:20px; }
	.columns-mod th { width:auto; }
	.sys-info th, .sys-info td { padding:6px 0; line-height:18px; height:18px;}
	.fb .fl { margin-left:30px; }
	.fb span, .cf span { font-weight:bold; }
	.fb p { line-height:38px; }
</style>


<form action="<?php echo U('Psi/out_do');?>" method="POST" id="list" class="form-horizontal">
	<!-- 标题栏 -->
	<div class="main-title">
		<h2>创建出库单</h2>
	</div>
	<div class="cf">
		<div class="fl">
            <span>出库单号：</span>
            <input name="no" type="text" class="text large" placeholder="请填写出库单号" value="<?php echo C('SITE_OUT_NO');?>"/>&nbsp;&nbsp;&nbsp;
            <button class="btn confirm" href="javascript:void(0);" id="submit" type="submit" target-form="form-horizontal">提交出库</button>
        </div>
        <div class="fr">
        	<span>业务员：</span>
			<input name="info1" type="text" class="text input-small" value="<?php echo C('SITE_OUT_INFO1');?>"/>&nbsp;&nbsp;&nbsp;
			<span>发货日期：</span>
			<input name="date" type="text" class="text time" placeholder="请选择时间"/>&nbsp;&nbsp;&nbsp;
			<span>单位：元&nbsp;&nbsp;&nbsp;</span>
        </div>
    </div>
	    <!-- 数据列表 -->
	    <div class="data-table table-striped">
		<table class="">
	    <thead>
	        <tr>
				<th width="14%">产品编码</th>
				<th width="5%">OE码</th>
				<th width="13%">产品名称</th>
				<th>适用车型</th>
				<th width="">库位号</th>
				<th width="">库存</th>
				<th width="">订购数量</th>
				<th width="">单位</th>
				<th width="">单价</th>
				<th width="">合计</th>
				<th width="10%">出货</th>
				<th width="10%">箱号</th>
			</tr>
	    </thead>
	    <tbody>	
			<?php if(!empty($_list)): if(is_array($_list["goods"])): $i = 0; $__LIST__ = $_list["goods"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(!empty($vo["code"])): ?><tr>
		            <td><a href="<?php echo U('Psi/stock_info');?>&id=<?php echo ($vo["id"]); ?>"><?php echo ($vo["code"]); ?></a></td>
		            <td><?php echo ($vo["OE"]); ?></td>
					<td><?php echo ($vo["name"]); ?></td>
					<td><?php echo ($vo["standard"]); ?></td>
					<td><?php echo ($vo["store_no"]); ?></td>
					<td><?php echo ($vo["last"]); ?></td>
					<td><?php echo ($vo["num"]); ?></td>
					<td><?php echo ($vo["unit"]); ?></td>
					<td><?php echo ($vo["sale_price"]); ?></td>
					<td><?php echo ($vo["total_price"]); ?></td>
					<td><input name="info[<?php echo ($i); ?>][num]" id="num_<?php echo ($vo["code"]); ?>" type="text" class="text input-small" placeholder="本期出货" /></td>
					<td><input name="info[<?php echo ($i); ?>][box_no]" type="text" class="text input-small" placeholder="填写箱号" /></td>
					<input type="hidden" name="info[<?php echo ($i); ?>][sid]" value="<?php echo ($vo["id"]); ?>" /> 
					<input type="hidden" name="info[<?php echo ($i); ?>][ware]" value="<?php echo ($vo["last"]); ?>" /> 
					<input type="hidden" name="info[<?php echo ($i); ?>][num_i]" value="<?php echo ($vo["num"]); ?>" />
					<input type="hidden" name="info[<?php echo ($i); ?>][code]" value="<?php echo ($vo["code"]); ?>" />
					<!-- 扫描枪用意 -->
					<input type="hidden" id="<?php echo ($vo["code"]); ?>" value="0" />
					<input type="hidden" id="limit_<?php echo ($vo["code"]); ?>" value="<?php echo ($vo["num"]); ?>" />
				</tr>
				<?php $sum += $vo['num']; ?>
				<?php $total += $vo['total_price']; endif; endforeach; endif; else: echo "" ;endif; ?>
			<tr>
				<td colspan="6" style="font-weight:bold; text-align:center"> 合 计 </td>
				<td><?php echo ($sum); ?></td>
				<td>——</td>
				<td>——</td>
				<td><?php echo ($total); ?></td>
				<td colspan="2">——</td>
			</tr>
			    <input type="hidden" name="customer" value="<?php echo ($_list["uid"]); ?>">
				<input type="hidden" name="from_no" value="<?php echo ($_list["order_id"]); ?>" /> 
				<input type="hidden" name="from_id" value="<?php echo ($_list["id"]); ?>" />
				<input type="hidden" name="nums" value="0">
			<?php else: ?>
			<td colspan="9" class="text-center"> aOh! 暂时还没有内容! </td><?php endif; ?>
		</tbody>
	    </table>
		</div>
	    <div class="page">
	        <?php echo ($_page); ?>
	    </div>
		<div class="fb">
			<p>
				<span>开票：</span>
				<input name="info2" type="text" class="text input-small" value="<?php echo C('SITE_OUT_INFO2');?>" />&nbsp;&nbsp;&nbsp;
				<span>主管：</span>
				<input name="info3" type="text" class="text input-small" value="<?php echo C('SITE_OUT_INFO3');?>" />&nbsp;&nbsp;&nbsp;
				<span>会计：</span>
				<input name="info4" type="text" class="text input-small" value="<?php echo C('SITE_OUT_INFO4');?>" />&nbsp;&nbsp;&nbsp;
				<span>审核：</span>
				<input name="info5" type="text" class="text input-small" value="<?php echo C('SITE_OUT_INFO5');?>" />&nbsp;&nbsp;&nbsp;
				<span>仓库人：</span>
				<input name="info6" type="text" class="text input-small" value="<?php echo C('SITE_OUT_INFO6');?>" />&nbsp;&nbsp;&nbsp;
				<span>物流：</span>
				<input name="info7" type="text" class="text" value="<?php echo C('SITE_OUT_INFO7');?>" />&nbsp;&nbsp;&nbsp;
			</p>
			<p>
				<span>出库备注：</span>
				<input name="note" type="text" class="text input-large" />&nbsp;&nbsp;&nbsp;
				<span>订单编号：</span>
				<?php echo ($_list["order_id"]); ?>&nbsp;&nbsp;&nbsp;
				<span>扫描位置：</span>
				<input type="text" id="draw" class="text input" />
			</p>
			<div class="data-table table-striped" style="width:500px;">
				<table class="">
				    <thead>
				        <tr>
				        	<th>Id</th>
							<th width="">包装品名称</th>
							<th width="">成本价格</th>
							<th width="">库存量</th>
							<th width="">出货数</th>
						</tr>
				    </thead>
				    <tbody>
						<?php if(is_array($_ex_list)): $i = 0; $__LIST__ = $_ex_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
								<td><?php echo ($vo["id"]); ?></td>
					            <td><a href=""><?php echo ($vo["name"]); ?></a></td>
					            <td><?php echo ($vo["price"]); ?></td>
								<td><?php echo ($vo["num"]); ?></td>
								<td><input type="text" class="text input-small" name="exstock[<?php echo ($vo["id"]); ?>]" value="0" /></td>
							</tr><?php endforeach; endif; else: echo "" ;endif; ?>
					</tbody>
			    </table>
			</div>

			<div class="fl">
				<div class="columns-mod">
			        <div class="hd cf">
			            <h5>供方</h5>
			            <div class="title-opt"></div>
			        </div>
       				<div class="bd" style="height:auto;"><div class="sys-info">
            			<table><tbody>
			                <tr>
			                    <th>购货单位</th>
			                    <td><?php echo ($_list["company"]); ?></td>
			                </tr>
			                <tr>
			                    <th>联系人</th>
			                    <td><?php echo ($_list["name"]); ?></td>
			                </tr>
			                <tr>
			                    <th>电话</th>
			                    <td><?php echo ($_list["tel"]); ?></td>
			                </tr>
			                <tr>
			                    <th>地址</th>
			                    <td><?php echo ($_list["address"]); ?></td>
			                </tr>	
            			</tbody></table>
        			</div></div>
    			</div>				
			</div>
			<div class="fl">
				<div class="columns-mod">
			        <div class="hd cf">
			            <h5>需方</h5>
			            <div class="title-opt"></div>
			        </div>
       				<div class="bd" style="height:auto;"><div class="sys-info">
            			<table><tbody>
			                <tr>
			                    <th>单位名称</th>
			                    <td><?php echo C('COMPANY_NAME') ?></td>
			                </tr>
			                <tr>
			                    <th>单位地址</th>
			                    <td><?php echo C('COMPANY_ADDRESS') ?></td>
			                </tr>
			                <tr>
			                    <th>公司电话</th>
			                    <td><?php echo C('COMPANY_PHONE') ?></td>
			                </tr>
			                <tr>
			                    <th>公司传真</th>
			                    <td><?php echo C('COMPANY_FAX') ?></td>
			                </tr>	
            			</tbody></table>
        			</div></div>
    			</div>	
			</div>
			<div style="clear:both"></div>


		</div>

	</form>

        </div>
        <div class="cont-ft">
            <div class="copyright">
                <div class="fl">感谢使用德众进销存管理系统</div>
                <div class="fr">V1.0 Beta</div>
            </div>
        </div>
    </div>
    <!-- /内容区 -->
    <script type="text/javascript">
    (function(){
        var ThinkPHP = window.Think = {
            "ROOT"   : "", //当前网站地址
            "APP"    : "/index.php?s=", //当前项目地址
            "PUBLIC" : "/Public", //项目公共目录地址
            "DEEP"   : "<?php echo C('URL_PATHINFO_DEPR');?>", //PATHINFO分割符
            "MODEL"  : ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],
            "VAR"    : ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"]
        }
    })();
    </script>
    <script type="text/javascript" src="/Public/static/think.js"></script>
    <script type="text/javascript" src="/Public/Admin/js/common.js"></script>
    <script type="text/javascript">
        +function(){
            var $window = $(window), $subnav = $("#subnav"), url;
            $window.resize(function(){
                $("#main").css("min-height", $window.height() - 130);
            }).resize();

            /* 左边菜单高亮 */
            url = window.location.pathname + window.location.search;
            url = url.replace(/(\/(p)\/\d+)|(&p=\d+)|(\/(id)\/\d+)|(&id=\d+)|(\/(group)\/\d+)|(&group=\d+)/, "");
            $subnav.find("a[href='" + url + "']").parent().addClass("current");

            /* 左边菜单显示收起 */
            $("#subnav").on("click", "h3", function(){
                var $this = $(this);
                $this.find(".icon").toggleClass("icon-fold");
                $this.next().slideToggle("fast").siblings(".side-sub-menu:visible").
                      prev("h3").find("i").addClass("icon-fold").end().end().hide();
            });

            $("#subnav h3 a").click(function(e){e.stopPropagation()});

            /* 头部管理员菜单 */
            $(".user-bar").mouseenter(function(){
                var userMenu = $(this).children(".user-menu ");
                userMenu.removeClass("hidden");
                clearTimeout(userMenu.data("timeout"));
            }).mouseleave(function(){
                var userMenu = $(this).children(".user-menu");
                userMenu.data("timeout") && clearTimeout(userMenu.data("timeout"));
                userMenu.data("timeout", setTimeout(function(){userMenu.addClass("hidden")}, 100));
            });

	        /* 表单获取焦点变色 */
	        $("form").on("focus", "input", function(){
		        $(this).addClass('focus');
	        }).on("blur","input",function(){
				        $(this).removeClass('focus');
			        });
		    $("form").on("focus", "textarea", function(){
			    $(this).closest('label').addClass('focus');
		    }).on("blur","textarea",function(){
			    $(this).closest('label').removeClass('focus');
		    });

            // 导航栏超出窗口高度后的模拟滚动条
            var sHeight = $(".sidebar").height();
            var subHeight  = $(".subnav").height();
            var diff = subHeight - sHeight; //250
            var sub = $(".subnav");
            if(diff > 0){
                $(window).mousewheel(function(event, delta){
                    if(delta>0){
                        if(parseInt(sub.css('marginTop'))>-10){
                            sub.css('marginTop','0px');
                        }else{
                            sub.css('marginTop','+='+10);
                        }
                    }else{
                        if(parseInt(sub.css('marginTop'))<'-'+(diff-10)){
                            sub.css('marginTop','-'+(diff-10));
                        }else{
                            sub.css('marginTop','-='+10);
                        }
                    }
                });
            }
        }();
    </script>
    
	<script type="text/javascript">
		$(function(){
		    $('.num').change(function() {
		    	if ( $(this).val() < 0 ) { 
					alert('数目不能小于等于零。'); 
					$(this).val(0); 
					return false; 
				};
		    	var sum = 0;
		    	var i = 1;
		    	$('.num').each(function() {
		    		var last = $('#num_'+i).val();
		    		if (parseInt($(this).val()) > parseInt(last)) { 
		    			alert('数目不能大于合同剩余量。'); 
		    			$(this).val(0); 
		    		};
		    		sum += parseInt($(this).val());	
		    		i++;	    		
		    	});
		    	$('#sum_do').html(sum);
		    });
		});
	</script>

	<link href="/Public/static/datetimepicker/css/datetimepicker.css" rel="stylesheet" type="text/css">
    <?php if(C('COLOR_STYLE')=='blue_color') echo '<link href="/Public/static/datetimepicker/css/datetimepicker_blue.css" rel="stylesheet" type="text/css">'; ?>
    <link href="/Public/static/datetimepicker/css/dropdown.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="/Public/static/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="/Public/static/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
    <script type="text/javascript">
    $(function(){
    	$('#draw').focus();

        $('.time').datetimepicker({
            format: 'yyyy-mm-dd',
            language:"zh-CN",
            minView:2,
            autoclose:true
        });
        showTab();

        $('#draw').bind('keypress',function(){   
            if(event.keyCode == 13){    	
	        	var oe  = $(this).val();
	        	var num = parseInt($('#'+oe).val());
	        	num += 1;
	        	var limit = $('#limit_'+oe).val();
	        	// if (num > limit) { 
	        	// 	$(this).val('');
	        	// 	alert('出货量已经超出订购数量。');
	        	// 	return false; 
	        	// }
	        	$('#'+oe).attr('value',num);
	        	$('#num_'+oe).attr('value',num);
	        	if (num > limit) { 
	        		$('#num_'+oe).attr('style',"color:red");
	        	}
	        	$(this).val('');
	        	return false;
	        }
        });

        $('#apply').click(function() {
            $('#list').submit();
        });  

    });
    </script>

	<script type="text/javascript">
	    //导航高亮
	    highlight_subnav('<?php echo U('Psi/out_order');?>');
	</script>


</body>
</html>