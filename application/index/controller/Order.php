<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Validate;

class Order extends Controller{

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

    //获取订单信息(普通商城版)
    public function getOrderInfo(){
        $this->userInfo();
        //获取参数
        $param = input('post.');
        $res = model('Order')->getOrderInfo($param);
        echo json_encode(array(
            'statuscode'  => 1,
            'result'      => $res
        ));
        exit;
    }


    //获取订单详情信息(普通商城版)
    public function getOrderDetailInfo(){
        $this->userInfo();
        //获取参数
        $param = input('post.');
        $res = model('Order')->getOrderDetailInfo($param);
        echo json_encode(array(
            'statuscode'  => 1,
            'message'   =>  '获取订单详情信息成功',
            'result'      => $res
        ));
        exit;
    }

    //生成订单(普通商城版)
    public function makeOrder(){
        $this->userInfo();
        //获取参数
        $param = input('post.');
        $res = model('Order')->makeOrder($param);
        if ($res)
        {
            echo json_encode(array(
                'statuscode'  => 1,
                'message'   =>  '生成订单成功',
                'result'      => $res
            ));
            exit;
        }else{
            echo json_encode(array(
                'statuscode'  => 0,
                'message'   =>  '生成订单失败'
            ));
            exit;
        }
    }

    /*
     *更改订单状态为已付款(普通商城版)
     */

    //接口地址: https://yp.dxshuju.com/app/public/index/order/updateOrderStatus
    //条件    order_id(订单id)
    //传输方式  $_POST
    // order_status 订单状态(1-订单未处理 2-订单已确认，款未到 3-款已到，正在备货 4-货已发出 5-货已收到，谢谢您的定购 )
    //关联表           store_main_orders   订单表

    public function updateOrderStatus(){
        $this->userInfo();
        $order_id = input('post.order_id');
        $data['order_status'] = 3;
        $data['update_time'] = date('Y-m-d H:i:s');
        $data['pay_time'] = date('Y-m-d H:i:s');

        $res = Db::table('store_main_orders')->where('id = '.$order_id)->update($data);
        if ($res)
        {
            echo json_encode(array(
                'statuscode'  => 1,
                'message'      => '更改订单状态为已付款(普通商城版）成功',
                'result'      => $res
            ));
            exit;
        }else{
            echo json_encode(array(
                'statuscode'  => 0,
                'message'      => '更改订单状态为已付款(普通商城版)失败'
            ));
            exit;
        }
    }

    /*
     *根据id查询订单号和总金额(普通商城版)
     */

    //接口地址: https://yp.dxshuju.com/app/public/index/order/getOrderInfoByOrderId
    //条件    order_id(订单id)
    //传输方式  $_POST
    // order_status 订单状态(1-订单未处理 2-订单已确认，款未到 3-款已到，正在备货 4-货已发出 5-货已收到，谢谢您的定购 )
    //关联表           store_main_orders   订单表

    public function getOrderInfoByOrderId(){
        $this->userInfo();
        $order_id = input('post.order_id');
        $res = Db::table('store_main_orders')
            ->field('order_no,total_amount')
            ->where('id = '.$order_id)
            ->find();
        if ($res)
        {
            echo json_encode(array(
                'statuscode'  => 1,
                'message'      => '根据id查询订单号和总金额(普通商城版)成功',
                'result'      => $res
            ));
            exit;
        }else{
            echo json_encode(array(
                'statuscode'  => 0,
                'message'      => '根据id查询订单号和总金额(普通商城版)失败'
            ));
            exit;
        }
    }

    /*
     *确认订单(普通商城版)
     */

    //接口地址: https://yp.dxshuju.com/app/public/index/order/confirmOrder
    //条件    order_id(订单id)
    //传输方式  $_POST
    // order_status 订单状态(1-订单未处理 2-订单已确认，款未到 3-款已到，正在备货 4-货已发出 5-货已收到，谢谢您的定购 )
    //关联表           store_main_orders   订单表


    public function confirmOrder(){
        $this->userInfo();
        $order_no = input('post.order_no');
        $data['order_status'] = 5;
        $res = Db::table('store_main_orders')->where("order_no = '$order_no'")->update($data);
        if ($res)
        {
            echo json_encode(array(
                'statuscode'  => 1,
                'message'      => '确认订单成功',
                'result'      => $res
            ));
            exit;
        }else{
            echo json_encode(array(
                'statuscode'  => 0,
                'message'      => '确认订单失败'
            ));
            exit;
        }
    }

