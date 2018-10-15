<?php
namespace app\index\model;
use think\Controller;
use think\Model;
use think\Db;

class ShoppingCart extends Model{

//    public function userId()
//    {
//
//        $token = input('post.token');
//        $telephone = input('post.telephone');
//        $where = [
//            'telephone'=>$telephone,
//            'token' => $token
//        ];
//        $user_id[] = Db::table('yp_member')->field('user_id')->where($where)->find();
//        $user =[];
//        foreach ($user_id as $v)
//        {
//            $user = $v['user_id'];
//        }
//        return $user;
//    }

    /*
     * 添加商品到购物车(普通版)
     */

    //接口地址: https://yp.dxshuju.com/app/public/index/shoppingcart/addProIntoCart
    //条件        $pro_id(商品配置id)   bis_id(店铺id)    count(数量)
    //传输方式  $_POST
    //关联表       store_shopping_carts   订单表

    public function addProIntoCart($param){
        //获取参数
        $pro_id = !empty($param['pro_id']) ? $param['pro_id'] : '';
        $bis_id = !empty($param['bis_id']) ? $param['bis_id'] : '';
        $count = !empty($param['count']) ? $param['count'] : '';
        $telephone = !empty($param['telephone']) ? $param['telephone'] : '';
        $data = [
            'telephone'=>$telephone,
            'bis_id'  => $bis_id,
            'pro_id'  => $pro_id,
            'count'  => $count,
            'selected'  => 1,
            'cart_type'  => 1,
            'status'    => 1,
            'create_time'  => date('Y-m-d H:i:s'),
            'update_time'  => date('Y-m-d H:i:s'),
        ];
        $res = Db::table('store_shopping_carts')->insertGetId($data);

        if(!$res){
            echo json_encode(array(
                'statuscode'  => 0,
                'message'     => '添加商品到购物车失败'
            ));
            exit;
        }

        return $res;
    }

    //添加商品到购物车(拼团版)
    public function addGroupProIntoCart($param){
        //获取参数
        $pro_id = !empty($param['pro_id']) ? $param['pro_id'] : '';
        $bis_id = !empty($param['bis_id']) ? $param['bis_id'] : '';
        $count = !empty($param['count']) ? $param['count'] : '';
        $wx_id = !empty($param['wx_id']) ? $param['wx_id'] : '';

        $data = [
            'bis_id'  => $bis_id,
            'pro_id'  => $pro_id,
            'wx_id'  => $wx_id,
            'count'  => $count,
            'selected'  => 1,
            'cart_type'  => 2,
            'create_time'  => date('Y-m-d H:i:s'),
            'update_time'  => date('Y-m-d H:i:s'),
        ];

        $res = Db::table('store_shopping_carts')->insertGetId($data);

        if(!$res){
            echo json_encode(array(
                'statuscode'  => 0,
                'message'     => '添加失败'
            ));
            exit;
        }

        return $res;
    }

    //添加积分商品到购物车(多用户版)
    //接口地址: https://yp.dxshuju.com/app/public/index/shoppingcart/addJfProIntoCart
    //条件        $pro_id(商品id)   bis_id(店铺id)    count(数量)
    //传输方式  $_POST
    //关联表       store_shopping_carts   订单表
    public function addJfProIntoCart($param){
        //获取参数
        $pro_id = !empty($param['pro_id']) ? $param['pro_id'] : '';
        $bis_id = !empty($param['bis_id']) ? $param['bis_id'] : '';
        $count = !empty($param['count']) ? $param['count'] : '';
//        $wx_id = !empty($param['wx_id']) ? $param['wx_id'] : '';

        $data = [
            'bis_id'  => $bis_id,
            'pro_id'  => $pro_id,
            'wx_id'  =>$this->userId(),
            'count'  => $count,
            'selected'  => 1,
            'cart_type'  => 3,
            'create_time'  => date('Y-m-d H:i:s'),
            'update_time'  => date('Y-m-d H:i:s'),
        ];

        $res = Db::table('store_shopping_carts')->insertGetId($data);

        if(!$res){
            echo json_encode(array(
                'statuscode'  => 0,
                'message'     => '添加积分商品到购物车失败'
            ));
            exit;
        }

        return $res;
    }

    /*
     *获取购物车信息(单用户版)
    */

    //接口地址: https://yp.dxshuju.com/app/public/index/shoppingcart/getShoppingCartInfo
    //条件        bis_id(店铺id)  telephone(手机号)
    //传输方式  $_POST
    //关联表       store_shopping_carts   订单表      store_pro_config   商品配置表
    //             store_products     商品表    store_pro_images       商品图片表

