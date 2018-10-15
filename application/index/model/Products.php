<?php
namespace app\index\model;
use think\Model;
use think\Db;

class Products extends Model{
    //获取推荐商品列表(单用户版)
    ////接口地址      https://yp.dxshuju.com/app/public/index/index/getRecommendProInfo?bis_id=1
    // 关联表        store_products  商品表     store_pro_images    商品图片表

    public function getRecommendProInfo($bis_id){
        //  p_name  商品名称      thumb    商品缩略图url  original_price  零售价   associator_price  会员价  jifen   积分
        $res = Db::table('store_products')
                ->alias('pro')
                ->field('pro.id as pro_id,pro.p_name,i.thumb,pro.original_price,pro.associator_price,pro.jifen * 5 as jifen')
                ->join('store_pro_images i','pro.id = i.p_id','LEFT')
            //  bis_id  商家id       on_sale   商品状态（0--下架 1--上架）   is_recommend     status      is_jf_product
                ->where('pro.bis_id = '.$bis_id.' and pro.on_sale = 1 and pro.is_recommend = 1 and pro.status = 1 and i.status = 1 and pro.is_jf_product = 0')
                ->order('pro.update_time desc')
                ->limit(6)
                ->select();
        foreach ($res as $k=>$v)
        {
            $res[$k]['thumb']='http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['thumb']);
        }
        $ind = 0;
        foreach($res as $val){
            $jifen = $val['jifen'];
            $res[$ind]['jifen'] = floor($jifen);
            $ind ++;
        }

        return $res;
    }

    //获取推荐商品列表(多用户版)
    public function getRecProInfoMut(){
        $res = Db::table('store_products')->alias('pro')->field('pro.id as pro_id,pro.p_name,i.thumb,pro.original_price,pro.associator_price,pro.jifen * 5 as jifen')
            ->join('store_pro_images i','pro.id = i.p_id','LEFT')
            ->where('bis_id !=34 and pro.on_sale = 1 and pro.is_recommend = 1 and pro.status = 1 and i.status = 1 and pro.is_jf_product = 0')
            ->order('pro.update_time desc')
            ->limit(6)
            ->select();

        $ind = 0;
        foreach($res as $val){
            $jifen = $val['jifen'];
            $res[$ind]['jifen'] = floor($jifen);
            $ind ++;
        }

        return $res;
    }

    //获取推荐商品列表(多用户普通商城版)
    public function getRecProInfo($limit,$offset){
        $res = Db::table('store_products')->alias('pro')->field('pro.id as pro_id,pro.p_name,i.thumb,pro.original_price,pro.associator_price,pro.jifen * 5 as jifen')
            ->join('store_pro_images i','pro.id = i.p_id','LEFT')
            ->join('store_bis bis','pro.bis_id = bis.id','LEFT')
            ->where('bis.is_pintuan = 0 and pro.on_sale = 1 and pro.is_recommend = 1 and pro.status = 1 and i.status = 1 and pro.is_jf_product = 0')
            ->order('pro.update_time desc')
            ->limit($offset,$limit)
            ->select();

        $ind = 0;
        foreach($res as $val){
            $jifen = $val['jifen'];
            $res[$ind]['jifen'] = floor($jifen);
            $ind ++;
        }

        return $res;
    }

    //获取推荐商品列表(拼团)
    public function getRecProByGroup($bis_id,$limit,$offset){
        $res = Db::table('store_products')->alias('pro')->field('pro.id as pro_id,pro.p_name,i.thumb,pro.pintuan_price,pro.associator_price,pro.pintuan_count')
            ->join('store_pro_images i','pro.id = i.p_id','LEFT')
            ->where('pro.bis_id = '.$bis_id.' and pro.on_sale = 1 and pro.is_recommend = 1 and pro.status = 1 and i.status = 1 and pro.is_jf_product = 0')
            ->order('pro.update_time desc')
            ->limit($offset,$limit)
            ->select();
        return $res;
    }

    //获取推荐商品列表(多用户拼团)
    public function getRecProByGroupMulti($limit,$offset){
        $res = Db::table('store_products')->alias('pro')->field('pro.id as pro_id,pro.p_name,i.thumb,pro.pintuan_price,pro.associator_price,pro.pintuan_count')
            ->join('store_pro_images i','pro.id = i.p_id','LEFT')
            ->join('store_bis bis','pro.bis_id = bis.id','LEFT')
            ->where('bis_id !=34 and bis.is_pintuan = 1 and pro.on_sale = 1 and pro.is_recommend = 1 and pro.status = 1 and i.status = 1 and pro.is_jf_product = 0')
            ->order('pro.update_time desc')
            ->limit($offset,$limit)
            ->select();
        return $res;
    }

    //获取推荐商品数量(拼团)
    public function getRecProCountByGroup($bis_id,$limit,$offset){
        $res = Db::table('store_products')->alias('pro')
            ->join('store_pro_images i','pro.id = i.p_id','LEFT')
            ->where('pro.bis_id = '.$bis_id.' and pro.on_sale = 1 and pro.is_recommend = 1 and pro.status = 1 and i.status = 1')
            ->limit($offset,$limit)
            ->select();
        $count = count($res);
        return $count;
    }

    //获取新品列表(单用户版)
    //接口地址: https://yp.dxshuju.com/app/public/index/index/getNewProInfo?bis_id = 1
    //关联表       store_products   商品表   store_pro_images    商品图片表
    public function getNewProInfo($bis_id){
        //  p_name  商品名称      thumb    商品缩略图url  original_price  零售价   associator_price  会员价  jifen   积分
        $res = Db::table('store_products')->alias('pro')
                ->field('pro.id as pro_id,pro.p_name,i.thumb,pro.original_price,pro.associator_price,pro.jifen * 5 as jifen')
                ->join('store_pro_images i','pro.id = i.p_id','LEFT')
            //  bis_id  商家id       on_sale   商品状态（0--下架 1--上架）   is_recommend     status      is_jf_product
                ->where('pro.bis_id = '.$bis_id.' and pro.on_sale = 1 and pro.status = 1 and i.status = 1 and pro.is_jf_product = 0')
                ->order('pro.create_time desc')
                ->limit(8)
                ->select();

        foreach($res as $k=>$val){
            $jifen = $val['jifen'];
            $res[$k]['thumb']='http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$val['thumb']);
            $res[$k]['jifen'] = floor($jifen);
        }
        $count = Db::table('store_products')->alias('pro')
            ->join('store_pro_images i','pro.id = i.p_id','LEFT')
            ->where('pro.bis_id = '.$bis_id.' and pro.on_sale = 1 and pro.status = 1 and i.status = 1 and pro.is_jf_product = 0')
            ->count();

        if(!$res){
            echo json_encode(array(
                'statuscode'  => 0,
                'message'      => '暂无数据'
            ));
            exit;
        }
        $new_count = ceil($count / 2);
        $new_count = $new_count > 4 ? 4 : $new_count;
        $new_res = array();

        for($i = 0; $i < $new_count; $i ++){
            for($j=0;$j<2;$j++){
                $new_num = $i*2+$j;
                if($new_num < $count){
                    $new_res[$i][] = $res[$new_num];
                }else{
                    break;
                }

            }
        }

        return $new_res;
    }

