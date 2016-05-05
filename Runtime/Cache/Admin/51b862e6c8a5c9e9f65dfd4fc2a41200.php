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
        .uploadify-queue-item { position: inherit; }
        .thumb_queue { width:120px; height:100px; float:left; margin: 3px 3px 3px 0px; border:1px solid #999;}
        .thumb_del { position: relative; bottom:100%; left:75%; }
    </style>
    <script type="text/javascript" src="/Public/static/uploadify/jquery.uploadify.min.js"></script>
    <div class="main-title">
        <h2>修改产品</h2>
    </div>
    <form action="<?php echo U();?>" method="post" class="form-horizontal">
        <div class="form-item">
            <label class="item-label">产品编码<span class="check-tips">（请按15位标准格式填写，例：AMD-DPJ-JZH-L01）</span></label>
            <div class="controls">
                <input type="text" class="text input-large" name="code" value="<?php echo ($info["code"]); ?>">
            </div>
        </div>
        <div class="form-item">
            <label class="item-label">产品名称<span class="check-tips"></span></label>
            <div class="controls">
                <input type="text" class="text input-large" name="name" value="<?php echo ($info["name"]); ?>">
            </div>
        </div>
        <div class="form-item">
            <label class="item-label">库位号<span class="check-tips"></span></label>
            <div class="controls">
                <input type="text" class="text input-large" name="store_no" value="<?php echo ($info["store_no"]); ?>">
            </div>
        </div>
        <div class="form-item">
            <label class="item-label">单位<span class="check-tips"></span></label>
            <div class="controls">
                <input type="text" class="text input-large" name="unit" value="<?php echo ($info["unit"]); ?>">
            </div>
        </div>
        <div class="form-item">
            <label class="item-label">产品规格<span class="check-tips">（勾选适用车型）</span></label>
            <div class="controls">
				<?php if(is_array($cat_car)): $i = 0; $__LIST__ = $cat_car;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if (in_array($vo['title'], $info['car'])) { ?>
                        <label class="checkbox inline">
                            <input type="checkbox" id="inlineCheckbox<?php echo ($vo["id"]); ?>" name="car[]" value="<?php echo ($vo["id"]); ?>" checked><?php echo ($vo["title"]); ?>
                        </label>  
                    <?php } else { ?>
                        <label class="checkbox inline">
                            <input type="checkbox" id="inlineCheckbox<?php echo ($vo["id"]); ?>" name="car[]" value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["title"]); ?>
                        </label>
                    <?php } endforeach; endif; else: echo "" ;endif; ?>
            </div>
        </div>
        <div class="form-item">
            <label class="item-label">产品分类<span class="check-tips">（请选择产品分类）</span></label>
            <div class="controls">
                <select name="category">
            		<option value="">选择分类</option>
 					<?php if(is_array($cat_pro)): $i = 0; $__LIST__ = $cat_pro;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if ($vo['title'] == $info['category']) { ?>
                                <option value="<?php echo ($vo["id"]); ?>" selected><?php echo ($vo["title"]); ?></option>   
                        <?php } else { ?>
						        <option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["title"]); ?></option>
                        <?php } endforeach; endif; else: echo "" ;endif; ?>
            	</select>
            </div>
        </div>
        <div class="form-item">
            <label class="item-label">供应商<span class="check-tips"></span></label>
            <div class="controls">
                <select name="supplier">
                    <option value="">选择供应商</option>
                    <?php if(is_array($sup_list)): $i = 0; $__LIST__ = $sup_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if ($vo['company'] == $info['supplier']) { ?>
                                <option value="<?php echo ($vo["id"]); ?>" selected><?php echo ($vo["company"]); ?></option>  
                        <?php } else { ?>
                                <option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["company"]); ?></option>
                        <?php } endforeach; endif; else: echo "" ;endif; ?>
                </select>
            </div>
        </div>
        <div class="form-item">
            <label class="item-label">供应商产品编号<span class="check-tips"></span></label>
            <div class="controls">
                <input type="text" class="text input-large" name="supplier_no" value="<?php echo ($info["supplier_no"]); ?>">
            </div>
        </div>
        <div class="form-item">
            <label class="item-label">进货价<span class="check-tips">(含税 元)</span></label>
            <div class="controls">
                <input type="text" class="text input-large" name="purch_price" value="<?php echo ($info["purch_price"]); ?>">
            </div>
        </div>
        <div class="form-item">
            <label class="item-label">进货价<span class="check-tips">(不含税 元)</span></label>
            <div class="controls">
                <input type="text" class="text input-large" name="purch_price_off" value="<?php echo ($info["purch_price_off"]); ?>">
            </div>
        </div>
        <div class="form-item">
            <label class="item-label">销售价<span class="check-tips">(含税 元)</span></label>
            <div class="controls">
                <input type="text" class="text input-large" name="sale_price" value="<?php echo ($info["sale_price"]); ?>">
            </div>
        </div>
        <div class="form-item">
            <label class="item-label">销售价<span class="check-tips">(不含税 元)</span></label>
            <div class="controls">
                <input type="text" class="text input-large" name="sale_price_off" value="<?php echo ($info["sale_price_off"]); ?>">
            </div>
        </div>
        <div class="form-item">
            <label class="item-label">质保时间<span class="check-tips"></span></label>
            <div class="controls">
            	<select name="warranty">
            		<option value="无质保" <?php if($info['warranty'] == "无质量") { echo "selected"; } ?>>无质保</option>
            		<option value="半年" <?php if($info['warranty'] == "半年") { echo "selected"; } ?>>半年</option>
            		<option value="一年" <?php if($info['warranty'] == "一年") { echo "selected"; } ?>>一年</option>
            		<option value="一年半" <?php if($info['warranty'] == "一年半") { echo "selected"; } ?>>一年半</option>
            		<option value="两年" <?php if($info['warranty'] == "两年") { echo "selected"; } ?>>两年</option>
            		<option value="两年半" <?php if($info['warranty'] == "两年半") { echo "selected"; } ?>>两年半</option>
            	</select>
            </div>
        </div>
        <div class="form-item">
            <label class="item-label">彩袋尺寸<span class="check-tips"></span></label>
            <div class="controls">
                <input type="text" class="text input-large" name="bag_size" value="<?php echo ($info["bag_size"]); ?>">
            </div>
        </div>
        <div class="form-item">
            <label class="item-label">彩盒尺寸<span class="check-tips"></span></label>
            <div class="controls">
                <input type="text" class="text input-large" name="box_size" value="<?php echo ($info["box_size"]); ?>">
            </div>
        </div>
        <div class="form-item">
            <label class="item-label">彩袋价格<span class="check-tips">（元）</span></label>
            <div class="controls">
                <input type="text" class="text input-large" name="bag_price" value="<?php echo ($info["bag_price"]); ?>">
            </div>
        </div>

        <div class="form-item">
            <label class="item-label">彩盒价格<span class="check-tips">（元）</span></label>
            <div class="controls">
                <input type="text" class="text input-large" name="box_price" value="<?php echo ($info["box_price"]); ?>">
            </div>
        </div>

        <div class="form-item">
            <label class="item-label">OE码<span class="check-tips">（选填）</span></label>
            <div class="controls">
                <input type="text" class="text input-large" name="OE" value="<?php echo ($info["OE"]); ?>">
            </div>
        </div>

        <div class="form-item">
            <label class="item-label">产品属性<span class="check-tips"></span></label>
            <div class="controls">
                <input type="checkbox" name="status[]" value="1" <?php if(in_array('1',$info['status'])){ echo "checked"; } ?> >&nbsp;普通商品&nbsp;&nbsp;
                <input type="checkbox" name="status[]" value="2" <?php if(in_array('2',$info['status'])){ echo "checked"; } ?> >&nbsp;新品上市&nbsp;&nbsp;
                <input type="checkbox" name="status[]" value="3" <?php if(in_array('3',$info['status'])){ echo "checked"; } ?> >&nbsp;促销产品&nbsp;&nbsp;
            </div>
        </div>

        <div class="form-item">
            <label class="item-label">产品图片<span class="check-tips">（请勿上传三张以上图片）</span></label>
            <div class="controls">
                <div id="imgs">
                    <?php if(is_array($info["thumb"])): $j = 0; $__LIST__ = $info["thumb"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($j % 2 );++$j;?><div id="pic_<?php echo ($j); ?>" class="thumb_queue">
                            <img width="100%" height="100%" src="<?php echo ($vo); ?>">
                            <a href="javascript:void(0);" onclick="remove_thumb(<?php echo ($j); ?>); return false;" class="thumb_del">删除</a>
                            <input type="hidden" name="thumb[]" value="<?php echo ($vo); ?>" id="p_<?php echo ($j); ?>">
                        </div><?php endforeach; endif; else: echo "" ;endif; ?>           

                </div>
                <div style="clear:both"></div>
                <!-- <a href="javascript:$('#file_upload').uploadify('upload','*')" class="btn" style="background-color:#CC3900">上传</a> -->
                <input id="file_upload" name="file_upload" type="file" multiple="true" value="" />
                
            </div>
        </div>
        <script type="text/javascript">
            var img = '';
            var con = '';
            var i   = '';
            $('#file_upload').uploadify({
                    'queueSizeLimit' : 3,
                    'swf'      : '/Public/static/uploadify/uploadify.swf',
                    'uploader' : '<?php echo U("Psi/upload");?>',   //上传的方法
                    'buttonText' : '图片上传',
                    'auto'       : true,
                    'preventCaching' : true,
                    'onUploadSuccess' : function(file, data, response) {
                     //把所有上传的图片都放入DIV中
                        i++;
                        img = "<div id='thumb_"+i+"' class='thumb_queue'><img width='100%' height='100%' src='/Public/Uploads/"+data+"'><a href='javascript:void(0);' onclick='del_thumb("+i+"); return false;' class='thumb_del'>删除</a><input type='hidden' name='thumb[]' value='Public/Uploads/"+data+"'><input type='hidden' value='"+data+"' id='con_"+i+"'/></div>";
                        $('#imgs').append(img);                 
                    }
            });
            function del_thumb(j) {
                var con = $('#con_'+j).val();
                $.post('/index.php?s=/Admin/Psi/thumb_del.html',{file_name: con}, function(datas){
                    return false;
                });
                $('#thumb_'+j).html('');
                $('#thumb_'+j).attr('class','');
            }

            function remove_thumb(j) {
                if (!confirm("删除后将无法恢复，确认删除么？")) { return false; };
                var p = $('#p_'+j).val();
                $.post('/index.php?s=/Admin/Psi/thumb_remove.html',{file_name: p}, function(datas){
                    return false;
                });
                $('#pic_'+j).html('');
                $('#pic_'+j).attr('class','');
            }

        </script>

        <div class="form-item">
            <label class="item-label">备注1<span class="check-tips">（50字以内，选填）</span></label>
            <div class="controls">
                <input type="text" class="text input-large" name="note" value="<?php echo ($info["note"]); ?>">
            </div>
        </div>
        <div class="form-item">
            <label class="item-label">备注2<span class="check-tips">（50字以内，选填）</span></label>
            <div class="controls">
                <input type="text" class="text input-large" name="note_2" value="<?php echo ($info["note_2"]); ?>">
            </div>
        </div>
        <div class="form-item">
            <button class="btn submit-btn ajax-post" id="submit" type="submit" target-form="form-horizontal">确 定</button>
            <button class="btn btn-return" onclick="javascript:history.back(-1);return false;">返 回</button>
        </div>
        <input type="hidden" name="id" value="<?php echo ($info["id"]); ?>">
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
        //导航高亮
        highlight_subnav('<?php echo U('Psi/stock');?>');
    </script>

</body>
</html>