    //  cat_id  订单id    p_name   商品名称    con_content1      配置内容1
    //    con_content2    配置内容2     associator_price   商品单价
    //    count 商品数量    thumb   缩略图         selected    是否被选中

    public function getShoppingCartInfo($param){
        //获取参数
        $telephone = !empty($param['telephone']) ? $param['telephone'] : '';
//        $order_no = !empty($param['order_no']) ? $param['order_no'] : '';
        $bis_id = !empty($param['bis_id']) ? $param['bis_id'] : '';
        $where = "carts.bis_id = ".$bis_id." and carts.telephone = '$telephone' and carts.status = 1 and carts.cart_type = 1 and con.status = 1 and img.status = 1 ";
        $res = Db::table('store_shopping_carts')
            ->alias('carts')
            ->field('carts.id as cart_id,pro.p_name,con.con_content1,con_content2,con.price as associator_price,carts.count,carts.selected,img.thumb')
            ->join('store_pro_config con','carts.pro_id = con.id','LEFT')
            ->join('store_products pro','con.pro_id = pro.id','LEFT')
            ->join('store_pro_images img','img.p_id = pro.id','LEFT')
            ->where($where)
            ->order('carts.create_time desc')
            ->select();
        if(!$res){
            echo json_encode(array(
                'statuscode'  => 0,
                'message'     => '获取购物车信息失败'
            ));
            exit;
        }
        $index = 0;
        foreach($res as $val){
            $res[$index]['selected'] = $val['selected']  == 1 ? true : false;
            //http://39.105.68.130:81/app/public/image/
            $res[$index]['thumb'] = str_replace('\\','/',$val['thumb']);
            $index ++;
        }
        return $res;
    }

    //获取购物车信息(多用户版)
    public function getShoppingCartInfoMulti($param){
        //获取参数
        $wx_id = !empty($param['wx_id']) ? $param['wx_id'] : '';

        $where = "carts.wx_id = '$wx_id' and carts.status = 1 and carts.cart_type = 1 and con.status = 1 and img.status = 1";
        $res = Db::table('store_shopping_carts')->alias('carts')->field('carts.id as cart_id,carts.bis_id')
            ->join('store_pro_config con','carts.pro_id = con.id','LEFT')
            ->join('store_products pro','con.pro_id = pro.id','LEFT')
            ->join('store_pro_images img','img.p_id = pro.id','LEFT')
            ->where($where)
            ->order('carts.create_time desc')
            ->select();

        $bisArr = array();
        foreach($res as $val){
            if(!in_array($val['bis_id'],$bisArr)){
               array_push($bisArr,$val['bis_id']) ;
            }
        }

        $index = 0;
        $cart_res = array();
        foreach($bisArr as $val){
            //获取店铺信息
            $bis_res = Db::table('store_bis')->field('id as bis_id,bis_name,thumb as bis_thumb')->where('id = '.$val)->find();
            $cart_res[$index]['bis_id'] = $bis_res['bis_id'];
            $cart_res[$index]['bis_status'] = $this->checkBisStatus($wx_id,$bis_res['bis_id']);
            $cart_res[$index]['bis_name'] = $bis_res['bis_name'];
            $cart_res[$index]['bis_thumb'] = $bis_res['bis_thumb'];
            $cart_res[$index]['pro_info'] = $this->getCartProInfo($wx_id,$bis_res['bis_id']);
            $index ++;
        }

        return $cart_res;
    }

    //获取购物车内指定店铺商品信息
    public function getCartProInfo($wx_id,$bis_id){
        $where = "carts.wx_id = '$wx_id' and carts.bis_id = $bis_id and carts.status = 1 and carts.cart_type = 1 and con.status = 1 and img.status = 1";
        $res = Db::table('store_shopping_carts')->alias('carts')->field('carts.id as cart_id,pro.p_name,con.con_content1,con_content2,con.price as associator_price,carts.count,carts.selected,img.thumb')
            ->join('store_pro_config con','carts.pro_id = con.id','LEFT')
            ->join('store_products pro','con.pro_id = pro.id','LEFT')
            ->join('store_pro_images img','img.p_id = pro.id','LEFT')
            ->where($where)
            ->order('carts.create_time desc')
            ->select();
        $index = 0;
        foreach($res as $val){
            $res[$index]['selected'] = $val['selected']  == 1 ? true : false;
            $index ++;
        }
        return $res;
    }

