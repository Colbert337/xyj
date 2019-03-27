<?php
header("Content-type: text/html; charset=utf-8");
ini_set('display_errors', '0');
error_reporting(E_ALL ^ E_NOTICE);
if(date_default_timezone_get() != "1Asia/Shanghai") date_default_timezone_set("Asia/Shanghai");
defined('BASEPATH') OR exit('No direct script access allowed');
require_once dirname(__FILE__).'/WxPay.php';
require_once dirname(__FILE__).'/WxApi.php';
class WxXcx extends CI_Controller {
	public $appid;
	//	public $mch_id;
	//	public $key;

	public function __construct()
	{
		parent::__construct();
		$this->appid = 'wxa5d85fdc620e20bb';
		//		$this->mch_id = '1415605302';
		//		$this->key = 'fc68fe4ec8e4e338e391177a4b723b63';
		$this->load->library('functions');
	}
	//付款反馈
	public function pay_back(){
		//获取返回的xml
		$testxml = file_get_contents("php://input");
		//将xml转化为json格式
		$jsonxml = json_encode(simplexml_load_string($testxml, 'SimpleXMLElement', LIBXML_NOCDATA));
		//转成数组
		$result = json_decode($jsonxml, true);
		if($result){
			//如果成功返回了
			if($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS'){
				echo 'success';
				//$this->functions->debuglog('用户订单号：'.$result['out_trade_no']);
				$post_arr=array(
					             'QueryType'=>'get_orderinfo',
					             'Params'=>'{"orderno":"'.$result['out_trade_no'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->object_to_array($this->functions->curlPostArray($url, $post_arr));
					             $res = $res[0];
					             //发送模板消息
					             $prepayid = $this->functions->getRedisByKey('prepayid'.$result['out_trade_no']);
					             //$this->functions->debuglog('prepay'.$prepayid);
					             if($prepayid){
					                 $wxtemp = new WxApi();
					                 $access_token = $this->functions->getRedisByKey('access_token');
					                 if(!$access_token){
					                     $access_token = $wxtemp->get_access_token();
					                     $this->functions->setRedisKeyVal('access_token',$access_token,7200);
					                 }
					               
					                 $tem=$wxtemp->rent_tempsend($access_token,$res['USER_ID'], $prepayid, $result['out_trade_no'],$res['GOODS_NAME'],$res['DEPOSIT_AMOUNT'],$res['LEASEBRANCHGUID']);
					              
					             }
					             //发送模板消息结束
					             //如果订单是刚付款成功的
					             if($res['STATUS'] == '4'){
					             	$create_time=date("Y-m-d H:i:s");
					             	if($res['LOCKCODE'] != '' && $res['LOCKCODE'] != ' '){
					             		$status = '7';
					             	}else{
					             		$status = '0';
					             	}
					             	$post_arr=array(
					             'QueryType'=>'update_order',
					             'Params'=>'{"alipricetime":"'.$create_time.'","status":"'.$status.'","order_no":"'.$result['out_trade_no'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $updata=$this->object_to_array($this->functions->curlPostArray($url, $post_arr));
					             if($updata['state'] == '1'){
					             	$this->functions->debuglog($result['out_trade_no'].'用户付款成功状态已更改');
					             }else{
					             	$this->functions->debuglog($result['out_trade_no'].'用户付款成功状态更改失败'.json_encode($updata,JSON_UNESCAPED_UNICODE));
					             }
					             if($res['LOCKCODE'] != '' && $res['LOCKCODE'] != ' '){
					             	//记录用户使用情况
					             	$isrent = $this->functions->getRedisByKey('AliRent'.$res['USER_ID']);
					             	if(!$isrent){
					             		$this->functions->setRedisKeyVal('AliRent'.$res['USER_ID'],'hasrent',86400);
					             		$isrent = $this->functions->getRedisByKey('AliRent'.$res['USER_ID']);
					             		//$this->functions->debuglog($datas['user_id'].'用户已使用共享轮椅,记录为'.$isrent);
					             	}
					             	if(substr($res['LOCKCODE'],0,5) == '10000'){
					             		$this->qiqi_unlock($res['OUT_ORDER_NO'],$res['LOCKCODE'],$create_time,$res['USER_ID'],$result['out_trade_no']);
					             	}else{
					             		$this->new_unlock($res['OUT_ORDER_NO'],$res['LOCKCODE'],$create_time,$res['USER_ID'],$result['out_trade_no']);
					             	}
					             	
					             }
					             }
					             //进行改变订单状态等操作。。。。
			}
		}
	}
	//奇奇开锁
	public function qiqi_unlock($orderno,$lockCode,$createtime,$userid,$out_order_no){
		$this->functions->debuglog($out_order_no.'发起开锁');
		$url="http://java.xingoxing.com/api/rent";//"http://server.571cn.com:12580/sg-rest-api/api/rent";
		//$url="http://server.571cn.com:12580/api/rent";
		$data=array(
		  'extOrderNo'=>$orderno,
		  'lockCode'=>$lockCode,//'100000004492',
		  'createTime'=>$createtime
		);
		foreach ($data as $key=>$value){
			$arr[$key] = $key;
		}
		sort($arr); //字典排序的作用就是防止因为参数顺序不一致而导致下面拼接加密不同
		// 2. 将Key和Value拼接
		$str = "5ADC9AD1224C38886394C3FB45BD77FC";
		foreach ($arr as $k => $v) {
			$str = $str.$arr[$k].$data[$v];
		}
		$data['sign']=md5($str);
		$data['channel']='5ADC9AD1224C38886394C3FB45BD77FC';
		$res = $this->object_to_array(json_decode(json_decode($this->curlpost($url, $data))));
		if($res['code'] == '000000'){
			$data=array(
			'state'=>'1',
			'msg'=>'开锁成功'
			);
			echo json_encode($data,JSON_UNESCAPED_UNICODE);
			$this->functions->debuglog($orderno.'开锁成功等待反馈');
			//			M('rm_lockorder')->where($where)->save(array('status'=>'1'));
		}else{
			//			M('rm_lockorder')->where($where)->save(array('status'=>'2'));
			$data=array(
			'state'=>'2',
			'msg'=>'开锁失败,请重试'
			);
			$this->functions->debuglog('开锁结果：'.json_encode($res,JSON_UNESCAPED_UNICODE));
			$this->functions->delRedisByKey('AliRent'.$userid);
			//echo json_encode($data,JSON_UNESCAPED_UNICODE);
			$post_arr=array(
					             'QueryType'=>'get_orderinfo',
					             'Params'=>'{"id":"'.$orderno.'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->object_to_array($this->functions->curlPostArray($url, $post_arr));
		                         if(!$res['ID']){
					             	$post_arr=array(
					             'QueryType'=>'get_orderinfo',
					             'Params'=>'{"out_order_no":"'.$orderno.'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->object_to_array($this->functions->curlPostArray($url, $post_arr));
					             $res = $res[0];
					             }
					             $order_no=$res['ORDER_NO'];
					             $dmoney = $res['DEPOSIT_AMOUNT'];
					             $post_arr=array(
					             'QueryType'=>'update_order',
					             'Params'=>'{"money":"0","isclerk":"1","branchguid":"'.$res['LEASEBRANCHGUID'].'","goodsid":"'.$res['RENT_INFO'].'","orgguid":"88","status":"2","order_no":"'.$order_no.'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					             $this->functions->debuglog($order_no.'开锁失败');
					             $this->functions->debuglog('订单:'.$order_no.'发起退款');
					             $wxpay = new WxPay();
					             $wxpay->pay_refund($order_no,$dmoney,$dmoney);

		}

	}
    //云锁开锁
	public function new_unlock($orderno,$lockCode,$createtime,$userid,$out_order_no){
		$this->functions->debuglog($out_order_no.'发起开锁');
		$lockurl = 'http://140.143.129.247:8081/api/rent?extOrderNo='.$orderno.'&lockCode='.$lockCode;
		$res=$this->object_to_array(json_decode(json_decode($this->curlpost($lockurl, ''))));
		$this->functions->debuglog('云锁开锁结果:'.json_encode($res,JSON_UNESCAPED_UNICODE));
		if($res['code'] == '000000'){
			$data=array(
			'state'=>'1',
			'msg'=>'开锁成功'
			);
			echo json_encode($data,JSON_UNESCAPED_UNICODE);
			$this->functions->debuglog($out_order_no.'开锁成功等待反馈');
			//			M('rm_lockorder')->where($where)->save(array('status'=>'1'));
		}else{
			$msg = $res['msg'];
			//			M('rm_lockorder')->where($where)->save(array('status'=>'2'));
			$data=array(
			'state'=>'2',
			'msg'=>'开锁失败,请重试'
			);
			echo json_encode($data,JSON_UNESCAPED_UNICODE);
			$this->functions->debuglog($out_order_no.'开锁失败'.$msg);
			$post_arr=array(
					             'QueryType'=>'get_orderinfo',
					             'Params'=>'{"id":"'.$orderno.'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->object_to_array($this->functions->curlPostArray($url, $post_arr));
		                         if(!$res['ID']){
					             	$post_arr=array(
					             'QueryType'=>'get_orderinfo',
					             'Params'=>'{"out_order_no":"'.$orderno.'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->object_to_array($this->functions->curlPostArray($url, $post_arr));
					             $res = $res[0];
					             }
					             $order_no=$res['ORDER_NO'];
					             $dmoney = $res['DEPOSIT_AMOUNT'];
					             $post_arr=array(
					             'QueryType'=>'update_order',
					             'Params'=>'{"money":"0","isclerk":"1","branchguid":"'.$res['LEASEBRANCHGUID'].'","goodsid":"'.$res['RENT_INFO'].'","orgguid":"88","status":"2","order_no":"'.$order_no.'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					             $this->functions->debuglog($order_no.'开锁失败');
					             $this->functions->debuglog('订单:'.$order_no.'发起退款');
					             $wxpay = new WxPay();
					             $wxpay->pay_refund($order_no,$dmoney,$dmoney);
		}

	}
	//附近门店距离计算
	public function near_store(){
		$post_arr=array(
					             'QueryType'=>'getBranchInfo',
					             'Params'=>'{"businessesId":"'.$_POST['businessesId'].'","goodsid":"'.$_POST['goodsid'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					             echo json_encode($res,JSON_UNESCAPED_UNICODE);
	}
	//地图附近门店
	public function nearstore(){
		$_POST['lat']='36.072517005321';
		$_POST['lng']='120.37618412944';
		$post_arr=array(
					             'QueryType'=>'getBranch',
					             'Params'=>'{"lat":"'.$_POST['lat'].'","lng":"'.$_POST['lng'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=curlPostArray($url, $post_arr);
					             echo"<pre>";
					             var_dump($res);
					             //echo json_encode($res,JSON_UNESCAPED_UNICODE);
	}
	//主单预计归还时间
	public function firstreturn_time(){
		//		if($_POST['rentunit'] == 'HOUR_YUAN'){
		//			$isrent = getRedisByKey('AliRent'.$_POST['userid']);
		//			$this->functions->debuglog('AliRent'.$_POST['userid'].'用户的借用记录为:'.$isrent);
		//            if($isrent && $_POST['userid']!= '2088912068111591' && $_POST['userid']!= '2088902145150773'){
		//                if($isrent == 'hasrent'){
		//					echo json_encode($isrent);die;
		//				}
		//            }
		//		}
		$create_time=date("Y-m-d H:i:s");
		$time=(int)$_POST['borrow_cycle'];
		$timestart=$create_time;
		if($time == 31){
			$returntime = date("Y-m-d H:i:s",strtotime("$timestart + 1 month"));
		}else{
			$returntime = date("Y-m-d H:i:s",strtotime("$timestart + $time day"));
		}
		$data=array(
		   'create_time' => $create_time,
		   'return_time' => $returntime
		);
		echo json_encode($data);
	}
	//计算预计归还时间  -2017-8-10-
	public function return_time(){
		if(!$_POST['create_time']){
			$create_time=date("Y-m-d H:i:s");
		}else{
			$create_time=$_POST['create_time'];
		}
		$time=(int)$_POST['borrow_cycle'];
		$timestart=$create_time;
		if($time == 31){
			$returntime = date("Y-m-d H:i:s",strtotime("$timestart + 1 month"));
		}else{
			$returntime = date("Y-m-d H:i:s",strtotime("$timestart + $time day"));
		}
		$post_arr=array(
					             'QueryType'=>'get_orderinfo',
					             'Params'=>'{"id":"'.$_POST['parentguid'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					             $res=$this->object_to_array($res);
					             //					             if(strtotime($res['ESTIMATERETURNDATETIME'])<strtotime($returntime)){
					             //					             	$data = array(
					             //					             	    'data'=>1,
					             //					             	    'time'=>$res['ESTIMATERETURNDATETIME']
					             //					             	);
					             //					             	echo json_encode($data);
					             //					             }else{
					             $data = array(
					             	    'data'=>2,
					             	    'time'=>$returntime
					             );
					             //$this->functions->debuglog('returntime:'.json_encode($data));
					             echo json_encode($data);
					             //					             }

	}
	//用户提货
	public function pick_up(){
		$time=(int)$_POST['borrow_cycle'];
		$timestart=date("Y-m-d H:i:s");
		if($time == 31){
			$expiry_time = date("Y-m-d H:i:s",strtotime("$timestart + $time day"));
		}else{
			$expiry_time = date("Y-m-d H:i:s",strtotime("$timestart + $time day"));
		}
		$post_arr=array(
					'QueryType'=>'update_order',
					'Params'=>'{"branchguid":"'.$_POST['branchguid'].'","goodsid":"'.$_POST['goodsid'].'","takegoodstime":"'.$timestart.'","returntime":"'.$expiry_time.'","iswarning":"0","numberno":"'.$_POST['numberno'].'","status":"1","order_no":"'.$_POST['order_no'].'"}',
					'UserGuid'=>'ODh8QHJvbWVucw--'
					);
					//$this->functions->debuglog('修改订单的信息：'.json_encode($post_arr));
					$url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					$res=$this->functions->curlPostArray($url, $post_arr);
					echo json_encode($res);
	}
	//续租
	public function rent_continue(){
		$time=(int)$_POST['borrow_cycle'];
		$timestart=$_POST['create_time'];
		if($time == 31){
			$returntime = date("Y-m-d H:i:s",strtotime("$timestart + 1 month"));
		}else{
			$returntime = date("Y-m-d H:i:s",strtotime("$timestart + $time day"));
		}
		$post_arr=array(
					'QueryType'=>'update_order',
					'Params'=>'{"money":"'.$_POST['money'].'","branchguid":"'.$_POST['branchguid'].'","goodsid":"'.$_POST['goodsid'].'","returntime":"'.$returntime.'","iswarning":"0","status":"1","order_no":"'.$_POST['order_no'].'"}',
					'UserGuid'=>'ODh8QHJvbWVucw--'
					);
					$url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					$res=$this->functions->curlPostArray($url, $post_arr);
					echo json_encode($res,JSON_UNESCAPED_UNICODE);
	}
	//商家确认订单
	public function sure_rent(){
		$post_arr=array(
					'QueryType'=>'update_order',
					'Params'=>'{"clerkno":"'.$_POST['clerk'].'","isclerk":"1","status":"1","order_no":"'.$_POST['order_no'].'"}',
					'UserGuid'=>'ODh8QHJvbWVucw--'
					);
					$url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					$res=$this->functions->curlPostArray($url, $post_arr);
					echo json_encode($res,JSON_UNESCAPED_UNICODE);
	}
	//押金模式创建订单新
	public function xcx_create_new(){
		$this->functions->debuglog($_POST['phone'].'用户创建订单,订单号:'.$_POST['out_order_no']);
		$cons=$this->object_to_array(json_decode($_POST['consumables']));
		$consumables=array();
		$consumablesamount=0;
		for($i=0;$i<count($cons);$i++){
			$consumables[$i]['consumablesguid']=$cons[$i]['GUID'];
			$consumables[$i]['goodsprice']=$cons[$i]['PRODUCT_PRICE'];
			$consumables[$i]['price']=$cons[$i]['PRODUCT_PRICE'];
			$consumables[$i]['num']=$cons[$i]['num'];
			$consumables[$i]['tamount']=(int)$cons[$i]['PRODUCT_PRICE']*(int)$cons[$i]['num'];
			$consumablesamount=$consumablesamount+$consumables[$i]['tamount'];
		}
		if(!$_POST['create_time']){
			$create_time=date("Y-m-d H:i:s");
		}else{
			$create_time=$_POST['create_time'];
		}
		$time=(int)$_POST['borrow_cycle'];
		$timestart=$create_time;
		if($time == 31){
			$returntime = date("Y-m-d H:i:s",strtotime("$timestart + 1 month"));
		}else{
			$returntime = date("Y-m-d H:i:s",strtotime("$timestart + $time day"));
		}
		$status = '4';
		//		if($_POST['serialno'] == '' && $_POST['lockCode'] == ''){
		//			$status = '0';
		//		}else{
		//			$status = '9';
		//		}
		$datas= array (
          'id'=>uniqid(),
		  'create_time'=>$create_time,
		  'return_time'=>$returntime,
          'token'=>'',//$_info['token'],
          'out_order_no'=>$_POST['out_order_no'],
          'order_no'=>$_POST['out_order_no'],
          'user_id'=>$_POST['user_id'],
          'admit_state'=>'Y',
          'name'=>'BK',
          'product_code'=>$_POST['product_code'],
          'goods_name'=>$_POST['goods_name'],
          'rent_info'=>$_POST['rent_info'],
          'rent_unit'=>$_POST['rent_unit'],
          'rent_amount'=>$_POST['rent_amount'],
          'deposit_amount'=>$_POST['deposit_amount'],
          'deposit_state'=>$_POST['deposit_state'],
          'borrow_cycle'=>$_POST['borrow_cycle'],
          'borrow_cycle_unit'=>$_POST['borrow_cycle_unit'],
          'borrow_shop_name'=>$_POST['borrow_shop_name'],
		  'leaseprice'=>$_POST['leaseprice'],
          'leasebranchguid'=>$_POST['leasebranchguid'],
          'status'=>$status, 
          'orgguid'=>$_POST['orgguid'],
		  'phone'=>$_POST['phone'],
		  'user_name'=>'',//S('user_name'.$result->$responseNode->user_id),
		  'consumables'=>$consumables,
		  'consumablesamount'=>$consumablesamount,
		  'usercouponid'=>$_POST['usercouponid'],
		  'numberno'=>$_POST['serialno'],
		  'lockinfo'=>$_POST['lockinfo'],
		  'lockcode'=>$_POST['lockCode'],
		  'transportguid'=>$_POST['transportguid'],
		  'transportprice'=>$_POST['transportprice'],
		  'useraddress'=>$_POST['useraddress'],
		  'ISDEPOSIT'=>'1'
		  );
		  $post_arr=array(
         'QueryType'=>'sub_order',
		 'Params'=>json_encode($datas,true),
		 'UserGuid'=>'ODh8QHJvbWVucw--'
		 );
		 $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
		 $res=$this->functions->curlPostArray($url, $post_arr);
		 echo json_encode($res,JSON_UNESCAPED_UNICODE);
		 if($res->state != '1'){
		 	$this->functions->debuglog(json_encode($datas));
            $this->functions->debuglog('1111'.json_encode($res,JSON_UNESCAPED_UNICODE));
		 	$this->functions->debuglog($order_no.'微信创建订单失败');
		    $this->functions->debuglog('订单:'.$order_no.'发起退款');
			$wxpay = new WxPay();
			$wxpay->pay_refund($datas['out_order_no'],$datas['deposit_amount'],$datas['deposit_amount']);
		 }
		 //		 if($res->state != '1'){
		 //		 	$wxpay = new WxPay();
		 //		 	$wxpay->pay_refund($datas['out_order_no'],$datas['deposit_amount'],$datas['deposit_amount']);
		 //		 }else{
		 //		 	if($_POST['lockCode'] != ''){
		 //		 		$isrent = $this->functions->getRedisByKey('AliRent'.$datas['user_id']);
		 //		 		if(!$isrent){
		 //		 			$this->functions->setRedisKeyVal('AliRent'.$datas['user_id'],'hasrent',86400);
		 //		 			$isrent = $this->functions->getRedisByKey('AliRent'.$datas['user_id']);
		 //		 			//$this->functions->debuglog($datas['user_id'].'用户已使用共享轮椅,记录为'.$isrent);
		 //		 		}
		 //		 		$this->qiqi_unlock($datas['id'],$_POST['lockCode'],$create_time,$datas['user_id'],$_POST['out_order_no']);
		 //		 	}else{
		 //		 		echo json_encode($res,JSON_UNESCAPED_UNICODE);
		 //		 	}
		 //		 }

	}
	//押金模式创建订单
	public function xcx_create(){
		$this->functions->debuglog($_POST['phone'].'用户创建订单,订单号:'.$_POST['out_order_no']);
		$cons=$this->object_to_array(json_decode($_POST['consumables']));
		$consumables=array();
		$consumablesamount=0;
		for($i=0;$i<count($cons);$i++){
			$consumables[$i]['consumablesguid']=$cons[$i]['GUID'];
			$consumables[$i]['goodsprice']=$cons[$i]['PRODUCT_PRICE'];
			$consumables[$i]['price']=$cons[$i]['PRODUCT_PRICE'];
			$consumables[$i]['num']=$cons[$i]['num'];
			$consumables[$i]['tamount']=(int)$cons[$i]['PRODUCT_PRICE']*(int)$cons[$i]['num'];
			$consumablesamount=$consumablesamount+$consumables[$i]['tamount'];
		}
		if(!$_POST['create_time']){
			$create_time=date("Y-m-d H:i:s");
		}else{
			$create_time=$_POST['create_time'];
		}
		$time=(int)$_POST['borrow_cycle'];
		$timestart=$create_time;
		if($time == 31){
			$returntime = date("Y-m-d H:i:s",strtotime("$timestart + 1 month"));
		}else{
			$returntime = date("Y-m-d H:i:s",strtotime("$timestart + $time day"));
		}
		if($_POST['serialno'] == '' && $_POST['lockCode'] == ''){
			$status = '0';
		}else{
			$status = '7';
		}
		$datas= array (
          'id'=>uniqid(),
		  'create_time'=>$create_time,
		  'return_time'=>$returntime,
          'token'=>'',//$_info['token'],
          'out_order_no'=>$_POST['out_order_no'],
          'order_no'=>$_POST['out_order_no'],
          'user_id'=>$_POST['user_id'],
          'admit_state'=>'Y',
          'name'=>'BK',
          'product_code'=>$_POST['product_code'],
          'goods_name'=>$_POST['goods_name'],
          'rent_info'=>$_POST['rent_info'],
          'rent_unit'=>$_POST['rent_unit'],
          'rent_amount'=>$_POST['rent_amount'],
          'deposit_amount'=>$_POST['deposit_amount'],
          'deposit_state'=>$_POST['deposit_state'],
          'borrow_cycle'=>$_POST['borrow_cycle'],
          'borrow_cycle_unit'=>$_POST['borrow_cycle_unit'],
          'borrow_shop_name'=>$_POST['borrow_shop_name'],
		  'leaseprice'=>$_POST['leaseprice'],
          'leasebranchguid'=>$_POST['leasebranchguid'],
          'status'=>$status, 
          'orgguid'=>$_POST['orgguid'],
		  'phone'=>$_POST['phone'],
		  'user_name'=>'',//S('user_name'.$result->$responseNode->user_id),
		  'consumables'=>$consumables,
		  'consumablesamount'=>$consumablesamount,
		  'usercouponid'=>$_POST['usercouponid'],
		  'numberno'=>$_POST['serialno'],
		  'lockinfo'=>$_POST['lockinfo'],
		  'lockcode'=>$_POST['lockCode'],
		  'transportguid'=>$_POST['transportguid'],
		  'transportprice'=>$_POST['transportprice'],
		  'useraddress'=>$_POST['useraddress'],
		  'ISDEPOSIT'=>'1'
		  );
		  $post_arr=array(
         'QueryType'=>'sub_order',
		 'Params'=>json_encode($datas,true),
		 'UserGuid'=>'ODh8QHJvbWVucw--'
		 );
		 $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
		 $res=$this->functions->curlPostArray($url, $post_arr);
		 if($res->state != '1'){
		 	//$this->transpay($datas['out_order_no'],$datas['user_id'],$datas['deposit_amount'],'下单失败押金退款');
		 	$wxpay = new WxPay();
		 	$wxpay->pay_refund($datas['out_order_no'],$datas['deposit_amount'],$datas['deposit_amount']);
		 }else{
		 	if($_POST['lockCode'] != ''){
		 		$isrent = $this->functions->getRedisByKey('AliRent'.$datas['user_id']);
		 		if(!$isrent){
		 			$this->functions->setRedisKeyVal('AliRent'.$datas['user_id'],'hasrent',86400);
		 			$isrent = $this->functions->getRedisByKey('AliRent'.$datas['user_id']);
		 			//$this->functions->debuglog($datas['user_id'].'用户已使用共享轮椅,记录为'.$isrent);
		 		}
		 		$this->qiqi_unlock($datas['out_order_no'],$_POST['lockCode'],$create_time,$datas['user_id'],$_POST['out_order_no']);
		 	}else{
		 		echo json_encode($res,JSON_UNESCAPED_UNICODE);
		 	}
		 }

	}
	public function failcreate(){
		$wxpay = new WxPay();
		$wxpay->pay_refund($_POST['out_order_no'],$_POST['dmoney'],$_POST['dmoney']);
	}
	//商家版完成订单
	public function myrent_complete(){
	    $rtime=date("Y-m-d H:i:s");
	    $return_rent = (Float)$_POST['dmoney']-(Float)$_POST['rent'];
	    $wxpay = new WxPay();
	    $res_pay=$wxpay->pay_refund($_POST['order_no'],(String)$_POST['dmoney'],(String)$return_rent);
	    if($res_pay['result_code'] == 'SUCCESS'){
	        $post_arr=array(
	            'QueryType'=>'update_order',
	            'Params'=>'{"goodsid":"'.$_POST['goodsid'].'","branchguid":"'.$_POST['branchguid'].'","money":"'.$_POST['rent'].'","returngoodstime":"'.$rtime.'","isclerk":"1","iswarning":"2","orgguid":"88","status":"3","order_no":"'.$_POST['order_no'].'"}',
	            'UserGuid'=>'ODh8QHJvbWVucw--'
	        );
	        $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
	        $res=$this->functions->curlPostArray($url, $post_arr);
	    }else{
	        $res = array(
	            'state'=>'2',
	            'msg'=>'微信退款失败'
	        );
	    }
	    echo json_encode($res,JSON_UNESCAPED_UNICODE);
	}
	//撤销订单
	public function alirent_cancel(){
		$post_arr=array(
					'QueryType'=>'get_orderinfo',
					'Params'=>'{"id":"'.$_POST['id'].'","orgguid":"88"}',
					'UserGuid'=>'ODh8QHJvbWVucw--'
					);
					$url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					$res=$this->functions->curlPostArray($url, $post_arr);
					$result = $this->object_to_array($res);
					$out_order_no = $result['OUT_ORDER_NO'];
					$dmoney = $result['DEPOSIT_AMOUNT'];
					$tranmoney = $result['TRANSPORTPRICE'];
					$remoney = (float)$dmoney + (float)$tranmoney;
					if ($res->STATUS == '0' || $res->STATUS == '6'){
						//echo "成功";
						$post_arr=array(
					             'QueryType'=>'update_order',
					             'Params'=>'{"isclerk":"1","branchguid":"'.$_POST['branchguid'].'","goodsid":"'.$_POST['goodsid'].'","orgguid":"88","status":"2","order_no":"'.$_POST['order_no'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					             echo json_encode($res,JSON_UNESCAPED_UNICODE);
					             $wxpay = new WxPay();
					             $wxpay->pay_refund($out_order_no,(String)$remoney,(String)$remoney);
					}else{
						$data=array('state'=>'2');
						echo json_encode($data);
					}

	}
	public function curlpost($url,$data){
		$ch = curl_init(); //初始化curl
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt ( $ch, CURLOPT_SAFE_UPLOAD, FALSE);
		curl_setopt($ch, CURLOPT_URL, $url);//设置链接
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息
		curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//POST数据
		$response = curl_exec($ch);//接收返回信息
		$res=json_encode($response);
		if(curl_errno($ch)){//出错则显示错误信息
			echo"错误：";
			print curl_error($ch);
		}
		curl_close($ch); //关闭curl链接
		return $res;
	}
	function object_to_array($obj){
		$_arr = is_object($obj)? get_object_vars($obj) :$obj;
		foreach ($_arr as $key => $val){
			$val=(is_array($val)) || is_object($val) ? $this->object_to_array($val) :$val;
			$arr[$key] = $val;
		}
		return $arr;
	}
}
