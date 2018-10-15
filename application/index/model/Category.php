<?php
namespace app\index\model;
use think\Model;
use think\Db;

class Category extends Model{
    //获取一级分类信息(单用户版)接口
    //接口地址: https://yp.dxshuju.com/app/public/index/category/getFirstCatInfo?bis_id=
    //传输方式  $_GET
    //关联表       store_defined_category   商品分类表
    public function getFirstCatInfo($bis_id){
        //  cat_id  分类id    cat_name    分类名称
        $res = Db::table('store_defined_category')->field('id as cat_id,cat_name')
                ->where('bis_id = '.$bis_id.' and parent_id = 0 and status = 1')
                ->order('listorder desc')
                ->select();
        return $res;
    }

    //获取一级分类信息(多用户版)
    public function getFirstCatInfoMulti(){
        $res = Db::table('store_category')->field('id as cat_id,cat_name')
            ->where('parent_id = 0 and status = 1')
            ->order('listorder desc')
            ->select();
        return $res;
    }

    //根据一级id获取二级分类信息(单用户版)
    //接口地址: https://yp.dxshuju.com/app/public/index/category/getSecondCarInfoById
    //传输方式  $_POST
    //条件    key=>cat_id     value=>上一级传过来的id
    //关联表       store_defined_category   商品分类表

    public function getSecondCarInfoById($cat1_id){
        $res = Db::table('store_defined_category')->field('id as cat_id,cat_name')
            ->where('parent_id = '.$cat1_id.' and status = 1')
            ->order('listorder desc')
            ->select();

        $count = Db::table('store_defined_category')
            ->where('parent_id = '.$cat1_id.' and status = 1')
            ->count();

        if(!$res){
            echo json_encode(array(
                'statuscode'  => 0,
                'message'      => '暂无二级分类数据!'
            ));
            exit;
        }
        //将“全部分类”插入到第一位
        array_unshift($res,['cat_id'=>0,'cat_name'=>'全部分类']);

//        $new_count = ceil($count / 3);
        $new_res = array();

        for($i = 0; $i < $count+1; $i ++){
            for($j=0;$j<3;$j++) {
                $new_num = $i * 3 + $j;
//                var_dump($new_num);
                if ($new_num < $count + 1) {
                    $new_res[$i][] = $res[$new_num];
                } else {
                    break;
                }
            }

        }


        return $new_res;
    }

    //根据一级id获取二级分类信息(多用户版)
    public function getSecondCarInfoByIdMulti($cat1_id){
        $res = Db::table('store_category')->field('id as cat_id,cat_name')
            ->where('parent_id = '.$cat1_id.' and status = 1')
            ->order('listorder desc')
            ->select();

        $count = Db::table('store_category')
            ->where('parent_id = '.$cat1_id.' and status = 1')
            ->count();

        if(!$res){
            echo json_encode(array(
                'statuscode'  => 0,
                'message'      => '暂无二级分类数据!'
            ));
            exit;
        }

        array_unshift($res,['cat_id'=>0,'cat_name'=>'全部分类']);

        $new_count = ceil($count / 3);
        $new_res = array();

        for($i = 0; $i < $new_count+1; $i ++){
            for($j=0;$j<3;$j++){
                $new_num = $i*3+$j;
                if($new_num < $count+1){
                    $new_res[$i][] = $res[$new_num];
                }else{
                    break;
                }
            }
        }
        return $new_res;
    }
}