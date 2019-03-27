<?php
header("Content-type: text/html; charset=utf-8");
ini_set('display_errors', '0');
error_reporting(E_ALL ^ E_NOTICE);
require_once '/var/www/AliRentRomens/application/aop/AopClient.php';
require_once '/var/www/AliRentRomens/application/aop/request/ZhimaMerchantOrderRentCancelRequest.php';
require_once '/var/www/AliRentRomens/application/aop/request/ZhimaMerchantOrderRentQueryRequest.php';
if(date_default_timezone_get() != "1Asia/Shanghai") date_default_timezone_set("Asia/Shanghai");
class AliRentReturn extends CI_Controller {
	public $aliappid;
	public $rsaPrivateKey;
	public $rsaPrivateKeyFilePath;
	public $alipayrsaPublicKey;
	public $token;
	public $aop;
	public $wxuser;
	public function __construct()
	{
		parent::__construct();
		$this->aop = new AopClient ();
		$this->aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
		$this->aop->appId = '2016121004107537';
		//$this->aop->rsaPrivateKey = $this->rsaPrivateKey;
		//merchant_rsa_private_key.pem路径111
		$this->rsaPrivateKeyFilePath ='/var/www/AliRentRomens/application/aop/key/jzkey/merchant_rsa_private_key.pem';
		//merchant_rsa_private_key.pem内容
		//$this->rsaPrivateKey ='MIICWwIBAAKBgQCoHBQthaQWaV5pzZ0B8qQwDf8a+GbRxZDqvwOfM3rJckBDuJ2qXJvYCQyQIq+ZdfPw1dDd4pJgK4t3U83qXhakM/nCRgi4gVHdobP7KUpLJURKfsadTcQX7aaydwr176yT++0Ke51/G5/HsqZ2jHPY3ooUeiQYhUskCi/SCGLkYQIDAQABAoGARJSv5qJOfpYd3ivzkYfbU39iQy5zQ8DFjf6/C4OE5AmoDfiS2Z1ONqP6bBK6cHCeQ/H2c46rCHC7RML7jlE0Cr9zWPis/G2x1jYPiVb3wUzmR/21lHIbB7qIzQVGKPx7Ugd2Z71HUyrtH4z3aZ9sTr2JE6p2Y4tuN7LibV8A9cECQQDZu34bYZjATyxrdRdz3dufO+IcYksKTHQZvC2XAj6d1DRryB8puQFa9gVDD6Q4NdbJkvBfHxvwugm3iQik9eTfAkEAxaflEQLXzNLbHRDIEfJoboU4ZN3KJZkY0hKV5iG0YjJRIq1t9zTDvoP4udNF7k5rFYDbm3Wb5IVt9d4XDEUevwJAZYaOq/fbQTjpzoV/1RBLWzmSGoge04OI04MyguqSBggwFV3wYgUZQ6/aDkYZ3fgE2mNA4CniXmJxK3qjZEAgYwJAG4trO7SmuC+GQ4WsK/wZG5XLJxtVaWntcJEQfLKjva9/aRK8KWAcCze++L59l1ksSSHc+Mwp/m2txj69/YLAZwJADKgq0MAXm4txbIAyWAdmvKnTUAC7RFoFIB9GKyefwc53HSgJv96GQ9eVLfaqtAhR0dhF0C/4DoBwONh1jk1zkg==';
		//alipay_rsa_public_key.pem内容
		$this->alipayrsaPublicKey ='MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDDI6d306Q8fIfCOaTXyiUeJHkrIvYISRcc73s3vF1ZT7XN8RNPwJxo8pWaJMmvyTn9N4HQ632qJBVHf8sxHi/fEsraprwCtzvzQETrNRwVxLO5jVmRGi60j8Ue1efIlzPXV9je9mkjzOmdssymZkh2QhUrCmZYI/FCEa3/cNMW0QIDAQAB';
		//$this->aop->rsaPrivateKey = $this->rsaPrivateKey;
		$this->aop->rsaPrivateKeyFilePath = $this->rsaPrivateKeyFilePath;
		$this->aop->alipayrsaPublicKey = $this->alipayrsaPublicKey;
		$this->aop->apiVersion = '1.0';
		$this->aop->signType = 'RSA';
		$this->aop->postCharset = 'GBK';
		$this->aop->format = 'json';
	}
	public function returnorder(){
		$this->load->library('functions');
		if($_POST['notify_type']=='ORDER_CREATE_NOTIFY'){
			$data = array(
			  'msg'=>'创建订单',
			  'out_order_no'=>$_POST['out_order_no'],
			  'order_no'=>$_POST['order_no']
			);
			$this->functions->alireturn(json_encode($data,JSON_UNESCAPED_UNICODE));
			echo "success";
			sleep(180);
			$post_arr=array(
					             'QueryType'=>'get_orderinfo',
					             'Params'=>'{"orderno":"'.$_POST['order_no'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->object_to_array($this->functions->curlPostArray($url, $post_arr));
                                 if($res == null){
                                 $this->functions->alireturn($_POST['out_order_no'].'没有生成订单，请注意');	
                                 }
					             if($res == null){
					             	$request = new ZhimaMerchantOrderRentCancelRequest ();
					             	$bizcontentarray=array(
				'order_no'=>$_POST['order_no'],
				'product_code'=>'w1010100000000002858'
				);
				$bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
				//echo '0^'.$bizcontent0;die;
				$bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
				$request->setBizContent($bizcontent);
				//				$signData = $request->getApiParas();
				//				$sign = $this->aop->rsaSign($signData, $this->aop->signType);
				$result = $this->aop->execute($request);
				$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
				$resultCode = $result->$responseNode->code;
				if(!empty($resultCode)&&$resultCode == 10000){
					$this->functions->alireturn($_POST['out_order_no'].'没有生成订单，已撤销');
					if($res[0]['STATUS']=='7'){
						$post_arr=array(
					             'QueryType'=>'update_order',
					             'Params'=>'{"money":"0","isclerk":"1","branchguid":"'.$res[0]['LEASEBRANCHGUID'].'","goodsid":"'.$res[0]['RENT_INFO'].'","orgguid":"88","status":"2","order_no":"'.$_POST['order_no'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					}
				}else{
					$this->functions->alireturn($_POST['out_order_no'].'没有生成订单，撤销失败');
				}
					             }else{
					             //$this->functions->alireturn($_POST['out_order_no'].'成功生成订单');
					             }
		}else{
			$data = array(
			  'msg'=>'归还订单',
			  'out_order_no'=>$_POST['out_order_no'],
			  'order_no'=>$_POST['order_no']
			);
			$request = new ZhimaMerchantOrderRentQueryRequest ();
			$bizcontentarray=array(
		       'out_order_no'=>$_POST['out_order_no'],
	           'product_code'=>'w1010100000000002858'
	           );
	           $bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
	           //echo '0^'.$bizcontent0;die;
	           $bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
	           $request->setBizContent($bizcontent);
	           //$signData = $request->getApiParas();
	           //$sign = $this->aop->rsaSign($signData, $this->aop->signType);
	           $result = $this->aop->execute($request);
	           $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
	           $resultCode = $result->$responseNode->code;
	           $money = $result->$responseNode->pay_amount;
	           $out_zfb_no = $result->$responseNode->alipay_fund_order_no;
	           $paytime = $result->$responseNode->pay_time;
	           //$this->functions->alireturn((String)$money);
	           if($result->$responseNode->pay_status == 'PAY_SUCCESS'){
	           	$post_arr=array(
					             'QueryType'=>'get_orderinfo',
					             'Params'=>'{"orderno":"'.$_POST['order_no'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->object_to_array($this->functions->curlPostArray($url, $post_arr));
					             //$this->functions->alireturn(json_encode($res[0],JSON_UNESCAPED_UNICODE));
					             $res = $res[0];
					             //发送归还模板消息
		                            if($res['ISDEPOSIT'] != '1'){
		                            	//康泰双旦活动开始
		                            	if($res['ORGGUID'] == '69'){		                            	
		                            	    //入库优惠券
		                            	    $this->load->database();
		                            	    $this->load->dbforge();
		                            	    $userid=$res['USER_ID'];
		                            	    $orderno = $res['OUT_ORDER_NO'];
		                            	    //入库优惠券结束
		                            	    //核销优惠券开始
		                            	    $query = $this->db->query("SELECT * FROM alicoupon WHERE orderno = '$orderno';");
		                            	    $result = $query->result_array();
		                            	    if(!empty($result)){
		                            	        $data = array(
		                            	            'status'=>'3'
		                            	        );
		                            	        $this->db->where('orderno', $orderno);
		                            	        $this->db->update('alicoupon', $data);
		                            	    }
		                            	    //核销优惠券结束
		                            	    //康泰双旦活动结束
		                            	}else{
		                            	    $temp = array(
		                            	        'tempid'=>'fdfaad7b088f4783b895fcf774612e03',
		                            	        'first'=>'归还成功，扣款'.$result->$responseNode->pay_amount.'元',
		                            	        'keyword1'=>$res['CREATETIME'],
		                            	        'keyword2'=>$res['BORROW_SHOP_NAME'],
		                            	        'keyword3'=>$result->$responseNode->restore_time,
		                            	        'keyword4'=>$res['BORROW_SHOP_NAME'],
		                            	        'keyword5'=>$res['OUT_ORDER_NO'],
		                            	        'keyword6'=>'400-686-0176',
		                            	        'remark'=>'感谢您使用雨诺信用借,祝您生活愉快'
		                            	    );
		                            	    $this->send_temp($res['USER_ID'],$temp);
		                            	}
		                            	
		                            	
		                            }
					             if($res['STATUS'] == '9'){
					             	//$money = (float)$res['TRANSPORTPRICE']+(float)$res['RENT_AMOUNT'];
					             	$post_arr=array(
					             'QueryType'=>'update_order',
					             	//'Params'=>'{"status":"3","order_no":"'.$_POST['order_no'].'"}',
					             'Params'=>'{"alipricetime":"'.$paytime.'","out_zfb_no":"'.$out_zfb_no.'","money":"'.(String)$money.'","status":"3","order_no":"'.$_POST['order_no'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $this->functions->alireturn(json_encode($post_arr));
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					             $this->functions->alireturn($res->state);
					                if($res->state == '1'){
					             	    $this->functions->alireturn($_POST['out_order_no'].'用户主动付款成功');
					                }
					             }
	           }
	           $this->functions->alireturn(json_encode($data,JSON_UNESCAPED_UNICODE));
	           echo "success";
		}
	}
    //生活号推送模板消息接口
	public function send_temp($userid,$msg){
		$url = 'http://zfb.yiyao365.cn/index.php?g=User&m=AliMemberCard&a=SENDTEMP&token=fdpbbq1480645793&userid='.$userid;
		$a=$this->functions->curlPostArray($url,$msg);
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
?>