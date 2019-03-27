<?php
header("Content-type: text/html; charset=utf-8");
ini_set('display_errors', '0');
error_reporting(E_ALL ^ E_NOTICE);
if(date_default_timezone_get() != "1Asia/Shanghai") date_default_timezone_set("Asia/Shanghai");
defined('BASEPATH') OR exit('No direct script access allowed');
require_once dirname(__FILE__).'/../WxRent/WxPay.php';
require_once dirname(__FILE__).'/../AliRent/AliZhimaAPI.php';
require_once "/var/www/AliRentRomens/application/libraries/jssdk.php";
class Order extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->library('functions');
	}
	public function login(){
		$this->load->view('Morder/login');
	}
	public function under(){
		$jssdk = new JSSDK("wxaf0846f7bb9a42e9", "fdec3f897a8872f51fccd37ae3feb36a");
		$signPackage = $jssdk->GetSignPackage();
		$data['signPackage'] = $signPackage;
		$this->load->view('Morder/under',$data);
	}
	public function test(){
		$this->load->view('Morder/test');
	}
	//下架操作
	public function underlock(){
		$post_arr=array(
					             'QueryType'=>'get_goodsinfo',
					             'Params'=>'{"qrcode":"'.$_POST['qrcode'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->object_to_array($this->functions->curlPostArray($url, $post_arr));
					             $lockcode=$res[0]['lockcode'];
					             if($lockcode == null){
					             	$data = array(
					             	  'state'=>'2',
					             	  'msg'=>'未找到'.$_POST['qrcode'].'这个锁号'
					             	);
					             }else{
					             	$orderno = date("YmdHis").$_COOKIE['name'];
					             	$lockurl = 'http://140.143.129.247:8081/api/openLock?extOrderNo='.$orderno.'&lockCode='.$lockcode;
					             	$res=$this->curlpost($lockurl, '');
					             	$c =  $this->object_to_array(json_decode(json_decode($res)));
                                    if($c['code'] == '000000'){
                                    	$data= array(
                                    	  'state' =>'1',
                                    	  'msg'=>'下架成功'
                                    	);
                                    }else{
                                    	$data= array(
                                    	  'state' =>'2',
                                    	  'msg'=>$c['msg']
                                    	);
                                    }
					             }
					             echo json_encode($data,JSON_UNESCAPED_UNICODE);
	}
	public function home(){
		$this->load->view('Morder/home');
	}
	public function update_pwd(){
		$data =array(
		  'status'=>'2'
		  );
		  if($_POST){
		  	if(empty($_POST['username'])){
		  		$msg = '用户名不能为空';
		  	}elseif (empty($_POST['opwd'])){
		  		$msg = '密码不能为空';
		  	}elseif (empty($_POST['npwd']) || empty($_POST['npwd2'])){
		  		$msg = '新密码不能为空';
		  	}else{
		  		$this->load->database();
		  		$username = $_POST['username'];
		  		$query = $this->db->query("SELECT pwd FROM users where name='$username';");
		  		$res=$query->result_array();
		  		if(!empty($res[0])){
		  			if($res[0]['pwd'] != $_POST['opwd']){
		  				$msg = '密码错误';
		  			}else{
		  				if($_POST['npwd'] != $_POST['npwd2']){
		  					$msg = '两次新密码不一样';
		  				}else{
		  					$pwd = $_POST['npwd'];
		  					$query = $this->db->query("UPDATE users SET pwd='$pwd' where name='$username';");
		  					if($query){
		  						$msg = '修改成功！';
		  						$data['status'] = '1';
		  					}else{
		  						$msg = '系统正忙';
		  					}
		  				}
		  			}
		  		}else{
		  			$msg = '用户名错误';
		  		}
		  		 
		  	}
		  	$data['msg']= $msg;
		  	echo json_encode($data,JSON_UNESCAPED_UNICODE);die;
		  }
		  $this->load->view('Morder/update_pwd');
	}
	public function check(){
		$state = '2';
		$this->load->database();
		$username = $_POST['username'];
		$query = $this->db->query("SELECT pwd,permission FROM users where name='$username';");
		$res=$query->result_array();
		if(!empty($res)){
			if($res[0]['pwd'] == $_POST['pwd']){
				$msg = "登录成功";
				setcookie('name',$_POST['username'],time()+7200);
				setcookie('pwd',$_POST['pwd'],time()+7200);
				setcookie('username',$res[0]['username'],time()+7200);
				setcookie('permission',$res[0]['permission'],time()+7200);
				$state = '1';
			}else{
				$msg = "密码错误";
			}
		}else{
			$msg = "用户名错误";
		}
		//		  if($_POST['username'] != 'admin' && $_POST['username'] != 'liushuai'){
		//			$msg = "用户名错误";
		//		  }else if($_POST['pwd'] != '123456'){
		//			$msg = "密码错误";
		//		  }else{
		//			$msg = "登录成功";
		//			setcookie('name',$_POST['username'],time()+7200);
		//            setcookie('pwd',$_POST['pwd'],time()+7200);
		//			if($_POST['username'] == 'liushuai'){
		//				$state = '2';
		//			}else{
		//				$state = '1';
		//			}
		//		  }
		$data = array(
		    'state'=>$state,
		    'msg'=>$msg
		);
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	}
	public function index($admin,$name){
		if($_POST){
			$post_arr=array(
					             'QueryType'=>'get_orderinfo',
					             'Params'=>'{"username":"'.$_POST['username'].'","goodsname":"'.$_POST['goodsname'].'","phone":"'.$_POST['phone'].'","out_order_no":"'.$_POST['out_order_no'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $info=$this->functions->object_to_array($this->functions->curlPostArray($url, $post_arr));
					             $res = array(
					                'info'=>$info,
					                'out_order_no'=>$_POST['out_order_no'],
					                'goodsname'=>$_POST['goodsname'],
					                'username'=>$_POST['username'],
					                'phone'=>$_POST['phone'],
					                'admin'=>$_COOKIE['permission'],
					                'name'=>$_COOKIE['name']
					             );
					             //var_dump($info);
					             $this->load->view('Morder/Order',$res);
		}else{
			$this->load->view('Morder/Order');
		}
		
	}
	public function sel_info(){
		$post_arr=array(
					             'QueryType'=>'get_orderinfo',
					             'Params'=>'{"id":"'.$_POST['id'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->object_to_array($this->functions->curlPostArray($url, $post_arr));
					             //判断状态显示归还时间及金额
					             if($res['STATUS']== '3'){
					             	$rent = '￥'.$res['RENT_AMOUNT'];
					             	$returntime = $res['RETURNGOODSTIME'];
					             }else{
					             	$rent = '未结算';
					             	$returntime = '暂无';
					             }
					             //判断下单渠道显示微信或支付宝订单信息
					             if($res['ISDEPOSIT'] == '1'){
					             	$orderway = '微信订单';
					             	$wxpay = new WxPay();
					             	$wxorder = $wxpay->sel_refund_api($res['OUT_ORDER_NO']);
					             	if($wxorder['refund_status_0'] == 'SUCCESS'){
					             		$wayinfo = '退款成功';
					             	}else if($wxorder['refund_status_0'] == 'PROCESSING'){
					             		$wayinfo = '退款处理中';
					             	}else if($wxorder['refund_status_0'] == 'REFUNDCLOSE'){
					             		$wayinfo = '退款关闭';
					             	}else if($wxorder['refund_status_0'] == 'CHANGE'){
					             		$wayinfo = '账户异常';
					             	}else{
					             		$wayinfo = '未退款';
					             	}
					             }else{
					             	$orderway = '支付宝订单';
					             	$alipay = new AliZhimaAPI();
					             	$aliorder = $alipay->zhima_sel($res['OUT_ORDER_NO']);
					             	if($aliorder['admit_state'] == 'Y'){
					             		$ispay = '免押金';
					             	}else{
					             		$ispay = '非免押';
					             	}
					             	if($aliorder['use_state'] == 'borrow'){
					             		$wayinfo = '未归还('.$ispay.')';
					             	}else if($aliorder['use_state'] == 'cancel'){
					             		$wayinfo = '已撤销('.$ispay.')';
					             	}else if($aliorder['use_state'] == 'restore' && $aliorder['pay_status'] == 'PAY_INIT'){
					             		$wayinfo = '待支付('.$ispay.')';
					             	}else if($aliorder['use_state'] == 'restore' && $aliorder['pay_status'] == 'PAY_SUCCESS'){
					             		$wayinfo = '已完成('.$ispay.')';
					             	}else if($aliorder['use_state'] == 'restore' && $aliorder['pay_status'] == 'PAY_FAILED'){
					             		$wayinfo = '付款失败('.$ispay.')';
					             	}else if($aliorder['use_state'] == 'restore' && $aliorder['pay_status'] == 'PAY_INPROGRESS'){
					             		$wayinfo = '支付中('.$ispay.')';
					             	}
					             	if(trim($aliorder['pay_amount'])!=''){
					             		$rent = '￥'.$aliorder['pay_amount'];
					             	}
					             }
					             $info = array(
					               'order_no'=>$res['ORDER_NO'],
	                               'rent'=>$rent,
					               'drent'=>$res['DEPOSIT_AMOUNT'],
					               'lockcode'=>$res['LOCKCODE'],
					               'shopname'=>$res['BORROW_SHOP_NAME'],
					               'returntime'=>$returntime,
					               'orderway'=>$orderway,
					               'wayinfo'=>$wayinfo,
					             );
					             echo json_encode($info, JSON_UNESCAPED_UNICODE);
	}
	//修改完成订单金额
	public function update_money(){
	    $post_arr=array(
	        'QueryType'=>'get_orderinfo',
	        'Params'=>'{"id":"'.$_POST['id'].'"}',
	        'UserGuid'=>'ODh8QHJvbWVucw--'
	    );
	    $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
	    $res=$this->functions->object_to_array($this->functions->curlPostArray($url, $post_arr));
	    $order_no=$res['ORDER_NO'];
	    $post_arr=array(
	        'QueryType'=>'update_order',
	        'Params'=>'{"money":"'.$_POST['money'].'","order_no":"'.$order_no.'"}',
	        'UserGuid'=>'ODh8QHJvbWVucw--'
	    );
	    $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
	    $res=$this->functions->curlPostArray($url, $post_arr);
	    echo json_encode($res,JSON_UNESCAPED_UNICODE);
	    $this->functions->debuglog($_POST['name'].'通过公众号修改订单金额'.$out_order_no);
	}
	public function complete(){
		$post_arr=array(
					             'QueryType'=>'get_orderinfo',
					             'Params'=>'{"id":"'.$_POST['id'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->object_to_array($this->functions->curlPostArray($url, $post_arr));
					             $out_order_no=$res['OUT_ORDER_NO'];
					             if($res['ISDEPOSIT'] == '1' && ($res['STATUS'] == '1' || $res['STATUS'] == '7' || $res['STATUS'] == '0' || $res['STATUS'] == '8')){
					             	$wxpay = new WxPay();
					             	$wxorder = $wxpay->pay_refund_api($res['ORDER_NO'],$res['DEPOSIT_AMOUNT'],$res['DEPOSIT_AMOUNT']-$_POST['money']);
					             	if($wxorder['result_code'] == 'SUCCESS'){
					             		$post_arr=array(
					             'QueryType'=>'update_order',
					             'Params'=>'{"goodsid":"'.$res['RENT_INFO'].'","branchguid":"'.$res['LEASEBRANCHGUID'].'","money":"'.$_POST['money'].'","returngoodstime":"'.date("Y-m-d H:i:s").'","isclerk":"1","iswarning":"2","orgguid":"88","status":"3","order_no":"'.$res['ORDER_NO'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					             echo json_encode($res,JSON_UNESCAPED_UNICODE);
					             $this->functions->debuglog($_POST['name'].'通过公众号操作订单'.$out_order_no);
					             	}else{
					             		$data = array(
	        	   'state'=>'2',
	        	   'msg'=>'微信订单归还失败了兄dei,问问帅气的刘工咋回事？'
	        	   );
	        	   echo json_encode($data,JSON_UNESCAPED_UNICODE);
					             	}
					             }else if ($res['STATUS'] == '1' || $res['STATUS'] == '7' || $res['STATUS'] == '0' || $res['STATUS'] == '8' || $res['STATUS'] == '9'){
					             	$alipay = new AliZhimaAPI();
					             	$rtime = date("Y-m-d H:i:s");
					             	$aliorder = $alipay->zhima_complete($res['ORDER_NO'], $rtime, $_POST['money']);
					             	if($aliorder['code'] == 10000){
					             		$post_arr=array(
					             'QueryType'=>'update_order',
					             'Params'=>'{"goodsid":"'.$res['RENT_INFO'].'","branchguid":"'.$res['LEASEBRANCHGUID'].'","money":"'.$_POST['money'].'","returngoodstime":"'.$rtime.'","isclerk":"1","iswarning":"2","orgguid":"88","status":"3","order_no":"'.$res['ORDER_NO'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					             echo json_encode($res,JSON_UNESCAPED_UNICODE);
					             $this->functions->debuglog($_POST['name'].'通过公众号操作订单'.$out_order_no);
					             	}else{
					             		$data = array(
	        	   'state'=>'2',
	        	   'msg'=>'订单归还失败了兄dei,问问帅气的刘工咋回事？'
	        	   );
	        	   echo json_encode($data,JSON_UNESCAPED_UNICODE);
					             	}
					             }else{
					             	$data = array(
	        	   'state'=>'2',
	        	   'msg'=>'只能操作进行中订单'
	        	   );
	        	   echo json_encode($data,JSON_UNESCAPED_UNICODE);
					             }
	}
	public function cancel(){
		$post_arr=array(
					             'QueryType'=>'get_orderinfo',
					             'Params'=>'{"id":"'.$_POST['id'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->object_to_array($this->functions->curlPostArray($url, $post_arr));
					             $out_order_no = $res['OUT_ORDER_NO'];
					             if($res['ISDEPOSIT'] == '1' && ($res['STATUS'] == '1' || $res['STATUS'] == '7' || $res['STATUS'] == '0' || $res['STATUS'] == '8')){
					             	$wxpay = new WxPay();
					             	$wxorder = $wxpay->pay_refund_api($res['ORDER_NO'],$res['DEPOSIT_AMOUNT'],$res['DEPOSIT_AMOUNT']);
					             	if($wxorder['result_code'] == 'SUCCESS'){
					             		$post_arr=array(
					             'QueryType'=>'update_order',
					             'Params'=>'{"money":"0","isclerk":"1","branchguid":"'.$res['LEASEBRANCHGUID'].'","goodsid":"'.$res['RENT_INFO'].'","orgguid":"88","status":"2","order_no":"'.$res['ORDER_NO'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					             echo json_encode($res,JSON_UNESCAPED_UNICODE);
					             $this->functions->debuglog($_POST['name'].'通过公众号操作订单'.$out_order_no);
					             	}else{
					             		$data = array(
	        	   'state'=>'2',
	        	   'msg'=>'微信订单撤销失败了兄dei,问问帅气的刘工咋回事？'
	        	   );
	        	   echo json_encode($data,JSON_UNESCAPED_UNICODE);
					             	}
					             }else if ($res['STATUS'] == '1' || $res['STATUS'] == '7' || $res['STATUS'] == '0' || $res['STATUS'] == '8'){
					             	$alipay = new AliZhimaAPI();
					             	$aliorder = $alipay->zhima_cancel($res['ORDER_NO']);
					             	if($aliorder['code'] == 10000){
					             		$post_arr=array(
					             'QueryType'=>'update_order',
					             'Params'=>'{"money":"0","isclerk":"1","branchguid":"'.$res['LEASEBRANCHGUID'].'","goodsid":"'.$res['RENT_INFO'].'","orgguid":"88","status":"2","order_no":"'.$res['ORDER_NO'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					             echo json_encode($res,JSON_UNESCAPED_UNICODE);
					             $this->functions->debuglog($_POST['name'].'通过公众号操作订单'.$out_order_no);
					             	}else{
					             		$data = array(
	        	   'state'=>'2',
	        	   'msg'=>'撤销失败了兄dei,问问帅气的刘工咋回事？'
	        	   );
	        	   echo json_encode($data,JSON_UNESCAPED_UNICODE);
					             	}
					             }else{
					             	$data = array(
	        	   'state'=>'2',
	        	   'msg'=>'只能操作进行中订单'
	        	   );
	        	   echo json_encode($data,JSON_UNESCAPED_UNICODE);
					             }
	}
	function object_to_array($obj){
		$_arr = is_object($obj)? get_object_vars($obj) :$obj;
		foreach ($_arr as $key => $val){
			$val=(is_array($val)) || is_object($val) ? $this->object_to_array($val) :$val;
			$arr[$key] = $val;
		}
		return $arr;
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
}
?>