    //检验指定店铺购物车选中状态
    public function checkBisStatus($wx_id,$bis_id){
        $where = "carts.wx_id = '$wx_id' and carts.bis_id = $bis_id and carts.status = 1 and carts.cart_type = 1";
        $res = Db::table('store_shopping_carts')->alias('carts')->field('carts.id as cart_id,carts.selected')
            ->where($where)
            ->order('carts.create_time desc')
            ->select();

        foreach($res as $val){
            if($val['selected'] == 1){
                continue;
            }else{
                return false;
            }
        }
        return true;

    }

    /*
     * 更改单条信息选中状态
     */

    //接口地址: https://yp.dxshuju.com/app/public/index/shoppingcart/updateSelectedStatus
    //条件        cart_id(订单id)       selected_status(状态 0 或 1)
    //传输方式  $_POST
    //关联表       store_shopping_carts    订单表

    public function updateSelectedStatus($param){
        //获取参数
        $cart_id = !empty($param['cart_id']) ? $param['cart_id'] : '';
        $selected_status = !empty($param['selected_status']) ? $param['selected_status'] : '';

        $data['selected'] = $selected_status;
        $res = Db::table('store_shopping_carts')->where('id = '.$cart_id)->update($data);
        return $res;
    }

    /*
     * 获取选中的价格信息(单用户版)
     */

    //接口地址: https://yp.dxshuju.com/app/public/index/shoppingcart/getSelectedTotalPrice
    //条件        bis_id(店铺id)    telephone(用户手机号)   cart_id(购物车商品id)
    //传输方式  $_POST
    //关联表       store_shopping_carts   订单表      store_pro_config   商品配置表
    //             store_products     商品表

    public function getSelectedTotalPrice($param,$cart_id){

        //获取参数
        $telephone = !empty($param['telephone']) ? $param['telephone'] : '';
//        $order_no = !empty($param['order_no']) ? $param['order_no'] : '';
        $bis_id = !empty($param['bis_id']) ? $param['bis_id'] : '';
        $where = "carts.bis_id = ".$bis_id." and carts.telephone = '$telephone' and carts.status = 1 and carts.selected = 1 and carts.id ='$cart_id'";

//        $where = "carts.bis_id = ".$bis_id." and carts.order_no = '$order_no' and carts.status = 1 and carts.selected = 1 and carts.id ='$cart_id'";
        $res = Db::table('store_shopping_carts')->alias('carts')
            ->join('store_pro_config con','carts.pro_id = con.id','LEFT')
            ->join('store_products pro','con.pro_id = pro.id','LEFT')
            ->where($where)
            ->SUM('con.price * carts.count');

        if(!$res){
            $res = 0;
            echo json_encode(array(
                'statuscode'  => 0,
                'message'   =>  '获取选中的价格信息失败'
            ));
            exit;
        }
        return $res;
    }

    //获取选中的价格信息(多用户版)
    public function getSelectedTotalPriceMulti($param){
        //获取参数
        $wx_id = !empty($param['wx_id']) ? $param['wx_id'] : '';
        $where = "carts.wx_id = '$wx_id' and carts.status = 1 and carts.cart_type = 1 and carts.selected = 1";
        $res = Db::table('store_shopping_carts')->alias('carts')
            ->join('store_pro_config con','carts.pro_id = con.id','LEFT')
            ->join('store_products pro','con.pro_id = pro.id','LEFT')
            ->where($where)
            ->SUM('con.price * carts.count');
        if(!$res){
            $res = 0;
        }

        return $res;
    }

    /*
     * 更改全部选中状态(单用户版)
     */

    //接口地址: https://yp.dxshuju.com/app/public/index/shoppingcart/updateAllSelectedStatus
    //条件        bis_id(店铺id)    telephone(用户手机号)      selected_status     当前是否被选中状态（0-否 1-是）
    //传输方式  $_POST
    //关联表       store_shopping_carts   订单表

    public function updateAllSelectedStatus($param){
        //获取参数
        $telephone = !empty($param['telephone']) ? $param['telephone'] : '';
        $bis_id = !empty($param['bis_id']) ? $param['bis_id'] : '';
        $selected_status = !empty($param['selected_status']) ? $param['selected_status'] : '';
        $data['selected'] = $selected_status;
        $where = "bis_id = ".$bis_id." and telephone = '$telephone'";
        $res = Db::table('store_shopping_carts')->where($where)->update($data);
        return $res;
    }

    //更改全部选中状态(多用户版)
    public function updateAllSelectedStatusMulti($param){
        //获取参数
        $wx_id = !empty($param['wx_id']) ? $param['wx_id'] : '';
        $selected_status = !empty($param['selected_status']) ? $param['selected_status'] : '';
        $data['selected'] = $selected_status;
        $where = "wx_id = '$wx_id' and cart_type = 1";
        $res = Db::table('store_shopping_carts')->where($where)->update($data);
        return $res;
    }

