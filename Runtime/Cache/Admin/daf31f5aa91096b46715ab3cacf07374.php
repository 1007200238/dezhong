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
            

            

	<!-- 新加样式 -->
	<style>
		.drop-down { width:100px !important; }
		.sort-txt { width:60px !important; }
		.change { height:30px !important; }
		.fl { margin-right:10px; }
		.asc,.desc { color:#fff; text-decoration: none; }
		a.asc:hover,a.desc:hover { color:#fff; text-decoration: none;}
	</style>

	<!-- 标题栏 -->
	<div class="main-title">
		<h2>产品列表</h2>
	</div>
	<div class="cf">
		<div class="fl">
			<?php if (session('user_auth.group_id') != C('JOIN_GROUP')){ ?>
            <a class="btn" href="<?php echo U('Psi/stock_add');?>">新 增</a>
            <button class="btn ajax-post confirm" url="<?php echo U('Psi/stock_del');?>" target-form="ids">删 除</button>
            <?php } ?>
            <?php if (session('user_auth.group_id') == C('JOIN_GROUP')){ ?>
            	<a class="btn" href="<?php echo U('Psi/cart');?>">订购单确认</a>
            <?php } ?>
        </div>
		<!-- 高级搜索 -->
		<div class="search-form fr cf">
			<div class="sleft">
				<div class="drop-down">
					<span id="sch-sort-txt" class="sort-txt" data="<?php echo ($type); ?>"><?php if(I('type') == ''): ?>产品名称<?php else: echo get_status_type($type); endif; ?></span>
					<i class="arrow arrow-down"></i>
					<ul id="sub-sch-menu" class="nav-list hidden">
						<li><a href="javascript:;" value="2">产品名称</a></li>
						<li><a href="javascript:;" value="1">产品编号</a></li>	
						<li><a href="javascript:;" value="3">OE码</a></li>
					</ul>
				</div>
				<input type="text" name="keyword" class="search-input" value="<?php echo I('keyword');?>" placeholder="请输入查询关键字">
				<a class="sch-btn" href="javascript:;" id="search" url="<?php echo U('Psi/stock');?>"><i class="btn-search"></i></a>
			</div>
			<div class="fl">
				<div class="controls">
                    <select name="category" class="change" id="category" onchange="gourl('category');">
                        <option value="0">选择产品分类</option>
						<?php if(is_array($cat_pro)): $i = 0; $__LIST__ = $cat_pro;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>" <?php if(I('category')==$vo['id']){ ?> selected <?php } ?> ><?php echo ($vo["title"]); ?></option>
							<?php if ($vo['_']) { ?>
								<?php if(is_array($vo["_"])): $i = 0; $__LIST__ = $vo["_"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo2): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo2["id"]); ?>" <?php if(I('category')==$vo2['id']){ ?> selected <?php } ?>> -- <?php echo ($vo2["title"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
							<?php } endforeach; endif; else: echo "" ;endif; ?>
                    </select>                    
                </div>
			</div>
			<?php if (session('user_auth.group_id') != C('JOIN_GROUP')){ ?>
			<div class="fl">
				<div class="controls">
                    <select name="supplier" class="change" id="supplier" onchange="gourl('supplier');">
                        <option value="0">选择供应商</option>
	                    <?php if(is_array($sup_list)): $i = 0; $__LIST__ = $sup_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>" <?php if(I('supplier')==$vo['id']){ ?> selected <?php } ?> ><?php echo ($vo["company"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>                    
                </div>
			</div>
			<?php } ?>
			<div class="fl">
				<div class="controls">
                    <select name="status" class="change" id="status" onchange="gourl('status');">
                    	<option value="0">筛选最新与促销</option>
                        <option value="2" <?php if(I('status')==2){ ?> selected <?php } ?> >最新商品</option>
                        <option value="3" <?php if(I('status')==3){ ?> selected <?php } ?> >促销商品</option>
                    </select>                    
                </div>
			</div>
		</div>
    </div>

    <!-- 数据列表 -->
    <div class="data-table table-striped">
	<table class="">
    <thead>
        <tr>
    	<?php if (session('user_auth.group_id') != C('JOIN_GROUP')){ ?>
		<th class="row-selected row-selected"><input class="check-all" type="checkbox"/></th>
		<?php } ?>
		<th class="" width="1%"><a href="#" class="asc" onclick="dosort($(this),'id');">Id</a></th>
		<th class="" width="13%">产品编号</th>
		<th class="" width="14%">产品名称</th>
		<th class="" width="8%"><a href="#" class="asc" onclick="dosort($(this),'category');">产品分类</a></th>
		<?php if (session('user_auth.group_id') != C('JOIN_GROUP')){ ?>
			<th class="">供应商</th>
		<?php } else { ?>
			<th class=""><a href="#" class="asc" onclick="dosort($(this),'sale_price');">销售价</a></th>
			<!-- <th class="">销售价（不含税）</th> -->
		<?php } ?>
		<th class="">适用车型</th>
		<th class="">操作</th>
		</tr>
    </thead>
    <tbody style="font-size:13px">
		<?php if(!empty($_list)): if(is_array($_list)): $i = 0; $__LIST__ = $_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
			<?php if (session('user_auth.group_id') != C('JOIN_GROUP')){ ?>
            <td><input class="ids" type="checkbox" name="id[]" value="<?php echo ($vo["id"]); ?>" /></td>
            <?php } ?>
            <td><?php echo ($vo["id"]); ?></td>
			<td><?php echo ($vo["code"]); ?></td>
			<td><a href="<?php echo U('Psi/stock_info');?>&id=<?php echo ($vo["id"]); ?>"><?php echo ($vo["name"]); ?></a></td>
			<td><?php echo ($vo["category"]); ?></td>
			<?php if (session('user_auth.group_id') != C('JOIN_GROUP')){ ?>
				<td><?php echo ($vo["supplier"]); ?></td>
			<?php } else { ?>
				<!-- <td><?php echo ($vo["sale_price"]); ?></td> -->
				<td><?php echo ($vo["sale_price_off"]); ?></td>
			<?php } ?>
			<td><?php echo ($vo["standard"]); ?></td>
			<td> 
				<a href="<?php echo U('Psi/stock_info');?>&id=<?php echo ($vo["id"]); ?>">详细</a> | 
				<?php if (session('user_auth.group_id') == C('JOIN_GROUP')){ ?>
					<!-- 加盟商专属 订购选项 -->
					<?php if (in_array($vo['id'],session('cart_in'))){ ?>
					<!-- 判断是否已订购 在session中 -->
					<span style="color:grey" >已添加</span>
					<?php } else { ?>
					<a class="ajax-get" href="<?php echo U('Psi/cart_in');?>&id=<?php echo ($vo["id"]); ?>">加入购物单</a>
					<?php } ?>
				<?php } else { ?>
				<a href="<?php echo U('Psi/stock_edit');?>&id=<?php echo ($vo["id"]); ?>">修改</a> | 
				<a href="<?php echo U('Psi/stock_del?id='.$vo['id']);?>" class="confirm ajax-get">删除</a>
				<?php } ?>
			</td>
		</tr><?php endforeach; endif; else: echo "" ;endif; ?>
		<?php else: ?>
		<td colspan="9" class="text-center"> aOh! 暂时还没有内容! </td><?php endif; ?>
	</tbody>
    </table>
	</div>
    <div class="page">
        <?php echo ($_page); ?>
    </div>

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
    
	<script src="/Public/static/thinkbox/jquery.thinkbox.js"></script>

	<script type="text/javascript">
    //导航高亮
    highlight_subnav('<?php echo U('User/index');?>');
	</script>

	<script type="text/javascript">
	$(function(){
		//搜索功能
		$("#search").click(function(){
			var url    = $(this).attr('url');
			var type   = $("#sch-sort-txt").attr("data");
	        var query  = $('.search-form').find('input').serialize();
	        query = query.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g,'');
	        query = query.replace(/^&/g,'');
			if(type != ''){
				query = 'type=' + type + "&" + query;
	        }
	        if( url.indexOf('?')>0 ){
	            url += '&' + query;
	        }else{
	            url += '?' + query;
	        }
	        url += '&ask=1';
			window.location.href = url;
		});

		/* 状态搜索子菜单 */
		$(".search-form").find(".drop-down").hover(function(){
			$("#sub-sch-menu").removeClass("hidden");
		},function(){
			$("#sub-sch-menu").addClass("hidden");
		});
		$("#sub-sch-menu li").find("a").each(function(){
			$(this).click(function(){
				var text = $(this).text();
				$("#sch-sort-txt").text(text).attr("data",$(this).attr("value"));
				$("#sub-sch-menu").addClass("hidden");
			})
		});

	    //回车自动提交
	    $('.search-form').find('input').keyup(function(event){
	        if(event.keyCode===13){
	            $("#search").click();
	        }
	    });
	})

	//排序
	function dosort(ob,f){	
		if (getQueryString('orderby') == 'asc' && getQueryString('field') == f) { replaceParamVal('orderby','desc'); };
		if (getQueryString('orderby') == 'desc' && getQueryString('field') == f) { replaceParamVal('orderby','asc'); };
		if (getQueryString('field') != f) {
			var d = ob.attr('class');  //排序方式
			replaceParamVal('orderby|field',d+"|"+f);
		}
	}	

	//分类筛选
    function gourl(i){
    	var c = $("#"+i).val();  //参数值
    	replaceParamVal(i,c);
    }

	//获取url特定参数值
	function getQueryString(name) {
		var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
		var r = window.location.search.substr(1).match(reg);
		if (r != null) return unescape(r[2]); return null;
	}
   
	function replaceParamVal(strArr,reArr) {
		var nUrl = this.location.href.toString(); //获取当前url 
		// var nUrl = '/index.php?s=/Admin/Psi/stock.html&' + 'category=' + getQueryString("category") + "&supplier=" + getQueryString("supplier");
		var strArr = strArr.split("|"); //将传入的参数集合转成数组
		var reArr = reArr.split("|"); //将传入的参数对应值集合转成数组
		for(var i=0;i<strArr.length;i++){
			if (getQueryString(strArr[i]) == null) { 
				nUrl += '&'+strArr[i]+'='+reArr[i];
				continue;
			};
			var re = eval('/('+ strArr[i] +'=)([^&]*)/gi'); //获取传入参数的当前值
			var nUrl = nUrl.replace(re,strArr[i]+'='+reArr[i]); //替换为传入的对应值
		}
		this.location = nUrl + "&p=1"; //刷新页面
	}

	</script>



</body>
</html>