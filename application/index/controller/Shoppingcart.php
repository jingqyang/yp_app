<?php
namespace app\index\controller;
use think\Controller;
use think\Db;

class Shoppingcart extends Controller{



    public function userInfo()
    {
        $token = input('post.token');
        $telephone = input('post.telephone');
        $where = [
            'telephone'=>$telephone,
            'token' => $token
        ];
        $tab = Db::table('yp_member')->field('token,telephone')->where($where)->find();

        if (!$tab)
        {
            echo json_encode([
               'statuscode'=>-1,
                'message'=>'您还未登录，请登录'
            ]);
            exit();
        }

    }

    public function userId()
    {
        $token = input('post.token');
        $telephone = input('post.telephone');
        $where = [
            'telephone'=>$telephone,
            'token' => $token
        ];
        $user_id[] = Db::table('yp_member')->field('user_id')->where($where)->find();
        $user =[];
        foreach ($user_id as $v)
        {
            $user = $v['user_id'];
        }
        return $user;
    }

    //添加商品到购物车(普通版)
    public function addProIntoCart(){
        //判断用户是否登录
        $this->userInfo();
        //获取参数
        $param = input('post.');
        $res = model('ShoppingCart')->addProIntoCart($param);
        echo json_encode(array(
            'statuscode'  => 1,
            'message' => '添加商品到购物车成功',
            'result'  => $res
        ));
        exit;
    }

    //添加商品到购物车(拼团版)
    public function addGroupProIntoCart(){
        //获取参数
        $param = input('post.');
        $res = model('ShoppingCart')->addGroupProIntoCart($param);
        echo json_encode(array(
            'statuscode'  => 1,
            'result'  => $res
        ));
        exit;
    }

    //添加积分商品到购物车(普通版)
    public function addJfProIntoCart(){
        //判断用户是否登录
        $this->userInfo();
        //获取参数
        $param = input('post.');
        $res = model('ShoppingCart')->addJfProIntoCart($param);
        echo json_encode(array(
            'statuscode'  => 1,
            'message' => '添加积分商品到购物车成功',
            'result'  => $res
        ));
        exit;
    }

    //获取购物车信息(单用户版)
    public function getShoppingCartInfo(){
        //判断用户是否登录
        $this->userInfo();
        //获取参数
        $param = input('post.');
        $cart_id = input('post.cart_id');
        $res = model('ShoppingCart')->getShoppingCartInfo($param);
        echo json_encode(array(
            'statuscode'  => 1,
            'message'       => '获取购物车信息成功',
            'result'      => $res

        ));
        exit;
    }

    //获取购物车信息(多用户版)
    public function getShoppingCartInfoMulti(){
        //获取参数
        $param = input('post.');
        $res = model('ShoppingCart')->getShoppingCartInfoMulti($param);
        echo json_encode(array(
            'statuscode'  => 1,
            'result'      => $res
        ));
        exit;
    }

    //更改单条信息选中状态
    public function updateSelectedStatus(){
        //判断用户是否登录
        $this->userInfo();
        //获取参数
        $param = input('post.');
        $res = model('ShoppingCart')->updateSelectedStatus($param);
        if ($res)
        {
            echo json_encode(array(
                'statuscode'  => 1,
                'message'   => '更改单条信息选中状态成功'
            ));
            exit;
        }else{
            echo json_encode(array(
                'statuscode'  => 0,
                'message'   => '更改单条信息选中状态失败'
            ));
            exit();
        }
    }

    //获取选中的价格信息(单用户版)
    public function getSelectedTotalPrice(){
        //判断用户是否登录
        $this->userInfo();
        //获取参数
        $param = input('post.');
        $cart_id = input('post.cart_id');
        $res = model('ShoppingCart')->getSelectedTotalPrice($param,$cart_id);
        echo json_encode(array(
            'statuscode'  => 1,
            'message'   =>  '获取选中的价格信息成功',
            'result'      => $res
        ));
        exit;
    }

    //获取选中的价格信息(多用户版)
    public function getSelectedTotalPriceMulti(){
        //获取参数
        $param = input('post.');
        $res = model('ShoppingCart')->getSelectedTotalPriceMulti($param);
        echo json_encode(array(
            'statuscode'  => 1,
            'result'      => $res
        ));
        exit;
    }

    //更改全部选中状态(单用户版)
    public function updateAllSelectedStatus(){
        //判断用户是否登录
        $this->userInfo();
        //获取参数
        $param = input('post.');
        $res = model('ShoppingCart')->updateAllSelectedStatus($param);
        if ($res)
        {
            echo json_encode(array(
                'statuscode'  => 1,
                'message'      => '更改全部选中状态成功'
            ));
            exit;
        }else{
            echo json_encode(array(
                'statuscode'  => 0,
                'message'      => '更改全部选中状态失败'
            ));
            exit;
        }
    }

    //更改全部选中状态(多用户版)
    public function updateAllSelectedStatusMulti(){
        //获取参数
        $param = input('post.');
        $res = model('ShoppingCart')->updateAllSelectedStatusMulti($param);
        echo json_encode(array(
            'statuscode'  => 1

        ));
        exit;
    }

    //更改单个商品选中数量((普通商城版))
    public function updateSelectedCount(){
        //判断用户是否登录
        $this->userInfo();
        //获取参数
        $param = input('post.');
        $res = model('ShoppingCart')->updateSelectedCount($param);
        if ($res)
        {
            echo json_encode(array(
                'statuscode'  => 1,
                'message'   =>'更改单个商品选中数量成功'
            ));
            exit;
        }else{
            echo json_encode(array(
                'statuscode'  => 0,
                'message'      => '更改单个商品选中数量失败'
            ));
            exit;
        }
    }