    //获取新品列表(多用户版)
    public function getNewProInfoMut(){
        $res = Db::table('store_products')->alias('pro')->field('pro.id as pro_id,pro.p_name,i.thumb,pro.original_price,pro.associator_price,pro.jifen * 5 as jifen')
            ->join('store_pro_images i','pro.id = i.p_id','LEFT')
            ->where('bis_id !=34 and pro.on_sale = 1 and pro.status = 1 and i.status = 1 and pro.is_jf_product = 0')
            ->order('pro.create_time desc')
            ->limit(8)
            ->select();

        $ind = 0;
        foreach($res as $val){
            $jifen = $val['jifen'];
            $res[$ind]['jifen'] = floor($jifen);
            $ind ++;
        }

        $count = Db::table('store_products')->alias('pro')
            ->join('store_pro_images i','pro.id = i.p_id','LEFT')
            ->where('pro.on_sale = 1 and pro.status = 1 and i.status = 1 and pro.is_jf_product = 0')
            ->count();

        if(!$res){
            echo json_encode(array(
                'statuscode'  => 0,
                'message'      => '暂无数据'
            ));
            exit;
        }
        $new_count = ceil($count / 2);
        $new_count = $new_count > 4 ? 4 : $new_count;
        $new_res = array();

        for($i = 0; $i < $new_count; $i ++){
            for($j=0;$j<2;$j++){
                $new_num = $i*2+$j;
                if($new_num < $count){
                    $new_res[$i][] = $res[$new_num];
                }else{
                    break;
                }

            }
        }

        return $new_res;
    }

