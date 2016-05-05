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
        .img_box { width:260px; height:230px; float:left; margin-right:3px; border:1px solid #999;}

    </style>


    <div class="main-title">
        <h2>订单详细</h2>
    </div>

    <div class="columns-mod">
        <div class="hd cf">
            <h5>产品编码：<?php echo ($info["code"]); ?></h5>
            <div class="title-opt"></div>
        </div>
        <div class="bd" style="height:auto;"><div class="sys-info">
            <table><tbody>
                <tr>
                    <th>产品编码</th>
                    <td><?php echo ($info["code"]); ?></td>
                </tr>
                <tr>
                    <th>产品名称</th>
                    <td><?php echo ($info["name"]); ?></td>
                </tr>
                <tr>
                    <th>OE码</th>
                    <td><?php echo ($info["OE"]); ?></td>
                </tr>
                <tr>
                    <th>单位</th>
                    <td><?php echo ($info["unit"]); ?></td>
                </tr>
                <tr>
                    <th>产品规格<span class="check-tips">（适用）</span></th>
                    <td><?php echo ($info["standard"]); ?></td>
                </tr>
                <tr>
                    <th>产品分类</th>
                    <td><?php echo ($info["category"]); ?></td>
                </tr>
            <?php if (session('user_auth.group_id') != C('JOIN_GROUP')){ ?>
                <tr>
                    <th>库位号</th>
                    <td><?php echo ($info["store_no"]); ?></td>
                </tr>
                <tr>
                    <th>供应商</th>
                    <td><?php echo ($info["supplier"]); ?></td>
                </tr>
                <tr>
                    <th>供应商产品编码</th>
                    <td><?php echo ($info["supplier_no"]); ?></td>
                </tr>
                <tr>
                    <th>进货价格（含税 元）</th>
                    <td><?php echo ($info["purch_price"]); ?></td>
                </tr>
                <tr>
                    <th>进货价格（不含税 元）</th>
                    <td><?php echo ($info["purch_price_off"]); ?></td>
                </tr>
            <?php } ?>
                <tr>
                    <th>销售价格（含税 元）</th>
                    <td><?php echo ($info["sale_price"]); ?></td>
                </tr>
                <tr>
                    <th>销售价格（不含税 元）</th>
                    <td><?php echo ($info["sale_price_off"]); ?></td>
                </tr>
                <tr>
                    <th>质保时间</th>
                    <td><?php echo ($info["warranty"]); ?></td>
                </tr>
            <?php if (session('user_auth.group_id') != C('JOIN_GROUP')){ ?>
                <tr>
                    <th>彩袋尺寸</th>
                    <td><?php echo ($info["bag_size"]); ?></td>
                </tr>
                <tr>
                    <th>彩盒尺寸</th>
                    <td><?php echo ($info["box_size"]); ?></td>
                </tr>
                <tr>
                    <th>彩袋价格</th>
                    <td><?php echo ($info["bag_price"]); ?></td>
                </tr>
                <tr>
                    <th>彩盒价格</th>
                    <td><?php echo ($info["box_price"]); ?></td>
                </tr> 
            <?php } ?>      
                <tr>
                    <th>产品图片</th>
                    <td>
                        <?php if(is_array($info["thumb"])): $j = 0; $__LIST__ = $info["thumb"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($j % 2 );++$j;?><div class="img_box">
                                <a href="<?php echo ($vo); ?>" target="_blank"><img width="100%" height="100%" src="<?php echo ($vo); ?>"></a>
                            </div><?php endforeach; endif; else: echo "" ;endif; ?>     
                    </td>
                </tr>
                <tr>
                    <th>备注1</th>
                    <td><?php echo ($info["note"]); ?></td>
                </tr>
                <tr>
                    <th>备注2</th>
                    <td><?php echo ($info["note_2"]); ?></td>
                </tr>
            </tbody></table>
        </div></div>
        <button style="margin:5px 0 20px 20px;" class="btn btn-return" onclick="javascript:history.back(-1);return false;">返 回</button>
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
    
    <script type="text/javascript">
        //导航高亮
        highlight_subnav('<?php echo U('Psi/stock');?>');
    </script>

</body>
</html>