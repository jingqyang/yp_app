<?php
namespace app\index\model;
use think\Model;
use think\Db;

class Recommend extends Model{
    //获取首页banner图
    //接口地址      https://yp.dxshuju.com/app/public/index/getBannerInfo?bis_id =
    //关联表   yp_recommend        banner表
    public function getBanners($bis_id){
        $res = Db::table('yp_recommend')
                    ->field('bis_id,image,create_time')
                    ->where('bis_id = '.$bis_id.' and type = 1 and status = 1')
            ->order('listorder desc,create_time desc')
            ->limit(3)
            ->select();
        foreach ($res as $k=>$v)
        {
            $res[$k]['bis_id'] = $v['bis_id'];
            $res[$k]['image'] = "http://39.105.68.130:81/app/public/image/".str_replace('\\','/',$v['image']);
            $res[$k]['create_time'] = $v['create_time'];
        }
        return $res;
    }
}
