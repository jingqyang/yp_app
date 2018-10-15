<?php
namespace app\index\controller;
use think\Controller;
use think\Db;

class Address extends Controller{

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

    //获取地址信息    26
    public function getAddressInfo(){
        $this->userInfo();
        //获取参数
        $telephone = input('post.telephone');
        $res = model('Address')->getAddressInfo($telephone);
        if ($res)
        {
            echo json_encode(array(
                'statuscode'  => 1,
                'message'   =>  '获取地址信息成功',
                'result'      => $res
            ));
            exit;
        }else{
            echo json_encode(array(
                'statuscode'  => 0,
                'message'   =>  '获取地址信息失败'
            ));
            exit;
        }
    }

    //添加地址  60
    public function addAddress(){
        $this->userInfo();
        //获取参数
        $param = input('post.');
        $res = model('Address')->addAddress($param);
       if ($res)
       {
           echo json_encode(array(
               'statuscode'  => 1,
               'message'   =>  '添加地址成功',
               'result'      => $res
           ));
           exit;
       }else{
           echo json_encode(array(
               'statuscode'  => 1,
               'message'   =>  '添加地址失败'
           ));
           exit;
       }
    }

    //编辑地址(返回地址信息)  95
    public function getAddressInfoById(){
        $this->userInfo();
        $a_id = input('post.a_id');
        $res = model('Address')->getAddressInfoById($a_id);
        if ($res)
        {
            echo json_encode(array(
                'statuscode'  => 1,
                'message'   =>  '编辑地址(返回地址信息)成功',
                'result'      => $res
            ));
            exit;
        }else{
            echo json_encode(array(
                'statuscode'  => 0,
                'message'   =>  '编辑地址(返回地址信息)失败'
            ));
            exit;
        }
    }

    //更新地址信息    109
    public function updateAddress(){
        $this->userInfo();
        //获取参数
        $param = input('post.');
        $res = model('Address')->updateAddress($param);
       if ($res)
       {
           echo json_encode(array(
               'statuscode'  => 1,
               'message'   =>  '更新地址信息成功',
               'result' =>  $res
           ));
           exit;
       }else{
           echo json_encode(array(
               'statuscode'  => 0,
               'message'   =>  '更新地址信息失败'
           ));
           exit;
       }
    }

    //下单时选择地址
    public function chooseAddress(){
        $this->userInfo();
        //获取参数
        $param = input('post.');
        $res = model('Address')->chooseAddress($param);
        if ($res)
        {
            echo json_encode(array(
                'statuscode'  => 1,
                'message'   =>  '下单时选择地址成功'
            ));
            exit;
        }else{
            echo json_encode(array(
                'statuscode'  => 0,
                'message'   =>  '下单时选择地址失败'
            ));
            exit;
        }
    }

    /*
     * 根据id删除地址
     */

    //接口地址: https://yp.dxshuju.com/app/public/index/Address/deleteAddress
    //条件    $a_id(地址信息id)
    //传输方式  $_POST
    //关联表        store_address 住址表

    public function deleteAddress(){
        $this->userInfo();
        $a_id = input('post.a_id');
        $data['status'] = -1;
        $res = Db::table('store_address')->where('id = '.$a_id)->update($data);
        if($res){
            echo json_encode(array(
                'statuscode'  => 1,
                'message'   =>  '根据id删除地址成功'
            ));
            exit;
        }else{
            echo json_encode(array(
                'statuscode'  => 0,
                'message'   =>  '根据id删除地址失败'
            ));
            exit;
        }
    }
}



