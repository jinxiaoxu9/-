<?php
namespace app\admin\Controller;

use Think\Controller;

use think\Request;
use think\Db;
/**
 * 用户控制器
 * 陶
 */
class TreeController extends AdminController
{


    /**
     * 用户列表
     * @author jry <bbs.sasadown.cn>
     */
    public function index(Request $request)
    {   
         // 搜索
        $pid        =   $request->param('keyword', '0', 'string');
        $user           =   Db::name('user');
        if($pid!='0')
        {
            /*$k_where['userid|username|account'] = array(
                $pid,
                $pid,
                $pid,
                '_multi' => true,
            );*/
            $k_where = 'CONCAT(`userid`,`username`,`account`) LIKE "%' . $pid . '%"';
            $query=$user->where($k_where)->Field('userid,pid')->find();
            $pid=$query['pid'];
        }
       
        $tree           =   $this->getTree($pid);
        $this->assign('tree',$tree);

        return $this->fetch();
    }


    public function getTree($pid='0')
    {
        $t=Db::name('user');
        $wherea=array(  
            "pid"=>$pid
         );
        $list=$t->where($wherea)->order('userid asc')->select();

        if(is_array($list)){
            $html = '';
                //$i++;
                foreach($list as $k => $v)
                {
                    $map['pid']=$v['userid'];
                    $count=$t->where($map)->count(1);
                    $class=$count==0 ? 'fa-user':'fa-sitemap';

                   if($v['pid'] == $pid)
                   {   
                        //父亲找到儿子
                        $html .= '<li style="display:none" >';
                        $html .= '<span><i class="icon-plus-sign '.$class.' blue "></i>';
                        $html .= $v['username'];
                        $html .= '</span> <a href="' . url('User/edit',array('userid'=>$v['userid'])).'">';
                        $html .= $v['account'];
                        $html .= '</a>';
                        $html .= $this->getTree($v['userid']);
                        $html = $html."</li>";
                   }
                }
            return $html ? '<ul>'.$html.'</ul>' : $html ;
        }
    }
    
    
}
