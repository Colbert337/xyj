<?php
header("Content-type: text/html; charset=utf-8");
ini_set('display_errors', '0');
error_reporting(E_ALL ^ E_NOTICE);
if(date_default_timezone_get() != "1Asia/Shanghai") date_default_timezone_set("Asia/Shanghai");
defined('BASEPATH') OR exit('No direct script access allowed');
require_once '/var/www/AliRentRomens/application/aop/AopClient.php';
require_once '/var/www/AliRentRomens/application/aop/request/AlipayMarketingCardTemplateCreateRequest.php';
require_once '/var/www/AliRentRomens/application/aop/request/AlipayMarketingCardTemplateModifyRequest.php';
require_once '/var/www/AliRentRomens/application/aop/request/AlipayOfflineMaterialImageUploadRequest.php';
require_once '/var/www/AliRentRomens/application/aop/request/AlipayMarketingCardOpenRequest.php';
require_once '/var/www/AliRentRomens/application/aop/request/AlipayOpenPublicShortlinkCreateRequest.php';
require_once '/var/www/AliRentRomens/application/aop/request/AlipayOpenPublicQrcodeCreateRequest.php';
require_once '/var/www/AliRentRomens/application/aop/request/AlipayMarketingCardActivateurlApplyRequest.php';
require_once '/var/www/AliRentRomens/application/aop/request/AlipayMarketingCardActivateformQueryRequest.php';
require_once '/var/www/AliRentRomens/application/aop/request/AlipayOpenPublicMessageSingleSendRequest.php';
require_once '/var/www/AliRentRomens/application/aop/request/ZhimaMerchantOrderRentCreateRequest.php';
require_once '/var/www/AliRentRomens/application/aop/request/ZhimaMerchantOrderRentCancelRequest.php';
require_once '/var/www/AliRentRomens/application/aop/request/ZhimaMerchantOrderRentCompleteRequest.php';
require_once '/var/www/AliRentRomens/application/aop/request/ZhimaMerchantOrderRentQueryRequest.php';
require_once '/var/www/AliRentRomens/application/aop/request/ZhimaMerchantBorrowEntityUploadRequest.php';
require_once '/var/www/AliRentRomens/application/aop/request/ZhimaMerchantOrderRentModifyRequest.php';
require_once dirname(__FILE__).'/../../aop/request/AlipayTradeAppPayRequest.php';
require_once dirname(__FILE__).'/../../aop/request/AlipayUserElectronicidUserQueryRequest.php';
require_once dirname(__FILE__).'/../../aop2/request/AlipayFundTransToaccountTransferRequest.php';
class AliZhimaAPI extends CI_Controller {
	public $aliappid;
	public $rsaPrivateKey;
	public $rsaPrivateKeyFilePath;
	public $alipayrsaPublicKey;
	public $token;
	public $aop;
	public $wxuser;
	public function __construct()
	{
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
	//查询订单方法
	public function zhima_sel($out_order_no){
		$request = new ZhimaMerchantOrderRentQueryRequest ();
		$bizcontentarray=array(
		    'out_order_no'=>$out_order_no,
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
	        $res=$this->object_to_array($result->$responseNode);
	        return $res;
	}
	public function zhima_complete($order_no,$rtime,$money){
		$request = new ZhimaMerchantOrderRentCompleteRequest ();
		$bizcontentarray=array(
				'order_no'=>$order_no,
				'product_code'=>'w1010100000000002858',
				'restore_time'=>$rtime,
				'pay_amount_type'=>'RENT',
				'pay_amount'=>$money
		);
		$bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
		$bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
		$request->setBizContent($bizcontent);
		$result = $this->aop->execute($request);
		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$res=$this->object_to_array($result->$responseNode);
	    return $res;
	}
	public function zhima_cancel($order_no){
		$request = new ZhimaMerchantOrderRentCancelRequest ();
		$bizcontentarray=array(
				'order_no'=>$order_no,
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
				$res=$this->object_to_array($result->$responseNode);
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