    //更改单个商品选中数量(拼团商城单独购买版)
    public function updateProCountBySingle(){
        //获取参数
        $param = input('post.');
        $res = model('ShoppingCart')->updateProCountBySingle($param);
        echo json_encode(array(
            'statuscode'  => 1,
            'result'  => $res
        ));
        exit;
    }

    //独立版--点击"去结算"返回选中的购物车内信息
    public function getSelectedCartInfo(){
        //判断用户是否登录
        $this->userInfo();

        $telephone = input('post.telephone');
        $mobile = input('post.mobile');
        $bis_id = input('post.bis_id');
        $res = model('ShoppingCart')->getSelectedCartInfo($telephone,$bis_id,$mobile);
        if ($res)
        {
            echo json_encode(array(
                'statuscode'  => 1,
                'message'   =>  '返回选中的购物车内信息成功',
                'result'      => $res
            ));
            exit;
        }else{
            echo json_encode(array(
                'statuscode'  => 0,
                'message'   =>  '返回选中的购物车内信息失败',
            ));
            exit;
        }
    }

    //总站--点击"去结算"返回选中的购物车及运费相关信息    513
    public function getSelectedCartInfoMulti(){
        //判断用户是否登录
        $this->userInfo();
        $telephone = input('post.telephone');
        $mobile = input('post.mobile');
        $res = model('ShoppingCart')->getSelectedCartInfoMulti($telephone,$mobile);
        if ($res)
        {
            echo json_encode(array(
                'statuscode'  => 1,
                'message'   =>  '返回选中的购物车及运费相关信息成功',
                'result'      => $res
            ));
            exit;
        }else{
            echo json_encode(array(
                'statuscode'  => 1,
                'message'   =>  '返回选中的购物车及运费相关信息失败'
            ));
            exit;
        }
    }

    //单独购买(拼团版)
    public function getSelectedCartInfoBySingle(){
        $cart_id = input('post.cart_id');
        $openid = input('post.openid');
        $bis_id = input('post.bis_id');
        $from = input('post.from');
        $res = model('ShoppingCart')->getSelectedCartInfoBySingle($cart_id,$openid,$bis_id,$from);
        echo json_encode(array(
            'statuscode'  => 1,
            'result'      => $res
        ));
        exit;
    }

    //获取购物车内选中的积分商品
    public function getJfProSelectedInfo(){
        $cart_id = input('post.cart_id');
        $openid = input('post.openid');
        $bis_id = input('post.bis_id');
        $res = model('ShoppingCart')->getJfProSelectedInfo($cart_id,$openid,$bis_id);
        echo json_encode(array(
            'statuscode'  => 1,
            'result'      => $res
        ));
        exit;
    }

    //判断购物车内是否存在勾选的产品
    public function checkSelectedPro(){
        $this->userInfo();
        $telephone = input('post.telephone');
        $res = model('ShoppingCart')->checkSelectedPro($telephone);
       if ($res)
       {
           echo json_encode(array(
               'statuscode'  => 1,
               'message'   =>  '判断购物车内是否存在勾选的产品成功',
               'result'      => $res
           ));
           exit;
       }else{
           echo json_encode(array(
               'statuscode'  => 0,
               'message'   =>  '判断购物车内是否存在勾选的产品失败',
           ));
           exit;
       }
    }

    //根据id删除购物车
    //接口地址: https://yp.dxshuju.com/app/public/index/shoppingcart/deleteCartById
    //条件        cart_id (订单id)
    //传输方式  $_POST
    //关联表       store_shopping_carts   订单表
    public function deleteCartById(){
        $this->userInfo();
        $cart_id = input('post.cart_id');
        $data['status'] = -1;
        $res = Db::table('store_shopping_carts')->where('id = '.$cart_id)->update($data);
        if($res){
            echo json_encode(array(
                'statuscode'  => 1,
                'message'       => '根据id删除购物车成功'
            ));
            exit;
        }else{
            echo json_encode(array(
                'statuscode'  => 0,
                'message'       => '根据id删除购物车失败'
            ));
            exit;
        }
    }

    //获取当前店铺的物流种类相关 801
    public function getTransportType(){
        //获取参数
        $bis_id = input('post.bis_id');
        $province = input('post.province');
        $res = model('ShoppingCart')->getTransportType($bis_id,$province);
        echo json_encode(array(
            'statuscode'  => 1,
            'message'   =>  '获取当前店铺的物流种类相关成功',
            'result'      => $res
        ));
        exit;
    }

    //更改购物车指定店铺内商品选中状态  880
    public function updateBisStatus(){
        $this->userInfo();
        //获取参数
        $telephone = input('post.telephone');
        $bis_id = input('post.bis_id');
        $bis_status = input('post.bis_status');
        $res = model('ShoppingCart')->updateBisStatus($telephone,$bis_id,$bis_status);
        if ($res)
        {
            echo json_encode(array(
                'statuscode'  => 1,
                'message'   =>  '更改购物车指定店铺内商品选中状态成功',
                'result'      => $res
            ));
            exit;
        }else{
            echo json_encode(array(
                'statuscode'  => 0,
                'message'   =>  '更改购物车指定店铺内商品选中状态失败'
            ));
            exit;
        }
    }

    //判断积分是否足够
    public function checkJifenEnough(){
        //获取参数
        $openid = input('post.openid');
        $jifen_amount = input('post.jifen_amount');
        $where = "mem_id = '$openid'";
        $res = Db::table('store_members')->field('jifen')->where($where)->find();
        if($res['jifen'] < $jifen_amount){
            echo json_encode(array(
                'statuscode'  => 1,
                'result'      => -1
            ));
            exit;
        }else{
            echo json_encode(array(
                'statuscode'  => 1,
                'result'      => 1
            ));
            exit;
        }
    }
}



