<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: yangweijie <yangweijiester@gmail.com> <code-tech.diandian.com>
// +----------------------------------------------------------------------
namespace Addons\SiteStat;
use Common\Controller\Addon;

/**
 * 系统环境信息插件
 * @author thinkphp
 */
class SiteStatAddon extends Addon{

    public $info = array(
        'name'=>'SiteStat',
        'title'=>'站点统计信息',
        'description'=>'统计站点的基础信息',
        'status'=>1,
        'author'=>'thinkphp',
        'version'=>'0.1'
    );

    public function install(){
        return true;
    }

    public function uninstall(){
        return true;
    }

    //实现的AdminIndex钩子方法
    public function AdminIndex($param){
        $config = $this->getConfig();
        $this->assign('addons_config', $config);
        if($config['display']){
            $info['user']	  =	M('Member')->count();
            $info['action']	  =	M('ActionLog')->count();
            $info['document'] =	M('Document')->count();
            $info['category'] =	M('Category')->count();
            $info['model']	  =	M('Model')->count();
    
            $info['stock']    = M('Stock')->count();
            $info['order']    = M('order')->count();
            $info['order_new']  = M('order')->where(array('status'=>'1'))->count();
            $info['apply']    = M('apply')->count();
                $info['customer'] = M('auth_group_access')->count();

            $map1['num']      = array('exp','< min_num OR (num > max_num AND max_num != 0)');   //预警产品筛选
            $info['warning']  = D('Warehouse')->where($map1)->count();

            // 加盟商信息
            $map2['warehouse'] = session('user_auth.uid');
            $info['join_ware'] = D('Warehouse')->where($map2)->count();  //加盟商库存
            $map1['warehouse'] = session('user_auth.uid');
            $info['join_warn'] = D('Warehouse')->where($map1)->count();  //加盟商预警

            $this->assign('info',$info);
            $this->display('info');
        }
    }
}