    /*
     * 更改单个商品选中数量(普通商城版)
     */

    //接口地址: https://yp.dxshuju.com/app/public/index/shoppingCart/updateSelectedCount
    //条件        cart_id(购物车订单id)        selectedcount(当前的个数)
    //       type 等于 'sub' ($newselectedcount = $selectedcount - 1)   type 不等于 'sub' ($newselectedcount = $selectedcount + 1)
    //获取    newselectedcount（新的个数）
    //传输方式  $_POST
    //关联表       store_shopping_carts   订单表

    public function updateSelectedCount($param){
        //获取参数
        $cart_id = !empty($param['cart_id']) ? $param['cart_id'] : '';
        $selected_status = !empty($param['selected_status']) ? $param['selected_status'] : '';
        $selectedcount = !empty($param['selectedcount']) ? $param['selectedcount'] : '';
        $type = !empty($param['type']) ? $param['type'] : '';

        //设置修改内容
        $con = '';
        if($selected_status && $selected_status == 1){
            $con = ",selected = 1";
        }

        if($type == 'sub'){
            $newselectedcount = $selectedcount - 1;
        }else{
            $newselectedcount = $selectedcount + 1;
        }

        $res = Db::table('store_shopping_carts')->where(['id'=>$cart_id])->update(['count'=>$newselectedcount,'selected'=>1]);

//        $sql = "UPDATE store_shopping_carts SET count = ".$newselectedcount.$con." WHERE id = ".$cart_id;
//        $res = Db::execute($sql);
        return $res;
    }

    //更改单个商品选中数量(拼团商城单独购买版)
    public function updateProCountBySingle($param){
        //获取参数
        $cart_id = !empty($param['cart_id']) ? $param['cart_id'] : '';
        $selectedcount = !empty($param['selectedcount']) ? $param['selectedcount'] : '';
        $type = !empty($param['type']) ? $param['type'] : '';

        if($type == 'sub'){
            $newselectedcount = $selectedcount - 1;
        }else{
            $newselectedcount = $selectedcount + 1;
        }

        $data['count'] = $newselectedcount;
        //更新数量
        Db::table('store_shopping_carts')->where('id = '.$cart_id)->update($data);
        $res = Db::table('store_shopping_carts')->field('count')->where('id = '.$cart_id)->find();

        return $res['count'];
    }



    /*
     * 独立版--点击"去结算"返回选中的购物车内信息
     */

    //接口地址: https://yp.dxshuju.com/app/public/index/shoppingcart/getSelectedCartInfo
    //条件        telephone(用户手机号)     bis_id(店铺id)  cart_id(商品配置id)    mobile(联系电话，根据联系电话获取收货人信息)
    //传输方式  $_POST
    //关联表           store_shopping_carts   订单表  store_pro_config  商品配置表
    //              store_products 商品表     store_pro_images    商品图片表
    //              store_post_mode   快递表  store_logistics_template 快递价格表

