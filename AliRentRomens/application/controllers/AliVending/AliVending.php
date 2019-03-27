<?php
//header("Content-type: text/html; charset=utf-8");
//ini_set('display_errors', '0');
//error_reporting(E_ALL ^ E_NOTICE);
if(date_default_timezone_get() != "1Asia/Shanghai") date_default_timezone_set("Asia/Shanghai");
defined('BASEPATH') OR exit('No direct script access allowed');
require_once '/var/www/AliRentRomens/application/aop/AopClient.php';
//require_once '/var/www/AliRentRomens/application/aop/SignData.php';
//require_once '/var/www/AliRentRomens/application/vaop/lotusphp_runtime/Logger/Logger.php';	
require_once '/var/www/AliRentRomens/application/vaop/aop/request/AntMerchantExpandAutomatApplyUploadRequest.php';
class AliVending extends CI_Controller {
	public $aliappid;
	public $rsaPrivateKey;
	public $rsaPrivateKeyFilePath;
	public $alipayrsaPublicKey;
	public $token;
	public $aop;
	public function __construct()
	{
		parent::__construct();
		$this->aop = new AopClient ();
		$this->aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
		$this->aop->appId = '2018073160821899';
		//$this->aop->rsaPrivateKey = $this->rsaPrivateKey;
		//merchant_rsa_private_key.pem路径111
		$this->rsaPrivateKeyFilePath ='/var/www/AliRentRomens/application/aop/key/jzkey/rsa_private_key.pem';
		//merchant_rsa_private_key.pem内容
		//$this->rsaPrivateKey ='MIICWwIBAAKBgQCoHBQthaQWaV5pzZ0B8qQwDf8a+GbRxZDqvwOfM3rJckBDuJ2qXJvYCQyQIq+ZdfPw1dDd4pJgK4t3U83qXhakM/nCRgi4gVHdobP7KUpLJURKfsadTcQX7aaydwr176yT++0Ke51/G5/HsqZ2jHPY3ooUeiQYhUskCi/SCGLkYQIDAQABAoGARJSv5qJOfpYd3ivzkYfbU39iQy5zQ8DFjf6/C4OE5AmoDfiS2Z1ONqP6bBK6cHCeQ/H2c46rCHC7RML7jlE0Cr9zWPis/G2x1jYPiVb3wUzmR/21lHIbB7qIzQVGKPx7Ugd2Z71HUyrtH4z3aZ9sTr2JE6p2Y4tuN7LibV8A9cECQQDZu34bYZjATyxrdRdz3dufO+IcYksKTHQZvC2XAj6d1DRryB8puQFa9gVDD6Q4NdbJkvBfHxvwugm3iQik9eTfAkEAxaflEQLXzNLbHRDIEfJoboU4ZN3KJZkY0hKV5iG0YjJRIq1t9zTDvoP4udNF7k5rFYDbm3Wb5IVt9d4XDEUevwJAZYaOq/fbQTjpzoV/1RBLWzmSGoge04OI04MyguqSBggwFV3wYgUZQ6/aDkYZ3fgE2mNA4CniXmJxK3qjZEAgYwJAG4trO7SmuC+GQ4WsK/wZG5XLJxtVaWntcJEQfLKjva9/aRK8KWAcCze++L59l1ksSSHc+Mwp/m2txj69/YLAZwJADKgq0MAXm4txbIAyWAdmvKnTUAC7RFoFIB9GKyefwc53HSgJv96GQ9eVLfaqtAhR0dhF0C/4DoBwONh1jk1zkg==';
		//alipay_rsa_public_key.pem内容
		$this->alipayrsaPublicKey ='MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAsJc7O7cKkegegkhuqLJl0Vi93aT1fQXqqjWKCejxvTPxE2f2DUx2J8kE/cntWUpeWmXTdFmO7Xi+EaKofEsTYXeDa1B+R6w7tSQrnhnODDi1uJ0aACmQxm3tTo3xrWRjaMfSeJtDtMLXlo7kFl82FmEzzpcxGXH7Msx9T+bsd1sj7+ZAG3bpjFHE3SC+DYHqlxUN5ioFc31uI7kwjM4OdJRGSASifGZBy3C23UEd5MafHIWj990zGadxSQR1v+qXyYeO1yfbE4lEj1rLO0a0a/3agVMwY1TswXQ8hKkam20VIHchbVYnYLsa6eAAZi95Ex6HvsrNnUdMXMFzDbQCxwIDAQAB';
		//$this->aop->rsaPrivateKey = $this->rsaPrivateKey;
		$this->aop->rsaPrivateKeyFilePath = $this->rsaPrivateKeyFilePath;
		$this->aop->alipayrsaPublicKey = $this->alipayrsaPublicKey;
		$this->aop->apiVersion = '1.0';
		$this->aop->signType = 'RSA2';
		$this->aop->postCharset = 'GBK';
		$this->aop->format = 'json';
		$this->load->library('functions');
	}
	public function addpro(){//2018080202611348
	$request = new AntMerchantExpandAutomatApplyUploadRequest ();
	$bizcontentarray=array(
				'terminal_id'=>'YN-D002',
				'product_user_id'=>'2088521432874321',
	            'merchant_user_id'=>'2088521432874321',
	            'machine_type'=>'OTHER',
	            'machine_cooperation_type'=>'COOPERATION_CONTRACT',
	            'machine_delivery_date'=>date("Y-m-d H:i:s"),
	            'machine_name'=>'雨诺轮椅',
	            'delivery_address'=>
	                  array(
	                    'machine_address'=>'青岛软件园',
	                    'area_code'=>'370202',
	                    'city_code'=>'370200',
	                    'province_code'=>'370000'
	                  ),
	            'point_position'=>
	                  array(
	                    'machine_address'=>'青岛软件园',
	                    'area_code'=>'370202',
	                    'city_code'=>'370200',
	                    'province_code'=>'370000'
	                  ), 
	            'merchant_user_type'=>'ALIPAY_MERCHANT',
	            'associate'=>
	                  array(
	                    'associate_type'=>'DISTRIBUTORS',
	                    'associate_user_id'=>'2088521432874321',
	                  ),
	            'scene'=>
	                  array(
	                    'level_1'=>'HOSPITAL',
	                    'level_2'=>'001'
	                  )
				);
				$bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
				var_dump($bizcontent0);
				//echo '0^'.$bizcontent0;die;
				$bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
				$request->setBizContent($bizcontent);
//			    $signData = $request->getApiParas();
//				$sign = $this->aop->rsaSign($signData, $this->aop->signType);
				$result = $this->aop->execute($request);
				$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
				$resultCode = $result->$responseNode->code;
				if(!empty($resultCode)&&$resultCode == 10000){
					echo "成功";
					var_dump($result);
				}else{
					echo "失败";
					var_dump($result);
				}
	}
	public function addpro2(){
		$request = new AntMerchantExpandAutomatApplyUploadRequest ();
		$request->setBizContent("{" .
		"\"terminal_id\":\"100100000045\"," .
		"\"product_user_id\":\"2088101011563130\"," .
		"\"merchant_user_id\":\"2088302163326077\"," .
		"\"machine_type\":\"AUTOMAT\"," .
		"\"machine_cooperation_type\":\"COOPERATION_CONTRACT\"," .
		"\"machine_delivery_date\":\"2017-09-18 20:00:00\"," .
		"\"trade_no\":\"2017092721001002070500100030\"," .
		"\"machine_name\":\"以勒科技\"," .
        "\"delivery_address\":{" .
		"\"area_code\":330106," .
        "\"machine_address\":\"浙江省杭州市西湖区黄龙时代广场\"," .
		"\"province_code\":330000," .
        "\"city_code\":330100" .
        " }," .
		"\"point_position\":{" .
        "\"area_code\":330106," .
		"\"machine_address\":\"浙江省杭州市西湖区黄龙时代广场\"," .
		"\"province_code\":330000," .
        "\"city_code\":330100" .
		" }," .
        "\"merchant_user_type\":\"ALIPAY_MERCHANT\"," .
		"\"associate\":{" .
        "\"associate_type\":\"DISTRIBUTORS\"," .
		"\"associate_user_id\":\"2088302163326032\"".
		" }," .
        "\"scene\":{" .
        "\"level_1\":\"SCHOOL\"," .
        "\"level_2\":\"001\"" .
		" }" .
        " }");
        $result = $this->aop->execute ( $request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
	    if(!empty($resultCode)&&$resultCode == 10000){
            echo "成功";
        } else {
            echo "失败";
            var_dump($result);
        }
	}
}