    /*
     *取消订单(普通商城版)
     */

    //接口地址: https://yp.dxshuju.com/app/public/index/order/cancelOrder
    //条件    order_id(订单id)
    //传输方式  $_POST
    // order_status 订单状态(1-订单未处理 2-订单已确认，款未到 3-款已到，正在备货 4-货已发出 5-货已收到，谢谢您的定购 )
    //关联表           store_main_orders   订单表

    public function cancelOrder(){
        $this->userInfo();
        $order_no = input('post.order_no');
        $data['order_status'] = -1;
        $res = Db::table('store_main_orders')->where("order_no = '$order_no'")->update($data);
        if ($res)
        {
            echo json_encode(array(
                'statuscode'  => 1,
                'message'      => '取消订单成功',
                'result'      => $res
            ));
            exit;
        }else{
            echo json_encode(array(
                'statuscode'  => 0,
                'message'      => '取消订单失败'
            ));
            exit;
        }
    }

    //设置主订单表推荐人及佣金信息(普通商城版)
    public function setMainRecInfo(){
        $order_id = input('post.order_id');
        $rec_id = input('post.rec_id');
        $res = model('Order')->setMainRecInfo($order_id,$rec_id);
        echo json_encode(array(
            'statuscode'  => 1,
            'result'      => $res
        ));
        exit;
    }

    //查询佣金订单
    public function getRecOrders(){
        $param = input('post.');
        $res = model('Order')->getRecOrders($param);
        $count = model('Order')->getRecOrderCount($param);
        echo json_encode(array(
            'statuscode'  => 1,
            'count'       => $count,
            'result'      => $res
        ));
        exit;
    }

    //生成提现订单
    public function makeTixianOrders(){
        $param = input('post.');
        $res = model('Order')->makeTixianOrders($param);
        echo json_encode(array(
            'statuscode'  => 1,
            'message'      => '生成提现订单成功'
        ));
        exit;
    }

    //查看提现记录
    public function getTixianRecords(){
        $openid = input('post.openid');
        $res = model('Order')->getTixianRecords($openid);
        echo json_encode(array(
            'statuscode'  => 1,
            'result'      => $res
        ));
        exit;
    }

    //查询物流
    public function getLogisticInfo(){
        //获取参数
        $shipperCode = input('post.shipperCode');
        $logisticCode = input('post.logisticCode');

        $EBusinessID = "1313383";
        $AppKey = "06ab8d28-ff46-4225-9fcf-86eb3693c6ec";
        $ReqURL = "http://api.kdniao.cc/Ebusiness/EbusinessOrderHandle.aspx";

        $requestData= "{'OrderCode':'','ShipperCode':'".$shipperCode."','LogisticCode':'".$logisticCode."'}";

        $datas = array(
            'EBusinessID' => $EBusinessID,
            'RequestType' => '1002',
            'RequestData' => urlencode($requestData) ,
            'DataType' => '2',
        );
        $datas['DataSign'] = $this->encrypt($requestData, $AppKey);
        $result = $this->sendPost($ReqURL, $datas);

        //根据公司业务处理返回的信息......
        return $result;
    }

    function sendPost($url, $datas) {
        $temps = array();
        foreach ($datas as $key => $value) {
            $temps[] = sprintf('%s=%s', $key, $value);
        }
        $post_data = implode('&', $temps);
        $url_info = parse_url($url);
        if(empty($url_info['port']))
        {
            $url_info['port']=80;
        }
        $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        $httpheader.= "Host:" . $url_info['host'] . "\r\n";
        $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
        $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
        $httpheader.= "Connection:close\r\n\r\n";
        $httpheader.= $post_data;
        $fd = fsockopen($url_info['host'], $url_info['port']);
        fwrite($fd, $httpheader);
        $gets = "";
        $headerFlag = true;
        while (!feof($fd)) {
            if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
                break;
            }
        }
        while (!feof($fd)) {
            $gets.= fread($fd, 128);
        }
        fclose($fd);

        return $gets;
    }

    //生成签名
    function encrypt($data, $appkey) {
        return urlencode(base64_encode(md5($data.$appkey)));
    }

}