    public function getSelectedCartInfo($telephone,$bis_id,$mobile){
        $where = "carts.telephone = '$telephone' and carts.status = 1 and con.status = 1 and img.status = 1 and carts.selected = 1";
        //商品信息
        $pro_res = Db::table('store_shopping_carts')->alias('carts')
            ->field('carts.id as cart_id,con.id as pro_id ,pro.p_name,
            con.con_content1,con_content2,con.price as associator_price,
            carts.count,carts.selected,
            img.thumb,
            pro.rec_rate')
            ->join('store_pro_config con','carts.pro_id = con.id','LEFT')
            ->join('store_products pro','pro.id = con.pro_id','LEFT')
            ->join('store_pro_images img','img.p_id = pro.id','LEFT')
            ->where($where)
            ->order('carts.create_time desc')
            ->select();
        //'http://39.105.68.130:81/app/public/image/'
        foreach ($pro_res as $k=>$v)
        {
            $pro_res[$k]['thumb'] =str_replace('\\','/',$v['thumb']);
        }
        $where1 = "carts.telephone = '$telephone' and carts.status = 1 and con.status = 1 and carts.selected = 1";
        //总金额
        $pro_amount = Db::table('store_shopping_carts')->alias('carts')
            ->join('store_pro_config con','carts.pro_id = con.id','LEFT')
            ->field('con.price,carts.count')
            ->join('store_products pro','con.pro_id = pro.id','LEFT')
            ->where($where1)
            ->SUM('con.price * carts.count');
        //总重量
        $total_weight = Db::table('store_shopping_carts')->alias('carts')
            ->join('store_pro_config con','carts.pro_id = con.id','LEFT')
            ->join('store_products pro','con.pro_id = pro.id','LEFT')
            ->where($where1)
            ->SUM('pro.weight * carts.count');
        //收件人信息
        $address_res = Db::table('store_address')->field('id as a_id,rec_name,mobile,province,city,area,address')
            ->where("mobile = '$mobile' and status = 1 and is_default = 1")
            ->find();
        if(!$address_res){
            $address_array = array();
            $transportType = array();
            $transportInfo = array();
        }else{
            $address_array = [
                'address_id'  => $address_res['a_id'],
                'rec_name'  => $address_res['rec_name'],
                'mobile'  => $address_res['mobile'],
                'address'  => $address_res['province'].$address_res['city'].$address_res['area'].$address_res['address'],
                'province'  => $address_res['province']
            ];
            //快递名称
            $transportType = $this->getTransportType($bis_id,$address_res['province']);
            //快递详细信息
            $transportInfo = $this->getTransportInfo($bis_id,$address_res['province']);
        }
        $res = array(
            'address_info' => $address_array,//收货地址信息
            'pro_info' => $pro_res,//商品信息
            'pro_amount' => $pro_amount,//商品总价
            'total_weight' => $total_weight,//商品重量
            'transportType' => $transportType,//运输类型
            'transportInfo' => $transportInfo//运输信息
        );

        return $res;
    }

    //总站--点击"去结算"返回选中的购物车及运费相关信息

    //接口地址: https://yp.dxshuju.com/app/public/index/shoppingcart/getSelectedCartInfoMulti
    //条件          telephone(手机号)    mobile(联系电话，根据联系电话获取收货人信息)
    //传输方式  $_POST
    //关联表       store_shopping_carts   订单表      store_pro_config   商品配置表
    //             store_products     商品表


    public function getSelectedCartInfoMulti($telephone,$mobile){
        //获取店铺信息
        $where = "carts.telephone = '$telephone' and carts.cart_type = 1 and carts.status = 1 and con.status = 1 and img.status = 1 and carts.selected = 1";
        $bis_res = Db::table('store_shopping_carts')->alias('carts')
            ->field('carts.id as cart_id,carts.bis_id')
            ->join('store_pro_config con','carts.pro_id = con.id','LEFT')
            ->join('store_products pro','con.pro_id = pro.id','LEFT')
            ->join('store_pro_images img','img.p_id = pro.id','LEFT')
            ->where($where)
            ->order('carts.create_time desc')
            ->select();
        $bisArr = array();
        foreach($bis_res as $val){
            if(!in_array($val['bis_id'],$bisArr)){
                array_push($bisArr,$val['bis_id']);
            }
        }

        $index = 0;
        $cart_res = array();
        //地址信息
        $address_info = $this->getAddressInfoMulti($mobile);

        foreach($bisArr as $val){
            //获取店铺信息
            $bis_res = Db::table('store_bis')->field('id as bis_id,bis_name,thumb as bis_thumb')->where('id = '.$val)->find();

            $cart_res[$index]['bis_id'] = $bis_res['bis_id'];
            $cart_res[$index]['bis_name'] = $bis_res['bis_name'];
            $cart_res[$index]['bis_thumb'] = $bis_res['bis_thumb'];
            //获取选中的商品信息
            $cart_res[$index]['pro_info'] = $this->getSelectedCartProInfoMulti($telephone,$val);

            //获取购物车内指定店铺下商品总重量
            $total_weight = $this->getTotalWeightMulti($telephone,$val);    //2
            //获取购物车内指定店铺下商品总价格
            $pro_amount = $this->getProAmountMulti($telephone,$val);    //290
            $cart_res[$index]['pro_amount'] = number_format($pro_amount,2,".","");
            $cart_res[$index]['total_weight'] = $total_weight;
            //商家支持的快递类型
            $transportType = $this->getTransportType($val,$address_info['province']);

            $cart_res[$index]['transport_type'] = $transportType;
            //获取当前店铺的物流种类相关
            $transportInfo = $this->getTransportInfo($val,$address_info['province']);
            $cart_res[$index]['transport_info'] = $transportInfo;
            //获取店铺的运费模式及一口价运费
            $bisTransportTypeRes = $this->getBisTransportAbout($val);
            $bisTransportType = $bisTransportTypeRes['transport_type'];
            $cart_res[$index]['transportType'] = $bisTransportType;

            //如果运费为一口价的话
            if($bisTransportType == 1){
                if(count($transportInfo) == 0){
                    $cart_res[$index]['showFreightView'] = false;
                    $cart_res[$index]['total_amount'] = '0.00';
                    $cart_res[$index]['transport_fee'] = '0.00';
                    $cart_res[$index]['selected_transport_type'] = '';
                    $cart_res[$index]['buttonUsable'] = true;
                }else{
                    $cart_res[$index]['showFreightView'] = true;
                    //首重
                    $first_heavy = $transportInfo[0]['first_heavy'];
                    //超中
                    $continue_heavy = $transportInfo[0]['continue_heavy'];
                    $continue_stage = $transportInfo[0]['continue_stage'];
                   //运费
                    $transport_fee = number_format($first_heavy + ($continue_heavy * (($total_weight - 1) / $continue_stage)),2,".","");
                    $cart_res[$index]['transport_fee'] = $transport_fee;
                   //总价
                    $total_amount = number_format(($pro_amount + $transport_fee),2,".","");
                    $cart_res[$index]['total_amount'] = $total_amount;
                    //付费类型  1-普通类型，即使用模板 2-一口价
                    $cart_res[$index]['selected_transport_type'] = $transportInfo[0]['mode_id'];
                    $cart_res[$index]['buttonUsable'] = false;
                }

            }else{
                $ykj_price = $bisTransportTypeRes['ykj_price'];
                $total_amount = number_format(($pro_amount + $ykj_price),2,".","");
                $cart_res[$index]['showFreightView'] = false;
                $cart_res[$index]['total_amount'] = $total_amount;
                $cart_res[$index]['transport_fee'] = $ykj_price;
                $cart_res[$index]['selected_transport_type'] = '';
                $cart_res[$index]['buttonUsable'] = false;
            }
            $cart_res[$index]['selectedIndex'] = 0;
            $cart_res[$index]['showTransportFeeDetail'] = false;
            $index ++;
        }


        $total_amount = 0;
        foreach($cart_res as $val){
            $single_amount = $val['total_amount'];
            $total_amount = number_format(($total_amount + $single_amount),2,".","");
        }

        return array(
            'address_info' => $address_info,
            'cart_info' => $cart_res,
            'total_amount' => $total_amount
        );
    }

    //获取选中的商品信息
    public function getSelectedCartProInfoMulti($telephone,$bis_id){
        $where = "carts.telephone = '$telephone' and carts.bis_id = $bis_id and carts.cart_type = 1 and carts.status = 1 and con.status = 1 and img.status = 1 and carts.selected = 1";
        $pro_res = Db::table('store_shopping_carts')->alias('carts')->field('carts.id as cart_id,con.id as pro_id,pro.p_name,con.con_content1,con_content2,con.price as associator_price,carts.count,carts.selected,img.thumb,pro.rec_rate')
            ->join('store_pro_config con','carts.pro_id = con.id','LEFT')
            ->join('store_products pro','con.pro_id = pro.id','LEFT')
            ->join('store_pro_images img','img.p_id = pro.id','LEFT')
            ->where($where)
            ->order('carts.create_time desc')
            ->select();
        return $pro_res;
    }

    //获取用户选中的地址
    public function getAddressInfoMulti($mobile){
        $address_res = Db::table('store_address')->field('id as a_id,rec_name,mobile,province,city,area,address')
            ->where("mobile = '$mobile' and status = 1 and is_default = 1")
            ->find();
        return $address_res;
    }

    //获取购物车内指定店铺下商品总重量
    public function getTotalWeightMulti($telephone,$bis_id){
        $where1 = "carts.telephone = '$telephone' and carts.bis_id = $bis_id and carts.status = 1 and con.status = 1 and carts.selected = 1";
        $total_weight = Db::table('store_shopping_carts')->alias('carts')
            ->join('store_pro_config con','carts.pro_id = con.id','LEFT')
            ->join('store_products pro','con.pro_id = pro.id','LEFT')
            ->where($where1)
            ->SUM('pro.weight * carts.count');
        return $total_weight;
    }

    //获取购物车内指定店铺下商品总价格
    public function getProAmountMulti($telephone,$bis_id){
        $where1 = "carts.telephone = '$telephone' and carts.bis_id = $bis_id and carts.cart_type = 1 and carts.status = 1 and con.status = 1 and carts.selected = 1";
        $pro_amount = Db::table('store_shopping_carts')->alias('carts')
            ->join('store_pro_config con','carts.pro_id = con.id','LEFT')
            ->join('store_products pro','con.pro_id = pro.id','LEFT')
            ->where($where1)
            ->SUM('con.price * carts.count');
        return $pro_amount;
    }

    //获取店铺的运费模式及一口价运费
    public function getBisTransportAbout($bis_id){
        $res = Db::table('store_bis')->field('transport_type,ykj_price')->where('id = '.$bis_id)->find();
        return $res;
    }

    //单独购买(拼团版)
    public function getSelectedCartInfoBySingle($cart_id,$openid,$bis_id,$from){
        $where = "carts.id = '$cart_id' and con.status = 1 and img.status = 1";
        $where1 = "carts.id = '$cart_id' and carts.status = 1 and con.status = 1";
        if($from == 'single'){
            $pro_res = Db::table('store_shopping_carts')->alias('carts')->field('carts.id as cart_id,con.id as pro_id,pro.p_name,con.con_content1,con_content2,con.price as associator_price,carts.count,carts.selected,img.thumb,pro.rec_rate')
                ->join('store_pro_config con','carts.pro_id = con.id','LEFT')
                ->join('store_products pro','con.pro_id = pro.id','LEFT')
                ->join('store_pro_images img','img.p_id = pro.id','LEFT')
                ->where($where)
                ->find();

            $pro_amount = Db::table('store_shopping_carts')->alias('carts')
                ->join('store_pro_config con','carts.pro_id = con.id','LEFT')
                ->join('store_products pro','con.pro_id = pro.id','LEFT')
                ->where($where1)
                ->SUM('con.price * carts.count');
        }else{
            $pro_res = Db::table('store_shopping_carts')->alias('carts')->field('carts.id as cart_id,con.id as pro_id,pro.p_name,con.con_content1,con_content2,con.group_price as associator_price,carts.count,carts.selected,img.thumb,pro.rec_rate')
                ->join('store_pro_config con','carts.pro_id = con.id','LEFT')
                ->join('store_products pro','con.pro_id = pro.id','LEFT')
                ->join('store_pro_images img','img.p_id = pro.id','LEFT')
                ->where($where)
                ->find();

            $pro_amount = Db::table('store_shopping_carts')->alias('carts')
                ->join('store_pro_config con','carts.pro_id = con.id','LEFT')
                ->join('store_products pro','con.pro_id = pro.id','LEFT')
                ->where($where1)
                ->SUM('con.group_price * carts.count');
        }

        $total_weight = Db::table('store_shopping_carts')->alias('carts')
            ->join('store_pro_config con','carts.pro_id = con.id','LEFT')
            ->join('store_products pro','con.pro_id = pro.id','LEFT')
            ->where($where1)
            ->SUM('pro.weight * carts.count');

        $address_res = Db::table('store_address')->field('id as a_id,rec_name,mobile,province,city,area,address,idno')
            ->where("mem_id = '$openid' and status = 1 and is_default = 1")
            ->find();


        if(!$address_res){
            $address_array = array();
            $transportType = array();
            $transportInfo = array();
        }else{
            $address_array = [
                'address_id'  => $address_res['a_id'],
                'rec_name'  => $address_res['rec_name'],
                'mobile'  => $address_res['mobile'],
                'idno'  => $address_res['idno'],
                'address'  => $address_res['province'].$address_res['city'].$address_res['area'].$address_res['address'],
                'province'  => $address_res['province']
            ];
            $transportType = $this->getTransportType($bis_id,$address_res['province']);
            $transportInfo = $this->getTransportInfo($bis_id,$address_res['province']);
        }

        return array(
            'address_info' => $address_array,
            'pro_info' => $pro_res,
            'pro_amount' => $pro_amount,
            'total_weight' => $total_weight,
            'transportType' => $transportType,
            'transportInfo' => $transportInfo
        );

    }

    //获取购物车内选中的积分商品
    public function getJfProSelectedInfo($cart_id,$openid,$bis_id){
        $where = "carts.id = '$cart_id' and con.status = 1 and img.status = 1";
        $where1 = "carts.id = '$cart_id' and carts.status = 1 and con.status = 1";

        $pro_res = Db::table('store_shopping_carts')->alias('carts')->field('carts.id as cart_id,con.id as pro_id,pro.p_name,pro.weight,con.con_content1,con_content2,con.price as associator_price,con.ex_jifen,carts.count,carts.selected,img.thumb,pro.rec_rate')
            ->join('store_pro_config con','carts.pro_id = con.id','LEFT')
            ->join('store_products pro','con.pro_id = pro.id','LEFT')
            ->join('store_pro_images img','img.p_id = pro.id','LEFT')
            ->where($where)
            ->find();
        if($pro_res['associator_price'] < '0.01'){
            $pro_res['jf_price'] = $pro_res['ex_jifen'].'积分';
        }else{
            $pro_res['jf_price'] = $pro_res['ex_jifen'].'积分'.' + '.$pro_res['associator_price'].'元';
        }


        $jifen_amount = Db::table('store_shopping_carts')->alias('carts')
            ->join('store_pro_config con','carts.pro_id = con.id','LEFT')
            ->join('store_products pro','con.pro_id = pro.id','LEFT')
            ->where($where1)
            ->SUM('con.ex_jifen * carts.count');

        $price_amount = Db::table('store_shopping_carts')->alias('carts')
            ->join('store_pro_config con','carts.pro_id = con.id','LEFT')
            ->join('store_products pro','con.pro_id = pro.id','LEFT')
            ->where($where1)
            ->SUM('con.price * carts.count');

        $total_weight = Db::table('store_shopping_carts')->alias('carts')
            ->join('store_pro_config con','carts.pro_id = con.id','LEFT')
            ->join('store_products pro','con.pro_id = pro.id','LEFT')
            ->where($where1)
            ->SUM('pro.weight * carts.count');

        $address_res = Db::table('store_address')->field('id as a_id,rec_name,mobile,province,city,area,address,idno')
            ->where("mem_id = '$openid' and status = 1 and is_default = 1")
            ->find();


        if(!$address_res){
            $address_array = array();
            $transportType = array();
            $transportInfo = array();
        }else{
            $address_array = [
                'address_id'  => $address_res['a_id'],
                'rec_name'  => $address_res['rec_name'],
                'mobile'  => $address_res['mobile'],
                'idno'  => $address_res['idno'],
                'address'  => $address_res['province'].$address_res['city'].$address_res['area'].$address_res['address'],
                'province'  => $address_res['province']
            ];
            $transportType = $this->getTransportType($bis_id,$address_res['province']);
            $transportInfo = $this->getTransportInfo($bis_id,$address_res['province']);
        }

        return array(
            'address_info' => $address_array,
            'pro_info' => $pro_res,
            'jifen_amount' => $jifen_amount,
            'price_amount' => $price_amount,
            'total_weight' => $total_weight,
            'transportType' => $transportType,
            'transportInfo' => $transportInfo
        );

    }
    /*
     * 判断购物车内是否存在勾选的产品
     */

    //接口地址: https://yp.dxshuju.com/app/public/index/shoppingcart/checkSelectedPro
    //条件         wx_id(用户id)
    //传输方式  $_POST
    //关联表       store_shopping_carts   订单表


    public function checkSelectedPro($telephone){
        $where = "telephone = '$telephone' and selected = 1 and status = 1";
        $res = Db::table('store_shopping_carts')->where($where)->count();
        if($res > 0){
            return 1;
        }else{
            return 0;
        }
    }

    /*
     * 获取当前店铺的物流种类
     */

    //接口地址: https://yp.dxshuju.com/app/public/index/shoppingcart/getTransportType
    //条件         bis_id(店铺表)    province(省份)
    //传输方式  $_POST
    //关联表       store_logistics_template   快递价格表      store_post_mode   快递类型表

    //商家支持的快递类型
    public function getTransportType($bis_id,$province){
        $where = "tem.bis_id = '$bis_id' and tem.province like '%$province%' and tem.status = 1";
        $res = Db::table('store_logistics_template')->alias('tem')->field('tem.id as tem_id,mode.post_mode')
            ->join('store_post_mode mode','tem.mode_id = mode.id','LEFT')
            ->where($where)
            ->select();
        $tt_res = array();
        foreach($res as $val){
            array_push($tt_res,$val['post_mode']);
        }
        return $tt_res;
    }


    //获取当前店铺的物流种类相关

    public function getTransportInfo($bis_id,$province){
        $where = "tem.bis_id = '$bis_id' and tem.province like '%$province%' and tem.status = 1";
        $res = Db::table('store_logistics_template')->alias('tem')->field('tem.id as tem_id,mode.post_mode,tem.first_heavy,tem.continue_heavy,mode.id as mode_id,mode.continue_stage')
            ->join('store_post_mode mode','tem.mode_id = mode.id','LEFT')
            ->where($where)
            ->select();

        return $res;
    }

    /*
     * 更改购物车指定店铺内商品选中状态
     * */

    //接口地址: https://yp.dxshuju.com/app/public/index/shoppingcart/updateBisStatus
    //条件         telephone(手机号)   bis_id  店铺id     bis_status  店铺状态
    //传输方式  $_POST
    //关联表       store_shopping_carts   订单表

    public function updateBisStatus($telephone,$bis_id,$bis_status){
        $where = "telephone = '$telephone' and bis_id = $bis_id";
        if($bis_status == 1){
            $data['selected'] = 0;
        }else{
            $data['selected'] = 1;
        }
        $res = Db::table('store_shopping_carts')->where($where)->update($data);
        return $res;
    }
}