    //获取商品详情(二维规格)
    //接口地址: https://yp.dxshuju.com/app/public/index/index/getProDetail
    //条件        $pro_id(获取新品列表传过去的)
    //传输方式  $_POST
    //关联表       store_products   商品表   store_pro_images    商品图片表
    public function getProDetail($pro_id){
        $res = Db::table('store_products')
                ->alias('pro')
            //  p_name  商品名称    brand   品牌   thumb    商品缩略图url      image  商品大图url
            //  original_price  零售价  associator_price    会员价 config_image1       配置图1   config_image2   配置图2   config_image3    配置图3 config_image4  配置图4
            //       wx_introduce    微信介绍    wx_config_image1    配置图1

            ->field('pro.id as pro_id,pro.p_name,pro.brand,i.thumb,i.image,i.config_image1,
                i.config_image2,i.config_image3,i.config_image4,pro.original_price,pro.associator_price,
                pro.wx_introduce,i.wx_config_image1,i.wx_config_image2,i.wx_config_image3,i.wx_config_image4,
                i.wx_config_image5,i.wx_config_image6,i.wx_config_image7,
                i.wx_config_image8,i.wx_config_image9,i.wx_config_image10')
                ->join('store_pro_images i','pro.id = i.p_id','LEFT')
                ->where('pro.id = '.$pro_id)
                ->select();
        foreach ($res as $k=>$v)
        {
            $res[$k]['pro_id'] = $v['pro_id'];
            $res[$k]['p_name'] = $v['p_name'];
            $res[$k]['brand'] = $v['brand'];
            $res[$k]['thumb'] ='http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['thumb']);
            $res[$k]['image'] = 'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['image']);
            $res[$k]['config_image1'] = $v['config_image1'];
            $res[$k]['config_image2'] = $v['config_image2'];
            $res[$k]['config_image3'] = $v['config_image3'];
            $res[$k]['config_image4'] = $v['config_image4'];
            $res[$k]['original_price'] = $v['original_price'];
            $res[$k]['associator_price'] = $v['associator_price'];
            $res[$k]['wx_introduce'] = $v['wx_introduce'];
            $res[$k]['wx_config_image1'] = $v['wx_config_image1'];
            $res[$k]['wx_config_image2'] = $v['wx_config_image2'];
            $res[$k]['wx_config_image3'] = $v['wx_config_image3'];
            $res[$k]['wx_config_image4'] = $v['wx_config_image4'];
            $res[$k]['wx_config_image5'] = $v['wx_config_image5'];
            $res[$k]['wx_config_image6'] = $v['wx_config_image6'];
            $res[$k]['wx_config_image7'] = $v['wx_config_image7'];
            $res[$k]['wx_config_image8'] = $v['wx_config_image8'];
            $res[$k]['wx_config_image9'] = $v['wx_config_image9'];
            $res[$k]['wx_config_image10'] = $v['wx_config_image10'];
        }
        //设置详情页轮播图
        $images_info = array();
        array_push($images_info,$res[$k]['image']);
        if($res[$k]['config_image1'] && $res[$k]['config_image1'] != ''){
            array_push($images_info,'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$res[$k]['config_image1']));
        }
        if($res[$k]['config_image2'] && $res[$k]['config_image2'] != ''){
            array_push($images_info,'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$res[$k]['config_image2']));
        }
        if($res[$k]['config_image3'] && $res[$k]['config_image3'] != ''){
            array_push($images_info,'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$res[$k]['config_image3']));
        }
        if($res[$k]['config_image4'] && $res[$k]['config_image4'] != ''){
            array_push($images_info,'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$res[$k]['config_image4']));
        }



        //设置商品详情部分图片
        $des_images_info = array();
        if($res[$k]['wx_config_image1'] && $res[$k]['wx_config_image1'] != ''){
            array_push($des_images_info,'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$res[$k]['wx_config_image1']));
        }
        if($res[$k]['wx_config_image2'] && $res[$k]['wx_config_image2'] != ''){
            array_push($des_images_info,'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$res[$k]['wx_config_image2']));
        }
        if($res[$k]['wx_config_image3'] && $res[$k]['wx_config_image3'] != ''){
            array_push($des_images_info,'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$res[$k]['wx_config_image3']));
        }
        if($res[$k]['wx_config_image4'] && $res[$k]['wx_config_image4'] != ''){
            array_push($des_images_info,'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$res[$k]['wx_config_image4']));
        }
        if($res[$k]['wx_config_image5'] && $res[$k]['wx_config_image5'] != ''){
            array_push($des_images_info,'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$res[$k]['wx_config_image5']));
        }
        if($res[$k]['wx_config_image6'] && $res[$k]['wx_config_image6'] != ''){
            array_push($des_images_info,'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$res[$k]['wx_config_image6']));
        }
        if($res[$k]['wx_config_image7'] && $res[$k]['wx_config_image7'] != ''){
            array_push($des_images_info,'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$res[$k]['wx_config_image7']));
        }
        if($res[$k]['wx_config_image8'] && $res[$k]['wx_config_image8'] != ''){
            array_push($des_images_info,'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$res[$k]['wx_config_image8']));
        }
        if($res[$k]['wx_config_image9'] && $res[$k]['wx_config_image9'] != ''){
            array_push($des_images_info,'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$res[$k]['wx_config_image9']));
        }
        if($res[$k]['wx_config_image10'] && $res[$k]['wx_config_image10'] != ''){
            array_push($des_images_info,'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$res[$k]['wx_config_image10']));
        }

        //设置规格信息
        $org_guige_info = DB::table('store_pro_config')
                    //content1_name     配置名称1    con_content1   配置内容1
                    ->field('content1_name,con_content1,content2_name,con_content2')
                    ->where('pro_id = '.$res[$k]['pro_id'].' and status = 1')
                    ->select();

        $guige_info = array();
        if(!$org_guige_info){
            $guige_info = array();
        }else{
            $con_content1_array = array();
            $con_content2_array = array();
            foreach($org_guige_info as $val){
                if(!in_array($val['con_content1'],$con_content1_array)){
                    array_push($con_content1_array,$val['con_content1']);
                }
                if(!in_array($val['con_content2'],$con_content2_array)){
                    array_push($con_content2_array,$val['con_content2']);
                }
            }

            $con_content1 = '';
            $con_content2 = '';
            for($i=0;$i < count($con_content1_array);$i++){
                $con_content1 .=  $con_content1_array[$i] . '/';
            }
            for($i=0;$i<count($con_content2_array);$i++){
                $con_content2 .=  $con_content2_array[$i] . '/';
            }
            $con_content1 = substr($con_content1,0,-1);
            $con_content2 = substr($con_content2,0,-1);

            $aa = [
                'content1_name'  => $org_guige_info[0]['content1_name'],
                'con_content1'   => $con_content1
            ];
            $bb = [
                'content1_name'  => $org_guige_info[0]['content2_name'],
                'con_content1'   => $con_content2
            ];

            array_push($guige_info,$aa);
            array_push($guige_info,$bb);
        }


//        $result = array();
        $result = [
            'pro_id' => $res[$k]['pro_id'],
            'p_name' => $res[$k]['p_name'],
            'brand' => $res[$k]['brand'],
            'original_price' => $res[$k]['original_price'],
            'associator_price' => $res[$k]['associator_price'],
            'wx_introduce' => $res[$k]['wx_introduce'],
            'images'  => $images_info,
            'config_info' => $guige_info,
            'des_images' => $des_images_info
        ];

        return $result;
    }

    //获取商品详情(一维规格)
    //接口地址: https://yp.dxshuju.com/app/public/index/index/getProDetailOneDimensional
    //条件        $pro_id(获取新品列表传过去的)
    //传输方式  $_POST
    //关联表       store_products   商品表   store_pro_images    商品图片表


    public function getProDetailOneDimensional($pro_id){

        $res[] = Db::table('store_products')
            ->alias('pro')
            //  p_name  商品名称    brand   品牌   thumb    商品缩略图url      image  商品大图url      pintuan_price 优惠价格
            //  original_price  零售价  associator_price    会员价 config_image1       配置图1   config_image2   配置图2   config_image3    配置图3 config_image4  配置图4
            //       wx_introduce    微信介绍    wx_config_image1    配置图1

            ->field('pro.id as pro_id,pro.p_name,pro.brand,pro.pintuan_count,pro.jifen * 5 as jifen,
                    i.thumb,i.image,i.config_image1,i.config_image2,i.config_image3,i.config_image4,
                    pro.original_price,pro.associator_price,pro.pintuan_price,pro.wx_introduce,
                    i.wx_config_image1,i.wx_config_image2,i.wx_config_image3,i.wx_config_image4,i.wx_config_image5,
                    i.wx_config_image6,i.wx_config_image7,i.wx_config_image8,i.wx_config_image9,i.wx_config_image10')   //pintuan_price 优惠价格
            ->join('store_pro_images i','pro.id = i.p_id','LEFT')
            ->where('pro.id = '.$pro_id)
            ->find();
        foreach ($res as $k=>$v)
        {
            $res[$k]['pro_id'] = $v['pro_id'];
            $res[$k]['p_name'] = $v['p_name'];
            $res[$k]['brand'] = $v['brand'];
            $res[$k]['pintuan_count'] = $v['pintuan_count'];
            $res[$k]['thumb'] ='http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['thumb']);
            $res[$k]['image'] = 'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['image']);
            $res[$k]['config_image1'] = 'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['config_image1']);
            $res[$k]['config_image2'] = 'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['config_image2']);
            $res[$k]['config_image3'] = 'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['config_image3']);
            $res[$k]['config_image4'] = 'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['config_image4']);
            $res[$k]['original_price'] = $v['original_price'];
            $res[$k]['associator_price'] = $v['associator_price'];
            $res[$k]['wx_introduce'] = $v['wx_introduce'];
            $res[$k]['wx_config_image1'] = 'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['wx_config_image1']);
            $res[$k]['wx_config_image2'] = 'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['wx_config_image2']);
            $res[$k]['wx_config_image3'] = 'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['wx_config_image3']);
            $res[$k]['wx_config_image4'] = 'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['wx_config_image4']);
            $res[$k]['wx_config_image5'] = 'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['wx_config_image5']);
            $res[$k]['wx_config_image6'] = 'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['wx_config_image6']);
            $res[$k]['wx_config_image7'] = 'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['wx_config_image7']);
            $res[$k]['wx_config_image8'] = 'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['wx_config_image8']);
            $res[$k]['wx_config_image9'] = 'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['wx_config_image9']);
            $res[$k]['wx_config_image10'] = 'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['wx_config_image10']);
        }
        $res[$k]['jifen'] = floor($res[$k]['jifen']);



        //设置详情页轮播图
        $images_info = array();
        array_push($images_info,$res[$k]['image']);
        if($res[$k]['config_image1'] && $res[$k]['config_image1'] != ''){
            array_push($images_info,$res[$k]['config_image1']);
        }
        if($res[$k]['config_image2'] && $res[$k]['config_image2'] != ''){
            array_push($images_info,$res[$k]['config_image2']);
        }
        if($res[$k]['config_image3'] && $res[$k]['config_image3'] != ''){
            array_push($images_info,$res[$k]['config_image3']);
        }
        if($res[$k]['config_image4'] && $res[$k]['config_image4'] != ''){
            array_push($images_info,$res[$k]['config_image4']);
        }

        //设置商品详情部分图片
        $des_images_info = array();
        if($res[$k]['wx_config_image1'] && $res[$k]['wx_config_image1'] != ''){
            array_push($des_images_info,$res[$k]['wx_config_image1']);
        }
        if($res[$k]['wx_config_image2'] && $res[$k]['wx_config_image2'] != ''){
            array_push($des_images_info,$res[$k]['wx_config_image2']);
        }
        if($res[$k]['wx_config_image3'] && $res[$k]['wx_config_image3'] != ''){
            array_push($des_images_info,$res[$k]['wx_config_image3']);
        }
        if($res[$k]['wx_config_image4'] && $res[$k]['wx_config_image4'] != ''){
            array_push($des_images_info,$res[$k]['wx_config_image4']);
        }
        if($res[$k]['wx_config_image5'] && $res[$k]['wx_config_image5'] != ''){
            array_push($des_images_info,$res[$k]['wx_config_image5']);
        }
        if($res[$k]['wx_config_image6'] && $res[$k]['wx_config_image6'] != ''){
            array_push($des_images_info,$res[$k]['wx_config_image6']);
        }
        if($res[$k]['wx_config_image7'] && $res[$k]['wx_config_image7'] != ''){
            array_push($des_images_info,$res[$k]['wx_config_image7']);
        }
        if($res[$k]['wx_config_image8'] && $res[$k]['wx_config_image8'] != ''){
            array_push($des_images_info,$res[$k]['wx_config_image8']);
        }
        if($res[$k]['wx_config_image9'] && $res[$k]['wx_config_image9'] != ''){
            array_push($des_images_info,$res[$k]['wx_config_image9']);
        }
        if($res[$k]['wx_config_image10'] && $res[$k]['wx_config_image10'] != ''){
            array_push($des_images_info,$res[$k]['wx_config_image10']);
        }

        //设置规格信息
        $org_guige_info = DB::table('store_pro_config')
            ->field('content1_name,con_content1')
            ->where('pro_id = '.$res[$k]['pro_id'].' and status = 1')
            ->select();

        $guige_info = array();
        if(!$org_guige_info){
            $guige_info = array();
        }else{
            $con_content1_array = array();
            foreach($org_guige_info as $val){
                if(!in_array($val['con_content1'],$con_content1_array)){
                    array_push($con_content1_array,$val['con_content1']);
                }
            }

            $con_content1 = '';
            for($i=0;$i < count($con_content1_array);$i++){
                $con_content1 .=  $con_content1_array[$i] . '/';
            }
            $con_content1 = substr($con_content1,0,-1);

            $aa = [
                'content1_name'  => $org_guige_info[0]['content1_name'],
                'con_content1'   => $con_content1
            ];
            array_push($guige_info,$aa);
        }


        $result = [
            'pro_id' => $res[$k]['pro_id'],
            'p_name' => $res[$k]['p_name'],
            'brand' => $res[$k]['brand'],
            'jifen' => $res[$k]['jifen'],
            'pintuan_count' => $res[$k]['pintuan_count'],
            'original_price' => $res[$k]['original_price'],
            'associator_price' => $res[$k]['associator_price'],
            'group_price' => $res[$k]['pintuan_price'],
            'wx_introduce' => $res[$k]['wx_introduce'],
            'images'  => $images_info,
            'config_info' => $guige_info,
            'des_images' => $des_images_info
        ];

        return $result;
    }

    //根据一级分类id获取商品信息(单用户版)接口
    //接口地址: https://yp.dxshuju.com/app/public/index/category/getProInfoByFirstId
    //传输方式  $_POST
    //条件    key=>cat_id (商品分类id)   values=>传过来的
    //关联表       store_products   商品表    store_pro_images    商品图片表
    public function getProInfoByFirstId($param){
        //获取参数
        //cat_id    商品分类id
        $cat1_id = $param['cat_id'];
        //多少页
        $page = !empty($param['page']) ? $param['page'] : 1;
        $limit = 10;
        //总共多少数
        $offset = ($page - 1) * $limit;
        $order = "pro.update_time desc";
        $res = Db::table('store_products')
            ->alias('pro')
            // pro_id   商品id    p_name  商品名称    brand   品牌   thumb    商品缩略图url     original_price  零售价  associator_price    会员价 config_image1
            ->field('pro.id as pro_id,pro.p_name,i.thumb,pro.original_price,pro.associator_price')
            ->join('store_pro_images i','pro.id = i.p_id','LEFT')
            //  $cat1_id  商品分类id       on_sale   商品状态（0--下架 1--上架）    status      is_jf_product
            ->where('pro.defined_cat1_id = '.$cat1_id.' and pro.on_sale = 1 and pro.status = 1 and i.status = 1 and pro.is_jf_product = 0')
            ->order($order)
            ->limit($offset,$limit)
            ->select();
        foreach ($res as $k=>$v)
        {
            $res[$k]['thumb']="http://39.105.68.130:81/app/public/image/".str_replace('\\','/',$v['thumb']);
        }
        if(!$res){
            echo json_encode(array(
                'statuscode'  => 0,
                'message'      => '暂无数据'
            ));
            exit;
        }

        return $res;
    }

    //根据一级分类id获取商品信息(多用户版)
    public function getProInfoByFirstIdMulti($param){
        //获取参数
        $cat1_id = $param['cat_id'];
        $page = !empty($param['page']) ? $param['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $order = "pro.update_time desc";
        $res = Db::table('store_products')->alias('pro')->field('pro.id as pro_id,pro.p_name,i.thumb,pro.original_price,pro.associator_price,pro.jifen * 5 as jifen')
            ->join('store_pro_images i','pro.id = i.p_id','LEFT')
            ->where('bis_id !=34 and pro.cat1_id = '.$cat1_id.' and pro.on_sale = 1 and pro.status = 1 and i.status = 1 and pro.is_jf_product = 0')
            ->order($order)
            ->limit($offset,$limit)
            ->select();
        if(!$res){
            echo json_encode(array(
                'statuscode'  => 0,
                'message'      => '暂无数据'
            ));
            exit;
        }

        $ind = 0;
        foreach($res as $val){
            $jifen = $val['jifen'];
            $res[$ind]['jifen'] = floor($jifen);
            $ind ++;
        }

        return $res;
    }

    //根据一级分类id获取商品信息(拼团版)
    public function getGroupProInfoByFirstId($param){
        //获取参数
        $cat1_id = $param['cat_id'];
        $page = !empty($param['page']) ? $param['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $order = "pro.update_time desc";
        $res = Db::table('store_products')->alias('pro')->field('pro.id as pro_id,pro.p_name,i.thumb,pro.pintuan_price,pro.pintuan_count,pro.associator_price')
            ->join('store_pro_images i','pro.id = i.p_id','LEFT')
            ->where('pro.defined_cat1_id = '.$cat1_id.' and pro.on_sale = 1 and pro.status = 1 and i.status = 1 and pro.is_jf_product = 0')
            ->order($order)
            ->limit($offset,$limit)
            ->select();
        if(!$res){
            echo json_encode(array(
                'statuscode'  => 0,
                'message'      => '暂无数据'
            ));
            exit;
        }

        return $res;
    }

    //根据一级分类id获取商品数量(单用户版)
    public function getProInfoByFirstIdCount($param){
        //获取参数
        $cat1_id = $param['cat_id'];
        $page = !empty($param['page']) ? $param['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $order = "pro.update_time desc";

        $res = Db::table('store_products')->alias('pro')
            ->join('store_pro_images i','pro.id = i.p_id','LEFT')
            ->where('pro.defined_cat1_id = '.$cat1_id.' and pro.on_sale = 1 and pro.status = 1 and i.status = 1 and pro.is_jf_product = 0')
            ->order($order)
            ->limit($offset,$limit)
            ->select();
        $count = count($res);

        return $count;
    }

    //根据一级分类id获取商品数量(多用户版)
    public function getProInfoByFirstIdCountMulti($param){
        //获取参数
        $cat1_id = $param['cat_id'];
        $page = !empty($param['page']) ? $param['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $order = "pro.update_time desc";

        $res = Db::table('store_products')->alias('pro')
            ->join('store_pro_images i','pro.id = i.p_id','LEFT')
            ->where('pro.cat1_id = '.$cat1_id.' and pro.on_sale = 1 and pro.status = 1 and i.status = 1 and pro.is_jf_product = 0')
            ->order($order)
            ->limit($offset,$limit)
            ->select();
        $count = count($res);

        return $count;
    }

    //根据一级分类id获取商品数量(拼团版)
    public function getGroupProInfoByFirstIdCount($param){
        //获取参数
        $cat1_id = $param['cat_id'];
        $page = !empty($param['page']) ? $param['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $order = "pro.update_time desc";
        $res = Db::table('store_products')->alias('pro')
            ->join('store_pro_images i','pro.id = i.p_id','LEFT')
            ->where('pro.defined_cat1_id = '.$cat1_id.' and pro.on_sale = 1 and pro.status = 1 and i.status = 1 and pro.is_jf_product = 0')
            ->order($order)
            ->limit($offset,$limit)
            ->select();
        $count = count($res);

        return $count;
    }

    //根据二级分类id获取商品信息(单用户版)接口
    //接口地址: https://yp.dxshuju.com/app/public/index/category/getProInfoBySecondId
    //传输方式  $_POST
    //条件    key=>cat_id (商品分类id)   values=>传过来的 （cat_id =  24       cat2_id = 27）
    //关联表       store_products   商品表    store_pro_images    商品图片表
    public function getProInfoBySecondId($param){
        //获取参数
        $cat_id = !empty($param['cat_id']) ? $param['cat_id'] : '';
        $cat2_id = !empty($param['cat2_id']) ? $param['cat2_id'] : '';
        $page = !empty($param['page']) ? $param['page'] : 1;
        if($cat_id){
            $con = "pro.defined_cat1_id = ".$cat_id;
        }
        if($cat2_id){
            $con = "pro.defined_cat2_id = ".$cat2_id;
        }
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $order = "pro.update_time desc";
        $res = Db::table('store_products')->alias('pro')->field('pro.id as pro_id,pro.p_name,i.thumb,pro.original_price,pro.associator_price')
            ->join('store_pro_images i','pro.id = i.p_id','LEFT')
            ->where($con.' and pro.on_sale = 1 and pro.status = 1 and i.status = 1 and pro.is_jf_product = 0')
            ->order($order)
            ->limit($offset,$limit)
            ->select();
        foreach ($res as $k=>$v)
        {
            $res[$k]['thumb'] = "http://39.105.68.130:81/app/public/image/".str_replace('\\','/',$v['thumb']);
        }
        if(!$res){
            echo json_encode(array(
                'statuscode'  => 0,
                'message'      => '暂无数据'
            ));
            exit;
        }
        return $res;
    }

    //根据二级分类id获取商品信息(多用户版)
    public function getProInfoBySecondIdMulti($param){
        //获取参数
        $cat_id = !empty($param['cat_id']) ? $param['cat_id'] : '';
        $cat2_id = !empty($param['cat2_id']) ? $param['cat2_id'] : '';
        $page = !empty($param['page']) ? $param['page'] : 1;
        if($cat_id){
            $con = "pro.defined_cat1_id = ".$cat_id;
        }
        if($cat2_id){
            $con = "pro.defined_cat2_id = ".$cat2_id;
        }

        $limit = 10;
        $offset = ($page - 1) * $limit;
        $order = "pro.update_time desc";
        $res = Db::table('store_products')->alias('pro')->field('pro.id as pro_id,pro.p_name,i.thumb,pro.original_price,pro.associator_price,pro.jifen * 5 as jifen')
            ->join('store_pro_images i','pro.id = i.p_id','LEFT')
            ->where($con.' and pro.on_sale = 1 and pro.status = 1 and i.status = 1 and pro.is_jf_product = 0')
            ->order($order)
            ->limit($offset,$limit)
            ->select();

        if(!$res){
            echo json_encode(array(
                'statuscode'  => 0,
                'message'      => '暂无数据'
            ));
            exit;
        }

        $ind = 0;
        foreach($res as $val){
            $jifen = $val['jifen'];
            $res[$ind]['jifen'] = floor($jifen);
            $ind ++;
        }

        return $res;
    }

    //根据二级分类id获取商品信息(拼团版)
    public function getGroupProInfoBySecondId($param){
        //获取参数
        $cat_id = !empty($param['cat_id']) ? $param['cat_id'] : '';
        $cat2_id = !empty($param['cat2_id']) ? $param['cat2_id'] : '';
        $page = !empty($param['page']) ? $param['page'] : 1;
        if($cat_id){
            $con = "pro.defined_cat1_id = ".$cat_id;
        }
        if($cat2_id){
            $con = "pro.defined_cat2_id = ".$cat2_id;
        }

        $limit = 10;
        $offset = ($page - 1) * $limit;
        $order = "pro.update_time desc";
        $res = Db::table('store_products')->alias('pro')->field('pro.id as pro_id,pro.p_name,i.thumb,pro.pintuan_price,pro.pintuan_count,pro.associator_price')
            ->join('store_pro_images i','pro.id = i.p_id','LEFT')
            ->where($con.' and pro.on_sale = 1 and pro.status = 1 and i.status = 1 and pro.is_jf_product = 0')
            ->order($order)
            ->limit($offset,$limit)
            ->select();

        if(!$res){
            echo json_encode(array(
                'statuscode'  => 0,
                'message'      => '暂无数据'
            ));
            exit;
        }
        return $res;
    }

    //根据二级分类id获取商品数量(单用户版)
    public function getProInfoBySecondIdCount($param){
        //获取参数
        $cat_id = !empty($param['cat_id']) ? $param['cat_id'] : '';
        $cat2_id = !empty($param['cat2_id']) ? $param['cat2_id'] : '';
        $page = !empty($param['page']) ? $param['page'] : 1;
        if($cat_id){
            $con = "pro.defined_cat1_id = ".$cat_id;
        }
        if($cat2_id){
            $con = "pro.defined_cat2_id = ".$cat2_id;
        }

        $limit = 10;
        $offset = ($page - 1) * $limit;
        $order = "pro.update_time desc";
        $res = Db::table('store_products')->alias('pro')->field('pro.id as pro_id,pro.p_name,i.thumb,pro.original_price,pro.associator_price')
            ->join('store_pro_images i','pro.id = i.p_id','LEFT')
            ->where($con.' and pro.on_sale = 1 and pro.status = 1 and i.status = 1 and pro.is_jf_product = 0')
            ->order($order)
            ->limit($offset,$limit)
            ->select();
        $count = count($res);

        return $count;
    }

    //根据二级分类id获取商品数量(多用户版)
    public function getProInfoBySecondIdCountMulti($param){
        //获取参数
        $cat_id = !empty($param['cat_id']) ? $param['cat_id'] : '';
        $cat2_id = !empty($param['cat2_id']) ? $param['cat2_id'] : '';
        $page = !empty($param['page']) ? $param['page'] : 1;
        if($cat_id){
            $con = "pro.defined_cat1_id = ".$cat_id;
        }
        if($cat2_id){
            $con = "pro.defined_cat2_id = ".$cat2_id;
        }

        $limit = 10;
        $offset = ($page - 1) * $limit;
        $order = "pro.update_time desc";
        $res = Db::table('store_products')->alias('pro')->field('pro.id as pro_id,pro.p_name,i.thumb,pro.original_price,pro.associator_price')
            ->join('store_pro_images i','pro.id = i.p_id','LEFT')
            ->where($con.' and pro.on_sale = 1 and pro.status = 1 and i.status = 1 and pro.is_jf_product = 0')
            ->order($order)
            ->limit($offset,$limit)
            ->select();
        $count = count($res);

        return $count;
    }

    //根据二级分类id获取商品数量(拼团版)
    public function getGroupProInfoBySecondIdCount($param){
        //获取参数
        $cat_id = !empty($param['cat_id']) ? $param['cat_id'] : '';
        $cat2_id = !empty($param['cat2_id']) ? $param['cat2_id'] : '';
        $page = !empty($param['page']) ? $param['page'] : 1;
        if($cat_id){
            $con = "pro.defined_cat1_id = ".$cat_id;
        }
        if($cat2_id){
            $con = "pro.defined_cat2_id = ".$cat2_id;
        }

        $limit = 10;
        $offset = ($page - 1) * $limit;
        $order = "pro.update_time desc";
        $res = Db::table('store_products')->alias('pro')
            ->join('store_pro_images i','pro.id = i.p_id','LEFT')
            ->where($con.' and pro.on_sale = 1 and pro.status = 1 and i.status = 1 and pro.is_jf_product = 0')
            ->order($order)
            ->limit($offset,$limit)
            ->select();
        $count = count($res);

        return $count;
    }

    //获取商品配置信息(二维规格)

    //接口地址: https://yp.dxshuju.com/app/public/index/product/getProConfigInfo
    //传输方式  $_POST
    //条件    key=>pro_id(商品id)     value=>上一级传过来的id
    //关联表       store_products   商品表    store_pro_config    商品配置表  store_pro_images    商品图片表
        public function getProConfigInfo($pro_id){
        $pro_res = Db::table('store_products')
                ->alias('pro')
            //  pro_id   商品id    p_name  商品名称    brand   品牌   thumb    商品缩略图url     original_price  零售价  associator_price    会员价
                ->field('pro.id as pro_id,pro.p_name,pro.brand,i.thumb,pro.original_price,pro.associator_price')
                ->join('store_pro_images i','pro.id = i.p_id','LEFT')
            //  on_sale     商品状态（0--下架 1--上架）   status      状态
                ->where('pro.id = '.$pro_id.' and pro.on_sale = 1 and pro.status = 1 and i.status = 1')
                ->find();


        //查询规格信息
        $org_guige_info = DB::table('store_pro_config')
            //  content1_name   配置名称1   con_content1    配置内容1   content2_name   配置名称2   con_content2    配置内容2
            ->field('id as con_id,content1_name,con_content1,content2_name,con_content2')
            ->where('pro_id = '.$pro_res['pro_id'].' and status = 1')
            ->select();
        //设置规格数组内容
        $config1_info_array = array();
        $temp_config1_info_array = array();
        $config2_info_array = array();
        $temp_config2_info_array = array();
        $config1_info_array['content1_name'] = $org_guige_info[0]['content1_name'];
        $config2_info_array['content2_name'] = $org_guige_info[0]['content2_name'];
        foreach($org_guige_info as $val){
            if(!in_array($val['con_content1'],$temp_config1_info_array)){
                $temp_config1_info_array[] = $val['con_content1'];
            }
            if(!in_array($val['con_content2'],$temp_config2_info_array)){
                $temp_config2_info_array[] = $val['con_content2'];
            }
        }
        $config1_info_count = count($temp_config1_info_array);
        $config2_info_count = count($temp_config2_info_array);

        $new_count1 = ceil($config1_info_count / 5);
        $new_count2 = ceil($config2_info_count / 5);

        $new_res1 = array();
        $new_res2 = array();
        for($i = 0; $i < $new_count1; $i ++){
            for($j=0;$j<5;$j++){
                $new_num = $i*5+$j;
                if($new_num < $config1_info_count){
                    $new_res1[$i][] = $temp_config1_info_array[$new_num];
                }else{
                    break;
                }

            }
        }

        for($i = 0; $i < $new_count2; $i ++){
            for($j=0;$j<5;$j++){
                $new_num = $i*5+$j;
                if($new_num < $config2_info_count){
                    $new_res2[$i][] = $temp_config2_info_array[$new_num];
                }else{
                    break;
                }

            }
        }

        $config1_info_array['con_content1'] = $new_res1;
        $config2_info_array['con_content2'] = $new_res2;

        $result = [
            'pro_id' => $pro_res['pro_id'],
            'p_name' => $pro_res['p_name'],
            'brand' => $pro_res['brand'],
            'thumb' => $pro_res['thumb'],
            'original_price' => $pro_res['original_price'],
            'associator_price' => $pro_res['associator_price'],
            'config1_info' => $config1_info_array
        ];
        return $result;
    }

    //获取商品配置信息(一维规格普通版)
    //接口地址: https://yp.dxshuju.com/app/public/index/product/getProConfigInfoOneDimensional

    //传输方式  $_POST
    //条件    key=>pro_id(商品id)     value=>上一级传过来的id
    //关联表       store_products   商品表    store_pro_config    商品配置表  store_pro_images    商品图片表
    public function getProConfigInfoOneDimensional($pro_id)
    {
        //  pro_id   商品id    p_name  商品名称    brand   品牌   thumb    商品缩略图url     original_price  零售价  associator_price    会员价
        //  on_sale     商品状态（0--下架 1--上架）   status      状态

        //查询商品信息


        $pro_res = Db::table('store_products')
            ->alias('pro')
            ->field('pro.id as pro_id,pro.p_name,pro.brand,i.thumb,pro.original_price,pro.associator_price')
            ->join('store_pro_images i', 'pro.id = i.p_id', 'LEFT')
            ->where('pro.id = ' . $pro_id . ' and pro.on_sale = 1 and pro.status = 1 and i.status = 1')
            ->find();

        //查询规格信息

        //  content1_name
        $org_guige_info = DB::table('store_pro_config')
            ->field('id as con_id,content1_name,con_content1')
            ->where('pro_id = '.$pro_res['pro_id'].' and status = 1')
            ->select();


        //设置规格数组内容
        $config1_info_array = array();
        $temp_config1_info_array = array();
        $config1_info_array['content_name'] = $org_guige_info[0]['content1_name'];
        $index = 0;
        foreach($org_guige_info as $val){

            $temp_config1_info_array[$index]['conid'] = $val['con_id'];
            $temp_config1_info_array[$index]['con_content'] = $val['con_content1'];
            $index ++;
        }


        $config1_info_count = count($temp_config1_info_array);
        $new_count1 = ceil($config1_info_count / 5);
        $new_res1 = array();
        for($i = 0; $i < $new_count1; $i ++){
            for($j=0;$j<5;$j++){
                $new_num = $i*5+$j;
                if($new_num < $config1_info_count){
                    $new_res1[$i][] = $temp_config1_info_array[$new_num];
                }else{
                    break;
                }

            }
        }

        $config1_info_array['con_content'] = $new_res1;

        $result = [
            'pro_id' => $pro_res['pro_id'],
            'p_name' => $pro_res['p_name'],
            'brand' => $pro_res['brand'],
            'thumb' => "http://39.105.68.130:81/app/public/image/".str_replace('\\','/',$pro_res['thumb']),
            'original_price' => $pro_res['original_price'],
            'associator_price' => $pro_res['associator_price'],
            'config1_info' => $config1_info_array
        ];
        return $result;
    }


    public function getProConfigInfoOneDimensionalByGroup($pro_id,$from){
        if($from == 'single'){
            $pro_res = Db::table('store_products')->alias('pro')->field('pro.id as pro_id,pro.p_name,pro.brand,i.thumb,pro.original_price,pro.associator_price')
                ->join('store_pro_images i','pro.id = i.p_id','LEFT')
                ->where('pro.id = '.$pro_id.' and pro.on_sale = 1 and pro.status = 1 and i.status = 1')
                ->find();
        }else{
            $pro_res = Db::table('store_products')->alias('pro')->field('pro.id as pro_id,pro.p_name,pro.brand,i.thumb,pro.original_price,pro.pintuan_price')
                ->join('store_pro_images i','pro.id = i.p_id','LEFT')
                ->where('pro.id = '.$pro_id.' and pro.on_sale = 1 and pro.status = 1 and i.status = 1')
                ->find();
        }


        //查询规格信息
        $org_guige_info = DB::table('store_pro_config')
            ->field('id as con_id,content1_name,con_content1')
            ->where('pro_id = '.$pro_res['pro_id'].' and status = 1')
            ->select();

        //设置规格数组内容
        $config1_info_array = array();
        $temp_config1_info_array = array();
        $config1_info_array['content_name'] = $org_guige_info[0]['content1_name'];
        $index = 0;
        foreach($org_guige_info as $val){
            $temp_config1_info_array[$index]['conid'] = $val['con_id'];
            $temp_config1_info_array[$index]['con_content'] = $val['con_content1'];
            $index ++;
        }

        $config1_info_count = count($temp_config1_info_array);
        $new_count1 = ceil($config1_info_count / 1);
        $new_res1 = array();
        for($i = 0; $i < $new_count1; $i ++){
            for($j=0;$j<99;$j++){
                $new_num = $i*99+$j;
                if($new_num < $config1_info_count){
                    $new_res1[$i][] = $temp_config1_info_array[$new_num];
                }else{
                    break;
                }

            }
        }

        $config1_info_array['con_content'] = $new_res1;

        if($from == 'single'){
            $result = [
                'pro_id' => $pro_res['pro_id'],
                'p_name' => $pro_res['p_name'],
                'brand' => $pro_res['brand'],
                'thumb' => 'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$pro_res['thumb']),
                'original_price' => $pro_res['original_price'],
                'associator_price' => $pro_res['associator_price'],
                'config1_info' => $config1_info_array
            ];
        }else{
            $result = [
                'pro_id' => $pro_res['pro_id'],
                'p_name' => $pro_res['p_name'],
                'brand' => $pro_res['brand'],
                'thumb' =>  'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$pro_res['thumb']),
                'original_price' => $pro_res['original_price'],
                'associator_price' => $pro_res['pintuan_price'],
                'config1_info' => $config1_info_array
            ];
        }

        return $result;
    }

    //获取积分商品配置信息(多用户一维规格版)
    public function getJfProConfigInfo($pro_id){
        $pro_res = Db::table('store_products')->alias('pro')->field('pro.id as pro_id,pro.p_name,pro.brand,i.thumb,pro.original_price,pro.associator_price,pro.ex_jifen')
            ->join('store_pro_images i','pro.id = i.p_id','LEFT')
            ->where('pro.id = '.$pro_id.' and pro.on_sale = 1 and pro.status = 1 and i.status = 1')
            ->find();

        //查询规格信息
        $org_guige_info = DB::table('store_pro_config')
            ->field('id as con_id,content1_name,con_content1')
            ->where('pro_id = '.$pro_res['pro_id'].' and status = 1')
            ->select();

        //设置规格数组内容
        $config1_info_array = array();
        $temp_config1_info_array = array();
        $config1_info_array['content_name'] = $org_guige_info[0]['content1_name'];
        $index = 0;
        foreach($org_guige_info as $val){
            $temp_config1_info_array[$index]['conid'] = $val['con_id'];
            $temp_config1_info_array[$index]['con_content'] = $val['con_content1'];
            $index ++;
        }

        $config1_info_count = count($temp_config1_info_array);
        $new_count1 = ceil($config1_info_count / 5);
        $new_res1 = array();
        for($i = 0; $i < $new_count1; $i ++){
            for($j=0;$j<5;$j++){
                $new_num = $i*5+$j;
                if($new_num < $config1_info_count){
                    $new_res1[$i][] = $temp_config1_info_array[$new_num];
                }else{
                    break;
                }

            }
        }

        $config1_info_array['con_content'] = $new_res1;

        if($pro_res['associator_price'] < '0.01'){
            $ex_jifen = $pro_res['ex_jifen'] .'积分';
        }else{
            $ex_jifen = $pro_res['ex_jifen'] .'积分'.' + '.$pro_res['associator_price'].'元';
        }

        $result = [
            'pro_id' => $pro_res['pro_id'],
            'p_name' => $pro_res['p_name'],
            'brand' => $pro_res['brand'],
            'thumb' => $pro_res['thumb'],
            'original_price' => $pro_res['original_price'],
            'associator_price' => $ex_jifen,
            'config1_info' => $config1_info_array
        ];
        return $result;
    }

    //根据一级名称&商品id获取二级配置信息
    //接口地址: https://yp.dxshuju.com/app/public/index/product/getConfig2InfoById
    //传输方式  $_POST
    //条件    key=>pro_id(商品id)     value=>上一级传过来的id
    //关联表         store_pro_config    商品配置表
    public function getConfig2InfoById($pro_id,$con_info){
        $where = "pro_id = ".$pro_id." and status = 1";
        $res = DB::table('store_pro_config')
            ->field('id as con_id,content2_name,con_content2')
            ->where($where)
            ->select();
        //and con_content1 = '$con_info'
        if(!$res){
            echo json_encode(array(
                'statuscode'  => 0,
                'message'      => '暂无数据'
            ));
            exit;
        }
        $temp_res = array();
        $temp_res['content_name'] = $res[0]['content2_name'];
        $config_info_count = count($res);
        $new_count = ceil($config_info_count / 5);
        $new_res = array();
        for($i = 0; $i < $new_count; $i ++){
            for($j=0;$j<5;$j++){
                $new_num = $i*5+$j;
                if($new_num < $config_info_count){
                    $new_res[$i][$j]['con_id'] = $res[$new_num]['con_id'];
                    $new_res[$i][$j]['con_content'] = $res[$new_num]['con_content2'];
                }else{
                    break;
                }
            }
        }
        $temp_res['con_content'] = $new_res;
        return $temp_res;
    }

    //获取选中商品单价
    //接口地址: https://yp.dxshuju.com/app/public/index/product/getSelectedProPrice
    //传输方式  $_POST
    //条件    key=>pro_id(商品id)     value=>上一级传过来的id
    //        key=>con_id(商品配置id)     value=>上一级传过来的id
    //关联表         store_pro_config    商品配置表
    public function getSelectedProPrice($pro_id,$con_id){
        $res = DB::table('store_pro_config')->field('id as con_id,price')
                ->where(['id'=>$con_id,'pro_id'=>$pro_id])
                ->find();
        return $res;
    }

    //根据一级名称获取商品信息(一维规格)

    //接口地址: https://yp.dxshuju.com/app/public/index/product/getConByIdOneDimensional
    //传输方式  $_POST
    //条件    key=>pro_id(商品id)     value=>上一级传过来的id

    //关联表         store_pro_config    商品配置表

    public function getConByIdOneDimensional($pro_id){
        $res = DB::table('store_pro_config')->field('price')
            ->where('id = '.$pro_id)
            ->find();
        return $res;
    }

    //根据一级名称获取商品信息(一维规格拼团版)
    public function getConByIdOneDimensionalByGroup($pro_id,$from){
        if($from == "single"){
            $res = DB::table('store_pro_config')->field('price')
                ->where('id = '.$pro_id)
                ->find();
        }else{
            $res = DB::table('store_pro_config')->field('group_price as price')
                ->where('id = '.$pro_id)
                ->find();
        }

        return $res;
    }

    //根据一级名称获取积分商品信息(多用户一维规格版)
    public function getJfConById($pro_id){
        $res = DB::table('store_pro_config')->field('ex_jifen,price')
            ->where('id = '.$pro_id)
            ->find();
        if($res['price'] < '0.01'){
            $res['jf_price'] = $res['ex_jifen'].'积分';
        }else{
            $res['jf_price'] = $res['ex_jifen'].'积分'.' + '.$res['price'].'元';
        }
        return $res;
    }

    //获取指定商品的拼团信息
    public function getGroupInfoByProId($pro_id){
        $res = Db::table('store_group_main_orders')
            ->alias('gmo')
            ->field('gmo.mem_id,gmo.group_num,gmo.pintuan_count,gmo.pay_time,mem.avatarUrl,mem.nickname')
            ->join('store_members mem','gmo.mem_id = mem.mem_id','LEFT')
            ->order('gmo.create_time desc')
            ->where('gmo.pro_id = '.$pro_id.' and gmo.group_status = 1 and gmo.group_identity = 1 and order_status = 2 and gmo.status = 1')
            ->select();

        $index = 0;
        $arr = array();
        foreach($res as $val){
            $arr[$index]['mem_id'] = $val['mem_id'];
            $arr[$index]['group_num'] = $val['group_num'];
            $arr[$index]['lack_count'] = $val['pintuan_count'] - $this->getGroupCountByGroupNum($val['group_num']);
            $arr[$index]['pay_time'] = $val['pay_time'];
            $arr[$index]['avatarUrl'] = $val['avatarUrl'];
            $arr[$index]['nickname'] = $val['nickname'];
            $index ++;
        }
        return $arr;
    }

    //获取指定商品的拼团信息数量
    public function getGroupCountByProId($pro_id){
        $count = Db::table('store_group_main_orders')->alias('gmo')
            ->join('store_members mem','gmo.mem_id = mem.mem_id','LEFT')
            ->where('gmo.pro_id = '.$pro_id.' and gmo.group_status = 1 and gmo.group_identity = 1 and order_status = 2 and gmo.status = 1')
            ->count();
        return $count;
    }

    //获取指定团号当前拼团人数
    public function getGroupCountByGroupNum($group_num){
        $where = "group_num = '$group_num' and (order_status = 2 or order_status = 3)";
        $res = Db::table('store_group_main_orders')->where($where)->count();
        return $res;
    }

    //获取积分商品列表(多用户版)
    public function getJfProInfo($limit,$offset){
        $res = Db::table('store_products')->alias('pro')->field('pro.id as pro_id,pro.p_name,i.thumb,pro.original_price,pro.associator_price,ex_jifen')
            ->join('store_pro_images i','pro.id = i.p_id','LEFT')
            ->where('pro.is_jf_product = 1 and pro.on_sale = 1 and pro.status = 1 and i.status = 1')
            ->order('pro.update_time desc')
            ->limit($offset,$limit)
            ->select();

        $ind = 0;
        $result = array();
        foreach($res as $val){
            $result[$ind]['pro_id'] = $val['pro_id'];
            $result[$ind]['p_name'] = $val['p_name'];
            $result[$ind]['thumb'] = $val['thumb'];
            $result[$ind]['original_price'] = $val['original_price'];
            if($val['associator_price'] < '0.01'){
                $result[$ind]['ex_jifen'] = $val['ex_jifen'].'积分';
            }else{
                $result[$ind]['ex_jifen'] = $val['ex_jifen'] .'积分' .' + '.$val['associator_price'] .'元';
            }
            $ind ++;
        }

        return $result;
    }

    //获取积分商品详情
    //接口地址: https://yp.dxshuju.com/app/public/index/product/getJfProDetail
    //传输方式  $_POST
    //条件    key=>pro_id(商品id)     value=>上一级传过来的id
    //关联表         store_products   商品表      store_pro_images    商品图片表
    public function getJfProDetail($pro_id){
        $res[] = Db::table('store_products')
            ->alias('pro')
            ->field('pro.id as pro_id,pro.p_name,pro.brand,pro.ex_jifen,
            i.thumb,i.image,i.config_image1,i.config_image2,i.config_image3,i.config_image4,
            pro.original_price,pro.associator_price,pro.wx_introduce,
            i.wx_config_image1,i.wx_config_image2,i.wx_config_image3,i.wx_config_image4,i.wx_config_image5,
            i.wx_config_image6,i.wx_config_image7,i.wx_config_image8,i.wx_config_image9,i.wx_config_image10')
            ->join('store_pro_images i','pro.id = i.p_id','LEFT')
            ->where('pro.id = '.$pro_id)
            ->find();
       foreach ($res as $k => $v)
       {
           $res[$k]['pro_id'] = $v['pro_id'];
           $res[$k]['p_name'] = $v['p_name'];
           $res[$k]['brand'] = $v['brand'];
           $res[$k]['thumb'] ='http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['thumb']);
           $res[$k]['image'] = 'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['image']);
           $res[$k]['config_image1'] = 'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['config_image1']);
           $res[$k]['config_image2'] = 'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['config_image2']);
           $res[$k]['config_image3'] = 'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['config_image3']);
           $res[$k]['config_image4'] = 'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['config_image4']);
           $res[$k]['original_price'] = $v['original_price'];
           $res[$k]['associator_price'] = $v['associator_price'];
           $res[$k]['wx_introduce'] = $v['wx_introduce'];
           $res[$k]['wx_config_image1'] = 'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['wx_config_image1']);
           $res[$k]['wx_config_image2'] = 'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['wx_config_image2']);
           $res[$k]['wx_config_image3'] = 'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['wx_config_image3']);
           $res[$k]['wx_config_image4'] = 'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['wx_config_image4']);
           $res[$k]['wx_config_image5'] = 'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['wx_config_image5']);
           $res[$k]['wx_config_image6'] = 'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['wx_config_image6']);
           $res[$k]['wx_config_image7'] = 'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['wx_config_image7']);
           $res[$k]['wx_config_image8'] = 'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['wx_config_image8']);
           $res[$k]['wx_config_image9'] = 'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['wx_config_image9']);
           $res[$k]['wx_config_image10'] = 'http://39.105.68.130:81/app/public/image/'.str_replace('\\','/',$v['wx_config_image10']);
       }
        //设置详情页轮播图
        $images_info = array();
        array_push($images_info,$res[$k]['image']);
        if($res[$k]['config_image1'] && $res[$k]['config_image1'] != ''){
            array_push($images_info,$res[$k]['config_image1']);
        }
        if($res[$k]['config_image2'] && $res[$k]['config_image2'] != ''){
            array_push($images_info,$res[$k]['config_image2']);
        }
        if($res[$k]['config_image3'] && $res[$k]['config_image3'] != ''){
            array_push($images_info,$res[$k]['config_image3']);
        }
        if($res[$k]['config_image4'] && $res[$k]['config_image4'] != ''){
            array_push($images_info,$res[$k]['config_image4']);
        }

        //设置商品详情部分图片
        $des_images_info = array();
        if($res[$k]['wx_config_image1'] && $res[$k]['wx_config_image1'] != ''){
            array_push($des_images_info,$res[$k]['wx_config_image1']);
        }
        if($res[$k]['wx_config_image2'] && $res[$k]['wx_config_image2'] != ''){
            array_push($des_images_info,$res[$k]['wx_config_image2']);
        }
        if($res[$k]['wx_config_image3'] && $res[$k]['wx_config_image3'] != ''){
            array_push($des_images_info,$res[$k]['wx_config_image3']);
        }
        if($res[$k]['wx_config_image4'] && $res[$k]['wx_config_image4'] != ''){
            array_push($des_images_info,$res[$k]['wx_config_image4']);
        }
        if($res[$k]['wx_config_image5'] && $res[$k]['wx_config_image5'] != ''){
            array_push($des_images_info,$res[$k]['wx_config_image5']);
        }
        if($res[$k]['wx_config_image6'] && $res[$k]['wx_config_image6'] != ''){
            array_push($des_images_info,$res[$k]['wx_config_image6']);
        }
        if($res[$k]['wx_config_image7'] && $res[$k]['wx_config_image7'] != ''){
            array_push($des_images_info,$res[$k]['wx_config_image7']);
        }
        if($res[$k]['wx_config_image8'] && $res[$k]['wx_config_image8'] != ''){
            array_push($des_images_info,$res[$k]['wx_config_image8']);
        }
        if($res[$k]['wx_config_image9'] && $res[$k]['wx_config_image9'] != ''){
            array_push($des_images_info,$res[$k]['wx_config_image9']);
        }
        if($res[$k]['wx_config_image10'] && $res[$k]['wx_config_image10'] != ''){
            array_push($des_images_info,$res[$k]['wx_config_image10']);
        }

        //设置规格信息
        $org_guige_info = DB::table('store_pro_config')
            ->field('content1_name,con_content1')
            ->where('pro_id = '.$res[$k]['pro_id'].' and status = 1')
            ->select();
        $guige_info = array();
        if(!$org_guige_info){
            $guige_info = array();
        }else{
            $con_content1_array = array();
            foreach($org_guige_info as $val){
                if(!in_array($val['con_content1'],$con_content1_array)){
                    array_push($con_content1_array,$val['con_content1']);
                }
            }
            $con_content1 = '';
            for($i=0;$i < count($con_content1_array);$i++){
                $con_content1 .=  $con_content1_array[$i] . '/';
            }
            $con_content1 = substr($con_content1,0,-1);
            $aa = [
                'content1_name'  => $org_guige_info[0]['content1_name'],
                'con_content1'   => $con_content1
            ];

            array_push($guige_info,$aa);
        }

        if($res[$k]['associator_price'] < '0.01'){
            $ex_jifen = $res['ex_jifen'] .'积分';
        }else{
            $ex_jifen = $res[$k]['ex_jifen'] .'积分'.' + '.$res[$k]['associator_price'].'元';
        }
        $result = [
            'pro_id' => $res[$k]['pro_id'],
            'p_name' => $res[$k]['p_name'],
            'brand' => $res[$k]['brand'],
            'ex_jifen' => $ex_jifen,
            'original_price' => $res[$k]['original_price'],
            'wx_introduce' => $res[$k]['wx_introduce'],
            'images'  => $images_info,
            'config_info' => $guige_info,
            'des_images' => $des_images_info
        ];
        return $result;
    }
}
