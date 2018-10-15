<?php
namespace app\index\model;
use think\Model;
use think\Db;

class Address extends Model
{

    public function userId()
    {

        $token = input('post.token');
        $telephone = input('post.telephone');
        $where = [
            'telephone' => $telephone,
            'token' => $token
        ];
        $user_id[] = Db::table('yp_member')->field('user_id')->where($where)->find();
        $user = [];
        foreach ($user_id as $v) {
            $user = $v['user_id'];
        }
        return $user;
    }

    /*
     *获取地址信息
     */

    //接口地址: https://yp.dxshuju.com/app/public/index/Address/getAddressInfo
    //条件       telephone(联系电话)
    //传输方式  $_POST
    //关联表        store_address 住址表

    public function getAddressInfo($telephone)
    {
        $res = DB::table('store_address')
            ->field('id as a_id,rec_name,mobile,province,city,area,address')
            ->where("telephone = '$telephone' and status = 1 ")
            ->order('create_time desc')
            ->select();
        if ($res == [])
        {
            echo json_encode(array(
                'statuscode'  => 1,
                'message'   =>  '获取地址信息成功',
                'result'      => $res
            ));
            exit;
        }

        $index = 0;
        $result = array();
        foreach ($res as $val) {
            $result[$index]['id'] = $val['id'];
            $result[$index]['rec_name'] = $val['rec_name'];
            $result[$index]['mobile'] = $val['mobile'];
            $result[$index]['address'] = $val['province'] . $val['city'] . $val['area'] . $val['address'];
            $index++;
        }
        return $result;

    }

    /*
     * 添加地址
     */

    //接口地址: https://yp.dxshuju.com/app/public/index/Address/addAddress
    //条件    rec_name用户名     mobile  电话  address 详细住址`province  省   city 市   area  区
    //传输方式  $_POST
    //关联表        store_address 住址表

    public function addAddress($param){
        //获取参数
        $rec_name = !empty($param['rec_name']) ? $param['rec_name'] : '';
        $mobile = !empty($param['mobile']) ? $param['mobile'] : '';
        $telephone = !empty($param['telephone']) ? $param['telephone'] : '';
        $address = !empty($param['address']) ? $param['address'] : '';
        $province = !empty($param['province']) ? $param['province']: '';
        $city = !empty($param['city']) ? $param['city'] : '';
        $area = !empty($param['area']) ? $param['area'] : '';

        $data = [
            'rec_name' => $rec_name,
            'mobile' => $mobile,
            'telephone'=>$telephone,
            'province' => $province,
            'city' => $city,
            'area' => $area,
            'address' => $address,
            'is_default' => 0,
            'create_time' => date('Y-m-d H:i:s')
        ];

        $res = DB::table('store_address')->insertGetId($data);
        return $res;
    }

    /*
     * 编辑地址(返回地址信息)
     */

    //接口地址: https://yp.dxshuju.com/app/public/index/Address/getAddressInfoById
    //条件    a_id    住址id
    //传输方式  $_POST
    //关联表        store_address 住址表

    public function getAddressInfoById($a_id){
        $res = DB::table('store_address')
            ->field('id as a_id,rec_name,mobile,province,city,area,address')
            ->where("id = $a_id")
            ->find();
        return $res;
    }

    /*
     * 更新地址信息
     */

    //接口地址: https://yp.dxshuju.com/app/public/index/Address/updateAddress
    //条件    a_id(地址信息id)
    //传输方式  $_POST
    //关联表        store_address 住址表

    public function updateAddress($param){
        //获取参数
        $aid = !empty($param['a_id']) ? $param['a_id'] : '';

        $rec_name = !empty($param['rec_name']) ? $param['rec_name'] : '';
        $mobile = !empty($param['mobile']) ? $param['mobile'] : '';
        $address = !empty($param['address']) ? $param['address'] : '';
        $province = !empty($param['province']) ? $param['province']: '';
        $city = !empty($param['city']) ? $param['city'] : '';
        $area = !empty($param['area']) ? $param['area'] : '';

        $data = [
            'rec_name' => $rec_name,
            'mobile' => $mobile,
            'province' => $province,
            'city' => $city,
            'area' => $area,
            'address' => $address,
            'is_default' => 0
        ];

        $res = DB::table('store_address')
            ->where("id = $aid")
            ->update($data);
        return $res;
    }

    /*
     *下单时选择地址
     */

    //接口地址: https://yp.dxshuju.com/app/public/index/Address/chooseAddress
    //条件    a_id(地址信息id)    mobile（联系电话）
    //传输方式  $_POST
    //关联表        store_address 住址表

    public function chooseAddress($param){
        //获取参数
        $telephone = !empty($param['telephone']) ? $param['telephone'] : '';
        $a_id = !empty($param['a_id']) ? $param['a_id'] : '';
        //更改该用户所有地址默认状态为0
        $where = "telephone = '$telephone' and status = 1";


        $data1['is_default'] = 0;
        $default_res = Db::table('store_address')->where($where)->update($data1);
        //设置选中的地址is_default为1
        $con = "id = $a_id";
        $data2['is_default'] = 1;
        $res = Db::table('store_address')->where($con)->update($data2);
        if($res){
            return 1;
        }else{
            return 0;
        }
    }
}