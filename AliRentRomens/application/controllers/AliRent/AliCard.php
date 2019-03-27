<?php
// header("Content-type: text/html; charset=utf-8");
// ini_set('display_errors', '0');
// error_reporting(E_ALL ^ E_NOTICE);
if(date_default_timezone_get() != "1Asia/Shanghai") date_default_timezone_set("Asia/Shanghai");
defined('BASEPATH') OR exit('No direct script access allowed');
require_once dirname(__FILE__).'/../../aop/AopClient.php';
require_once dirname(__FILE__).'/../../aop2/request/AlipayMarketingVoucherQueryRequest.php';
require_once dirname(__FILE__).'/../../aop2/request/AlipayMarketingVoucherListQueryRequest.php';
require_once dirname(__FILE__).'/../../aop2/request/AlipayMarketingVoucherSendRequest.php';
require_once dirname(__FILE__).'/../../aop2/request/AlipayMarketingVoucherTemplatedetailQueryRequest.php';
require_once dirname(__FILE__).'/../../aop2/request/AlipayMarketingVoucherListQueryRequest.php';
class AliCard extends CI_Controller {
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
        $this->aop->appId = '2088521432874321';
        //$this->aop->rsaPrivateKey = $this->rsaPrivateKey;
        //merchant_rsa_private_key.pem路径111
        $this->rsaPrivateKeyFilePath ='/var/www/AliRentRomens/application/aop/key/jksh/rsa_private_key.pem';
        //merchant_rsa_private_key.pem内容
        //$this->rsaPrivateKey ='MIICWwIBAAKBgQCoHBQthaQWaV5pzZ0B8qQwDf8a+GbRxZDqvwOfM3rJckBDuJ2qXJvYCQyQIq+ZdfPw1dDd4pJgK4t3U83qXhakM/nCRgi4gVHdobP7KUpLJURKfsadTcQX7aaydwr176yT++0Ke51/G5/HsqZ2jHPY3ooUeiQYhUskCi/SCGLkYQIDAQABAoGARJSv5qJOfpYd3ivzkYfbU39iQy5zQ8DFjf6/C4OE5AmoDfiS2Z1ONqP6bBK6cHCeQ/H2c46rCHC7RML7jlE0Cr9zWPis/G2x1jYPiVb3wUzmR/21lHIbB7qIzQVGKPx7Ugd2Z71HUyrtH4z3aZ9sTr2JE6p2Y4tuN7LibV8A9cECQQDZu34bYZjATyxrdRdz3dufO+IcYksKTHQZvC2XAj6d1DRryB8puQFa9gVDD6Q4NdbJkvBfHxvwugm3iQik9eTfAkEAxaflEQLXzNLbHRDIEfJoboU4ZN3KJZkY0hKV5iG0YjJRIq1t9zTDvoP4udNF7k5rFYDbm3Wb5IVt9d4XDEUevwJAZYaOq/fbQTjpzoV/1RBLWzmSGoge04OI04MyguqSBggwFV3wYgUZQ6/aDkYZ3fgE2mNA4CniXmJxK3qjZEAgYwJAG4trO7SmuC+GQ4WsK/wZG5XLJxtVaWntcJEQfLKjva9/aRK8KWAcCze++L59l1ksSSHc+Mwp/m2txj69/YLAZwJADKgq0MAXm4txbIAyWAdmvKnTUAC7RFoFIB9GKyefwc53HSgJv96GQ9eVLfaqtAhR0dhF0C/4DoBwONh1jk1zkg==';
        //alipay_rsa_public_key.pem内容
        $this->alipayrsaPublicKey ='MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC0+LHskgzrsggyUqdOX8taFP+TDWrM+uBIf5I1Uh+/YRwVzBnE1JvFON022UnsWBqf2mSzuZJd54PwOCAGdauZIgOvWmOGdJD3kCnNmDKOdvLRsXwwEz4cFFsvPAb53Zq6thkJPETNs5hkdBca+5rOhSNQP0BWw7JM58o5vMZy/wIDAQAB';
        //$this->aop->rsaPrivateKey = $this->rsaPrivateKey;
        $this->aop->rsaPrivateKeyFilePath = $this->rsaPrivateKeyFilePath;
        $this->aop->alipayrsaPublicKey = $this->alipayrsaPublicKey;
        $this->aop->apiVersion = '1.0';
        $this->aop->signType = 'RSA';
        $this->aop->postCharset = 'GBK';
        $this->aop->format = 'json';
        $this->load->library('functions');
    }
    public function create_coupon(){
        echo "<pre>";
        $request = new AlipayMarketingCashvoucherTemplateCreateRequest ();
        $bizcontentarray=array(
            'voucher_type'=>'FIX_VOUCHER',
            'voucher_use_scene'=>'ALIPAY_COMMON',
            ''
        );
        $bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
        //echo '0^'.$bizcontent0;die;
        $bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
        $request->setBizContent($bizcontent);
        //$signData = $request->getApiParas();
        //$sign = $this->aop->rsaSign($signData, $this->aop->signType);
        $result = $this->aop->execute($request);
        var_dump($request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        var_dump($result->$responseNode);
        if(!empty($resultCode)&&$resultCode == 10000){
            echo "成功";
        } else {
            echo "失败";
        }
    }
    public function give_coupon(){
        echo "<pre>";
        $request = new AlipayMarketingVoucherSendRequest ();
        $bizcontentarray=array(
            'template_id'=>'2018122400073001327400261JJM',
            'user_id'=>'2088512480151814',
            'out_biz_no'=>date("YmdHis").rand(0000,9999)
        );
        $bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
        //echo '0^'.$bizcontent0;die;
        $bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
        $request->setBizContent($bizcontent);
        //$signData = $request->getApiParas();
        //$sign = $this->aop->rsaSign($signData, $this->aop->signType);
        $result = $this->aop->execute($request);
        var_dump($request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        var_dump($result->$responseNode);
        if(!empty($resultCode)&&$resultCode == 10000){
            echo "成功";
        } else {
            echo "失败";
        }
    }
    public function sel_coupon(){
        echo "<pre>";
        $request = new AlipayMarketingVoucherListQueryRequest ();
        $bizcontentarray=array(
            //'voucher_id'=>'201812240007300281510324OKNZ'
            'template_id'=>'201812210007300132740025O5DE',
            'user_id'=>'2088902145150773'
        );
        $bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
        //echo '0^'.$bizcontent0;die;
        $bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
        $request->setBizContent($bizcontent);
        //$signData = $request->getApiParas();
        //$sign = $this->aop->rsaSign($signData, $this->aop->signType);
        $result = $this->aop->execute($request);
        var_dump($request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        var_dump($result->$responseNode);
        if(!empty($resultCode)&&$resultCode == 10000){
            echo "成功";
        } else {
            echo "失败";
        }
    }
    public function sel_temp(){
        echo "<pre>";
        $request = new AlipayMarketingVoucherTemplatedetailQueryRequest ();
        $bizcontentarray=array(
            'template_id'=>'2018122400073001327400261JJM'
        );
        $bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
        //echo '0^'.$bizcontent0;die;
        $bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
        $request->setBizContent($bizcontent);
        //$signData = $request->getApiParas();
        //$sign = $this->aop->rsaSign($signData, $this->aop->signType);
        $result = $this->aop->execute($request);
        var_dump($request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        var_dump($result->$responseNode);
        if(!empty($resultCode)&&$resultCode == 10000){
            echo "成功";
        } else {
            echo "失败";
        }
    }
    public function b(){
        var_dump($_GET);
    }
    public function a(){
        $reurl = urlencode("http://xyj.yiyao365.cn/AliRentRomens/index.php/AliRent/AliCard/b");
        var_dump($reurl);
        //$url = "https://openauth.alipay.com/oauth2/appToAppAuth.htm?app_id=2016121004107537&redirect_uri=http%3A%2F%2Fxyj.yiyao365.cn%2FAliRentRomens%2Findex.php%2FAliRent%2FAliCard%2Fb";
        //header ( 'Location:' . $url );
    }
}
?>